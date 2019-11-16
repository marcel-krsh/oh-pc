<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($programs)}} TOTAL PROGRAMS 
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" >
        <thead>
        
        <th>
            <small>NAME</small>
        </th>
        <th>
            <small>ENTITY IT BELONGS TO</small>
        </th>
        <th>
            <small>COUNTY</small>
        </th>
        <th>
            <small>DEFAULT RULES</small>
        </th>
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($programs as $data)
            <tr>
                
                <td><small><a onclick="dynamicModalLoad('admin/program/create/{{$data->id}}')" class="uk-link-muted"> {{$data->program_name}}</a></small></td>
                <td><small><a onclick="dynamicModalLoad('admin/program/create/{{$data->id}}')" class="uk-link-muted"> @if($data->entity->active == 0) <span class="red-text attention">[ @endIf {{$data->entity->entity_name}} @if($data->entity->active == 0) ] </span> @endIf</a></small></td>
                <td><small><a onclick="dynamicModalLoad('admin/program/create/{{$data->id}}')" class="uk-link-muted"> {{$data->county->county_name}}</a></small></td>
                <td><small><a onclick="dynamicModalLoad('admin/program/create/{{$data->id}}')" class="uk-link-muted"> @if($data->programRule->active == 0) <span class="attention red-text"> [@endIf {{$data->programRule->rules_name}} @if($data->programRule->active == 0) ] </span>@endIf </a></small></td>
                <td>
                    <a onclick="dynamicModalLoad('admin/program/create/{{$data->id}}')" class="uk-link-muted"><i class="a-pencil-2 uk-margin-right"></i>
                    </a>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateProgram uk-link-muted' href="modals/admin/activate/program/{{$data->id }}" title="Click to activate program" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateProgram uk-link-muted' href="modals/admin/deactivate/program/{{$data->id }}" title="Click to deactivate program" data-active="1">
                        <i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
