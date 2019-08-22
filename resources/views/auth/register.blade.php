@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center uk-margin-large-bottom uk-margin-top" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<img src="/images/ohfa_logo_large.png" style="width: 200px;height: 200px;">
		@if( env('USER_REGISTRATION'))
		<h2 style="margin-top:0px">REGISTER</h2>
		<form class="uk-panel uk-panel-box uk-form" role="form" method="POST" action="{{ url('/register-user') }}">
			@if (count($errors) > 0)
			<div class="alert alert-danger uk-text-danger">
				<p>Check the errors below</p>
			</div>
			@endif
			{{ csrf_field() }}
			<div class="uk-form-row">
				<input id="first_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('first_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus placeholder="First Name *">
				@if ($errors->has('first_name'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('first_name') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row">
				<input id="last_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('last_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="last_name" value="{{ old('last_name') }}" required autofocus placeholder="Last Name *">
				@if ($errors->has('last_name'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('last_name') }}</strong>
				</span>
				@endif
			</div>

			<div class="uk-form-row">
				<input id="email" type="email" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('email') ? ' uk-form-danger uk-animation-shake' : '' }}" name="email" value="{{ old('email') }}" required placeholder="Your Email *">
				@if ($errors->has('email'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>

			<div class="uk-form-row">
				<input id="password" type="password" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password') ? ' uk-form-danger uk-animation-shake' : '' }}" name="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" required placeholder="Password *">
				@if ($errors->has('password'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('password') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row">
				<input id="password-confirm" type="password" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password_confirmation') ? ' uk-form-danger uk-animation-shake' : '' }}" name="password_confirmation" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');" required placeholder="Confirm Password *">
				@if ($errors->has('password_confirmation'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('password_confirmation') }}</strong>
				</span>
				@endif
			</div>

			<div class="uk-form-row">
				<input id="phone_number" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('phone_number') ? ' uk-form-danger uk-animation-shake' : '' }}" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Phone Number *: xxx-xxx-xxxx">
				@if ($errors->has('phone_number'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>{{ $errors->first('phone_number') }}</strong>
				</span>
				@endif
			</div>

			<div class="uk-form-row uk-margin-top">
				{!! app('captcha')->display(); !!}
				@if ($errors->has('g-recaptcha-response'))
				<span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
					<strong>Please prove you're not a robot.</strong>
				</span>
				@endif
				<br />
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Register</button>
			</div>
		</form>
		@else
		<h2 style="margin-top:0px">REGISTRATION HAS BEEN DISABLED</h2>
		<hr class="dashed-hr uk-margin-bottom">
		<p>If you would like to have access to this site, please contact your adminstrator.</p>
		@endIf
		
	</div>
</div>
@if( env('USER_REGISTRATION'))
<script type="text/javascript">
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

  document.getElementById('phone_number').onkeyup = function(e) {
  	business_phone_number_check(this,e);
  }
</script>
@endIf
@endsection
