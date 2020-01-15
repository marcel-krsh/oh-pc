<div class="modal-user-preferences">
	<div class="">
		<div uk-grid>
			<div class="user-preference-col-1  uk-padding-remove uk-margin-small-top">
				<div uk-grid>
					<div class="uk-width-1-1 uk-padding-remove-left" id="user-info">
						<h3><span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{ $data['summary']['name'] }};" class="user-badge-{{ $data['summary']['color'] }} user-badge-v2 uk-align-center user-badge use-hand-cursor">
							{{ $data['summary']['initials'] }}
						</span> {{ $data['summary']['name'] }} <i class="a-edit use-hand-cursor" uk-tooltip title="EDIT MY INFORMATION"  onclick="editUserInformationForm();"></i> <br /><small>{{ $data['summary']['email'] }} | {{ $data['summary']['phone'] }}</small>
						@if($current_user->id == $data['summary']['id'])
						<a href="javascript:logout()" class="uk-button uk-button-small uk-padding-small-top uk-align-right"><i class="a-circle-keyhole"></i> LOGOUT</a></h3>
						<form id="logout-form" action="/logout" method="POST" style="display: none;" siq_id="autopick_1705">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
						</form>
						@if($auditor_access)
						<a href="javascript:launchMobile()" class="uk-button uk-button-small uk-padding-small-top uk-align-left uk-visible@m" style="margin-left: 38px; padding-top: 3px;"><i class="a-mobile-home"></i> SEND AUTO-LOGIN TO YOUR PHONE</a>
						<script>
							function launchMobile(){
								var number = prompt("Please enter your mobile area code and phone number (no dashes or spaces please)");
								$.post("/mobile/request_auto_login", {
									'number' : number,
									'_token' : '{{ csrf_token() }}'
								}, function(data) {
									if(data!=1){
										UIkit.modal.alert(data+'<br /><br />NOTE: Do not dismiss this modal until you have input the code above on your phone.',{stack: true});
									} else {
										UIkit.notification('<span uk-icon="icon: check"></span> Unable to Send Link.', {pos:'top-right', timeout:1000, status:'warning'});
									}
								} );
							}
						</script>
						@endif
						@endif
					</div>

					<form id="edit-user-info" method="post" class="uk-width-1-1 uk-margin-bottom" style="display:none;">
						<h3>Edit My Info</h3>
						<div class="alert alert-danger uk-text-danger" style="display:none"></div>
						<div class="uk-grid-small uk-grid" uk-grid="">
							<div class="uk-width-1-2">
								<input id="first_name" name="first_name" type="text" class="uk-input" value="{{ $data['summary']['first_name'] }}" placeholder="Enter first name*">
							</div>
							<div class="uk-width-1-2 uk-margin-bottom">
								<input id="last_name" name="last_name" type="text" class="uk-input" value="{{ $data['summary']['last_name'] }}" placeholder="Enter last name*">
							</div>
							<div class="uk-width-1-2 uk-margin-bottom">
								<input id="email" name="email" type="text" class="uk-input" value="{{ $data['summary']['email'] }}" placeholder="Email*">
							</div>
							<div class="uk-width-1-2">
								<input id="business_phone_number" name="business_phone_number" type="text" class="uk-input" value="{{ $data['summary']['phone'] }}" placeholder="Phone, Format: xxx-xxx-xxxx">
							</div>
							<div class="uk-width-1-2 uk-margin-bottom">
								<select name="badge_color" class="uk-width-1-1 uk-select">
									<option value="blue">Select Badge</option>
									<option {{ $data['summary']['color'] == 'blue' ? 'selected=selected' : '' }} value="blue">Blue</option>
									<option {{ $data['summary']['color'] == 'green' ? 'selected=selected' : '' }} value="green">Green</option>
									<option {{ $data['summary']['color'] == 'orange' ? 'selected=selected' : '' }} value="orange">Orange</option>
									<option {{ $data['summary']['color'] == 'pink' ? 'selected=selected' : '' }} value="pink">Pink</option>
									<option {{ $data['summary']['color'] == 'sky' ? 'selected=selected' : '' }} value="sky">Sky</option>
									<option {{ $data['summary']['color'] == 'red' ? 'selected=selected' : '' }} value="red">Red</option>
									<option {{ $data['summary']['color'] == 'purple' ? 'selected=selected' : '' }} value="purple">Purple</option>
									<option {{ $data['summary']['color'] == 'yellow' ? 'selected=selected' : '' }} value="yellow">Yellow</option>
								</select>
							</div>
							<div class="uk-width-1-1 uk-padding-remove-bottom">
								<label class="uk-text-small">Leave blank if you don't want to change the password</label><br>
							</div>
							<div class="uk-width-1-2 uk-margin-remove-top">
								<input id="password" name="password" type="password" class="uk-input" value="" placeholder="Enter password">
							</div>
							<div class="uk-width-1-2 uk-margin-remove-top">
								<input id="password_confirmation" name="password_confirmation" type="password" class="uk-input" value="" placeholder="Confirm password">
							</div>
						</div>
						<div class="uk-grid" uk-grid="">
							<div class="uk-width-1-4 uk-padding-remove-left">
								<a class="uk-button uk-button-default uk-width-1-1" onclick="editUserInformationForm()"><span uk-icon="times-circle" class="uk-icon"></span> CANCEL</a>
							</div>
							<div class="uk-width-1-4 ">
								<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitUserInfoForm()"><span uk-icon="save" class="uk-icon"></span> SAVE</a>
							</div>
						</div>
					</form>


					@if($auditor_access)
					@if($current_user->id == $data['summary']['id'])
					<div class="uk-width-1-1 uk-margin-small-top uk-padding-remove-left">
						<hr />
						<h3 class="uk-margin-small-top uk-visible@m">Set Availability <i class="a-calendar-pencil use-hand-cursor" style="vertical-align: middle; padding-left: 10px;" onclick="expandModal(this);"></i></h3>@can('access_root')
						<div class="uk-grid-small uk-margin-remove" uk-grid>
							<div class="uk-width-1-3 uk-padding-remove">
								<label class="uk-text-small">Max Hours per Day</label>

								<select class="uk-select" id="max_hours" name="max_hours" onchange="autosave(this, 'auditor.availability_max_hours')">
									<option value="01:00:00" @if($data['summary']['availability_max_hours'] == '01:00:00') selected @endif>1:00 (H:MM)</option>
									<option value="01:15:00" @if($data['summary']['availability_max_hours'] == '01:15:00') selected @endif>1:15  (H:MM)</option>
									<option value="01:30:00" @if($data['summary']['availability_max_hours'] == '01:30:00') selected @endif>1:30 (H:MM)</option>
									<option value="01:45:00" @if($data['summary']['availability_max_hours'] == '01:45:00') selected @endif>1:45 (H:MM)</option>
									<option value="02:00:00" @if($data['summary']['availability_max_hours'] == '02:00:00') selected @endif>2:00 (H:MM)</option>
									<option value="02:15:00" @if($data['summary']['availability_max_hours'] == '02:15:00') selected @endif>2:15 (H:MM)</option>
									<option value="02:30:00" @if($data['summary']['availability_max_hours'] == '02:30:00') selected @endif>2:30 (H:MM)</option>
									<option value="02:45:00" @if($data['summary']['availability_max_hours'] == '02:45:00') selected @endif>2:45 (H:MM)</option>
									<option value="03:00:00" @if($data['summary']['availability_max_hours'] == '03:00:00') selected @endif>3:00 (H:MM)</option>
									<option value="03:15:00" @if($data['summary']['availability_max_hours'] == '03:15:00') selected @endif>3:15 (H:MM)</option>
									<option value="03:30:00" @if($data['summary']['availability_max_hours'] == '03:30:00') selected @endif>3:30 (H:MM)</option>
									<option value="03:45:00" @if($data['summary']['availability_max_hours'] == '03:45:00') selected @endif>3:45 (H:MM)</option>
									<option value="04:00:00" @if($data['summary']['availability_max_hours'] == '04:00:00') selected @endif>4:00 (H:MM)</option>
									<option value="04:15:00" @if($data['summary']['availability_max_hours'] == '04:15:00') selected @endif>4:15 (H:MM)</option>
									<option value="04:30:00" @if($data['summary']['availability_max_hours'] == '04:30:00') selected @endif>4:30 (H:MM)</option>
									<option value="04:45:00" @if($data['summary']['availability_max_hours'] == '04:45:00') selected @endif>4:45 (H:MM)</option>
									<option value="05:00:00" @if($data['summary']['availability_max_hours'] == '05:00:00') selected @endif>5:00 (H:MM)</option>
									<option value="05:15:00" @if($data['summary']['availability_max_hours'] == '05:15:00') selected @endif>5:15 (H:MM)</option>
									<option value="05:30:00" @if($data['summary']['availability_max_hours'] == '05:30:00') selected @endif>5:30 (H:MM)</option>
									<option value="05:45:00" @if($data['summary']['availability_max_hours'] == '05:45:00') selected @endif>5:45 (H:MM)</option>
									<option value="06:00:00" @if($data['summary']['availability_max_hours'] == '06:00:00') selected @endif>6:00 (H:MM)</option>
									<option value="06:15:00" @if($data['summary']['availability_max_hours'] == '06:15:00') selected @endif>6:15 (H:MM)</option>
									<option value="06:30:00" @if($data['summary']['availability_max_hours'] == '06:30:00') selected @endif>6:30 (H:MM)</option>
									<option value="06:45:00" @if($data['summary']['availability_max_hours'] == '06:45:00') selected @endif>6:45 (H:MM)</option>
									<option value="07:00:00" @if($data['summary']['availability_max_hours'] == '07:00:00') selected @endif>7:00 (H:MM)</option>
									<option value="07:15:00" @if($data['summary']['availability_max_hours'] == '07:15:00') selected @endif>7:15 (H:MM)</option>
									<option value="07:30:00" @if($data['summary']['availability_max_hours'] == '07:30:00') selected @endif>7:30 (H:MM)</option>
									<option value="07:45:00" @if($data['summary']['availability_max_hours'] == '07:45:00') selected @endif>7:45 (H:MM)</option>
									<option value="08:00:00" @if($data['summary']['availability_max_hours'] == '08:00:00') selected @endif>8:00 (H:MM)</option>
									<option value="08:15:00" @if($data['summary']['availability_max_hours'] == '08:15:00') selected @endif>8:15 (H:MM)</option>
									<option value="08:30:00" @if($data['summary']['availability_max_hours'] == '08:30:00') selected @endif>8:30 (H:MM)</option>
									<option value="08:45:00" @if($data['summary']['availability_max_hours'] == '08:45:00') selected @endif>8:45 (H:MM)</option>
									<option value="09:00:00" @if($data['summary']['availability_max_hours'] == '09:00:00') selected @endif>9:00 (H:MM)</option>
									<option value="09:15:00" @if($data['summary']['availability_max_hours'] == '09:15:00') selected @endif>9:15 (H:MM)</option>
									<option value="09:30:00" @if($data['summary']['availability_max_hours'] == '09:30:00') selected @endif>9:30 (H:MM)</option>
									<option value="09:45:00" @if($data['summary']['availability_max_hours'] == '09:45:00') selected @endif>9:45 (H:MM)</option>
									<option value="10:00:00" @if($data['summary']['availability_max_hours'] == '10:00:00') selected @endif>10:00 (HH:MM)</option>
									<option value="10:15:00" @if($data['summary']['availability_max_hours'] == '10:15:00') selected @endif>10:15 (HH:MM)</option>
									<option value="10:30:00" @if($data['summary']['availability_max_hours'] == '10:30:00') selected @endif>10:30 (HH:MM)</option>
									<option value="10:45:00" @if($data['summary']['availability_max_hours'] == '10:45:00') selected @endif>10:45 (HH:MM)</option>
									<option value="11:00:00" @if($data['summary']['availability_max_hours'] == '11:00:00') selected @endif>11:00 (HH:MM)</option>
									<option value="11:15:00" @if($data['summary']['availability_max_hours'] == '11:15:00') selected @endif>11:15 (HH:MM)</option>
									<option value="11:30:00" @if($data['summary']['availability_max_hours'] == '11:30:00') selected @endif>11:30 (HH:MM)</option>
									<option value="11:45:00" @if($data['summary']['availability_max_hours'] == '11:45:00') selected @endif>11:45 (HH:MM)</option>
									<option value="12:00:00" @if($data['summary']['availability_max_hours'] == '12:00:00') selected @endif>12:00 (HH:MM)</option>
								</select>
							</div>
							<div class="uk-width-1-3">
								<label class="uk-text-small">Max Hours for Breaks per Day</label>
								<select class="uk-select" id="lunch" name="lunch" onchange="autosave(this, 'auditor.availability_lunch')">
									<option value="00:00:00" @if($data['summary']['availability_lunch'] == '00:00:00') selected @endif>0:00</option>
									<option value="00:15:00" @if($data['summary']['availability_lunch'] == '00:15:00') selected @endif>0:15</option>
									<option value="00:30:00" @if($data['summary']['availability_lunch'] == '00:30:00') selected @endif>0:30</option>
									<option value="00:45:00" @if($data['summary']['availability_lunch'] == '00:45:00') selected @endif>0:45</option>
									<option value="01:00:00" @if($data['summary']['availability_lunch'] == '01:00:00') selected @endif>1:00</option>
									<option value="01:15:00" @if($data['summary']['availability_lunch'] == '01:15:00') selected @endif>1:15</option>
									<option value="01:30:00" @if($data['summary']['availability_lunch'] == '01:30:00') selected @endif>1:30</option>
								</select>
							</div>
							<div class="uk-width-1-3">
								<label class="uk-text-small">Max Hours for Driving per Day</label>
								<select class="uk-select" id="max_driving" name="max_driving" onchange="autosave(this, 'auditor.availability_max_driving')">
									<option value="01:00:00" @if($data['summary']['availability_max_driving'] == '01:00:00') selected @endif>1:00</option>
									<option value="01:15:00" @if($data['summary']['availability_max_driving'] == '01:15:00') selected @endif>1:15</option>
									<option value="01:30:00" @if($data['summary']['availability_max_driving'] == '01:30:00') selected @endif>1:30</option>
									<option value="01:45:00" @if($data['summary']['availability_max_driving'] == '01:45:00') selected @endif>1:45</option>
									<option value="02:00:00" @if($data['summary']['availability_max_driving'] == '02:00:00') selected @endif>2:00</option>
									<option value="02:15:00" @if($data['summary']['availability_max_driving'] == '02:15:00') selected @endif>2:15</option>
									<option value="02:30:00" @if($data['summary']['availability_max_driving'] == '02:30:00') selected @endif>2:30</option>
									<option value="02:45:00" @if($data['summary']['availability_max_driving'] == '02:45:00') selected @endif>2:45</option>
									<option value="03:00:00" @if($data['summary']['availability_max_driving'] == '03:00:00') selected @endif>3:00</option>
									<option value="03:15:00" @if($data['summary']['availability_max_driving'] == '03:15:00') selected @endif>3:15</option>
									<option value="03:30:00" @if($data['summary']['availability_max_driving'] == '03:30:00') selected @endif>3:30</option>
									<option value="03:45:00" @if($data['summary']['availability_max_driving'] == '03:45:00') selected @endif>3:45</option>
									<option value="04:00:00" @if($data['summary']['availability_max_driving'] == '04:00:00') selected @endif>4:00</option>
									<option value="04:15:00" @if($data['summary']['availability_max_driving'] == '04:15:00') selected @endif>4:15</option>
									<option value="04:30:00" @if($data['summary']['availability_max_driving'] == '04:30:00') selected @endif>4:30</option>
									<option value="04:45:00" @if($data['summary']['availability_max_driving'] == '04:45:00') selected @endif>4:45</option>
									<option value="05:00:00" @if($data['summary']['availability_max_driving'] == '05:00:00') selected @endif>5:00</option>
									<option value="05:15:00" @if($data['summary']['availability_max_driving'] == '05:15:00') selected @endif>5:15</option>
									<option value="05:30:00" @if($data['summary']['availability_max_driving'] == '05:30:00') selected @endif>5:30</option>
									<option value="05:45:00" @if($data['summary']['availability_max_driving'] == '05:45:00') selected @endif>5:45</option>
									<option value="06:00:00" @if($data['summary']['availability_max_driving'] == '06:00:00') selected @endif>6:00</option>
									<option value="06:15:00" @if($data['summary']['availability_max_driving'] == '06:15:00') selected @endif>6:15</option>
									<option value="06:30:00" @if($data['summary']['availability_max_driving'] == '06:30:00') selected @endif>6:30</option>
									<option value="06:45:00" @if($data['summary']['availability_max_driving'] == '06:45:00') selected @endif>6:45</option>
									<option value="07:00:00" @if($data['summary']['availability_max_driving'] == '07:00:00') selected @endif>7:00</option>
									<option value="07:15:00" @if($data['summary']['availability_max_driving'] == '07:15:00') selected @endif>7:15</option>
									<option value="07:30:00" @if($data['summary']['availability_max_driving'] == '07:30:00') selected @endif>7:30</option>
									<option value="07:45:00" @if($data['summary']['availability_max_driving'] == '07:45:00') selected @endif>7:45</option>
									<option value="08:00:00" @if($data['summary']['availability_max_driving'] == '08:00:00') selected @endif>8:00</option>
									<option value="08:15:00" @if($data['summary']['availability_max_driving'] == '08:15:00') selected @endif>8:15</option>
									<option value="08:30:00" @if($data['summary']['availability_max_driving'] == '08:30:00') selected @endif>8:30</option>
									<option value="08:45:00" @if($data['summary']['availability_max_driving'] == '08:45:00') selected @endif>8:45</option>
									<option value="09:00:00" @if($data['summary']['availability_max_driving'] == '09:00:00') selected @endif>9:00</option>
									<option value="09:15:00" @if($data['summary']['availability_max_driving'] == '09:15:00') selected @endif>9:15</option>
									<option value="09:30:00" @if($data['summary']['availability_max_driving'] == '09:30:00') selected @endif>9:30</option>
									<option value="09:45:00" @if($data['summary']['availability_max_driving'] == '09:45:00') selected @endif>9:45</option>
									<option value="10:00:00" @if($data['summary']['availability_max_driving'] == '10:00:00') selected @endif>10:00</option>
									<option value="10:15:00" @if($data['summary']['availability_max_driving'] == '10:15:00') selected @endif>10:15</option>
									<option value="10:30:00" @if($data['summary']['availability_max_driving'] == '10:30:00') selected @endif>10:30</option>
									<option value="10:45:00" @if($data['summary']['availability_max_driving'] == '10:45:00') selected @endif>10:45</option>
									<option value="11:00:00" @if($data['summary']['availability_max_driving'] == '11:00:00') selected @endif>11:00</option>
									<option value="11:15:00" @if($data['summary']['availability_max_driving'] == '11:15:00') selected @endif>11:15</option>
									<option value="11:30:00" @if($data['summary']['availability_max_driving'] == '11:30:00') selected @endif>11:30</option>
									<option value="11:45:00" @if($data['summary']['availability_max_driving'] == '11:45:00') selected @endif>11:45</option>
									<option value="12:00:00" @if($data['summary']['availability_max_driving'] == '12:00:00') selected @endif>12:00</option>
								</select>
							</div>
						</div>
						@endif
						@endif
					</div>
					@endcan

					<div class="uk-width-1-1 uk-margin-small-top uk-padding-remove-left">
						<form class="uk-form">
							<hr />
							<h3 class="uk-margin-small-top">Notification Preference<i class="a-bell-snooze uk-text-danger" style="vertical-align: middle; padding-left: 2px;"></i></h3>
							<div class="uk-grid-small uk-margin-remove" uk-grid>
								<div class="uk-width-1-3 uk-padding-remove">
									<label class="uk-text-small">How often would you like to receive notifications? <span class="uk-text-danger uk-text-bold">*</span></label>
									<select class="uk-select" id="notification_setting" name="notification_setting">
										<option value="1" {{ $unp ? ($unp->frequency == 1 ? 'selected=selected': '') : '' }}>Immediately</option>
										<option value="2" {{ $unp ? ($unp->frequency == 2 ? 'selected=selected': '') : 'selected=selected' }}>Hourly</option>
										<option value="3" {{ $unp ? ($unp->frequency == 3 ? 'selected=selected': '') : '' }}>Daily</option>
									</select>
								</div>
								<div class="uk-width-1-3  {{ $unp ? ($unp->frequency != 3 ? 'uk-hidden': '') : 'uk-hidden' }}" id="delivery_time_select">
									<label class="uk-text-small">Choose Deliver Time</label>
									<select class="uk-select" id="delivery_time" name="delivery_time">
										<option value="">Select Time</option>
										@for($i = 0; $i < 24; $i++)
										<option value="{{ $i }}:00:00" {{ ($unp && $unp->deliver_time)? (explode(":", $unp->deliver_time)[0] == $i ? 'selected=selected': '') : '' }}> {{ $i < 10 ? '0' : '' }}{{ ($i > 12 && ($i -12) < 10) ? '0' : '' }}{{ $i < 13 ? $i : $i - 12 }}:00 {{ $i < 12 ? 'AM' : 'PM' }}</option>
										@endfor
									</select>
								</div>
								<a onclick="submitNotificationPreference()" class="uk-button save uk-margin-top uk-margin-left">Save</a>
							</div>
						</form>
					</div>
					@if($auditor_access)
					<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left">
						<hr />
						<h3 class="uk-margin-small-top">Addresses @if($current_user->id == $data['summary']['id'])<i class="a-circle-plus use-hand-cursor" style="vertical-align: middle; padding-left: 10px;" onclick="auditorAddAddress();"></i>@endIf</h3>
						<div class="uk-grid-small uk-margin-remove" uk-grid>
							@if($current_user->id == $data['summary']['id']) <form id="auditor-add-address" method="post" class="uk-width-1-1 uk-margin-bottom" style="display:none;">
								<div class="uk-grid-small" uk-grid>
									<div class="uk-width-1-1 uk-padding-remove">
										<label class="uk-text-small">Add a new address below</label>
										<input id="address1" name="address1" type="text" class="uk-input" value=""  placeholder="Address line 1"/>
									</div>
									<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">
										<input id="address2" name="address2" type="text" class="uk-input" value=""  placeholder="Address line 2"/>
									</div>
									<div class="uk-width-1-3 uk-padding-remove-left uk-margin-small-top">
										<input id="city" name="city" type="text" class="uk-input" value=""  placeholder="City"/>
									</div>
									<div class="uk-width-1-6 uk-margin-small-top">
										<input id="state" name="state" type="text" class="uk-input" value=""  placeholder="State"/>
									</div>
									<div class="uk-width-1-3 uk-margin-small-top">
										<input id="zip" name="zip" type="text" class="uk-input" value=""  placeholder="Zip"/>
									</div>
									<div class="uk-width-1-6 uk-margin-small-top">
										<button class="uk-button uk-button-primary" style="height: 100%; width: 100%;" onclick="submitAuditorAddAddress(event);">SAVE</button>
									</div>
								</div>
							</form>
							@endif
							@if($data['summary']['organization']['address1'])
							<div class="uk-width-1-1">
								<div class="address">
									<i class="a-mailbox"></i>
									{{ $data['summary']['organization']['name'] }}<br />{{ $data['summary']['organization']['address1'] }}, @if($data['summary']['organization']['address2']){{ $data['summary']['organization']['address2'] }}@endif
									@if($data['summary']['organization']['city']) {{ $data['summary']['organization']['city'] }}, {{ $data['summary']['organization']['state'] }} {{ $data['summary']['organization']['zip'] }}
									@endif
								</div>
							</div>
							@endif
							<div class="uk-width-1-1 uk-margin-remove-top" id="addresses_list">
								<address-row v-if="addresses" v-for="address, index in addresses" :key="address.id" :address="address" :index="index" v-on:address-remove="removeAddress"></address-row>
							</div>
						</div>
						<div class="uk-grid-small" style="margin-top:30px;" uk-grid>
							@if($current_user->id == $data['summary']['id'])
							<label>Default address</label>
							<select id="default_address" class="uk-select" style="margin-left: 10px;">
								<option value="{{ $data['summary']['organization']['id'] }}" @if($user->default_address_id == $data['summary']['organization']['id']) selected @endif>{{ $data['summary']['organization']['address1'] }}, @if($data['summary']['organization']['address2']){{ $data['summary']['organization']['address2'] }}@endif
								@if($data['summary']['organization']['city']) {{ $data['summary']['organization']['city'] }}, {{ $data['summary']['organization']['state'] }} {{ $data['summary']['organization']['zip'] }} @endif</option>
								@foreach($data['summary']['addresses'] as $address)
								<option value="{{ $address['address_id'] }}" @if($user->default_address_id == $address['address_id']) selected @endif>{{ $address['address'] }}</option>
								@endforeach
							</select>


							@endif
						</div>
					</div>
					@endif
				</div>
			</div>
			@if($auditor_access)
			<div class="user-preference-col-2 uk-padding-remove uk-margin-small-top" style="display:none">
				<div uk-grid>
					<div class="uk-width-1-1">
						<h3>Add Availability</h3>
						<form name="newavailabilityform" id="newavailabilityform" method="post">
							<div uk-grid>
								<div class="uk-width-1-2 uk-padding-remove">
									<label class="uk-form-label" for="availabilitydaterange">DATE RANGE</label>
									<div class="uk-form-controls">
										<input type="text" id="availabilitydaterange" name="availabilitydaterange" value="" class="uk-input flatpickr flatpickr-input active"/>
									</div>
								</div>
								<div class="uk-width-1-4">
									<label class="uk-form-label" for="starttime">START TIME</label>
									<div class="uk-form-controls">
										<select class="uk-select" id="starttime" name="starttime">
											<option value="06:00:00">6:00 AM</option>
											<option value="06:15:00">6:15 AM</option>
											<option value="06:30:00">6:30 AM</option>
											<option value="06:45:00">6:45 AM</option>
											<option value="07:00:00">7:00 AM</option>
											<option value="07:15:00">7:15 AM</option>
											<option value="07:30:00">7:30 AM</option>
											<option value="07:45:00">7:45 AM</option>
											<option value="08:00:00">8:00 AM</option>
											<option value="08:15:00">8:15 AM</option>
											<option value="08:30:00">8:30 AM</option>
											<option value="08:45:00">8:45 AM</option>
											<option value="09:00:00">9:00 AM</option>
											<option value="09:15:00">9:15 AM</option>
											<option value="09:30:00">9:30 AM</option>
											<option value="09:45:00">9:45 AM</option>
											<option value="10:00:00">10:00 AM</option>
											<option value="10:15:00">10:15 AM</option>
											<option value="10:30:00">10:30 AM</option>
											<option value="10:45:00">10:45 AM</option>
											<option value="11:00:00">11:00 AM</option>
											<option value="11:15:00">11:15 AM</option>
											<option value="11:30:00">11:30 AM</option>
											<option value="11:45:00">11:45 AM</option>
											<option value="12:00:00">12:00 PM</option>
											<option value="12:15:00">12:15 PM</option>
											<option value="12:30:00">12:30 PM</option>
											<option value="12:45:00">12:45 PM</option>
											<option value="13:00:00">1:00 PM</option>
											<option value="13:15:00">1:15 PM</option>
											<option value="13:30:00">1:30 PM</option>
											<option value="13:45:00">1:45 PM</option>
											<option value="14:00:00">2:00 PM</option>
											<option value="14:15:00">2:15 PM</option>
											<option value="14:30:00">2:30 PM</option>
											<option value="14:45:00">2:45 PM</option>
											<option value="15:00:00">3:00 PM</option>
											<option value="15:15:00">3:15 PM</option>
											<option value="15:30:00">3:30 PM</option>
											<option value="15:45:00">3:45 PM</option>
											<option value="16:00:00">4:00 PM</option>
											<option value="16:15:00">4:15 PM</option>
											<option value="16:30:00">4:30 PM</option>
											<option value="16:45:00">4:45 PM</option>
											<option value="17:00:00">5:00 PM</option>
											<option value="17:15:00">5:15 PM</option>
											<option value="17:30:00">5:30 PM</option>
											<option value="17:45:00">5:45 PM</option>
											<option value="18:00:00">6:00 PM</option>
											<option value="18:15:00">6:15 PM</option>
											<option value="18:30:00">6:30 PM</option>
											<option value="18:45:00">6:45 PM</option>
											<option value="19:00:00">7:00 PM</option>
											<option value="19:15:00">7:15 PM</option>
											<option value="19:30:00">7:30 PM</option>
											<option value="19:45:00">7:45 PM</option>
											<option value="20:00:00">8:00 PM</option>
										</select>
									</div>
								</div>
								<div class="uk-width-1-4">
									<label class="uk-form-label" for="endtime">END TIME</label>
									<div class="uk-form-controls">
										<select class="uk-select" id="endtime" name="endtime">
											<option value="06:00:00">6:00 AM</option>
											<option value="06:15:00">6:15 AM</option>
											<option value="06:30:00">6:30 AM</option>
											<option value="06:45:00">6:45 AM</option>
											<option value="07:00:00">7:00 AM</option>
											<option value="07:15:00">7:15 AM</option>
											<option value="07:30:00">7:30 AM</option>
											<option value="07:45:00">7:45 AM</option>
											<option value="08:00:00">8:00 AM</option>
											<option value="08:15:00">8:15 AM</option>
											<option value="08:30:00">8:30 AM</option>
											<option value="08:45:00">8:45 AM</option>
											<option value="09:00:00">9:00 AM</option>
											<option value="09:15:00">9:15 AM</option>
											<option value="09:30:00">9:30 AM</option>
											<option value="09:45:00">9:45 AM</option>
											<option value="10:00:00">10:00 AM</option>
											<option value="10:15:00">10:15 AM</option>
											<option value="10:30:00">10:30 AM</option>
											<option value="10:45:00">10:45 AM</option>
											<option value="11:00:00">11:00 AM</option>
											<option value="11:15:00">11:15 AM</option>
											<option value="11:30:00">11:30 AM</option>
											<option value="11:45:00">11:45 AM</option>
											<option value="12:00:00">12:00 PM</option>
											<option value="12:15:00">12:15 PM</option>
											<option value="12:30:00">12:30 PM</option>
											<option value="12:45:00">12:45 PM</option>
											<option value="13:00:00">1:00 PM</option>
											<option value="13:15:00">1:15 PM</option>
											<option value="13:30:00">1:30 PM</option>
											<option value="13:45:00">1:45 PM</option>
											<option value="14:00:00">2:00 PM</option>
											<option value="14:15:00">2:15 PM</option>
											<option value="14:30:00">2:30 PM</option>
											<option value="14:45:00">2:45 PM</option>
											<option value="15:00:00">3:00 PM</option>
											<option value="15:15:00">3:15 PM</option>
											<option value="15:30:00">3:30 PM</option>
											<option value="15:45:00">3:45 PM</option>
											<option value="16:00:00">4:00 PM</option>
											<option value="16:15:00">4:15 PM</option>
											<option value="16:30:00">4:30 PM</option>
											<option value="16:45:00">4:45 PM</option>
											<option value="17:00:00">5:00 PM</option>
											<option value="17:15:00">5:15 PM</option>
											<option value="17:30:00">5:30 PM</option>
											<option value="17:45:00">5:45 PM</option>
											<option value="18:00:00">6:00 PM</option>
											<option value="18:15:00">6:15 PM</option>
											<option value="18:30:00">6:30 PM</option>
											<option value="18:45:00">6:45 PM</option>
											<option value="19:00:00">7:00 PM</option>
											<option value="19:15:00">7:15 PM</option>
											<option value="19:30:00">7:30 PM</option>
											<option value="19:45:00">7:45 PM</option>
											<option value="20:00:00">8:00 PM</option>
										</select>
									</div>
								</div>
								<div class="uk-width-1-2 uk-margin-top uk-padding-remove">
									<label class="uk-form-label" for="endtime">ON DAYS</label>
									<div class="uk-form-controls">
										<span class="uk-badge dayselector dayselector-monday use-hand-cursor" uk-tooltip title="MONDAYS" onclick="selectday(this,'monday');">M</span>
										<span  uk-tooltip title="TUESDAYS" class="uk-badge dayselector  dayselector-tuesday use-hand-cursor" onclick="selectday(this,'tuesday');">T</span>
										<span  uk-tooltip title="WEDNESDAYS" class="uk-badge dayselector  dayselector-wednesday use-hand-cursor" onclick="selectday(this,'wednesday');">W</span>
										<span  uk-tooltip title="THURSDAYS" class="uk-badge dayselector  dayselector-thursday use-hand-cursor" onclick="selectday(this,'thursday');">T</span>
										<span  uk-tooltip title="FRIDAYS" class="uk-badge dayselector  dayselector-friday use-hand-cursor" onclick="selectday(this,'friday');">F</span>
										<span  uk-tooltip title="SATURDAYS" class="uk-badge dayselector  dayselector-saturday outline use-hand-cursor" onclick="selectday(this,'saturday');">S</span>
										<span  uk-tooltip title="SUNDAYS" class="uk-badge dayselector  dayselector-sunday outline use-hand-cursor" onclick="selectday(this,'sunday');">S</span>
										<input class="uk-checkbox dayselectorcheckbox" name="monday" type="checkbox" checked hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="tuesday" type="checkbox" checked hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="wednesday" type="checkbox" checked hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="thursday" type="checkbox" checked hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="friday" type="checkbox" checked hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="saturday" type="checkbox" hidden>
										<input class="uk-checkbox dayselectorcheckbox" name="sunday" type="checkbox" hidden>
									</div>
								</div>
								<div class="uk-width-1-4 uk-margin-top">
									<div class="uk-form-controls">
										<span  uk-tooltip title="ADDS AVAILABILITY" class="uk-badge availselect use-hand-cursor uk-width-1-1" onclick="toggleAvailability('available');">AVAILABLE</span><span uk-tooltip title="REMOVES AVAILABILITY"  class="uk-badge availselect use-hand-cursor outline uk-width-1-1" onclick="toggleAvailability('notavailable');" style="margin-top:2px;">NOT AVAILABLE</span>
										<input name="availability" type="hidden" value="available">
									</div>
								</div>
								<div class="uk-width-1-4 uk-margin-small-top">
									<div class="uk-form-controls">
										<button class="uk-button uk-button-success" style="margin-top: 8px;height: 47px; width: 100%;" onclick="saveAvailability(event);">SAVE</button>
									</div>
								</div>
							</div>
						</form>
						<hr style="margin-top: 40px;"/>
					</div>
					<div class="uk-width-1-1 uk-margin-small-top uk-padding-remove">
						<div id="auditor-availability-calendar" class="uk-padding-remove uk-margin-top" >
							<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>
