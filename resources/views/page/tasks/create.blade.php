@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.new_case') @endsection

@section('content')

    <div class="card">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-body">
            @include('page.tasks.form.form-create')
        </div>
    </div>
@endsection

@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\TaskCreateRequest') !!}
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
</script>
@endsection
