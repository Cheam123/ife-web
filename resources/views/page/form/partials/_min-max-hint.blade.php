@if(isset($element['min']) || isset($element['max']))
    <small class="text-muted d-block mb-2">
        (Select
        @if(isset($element['min']) && isset($element['max']))
            {{ $element['min'] }} to {{ $element['max'] }} options
        @elseif(isset($element['min']))
            at least {{ $element['min'] }} option(s)
        @elseif(isset($element['max']))
            up to {{ $element['max'] }} option(s)
        @endif
        )
    </small>
@endif
