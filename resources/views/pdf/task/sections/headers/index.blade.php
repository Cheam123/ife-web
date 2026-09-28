<div style="background-color: #fbfbecff; border: 1px solid #292929; border-radius: 4px; overflow: hidden;">
    <table style="margin: 10px; padding-top: 0px; border-spacing: 3px; border-collapse: separate; width: 100%;">
        <tr>
            <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Task Title</td>
            <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
            <td style="vertical-align: text-top; font-size: 13px; padding-right: 10px;">{{ $data->title }}</td>
        </tr>
        <tr>
            <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Reference No</td>
            <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
            <td style="vertical-align: text-top; font-size: 13px;">{{ isset($data->task_reference) ? $data->task_reference : '' }}</td>
        </tr>
        @if($data->lead)
        <tr>
            <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Customer Name</td>
            <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
            <td style="vertical-align: text-top; font-size: 13px;">{{ $data->lead->name }}</td>
        </tr>
        @endif
        <tr>
            <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Subscriber</td>
            <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
            <td style="vertical-align: text-top; font-size: 13px; color: #0064A1;">{{ $data->users->where('role',2)->first()->user->name }}</td>
        </tr>
        @php $idx = 0; @endphp
        @foreach($data->users->where('role',6) as $index => $u)
            @if($idx == 0)
            <tr>
                <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Sub-Subscriber</td>
                <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
                <td style="vertical-align: text-top; font-size: 13px;">{{ $u->user->name }}</td>
            </tr>
            @else
            <tr>
                <td style="width: 134px; vertical-align: text-top; font-size: 13px;"></td>
                <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; "></td>
                <td style="vertical-align: text-top; font-size: 13px;">{{ $u->user->name }}</td>
            </tr>
            @endif
            @php $idx = $idx + 1; @endphp
        @endforeach
        <tr>
            <td style="width: 134px; vertical-align: text-top; font-size: 13px;">Creation Date</td>
            <td style="width: 1px; padding: 0 2px 7px 2px; vertical-align: text-top; ">:</td>
            <td style="vertical-align: text-top; font-size: 13px;">{{ isset($data->creation_date) ? $data->creation_date : '' }}</td>
        </tr>
    </table>
</div>
