<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($amenities)}} TOTAL AMENITIES</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
            <th>
                <small>AMENITY NAME</small>
            </th>
            <th>
                <small>PROJECT</small>
            </th>
            <th>
                <small>BUILDING</small>
            </th>
            <th>
                <small>UNIT</small>
            </th>
            <th>
                <small>INSPECTABLE</small>
            </th>
            <th>
                <small>POLICY</small>
            </th>
            <th>
                <small>TIME TO COMPLETE</small>
            </th>
            <th>
                <small>ICON</small>
            </th>
        </thead>
        <tbody>
        @foreach($amenities as $data)
            <tr>
                <td><a onclick="dynamicModalLoad('admin/amenity/create/{{$data->id}}')" class="uk-link-muted"><small>{{$data->amenity_description}}</small></a></td>
                <td><small>@if($data->project) yes @else - @endif</small></td>
                <td><small>@if($data->building) yes @else - @endif</small></td>
                <td><small>@if($data->unit) yes @else - @endif</small></td>
                <td><small>@if($data->inspectable) yes @else - @endif</small></td>
                <td><small>{{$data->policy}}</small></td>
                <td><small>{{($data->time_to_complete) ? $data->time_to_complete : 0}} min</small></td>
                <td><i class="{{$data->icon}}" style="font-size: 20px;"></i></td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>