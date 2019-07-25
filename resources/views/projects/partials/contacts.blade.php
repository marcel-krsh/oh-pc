<div  id="contacts-content">
	<div class="uk-width-1-1 uk-align-center	" style="width: 90%">
		<a name="organizationtop"></a>
		<div class="uk-overflow-container uk-margin-top">
			<div uk-grid class="uk-margin-remove">
				<h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{ number_format($users->total(), 0) }} TOTAL USERS</h4>
			</div>
			<hr>
			{{ $users->links() }} <a onClick="dynamicModalLoad('{{ $project->id }}/add-user-to-project');" class="uk-badge uk-margin-top uk-badge-success"><i class="a-circle-plus"></i> ADD USER TO PROJECT</a>
			<div class="uk-margin-top">
				<div class="uk-background-muted uk-padding uk-panel">
					<p><strong>NOTE:</strong> Please select the radio button next to the contact information of any contact that should be used on reports.<br> The default selection is applied to the primary manager designated in DEV|CO. <br>However you can choose any combination of User, Organization, and Address across users.
					</p>
				</div>
			</div>
			<table class="uk-table uk-table-hover uk-table-striped uk-overflow-container small-table-text">
				<thead>
					<tr>
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
							<small>REPORT ACCESS</small>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($users as $user)
					<tr class="{{ !$user->active ? 'uk-text-muted' : '' }}">
						<td>
							<input class="uk-radio" onchange="makeDefaultUser({{ $user->id }}, {{ in_array($user->id, $project_user_ids) }})" id="contact-user-{{ $user->id }}" name="contact" type="radio" uk-tooltip="" title="" aria-expanded="false" {{ $default_user_id == $user->id ? 'checked=checked' : '' }}> {{ $user->name }}<i  onclick="editUserName({{ $user->id }})" class="a-pencil" uk-tooltip="" title="EDIT NAME" aria-expanded="false"></i><br>
							<small style="margin-left:20px;">@if($user->role){{ strtoupper($user->role->role->role_name) }}@else NO ACCESS @endIf</small>
						</td>
						@php
						$user_orgs = $user->user_organizations->where('project_id', $project->id);
						@endphp
						<td>
							@if($user->organization_details)
							@php
							$exists_in_uo = $user_orgs->where('devco', 1)->where('organization_id', $user->organization_details->id)->first();
							@endphp
							<input class="uk-radio" onchange="makeDefaultOrganization({{ $user->organization_details->id }}, {{ $user->id }},  1)" name="organization" id="organization-{{ $user->organization_details->id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ORGANIZATION FOR REPORT" aria-expanded="false" {{ ($exists_in_uo && $exists_in_uo->default) ? 'checked=checked': '' }}>
							<small>
								{{ $user->organization_details->organization_name }}
							</small>
						</small> <hr class="dashed-hr  uk-margin-small-bottom">
							{{-- @elseif(!is_null($user->organization) && ($user->organization != ''))
								{{ $user->organization }}
							</small> <hr class="dashed-hr  uk-margin-small-bottom">
							<i id="allita-organization-{{ $user->id }}" class="a-pencil" uk-tooltip="" title="EDIT ORGANIZATION NAME" aria-expanded="false"></i> --}}
							@elseif(!$user->organization_details)
							NA
						</small> <hr class="dashed-hr  uk-margin-small-bottom">
						@endif
						@php
						$user_orgs = $user_orgs->where('devco', '!=', 1);
						@endphp
						@foreach($user_orgs as $org)
						<input class="uk-radio" onchange="makeDefaultOrganization({{ $org->id }}, {{ $user->id }})" name="organization" id="organization-{{ $org->organization_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ORGANIZATION FOR REPORT" aria-expanded="false" {{ $org->default ? 'checked=checked': '' }}>
						<small>
							{{ $org->organization->organization_name }}
						</small>
						<i onclick="editOrganization({{ $org->id }})" id="project-organization-{{ $org->id }}" class="a-pencil" uk-tooltip="" title="{{ is_null($org->organization->organization_key) ? 'EDIT ORGANIZATION NAME / REMOVE ORGANIZATION' : 'REMOVE ORGANIZATION'}}" aria-expanded="false"></i>
						<hr class="dashed-hr  uk-margin-small-bottom">
						@endforeach
						{{--  --}}
						<small id="add-organization-{{ $user->id }}" onclick="addOrganization({{ $user->id }})"  uk-tooltip="" title="ADD NEW ORGANIZATION" aria-expanded="false"><i class="a-circle-plus"></i> ADD ANOTHER ORGANIZATION</small>
					</td>
					<td>
						@php
						$user_addresses = $user->user_addresses->where('project_id', $project->id);
						@endphp
						@if(!is_null($user->organization_id) && $user->organization_details)
						@php
						$exists_in_ua = $user_addresses->where('devco', 1)->where('address_id', $user->organization_details->address->id)->first();
						@endphp
						<small>
							<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$user->organization_details->address->formatted_address())) }}" class="uk-text-muted uk-align-left"><i class="a-marker-basic uk-text-muted uk-link"></i></a>
							<input class="uk-radio uk-align-left uk-margin-small-top" onchange="makeDefaultAddress({{ $user->organization_details->address->id }}, {{ $user->id }}, 1)" name="address" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ADDRESS FOR REPORT" aria-expanded="false" {{ ($exists_in_ua && $exists_in_ua->default) ? 'checked=checked': '' }}></span>
							<div class="uk-align-left">
								{!! $user->organization_details->address->formatted_address() !!}
							</div>
						</small>
						<br><hr class="dashed-hr  uk-margin-small-bottom">
						@else
						<div class="uk-text-muted uk-align-left">NA</div>
						<br><hr class="dashed-hr  uk-margin-small-bottom">
						@endif
						@php
						$user_addresses = $user_addresses->where('devco', '!=', 1);
						@endphp
						@foreach($user_addresses as $address)
						<small>
						<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$address->address->formatted_address())) }}" class="uk-text-muted uk-align-left"><i class="a-marker-basic uk-text-muted uk-link"></i></a>
						<input class="uk-radio uk-align-left uk-margin-small-top" onchange="makeDefaultAddress({{ $address->id }}, {{ $user->id }})" name="address" id="address-{{ $address->address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ADDRESS FOR REPORT" aria-expanded="false" {{ $address->default ? 'checked=checked': '' }}>
							<div class="uk-align-left">
								{!! $address->address->formatted_address() !!}
							</div>
						<i onclick="editAddress({{ $address->id }})" id="project-address-{{ $address->id }}" class="a-pencil" uk-tooltip="" title="{{ is_null($address->address->address_key) ? 'EDIT ADDRESS / REMOVE ADDRESS' : 'REMOVE ADDRESS'}}" aria-expanded="false"></i>
						</small>
						<hr class="dashed-hr  uk-margin-small-bottom">
						@endforeach
						<small id="add-address-{{ $user->id }}" onclick="addAddress({{ $user->id }})"  uk-tooltip="" title="ADD NEW ADDRESS" aria-expanded="false"><i class="a-circle-plus"></i> ADD ANOTHER ADDRESS</small>

						{{-- <small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left">
							<input class="uk-radio" name="address" type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
							<div class="uk-align-left">
								1990A Kingsgate Road<br>Springfield OH 45502 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
							</div>
						</small><br><hr class="dashed-hr  uk-margin-small-bottom">
						<small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
							<div class="uk-align-left">
								123 Sesame Street<br>New York NY 12345 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
							</div>
							<br><hr class="dashed-hr uk-margin-small-bottom"><i class="a-circle-plus" uk-tooltip="" title="" style="
							" aria-expanded="false"></i> ADD ANOTHER ADDRESS
						</small> --}}
					</td>
					<td>
						<small> (937) 342-9071
						</small>
					</td>
					<td><small><a class="" href="mailto:RHellmuth6236@allita.org">RHellmuth6236@allita.org</a></small></td>
					<td>
						<span data-uk-tooltip="" title="" aria-expanded="false"><i class="a-file-gear_1"></i> | <i class="a-file-approve"></i>
						</span>
					</td>
				</tr>




					{{-- <tr class="">
						<td><input name="contact" type="radio" uk-tooltip="" title="" aria-expanded="false"> Brian Greenwood <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i><br>
							<small>PROPERTY MANAGER</small>
						</td>
						<td>
							<input name="organization" type="radio" uk-tooltip="" title="" aria-expanded="false"><small> Greenwood 360, LLC <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i></small> <hr class="dashed-hr uk-margin-small-bottom"> <input name="organization" type="radio" uk-tooltip="" title="" aria-expanded="false"><small> Allita 360 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i></small><br><hr class="dashed-hr uk-margin-small-bottom"><small><i class="a-circle-plus" uk-tooltip="" title="" aria-expanded="false"></i> ADD ANOTHER ORGANIZATION</small>
						</td>
						<td>
							<small> <span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									300 Marconi Blvd<br>Columbus OH 43215 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
							</small>
							<br><hr class="dashed-hr uk-margin-small-bottom">
							<small><span target="_blank" href="https://www.google.com/maps?q=1990A+Kingsgate+Road+Springfield+OH+45502" class="uk-text-muted uk-align-left"><input type="radio" uk-tooltip="" title="" aria-expanded="false"></span>
								<div class="uk-align-left">
									321 Sesame Street<br>New York NY 12345 <i class="a-pencil" uk-tooltip="" title="" aria-expanded="false"></i>
								</div>
								<br><hr class="dashed-hr uk-margin-small-bottom"><i class="a-circle-plus" uk-tooltip="" title="" style=" " aria-expanded="false"></i> ADD ANOTHER ADDRESS
							</small>
						</td>
						<td>
							<small> (937) 342-9071
							</small>
						</td>
						<td><small><a class="" href="mailto:RHellmuth6236@allita.org">BrianGreenwood@allita.org</a></small></td>

						<td>
							<span data-uk-tooltip="" title="" aria-expanded="false"><i class="a-file-gear_1" style="color:rgba(0,0,0,.3)"></i> | <i class="a-file-approve"></i>
							</span>
						</td>
					</tr> --}}
					@endforeach
				</tbody>
			</table>
			{{-- <a name="userbottom"></a> --}}
			{{ $users->links() }}
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.page-link').click(function(){
			$('#contacts-content').load($(this).attr('href'));
			return false;
		});
	});

	function editUser(id) {
		dynamicModalLoad('remove-user-from-project/{{ $project->id }}/'+id);
	}

	function addOrganization(userId) {
		dynamicModalLoad('add-organization-to-user/'+userId+'/{{ $project->id }}');
	}

	function editOrganization(orgId) {
		dynamicModalLoad('edit-organization-of-user/'+orgId+'/{{ $project->id }}');
	}

	function makeDefaultOrganization(orgId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-organization") }}",
			method: 'post',
			data: {
				organization_id : orgId,
				user_id : userId,
				project_id : {{ $project->id }},
				devco_org : devco,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Organization', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function editUserName(userId) {
		dynamicModalLoad('edit-name-of-user/'+userId+'/{{ $project->id }}');
	}

	function makeDefaultUser(userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-user") }}",
			method: 'post',
			data: {
				user_id : userId,
				project_id : {{ $project->id }},
				devco : devco,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default User', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function addAddress(userId) {
		dynamicModalLoad('add-address-to-user/'+userId+'/{{ $project->id }}');
	}

	function makeDefaultAddress(addressId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-address") }}",
			method: 'post',
			data: {
				address_id : addressId,
				user_id : userId,
				project_id : {{ $project->id }},
				devco : devco,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Address', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function editAddress(addressId) {
		dynamicModalLoad('edit-address-of-user/'+addressId+'/{{ $project->id }}');
	}






</script>
