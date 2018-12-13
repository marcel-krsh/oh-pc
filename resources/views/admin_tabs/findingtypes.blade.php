<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($findingtypes)}} TOTAL FINDING TYPES</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
            <th>
                <small>FINDING TYPE NAME</small>
            </th>
            <th>
                <small>NOMINAL WEIGHT</small>
            </th>
            <th>
                <small>CRITICALITY</small>
            </th>
            <th>
                <small>1</small>
            </th>
            <th>
                <small>2</small>
            </th>
            <th>
                <small>3</small>
            </th>
            <th>
                <small>TYPE</small>
            </th>
            <th>
                <small># OF BOILERPLATES</small>
            </th>
            <th>
                <small># OF FOLLOW UPS</small>
            </th>
        </thead>
        <tbody>
            @foreach($findingtypes as $data)
                <tr>
                    <td><a onclick="dynamicModalLoad('admin/finding_type/create/{{$data->id}}')" class="uk-link-muted"><small>{{$data->name}}</small></a></td>
                    <td><small>@if($data->nominal_item_weight){{$data->nominal_item_weight}}% @else 0% @endif</small></td>
                    <td><small>{{$data->criticality}}</small></td>
                    <td><small>{{$data->one}}</small></td>
                    <td><small>{{$data->two}}</small></td>
                    <td><small>{{$data->three}}</small></td>
                    <td><small>{{$data->type}}</small></td>
                    <td><small>@if($data->boilerplates){{count($data->boilerplates)}}@endif</small></td>
                    <td><small>@if($data->default_followups){{count($data->default_followups)}}@endif</small></td>
                </tr>
            @endforeach

        </tbody>
    </table>

</div>