<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
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
            'id'            => 'nullable',
            'receive_date'  => 'required',
            'customer_id'   => 'nullable',
            'business_name' => 'nullable',
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|string|max:255',
            'mobile'        => 'required|string|max:15',
            'address'       => 'nullable|string|max:500',
            'ifearea'       => 'nullable',
            'businesscat'   => 'nullable',
            'leadsource'    => 'nullable',
            'state_id'      => 'nullable',
            'city_id'       => 'nullable',
            'postcode'      => 'nullable',
            'remark'        => 'nullable|string',
            'file'          => 'nullable',
        ];
    }
}
