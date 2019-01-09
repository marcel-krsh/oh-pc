<div class="uk-overflow-container uk-margin-top">
    <a name="organization-top"></a>
    <div uk-grid class="uk-margin-remove">
        <h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{number_format($organizations->total(), 0)}} TOTAL ORGANIZATIONS</h4> 
        <div class="uk-width-1-3 uk-text-right">
            <input id="organizations-search" name="organizations-search" type="text" value="{{ Session::get('organizations-search') }}" class=" uk-input" placeholder="Search by organization name (press enter)"> 
        </div>
    </div>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>ORGANIZATION NAME</small>
        </th>
        <th>
            <small>CONTACT</small>
        </th>
        <th>
            <small>ADDRESS</small>
        </th>
        <th>
            <small>CITY</small>
        </th>
        <th>
            <small>STATE</small>
        </th>
        <th>
            <small>ZIP</small>
        </th>
        <th>
            <small>PHONE</small>
        </th>
        <th>
            <small>EMAIL</small>
        </th>
        </thead>
        <tbody>
            @foreach($organizations as $data)
                    <tr>
                        
                        <td><small>{{$data->organization_name}}</small></td>
                        <td><small>@if($data->person){{$data->person->first_name.' '.$data->person->last_name}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->line_1}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->city}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->state}}@endif</small></td>
                        <td><small>@if($data->address){{$data->address->zip}}@endif</small></td>
                        <td><small></small></td>
                        <td><small></small></td>
                
                    </tr>
                @endforeach

        </tbody>
    </table>
        {{ $organizations->links() }} <a href="#organization-top" id="organization-scroll-to-top" uk-scroll="{offset: 10}" class="uk-badge uk-badge-success"><i class="a-circle-up"></i> BACK TO TOP OF LIST</a>
    

</div>
<script>
    $(document).ready(function(){
   // your on click function here
   $('.page-link').click(function(){
           $('#organizations-tab-content').load($(this).attr('href'));
           $('#organization-scroll-to-top').trigger("click");
           //alert('Did I go to the top?');
           return false;
       });
    });
    function searchOrganizations(){
        $.post('{{ URL::route("organizations.search") }}', {
                'organizations-search' : $("#organizations-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#organization-top').trigger("click");
                    $('#organizations-tab-content').load('/tabs/organization');
                    
                }
        } );
    }

    // process search
    $(document).ready(function() {
        $('#organizations-search').keydown(function (e) {
          if (e.keyCode == 13) {
            searchOrganizations();
            e.preventDefault();
            return false; 
          }
        });
    });
</script>