@extends('layouts.plain-allita')
@section('content')
@if(Auth::guest())
<div class="uk-vertical-align uk-text-center" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<a href="{{env('DEVCO_LOGIN_URL')}}"><img src="https://devco.ohiohome.org/AuthorityOnlineALTTEST/images/Logo.jpg"> {{-- Login through DevCo --}}</a>
		<p>Program Compliance Inspection</p>
		<p>LOGIN</p>
		<form class="uk-panel uk-panel-box uk-form" role="form" method="POST" action="{{ url('/login') }}">
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
			<div class="uk-form-row {{ $errors->has('email') ? ' uk-form-danger' : '' }}" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small;">
				<input class="uk-input uk-width-1-1 uk-form-large" type="text" placeholder="Email" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus >
				@if ($errors->has('email'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small;">
				<input class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password') ? ' uk-form-danger' : '' }}" placeholder="Password" type="password" name="password" required>
				@if ($errors->has('password'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Login</button>
			</div>
			<div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade;">
				<label class="uk-float-left"><input type="checkbox" name="remember" class="uk-checkbox next-items"> Remember Me</label>
				<a class="uk-float-right uk-link uk-link-muted next-items" href="{{ url('/password/reset') }}">Forgot Password?</a>
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
</div>
@endif

<script type="text/javascript">
	$( document ).ready(function() {
	    //alert('Uh oh, looks like your login expired. You can login again or can go to DevCo and login.');
	  });
	</script>
	@endsection
