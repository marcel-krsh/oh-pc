@php
$projectIds = [];
@endphp
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
							M &nbsp; | &nbsp;O : <small>NAME</small>
						</th>
						<th>
							M &nbsp; | &nbsp;O : <small>ORGANIZATION</small>
						</th>
						<th>
							M &nbsp; | &nbsp;O : <small>ADDRESS</small>
						</th>
						<th>
							M &nbsp; | &nbsp;O : <small>PHONE</small>
						</th>
						<th>
							M &nbsp; | &nbsp;O : <small>EMAIL</small>
						</th>
						<th>
							<small>ACCESS</small>
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
							$exists_in_uo = $user_orgs->where('devco', 1)->where('organization_id', $user->organization_details->id)->where('default', 1)->first();
							$exists_in_uo_owner = $user_orgs->where('devco', 1)->where('organization_id', $user->organization_details->id)->where('owner_default', 1)->first();
							@endphp
							{{-- manager --}}
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOrganization({{ $user->organization_details->id }}, {{ $user->id }},  1)" name="organization" id="organization-{{ $user->organization_details->id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ORGANIZATION FOR REPORT" aria-expanded="false" {{ (($exists_in_uo) || (!$default_org && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}> |
							{{-- Owner --}}
							<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOwnerOrganization({{ $user->organization_details->id }}, {{ $user->id }},  1)" name="owner_organization" id="owner-organization-{{ $user->organization_details->id }}" type="radio" uk-tooltip="" title="MAKE THIS AS DEFAULT OWNER ORGANIZATION" aria-expanded="false" {{ (($exists_in_uo_owner) || (!$default_owner_org && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
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
							{{-- Manager --}}
							<input class="uk-radio" onchange="makeDefaultOrganization({{ $org->id }}, {{ $user->id }})" name="organization" id="organization-{{ $org->organization_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ORGANIZATION FOR REPORT" aria-expanded="false" {{ $org->default ? 'checked=checked': '' }}> |
							{{-- owner --}}
							<input class="uk-radio" onchange="makeDefaultOwnerOrganization({{ $org->id }}, {{ $user->id }})" name="owner_organization" id="owner-organization-{{ $org->organization_id }}" type="radio" uk-tooltip="" title="MAKE THIS AS DEFAULT OWNER ORGANIZATION" aria-expanded="false" {{ $org->owner_default ? 'checked=checked': '' }}>
							<small>
								{{ $org->organization->organization_name }}
							</small>
							<i onclick="editOrganization({{ $org->id }})" id="project-organization-{{ $org->id }}" class="a-pencil" uk-tooltip="" title="{{ is_null($org->organization->organization_key) ? 'EDIT ORGANIZATION NAME / REMOVE ORGANIZATION' : 'REMOVE ORGANIZATION' }}" aria-expanded="false"></i>
							<hr class="dashed-hr  uk-margin-small-bottom">
							@endforeach
							<small class="use-hand-cursor" id="add-organization-{{ $user->id }}" onclick="addOrganization({{ $user->id }})"  uk-tooltip="" title="ADD NEW ORGANIZATION" aria-expanded="false"><i class="a-circle-plus use-hand-cursor"></i> ADD ANOTHER ORGANIZATION</small>
						</td>
						<td>
							@php
							$user_addresses = $user->user_addresses->where('project_id', $project->id);
							@endphp
							@if(!is_null($user->organization_id) && $user->organization_details)
							@php
							$exists_in_ua = $user_addresses->where('devco', 1)->where('address_id', $user->organization_details->address->id)->where('default', 1)->first();
							$exists_in_ua_owner = $user_addresses->where('devco', 1)->where('address_id', $user->organization_details->address->id)->where('owner_default', 1)->first();
							@endphp
							<div class="uk-grid-collapse" uk-grid>
								<div class="uk-width-1-4 uk-padding-remove">

									{{-- Manager --}}
									<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultAddress({{ $user->organization_details->address->id }}, {{ $user->id }}, 1)" name="address" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ADDRESS FOR REPORT" aria-expanded="false" {{ (($exists_in_ua) || (!$default_addr && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}> |
									{{-- owner --}}
									<input class="uk-radio" style="margin-top: .1px" onchange="makeDefaultOwnerAddress({{ $user->organization_details->address->id }}, {{ $user->id }}, 1)" name="owner_address" type="radio" uk-tooltip="" title="MAKE THIS AS DEFAULT OWNER ADDRESS" aria-expanded="false" {{ (($exists_in_ua_owner) || (!$default_owner_addr && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
								</div>
								<div class="uk-width-3-4">
									<small>
										<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$user->organization_details->address->formatted_address())) }}" uk-tooltip="" title="VIEW ON MAP" class="uk-text-emphasis"><i style="font-size: 14px;" class="a-marker-basic uk-link"></i></a> {!! $user->organization_details->address->formatted_address() !!}
									</small>
								</div>
							</div>
							<hr class="dashed-hr  uk-margin-small-bottom">
							@else
							<div class="uk-text-muted uk-align-left">NA</div>
							<br><hr class="dashed-hr  uk-margin-small-bottom">
							@endif
							@php
							$user_addresses = $user_addresses->where('devco', '!=', 1);
							@endphp
							@foreach($user_addresses as $address)
							<div class="uk-grid-collapse" uk-grid>
								<div class="uk-width-1-3 uk-padding-remove">
									<a target="_blank" href="https://www.google.com/maps?q={{ urlencode(str_replace('<br />',' ',$address->address->formatted_address())) }}" class="uk-text-emphasis" uk-tooltip="" title="VIEW ON MAP"><i style="font-size: 14px;" class="a-marker-basic uk-link"></i></a>
									{{-- Manager --}}
									<input class="uk-radio"  style="margin-top: .1px" onchange="makeDefaultAddress({{ $address->id }}, {{ $user->id }})" name="address" id="address-{{ $address->address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT ADDRESS FOR REPORT" aria-expanded="false" {{ $address->default ? 'checked=checked': '' }}> |
									{{-- Owner --}}
									<input class="uk-radio"  style="margin-top: .1px" onchange="makeDefaultOwnerAddress({{ $address->id }}, {{ $user->id }})" name="owner_address" id="owner-address-{{ $address->address_id }}" type="radio" uk-tooltip="" title="MAKE THIS AS DEFAULT OWNER ADDRESS" aria-expanded="false" {{ $address->owner_default ? 'checked=checked': '' }}>
								</div>
								<div class="uk-width-2-3">
									<small>
										{!! $address->address->formatted_address() !!}
										<i onclick="editAddress({{ $address->id }})" id="project-address-{{ $address->id }}" class="a-pencil" uk-tooltip="" title="{{ is_null($address->address->address_key) ? 'EDIT ADDRESS / REMOVE ADDRESS' : 'REMOVE ADDRESS' }}" aria-expanded="false"></i>
									</small>
								</div>
							</div>
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
							{{-- This was from code in user tab, show org phone as default phone, chec if exists - DEVCO --}}
							@if($user->organization_details && $user->organization_details->phone_number_formatted() != '')
							@php
								// Check if this user phone number exists in user phone number
							$exists_in_up = $user_phones->where('devco', 1)->where('phone_number_id', $user->organization_details->default_phone_number_id)->where('default', 1)->first();
							$exists_in_up_owner = $user_phones->where('devco', 1)->where('phone_number_id', $user->organization_details->default_phone_number_id)->where('owner_default', 1)->first();
							// dd($exists_in_up_owner);
							@endphp
							{{-- Ie none of the phone number is marked as default, we mark default devco contact phone number as default --}}
							{{-- Manager --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultPhonenumber({{ $user->organization_details->default_phone_number_id }}, {{ $user->id }},  1)" name="phone_number" id="phone_number-{{ $user->organization_details->default_phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT PHONE NUMBER FOR REPORT" aria-expanded="false" {{ (($exists_in_up) || (!$default_phone && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}> |
							{{-- Owner --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultOwnerPhonenumber({{ $user->organization_details->default_phone_number_id }}, {{ $user->id }},  1)" name="owner_phone_number" id="owner-phone-number-{{ $user->organization_details->default_phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT OWNER PHONE NUMBER" aria-expanded="false" {{ (($exists_in_up_owner) || (!$default_owner_phone && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
							<small>
								{{ $user->organization_details->phone_number_formatted() }}
							</small>
							<hr class="dashed-hr  uk-margin-small-bottom">
							@else
							<div class="uk-text-muted uk-align-left">NA</div>
							<hr class="dashed-hr  uk-margin-small-bottom">
							@endif
							@php
								// Non devco phone numbers
							$user_phones = $user_phones->where('devco', '!=', 1);
							@endphp
							@foreach($user_phones as $phone)
							{{-- Manager --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultPhonenumber({{ $phone->id }}, {{ $user->id }})" name="phone_number" id="phone_number-{{ $phone->phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT PHONE NUMBER FOR REPORT" aria-expanded="false" {{ $phone->default ? 'checked=checked': '' }}> |
							{{-- Owner --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultOwnerPhonenumber({{ $phone->id }}, {{ $user->id }})" name="owner_phone_number" id="owner-phone-number-{{ $phone->phone_number_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT OWNER PHONE NUMBER" aria-expanded="false" {{ $phone->owner_default ? 'checked=checked': '' }}>
							<small>
								{{ $phone->phone_number_formatted() }}
							</small>
							<i onclick="editPhoneNumber({{ $phone->id }})" id="project-phone-number-{{ $phone->id }}" class="a-pencil" uk-tooltip="" title="EDIT / DELETE PHONE NUMBER" aria-expanded="false"></i>
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
							$exists_in_ue = $user_emails->where('devco', 1)->where('email_address_id', $user->person->default_email_address_id)->where('default', 1)->first();
							// dd($user->person->default_email_address_id);
							$exists_in_ue_owner = $user_emails->where('devco', 1)->where('email_address_id', $user->person->default_email_address_id)->where('owner_default', 1)->first();
							@endphp
							{{-- Manager --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultEmail({{ $user->person->default_email_address_id }}, {{ $user->id }},  1)" name="email" id="email-{{ $user->person->default_email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT EMAIL FOR REPORT" aria-expanded="false" {{ (($exists_in_ue) || (!$default_email && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}> |
							{{-- Owner --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultOwnerEmail({{ $user->person->default_email_address_id }}, {{ $user->id }},  1)" name="owner_email" id="owner-email-{{ $user->person->default_email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT OWNER EMAIL" aria-expanded="false" {{ (($exists_in_ue_owner) || (!$default_owner_email && $default_devco_user_id == $user->id)) ? 'checked=checked': '' }}>
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
							{{-- Manager --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultEmail({{ $email->id }}, {{ $user->id }})" name="email" id="email-{{ $email->email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT EMAIL FOR REPORT" aria-expanded="false" {{ $email->default ? 'checked=checked': '' }}> |
							{{-- Owner --}}
							<input style="margin-top: .1px" class="uk-radio" onchange="makeDefaultOwnerEmail({{ $email->id }}, {{ $user->id }})" name="owner_email" id="owner-email-{{ $email->email_address_id }}" type="radio" uk-tooltip="" title="MAKE THIS DEFAULT OWNER EMAIL" aria-expanded="false" {{ $email->owner_default ? 'checked=checked': '' }}>
							<small>
								{{ $email->email_address->email_address }}
							</small>
							<i onclick="editEmail({{ $email->id }})" id="project-email-{{ $email->id }}" class="a-pencil" uk-tooltip="" title="EDIT / DELETE EMAIL ADDRESS" aria-expanded="false"></i>
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
							<span><i class="a-file-gear_1" data-uk-tooltip title="{{ strtoupper($user_roles) }}"></i>  | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ in_array($user->id, $allita_user_ids) ? 'USER HAS ALLITA SPECIFIC ACCESS TO COMMUNICATIONS AND REPORTS (CLICK TO REMOVE)' : 'USER DOES NOT HAVE ALLITA SPECIFIC ACCESS TO COMMUNICATIONS AND REPORTS (CLICK TO ADD)' }}"> <i onclick="addAllitaAccess({{ $user->id }}, {{ in_array($user->id, $allita_user_ids) }})" class="{{ in_array($user->id, $allita_user_ids) ? 'a-mail-chart-up' : 'a-mail-chart-up uk-text-muted' }}" style="position: relative;top: -1px;"></i>  |
							</span>
							<span class="" data-uk-tooltip title="{{ $pm_access ? 'USER HAS ACCESS TO REPORTS VIA DEVCO (THIS IS NOT RELIABLE)' : 'USER HAS NO ACCESS TO REPORTS VIA DEVCO (THIS IS NOT RELIABLE)' }}"> <i class="{{ $pm_access ? 'a-file-approve' : 'a-file-fail' }}"></i>
							</span>
						</td>
						@else
						<td>
							<span><i class="uk-text-muted a-file-gear_1" data-uk-tooltip title="USER DOES NOT HAVE ACCESS TO THIS PROJECT VIA DEVCO"></i> | </span>
							<span class="use-hand-cursor" data-uk-tooltip title="{{ $pm_access ? 'USER HAS ALLITA SPECIFIC ACCESS TO COMMUNICATIONS AND REPORTS (CLICK TO REMOVE - DOING SO WILL REMOVE USER FROM PROJECT COMPLETELY)' : 'USER HAS NO ACCESS TO REPORTS NOR CAN THEY BE SENT COMMUNICATIONS' }}" onclick="editUser({{ $user->id }})"><i class="{{ $pm_access ? 'a-mail-chart-up' : 'a-mail-chart-up' }}"></i>
							</span>
						</td>
						@endif
					</tr>
					@endforeach
				</tbody>
			</table>
			{{-- <a name="userbottom"></a> --}}
			<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
			<h2>Project Contacts Without A User Login {{-- {{ $project->id }} --}}</h2>
			<table class="uk-table uk-table-striped uk-table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>NAME</th>
						<th>CONTACT ROLE</th>
						<th>ORGANIZATIONS</th>
						<th>PROJECTS</th>
						<th>CONTACT PHONE</th>
						<th>CONTACT EMAIL</th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>
					@forEach($contactsWithoutUsers as $contact)
					<tr>
						<td>{{ $contact->person->id }}</td>
						<td> {{ $contact->person->first_name }} {{ $contact->person->last_name }}</td>
						<td>{{ $contact->projectRole->role_name }}</td>
						<td>
							@if($contact->organization)
							{{ $contact->organization->organization_name }}

							@if(count($contact->person->organizations))
							<hr class="dashed-hr uk-margin-bottom" >
							Other Organizations This Contact is Associated With:
							<ul>
								@forEach($contact->person->organizations as $org)
								@if($org->id !== $contact->organization_id)
								<li>{{ $org->organization_name }}</li>
								@endIf
								@endForEach
							</ul>
							@endIf
							@elseif(count($contact->person->organizations))
							No Organization is Assigned to This Role

							<hr class="dashed-hr uk-margin-bottom" >
							Other Organizations This Contact is Associated With:
							@forEach($contact->person->organizations as $org)
							{{ $org->organization_name }}
							@if(null !== $org->address)
							{{ $org->address->line_1 }}
							@if($org->address->line_2)
							<br />{{ $org->address->line_2 }}
							@endIf
							<br />@if($org->address->city)
							{{ $org->address->city }},
							@endIf {{ $org->address->state }} {{ $org->address->zip }}

							@endIf
							@endForEach
							@else
							<span uk-tooltip title="This person may be associated with an organization, however I can only see if they are a default contact for an organization.">NA</span>
							@endIf
						</td>
						<td>@if(count($contact->person->projects)>1)
							<span onclick="$('#contact-{{ $contact->id }}-projects').slideToggle();" class="use-hand-cursor"><i class="a-info-circle"></i>
								Total Projects: {{ count($contact->person->projects) }}
							</span>
							<div id="contact-{{ $contact->id }}-projects" style="display: none;">
								<ul>
									@forEach($contact->person->projects as $p)
									<li @if($p->id == $project->id) style="font-weight:bold" @endIf>
										{{ $p->project_number }} : {{ $p->project_name }}
									</li>
									@php
									array_push($projectIds, $p->id);
									@endphp
									@endForEach
								</ul>
							</div>
							@endIf
						</td>
						<td>@if(null !== $contact->person->phone)({{ $contact->person->phone->area_code }}) {{ substr($contact->person->phone->phone_number,0,3) }}-{{ substr($contact->person->phone->phone_number,2,4) }} @else NA @endIf</td>
						<td>
							@if(null !== $contact->person->email)
							<a href="mailto:{{ $contact->person->email->email_address }}" target="_blank">{{ $contact->person->email->email_address }}</a> @if(null !== $contact->person->matchingUserByEmail) <span class="uk-warning attention" uk-title="User {{ $contact->person->matchingUserByEmail->name }} is using this email address.">!!!</span>@endIf @else NA @endIf
						</td>
						<td><span class="use-hand-cursor">ACTION</span>
							<div uk-dropdown="mode:click">
								<ul class="uk-nav uk-dropdown-nav">
									<li ><a onclick="dynamicModalLoad('createuser_for_contact?contact={{ $contact->id }}&on_project={{ $project->id }}&project={{ $project->id }}&multiple=0');">Create User & Add to This Project</a></li>
									@if(count($contact->person->projects)>1)
									<li><a onclick="dynamicModalLoad('createuser_for_contact?contact={{ $contact->id }}&on_project={{ json_encode($projectIds) }}&project={{ $project->id }}&multiple=1');">Create User & Add to All Their Projects</a></li>
									@endif
									<li ><a onclick="removePersonFromThisProject({{ $contact->id }}, {{ $project->id }})">Remove Person From This Project</a></li>
									@if(count($contact->person->projects)>1)
									<li><a onclick="removePersonFromThisProject({{ $contact->id }}, {{ json_encode($projectIds) }}, 1)">Remove Person From All Their Projects</a></li>
									@endif
									<li><a onclick="dynamicModalLoad('{{ $contact->id }}/{{ $project->id }}/combine-contact-with-user/0');">Combine this Contact with a Project User<br />(Using This Information)</a></li>
									<li><a onclick="dynamicModalLoad('{{ $contact->id }}/{{ $project->id }}/combine-contact-with-user/1');">Combine this Contact with a Project User<br /> (Using Project User's Information)</a></li>
									@if(null !== $contact->person->matchingUserByEmail)
									<li ><a href="#">Combine this Contact With Conflicting User (using user {{ $contact->person->matchingUserByEmail->name }}'s' information)</a></li>
									<li ><a href="#">Combine this Contact With Conflicting User (using contact {{ $contact->person->first_name }} {{ $contact->person->last_name }}'s information)</a></li>
									@endIf
								</ul>
							</div>
						</td>
					</tr>
					@endForEach
				</tbody>
			</table>
		</div>
		{{-- Project User Person Ids: {{ print_r($projectUserPersonIds) }} --}}
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

	function makeDefaultOwnerOrganization(orgId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-owner-organization") }}",
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
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Owner Organization', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function makeDefaultOwnerAddress(addressId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-owner-address") }}",
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
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Owner Address', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function makeDefaultOwnerPhonenumber(phonenumberId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-owner-phone") }}",
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
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Owner Phone Number', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function makeDefaultOwnerEmail(emailId, userId, devco = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.make-project-default-owner-email") }}",
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
					UIkit.notification('<span uk-icon="icon: check"></span> Marked as Default Owner Email Address', {pos:'top-right', timeout:1000, status:'success'});
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function removePersonFromThisProject(contactId, projectId, multiple = 0) {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var data = { };
		jQuery.ajax({
			url: "{{ URL::route("user.remove-contact-from-this-project") }}",
			method: 'post',
			data: {
				person_id : contactId,
				project_id : projectId,
				multiple: multiple,
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					if(multiple == 1)
						UIkit.notification('<span uk-icon="icon: check"></span> Person removed from all his projects', {pos:'top-right', timeout:1000, status:'success'});
					else
						UIkit.notification('<span uk-icon="icon: check"></span> Person removed from this project', {pos:'top-right', timeout:1000, status:'success'});
					loadProjectContacts();
	    		// loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
	    	}
	    	jQuery.each(data.errors, function(key, value){
	    		jQuery('.alert-danger').show();
	    		jQuery('.alert-danger').append('<p>'+value+'</p>');
	    	});
	    }
	  });
	}

	function loadProjectContacts() {
		loadTab('/project/'+{{ $project->id }}+'/contacts/', '7', 0, 0, 'project-', 1);
	}








</script>
