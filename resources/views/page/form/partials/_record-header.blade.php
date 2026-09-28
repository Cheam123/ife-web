{{--
    Shown above the fields while filing: what to call this case, and whether it
    follows up on an earlier one.

    The parent picker deliberately lists completed and closed cases too — a job
    that finished and then had an emergency is the reason this exists.

    Params: $parentOptions (Collection, may be empty), $parentCase (nullable),
            $recordTitle (nullable)
--}}
@php
    $parentOptions = $parentOptions ?? collect();
    $parentCase    = $parentCase ?? null;
    $recordTitle   = $recordTitle ?? null;
@endphp

<div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
        <label class="form-label fw-bold" for="record_title">Case title</label>
        <input type="text" class="form-control @error('record_title') is-invalid @enderror"
               name="record_title" id="record_title" maxlength="255"
               value="{{ old('record_title', $recordTitle) }}"
               placeholder="e.g. Acme Sdn Bhd – CNC-220 coolant leak">
        @error('record_title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text small">Optional — how people will find this case later.</div>
    </div>

    @if($parentOptions->isNotEmpty())
        <div class="col-12 col-md-6">
            <label class="form-label fw-bold" for="parent_submission_id">Follows up on</label>
            <select class="form-select js-select2 @error('parent_submission_id') is-invalid @enderror"
                    name="parent_submission_id" id="parent_submission_id"
                    data-placeholder="Not a follow-up">
                <option value=""></option>
                @foreach($parentOptions as $option)
                    <option value="{{ $option->id }}"
                            {{ (string) old('parent_submission_id', optional($parentCase)->id) === (string) $option->id ? 'selected' : '' }}>
                        {{ $option->recordReference() }} — {{ app(\App\Services\FormRecordService::class)->titleFor($option) }}@if(!$option->isRecordOpen()) (closed)@endif
                    </option>
                @endforeach
            </select>
            @error('parent_submission_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div class="form-text small">
                Leave empty unless this is a return visit on an earlier case.
            </div>
        </div>
    @endif
</div>
