{{--
    Runtime-handler picker. When submit/approve/complete-section activates a
    Handler step whose assignee is "decided when the flow reaches this step",
    the server saves nothing, flashes the original request payload in
    session('needs_assignee') = [name, action (POST url), fields (name=>value)]
    and redirects back here. This modal asks "who handles it?" and replays the
    request with next_assignee_ids[].

    Params: $pickerUsers (id/name collection). Self-contained vanilla JS —
    runs before jQuery/Bootstrap JS load, so it only touches them inside
    DOMContentLoaded.
--}}
@php $needsAssignee = session('needs_assignee'); @endphp

<div class="modal fade" id="assigneePickerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Choose the next handler</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="nap-hint mb-2" id="nap-hint"></div>
                <select class="form-select js-select2" id="nap-select" data-placeholder="Search and select a person...">
                    <option value=""></option>
                    @foreach(($pickerUsers ?? []) as $pickerUser)
                        <option value="{{ $pickerUser->id }}">{{ $pickerUser->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="nap-ok" disabled>Assign &amp; Continue</button>
            </div>
        </div>
    </div>
</div>

<style>
    #assigneePickerModal .nap-hint { font-size: 0.8rem; color: #6b7280; background: #f5f2fc;
                                     border: 1px solid #e2d9f5; border-radius: 6px; padding: 8px 10px; }
    #assigneePickerModal .nap-hint strong { color: #5b3fa8; }
    /* Give select2's dropdown room inside the modal body. */
    #assigneePickerModal .modal-body { min-height: 130px; }
</style>

<script src="{{ asset('js/forms/form-select2.js') }}"></script>
<script>
(function () {
    var pending = @json($needsAssignee);
    var csrf    = @json(csrf_token());

    function replay(selectedId) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = pending.action;
        var add = function (name, value) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };
        add('_token', csrf);
        Object.keys(pending.fields || {}).forEach(function (key) { add(key, pending.fields[key]); });
        add('next_assignee_ids[]', selectedId);
        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!pending || !window.bootstrap || !window.jQuery) return;

        document.getElementById('nap-hint').innerHTML =
            'This form is not finished yet — pick who should handle <strong></strong> next.';
        document.querySelector('#nap-hint strong').textContent = pending.name || 'the next step';

        var $select = jQuery('#nap-select');
        $select.on('change', function () {
            document.getElementById('nap-ok').disabled = !$select.val();
        });

        document.getElementById('nap-ok').addEventListener('click', function () {
            if ($select.val()) replay($select.val());
        });

        window.bootstrap.Modal.getOrCreateInstance(document.getElementById('assigneePickerModal')).show();
        // select2 measures width on init, so wire it once the modal is visible.
        jQuery('#assigneePickerModal').one('shown.bs.modal', function () {
            if (window.FormSelect2) window.FormSelect2.apply('#assigneePickerModal');
            $select.select2('open');
        });
    });
})();
</script>
