<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Tasks;
use App\Models\Reminders;
use App\Models\Rating;

use App\Http\Controllers\Controller;

class CommonController extends Controller
{
    public function set_reminder(Request $request)
    {
        $message = collect([
            'type'    => 'error',
            'message' => '',
        ]);

        DB::beginTransaction();
        try {
            $input = $request->all();

            $find  = Reminders::where('task_id',$input['tid'])
                            ->where('user_id', $input['uid'])
                            ->first();

            if ($find) {
                $find->reminder_date = $input['d'];
                $find->reminder_time = $input['t'];
                $find->message       = $input['m'];
                $find->save();
            } else {
                $new  = new Reminders();
                $new->task_id       = $input['tid'];
                $new->user_id       = $input['uid'];
                $new->reminder_date = $input['d'];
                $new->reminder_time = $input['t'];
                $new->message       = $input['m'];
                $new->save();
            }

            DB::commit();

            $message->put('type', 'success');
            $message->put('message', trans('translation.successfully_update'));
            return response()->json($message->toArray(), 200);

        } catch (\Throwable $e) {

            DB::rollBack();
            $message->put('type', 'error');
            $message->put('message', 'Oops...'. $e->getMessage());
            return response()->json($message->toArray(), 200);
        }
    }

    public function set_rating(Request $request)
    {
        $message = collect([
            'type'    => 'error',
            'message' => '',
        ]);

        DB::beginTransaction();
        try {
            $input = $request->all();
            $task  = Tasks::where('id',$input['tid'])->first();

            foreach($input['rateinfo'] as $info) {

                $find  = Rating::where('task_id',$input['tid'])
                                ->where('user_id', $info['uid'])
                                ->first();

                if ($find) {
                    $find->rate    = $info['rate'];
                    $find->comment = $info['comment'];
                    $find->save();
                } else {
                    $new  = new Rating();
                    $new->task_id            = $input['tid'];
                    $new->user_id            = $info['uid'];
                    $new->rate               = $info['rate'];
                    $new->comment            = $info['comment'];
                    $new->editable           = 1;
                    $new->task_creation_date = $task->created_at;
                    $new->save();
                }
            }

            DB::commit();

            $message->put('type', 'success');
            $message->put('message', trans('translation.successfully_update'));
            return response()->json($message->toArray(), 200);

        } catch (\Throwable $e) {

            DB::rollBack();
            $message->put('type', 'error');
            $message->put('message', 'Oops...'. $e->getMessage());
            return response()->json($message->toArray(), 200);
        }
    }
}
