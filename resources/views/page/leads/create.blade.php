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

            <form action="{{route('lead.store')}}" method="post" id="add_customer_form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-body">
                    @include('page.leads.form.lead-form', ['type' => 'add'])
                </div>

                <div class="form-actions">
                    <div class="custom_float">
                        <a href="{{ route('lead.index') }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                        <button type="submit" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.save')</button>
                    </div>
                </div>
            </form>
    </div>
@endsection

@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\LeadRequest') !!} 
<script>
    function fileValidation(type) { 
        fi = document.getElementById(type); 
        // Check if any file is selected. 
        if (fi.files.length > 0) { 
            for (i = 0; i <= fi.files.length - 1; i++) { 
    
                fsize = fi.files.item(i).size; 
                file = Math.round((fsize / 1024)); 
                // The size of the file.
                if(type == 'file'){
                    if (file >= 20480) { 
                        alert("File is too large,  > 20MB!");
                        document.getElementById(type).value='';
                    }
                }
            } 
        } 
    }

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