<style>
	.uk-badge.dayselector, .uk-badge.availselect {
		text-shadow: none;
	}

	.uk-badge.dayselector.outline,
	.uk-badge.availselect.outline{
		background-color: transparent;
		border-color: #005186;
		color: #005186;
	}

</style>
@if($auditor_access)
<script>
	$( document ).ready(function() {
		loadCalendar();
		fillSpacers();
		$('#business_phone_number').keyup();

		$( "#default_address" ).change(function() {
			var id = parseInt($(this).val(), 10);
			console.log("default address "+id);

			$.post("/auditors/{{ $data['summary']['id'] }}/addresses/"+id+"/default", {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!=1){
					UIkit.modal.alert(data,{stack: true});
				} else {
					UIkit.notification('<span uk-icon="icon: check"></span> Default Address Saved', {pos:'top-right', timeout:1000, status:'success'});
				}
			} );
		});
	});


	function phone_formatting(ele,restore) {
		var new_number,
		selection_start = ele.selectionStart,
		selection_end = ele.selectionEnd,
		number = ele.value.replace(/\D/g,'');
    	// automatically add dashes
    	if (number.length > 2) {
      // matches: 123 || 123-4 || 123-45
      new_number = number.substring(0,3) + '-';
      if (number.length === 4 || number.length === 5) {
        // matches: 123-4 || 123-45
        new_number += number.substr(3);
      }
      else if (number.length > 5) {
        // matches: 123-456 || 123-456-7 || 123-456-789
        new_number += number.substring(3,6) + '-';
      }
      if (number.length > 6) {
        // matches: 123-456-7 || 123-456-789 || 123-456-7890
        new_number += number.substring(6);
      }
    }
    else {
    	new_number = number;
    }

	    // if value is heigher than 12, last number is dropped
	    // if inserting a number before the last character, numbers
	    // are shifted right, only 12 characters will show
	    ele.value =  (new_number.length > 12) ? new_number.substring(0,12) : new_number;

	    // restore cursor selection,
	    // prevent it from going to the end
	    // UNLESS
	    // cursor was at the end AND a dash was added

	    if (new_number.slice(-1) === '-' && restore === false && (new_number.length === 8 && selection_end === 7) || (new_number.length === 4 && selection_end === 3)) {
	    	selection_start = new_number.length;
	    	selection_end = new_number.length;
	    }
	    else if (restore === 'revert') {
	    	selection_start--;
	    	selection_end--;
	    }
	    ele.setSelectionRange(selection_start, selection_end);
	  }

	  function business_phone_number_check(field,e) {
	  	var key_code = e.keyCode,
	  	key_string = String.fromCharCode(key_code),
	  	press_delete = false,
	  	dash_key = 189,
	  	delete_key = [8,46],
	  	direction_key = [33,34,35,36,37,38,39,40],
	  	selection_end = field.selectionEnd;

    // delete key was pressed
    if (delete_key.indexOf(key_code) > -1) {
    	press_delete = true;
    }

    // only force formatting is a number or delete key was pressed
    if (key_string.match(/^\d+$/) || press_delete) {
    	phone_formatting(field,press_delete);
    }
    // do nothing for direction keys, keep their default actions
    else if(direction_key.indexOf(key_code) > -1) {
      // do nothing
    }
    else if(dash_key === key_code) {
    	if (selection_end === field.value.length) {
    		field.value = field.value.slice(0,-1)
    	}
    	else {
    		field.value = field.value.substring(0,(selection_end - 1)) + field.value.substr(selection_end)
    		field.selectionEnd = selection_end - 1;
    	}
    }
    // all other non numerical key presses, remove their value
    else {
    	e.preventDefault();
      //    field.value = field.value.replace(/[^0-9\-]/g,'')
      phone_formatting(field,'revert');
    }
  }

  document.getElementById('business_phone_number').onkeyup = function(e) {
  	business_phone_number_check(this,e);
  }


  function loadCalendar(target=null) {
  	if(target == null){
  		var url = '/auditors/{{ $current_user->id }}/availability/loadcal/';
  		$.get(url, {}, function(data) {
  			if(data=='0'){
  				UIkit.modal.alert("There was a problem getting the calendar.");
  			} else {
  				$('#auditor-availability-calendar').html(data);
  				fillSpacers();
  			}
  		});
  	}else{
  		var url = '/auditors/{{ $current_user->id }}/availability/loadcal/'+target;
  		$('#auditor-availability-calendar').fadeOut("fast", function() {
  			$('#auditor-availability-calendar').html('<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>');
  			$('#auditor-availability-calendar').fadeIn("fast");
  			$.get(url, {}, function(data) {
  				if(data=='0'){
  					UIkit.modal.alert("There was a problem getting the calendar.");
  				} else {
  					loadCalendar();
  				}
  			});
  		});
  	}
  }

  function deleteAvailability(id){
  	UIkit.modal.confirm("Are you sure you want to delete this available time?", {center: true,  keyboard:false,  stack:true}).then(function() {

  		$.post("/auditors/{{ $current_user->id }}/availability/"+id+"/delete", {
  			'_token' : '{{ csrf_token() }}'
  		}, function(data) {
  			if(data!=1){
  				UIkit.modal.alert(data,{stack: true});
  			} else {
  				UIkit.notification('<span uk-icon="icon: check"></span> Availability Deleted', {pos:'top-right', timeout:1000, status:'success'});
  				loadCalendar();
  			}
  		} );

  	}, function () {
  		return false;
  	});
  }

  function fetchCalendar(element){

  	var target = $(element).attr('data-target');
	    // hide all


	    // check if the element is there first
	    if($('#auditor-calendar-'+target).length){
	    	$(element).closest('.grid-schedule-availability').fadeOut("fast", function() {
		    	// fade in new calendar
		    	$('#auditor-calendar-'+target).fadeIn("fast");
		    });

		    // next or previous dates are already loaded, load the next set
		    if($('#auditor-calendar-'+target).prev().length){

		    	console.log("there is another calendar available before");

		    }else{

		    	console.log("we need to load a calendar before");

		    	var url = '/auditors/{{ $current_user->id }}/availability/loadcal/'+target+'/before';
		    	$.get(url, {}, function(data) {
		    		if(data=='0'){
		    			UIkit.modal.alert("There was a problem getting the calendar.");
		    		} else {
		    			$('#auditor-calendar-'+target).before(data);
		    			fillSpacers();
		    		}
		    	});
		    }

		    if($('#auditor-calendar-'+target).next().length){
		    	console.log("there is another calendar available after");
		    }else{
		    	console.log("we need to load a calendar after");
		    	var url = '/auditors/{{ $current_user->id }}/availability/loadcal/'+target+'/after';
		    	$.get(url, {}, function(data) {
		    		if(data=='0'){
		    			UIkit.modal.alert("There was a problem getting the calendar.");
		    		} else {
		    			$('#auditor-calendar-'+target).after(data);
		    			fillSpacers();
		    		}
		    	});
		    }
		  }else{
		  	loadCalendar(target);
		  }

		}

		function selectday(element, day) {
			$(element).toggleClass("outline");
			if($("input[name='"+day+"']:checkbox").prop('checked')){
				$("input[name='"+day+"']:checkbox").prop('checked',false);
			}else{
				$("input[name='"+day+"']:checkbox").prop('checked',true);
			}

 		// at least one day should be selected
 		if(!$(".dayselectorcheckbox").is(':checked')){
 			selectday(".dayselector:first", 'monday');
 		}
 	}

 	function toggleAvailability(availability) {
 		$(".availselect").toggleClass("outline");
 		$("input[name='availability']:hidden").val(availability);
 	}

 	function expandModal(element) {
 		if($(element).closest('.uk-modal-body')[0].style.width != '70%') {
 			$('.user-preference-col-2').fadeOut("slow", function(){
 				$('.user-preference-col-1').animate({ width: "100%" }, 1000, function(){

 				});
 				$(element).closest('.uk-modal-body').animate({ width: "70%" });
 				$(element).closest('.uk-modal-body').toggleClass("modal-wide");
 			});

 		}else{
 			$(element).closest('.uk-modal-body').animate({ width: "90%" });
 			$(element).closest('.uk-modal-body').toggleClass("modal-wide");

 			$('.user-preference-col-1').animate({ width: "30%" }, 1000, function(){
 				$('.user-preference-col-2').css("width", "70%").fadeIn("slow");
 			});

	 		// $('.user-preference-col-1').switchClass('uk-width-1-1', 'uk-width-1-2', 500, 'swing', function(item){
	 		// 	$('.user-preference-col-1').toggleClass('uk-width-1-2');
	 		// });
	 	}
	 }

	 $('.uk-modal-body').animate({
	 	@if($current_user->id == $data['summary']['id']) width: "70%" @else width:"30%" @endIf
	 });

	 $(document).on('beforehide', '.uk-modal-body', function (item) {
	 	$(item).removeAttr('style');
	 });

	 function editUserInformationForm() {
	 	$('#edit-user-info').toggle();
	 	$('#user-info').toggle();
	 }

	 function auditorAddAddress(){
	 	$('#auditor-add-address').toggle();
	 }

	 function submitAuditorAddAddress(e){
	 	e.preventDefault();
	 	var form = $('#auditor-add-address');

	 	$.post("/auditors/{{ $data['summary']['id'] }}/addresses/create", {
	 		'inputs' : form.serialize(),
	 		'_token' : '{{ csrf_token() }}'
	 	}, function(data) {
	 		if(data!=1){
	 			UIkit.modal.alert(data,{stack: true});
	 		} else {
	 			UIkit.modal.alert('The address has been saved.',{stack: true});
	 			form.get(0).reset();
	 			$('#auditor-add-address').hide();
	 		}
	 	} );
	 }

	 function saveAvailability(e){
	 	e.preventDefault();
	 	var form = $('#newavailabilityform');
	 	// debugger;
		// check if date is not empty
		if($("#availabilitydaterange").val().length === 0) {
			$("#availabilitydaterange").addClass('uk-form-danger');
			return false;
		}else{
			$("#availabilitydaterange").removeClass('uk-form-danger');
		}

		// at least one day should be selected
		if(!$(".dayselectorcheckbox").is(':checked')){
			selectday(".dayselector:first", 'monday');
			return false;
		}

		// check if end time is later than start time
		var starttime = $("#starttime").val();
		var endtime = $("#endtime").val();

		if(starttime > endtime || starttime == endtime) {
			$("#endtime").addClass('uk-form-danger');
			return false;
		}else{
			$("#endtime").removeClass('uk-form-danger');
		}

		$.post("/auditors/{{ $data['summary']['id'] }}/availability/create", {
			'inputs' : form.serialize(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!=1){
				UIkit.modal.alert(data,{stack: true});
			} else {
				UIkit.notification('<span uk-icon="icon: check"></span> Availability Saved', {pos:'top-right', timeout:1000, status:'success'});
                //reload graph
                loadCalendar();
              }
            } );
	}

	function setDate(date, name){
		$('#availabilitydaterange').val(date);
		// also make sure the day of the week is selected
		if(!$("input[name='"+name+"']:checkbox").is(':checked')){
			selectday(".dayselector-"+name, name);
		}
	}

	function submitUserInfoForm() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});

		var form = $('#edit-user-info');

		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			url: "{{ url("modals/users/edit-my-info") }}",
			method: 'post',
			data: {
				first_name: data['first_name'],
				last_name: data['last_name'],
				email: data['email'],
				password: data['password'],
				password_confirmation: data['password_confirmation'],
				badge_color: data['badge_color'],
				business_phone_number: data['business_phone_number'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.notification('<span uk-icon="icon: check"></span> Your information has been saved', {pos:'top-right', timeout:1000, status:'success'});
	    		// UIkit.modal.alert('User has been saved.',{stack: true});
	    		// dynamicModalClose();
	    		// openUserPreferences();
	    		$('#apcsv-avatar').trigger('click');
	    	}
	    	jQuery.each(data.errors, function(key, value){
	    		jQuery('.alert-danger').show();
	    		jQuery('.alert-danger').append('<p>'+value+'</p>');
	    	});
	    }
	  });
	}



