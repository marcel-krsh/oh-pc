@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<a href="{{env('DEVCO_LOGIN_URL')}}"><img src="https://devco.ohiohome.org/AuthorityOnline/images/Logo.jpg"> </a>
		<p>Program Compliance Inspection</p>
		<p>RESET PASSWORD</p>
		<form class="uk-panel uk-panel-box uk-form" role="registrationForm" method="POST" action="{{ url('/password/reset') }}">
			 <input type="hidden" name="token" id="token" value="{{ $token }}">
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
			<div class="uk-form-row {{ $errors->has('email') ? ' uk-form-danger' : '' }}" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" type="text" placeholder="Email *" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus >
				<input type="hidden" name="token" value="{{ $token }}">
				@if ($errors->has('email'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" type="password" placeholder="Password *" id="password" type="password" name="password" required autofocus >
				@if ($errors->has('password'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
				<input class="uk-input uk-width-1-1 uk-form-large" placeholder="Confirm Password *" type="password" name="password_confirmation" required>
				@if ($errors->has('password_confirmation'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password_confirmation') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Reset Password</button>
				{{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
			</div>
			<div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade; delay: 1400">
				<a class="uk-float-right uk-link uk-link-muted next-items" href="{{ url('/login') }}">Go To Login</a>
			</div>
		</form>
		<div class="uk-grid">
			<div class="{{ env('USER_REGISTRATION') ? 'uk-width-1-2' : 'uk-width-1-1' }}">
				<div uk-scrollspy="cls:uk-animation-fade;">
					<a href="{{env('DEVCO_LOGIN_URL')}}" class="uk-button uk-button-default uk-width-1-1 uk-margin-top">Dev|Co Login</a>
				</div>
			</div>
			<div class="{{ env('USER_REGISTRATION') ? 'uk-width-1-2' : 'uk-width-1-1' }}">
				<div uk-scrollspy="cls:uk-animation-fade;">
					<a href="{{ url('/login') }}" class="uk-button uk-button-default uk-width-1-1 uk-margin-top">Login</a>
				</div>
			</div>
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
				token: $('#token').val(),
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
