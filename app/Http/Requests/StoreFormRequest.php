<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\FormProcessService;
use App\Services\FormSchemaService;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('form_creation');
    }

    public function rules()
    {
        return [
            'name'               => 'required|string|max:255',
            // Every form is filed in a group: an ungrouped form is invisible in
            // the grouped list until someone drags it somewhere.
            'form_group_id'      => 'required|integer|exists:form_groups,id',
            'description'        => 'required|string|max:255',
            'is_enabled'         => 'required|boolean',
            'form_elements'      => 'required|json',
            'process_definition' => 'nullable|json',
            'settings'           => 'nullable|json',
        ];
    }

    public function messages()
    {
        return [
            'form_group_id.required' => 'Choose a group for this form.',
            'form_group_id.exists'   => 'That group no longer exists — pick another.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $schemaService  = app(FormSchemaService::class);
            $processService = app(FormProcessService::class);

            $schema = $this->schemaData();
            if ($schema === null) {
                return; // json rule already failed
            }

            foreach ($schemaService->validateDefinition($schema) as $error) {
                $validator->errors()->add('form_elements', $error);
            }

            $validFieldIds = collect($schema['elements'] ?? [])
                ->filter(fn ($e) => ($e['kind'] ?? 'field') === 'field')
                ->mapWithKeys(fn ($e) => [($e['id'] ?? '') => true])
                ->all();

            $process = $this->processData();
            foreach ($processService->validateDefinition($process, $validFieldIds, $schemaService, $schema) as $error) {
                $validator->errors()->add('process_definition', $error);
            }

        });
    }

    public function schemaData(): ?array
    {
        $decoded = json_decode($this->input('form_elements', ''), true);

        return is_array($decoded) ? app(FormSchemaService::class)->normalize($decoded) : null;
    }

    public function processData(): array
    {
        $decoded = json_decode($this->input('process_definition') ?: '[]', true);

        return app(FormProcessService::class)->normalize(is_array($decoded) ? $decoded : []);
    }

    public function settingsData(): array
    {
        $decoded = json_decode($this->input('settings') ?: '[]', true);
        $decoded = is_array($decoded) ? $decoded : [];

        return [
            'access' => [
                'submit_scope' => in_array($decoded['access']['submit_scope'] ?? 'everyone', ['everyone', 'selected'], true)
                                  ? ($decoded['access']['submit_scope'] ?? 'everyone') : 'everyone',
                'user_ids'     => array_values(array_filter(array_map('intval', $decoded['access']['user_ids'] ?? []))),
                // array_intersect, not array_filter: Admin is type 0.
                'user_types'   => array_values(array_intersect(
                                      array_map('intval', $decoded['access']['user_types'] ?? []),
                                      array_keys(User::getUserTypeListing())
                                  )),
            ],
            // No `record` key any more: a case follows up on another case via
            // parent_submission_id, which is chosen per submission, not
            // configured per form. Stale `record` data on old forms is ignored.
        ];
    }
}
