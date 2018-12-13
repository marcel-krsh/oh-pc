<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{$organizations->total()}} TOTAL ORGANIZATIONS</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>ORGANIZATION NAME</small>
        </th>
        <th>
            <small>CONTACT</small>
        </th>
        <th>
            <small>ADDRESS</small>
        </th>
        <th>
            <small>CITY</small>
        </th>
        <th>
            <small>STATE</small>
        </th>
        <th>
            <small>ZIP</small>
        </th>
        <th>
            <small>PHONE</small>
        </th>
        <th>
            <small>EMAIL</small>
        </th>
        </thead>
        <tbody>
            @foreach($organizations as $data)
                    <tr>
                        
                        <td><small>{{$data->organization_name}}</small></td>
                        <td><small>@if($data->person){{$data->person->first_name.' '.$data->person->last_name}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->line_1}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->city}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->state}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->zip}}@endif</small></td>
                        <td><small></small></td>
                        <td><small></small></td>
                
                    </tr>
                @endforeach

        </tbody>
    </table>
        {{ $organizations->links() }}

</div>