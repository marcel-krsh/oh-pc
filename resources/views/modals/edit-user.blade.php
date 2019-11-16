<script type="text/javascript">
  // Auditor(2) and above roles would have API Key
  $('#role').change(function() {
  	var value = $("#role").val();
  	if(value >= 2){
  		document.getElementById('api_token_block').style.display='block';
  	} else {
  		document.getElementById('api_token_block').style.display='none';
  		$('#api_token').val('');
  	}
  });
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

	<h3 class="uk-text-uppercase">Edit User: <span class="uk-text-primary">{{ $user->name }}</span></h3>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ url('modals/edituser', $user->id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-2">
				<div class="uk-width-1-1">
					<label for="role">Role<span class="uk-text-danger uk-text-bold">*</span> : <br /></label>
					<select id="role" name="role" class="uk-width-1-1 uk-select">
            <option value="0" {{ !($user_role) ? 'selected=selected':'' }}>No Access</option>
						@foreach($roles as $role)
							<option {{ $user_role == $role->id ? 'selected=selected':'' }} value="{{ $role->id }}" >{{ $role->role_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">First Name<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="first_name" placeholder="Enter First name" value="{{ $user->person->first_name }}">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Last Name<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="last_name" placeholder="Enter Last name" value="{{ $user->person->last_name }}">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Work Email<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input disabled="disabled" type="text" class="uk-input uk-form-large uk-width-1-1" name="email" placeholder="Enter Email" value="{{ $user->email }}">
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
        		<option {{ $user->badge_color == '' ? 'selected=selected':'' }} value="blue">Select Badge</option>
        		<option {{ $user->badge_color == 'blue' ? 'selected=selected':'' }} value="blue" >Blue</option>
        		<option {{ $user->badge_color == 'green' ? 'selected=selected':'' }} value="green" >Green</option>
        		<option {{ $user->badge_color == 'orange' ? 'selected=selected':'' }} value="orange" >Orange</option>
        		<option {{ $user->badge_color == 'pink' ? 'selected=selected':'' }} value="pink" >Pink</option>
        		<option {{ $user->badge_color == 'sky' ? 'selected=selected':'' }} value="sky" >Sky</option>
        		<option {{ $user->badge_color == 'red' ? 'selected=selected':'' }} value="red" >Red</option>
        		<option {{ $user->badge_color == 'purple' ? 'selected=selected':'' }} value="purple" >Purple</option>
        		<option {{ $user->badge_color == 'yellow' ? 'selected=selected':'' }} value="yellow" >Yellow</option>
        	</select>
        </div>
        <div class="uk-width-1-1 uk-margin-top">
        	<label for="name">Business Phone Number :</label><br>
        	<input id="business_phone_number" type="text" class="uk-input uk-width-1-3" name="business_phone_number" placeholder="Format: xxx-xxx-xxxx" value="{{ $user_phone }}">
        	<input id="phone_extension" type="number" class="uk-input uk-width-1-3" name="phone_extension" placeholder="xxxx">
        </div>
      </div>

      <div class="uk-width-1-2">
      	<div class="uk-width-1-1">
      		<label for="role">Organization : <br /></label>
      		<select name="organization" class="uk-width-1-1 uk-select">
      			<option value="">Select Organization</option>
      			@foreach($organizations as $organization)
      			<option {{ $user_organization ==  $organization->id ? 'selected=selected': ''}} value="{{ $organization->id }}" >{{ $organization->organization_name }}</option>
      			@endforeach
      		</select>
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 1 :</label>
      		<input value="{{ ($default_address) ? $default_address->line_1 : '' }}" type="text" class="uk-input uk-width-1-1" name="address_line_1" placeholder="Enter Address Line 1">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Address Line 2 :</label>
      		<input value="{{ ($default_address) ? $default_address->line_2 : '' }}" type="text" class="uk-input uk-width-1-1" name="address_line_2" placeholder="Enter Address Line 2">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">City :</label>
      		<input value="{{ ($default_address) ? $default_address->city : '' }}" type="text" class="uk-input uk-width-1-1" name="city" placeholder="Enter City">
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="role">State : <br /></label>
      		<select name="state_id" class="uk-width-1-1 uk-select">
      			<option value="">Select State</option>
      			@foreach($states as $state)
      			<option  {{ ($default_address) ? ($default_address->state_id   == $state->id ? 'selected=selected' : '') : '' }} value="{{ $state->id }}" >{{ $state->state_name }}</option>
      			@endforeach
      		</select>
      	</div>
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">Zip :</label> <br>
      		<input value="{{ ($default_address) ? $default_address->zip : '' }}" type="number" class="uk-input uk-width-1-3" name="zip" placeholder="xxxxx">
      		<input value="{{ ($default_address) ? $default_address->zip_4 : '' }}" id="zip_4" type="number" class="uk-input uk-width-1-3" name="zip_4" placeholder="xxxx">
      	</div>
      </div>

      <div class="uk-width-1-1" id="api_token_block" style="display: none">
      	<div class="uk-width-1-1 uk-margin-top">
      		<label for="name">API Token :</label>
      		<input value="{{ $user->api_token }}" type="text" class="uk-input uk-width-1-1" id="api_token" name="api_token" placeholder="API Token">
      	</div>
      </div>
    </div>
    <div class="uk-grid">
    	<div class="uk-width-1-4">
    		<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
    	</div>
    	<div class="uk-width-1-4 ">
    		<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewUser()"><span uk-icon="save"></span> SAVE</a>
    	</div>
    </div>
  </form>

<script type="text/javascript">

 $( document ).ready(function() {
  	var value = $("#role").val();
  	if(value >= 2){
  		document.getElementById('api_token_block').style.display='block';
  	} else {
  		document.getElementById('api_token_block').style.display='none';
  		$('#api_token').val('');
  	}
  	$('#business_phone_number').keyup();
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

  function submitNewUser() {
  	jQuery.ajaxSetup({
  		headers: {
  			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
  		}
  	});

  	var form = $('#userForm');

  	var data = { };
  	$.each($('form').serializeArray(), function() {
  		data[this.name] = this.value;
  	});
  	jQuery.ajax({
  		url: "{{ url("modals/edituser", $user->id) }}",
  		method: 'post',
  		data: {
  			first_name: data['first_name'],
  			last_name: data['last_name'],
  			role: data['role'],
  			email: data['email'],
	      // password: data['password'],
	      // password_confirmation: data['password_confirmation'],
	      badge_color: data['badge_color'],
	      organization: data['organization'],
	      business_phone_number: data['business_phone_number'],
	      address_line_1: data['address_line_1'],
	      address_line_2: data['address_line_2'],
	      city: data['city'],
	      state_id: data['state_id'],
	      zip: data['zip'],
	      zip_4: data['zip_4'],
	      phone_extension: data['phone_extension'],
	      api_token: data['api_token'],
	      '_token' : '{{ csrf_token() }}',
	      user_id: {{ $user->id }}
	    },
	    success: function(data){
	    	$('.alert-danger' ).empty();
	    	if(data == 1) {
	    		UIkit.modal.alert('User has been saved.',{stack: true});
	    		dynamicModalClose();
	    		$('#users-tab').trigger('click');
	    	}
	    	jQuery.each(data.errors, function(key, value){
	    		jQuery('.alert-danger').show();
	    		jQuery('.alert-danger').append('<p>'+value+'</p>');
	    	});
	    }
	  });
  }

</script>
