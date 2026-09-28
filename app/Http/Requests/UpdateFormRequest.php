<?php

namespace App\Http\Requests;

class UpdateFormRequest extends StoreFormRequest
{
    public function authorize()
    {
        return $this->user()->can('form_creation');
    }
}
