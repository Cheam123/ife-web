{{--
    Renders a file-type answer: image files as clickable thumbnails (zoom
    lightbox), other files as download links. Include the
    _image-lightbox partial once per page when using this.
    Params: $value (string URL, array of URLs, or legacy {name} entries)
--}}
@php $files = is_array($value) ? $value : [$value]; @endphp
<div class="d-flex flex-wrap gap-2 mt-1 align-items-start">
    @foreach($files as $file)
        @php
            // file_path is the house key (same as IFE/chat); url is the
            // older forms key, still present on answers written before.
            $url  = is_array($file)
                ? ($file['file_path'] ?? $file['url'] ?? $file['name'] ?? '')
                : (string) $file;
            $path = parse_url($url, PHP_URL_PATH) ?? $url;
            $name = is_array($file) ? ($file['file_name'] ?? $file['name'] ?? basename($path)) : basename($path);
            $isUrl   = str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '/');
            $isImage = $isUrl && preg_match('/\.(jpe?g|png|gif|webp|bmp|svg)$/i', $path);
        @endphp

        @if($isImage)
            <a href="javascript:void(0);" class="answer-image d-inline-block" data-src="{{ $url }}" title="Click to zoom">
                <img src="{{ $url }}" alt="{{ $name }}" class="rounded border"
                     style="max-height:130px;max-width:200px;object-fit:cover;cursor:zoom-in;">
            </a>
        @elseif($isUrl)
            <a href="{{ $url }}" target="_blank" rel="noopener" class="badge rounded-pill px-3 py-2 text-decoration-none"
               style="background:#e8eeff;color:#3a5bd9;font-size:0.8rem;">
                <i class="mdi mdi-file-outline me-1"></i>{{ $name }}
                <i class="mdi mdi-open-in-new ms-1" style="font-size:0.7rem;"></i>
            </a>
        @else
            <span class="badge rounded-pill px-3 py-2" style="background:#e8eeff;color:#3a5bd9;font-size:0.8rem;">
                <i class="mdi mdi-file-outline me-1"></i>{{ $name ?: 'File' }}
            </span>
        @endif
    @endforeach
</div>
