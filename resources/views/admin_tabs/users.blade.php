 @php
$projectIds = [];
$projectsHtml = '';
$thisProjectHtml = '';
@endphp
 <a name="usertop"></a>
 <div class="uk-overflow-container uk-margin-top" >

 	<div uk-grid class="uk-margin-remove">
 		<h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{ number_format($users->total(), 0) }} TOTAL USERS</h4>
 		<div class="uk-width-1-3 uk-text-right">
 			<input id="users-search" name="users-search" type="text" value="" class=" uk-input" placeholder="Search by user name (press enter)">
 		</div>
 	</div>
 	<hr>
 	{{ $users->links() }} <a onClick="dynamicModalLoad('createuser');" class="uk-badge uk-margin-top uk-badge-success"><i class="a-circle-plus"></i> CREATE USER</a> <a href="#userbottom" id="user-scroll-to-top" class="uk-badge uk-overlay-background uk-margin-top"><i class="a-circle-down"></i> BOTTOM OF LIST</a> @if( Session::get('users-search')) &nbsp;&nbsp; <a onclick="$('#users-search').val(''); searchUsers();" class="uk-badge uk-overlay-background uk-margin-top" uk-tooltip title="CLICK TO CLEAR SEARCH"><i class="a-circle-cross"></i> {{ strtoupper(Session::get('users-search')) }} &nbsp;</a> @endIf
 	<table class="uk-table uk-table-hover uk-table-striped uk-overflow-container small-table-text" >
 		<thead >
 			<th>
 				<small>ID</small>
 			</th>
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
 				<small>ACTIONS</small>
 			</th>
 		</thead>
 		<tbody>
 			@foreach($users as $user)
 			<tr class="{{ !$user->active ? 'uk-text-muted' : '' }}">
 				<td><small>{{$user->id}}</small></td>
 				<td>@can('access_root')<span uk-tooltip title="USER_ID: {{$user->id}} USER_KEY: {{$user->devco_key}}">@endCan{{ $user->first_name }} {{ $user->last_name }}@can('access_root')</span>@endCan <br /><small>
 					@if($user->role)ROLE:{{strtoupper($user->role->role->role_name)}}@else NO ACCESS @endIf</small>
 					@php
							$devco_projects = $user->person->projects;
									// if($user->id == 6380)
									// 	dd($devco_projects);
							$allita_projects = $user->report_access->pluck('project');
							$all_projects = $devco_projects->merge($allita_projects)->unique();
							@endphp
							@if(count($all_projects) > 0)
							<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
							<small>
								<span onclick="$('#user-{{ $user->id }}-projects').slideToggle();" class="use-hand-cursor" uk-tooltip title="CLICK TO VIEW PROJECT(S)"><i class="a-info-circle"></i>
									Total Projects: {{ count($all_projects) }}
								</span>
							</small>
							<div id="user-{{ $user->id }}-projects" style="display: none;">
								<small><ul>
									@forEach($all_projects as $p)
									<li>
										{{ $p->project_number }} : {{ $p->project_name }}
									</li>
									@php
									array_push($projectIds, $p->id);
									$projectsHtml = $projectsHtml . $p->project_number . ' : ' . $p->project_name . '<br>';
									@endphp
									@endForEach
								</ul></small>
							</div>
							@endIf
 				</td>
 				<td><small>@if($user->organization_name)
 					{{ $user->organization_details->organization_name }}@else NA @endif</small></td>
 					<td><small>@if($user->has_address())
 						<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$user->organization_details->address->formatted_address())) }}" class="uk-text-muted uk-align-left">
 						<i class="a-marker-basic uk-text-muted uk-link"></i>
 					</a>
 					<div class="uk-align-left">
 						{!! $user->organization_details->address->formatted_address() !!}
 					</div>
 					@else <i class="a-marker-basic uk-link uk-align-left"></i><div class="uk-text-muted uk-align-left">NA</div> @endif
 				</small></td>
 				<td><small>@if($user->has_organization())
 					@if($user->area_code && $user->phone_number)
 					{{ $user->organization_details->phone_number_formatted() }}
 					@endif
 				@endif</small></td>
 				<td><small><a class="{{ !$user->active ? 'uk-text-muted' : '' }}" href="mailto:{{ $user->email }}">{{ $user->email }}</a></small></td>
				{{--  				<td class="use-hand-cursor" uk-tooltip="title:CLICK TO SET ROLES" onclick="setRoles({{ $user->id }})">@if($user->role_name){{ $user->role_name }}@else <i class="a-circle-plus"></i>@endif</td>
				 --}}
				 <td class="use-hand-cursor">
				 	@if(is_null($user->role_id) || $user->role_id < $user_role)
					 	<span data-uk-tooltip title="Edit User <br> {{ $user->role_name ? 'Role: ' . $user->role_name : '' }}" onclick="editUser({{ $user->id }})"><i class="a-edit "></i>
					 	</span>
					 	<span data-uk-tooltip title="Reset Password <br> {{ $user->role_name ? 'Role: ' . $user->role_name : '' }}" onclick="resetPassword({{ $user->id }})"><i class="a-password "></i>
					 	</span>
					 	@if($user->active)
						 	<span data-uk-tooltip title="USER IS ACTIVE - CLICK TO DEACTIVATE THE USER" onclick="deactivateUser({{ $user->id }})"><i class="a-avatar"></i>
						 	</span>
					 	@else
						 	<span data-uk-tooltip title="USER IS IN-ACTIVE - CLICK TO ACTIVATE THE USER" onclick="activateUser({{ $user->id }})"><i class="a-avatar" style="color: lightgray;"></i>
						 	</span>
					 	@endif
				 	@else
				 		<span> {{ $user->role_name ? 'Role: ' . $user->role_name : '' }}</span>
				 	@endif
				 </td>
				 {{-- <td class="use-hand-cursor" data-uk-tooltip title="Edit User <br> {{ $user->role_name ? 'Role: ' . $user->role_name : '' }}" onclick="editUser({{ $user->id }})"><i class="a-edit"></i>
				 </td> --}}
			</tr>
			@endforeach
		</tbody>
	</table>
	<a name="userbottom"></a>
	{{ $users->links() }}
	<a href="#usertop" id="user-scroll-to-top" class="uk-badge uk-badge-success uk-margin-top"><i class="a-circle-up"></i> BACK TO TOP OF LIST</a>
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

	function editUser(id) {
		dynamicModalLoad('edituser/'+id);
	}

	function resetPassword(id) {
		dynamicModalLoad('resetpassword/'+id);
	}

	function deactivateUser(id) {
		dynamicModalLoad('deactivateuser/'+id);
	}

	function activateUser(id) {
		dynamicModalLoad('activateuser/'+id);
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