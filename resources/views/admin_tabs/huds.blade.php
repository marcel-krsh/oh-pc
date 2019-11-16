<div class="uk-overflow-container uk-margin-top">
    <div class="uk-grid">
        <h4 class="uk-text-left uk-width-4-5">{{count($huds)}} TOTAL HUD AREAS</h4><a onclick="dynamicModalLoad('admin/hud_area/create')" class="uk-button uk-button-success uk-button-small uk-float-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE A HUD AREA</a>
    </div>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>HUD AREA NAME</small>
        </th>
        <th>
            <small>ASSOCIATED AMENITIES</small>
        </th>
        <th>
            <small>ASSOCIATED FINDINGS</small>
        </th>
        <th>
            <small>SITE</small>
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
            <small>LAST UPDATED</small>
        </th>
        </thead>
        <tbody>
            @foreach($huds as $data)
                    <tr>
                        <td><a onclick="dynamicModalLoad('admin/hud_area/create/{{$data->id}}')" class="uk-link-muted"><small>{{$data->name}}</small></a></td>
                        <td><small>{{count($data->amenities)}} Amenities</small></td>
                        <td><small>{{count($data->finding_types)}} Finding Types</small></td>
                        <td><small>@if($data->site) <i uk-tooltip title="Site" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small>@if($data->building_system) <i uk-tooltip title="Building System" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small>@if($data->building_exterior) <i uk-tooltip title="Building Exterior" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small>@if($data->common_area) <i uk-tooltip title="Common Area" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small>@if($data->unit) <i uk-tooltip title="Unit" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small>@if($data->file) <i uk-tooltip title="File" class="a-circle-checked"></i> @else - @endif</small></td>
                        <td><small uk-tooltip title="{{date('m/d/Y g:i a',strtotime($data->updated_at))}}">Updated {{ucwords(\Carbon\Carbon::createFromTimeStamp(strtotime($data->updated_at))->diffForHumans())}}</small></td>
                    </tr>
                @endforeach

        </tbody>
    </table>

</div>