{{-- Overview | Team switch on the dashboard (admins only). $active: 'overview' or 'team'. --}}
<nav class="adm-tabs" aria-label="Dashboard views">
    <a href="{{ url('index') }}" class="adm-tab" @if($active === 'overview') aria-current="page" @endif>Overview</a>
    <a href="{{ url('index') }}?tab=team" class="adm-tab" @if($active === 'team') aria-current="page" @endif>Team</a>
</nav>
