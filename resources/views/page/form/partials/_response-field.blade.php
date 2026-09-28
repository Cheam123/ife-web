{{--
    Renders one read-only response field inside the "Responses" card.
    Params: $field = ['element' => array|null, 'answer' => array]
      element: the current v2 schema element (may be null for legacy
               answers that no longer match any element — falls back to
               the answer's own label/type)
      answer:  {id, type, label, value, hidden}
--}}
@php
    $element = $field['element'] ?? null;
    $answer  = $field['answer'];
    $type    = $element['type'] ?? ($answer['type'] ?? 'text');
    $label   = $element['label'] ?? ($answer['label'] ?? 'Field');
    $value   = $answer['value'] ?? null;
    $isEmpty = is_null($value) || $value === '' || (is_array($value) && count(array_filter($value, fn ($v) => $v !== null && $v !== '')) === 0);

    // Wide (full-width) content types: lists, long text, files.
    $isWide = in_array($type, ['textarea', 'file', 'checkbox', 'multi-choice', 'multi-select'], true);
@endphp

<div class="rs-field {{ $isWide ? 'rs-field-wide' : '' }}">
    <div class="rs-field-label">{{ $label }}</div>

    @if($isEmpty)
        <span class="rs-field-empty">&mdash;</span>

    @elseif($type === 'file')
        @include('page.form.partials._file-answer', ['value' => $value])

    @elseif($type === 'checkbox' && $element && !empty($element['values']))
        {{-- Full checklist: every defined option, with a check/blank icon for selected state. --}}
        <div class="rs-check-list">
            @foreach($element['values'] as $option)
                @continue(!$option || !is_scalar($option))
                @php $checked = is_array($value) && in_array($option, $value); @endphp
                <div class="rs-check-item">
                    <span class="rs-check-icon {{ $checked ? 'rs-check-icon-on' : '' }}">
                        @if($checked)&#10003;@endif
                    </span>
                    {{ $option }}
                </div>
            @endforeach
        </div>

    @elseif($type === 'gps')
        {{-- Before the generic array branch: a stamp is one object, not a tag list. --}}
        @if(is_array($value) && isset($value['lat'], $value['lng']))
            @php
                try {
                    $gpsAt = !empty($value['captured_at'])
                        ? \Illuminate\Support\Carbon::parse($value['captured_at'])->setTimezone(config('app.timezone'))->format('j M Y, g:i A')
                        : null;
                } catch (\Throwable $e) {
                    $gpsAt = null;
                }
            @endphp
            <div class="rs-field-value">
                <i class="mdi mdi-map-marker-outline me-1 text-muted"></i>{{ $value['lat'] }}, {{ $value['lng'] }}
                <a href="https://www.google.com/maps?q={{ $value['lat'] }},{{ $value['lng'] }}" target="_blank" rel="noopener" class="ms-1 small">Open in Maps</a>
            </div>
            @if(isset($value['accuracy']) || $gpsAt)
                <div class="text-muted small">
                    {{ collect([
                        isset($value['accuracy']) ? '±' . round((float) $value['accuracy']) . ' m' : null,
                        $gpsAt,
                    ])->filter()->implode(' · ') }}
                </div>
            @endif
        @else
            <span class="rs-field-empty">&mdash;</span>
        @endif

    @elseif(is_array($value))
        <div class="rs-tag-list">
            @foreach($value as $item)
                @continue(is_array($item) || $item === null || $item === '')
                <span class="rs-tag">{{ $item }}</span>
            @endforeach
        </div>

    @elseif($type === 'user')
        <div class="rs-field-value">
            <i class="mdi mdi-account-outline me-1 text-muted"></i>{{ app(\App\Services\FormSchemaService::class)->userLabel($value) }}
        </div>

    @elseif($type === 'textarea')
        <p class="rs-field-text">{{ $value }}</p>

    @elseif($type === 'email')
        <div class="rs-field-value"><a href="mailto:{{ $value }}">{{ $value }}</a></div>

    @else
        <div class="rs-field-value">{{ $value }}</div>
    @endif
</div>
