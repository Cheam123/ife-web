<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'                 => 'required',
            'alertind'              => 'nullable',
            'task_id'               => 'required',
            'subscriber'            => 'required',
            'sub_subscriber'        => 'nullable',
            'invoice_no'            => 'nullable',
            'sales'                 => 'nullable',
            'task_start_date'       => 'required',
            'task_start_time'       => 'required',
            'task_due_date'         => 'required',
            'task_due_time'         => 'required',
            'task_appointment_date' => 'nullable',
            'task_appointment_time' => 'nullable',
            'owner'                 => 'required',
            'viewer'                => 'nullable',
            'remark'                => 'nullable',
            'lead_id'               => 'nullable',
            'special_remark'        => 'nullable',
            'status'                    => 'nullable',
            'filter_title'              => 'nullable',
            'filter_name'               => 'nullable',
            'filter_alert'              => 'nullable',
            'filter_reference_no'       => 'nullable',
            'filter_subscriber'         => 'nullable',
            'filter_subsubscriber'      => 'nullable',
            'filter_source'             => 'nullable',
            'filter_creator'            => 'nullable',
            'filter_checker'            => 'nullable',
            'filter_owner'              => 'nullable',
            'filter_viewer'             => 'nullable',
            'filter_business_category'  => 'nullable',
            'start'                     => 'nullable',
            'end'                       => 'nullable',
            'complete_date_start'       => 'nullable',
            'complete_date_end'         => 'nullable',
            'inprogress_date_start'     => 'nullable',
            'inprogress_date_end'       => 'nullable',
            'done_date_start'           => 'nullable',
            'done_date_end'             => 'nullable',
            'verify_date_start'         => 'nullable',
            'verify_date_end'           => 'nullable',
            'reject_date_start'         => 'nullable',
            'reject_date_end'           => 'nullable',
            'kiv_date_start'            => 'nullable',
            'kiv_date_end'              => 'nullable',
            'appointment_date_start'    => 'nullable',
            'appointment_date_end'      => 'nullable',
        ];
    }
}
