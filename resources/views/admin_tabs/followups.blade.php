<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($followups)}} TOTAL DEFAULT FOLLOW UPS</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        	<th><small>DESCRIPTION</small></th>
        	<th><small>#</small></th>
        	<th><small>DURATION</small></th>
        	<th><small>ASSIGNMENT</small></th>
        	<th><small>REPLY</small></th>
        	<th><small>PHOTO</small></th>
        	<th><small>DOCUMENT</small></th>
        </thead>
        <tbody>
            @foreach($followups as $data)
            <tr>
                <td><small>{{$data->description}}</small></td>
                <td><small>{{$data->quantity}}</small></td>
                <td><small>{{$data->duration}}</small></td>
                <td><small>{{$data->assignment}}</small></td>
                <td><small>{{$data->reply}}</small></td>
                <td><small>{{$data->photo}}</small></td>
                <td><small>{{$data->doc}}</small></td>
            </tr>
            @endforeach

        </tbody>
    </table>

</div>