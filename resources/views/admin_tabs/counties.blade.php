<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($counties)}} TOTAL COUNTIES 
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        <th>
            <small>COUNTY</small>
        </th>
        <th>
            <small>AUDITOR SITE</small>
        </th>
        
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($counties as $data)
            <tr >
                <td><small><a onclick="dynamicModalLoad('admin/county/create/{{$data->id}}')" class="uk-link-muted"> {{$data->county_name}}</a></small></td>
                <td><small><a href="{{$data->auditor_site}}" class="uk-link-muted" target="_blank"> {{$data->auditor_site}}</a></small></td>
                
                <td>
                    <a onclick="dynamicModalLoad('admin/county/create/{{$data->id}}')" class="uk-link-muted"><i  class="a-pencil-2 uk-margin-right"></i></a>
                    
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
