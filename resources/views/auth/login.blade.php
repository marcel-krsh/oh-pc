@extends('layouts.plain-allita')
@section('content')
@if(Auth::guest())
<div class="uk-vertical-align uk-text-center uk-margin-large-bottom  uk-margin-top" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<img src="/images/ohfa_logo_large.png" style="width: 200px;height: 200px;">
		<h2 style="margin-top:0px">Ohio Housing Finance Agency</h2>
		<p>Dev|Co Inspection Direct Login</p>
		
		@if(session('loginMessage'))
		<hr class="dashed-hr">
		{{session('loginMessage')}}
		<hr class="dashed-hr margin-bottom">
		<?php session()->forget('loginMessage'); ?>
		@endIf
		@if(session('status'))
		<hr class="dashed-hr">
		{{session('status')}}
		<hr class="dashed-hr margin-bottom">

		@endIf
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
		@if(session()->pull('password-reset-success'))
			UIkit.modal.alert('Your password reset was successful, login to access your account',{stack: true});
		@endif
    //alert('Uh oh, looks like your login expired. You can login again or can go to DevCo and login.');
  });
</script>
@endsection
