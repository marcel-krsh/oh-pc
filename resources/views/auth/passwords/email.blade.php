@extends('layouts.plain-allita')

@section('content')
@if(Auth::guest())
<div class="uk-vertical-align uk-text-center" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<a href="{{env('DEVCO_LOGIN_URL')}}"><img src="https://devco.ohiohome.org/AuthorityOnline/images/Logo.jpg"> </a>
		<p>Program Compliance Inspection</p>
		<p>FORGOT PASSWORD</p>
		@if(session('status'))
			<hr class="dashed-hr">
				<p>{{session('status')}}</p>
			<hr class="dashed-hr">
		@endIf
		<form class="uk-panel uk-panel-box uk-form" role="form" method="POST" action="{{ url('/password/email') }}">
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
			<div id="reset-field" class="uk-form-row {{ $errors->has('email') ? ' uk-form-danger' : '' }}" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
				<input class="uk-input uk-width-1-1 uk-form-large" type="text" placeholder="Email" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus >
				@if ($errors->has('email'))
				<span class="uk-block-primary">
					<strong class="uk-dark uk-light">{{ $errors->first('email') }}</strong>
				</span>
				@endif
			</div>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1600">
				<button id="reset-button" type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="$('#reset-field').slideUp(); $('#reset-button').html('Requesting Link...');">Send Password Reset Link</button>
			</div>
			<div class="uk-form-row uk-text-small" uk-scrollspy="target:.next-items;cls:uk-animation-fade; delay: 1400">
				<a class="uk-float-right uk-link uk-link-muted next-items" href="{{ url('/login') }}">Back to Login</a>
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
</div>
@endif
@endsection
