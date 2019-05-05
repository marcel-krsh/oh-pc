@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<h3>Enter Verification Code</h3>
		<h5>Your verification code is on it's way. Once you receive it, please enter it below.</h5>
		<form class="uk-panel uk-panel-box uk-form" role="registrationForm" method="POST" action="{{ url('verification') }}">
			<div class="alert alert-danger uk-text-danger" style="display:none"></div>
			@if (count($errors) > 0)
			<div class="alert alert-danger uk-text-danger">
				<ul>
					@foreach ($errors->all() as $error)
					{{ $error }}<br>
					@endforeach
				</ul>
			</div>
			@endif
			{{ csrf_field() }}
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" placeholder="Verification code*" id="verification_code" type="text" name="verification_code" required autofocus >
				@if ($errors->has('verification_code'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('verification_code') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row">
				<input id="device_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('device_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="device_name" value="{{ old('device_name') }}" required autofocus placeholder="Device Name">
				@if ($errors->has('device_name'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('device_name') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Verify</button>
				{{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
			</div>
		</form>
		<div class="uk-grid">
			<div class="{{ env('USER_REGISTRATION') ? 'uk-width-1-2' : 'uk-width-1-1' }}">
				<div uk-scrollspy="cls:uk-animation-fade;">
					<a href="{{env('DEVCO_LOGIN_URL')}}" class="uk-button uk-button-default uk-width-1-1 uk-margin-top">Dev|Co Login</a>
				</div>
			</div>
			@if(env('USER_REGISTRATION'))
			<div class="uk-width-1-2">
				<div uk-scrollspy="cls:uk-animation-fade;">
					<a href="{{ url('/register') }}" class="uk-button uk-button-default uk-width-1-1 uk-margin-top">Register</a>
				</div>
			</div>
			@endif
		</div>
	</div>
</div>

<script>

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
    ele.value =  (new_number.length > 11) ? new_number.substring(0,11) : new_number;

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

  document.getElementById('verification_code').onkeyup = function(e) {
  	business_phone_number_check(this,e);
  }


  function submitCompleteRegistration() {
  	jQuery.ajaxSetup({
  		headers: {
  			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
  		}
  	});
  	var form = $('#registrationForm');
  	var data = { };
  	$.each($('form').serializeArray(), function() {
  		data[this.name] = this.value;
  	});
  	jQuery.ajax({
  		url: "{{ URL::route("user.complete-registration") }}",
  		method: 'post',
  		data: {
  			email_token: $('#email_token').val(),
  			user_id: $('#user_id').val(),
  			password: data['password'],
  			password_confirmation: data['password_confirmation'],
  			'_token' : '{{ csrf_token() }}'
  		},
  		success: function(data){
  			$('.alert-danger' ).empty();
  			jQuery.each(data.errors, function(key, value){
  				jQuery('.alert-danger').show();
  				jQuery('.alert-danger').append('<p>'+value+'</p>');
  			});
  		}
  	});
  }
</script>
@endsection
