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
				<input class="uk-input uk-width-1-1 uk-form-large" placeholder="Verification code" id="verification_code" type="text" name="verification_code" required autofocus >
				@if ($errors->has('verification_code'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('verification_code') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Verify</button>
				{{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
			</div>
		</form>
		<div uk-scrollspy="cls:uk-animation-fade; delay: 2200">
			<a href="{{ url('/register') }}" class="uk-button uk-button-default uk-button-small uk-width-1-1 uk-margin-top">Not Registered?</a>
		</div>

	</div>
</div>

<script>
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
