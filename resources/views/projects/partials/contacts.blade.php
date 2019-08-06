<div  id="contacts-content">
	<div class="uk-width-1-1 uk-align-center	" style="width: 90%">
		<a name="organizationtop"></a>
		<div class="uk-overflow-container uk-margin-top">
			<div uk-grid class="uk-margin-remove">
				<h4 class="uk-text-left uk-width-2-3" style="padding-top: 8px;">{{ number_format($users->count(), 0) }} TOTAL USERS</h4>
			</div>
			<hr>
			{{-- {{ $users->links() }} --}} <a onClick="dynamicModalLoad('{{ $project->id }}/add-user-to-project');" class="uk-badge uk-margin-top uk-badge-success"><i class="a-circle-plus"></i> ADD USER TO PROJECT</a>
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
							<small>M | O : NAME</small>
						</th>
						<th>
							<small>M | O : ORGANIZATION</small>
						</th>
						<th>
							<small>M | O : ADDRESS</small>
						</th>
						<th>
							<small>M | O : PHONE</small>
						</th>
						<th>
							<small>M | O : EMAIL</small>
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
							{{-- manager --}}
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultUser({{ $user->id }}, {{ in_array($user->id, $project_user_ids) }})" id="contact-user-{{ $user->id }}" name="contact" type="radio" uk-tooltip="" title="MAKE THIS USER AS DEFAULT CONTACT TO REPORT" aria-expanded="false" {{ $default_user_id == $user->id ? 'checked=checked' : '' }}>  |
							{{-- owner --}}
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOwner({{ $user->id }}, {{ in_array($user->id, $project_user_ids) }})" id="owner-user-{{ $user->id }}" name="owner" type="radio" uk-tooltip="" title="MAKE THIS USER AS DEFAULT OWNER TO PROJECT" aria-expanded="false" {{ $default_owner_id == $user->id ? 'checked=checked' : '' }}>
							 <span style="margin-left: 2px">{{ $user->name }}</span><i  onclick="editUserName({{ $user->id }})" class="a-pencil" uk-tooltip="" title="EDIT NAME" aria-expanded="false"></i><br>
							<small class="uk-margin-large-left">@if($user->role){{ strtoupper($user->role->role->role_name) }}@else NO ACCESS @endIf</small>
						</td>
						@php
						$user_orgs = $user->user_organizations->where('project_id', $project->id);
						@endphp
						<td>
							@if($user->organization_details)
							@php
							$exists_in_uo = $user_orgs->where('devco', 1)->where('organization_id', $user->organization_details->id)->first();
							@endphp
							{{-- manager --}}
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOrganization({{ $user->organization_details->id }}, {{ $user->id }},  1)" name="organization" id="organization-{{ $user->organization_details->id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ORGANIZATION FOR REPORT" aria-expanded="false" {{ (($exists_in_uo && $exists_in_uo->default) || (!$default_org && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
							{{-- |
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOwnerOrganization({{ $user->organization_details->id }}, {{ $user->id }},  1)" name="owner_organization" id="owner-organization-{{ $user->organization_details->id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT OWNER ORGANIZATION" aria-expanded="false" {{ (($exists_in_uo && $exists_in_uo->default) || (!$default_org && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
							 --}}
							<small>
								{{ $user->organization_details->organization_name }}
							</small>
							<hr class="dashed-hr  uk-margin-small-bottom">
							{{-- @elseif(!is_null($user->organization) && ($user->organization != ''))
								{{ $user->organization }}
							</small> <hr class="dashed-hr  uk-margin-small-bottom">
							<i id="allita-organization-{{ $user->id }}" class="a-pencil" uk-tooltip="" title="EDIT ORGANIZATION NAME" aria-expanded="false"></i> --}}
							@elseif(!$user->organization_details)
							<div class="uk-text-muted uk-align-left">NA</div>
							<hr class="dashed-hr  uk-margin-small-bottom">
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
							<small class="use-hand-cursor" id="add-organization-{{ $user->id }}" onclick="addOrganization({{ $user->id }})"  uk-tooltip="" title="ADD NEW ORGANIZATION" aria-expanded="false"><i class="a-circle-plus use-hand-cursor"></i> ADD ANOTHER ORGANIZATION</small>
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
								<input class="uk-radio uk-align-left uk-margin-small-top" onchange="makeDefaultAddress({{ $user->organization_details->address->id }}, {{ $user->id }}, 1)" name="address" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ADDRESS FOR REPORT" aria-expanded="false" {{ (($exists_in_ua && $exists_in_ua->default) || (!$default_addr && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}></span>
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
							<small class="use-hand-cursor" id="add-address-{{ $user->id }}" onclick="addAddress({{ $user->id }})"  uk-tooltip="" title="ADD NEW ADDRESS" aria-expanded="false"><i class="a-circle-plus use-hand-cursor"></i> ADD ANOTHER ADDRESS</small>
						</td>
						{{-- PHONE NUMBER --}}
						@php
						// check if this project has entries in user phone numbers
						$user_phones = $user->user_phone_numbers->where('project_id', $project->id);
						@endphp
						<td>
							<small>
								{{-- This was from code in user tab, show org phone as default phone, chec if exists - DEVCO --}}
								@if($user->organization_details && $user->organization_details->phone_number_formatted() != '')
								@php
								// Check if this user phone number exists in user phone number
								$exists_in_up = $user_phones->where('devco', 1)->where('phone_number_id', $user->organization_details->default_phone_number_id)->first();
								@endphp
								{{-- Ie none of the phone number is marked as default, we mark default devco contact phone number as default --}}
								<input class="uk-radio" onchange="makeDefaultPhonenumber({{ $user->organization_details->default_phone_number_id }}, {{ $user->id }},  1)" name="phone_number" id="phone_number-{{ $user->organization_details->default_phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT PHONE NUMBER FOR REPORT" aria-expanded="false" {{ (($exists_in_up && $exists_in_up->default) || (!$default_phone && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
									{{ $user->organization_details->phone_number_formatted() }}
								<hr class="dashed-hr  uk-margin-small-bottom">
								@else
								<div class="uk-text-muted uk-align-left">NA</div>
								<hr class="dashed-hr  uk-margin-small-bottom">
								@endif
							</small>
								@php
								// Non devco phone numbers
								$user_phones = $user_phones->where('devco', '!=', 1);
								@endphp
								@foreach($user_phones as $phone)
								<input class="uk-radio" onchange="makeDefaultPhonenumber({{ $phone->id }}, {{ $user->id }})" name="phone_number" id="phone_number-{{ $phone->phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT PHONE NUMBER FOR REPORT" aria-expanded="false" {{ $phone->default ? 'checked=checked': '' }}>
								<small>
									{{ $phone->phone_number_formatted() }}
								</small>
								<i onclick="editPhoneNumber({{ $phone->id }})" id="project-phone-number-{{ $phone->id }}" class="a-pencil" uk-tooltip="" title="EDIT PHONE NUMBER" aria-expanded="false"></i>
								<hr class="dashed-hr  uk-margin-small-bottom">
								@endforeach
							<small class="use-hand-cursor" id="add-phone-{{ $user->id }}" onclick="addPhoneNumber({{ $user->id }})"  uk-tooltip="" title="ADD ANOTHER PHONE NUMBER" aria-expanded="false"><i class="a-circle-plus use-hand-cursor"></i> ADD ANOTHER PHONE NUMBER</small>
						</td>
						{{-- EMAIL ADDRESS --}}
						@php
						// check if this project has entries in user phone numbers
						$user_emails = $user->user_emails->where('project_id', $project->id);
						@endphp
						<td>
							{{-- {{ dd($user->person->email) }} --}}
							@if($user->person && $user->person->email)
							@php
							// Check if this user phone emails exists in user emails
							$exists_in_ue = $user_emails->where('devco', 1)->where('email_address_id', $user->person->default_email_address_id)->first();
							@endphp
							<input class="uk-radio" onchange="makeDefaultEmail({{ $user->person->default_email_address_id }}, {{ $user->id }},  1)" name="email" id="email-{{ $user->person->default_email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT EMAIL FOR REPORT" aria-expanded="false" {{ (($exists_in_ue && $exists_in_ue->default) || (!$default_phone && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
							<small><a class="{{ !$user->active ? 'uk-text-muted' : '' }}" href="mailto:{{ $user->person->email->email_address }}">{{ $user->person->email->email_address }}</a>
							</small>
							<hr class="dashed-hr  uk-margin-small-bottom">
							@else
								<div class="uk-text-muted uk-align-left">NA</div>
								<hr class="dashed-hr  uk-margin-small-bottom">
							@endif
							@php
								// Non devco emails
								$user_emails = $user_emails->where('devco', '!=', 1);
							@endphp
								@foreach($user_emails as $email)
								<input class="uk-radio" onchange="makeDefaultEmail({{ $email->id }}, {{ $user->id }})" name="email" id="email-{{ $email->email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT EMAIL FOR REPORT" aria-expanded="false" {{ $email->default ? 'checked=checked': '' }}>
								<small>
									{{ $email->email_address->email_address }}
								</small>
								<i onclick="editEmail({{ $email->id }})" id="project-email-{{ $email->id }}" class="a-pencil" uk-tooltip="" title="EDIT EMAIL ADDRESS" aria-expanded="false"></i>
								<hr class="dashed-hr  uk-margin-small-bottom">
								@endforeach
							<small class="use-hand-cursor" id="add-email-{{ $user->id }}" onclick="addEmail({{ $user->id }})"  uk-tooltip="" title="ADD ANOTHER EMAIL ADDRESS" aria-expanded="false"><i class="a-circle-plus use-hand-cursor"></i> ADD ANOTHER EMAIL ADDRESS</small>
						</td>
						@php
						$pm_access = $user->pm_access();
						@endphp
						@if(in_array($user->id, $project_user_ids))
						@php
						$project_roles = \App\Models\ProjectContactRole::with('projectRole')->where('person_id', $user->person_id)->where('project_id', $project->id)->get();
						$roles = [];
						foreach ($project_roles as $key => $pj) {
							if($pj->projectRole) {
								array_push($roles, $pj->projectRole->role_name);
							}
						}
						$user_roles = implode(',', $roles);
						@endphp
						<td>
							<span><i class="a-file-gear_1" data-uk-tooltip title="{{ strtoupper($user_roles)}}"></i>  | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ in_array($user->id, $allita_user_ids) ? 'REMOVE ALLITA ACCESS TO REPORTS' : 'ADD ALLITA ACCESS TO REPORTS' }}"> <i onclick="addAllitaAccess({{ $user->id }}, {{ in_array($user->id, $allita_user_ids) }})" class="{{ in_array($user->id, $allita_user_ids) ? 'a-avatar-approve' : 'a-avatar-fail uk-text-muted' }}" style="position: relative;top: -1px;"></i>  |
							</span>
							<span class="" data-uk-tooltip title="{{ $pm_access ? 'USER HAS ACCESS TO REPORTS' : 'USER HAS NO ACCESS TO REPORTS' }}"> <i class="{{ $pm_access ? 'a-file-approve' : 'a-file-fail' }}"></i>
							</span>
						</td>
						@else
						<td>
							<span><i class="uk-text-muted a-file-gear_1"></i> | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ $pm_access ? 'CLICK TO REMOVE REPORTS ACCESS' : 'USER HAS NO ACCESS TO REPORTS' }}" onclick="editUser({{ $user->id }})"><i class="{{ $pm_access ? 'a-file-approve' : 'a-file-fail' }}"></i>
							</span>
						</td>
						@endif
					</tr>
					@endforeach
				</tbody>
			</table>
			{{-- <a name="userbottom"></a> --}}
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
	    		loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
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

	function addPhoneNumber(userId) {
		dynamicModalLoad('add-phone-to-user/'+userId+'/{{ $project->id }}');
	}

	function editPhoneNumber(phonenumberId) {
		dynamicModalLoad('edit-phone-of-user/'+phonenumberId+'/{{ $project->id }}');
	}

	function makeDefaultPhonenumber(phonenumberId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-phone") }}",
			method: 'post',
			data: {
				phone_number_id : phonenumberId,
				user_id : userId,
				project_id : {{ $project->id }},
				devco : devco,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Phone Number', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function addAllitaAccess(userId, hasAccess) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.add-allita-access-to-user") }}",
			method: 'post',
			data: {
				user_id : userId,
				project_id : {{ $project->id }},
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					if(hasAccess) {
						UIkit.notification('<span uk-icon="icon: check"></span> Removed allita access to user', {pos:'top-right', timeout:1000, status:'success'});
		    		loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
					} else {
						UIkit.notification('<span uk-icon="icon: check"></span> Added allita access to user', {pos:'top-right', timeout:1000, status:'success'});
		    		loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
					}
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function addEmail(userId) {
		dynamicModalLoad('add-email-to-user/'+userId+'/{{ $project->id }}');
	}

	function editEmail(emailId) {
		dynamicModalLoad('edit-email-of-user/'+emailId+'/{{ $project->id }}');
	}

	function makeDefaultEmail(emailId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-email") }}",
			method: 'post',
			data: {
				email_address_id : emailId,
				user_id : userId,
				project_id : {{ $project->id }},
				devco : devco,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Email Address', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	// Owner related
	function makeDefaultOwner(userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-owner") }}",
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
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Owner', {pos:'top-right', timeout:1000, status:'success'});
	    		loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}



</script>
