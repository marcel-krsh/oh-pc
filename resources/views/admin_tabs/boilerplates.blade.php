<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($boilerplates)}} TOTAL BOILERPLATES</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        	<th><small>NAME</small></th>
        	<th><small>BOILERPLATE</small></th>
        	<th><small>CREATOR</small></th>
        	<th><small>GLOBAL</small></th>
        </thead>
        <tbody>
            @foreach($boilerplates as $data)
            <tr>
                <td><small>{{$data->name}}</small></td>
                <td><small>{{$data->boilerplate}}</small></td>
                <td><small>@if($data->user){{$data->user->first_name.' '.$data->user->last_name}}@endif</small></td>
                <td><small>{{$data->global}}</small></td>
            </tr>
            @endforeach

        </tbody>
    </table>

</div>