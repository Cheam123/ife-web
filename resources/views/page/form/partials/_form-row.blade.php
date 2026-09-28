{{-- One form in the grouped Form List. data-form-id is what the reorder
     payload is built from, so it must stay on the row element. --}}
<div class="fl-row" data-form-id="{{ $form->id }}">
    @can('form_creation')
        <i class="mdi mdi-drag-horizontal-variant fl-handle fl-form-handle"></i>
    @endcan

    <div class="fl-main">
        <div class="fl-name">
            {{ $form->name }}
            @unless($form->is_enabled ?? 1)
                <span class="badge bg-light text-muted border ms-1">Disabled</span>
            @endunless
        </div>
        @if($form->description)
            <div class="fl-desc">{{ Str::limit($form->description, 90) }}</div>
        @endif
    </div>

    <div class="fl-meta d-none d-md-block">{{ $form->created_at->format('d M Y') }}</div>

    @can('form_creation')
    <div class="fl-actions">
        <a href="{{ route('form.edit', $form->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
            <i class="mdi mdi-pencil"></i>
        </a>
        <a href="{{ route('form.preview', $form->id) }}" class="btn btn-sm btn-outline-info" title="Preview">
            <i class="mdi mdi-eye"></i>
        </a>
        <button type="button" onclick="toggleForm({{ $form->id }}, {{ $form->is_enabled ?? 1 }})"
                class="btn btn-sm btn-outline-{{ $form->is_enabled ?? 1 ? 'success' : 'secondary' }}"
                title="{{ $form->is_enabled ?? 1 ? 'Disable' : 'Enable' }}">
            <i class="mdi mdi-{{ $form->is_enabled ?? 1 ? 'check-circle' : 'close-circle' }}"></i>
        </button>
        <button type="button" onclick="deleteForm({{ $form->id }})" class="btn btn-sm btn-outline-danger" title="Delete">
            <i class="mdi mdi-trash-can"></i>
        </button>
    </div>
    @endcan
</div>
