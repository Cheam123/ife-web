@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') @lang('translation.telegrame_chat_id') @endsection

@section('content')

    <div class="card card3 custom-font-small">
        <div class="card-body">
            
            <div style="overflow-x:auto; white-space: nowrap; min-height: 300px;">
                <table class="table table-sm table-hover table-borderless table-striped custom-font-small custom-shadow" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <td scope="col" style="width:5%">#</td>
                            <td scope="col">{{ trans('translation.date') }}</td>
                            <td scope="col">{{ trans('translation.name') }}</td>
                            <td scope="col">{{ trans('translation.chat_id') }}</td>
                            <td scope="col">{{ trans('translation.message') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($collection as $index => $b)
                            <tr>
                                <td scope="row">
                                    {{ $index + 1 }}
                                </th>
                                <td>{{ $b['date'] }}</td>
                                <td>{{ $b['name'] }}</td>
                                <td>{{ $b['chat_id'] }}</td>
                                <td>{{ $b['text'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    
@endsection

@section('script')

@endsection
