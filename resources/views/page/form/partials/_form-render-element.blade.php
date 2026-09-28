{{--
    Single tree node: description block or field, wrapped for visibility toggling.
    Params: $element, $answers, $visibility, $form, $disabled
    Optional: $previousAnswers — [element id => last round's answer], rendered as
              a hint under the input. Empty everywhere except a looped fill stage.
--}}
@php
    $hiddenNow       = isset($visibility[$element['id']]) && !$visibility[$element['id']];
    $previousAnswers = $previousAnswers ?? [];
@endphp

@if(($element['kind'] ?? 'field') === 'description')
    <div class="form-element-wrapper mb-4 {{ $hiddenNow ? 'd-none' : '' }}" data-el-id="{{ $element['id'] }}" data-kind="description">
        <div class="alert alert-light border mb-0" style="white-space:pre-wrap;">{{ $element['text'] ?? '' }}</div>
    </div>
@else
    <div class="form-element-wrapper mb-4 {{ $hiddenNow ? 'd-none' : '' }}" data-el-id="{{ $element['id'] }}" data-kind="field" data-type="{{ $element['type'] ?? '' }}">
        @include('page.form.partials._field-input', [
            'element'  => $element,
            'value'    => $answers[$element['id']] ?? null,
            'form'     => $form,
            'disabled' => $disabled,
        ])
    </div>
@endif
