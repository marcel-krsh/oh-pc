<script type="text/javascript">
  // Auditor(2) and above roles would have API Key
  // $('#role').change(function() {
  //   var value = $("#role").val();
  //   if(value >= 2){
  //     document.getElementById('api_token_block').style.display='block';
  //   } else {
  //     document.getElementById('api_token_block').style.display='none';
  //     $('#api_token').val('');
  //   }
  // });
</script>
@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<div id="dynamic-modal-content">
	@if(null == $contact)
	<h2 class="uk-text-uppercase uk-text-emphasis">Create New User</h2>
	@else
	<h2 class="uk-text-uppercase uk-text-emphasis">Create User For {{ $contact->first_name }} {{ $contact->last_name }}</h2>
	@endIf
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('admin.createuser') }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-2">
				<div class="uk-width-1-1">
					<label for="role">Role<span class="uk-text-danger uk-text-bold">*</span> : <br /></label>
					<select id="role" name="role" class="uk-width-1-1 uk-select">
						<option value="0">No Access</option>
						@foreach($roles as $role)
						<option value="{{ $role->id }}" @if($contact !== null && $role->id == 1) selected @endIf >{{ $role->role_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">First Name<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="first_name" placeholder="Enter First name" @if(null !== $contact) value="{{ $contact->first_name }}"@endIf>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Last Name<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="last_name" placeholder="Enter Last name" @if(null !== $contact) value="{{ $contact->last_name }}"@endIf>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Work Email<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-form-large uk-width-1-1" name="email" placeholder="Enter Email" @if(null !== $contact && $contact->email !== null) value="{{ $contact->email->email_address }}"@endIf>
				</div>
       {{--  <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Password<span class="uk-text-danger uk-text-bold">*</span> :</label>
          <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password" placeholder="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Confirm Password<span class="uk-text-danger uk-text-bold">*</span> :</label>
          <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password_confirmation" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');">
        </div> --}}
        <div class="uk-width-1-1 uk-margin-top">
        	<label for="name">Badge Color :</label>
        	<select name="badge_color" class="uk-width-1-1 uk-select">
        		<option value="blue">Select Badge</option>
        		<option value="blue" selected>Blue</option>
        		<option value="green" >Green</option>
        		<option value="orange" >Orange</option>
        		<option value="pink" >Pink</option>
        		<option value="sky" >Sky</option>
        		<option value="red" >Red</option>
        		<option value="purple" >Purple</option>
        		<option value="yellow" >Yellow</option>
        	</select>
        </div>
        <div class="uk-width-1-1 uk-margin-top">
        	<label for="name">Business Phone Number :</label><br>
        	<input id="business_phone_number" type="text" class="uk-input uk-width-1-3" name="business_phone_number" placeholder="Format: (xxx) xxx-xxxx" @if(null !== $contact && $contact->phone !== null) value="{{ $contact->phone->number }}"@endIf>
        	<input id="phone_extension" type="number" class="uk-input uk-width-1-3" name="phone_extension" placeholder="xxxx">
        </div>
      </div>
      <div class="uk-width-1-2">
      	<div class="uk-width-1-1">
      		<label for="role">Organization : <br /></label>
      		<select name="organization" class="uk-width-1-1 uk-select">
      			<option value="">Select Organization</option>
      			@foreach($organizations as $organization)
      			@if(null != $contact)
      			<option @if(null != $contact) {{ $org && $org->id == $organization->id ? 'selected=selected': '' }} @endif value="{{ $organization->id }}" >{{ $organization->organization_name }}</option>
      			@else
      			<option value="{{ $organization->id }}" >{{ $organization->organization_name }}</option>
      			@endif
      			@endforeach
      		</select>
      	</div>
      	@if(null != $contact && $org && $org->address)
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 1 :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="address_line_1" placeholder="Enter Address Line 1" value="{{ $org->address->line_1 }}">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 2 :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="address_line_2" placeholder="Enter Address Line 2" value="{{ $org->address->line_2 }}">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">City :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="city" placeholder="Enter City" value="{{ $org->address->city }}">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="role">State : <br /></label>
      		<select name="state_id" class="uk-width-1-1 uk-select">
      			<option value="">Select State</option>
      			@foreach($states as $state)
      			<option {{ $org->address->state_id == $state->id ? 'selected=selected' : '' }} value="{{ $state->id }}" >{{ $state->state_name }}</option>
      			@endforeach
      		</select>
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Zip :</label> <br>
      		<input type="number" class="uk-input uk-width-1-3" name="zip" placeholder="xxxxx" value="{{ $org->address->zip }}">
      		<input id="zip_4" type="number" class="uk-input uk-width-1-3" name="zip_4" placeholder="xxxx" value="{{ $org->address->zip_4 }}">
      	</div>
      	@else
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 1 :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="address_line_1" placeholder="Enter Address Line 1">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 2 :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="address_line_2" placeholder="Enter Address Line 2">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">City :</label>
      		<input type="text" class="uk-input uk-width-1-1" name="city" placeholder="Enter City">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="role">State : <br /></label>
      		<select name="state_id" class="uk-width-1-1 uk-select">
      			<option value="">Select State</option>
      			@foreach($states as $state)
      			<option value="{{ $state->id }}" >{{ $state->state_name }}</option>
      			@endforeach
      		</select>
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Zip :</label> <br>
      		<input type="number" class="uk-input uk-width-1-3" name="zip" placeholder="xxxxx">
      		<input id="zip_4" type="number" class="uk-input uk-width-1-3" name="zip_4" placeholder="xxxx">
      	</div>
      	@endif
      </div>

      {{-- <div class="uk-width-1-1" id="api_token_block" style="display: none">
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">API Token :</label>
          <input type="text" class="uk-input uk-width-1-1" id="api_token" name="api_token" placeholder="API Token">
        </div>
      </div> --}}
    </div>
    <div class="uk-grid">
    	@if($projects && count($projects))
    	<div class="uk-width-1-1 uk-margin-bottom">
    		<h3>Adding to @if(count($projects) == 1)Project @else Projects @endIf</h3>
    		<ul>
    			@forEach($projects as $p)
    			<li>{{ $p->project_number }} : {{ $p->project_name }}</li>
    			@endForEach
    			<input type="hidden" name="projects" value="{{ is_array($projectIds) ? json_encode($projectIds, true) : implode(',',$projectIds) }}">
    			<input type="hidden" name="person_id" value="{{ $contact->id }}">
    			<input type="hidden" name="multiple" value="{{ $multiple }}">
    		</ul>
    	</div>
    	<hr class="uk-width-1-1 uk-margin-bottom">
    	@endIf
    	<div class="uk-width-1-2"></div>
    	<div class="uk-width-1-4">
    		<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
    	</div>
    	<div class="uk-width-1-4 ">
    		<button type="button" class="uk-button uk-width-1-1 uk-button uk-button-success" id="user_save_button" onclick="submitNewUser()"><span uk-icon="save"></span> SAVE</button>
    	</div>
    </div>
  </form>
</div>
<script>

	// $(document).ready(function(){
	// 	var codeMask = IMask(
	// 	                     document.getElementById('business_phone_number'),
	// 	                     {
	// 	                     	mask: '(000) 000-0000'
	// 	                     });
	// });

	var codeMask = IMask(
	                     document.getElementById('business_phone_number'),
	                     {
	                     	mask: '(000) 000-0000'
	                     });

	function submitNewUser() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		// var tempdiv = '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% auto;"></div></div>';
		// $('#project-detail-tab-7-content').html(tempdiv);
		$("#user_save_button").prop("disabled", true);
		$("#user_save_button").html('<span uk-spinner"></span>  Procesing');
		// $("#user_save_button").css('background-color', 'green');
		var form = $('#userForm');
		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			@if(null == $contact)
			url: "{{ URL::route("admin.createuser") }}",
			@else
			url: "{{ URL::route("admin.createuser_for_contact") }}",
			@endIf
			method: 'post',
			data: {
				first_name: data['first_name'],
				last_name: data['last_name'],
				role: data['role'],
				email: data['email'],
				badge_color: data['badge_color'],
				organization: data['organization'],
				business_phone_number: data['business_phone_number'],
				address_line_1: data['address_line_1'],
				address_line_2: data['address_line_2'],
				city: data['city'],
				state_id: data['state_id'],
				zip: data['zip'],
				zip_4: data['zip_4'],
				person_id: data['person_id'],
				multiple: data['multiple'],
				projects: data['projects'],
				phone_extension: data['phone_extension'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('User has been saved.',{stack: true});
					dynamicModalClose();
					@if(null == $contact)
					$('#users-tab').trigger('click');
					@else
					loadProjectContacts();
					@endIf
				}
				jQuery.each(data.errors, function(key, value){
					$("#user_save_button").prop("disabled", false);
					$("#user_save_button").html('Save');
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

</script>
