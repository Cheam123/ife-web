@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.customer') @endsection

@section('content')
    <div class="card">
        @if ($errors->any())
            <div class="alert alert-danger" id="error_box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{route('lead.update')}}" method="post" id="edit_users_form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="form-body">
                @include('page.leads.form.lead-form', ['type' => 'edit'])
                <input type="hidden" name="id" id="id" value="{{ $customerDetail->id }}">
            </div>

            <div class="form-actions">
                <div class="custom_float">
                    <a href="{{ route('lead.index') }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                    <button type="submit" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.save')</button>
                </div>
            </div>
        </form>

        <div class="row" style="margin-top:10px;">
            <h4>
                Document Upload
            </h4>
            <div>
                <label class="custom-font-xxsmall">Supported File: png, jpg, pdf, excel, word, wav, mp4 & other audio format</label>
                <div class="form-group">
                    <form name="frmUploadDocument" id="frmUploadDocument" action="{{ route('lead.file.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="leadid" value="{{ $customerDetail->id }}">
                        <div class="form-group">
                            <div class="needsclick dropzone" id="document-dropzone">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div style="margin-top: 10px; overflow-x:auto; white-space: nowrap;">
                <table class="table table-sm custom-font-small" style="width:100%; border-collapse: collapse; box-shadow: 1px 1px 3px 1px rgb(183, 183, 183);">
                    <thead class="table-dark">
                        <tr>
                            <td scope="col">{{ trans('translation.name') }}</td>
                            <td scope="col">{{ trans('translation.upload_date') }}</td>
                            <td scope="col">{{ trans('translation.upload_by') }}</td>
                            <td scope="col">{{ trans('translation.file_size') }}</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($customerDetail->documentUploads) > 0)
                            @foreach($customerDetail->documentUploads as $item)
                            <tr>
                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->filename }}</td>
                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->created_at }}</td>
                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->uploadBy->name }}</td>
                                <td style="border-bottom: 1px solid #acacac;" scope="row">{{ $item->size }} KB</td>
                                <td style="border-bottom: 1px solid #acacac; text-align:center;">
                                    <a href="{{ route('lead.file.download',['doc_id'=>$item->id]) }}"><i class="fas fa-cloud-download-alt fa-lg"></i></a>&NonBreakingSpace;
                                    <a onclick="deleteDoc({{ $item->id }})" href="#" ><i class="fas far fa-trash-alt fa-lg" style="color:rgb(157, 22, 22);"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\LeadRequest') !!} 
<script>
    function deleteDoc(doc_id) {
        event.preventDefault();

        new swal({
            title: "Are you sure to delete ?",
            text: "",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: 'Yes',
            denyButtonText: 'No',
            customClass: {
                actions: 'my-actions',
                cancelButton: 'order-1 right-gap',
                confirmButton: 'order-2',
            }
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'get',
                    url: "{{ route('lead.file.delete') }}",
                    data: {"_token": "{{ csrf_token() }}","doc_id":doc_id},
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        });
    }
</script>
@endsection
