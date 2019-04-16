@extends('layouts.plain-allita')

@section('content')
@if(Auth::guest())
<div class="uk-vertical-align uk-text-center" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<a href="https://devco.ohiohome.org/AuthorityOnlineALTTEST/default.aspx?ReturnUrl=%2fAuthorityOnlineALTTest%3fredirect%3dhttps%253A%252F%252Fpcinspecttrain.ohiohome.org&redirect=https%3A%2F%2Fpcinspecttrain.ohiohome.org"><img src="https://devco.ohiohome.org/AuthorityOnlineALTTEST/images/Logo.jpg"> {{-- Login through DevCo --}}</a>

		<p>NEIGHBORHOOD INITIATIVE PROGRAM</p>
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
			<div class="uk-form-row {{ $errors->has('email') ? ' uk-form-danger' : '' }}" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" type="text" placeholder="Email" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus >
				@if ($errors->has('email'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
				<input class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('password') ? ' uk-form-danger' : '' }}" placeholder="Password" type="password" name="password" required>
				@if ($errors->has('password'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('password') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Login</button>
			</div>
			<div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade; delay: 1400">
				<label class="uk-float-left"><input type="checkbox" name="remember" class="uk-checkbox next-items"> Remember Me</label>
				<a class="uk-float-right uk-link uk-link-muted next-items" href="{{ url('/password/reset') }}">Forgot Password?</a>
			</div>
		</form>
		@if(env('USER_REGISTRATION'))
		<div uk-scrollspy="cls:uk-animation-fade; delay: 2200">
			<a href="{{ url('/register') }}" class="uk-button uk-button-default uk-button-small uk-width-1-1 uk-margin-top">Not Registered?</a>
		</div>
		@endif

	</div>
</div>
@endif
@endsection
