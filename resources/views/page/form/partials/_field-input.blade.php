{{--
    Renders a single v2 field element.
    Params: $element (v2 field), $value (prefill), $form, $disabled (bool)
--}}
@php
    $elId     = $element['id'];
    $disabled = $disabled ?? false;
    $value    = $value ?? null;
    $required = ($element['mandatory'] ?? false) && !$disabled;
    $attr     = trim(($required ? 'required' : '') . ($disabled ? ' disabled' : ''));
@endphp

<label class="form-label fw-bold">
    {{ $element['label'] ?? 'Untitled Field' }}
    @if($element['mandatory'] ?? false)
        <span class="text-danger">*</span>
    @endif
</label>

@php $placeholder = $element['placeholder'] ?? ('Enter ' . ($element['label'] ?? '')); @endphp

@switch($element['type'])
    @case('text')
    @case('email')
    @case('tel')
    @case('number')
        <input type="{{ $element['type'] }}" class="form-control form-input" data-el-id="{{ $elId }}"
               placeholder="{{ $placeholder }}" value="{{ is_scalar($value) ? $value : '' }}" {{ $attr }}>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('textarea')
        <textarea class="form-control form-input" data-el-id="{{ $elId }}" rows="4"
                  placeholder="{{ $placeholder }}" {{ $attr }}>{{ is_scalar($value) ? $value : '' }}</textarea>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('date')
        @php
            $minDate = '';
            if ((int) ($element['min_days'] ?? 0) > 0) {
                $minDate = now()->addDays((int) $element['min_days'])->toDateString();
            }
        @endphp
        <input type="date" class="form-control form-input" data-el-id="{{ $elId }}"
               {!! $minDate ? 'min="'.$minDate.'"' : '' !!} value="{{ is_scalar($value) ? $value : '' }}" {{ $attr }}>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('time')
        <input type="time" class="form-control form-input" data-el-id="{{ $elId }}" value="{{ is_scalar($value) ? $value : '' }}" {{ $attr }}>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('select')
        <select class="form-select form-input js-select2" data-el-id="{{ $elId }}"
                data-placeholder="{{ $placeholder ?: 'Select an option' }}" {{ $attr }}>
            <option value=""></option>
            @if(isset($element['values']) && is_array($element['values']))
                @foreach($element['values'] as $option)
                    @if($option && is_scalar($option))
                        <option value="{{ $option }}" {{ $value === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endif
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('user')
        {{-- Person picker: stores the user id; a Handler step can be assigned
             from this value (assignee_mode: 'field'). Uses the app-wide
             select2 search dropdown, same as the Task pages. --}}
        @php $userOptions = app(\App\Services\FormSchemaService::class)->userOptionsFor($element); @endphp
        <select class="form-select form-input js-select2" data-el-id="{{ $elId }}"
                data-placeholder="{{ $placeholder ?: 'Select a person' }}" style="width:100%;" {{ $attr }}>
            <option value=""></option>
            @foreach($userOptions as $userId => $userName)
                <option value="{{ $userId }}" {{ (string) $value === (string) $userId ? 'selected' : '' }}>{{ $userName }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback" id="error_{{ $elId }}"></div>
        @break

    @case('multi-choice')
        @if(isset($element['values']) && is_array($element['values']))
            <div class="border rounded p-3" data-el-id="{{ $elId }}" data-type="multi-choice"
                 data-min="{{ $element['min'] ?? '' }}" data-max="{{ $element['max'] ?? '' }}">
                @include('page.form.partials._min-max-hint', ['element' => $element])
                @foreach($element['values'] as $option)
                    @if($option && is_scalar($option))
                        <div class="form-check">
                            <input class="form-check-input multi-choice-input" type="checkbox" value="{{ $option }}"
                                   id="mc_{{ $elId }}_{{ $loop->index }}" data-parent="{{ $elId }}"
                                   {{ is_array($value) && in_array($option, $value) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                            <label class="form-check-label" for="mc_{{ $elId }}_{{ $loop->index }}">{{ $option }}</label>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
        @endif
        @break

    @case('multi-select')
        @if(isset($element['values']) && is_array($element['values']))
            <div class="border rounded p-3" data-el-id="{{ $elId }}" data-type="multi-select"
                 data-min="{{ $element['min'] ?? '' }}" data-max="{{ $element['max'] ?? '' }}">
                @include('page.form.partials._min-max-hint', ['element' => $element])
                @foreach($element['values'] as $optionGroup)
                    @if(isset($optionGroup['group']) && isset($optionGroup['options']))
                        <div class="mb-3">
                            <strong class="d-block mb-2">{{ $optionGroup['group'] }}</strong>
                            <div class="ms-3">
                                @foreach($optionGroup['options'] as $option)
                                    @if($option)
                                        <div class="form-check">
                                            <input class="form-check-input multi-select-input" type="checkbox" value="{{ $option }}"
                                                   id="ms_{{ $elId }}_{{ $loop->parent->index }}_{{ $loop->index }}" data-parent="{{ $elId }}"
                                                   {{ is_array($value) && in_array($option, $value) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="ms_{{ $elId }}_{{ $loop->parent->index }}_{{ $loop->index }}">{{ $option }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
        @endif
        @break

    @case('checkbox')
        @if(isset($element['values']) && is_array($element['values']))
            <div data-el-id="{{ $elId }}" data-type="checkbox">
                @foreach($element['values'] as $option)
                    @if($option && is_scalar($option))
                        <div class="form-check">
                            <input class="form-check-input checkbox-input" type="checkbox" value="{{ $option }}"
                                   id="cb_{{ $elId }}_{{ $loop->index }}" data-parent="{{ $elId }}"
                                   {{ is_array($value) && in_array($option, $value) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                            <label class="form-check-label" for="cb_{{ $elId }}_{{ $loop->index }}">{{ $option }}</label>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
        @endif
        @break

    @case('file')
        {{-- Posted with the form as files[el_id][], the same way the IFE and
             Task pages do it. No upload-on-pick: nothing leaves the browser
             until the form is submitted. --}}
        @if(!empty($value))
            <div class="mb-2">
                <div class="text-muted small mb-1">
                    <i class="mdi mdi-paperclip me-1"></i>Attached
                </div>
                @include('page.form.partials._file-answer', ['value' => $value])
                @unless($disabled)
                    <div class="form-text small">Choosing new files replaces these.</div>
                @endunless
            </div>
        @endif
        <input type="file" class="form-control file-input" multiple
               name="files[{{ $elId }}][]" data-el-id="{{ $elId }}"
               data-current="{{ empty($value) ? '' : json_encode($value) }}" {{ $disabled ? 'disabled' : '' }}>
        <div id="upload_status_{{ $elId }}" class="mt-1"></div>
        <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
        @break

    @case('gps')
        {{-- Location stamp, filled only by the browser's geolocation
             (form-gps.js), never typed. The hidden input holds the answer as
             JSON {lat, lng, accuracy, captured_at} for readValue(). --}}
        @php
            $gpsValue = is_array($value) && isset($value['lat'], $value['lng']) ? $value : null;
            $gpsMode  = ($element['capture_mode'] ?? 'auto') === 'manual' ? 'manual' : 'auto';
        @endphp
        <div class="gps-field border rounded p-3">
            <input type="hidden" class="gps-input" data-el-id="{{ $elId }}" data-capture-mode="{{ $gpsMode }}"
                   value="{{ $gpsValue ? json_encode($gpsValue) : '' }}" {{ $disabled ? 'disabled' : '' }}>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="gps-readout flex-grow-1 small text-muted">
                    <i class="mdi mdi-map-marker-off-outline me-1"></i>Location not stamped yet.
                </div>
                @unless($disabled)
                    <button type="button" class="btn btn-sm btn-outline-primary gps-capture-btn">
                        <i class="mdi mdi-crosshairs-gps me-1"></i><span class="gps-btn-label">Stamp location</span>
                    </button>
                @endunless
            </div>
            <div class="gps-status small mt-1"></div>
        </div>
        <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
        @once
            <script src="{{ asset('js/forms/form-gps.js') }}"></script>
        @endonce
        @break

    @default
        <input type="text" class="form-control form-input" data-el-id="{{ $elId }}"
               placeholder="Enter {{ $element['label'] ?? '' }}" value="{{ is_scalar($value) ? $value : '' }}" {{ $disabled ? 'disabled' : '' }}>
        <div class="invalid-feedback d-block" id="error_{{ $elId }}"></div>
@endswitch

@once
    {{-- Dropdowns use select2 (loaded app-wide), same as the Task pages.
         form-select2.js initialises every .js-select2 on DOMContentLoaded. --}}
    <script src="{{ asset('js/forms/form-select2.js') }}"></script>
@endonce
