<div class="">
    <div class="m-3">
        <div style="padding: 10px; font-size: 12px;">
            Salesperson : {{ $ifeReport->createdBy->name }}
        </div>
        <div style="padding: 1px; font-size: 12px; color: #333; line-height: 1;">
            <!-- Report Summary Section -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">📊 Report Summary</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Created On:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;"> {{ $ifeReport->created_at->format('M j, Y g:i A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Last Updated:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ $ifeReport->updated_at->format('M j, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">📋 Task Information</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Task Title:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->task->title ?? 'N/A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">🏢 Company Information</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Company Name:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ ($ifeReport->company_name ?: '<em style="color: #8E8E93;">Not specified</em>') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Nature of Business:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->nature_of_business ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Status:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->status ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Cafe/Outlet/Shop Name:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->shop_name ?? 'N/A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Location & Area Section -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">📍 Location & Area</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">IFE Area:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars(\App\Models\IfeArea::find($ifeReport->ife_area)->area ?? null) }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Location:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->location) }}</div>
                    </div>
                </div>
            </div>

            <!-- Technical Details Section -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">🔧 Technical Details</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Problem:</span></u></div>
                        <div style="color: #1C1C1E; font-weight: 500; line-height: 1.6; padding-left: 8pt;">{!! nl2br(htmlspecialchars($ifeReport->problem_description ?? 'N/A')) !!}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Require Support:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->support_required ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Support Detail:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{!! nl2br(htmlspecialchars($ifeReport->support_description ?? 'N/A')) !!}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Advise/Suggestion/Opportunity:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{!! nl2br(htmlspecialchars($ifeReport->personal_remarks ?? 'N/A')) !!}</div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">👤 Contact Information</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">PIC Name:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->pic_name ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Mobile No:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->mobile_number ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Other Contact No:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->other_mobile_numbers ?? 'N/A') }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Email:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ htmlspecialchars($ifeReport->email ?? 'N/A') }}</div>
                    </div>
                </div>
            </div>

            <!-- Follow-up Schedule -->
            <div style="border: 1px solid #E5E5EA; margin-bottom: 10px; border-radius: 12px; overflow: hidden;">
                <div style="background-color: #F2F2F7; padding: 14px; border-bottom: 1px solid #E5E5EA;">
                    <strong style="color: #1C1C1E; font-size: 12px;">📅 Follow-up Schedule</strong>
                </div>
                <div style="padding: 10px;">
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{{ date('M j, Y', strtotime($ifeReport->next_followup_date)) }}</div>
                    </div>
                    <div style="padding-bottom: 12px;">
                        <div style="font-weight: 600; font-size: 11px; line-height: 1.5;"><u><span style="color:#c0392b;">Plannig:</span></u></div>
                        <div style="font-weight: 500; line-height: 1.5; padding-left: 8pt;">{!! htmlspecialchars($ifeReport->next_followup_plan ?? 'N/A') !!}</div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                @foreach($ifeReport->documentUploads as $doc_idx => $doc)
                    <!-- FOR LOOP SHOW THE DOCUMENT HERE -->
                    <div style="width: 300px;">
                        <img id="{{ 'ifeimg'.$doc->id }}" class="pic" src="{{ $doc->file_full_path }}" style="width: 100px; height: auto; object-fit: cover; cursor: pointer;" onclick="imageClick('{{ $doc->file_full_path }}')"/>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal" style="width:780%; background-color: white; margin: 100px 100px 100px 100px; z-index:50;">
  <span class="close" style="margin: 10px 10px 10px 10px; cursor: pointer; font-size:28pt; z-index:50;">&times;</span>
  <img class="modal-content" id="img01" style="margin: 20px 20px 20px 20px; height: 70vh; width: auto;">
</div>



@section('script')
<script>
    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function onlyAlphaNumberSpaceKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if ( !(ASCIICode == 32 || (ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 96 && ASCIICode < 123) || (ASCIICode > 64 && ASCIICode < 91)) )
            return false;
        return true;
    }

    function onlyAlphaNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if ( !((ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 96 && ASCIICode < 123) || (ASCIICode > 64 && ASCIICode < 91)) )
            return false;
        return true;
    }

    function imageClick(url) {
        // Get the modal
        var modal = document.getElementById("myModal");
        var modalImg = document.getElementById("img01");
    
        modal.style.display = "block";
        modalImg.src = url;

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() { 
            modal.style.display = "none";
        }
    }
</script>
@endsection