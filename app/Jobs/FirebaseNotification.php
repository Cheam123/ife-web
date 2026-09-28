<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Tasks;
use App\Models\Leads;
use App\Models\TaskHistory;
use App\Models\TaskComment;
use App\Models\TaskUsers;
use App\Models\DocumentUpload;
use App\Models\GeneralSetting;
use App\Models\Rating;
use App\Models\Notification as NotificationModel;
use App\Helpers\Helper;
use App\Services\FirebaseService;

use Config;
use Auth;

class FirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public      $timeout = 120;
    protected   $mode;
    protected   $messageType;
    protected   $message;
    protected   $infoChanged;
    protected   $task;
    protected   $lead;
    protected   $chat;
    protected   $user;
    
    public function __construct($id = '0', $mode = 'test', $messageType = '', $message = '', $infoChanged = false)
    {
        $this->mode        = $mode;
        $this->message     = $message;
        $this->messageType = $messageType;
        $this->infoChanged = $infoChanged;

        if ($this->mode == 'test') {
            $this->user = User::findOrFail($id);

        } elseif ($this->mode == 'lead') {
            $this->lead = Leads::findOrFail($id);

        } elseif ($this->mode == 'task') {
            $this->task = Tasks::findOrFail($id);

        } elseif ($this->mode == 'chatroom') {
            $this->chat = TaskComment::findOrFail($id);
            
        } elseif ($this->mode == 'u') {
        }
    }

    public function handle(FirebaseService $firebaseService)
    {
        switch ($this->mode) {
            case 'test': /* Test message from backend portal */
              Log::info("FirebaseNotification: Sending test notification to user ID " . $this->user->id);
                $title = Config('app.name') . " (TEST)";
                $body = $this->message;

                if (isset($this->user)) {
                    if (isset($this->user->fcm_token)) {
                        $data = ['type' => 'test', 'id' => (string)$this->user->id];
                        $firebaseService->sendToDevice($this->user->fcm_token, $title, $body, $data);
                    }
                }
                break;
            
            case 'lead': /* Admins and Managers will get notification */
                switch ($this->messageType) {
                    case '0': /* Lead created, send firebase message to every Admin and Manager */
                        $title = "New Lead";
                        $body  = "Lead Name: " . $this->lead->name . ($this->lead->business_name ? " (" . $this->lead->business_name . ")" : "") . "\n";
                        if ($this->lead->business_category) {
                            $body .= "Sector: " . Helper::getBusinessCategory($this->lead->business_category);
                        }

                        $users = User::whereIn('type',[User::TYPE_ADMIN, User::TYPE_MANAGER])->where('status',1)->get();
                        foreach($users as $u) {
                            if (isset($u->fcm_token)) {
                                $data = ['type' => 'lead', 'id' => (string)$this->lead->id];
                                $firebaseService->sendToDevice($u->fcm_token, $title, $body, $data);
                            }
                        }
                        break;
                }
                break;

            case 'task':
                /**
                 * ROLE:
                 *  1:creator
                 *  2:subscriber
                 *  3:checker
                 *  4:owner
                 *  5:viewer
                 *  6:sub-subscriber
                 */
                if ($this->task) {
                    $title = "";
                    $bodyPrefix = "";
                    
                    switch ($this->messageType) {
                        case '1': $title = "New Sales Task"; break;
                        case '2': $title = "Sales Task Accepted"; break;
                        case '3': $title = "Sales Task Done"; break;
                        case '4': $title = "Sales Task Verified"; break;
                        case '5': $title = "Sales Task Completed"; break;
                        case '6': $title = "Sales Task KIV"; break;
                        case '7': $title = "Sales Task Rejected"; break;
                        case '8': $title = "Sales Task Fallback"; break;
                        case '9': $title = "Sales Task Detail Updated"; break;
                        case '10': $title = "Sales Task Deleted"; break;
                    }

                    if ($title) {
                        $body = "Subscriber :\n" . $this->task->users->where('role',2)->first()->user->name . "\n\n";

                        $first = true;
                        if ($this->task->users->where('role',6)->count() > 0) {
                            foreach($this->task->users->where('role',6) as $item) {
                                if ($first) {
                                    $body .= "Sub-Subscriber :\n" . $item->user->name . "\n";
                                } else {
                                    $body .= $item->user->name . "\n";
                                }
                                $first = false;
                            }
                            $body .= "\n";
                        }

                        $body .= "Reference :\n" . $this->task->task_reference . "\n\n" .
                                 "Title :\n" .$this->task->title . "\n\n" . 
                                 "Lead Name :\n" . $this->task->lead->name . "\n\n";

                        if ($this->task->remark) {
                            $body .= "Remark :\n" . $this->task->remark;
                        }

                        if ($this->messageType == '9') {
                            if($this->infoChanged) {
                                $body .= "\n\nThere is changes on subscriber, sub-subscriber, owner or viewer. Please login to the system for more information";
                            }
                            if($this->message) {
                                $body .= "\n\nInfo Update Purpose :\n" . $this->message;
                            }
                        }

                        $ids   = array_unique($this->task->users->pluck('user_id')->toArray());
                        $users = User::whereIn('id',$ids)->where('status',1)->get();
                        
                        foreach($users as $u) {
                            $r = TaskUsers::where('task_id',$this->task->id)
                                            ->where('user_id',$u->id)
                                            ->first();
                            
                            $shouldSend = false;
                            if ($this->messageType == '1' || $this->messageType == '4' || $this->messageType == '8' || $this->messageType == '9') {
                                if ($r && $r->role != 3) $shouldSend = true;
                            } elseif ($this->messageType == '2' || $this->messageType == '3') {
                                if ($r && $r->role != 2) $shouldSend = true;
                            } elseif ($this->messageType == '5' || $this->messageType == '6' || $this->messageType == '7') {
                                $shouldSend = true;
                            } elseif ($this->messageType == '10') {
                                $shouldSend = true; // Logic in original was just checking isset($u->telegram_chat_id) inside loop, no role check? Wait.
                                // Original code for case 10:
                                // foreach($users as $u) {
                                //     $r = TaskUsers::where...->first();
                                //     if (isset($u->telegram_chat_id)) { ... }
                                // }
                                // It fetches $r but doesn't use it. So it sends to everyone in $users.
                            }

                            if ($shouldSend && isset($u->fcm_token)) {
                                $data = ['type' => 'task', 'id' => (string)$this->task->id];
                                $firebaseService->sendToDevice($u->fcm_token, $title, $body, $data);
                            }
                        }

                        // Special case for Rating in case 5
                        if ($this->messageType == '5') {
                            foreach($this->task->users->whereIn('role',[2,6]) as $item) {
                                if (isset($item->user->fcm_token)) {
                                    $rating = Rating::where('task_id',$this->task->id)->where('user_id',$item->user_id)->first();

                                    if ($rating) {
                                        $ratingTitle = "Sales Task Rating";
                                        $ratingBody  = "Role :\n" . ($item->role == 2 ? "Subscriber" : "Sub-Subscriber") . "\n" .
                                                       "Reference :\n" .$this->task->task_reference . "\n" .
                                                       "Title :\n" .$this->task->title . "\n\n" .
                                                       "Rate :\n" . $rating->rate . "\n" .
                                                       "Comment :\n" . $rating->comment;

                                        $data = ['type' => 'task', 'id' => (string)$this->task->id];
                                        $firebaseService->sendToDevice($item->user->fcm_token, $ratingTitle, $ratingBody, $data);
                                    }
                                }
                            }
                        }
                    }
                }
                break;

            case 'chatroom':
                if ($this->chat) {
                    switch ($this->messageType) {
                        case '1': // create chat message
                            $member = TaskUsers::where('task_id', $this->chat->task_id)->get();

                            $ids   = array_unique($member->pluck('user_id')->toArray());
                            $users = User::whereIn('id',$ids)->where('status',1)->get();

                            $users->each(function ($s) use ($firebaseService) {
                                if ($s->id != $this->chat->submit_by) {
                                    $existing_notification = NotificationModel::where([
                                        'notifiable_type'   => get_class($s),
                                        'notifiable_id'     => $s->id,
                                        'content_type'      => get_class($this->chat),
                                        'content_id'        => $this->chat->id,
                                        'read_at'           => null,
                                    ])->first();

                                    if (!$existing_notification) {
                                        if ($s->fcm_token) {    
                                            $title = "New Comment";
                                            $body = "You have a new comment from " . $this->chat->submitBy->name . " for below task.\n\n";
                                            $body .= "Sales Task Reference : \n" . $this->chat->task->task_reference . "\n\n";
                                            $body .= "Lead : \n" . $this->chat->task->lead->name . ($this->chat->task->lead->business_name ? " (" . $this->chat->task->lead->business_name . ")" : "") . "\n\n";
                                            $body .= "Subscriber : \n" . $this->chat->task->users->where('role',2)->first()->user->name . "\n\n";

                                            $first = true;
                                            if ($this->chat->task->users->where('role',6)->count() > 0) {
                                                foreach($this->chat->task->users->where('role',6) as $item) {
                                                    if ($first) {
                                                        $body .= "Sub-Subscriber : \n" . $item->user->name . "\n";
                                                    } else {
                                                        $body .= $item->user->name . "\n";
                                                    }
                                                    $first = false;
                                                }
                                                $body .= "\n";
                                            }

                                            if (str_replace('&nbsp;','',strip_tags($this->chat->message)) != '') {
                                                $body .= "Message : \n" . str_replace('&nbsp;','',strip_tags($this->chat->message)) . "\n\n";
                                            }

                                            /* Send message */
                                            $data = ['type' => 'chat', 'id' => (string)$this->chat->task_id];
                                            $response = $firebaseService->sendToDevice($s->fcm_token, $title, $body, $data);
            
                                            $notification = new NotificationModel();
                                            $notification->id               = $s->id .'-'. uniqid();
                                            $notification->data             = json_encode([]);
                                            $notification->type             = get_class($this->chat);
                                            $notification->notifiable_type  = get_class($s);
                                            $notification->notifiable_id    = $s->id;
                                            $notification->content_type     = get_class($this->chat);
                                            $notification->content_id       = $this->chat->id;
                                            $notification->read_at          = null;
                                            
                                            // Store Firebase message ID if available, though we can't delete it later easily.
                                            if (isset($response['name'])) {
                                                // $response['name'] is like "projects/project-id/messages/0:123..."
                                                // We can store it in telegram_send_message_id or a new column if we wanted to track it.
                                                // For now, reusing the column to avoid schema changes, although it's not a telegram ID.
                                                $notification->telegram_send_message_id = $response['name'];
                                            }
                                            $notification->save();
                                        }
                                    }
                                }
                            });
                            break;

                        case '2': // delete chat message
                            // Firebase Cloud Messaging does not support deleting delivered notifications programmatically
                            // in the same way Telegram does. We will just delete the notification record from DB.
                            
                            $member = TaskUsers::where('task_id', $this->chat->task_id)->get();

                            $ids   = array_unique($member->pluck('user_id')->toArray());
                            $users = User::whereIn('id',$ids)->where('status',1)->get();

                            $users->each(function ($s) {
                                    $existing_notification = NotificationModel::where([
                                        'notifiable_type'   => get_class($s),
                                        'notifiable_id'     => $s->id,
                                        'content_type'      => get_class($this->chat),
                                        'content_id'        => $this->chat->id,
                                        'read_at'           => null,
                                    ])->first();

                                    if ($existing_notification) {
                                        $existing_notification->delete();
                                    }
                            });
                            break;
                    }
                }
                break;
        }
    }
}
