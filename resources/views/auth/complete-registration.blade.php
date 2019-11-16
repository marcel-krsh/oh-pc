@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<p>NEIGHBORHOOD INITIATIVE PROGRAM</p>
		<p>COMPLETE REGISTRATION</p>
		<form class="uk-panel uk-panel-box uk-form" role="registrationForm" method="POST" action="{{ URL::route("user.complete-registration") }}">
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
			<input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
			<input type="hidden" name="email_token" id="email_token" value="{{ $email_token }}">
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" type="password" placeholder="Password" id="password" type="password" name="password" required autofocus >
				@if ($errors->has('password'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
				<input class="uk-input uk-width-1-1 uk-form-large" placeholder="Confirm Password" type="password" name="password_confirmation" required>
				@if ($errors->has('password_confirmation'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password_confirmation') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Create Password</button>
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
