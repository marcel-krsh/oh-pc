<script type='text/javascript'>
$=jQuery;
$(document).ready(function() {
	//TODO: Combine these into a single function
	$('.activateuser').click(function(e) {
		e.preventDefault();
		       $.ajax({
                  type: "GET",
                  url: $(this).attr('href'),
                  data: $("#userform").serialize(),
                  dataType: 'json',
                  success: function(data)
                  {
                  	if (data.status > 0) {
                  		UIkit.modal.alert('<h2>I have activated that user.</h2>');
                        loadDashBoardSubTab('dashboard','user_list');
                  	} else {
                  		UIkit.modal.alert('<h2>An error occured.</h2><p> All I know is that the server had this to say about it:</p><br />'+data.message);
                  	}
                  }
               });
	});
	$('.deactivateuser').click(function(e) {
		e.preventDefault();
		       $.ajax({
                  type: "GET",
                  url: $(this).attr('href'),
                  data: $("#userform").serialize(),
                  dataType: 'json',
                  success: function(data)
                  {
                  	if (data.status > 0) {
                  		UIkit.modal.alert('<h2>I have disabled that user.</h2>');
                        loadDashBoardSubTab('dashboard','user_list');
                  	} else {
                  		UIkit.modal.alert('<h2>An error occured.</h2><p> All I know is that the server had this to say about it:</p><br />'+data.message);
                  	}
                  }
               });
	});
})
</script>
<script>
    // disable infinite scroll:
    window.getContentForListId = 0;
    window.openedCommunication = 0;
    window.currentCommunication = "";
    window.restoreLastCommunicationItem = "";
    window.resetOpenCommunicationId == 0;
</script>

<div uk-grid class="uk-child-width-1-1">
   <div uk-grid class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid-collapse">
      <div>
         <div class="uk-badge uk-text-right " style="background: #005186; margin-top: 15px;">
               &nbsp;{{ $totalUsers }} TOTAL USERS&nbsp;
            </div>
         </div>
      <div>
         <a onClick="dynamicModalLoad('createuser');" class="uk-button uk-margin-top uk-button-default uk-button-small uk-align-right">CREATE USER</a> 
         <a onclick="$('.inactiveUser').toggle();" class="uk-button uk-margin-top uk-button-default uk-button-small uk-align-right"><span class="inactiveUser">SHOW</span><span class="inactiveUser" style="display: none;">HIDE</span> INACTIVE USERS</a>
      </div>
   </div>
	<div class="uk-margin-top ">
   <?php $currentEntity = ""; ?>
		<div class="uk-overflow-auto" class="margin:4px;">
		<table class="uk-table uk-table-hover ">
		<thead >
			<tr >
				<th >Name</th><th >Email</th><th >API Token</th><th >Actions</th>
			</tr>
		</thead>
		<tbody>
    @foreach ($myUsers as $user ) 
      @if($currentEntity != $user->entity_name)
      <tr style="background-color: #f7f7f7">
         <td colspan="4"><strong><a class=" entity{{$user->entity_id}}-up-down" onclick="$('.entity{{$user->entity_id}}').slideDown();$('.entity{{$user->entity_id}}-up-down').toggle();"> {{$user->entity_name}}</a><a class=" entity{{$user->entity_id}}-up-down" onclick="$('.entity{{$user->entity_id}}').slideUp();$('.entity{{$user->entity_id}}-up-down').toggle();" style="display: none;"> {{$user->entity_name}}</a></strong></td>
      </tr>
      <?PHP $currentEntity = $user->entity_name; ?>
      @endIf
    	<tr @if($user->active == 0) style="display:none;" class="inactiveUser"@else class="entity{{$user->entity_id}}" style="display:none;" @endIf>
         <td >
            <div class="user-badge user-badge-{{ $user->badge_color }} {{ strtolower($user->name) }}"> {{ userInitials($user->name) }} </div><div style="display: inline-block;"><a onClick="dynamicModalLoad('user/{{ $user->id }}');" target="_blank" class="">{{ $user->name }}</a></div></td>
         <td >
            <a herf="mailto:{{ $user->email }}" class=""><i class="a-envelope-4"></i> {{ $user->email }}</a>
         </td>
         
         <td >
            {{ $user->api_token}}
         </td>
         <td >
            @can('edit-user')
            <a onClick="dynamicModalLoad('user/{{ $user->id }}');" target="_blank" class="uk-link"><i class="a-pencil-2"></i></a>&nbsp;
            @endcan
            
            <a @if($user->active == 1) style="display:none;" @endIf class='activateuser uk-link-muted' href="/user/activate/{{ $user->id }}" uk-tooltip title="Click to Activate User" class="uk-link-muted"><i class="a-locked-2" onmouseout="$(this).removeClass('a-avatar');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-avatar');"></i></a>
            <a @if($user->active == 0) style="display:none;" @endIf class='deactivateuser' href="/user/deactivate/{{ $user->id }}" uk-tooltip title="Click to Deactivate User"><i class="a-avatar" onmouseover="$(this).removeClass('a-avatar');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-avatar');"></i></a>

         </td>
         </tr>
    @endforeach
		</tbody>
		</table>
      @if(session('editUserRoles')>0)
         <script>
         dynamicModalLoad('user/{{session('editUserRoles')}}');
         </script>
         @php session(['editUserRoles'=>0]); @endphp
      @endIf
		</div>
	</div>
</div>

<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>

