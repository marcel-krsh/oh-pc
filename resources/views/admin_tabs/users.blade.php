 <a name="organizationtop"></a>
 <div class="uk-overflow-container uk-margin-top">
   
    <div uk-grid class="uk-margin-remove">
        <h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{number_format($users->total(), 0)}} TOTAL USERS</h4> 
        <div class="uk-width-1-3 uk-text-right">
            <input id="users-search" name="users-search" type="text" value="{{ Session::get('users-search') }}" class=" uk-input" placeholder="Search by user name (press enter)"> 
        </div>
    </div>
    <hr>
    {{ $users->links() }} <a href="#userbottom" id="user-scroll-to-top" class="uk-badge uk-badge-success uk-margin-top"><i class="a-circle-down"></i> BOTTOM OF LIST</a>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>NAME</small>
        </th>
        
        <th>
            <small>ORGANIZATION</small>
        </th>
        <th>
            <small>ADDRESS</small>
        </th>
        <th>
            <small>PHONE</small>
        </th>
        <th>
            <small>EMAIL</small>
        </th>
        <th>
            <small>ROLE</small>
        </th>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    
                    <td><small>{{$user->full_name()}}</small></td>
                    
                    <td><small>{{$user->organization_details->organization_name}}</small></td>
                    <td><small>{!!$user->organization_details->address->formatted_address()!!}</small></td>
                    <td><small>{{$user->person->phone->number()}}</small></td>
                    <td><small><a href="mailto:{{$user->person->email->email_address}}">{{$user->person->email->email_address}}</a></small></td>
                    <td class="use-hand-cursor" uk-tooltip="title:CLICK TO SET ROLES" onclick="setRoles({{$user->id}})"><small>@if($user->roles_list() != ''){{$user->roles_list()}}@else <i class="a-circle-plus"></i>@endif</small></td>
            
                </tr>
            @endforeach
        </tbody>
    </table>
    <a name="userbottom"></a>
        {{ $users->links() }} <a href="#usertop" id="user-scroll-to-top" class="uk-badge uk-badge-success uk-margin-top"><i class="a-circle-up"></i> BACK TO TOP OF LIST</a>
    

</div>
<script>
    $(document).ready(function(){
   // your on click function here
   $('.page-link').click(function(){
            $('#users-content').load($(this).attr('href'));
           return false;
       });
    });
    function searchUsers(){
        $.post('{{ URL::route("users.search") }}', {
                'users-search' : $("#users-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#usertop').trigger("click");
                    $('#users-content').load('/tabs/users');
                    
                }
        } );
    }

    function setRoles(id) {
    	dynamicModalLoad('admin/users/'+id+'/manageroles');
    }

    // process search
    $(document).ready(function() {
        $('#users-search').keydown(function (e) {
          if (e.keyCode == 13) {
            searchUsers();
            e.preventDefault();
            return false; 
          }
        });
    });
</script>