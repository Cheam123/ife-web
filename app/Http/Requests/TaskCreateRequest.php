<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskCreateRequest extends FormRequest
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

        $user = $this->user();

        return [
            'title'                 => 'required',
            'subscriber'            => 'required',
            'sub_subscriber'        => 'nullable',
            'task_start_date'       => 'required',
            'task_start_time'       => 'required',
            'task_due_date'         => 'required',
            'task_due_time'         => 'required',
            'task_appointment_date' => 'nullable',
            'task_appointment_time' => 'nullable',
            'owner'                 => 'required',
            'viewer'                => 'nullable',
            'remark'                => 'nullable',
            'lead_id'               => 'required',
        ];
    }
}
