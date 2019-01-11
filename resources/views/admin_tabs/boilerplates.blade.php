<div class="uk-overflow-container uk-margin-top">
    <div class="uk-grid">
        <h4 class="uk-text-left uk-width-3-5">{{count($boilerplates)}} TOTAL BOILERPLATES</h4>
        <span class="uk-width-2-5"><a onclick="dynamicModalLoad('admin/boilerplate/create')" class="uk-button uk-button-success uk-button-small uk-align-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE A BOILERPLATE</a>
</span>
</div>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        	<th><small>TITLE</small></th>
        	<th><small>BOILERPLATE</small></th>
        	<th><small>CREATOR</small></th>
        	<th align="center"><small>GLOBAL</small></th>
            <th align="center"><small>FINDING #</small></th>
        </thead>
        <tbody>
            @foreach($boilerplates as $data)
            <tr>
                <td><small><a onclick="dynamicModalLoad('admin/boilerplate/create/{{$data->id}}')" class="uk-link-muted">{{$data->name}}</a></small></td>
                <td><small>{{$data->boilerplate}}</small></td>
                <td><small>@if($data->user){{$data->user->name}}@endif</small></td>
                <td align="center"><small>@if($data->global)<i uk-tooltip title="Global" class="a-circle-checked"></i> @else - @endif</small></td>
                <td align="center"><small>{{count($data->findings)}}</small></td>

            </tr>
            @endforeach

        </tbody>
    </table>

</div>