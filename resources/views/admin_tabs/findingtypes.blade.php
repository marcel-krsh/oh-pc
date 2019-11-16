<a name="findingstop"></a>
<div class="uk-overflow-container uk-margin-top">
    
    
    <div class="uk-grid">
        <h4 class="uk-text-left uk-width-3-5">{{number_format($findingtypes->total(), 0)}} TOTAL FINDING TYPES</h4>
        <span class="uk-width-2-5"><a onclick="dynamicModalLoad('admin/boilerplate/create')" class="uk-button uk-button-success uk-button-small uk-align-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE A BOILERPLATE</a><a onclick="dynamicModalLoad('admin/finding_type/create')" class="uk-button uk-button-success uk-button-small uk-align-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE A FINDING TYPE</a></span>
    </div>
    <hr>
    <div class="uk-grid">
    <div class="uk-width-1-3 uk-text-right">
            <input id="findingtypes-search" name="findingtypes-search" type="text" value="{{ Session::get('findingtypes-search') }}" class=" uk-input" placeholder="Search by finding types name (press enter)"> 
        </div>
    <div class="uk-width-1-2 " style="padding-top:4px;"> 
    {{ $findingtypes->links() }}
    </div>
    <div class="uk-width-1-6 "> <a href="#findingsbottom" id="organization-scroll-to-top" class="uk-button uk-button-small uk-button-default uk-margin-small-top uk-margin-bottom uk-align-right" style=""><i class="a-circle-down"></i> BOTTOM OF LIST</a>
    </div>
    </div>
    <hr class="dashed-hr">
    <table class="uk-table uk-table-condensed small-table-text">
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
                <small>ALLITA TYPE</small>
            </th>
            <th>
                <small>HUD TYPE #</small>
            </th>
            <th>
                <small>HUD AREA #</small>
            </th>
            <th>
                <small>BOILERPLATE #</small>
            </th>
            <th>
                <small>FOLLOW-UP #</small>
            </th>
            <th>
                <small>USE IN SCORING</small>
            </th>
        </thead>
        <tbody>
            @foreach($findingtypes as $data)
                <tr>
                    <td><a onclick="dynamicModalLoad('admin/finding_type/create/{{$data->id}}')" class="uk-link-muted"><small >{{$data->name}}: <br /><em style="color:gray">HUD INSPECTABLE AREAS WITH THIS FINDING:: <ul> @forEach($data->huds() as $hud) <li>{{$hud->name}}</li>  @endForEach</ul></em></small></a></td>
                    <td><small>@if($data->nominal_item_weight){{$data->nominal_item_weight}}% @else 0% @endif</small></td>
                    <td><small>{{$data->criticality}}</small></td>
                    <td><small>@if($data->one) <i uk-tooltip title="Level 1" class="a-circle-checked"></i> @else - @endif</small></td>
                    <td><small>@if($data->two) <i uk-tooltip title="Level 2" class="a-circle-checked"></i> @else - @endif</small></td>
                    <td><small>@if($data->three) <i uk-tooltip title="Level 3" class="a-circle-checked"></i> @else - @endif</small></td>
                    <td><small>{{strtoupper($data->type)}}</small></td>
                    <td><small>@if($data->site)SITE<br/>@endIf
                                @if($data->building_exterior)• BUILDING EXTERIOR<br/>@endIf
                                @if($data->building_system)• BUILDING SYSTEM<br/>@endIf
                                @if($data->common_area)• COMMON AREA<br/>@endIf
                                @if($data->unit)• UNIT<br/>@endIf
                                @if($data->file)• FILE<br/>@endIf
                    </small></td>
                    <td><small>@if($data->huds()){{count($data->huds())}}@endif</small></td>
                    <td><small>@if($data->boilerplates){{count($data->boilerplates)}}@endif</small></td>
                    <td><small>@if($data->default_followups){{count($data->default_followups)}}@endif</small></td>
                    <td><small>@if($data->one) <i uk-tooltip title="Level 1" class="a-circle-checked"></i> @else - @endif</small></td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <a name="findingsbottom"></a> 
    {{ $findingtypes->links() }}  <a href="#findingstop" id="organization-scroll-to-top" class="uk-badge uk-badge-success uk-margin-top"><i class="a-circle-up"></i> BACK TO TOP OF LIST</a>

</div>
<script>
    $(document).ready(function(){
   // your on click function here
   $('.page-link').click(function(){
           $('#findingtype-tab-content').load($(this).attr('href'));
           window.current_finding_type_page = $('#findingtype-tab-content').load($(this).attr('href'));
           return false;
       });
    });
    
    function searchFindingtypes(){
        $.post('{{ URL::route("findingtypes.search") }}', {
                'findingtypes-search' : $("#findingtypes-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#findingtype-tab-content').load('/tabs/findingtype');
                    $('#top').trigger('click');
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