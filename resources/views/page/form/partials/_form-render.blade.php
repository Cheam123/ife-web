{{--
    Renders the ordered v2 form tree (groups, description blocks, fields).
    Params: $tree (FormSchemaService::renderTree), $answers (id => value),
            $visibility (id => bool), $form, $disabled (bool)
--}}
@php
    $disabled        = $disabled ?? false;
    $answers         = $answers ?? [];
    $visibility      = $visibility ?? [];
    $previousAnswers = $previousAnswers ?? [];
@endphp

@foreach($tree as $item)
    @if($item['kind'] === 'group')
        <div class="form-group-wrapper card border mb-4" data-group-id="{{ $item['group']['id'] }}">
            <div class="card-header py-2 px-3" style="background-color:#fdf3e7;">
                <h6 class="mb-0 fw-bold" style="color:#b06f24;">{{ $item['group']['label'] }}</h6>
            </div>
            <div class="card-body pb-1">
                @foreach($item['elements'] as $element)
                    @include('page.form.partials._form-render-element', ['element' => $element])
                @endforeach
            </div>
        </div>
    @else
        @include('page.form.partials._form-render-element', ['element' => $item['element']])
    @endif
@endforeach
