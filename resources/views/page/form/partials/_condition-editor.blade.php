{{-- Shared condition editor modal (element/group visibility + branch conditions) --}}
<div class="modal fade" id="conditionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="mdi mdi-eye-settings-outline me-1 text-primary"></i>Display Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Conditions inside a block must <strong>all</strong> match (AND). If you add several blocks,
                    matching <strong>any one</strong> block is enough (OR).
                </p>
                <div id="condition-groups-list" class="condition-groups-list"></div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-condition-group-btn">
                    <i class="mdi mdi-plus"></i> Or condition block
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="condition-clear-btn">Clear (always show)</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="condition-save-btn">Save conditions</button>
            </div>
        </div>
    </div>
</div>
