<div style="margin: 10px;">
    (<span style="color: red;">Attachment will be display as icon link in this report</span>)
</div>

@foreach($data->comments->sortByDesc('created_at') as $comment)
    @if(!empty($comment->message))
    <div style="margin-left: 10px; margin-right: 10px;">
        <span style="padding-top: 1px; padding-bottom: 2px; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white;">
            {!! $comment->submitBy->name !!}
        </span> on
         <span style="color: blue;">
            {!! \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d h:i A') !!}
        </span>
        <div class="lineheight85" style="margin-left:5px; margin-bottom: 15px; margin-top: 6px;">
            {!! (str_replace("<div>&nbsp;</div>", "<br/>", $comment->message)) !!}
        </div>
        <!-- <hr style="margin-bottom: 8px;"> -->
    </div>
    @endif
    @if($comment->documentUploads->count() > 0)
    <div style="margin-left: 10px; margin-right: 10px;">
        <span style="padding-top: 1px; padding-bottom: 2px; padding-left: 10px; padding-right: 10px; border-radius: 25px; background:rgb(7, 111, 36); color: white;">
            {!! $comment->submitBy->name !!}
        </span> on
        <span style="color: blue;">
            {!! \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d h:i A') !!}
        </span>
        @foreach($comment->documentUploads as $document)
        <div class="lineheight85" style="margin-left:5px; margin-bottom: 15px; margin-top: 10px;">
            <a target="_blank" href="{{ $document->file_full_path }}"><img src="data:image/png;base64,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
" alt="" height="22"></a> 
        </div>
        @endforeach
        <!-- <hr style="margin-bottom: 8px;"> -->
    </div>
    @endif
@endforeach