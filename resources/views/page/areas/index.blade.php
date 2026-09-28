@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') IFE Areas @endsection

@section('content')
<div class="card card3 custom-font-small">
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="padding:5px; background-color:rgb(13, 114, 158); color:white;">Add Area</div>
        <form method="POST" action="{{ route('area.store') }}" class="row g-2 m-1 mb-3">
            @csrf
            <div class="col-md-3">
                <input type="text" name="area" maxlength="30" class="form-control form-control-sm" placeholder="Area name" value="{{ old('area') }}" required>
            </div>
            <div class="col-md-7">
                <input type="text" name="description" maxlength="500" class="form-control form-control-sm" placeholder="Description (optional)" value="{{ old('description') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary custom-button-shadow w-100">@lang('translation.add')</button>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table table-sm custom-font-small" style="width:100%; border-collapse:collapse; box-shadow:1px 1px 3px 1px rgb(183, 183, 183);">
                <thead class="table-dark">
                    <tr>
                        <td style="width:200px;">Area</td>
                        <td>Description</td>
                        <td style="text-align:right; width:80px;">Leads</td>
                        <td style="text-align:right; width:80px;">Visits</td>
                        <td style="width:170px;"></td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areas as $area)
                        <tr>
                            <td colspan="2" style="border-bottom:1px solid #acacac;">
                                <form method="POST" action="{{ route('area.update', $area->id) }}" id="area-form-{{ $area->id }}" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="area" maxlength="30" class="form-control form-control-sm" style="max-width:190px;" value="{{ $area->area }}" required>
                                    <input type="text" name="description" maxlength="500" class="form-control form-control-sm" value="{{ $area->description }}">
                                </form>
                            </td>
                            <td style="border-bottom:1px solid #acacac; text-align:right;">{{ $area->leads_count }}</td>
                            <td style="border-bottom:1px solid #acacac; text-align:right;">{{ $area->reports_count }}</td>
                            <td style="border-bottom:1px solid #acacac; white-space:nowrap;">
                                <button type="submit" form="area-form-{{ $area->id }}" class="btn btn-sm btn-primary custom-button-shadow">@lang('translation.save')</button>
                                @if($area->leads_count === 0 && $area->reports_count === 0)
                                    <form method="POST" action="{{ route('area.delete') }}" class="d-inline" onsubmit="return confirm('Delete {{ $area->area }}?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $area->id }}">
                                        <button type="submit" class="btn btn-sm btn-danger custom-button-shadow">@lang('translation.delete')</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted py-3">No areas yet. Add the first one above.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
