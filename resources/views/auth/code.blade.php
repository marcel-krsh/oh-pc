@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<h3>Choose how you want to receive your temporary identification code</h3>
	<h5>For your security, we need to verify your identity. Below are the email address and phone numbers you have listed with us.</h5>
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<form class="uk-panel uk-panel-box uk-form" role="verificationForm" method="POST" action="{{ url('/code') }}">
			<p>PICK YOUR DELIVERY METHOD</p>
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
			<?php
				$phonenumber = $user->person->allita_phone->area_code . $user->person->allita_phone->phone_number;
				$mask_phonenumber =  mask_phone_number($phonenumber);
				$mask_email =  mask_email($user->email);
			?>

			<h4 class="uk-align-center">Phone</h4>
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

			<h4 class="uk-align-center">Email</h4>
			<div class="uk-grid-small uk-grid" uk-grid="">
				<div class="uk-margin uk-align-center">
					<div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1550">
					<label>{{$mask_email}} <input class="uk-radio uk-margin-medium-left" type="radio" name="delivery_method" value="3"> Email</label>
					</div>
				</div>
			</div>

			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small; delay: 1500">
				<button type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Next</button>
				{{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
			</div>
		</form>
		<div uk-scrollspy="cls:uk-animation-fade; delay: 1600">
			<a href="{{ url('/register') }}" class="uk-button uk-button-default uk-button-small uk-width-1-1 uk-margin-top">Not Registered?</a>
		</div>

	</div>
</div>


@endsection
