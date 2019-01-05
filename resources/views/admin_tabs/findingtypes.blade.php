<div class="uk-overflow-container uk-margin-top">
    
    <div uk-grid class="uk-margin-remove">
        <h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{number_format($findingtypes->total(), 0)}} TOTAL FINDING TYPES</h4> 
        <div class="uk-width-1-3 uk-text-right">
            <input id="findingtypes-search" name="findingtypes-search" type="text" value="{{ Session::get('findingtypes-search') }}" class=" uk-input" placeholder="Search by finding types name (press enter)"> 
        </div>
    </div>
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
                    <td><a onclick="dynamicModalLoad('admin/finding_type/create/{{$data->id}}')" class="uk-link-muted"><small data-uk-tooltip title="">{{$data->name}}: @forEach($data->huds() as $hud) @php dd($hud) @endphp<br /> @endForEach</small></a></td>
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
    {{ $findingtypes->links() }}

</div>
<script>
    
    function searchFindingtypes(){
        $.post('{{ URL::route("findingtypes.search") }}', {
                'findingtypes-search' : $("#findingtypes-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#findingtype-tab-content').load('/tabs/findingtype');
                }
        } );
    }

    // process search
    $(document).ready(function() {
        $('#findingtypes-search').keydown(function (e) {
          if (e.keyCode == 13) {
            searchFindingtypes();
            e.preventDefault();
            return false; 
          }
        });
    });
</script>