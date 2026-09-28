<?php

namespace App\Http\Controllers\MobileApp;

use App\Helpers\Helper;
use App\Helpers\IFEHTMLGenerator;
use App\Http\Controllers\Controller;

use App\Models\IfeArea;
use App\Models\IFENatureOfBusiness;
use App\Models\IFEReport;
use App\Models\IfeReportDocumentUpload;
use App\Models\IFEStatus;
use App\Models\TaskComment;
use App\Models\User;
use App\Models\Leads;
use App\Models\Tasks;
use App\Models\TaskUsers;
use App\Models\TaskHistory;
use App\Models\GeneralSetting;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Config;

class IFEController extends Controller
{
    public function getIFEList(Request $request)
    {
        try {
            $user       = $request->user();
            $task_id    = $request->input('task_id');

            if(!$user) {
                return $this->response_failed('User not authenticated');
            }

            if(!$task_id) {
                return $this->response_failed('Task ID is required');
            }

            $IfeReports = IFEReport::with('documentUploads')->where('task_id', $task_id)->get();

            $ifeList = [];
            foreach($IfeReports as $ifeReport) {
                $ifeList[] = [
                    'id'                    => $ifeReport->id,
                    'company_name'          => $ifeReport->company_name,
                    'nature_of_business'    => $ifeReport->nature_of_business,
                    'status'                => $ifeReport->status,
                    'ife_area_name'         => IfeArea::find($ifeReport->ife_area)->area ?? null,
                    'ife_area'              => (int)$ifeReport->ife_area,
                    'shop_name'             => $ifeReport->shop_name,
                    'problem_description'   => $ifeReport->problem_description,
                    'require_support'       => $ifeReport->support_required,
                    'support_description'   => $ifeReport->support_description,
                    'personal_remarks'      => $ifeReport->personal_remarks,
                    'pic_name'              => $ifeReport->pic_name,
                    'mobile_number'         => $ifeReport->mobile_number,
                    'other_mobile_numbers'  => $ifeReport->other_mobile_numbers,
                    'email'                 => $ifeReport->email,
                    'follow_up_date'        => $ifeReport->next_followup_date,
                    'follow_up_plan'        => $ifeReport->next_followup_plan,
                    'location'              => $ifeReport->location,
                    'attachments'           => Helper::media_documents_array($ifeReport->documentUploads),
                ];
            }
            return $this->response_success($ifeList, 'IFE list retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('IFE List Retrieval Failed', [
                'error' => $e->getMessage(),
                'task_id' => $request->input('task_id')
            ]);
            return $this->response_failed($e->getMessage(), 500);
        } catch (\Throwable $e) {
            \Log::error('IFE List Retrieval Failed (Throwable)', ['error' => $e->getMessage()]);
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    public function getIFEFields(Request $request)
    {
        $requireLeadLists = $request->input('requireLeads', true);
        $keyword = $request->input('keyword', '');

        try {
            $areas              = Helper::formatForDropdown(IfeArea::all(), 'id', 'area');
            $natureOfBusinesses = Helper::formatForDropdown(IFENatureOfBusiness::all(), 'value', 'name');
            $statuses           = Helper::formatForDropdown(IFEStatus::all(), 'value', 'name');
            

            $data                           = [];
            $data['areas']                  = $areas;
            $data['nature_of_businesses']   = $natureOfBusinesses;
            $data['statuses']               = $statuses;

            if($requireLeadLists === true || $requireLeadLists === 'true' || $requireLeadLists === 1 || $requireLeadLists === '1') {
                $leadsQuery = Leads::select('id', 'name')->orderBy('name', 'asc');
                
                // Apply search filter if keyword is provided
                if (!empty($keyword)) {
                    $leadsQuery->where('name', 'like', '%' . $keyword . '%');
                }
                              
                $leadsDropdown = $leadsQuery->get();
                $data['leads'] = Helper::formatForDropdown($leadsDropdown, 'id', 'name');
            }

            return $this->response_ok($data, 'IFE fields retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('IFE Fields Retrieval Failed', ['error' => $e->getMessage()]);
            return $this->response_failed($e->getMessage(), 500);
        } catch (\Throwable $e) {
            \Log::error('IFE Fields Retrieval Failed (Throwable)', ['error' => $e->getMessage()]);
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    public function searchIFELeadsList(Request $request)
    {
        $query = $request->input('query', '');

        try {
            if (empty($query)) {
                return $this->response_ok([], 'No search query provided');
            }

            $leads = Leads::select('id as value', 'name as label')
                ->where('name', 'like', '%' . $query . '%')
                ->orWhere('business_name', 'like', '%' . $query . '%')
                ->orderBy('name', 'asc')
                ->limit(10)
                ->get();

            return $this->response_ok($leads, 'Leads search results retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('IFE Leads Search Failed', ['error' => $e->getMessage(), 'query' => $request->input('query')]);
            return $this->response_failed($e->getMessage(), 500);
        } catch (\Throwable $e) {
            \Log::error('IFE Leads Search Failed (Throwable)', ['error' => $e->getMessage()]);
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    public function addIFEAdhocReport(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'task_id'               => 'nullable|exists:tasks,id',
            'company_name'          => 'nullable|string',
            'existing_lead_id'      => 'nullable|string',
            'nature_of_business'    => 'nullable|string|max:255',
            'status'                => $request->has('task_id') ? 'nullable|string|max:255' : ($request->has('existing_lead_id') ? 'nullable|string|max:255' : 'required|string|max:255'),
            'ife_area'              => $request->has('task_id') ? 'nullable|string|max:255' : ($request->has('existing_lead_id') ? 'nullable|string|max:255' : 'required|string|max:255'),
            'shop_name'             => 'nullable|string|max:255',
            'problem_description'   => 'required|string',
            'support_required'      => 'required|string',
            'support_description'   => 'nullable|string',
            'personal_remarks'      => 'nullable|string',
            'pic_name'              => 'required|string|max:255',
            'mobile_number'         => 'nullable|string|max:20',
            'other_mobile_numbers'  => 'nullable|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'next_followup_date'    => 'required|date',
            'next_followup_plan'    => 'required|string',
            'location'              => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $user) {
                $IfeReport = new IFEReport();
                $IfeReport->created_by            = $user->id;
                $IfeReport->task_id               = null;
                $IfeReport->company_name          = $request->has('existing_lead_id') ? (Leads::findOrFail($validated['existing_lead_id'])->name ?? null) : $validated['company_name'];
                $IfeReport->nature_of_business    = $request->has('existing_lead_id') ? (Leads::findOrFail($validated['existing_lead_id'])->business_category ?? null) : $validated['nature_of_business'];
                $IfeReport->status                = $request->has('existing_lead_id') ? (Leads::findOrFail($validated['existing_lead_id'])->customer_id ? 'Existing Customer' : "New Customer") : $validated['status'];
                $IfeReport->ife_area              = $request->has('existing_lead_id') ? (Leads::findOrFail($validated['existing_lead_id'])->ife_area_id ?? null) : $validated['ife_area'];
                $IfeReport->shop_name             = $request->has('existing_lead_id') ? (Leads::findOrFail($validated['existing_lead_id'])->business_name ?? null) : $validated['shop_name'];
                $IfeReport->problem_description   = $validated['problem_description'] ?? null;
                $IfeReport->support_required      = $validated['support_required'] ?? null;
                $IfeReport->support_description   = $validated['support_description'] ?? null;
                $IfeReport->personal_remarks      = $validated['personal_remarks'] ?? null;
                $IfeReport->pic_name              = $validated['pic_name'] ?? null; 
                $IfeReport->mobile_number         = $validated['mobile_number'] ?? null;
                $IfeReport->other_mobile_numbers  = $validated['other_mobile_numbers'] ?? null;
                $IfeReport->email                 = $validated['email'] ?? null;
                $IfeReport->next_followup_date    = $validated['next_followup_date'] ?? null;
                $IfeReport->next_followup_plan    = $validated['next_followup_plan'] ?? null;
                $IfeReport->location              = $validated['location'] ?? null;
                $IfeReport->save();

                if ($request->hasFile('files')) {
                    Log::info("Request input: " . json_encode($request->all()));
                    $files = $request->file('files');

                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    foreach ($files as $i=>$file) {
                        if(!$file->isValid()) {
                            continue; // Skip invalid files
                        }

                        $originalName   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName  = str_replace(' ', '_', $originalName);
                        $extension      = $file->getClientOriginalExtension();
                        
                        $filename       = "{$sanitizedName}_{$IfeReport->id}-".($i + 1)."-".time().".{$extension}";
                        $path           = "ife/{$IfeReport->id}";

                        Storage::putFileAs($path, new File($file), $filename);

                        // Create the database record for the upload.
                        $documentUpload = new IfeReportDocumentUpload();
                        $documentUpload->ife_report_id   = $IfeReport->id;
                        $documentUpload->upload_by       = $user->id;
                        $documentUpload->filename        = $filename;
                        $documentUpload->size            = $file->getSize();
                        $documentUpload->mime_type       = $file->getMimeType();
                        $documentUpload->save();
                    }
                }

                return $IfeReport;
            });

            return $this->response_ok([], 'IFE report created successfully');

        } catch (\Exception $e) {
            \Log::error('IFE Report Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null,
                'request_data' => $request->except(['files'])
            ]);
            return $this->response_failed($e->getMessage(), 500);

        } catch (\Throwable $e) {
            \Log::error('IFE Report Creation Failed (Throwable)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    public function updateIFEReport(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'id'                    => 'required|integer',
            'problem_description'   => 'required|string',
            'support_required'      => 'required|string',
            'support_description'   => 'nullable|string',
            'personal_remarks'      => 'nullable|string',
            'pic_name'              => 'required|string|max:255',
            'mobile_number'         => 'nullable|string|max:20',
            'other_mobile_numbers'  => 'nullable|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'next_followup_date'    => 'required|date',
            'next_followup_plan'    => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $user) {
                $ifeReport = IFEReport::findOrFail($validated['id']);

                $ifeReport->problem_description   = $validated['problem_description'] ?? null;
                $ifeReport->support_required      = $validated['support_required'] ?? null;
                $ifeReport->support_description   = $validated['support_description'] ?? null;
                $ifeReport->personal_remarks      = $validated['personal_remarks'] ?? null;
                $ifeReport->pic_name              = $validated['pic_name'] ?? null;
                $ifeReport->mobile_number         = $validated['mobile_number'] ?? null;
                $ifeReport->other_mobile_numbers  = $validated['other_mobile_numbers'] ?? null;
                $ifeReport->email                 = $validated['email'] ?? null;
                $ifeReport->next_followup_date    = $validated['next_followup_date'] ?? null;
                $ifeReport->next_followup_plan    = $validated['next_followup_plan'] ?? null;
                $ifeReport->save();
                
                if ($request->has('deleted_attachment_ids')) {
                    $idsToDelete = $request->input('deleted_attachment_ids');

                    if (is_array($idsToDelete) && !empty($idsToDelete)) {
                        $documents = IfeReportDocumentUpload::whereIn('id', $idsToDelete)
                            ->where('ife_report_id', $ifeReport->id)
                            ->get();

                        foreach ($documents as $doc) {
                            if (file_exists($doc->getFileFullPathAttribute())) {
                                unlink($doc->getFileFullPathAttribute());
                            }
                            $doc->delete();
                        }
                    }
                }

                // Handle file uploads if any
                if ($request->hasFile('files')) {
                    $files = $request->file('files');

                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    
                    foreach ($files as $i => $file) {
                        if(!$file->isValid()) {
                            continue; // Skip invalid files
                        }

                        $originalName   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $sanitizedName  = str_replace(' ', '_', $originalName);
                        $extension      = $file->getClientOriginalExtension();
                        
                        $filename       = "{$sanitizedName}_{$ifeReport->id}-" . ($i + 1) . "-" . time() . ".{$extension}";
                        $path           = "ife/{$ifeReport->id}";

                        Storage::putFileAs($path, new File($file), $filename);

                        // Create the database record for the upload
                        $documentUpload = new IfeReportDocumentUpload();
                        $documentUpload->ife_report_id   = $ifeReport->id;
                        $documentUpload->upload_by       = $user->id;
                        $documentUpload->filename        = $filename;
                        $documentUpload->size            = $file->getSize();
                        $documentUpload->mime_type       = $file->getMimeType();
                        $documentUpload->save();
                    }
                }

                return $ifeReport;
            });

            return $this->response_ok([], 'IFE report updated successfully');

        } catch (\Exception $e) {
            \Log::error('IFE Report Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ife_report_id' => $request->input('id')
            ]);
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    public function deleteIFEReport(Request $request) 
    {
        try {
            if(!request('id')) {
                return $this->response_failed('IFE report ID is required', 400);
            }

            $validated = $request->validate([
                'id' => 'required|integer|exists:ife_report,id',
            ]);

            $ifeReport = IFEReport::find($validated['id']);
            if (!$ifeReport) {
                return $this->response_failed('IFE report not found', 404);
            }

            DB::transaction(function () use ($ifeReport) {
                $ifeReportDocuments = IfeReportDocumentUpload::where('ife_report_id', $ifeReport->id)->get();
                foreach ($ifeReportDocuments as $document) {
                    Storage::delete("ife/{$document->ife_report_id}/{$document->filename}");
                    $document->delete();
                }

                $ifeReport->delete();
            });

            return $this->response_ok([], 'IFE report deleted successfully');

        } catch (\Exception $e) {
            return $this->response_failed($e->getMessage(), 500);
        }
    }

    function getStatusStyle($status)
    {
        $status = strtolower(trim($status));

        // Handle specific IFE Report customer statuses with iOS-style colors
        switch ($status) {
            case 'new customer':
                return 'background-color: #34C759; color: white; border: 1px solid #34C759;';
            case 'existing customer':
                return 'background-color: #007AFF; color: white; border: 1px solid #007AFF;';
            case 'previous yes, now no':
                return 'background-color: #FF3B30; color: white; border: 1px solid #FF3B30;';
            case 'new inquiry':
                return 'background-color: #FF9500; color: white; border: 1px solid #FF9500;';
        }

        // Fallback for other potential statuses
        if (strpos($status, 'active') !== false || strpos($status, 'open') !== false || strpos($status, 'ongoing') !== false) {
            return 'background-color: #34C759; color: white; border: 1px solid #34C759;';
        } elseif (strpos($status, 'pending') !== false || strpos($status, 'waiting') !== false || strpos($status, 'review') !== false) {
            return 'background-color: #FF9500; color: white; border: 1px solid #FF9500;';
        } elseif (strpos($status, 'completed') !== false || strpos($status, 'closed') !== false || strpos($status, 'resolved') !== false) {
            return 'background-color: #007AFF; color: white; border: 1px solid #007AFF;';
        } elseif (strpos($status, 'urgent') !== false || strpos($status, 'critical') !== false || strpos($status, 'high') !== false) {
            return 'background-color: #FF3B30; color: white; border: 1px solid #FF3B30;';
        }

        return 'background-color: #8E8E93; color: white; border: 1px solid #8E8E93;';
    }

    function getStatusInlineStyle($status)
    {
        $status = strtolower(trim($status));
        
        // Base badge styles with iOS design
        $baseStyle = 'display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;';
        
        // Handle specific IFE Report customer statuses with iOS-style colors
        switch ($status) {
            case 'new customer':
                return $baseStyle . ' background: #34C759; color: white;';
            case 'existing customer':
                return $baseStyle . ' background: #007AFF; color: white;';
            case 'previous yes, now no':
                return $baseStyle . ' background: #FF3B30; color: white;';
            case 'new inquiry':
                return $baseStyle . ' background: #FF9500; color: white;';
        }

        return $baseStyle . ' background: #8E8E93; color: white;';
    }






    // BELOW FUNCTION NO LONGER USED
    function generateTask($id)
    {
        $ifeReport = IFEReport::where('id',$id)->first();
        $lead = Leads::where('name', 'like' ,'%'.$ifeReport->company_name.'%')->first();

        if (!$lead) {
            $lead = new Leads();
            $lead->name              = $ifeReport->company_name;
            $lead->business_name     = $ifeReport->shop_name;
            $lead->receiving_date    = $ifeReport->created_at;
            $lead->belong_to         = $ifeReport->created_by;
            $lead->assign_to         = $ifeReport->created_by;
            $lead->hq_checker        = $ifeReport->created_by;
            $lead->business_category = null;
            $lead->source            = null;
            $lead->email             = $ifeReport->email;
            $lead->mobile            = $ifeReport->mobile;
            $lead->address           = $ifeReport->location;
            $lead->city_id           = null;
            $lead->state_id          = null;
            $lead->postcode          = null;
            $lead->ife_area_id       = $ifeReport->ife_area;
            $lead->remark            = $ifeReport->other_mobile_number;
            $lead->save();
        }

        $task = new Tasks();
        $task->alert                = 0;
        $task->title                = 'IFE Route : ' . ($ifeReport->company_name ? $ifeReport->company_name : $ifeReport->shop_name);
        $task->invoice_no           = null;
        $task->sales                = null;
        $task->due_notify           = 0;
        $task->status               = 2;
        $task->lead_id              = $lead->id;
        $task->task_reference       = Tasks::nextReference();
        $task->appointment_date     = null;
        $task->start_date           = Carbon::parse($ifeReport->created_at)->format('Y-m-d');
        $task->start_time           = Carbon::parse($ifeReport->created_at)->format('09:00:00');
        $task->due_date             = $ifeReport->next_followup_date ? Carbon::parse($ifeReport->next_followup_date)->format('Y-m-d') : Carbon::parse($ifeReport->created_at)->format('Y-m-d');
        $task->due_time             = Carbon::parse($ifeReport->created_at)->format('09:00:00');
        $task->remark               = null;
        $task->creation_date        = $ifeReport->created_at;
        $task->inprogress_date      = $ifeReport->created_at;
        $task->done_date            = null;
        $task->verify_date          = null;
        $task->complete_date        = null;
        $task->reject_date          = null;
        $task->kiv_date             = null;
        $task->save();

        // Task Users
        // subscriber
        $taskUser = new TaskUsers();
        $taskUser->task_id = $task->id;
        $taskUser->user_id = $ifeReport->created_by;
        $taskUser->role    = 2;
        $taskUser->save();

        // checker
        $taskUser = new TaskUsers();
        $taskUser->task_id  = $task->id;
        $taskUser->user_id  = User::where('email', 'danny.ho@eciatto.com')->first()->id;
        $taskUser->role     = 3;
        $taskUser->save();

        // owner
        $taskUser = new TaskUsers();
        $taskUser->task_id  = $task->id;
        $taskUser->user_id  = User::where('email', 'danny.ho@eciatto.com')->first()->id;
        $taskUser->role     = 4;
        $taskUser->save();

        // Task History
        $param_b = new Tasks();
        $param_a = Tasks::with('lead')->findOrFail($task->id);
        $data    = Helper::prepareDataForSerialize($param_b, $param_a);

        $history = new TaskHistory();
        $history->task_id           = $task->id;
        $history->updated_by        = $ifeReport->created_by;
        $history->before_status     = 0;
        $history->after_status      = 2;
        $history->content_before    = serialize($data['before']);
        $history->content_after     = serialize($data['after']);
        $history->remark            = NULL;
        $history->save();

        $ifeReport->task_id = $task->id;
        $ifeReport->save();

        //$html = Helper::generateIfeReportHTMLforComment($ifeReport);
        
        self::addIfeReportAsComment($ifeReport, $task);
    }
    public function addIfeReportAsComment($ifeReport, $task) 
    {
        $msg = "IFE ROUTE SUMMARY\n";
        if ($ifeReport->ife_area) {
            $msg .= "\nIFE Area: \n";
            $msg .= htmlspecialchars(IfeArea::find($ifeReport->ife_area)->area ?? '') ."\n"; 
        }
        if ($ifeReport->location) {
            $msg .= "\nLocation: \n";
            $msg .= htmlspecialchars($ifeReport->location)."\n";
        }
        if ($ifeReport->problem_description) {
            $msg .= "\nSummary: \n";
            $msg .= nl2br(htmlspecialchars($ifeReport->problem_description ?? 'N/A'))."\n";
        }

        if ($ifeReport->support_required && $ifeReport->support_required <> 'N/A') {
            $msg .= "\nSupport Required: \n";
            $msg .= nl2br(htmlspecialchars($ifeReport->support_description ?? 'N/A'))."\n";
        }
        
        if ($ifeReport->personal_remarks && $ifeReport->personal_remarks <> 'N/A') {
            $msg .= "\nRemark: \n";
            $msg .= nl2br(htmlspecialchars($ifeReport->personal_remarks ?? 'N/A'))."\n";
        }

        if ($ifeReport->next_followup_date) {
            $msg .= "\nFollow Up Info: \n";
            $msg .= date('M j, Y', strtotime($ifeReport->next_followup_date))."\n";
            $msg .= htmlspecialchars($ifeReport->next_followup_plan ?? 'N/A')."\n";
        }

        $msg .= "\n";

        // Create a task comment with the IFE report HTML
        $comment = new TaskComment();
        $comment->task_id       = $task->id;
        $comment->submit_by     = $ifeReport->created_by;
        $comment->submit_date   = now();
        $comment->message       = $msg;
        $comment->ife_report_id = $ifeReport->id;
        $comment->save();
    }
}