</script>
@endif

<script>

		//Form notification preference ,
		$('#notification_setting').change(function() {
			var value = $("#notification_setting").val();
			if(value == 3){
				$('#delivery_time_select').removeClass('uk-hidden');
				$('#delivery_time_select').addClass('uk-visible');
			} else {
				$('#delivery_time_select').removeClass('uk-visible');
				$('#delivery_time_select').addClass('uk-hidden');
			}
		});

		function submitNotificationPreference() {
			jQuery.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			});
			var data = [];
			data['notification_setting'] = $('#notification_setting').val();
			data['delivery_time'] = $('#delivery_time').val();
			jQuery.ajax({
				url: "{{ url('user/notification-preference', $user->id) }}",
				method: 'post',
				data: {
					notification_setting: data['notification_setting'],
					delivery_time: data['delivery_time'],
					'_token' : '{{ csrf_token() }}'
				},
				success: function(data){
					$('.alert-danger' ).empty();
					if(data == 1) {
						UIkit.modal.alert('Notification preference has been saved',{stack: true});
						return;
					}
					UIkit.modal.alert(data.errors[0],{stack: true});
					return;
				}
			});
		}
	</script>

	<script>

		new Vue({
			el: '#addresses_list',

			data: function() {
				return {
					addresses: {!! json_encode($data['summary']['addresses']) !!}
				}
			},
			methods: {
				@if($current_user->id == $data['summary']['id'])
				removeAddress: function(index) {
					this.$delete(this.addresses, index)
				}
				@else
				removeAddress: function(index) {
					UIkit.modal.alert('<h2>Sorry!</h2><p>You need to be logged in as this user to delete their addresses.</p>');
				}
				@endIf
			},

      //       created() {
		    //     Echo.channel('auditors.'+uid+'.'+sid)
		    //           .listen('AuditorAddressEvent', (e) => {
		    //             this.addresses.push({
		    //               address_id: e.address_id,
		    //               address: e.address
		    //             });
		    //         // console.log("receiving address");
		    //     });
		    // }
		  });

		</script>
		<script>
			flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;

			flatpickr("#availabilitydaterange", {
				mode: "range",
				minDate: "today",
				altFormat: "F j, Y",
				dateFormat: "F j, Y",
				"locale": {
		        "firstDayOfWeek": 1 // start week on Monday
		      }
		    });

		  </script>
		  <style>
		  	.user-preference-col-1{
		  		width:100%;
		  	}
		  	.modal-wide {}
		  	.modal-wide .user-preference-col-1 {
		  		/*width:50%;*/
		  	}
		  	.user-preference-col-2 label {
		  		display: inline-block;
		  		margin-right: 8px;
		  	}
		  	.user-preference-col-2 .uk-form-controls {
		  		margin-top: 6px;
		  	}
		  	.user-preference-col-2 > div {
		  		padding-left:20px;
		  	}
		  </style>
