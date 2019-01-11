<div class="uk-overflow-container uk-margin-top uk-width-1-1">
    
    <div class="uk-grid">
        <h4 class="uk-text-left uk-width-4-5">{{count($amenities)}} TOTAL AMENITIES</h4><a onclick="dynamicModalLoad('admin/amenity/create')" class="uk-button uk-button-success uk-button-small uk-float-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE AN AMENITY</a>
    </div>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" style="margin-left:auto; margin-right:auto">
        <thead>
            <th>
                <small>AMENITY NAME</small>
            </th>
            <th>
                <small>PROJECT</small>
            </th>
            <th>
                <small>BUILDING SYSTEM</small>
            </th>
            <th>
                <small>BUILDING EXTERIOR</small>
            </th>
            <th>
                <small>COMMON AREA</small>
            </th>
            <th>
                <small>UNIT</small>
            </th>
            <th>
                <small>FILE</small>
            </th>
            <th>
                <small>INSPECTABLE</small>
            </th>
            <th>
                <small uk-tooltip title="Will be applied as a default Project Amenity">P Default</small>
            </th>
            <th>
                <small uk-tooltip title="Will be applied as a default Building Amenity">B Default</small>
            </th>
            <th>
                <small uk-tooltip title="Will be applied as a default Unit Amenity">U Default</small>
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
                <td><a onclick="dynamicModalLoad('admin/amenity/create/{{$data->id}}')" class="uk-link-muted"><small>{{$data->amenity_description}} @if(count($data->huds))  
                    <span class="gray-text">( {{count($data->huds)}} HUD AREAS )</span> @endif</small></a></td>
                <td><small>@if($data->project) <i uk-tooltip title="Project" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->building_system) <i uk-tooltip title="Building System" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->building_exterior) <i uk-tooltip title="Building Exterior" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->common_area) <i uk-tooltip title="Common Area" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->unit) <i uk-tooltip title="Unit" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->file) <i uk-tooltip title="File" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->inspectable) <i uk-tooltip title="Inspectable" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->project_default) <i uk-tooltip title="Project Default" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->building_default) <i uk-tooltip title="Building Default" class="a-circle-checked"></i> @else - @endif</small></td>
                <td><small>@if($data->unit_default) <i uk-tooltip title="Unit Default" class="a-circle-checked"></i> @else - @endif</small></td>

                <td><small>{{$data->policy}}</small></td>
                <td><small>{{($data->time_to_complete) ? $data->time_to_complete : 0}} min</small></td>
                <td><i class="{{$data->icon}}" style="font-size: 20px;"></i></td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>