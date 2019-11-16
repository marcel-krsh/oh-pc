@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center uk-margin-large-bottom  uk-margin-top" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
	<div class="uk-vertical-align-middle login-panel"  id="login-panel">
		<img src="/images/ohfa_logo_large.png" style="width: 200px;height: 200px;">
		<h2 style="margin-top:0px">SECURITY CHECK</h2>
	@if($mask_phonenumber)
		<h3>Choose how you would like to receive your temporary identification code</h3>
	@endIf

		<form class="uk-panel uk-panel-box uk-form" role="verificationForm" method="POST" action="{{ url('/code') }}">
			<p class="uk-margin-top">To protect you and others on this site, we utilize two factor authentication. We only require this to be done once per device you use.</p><hr class="dashed-hr uk-margin-bottom">
			@if($mask_phonenumber)<p>PICK YOUR DELIVERY METHOD</p>@else <p>We will email a verification code to the address we have on file:@endIf
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
			@if($mask_phonenumber)
				<h4 class="uk-align-center">PHONE:</h4>
				<div class="uk-grid-small uk-grid" uk-grid="">
					<div class="uk-margin uk-align-center">
						<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
							<label>{{$mask_phonenumber}} <input class="uk-radio uk-margin-medium-left" type="radio" name="delivery_method" value="1"> Text</label>
						</div>
					</div>
				</div>
				<div class="uk-grid-small uk-grid" uk-grid="">
					<div class="uk-margin uk-align-center">
						<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
							<label>{{$mask_phonenumber}} <input class="uk-radio uk-margin-medium-left" type="radio" name="delivery_method" value="2"> Voice</label>
						</div>
					</div>
				</div>
				<hr class="dashed-hr uk-margin-bottom">



			<h4 class="uk-align-center">Email</h4>
			@endif
			<div class="uk-grid-small uk-grid" uk-grid="">
				<div class="uk-margin uk-align-center">
					<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
						<label>{{$mask_email}} <input class="uk-radio uk-margin-medium-left" type="radio" name="delivery_method" value="3" checked="true"> Email</label>
					</div>
				</div>
			</div>

			<div class="uk-form-row uk-margin-bottom" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;">
				<button id="code-button" type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onClick="$('#code-button').html('REQUESTING CODE...');">SEND CODE</button>
				{{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
			</div>
		</form>


	</div>
</div>


@endsection
