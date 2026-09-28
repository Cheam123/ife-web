{{-- Image zoom lightbox (used by _file-answer thumbnails). Include once per page. --}}
<div class="modal fade" id="imageLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background:#1c1e21;">
            <div class="modal-header border-0 py-2">
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-dark" id="lightbox-zoom-out" title="Zoom out"><i class="mdi mdi-minus"></i></button>
                    <span class="text-white-50 small" id="lightbox-zoom-level" style="min-width:44px;text-align:center;">100%</span>
                    <button type="button" class="btn btn-sm btn-dark" id="lightbox-zoom-in" title="Zoom in"><i class="mdi mdi-plus"></i></button>
                    <button type="button" class="btn btn-sm btn-dark ms-1" id="lightbox-zoom-reset" title="Reset">1:1</button>
                    <a href="#" target="_blank" rel="noopener" class="btn btn-sm btn-dark ms-1" id="lightbox-open" title="Open original"><i class="mdi mdi-open-in-new"></i></a>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center" id="lightbox-body"
                 style="overflow:auto;max-height:82vh;min-height:300px;">
                <img src="" id="lightbox-img" alt=""
                     style="max-width:100%;transform-origin:center top;transition:transform .15s;">
            </div>
        </div>
    </div>
</div>

@push('lightbox-script')
<script>
    $(function () {
        var zoom = 1;

        function applyZoom() {
            $('#lightbox-img').css('transform', 'scale(' + zoom + ')');
            $('#lightbox-zoom-level').text(Math.round(zoom * 100) + '%');
        }

        $(document).on('click', '.answer-image', function () {
            var src = $(this).data('src');
            zoom = 1;
            $('#lightbox-img').attr('src', src);
            $('#lightbox-open').attr('href', src);
            applyZoom();
            new bootstrap.Modal(document.getElementById('imageLightbox')).show();
        });

        $('#lightbox-zoom-in').on('click', function () { zoom = Math.min(4, zoom + 0.25); applyZoom(); });
        $('#lightbox-zoom-out').on('click', function () { zoom = Math.max(0.25, zoom - 0.25); applyZoom(); });
        $('#lightbox-zoom-reset').on('click', function () { zoom = 1; applyZoom(); });

        // Click the image itself to toggle between fit and 2x.
        $('#lightbox-img').on('click', function () { zoom = zoom === 1 ? 2 : 1; applyZoom(); });
    });
</script>
@endpush
