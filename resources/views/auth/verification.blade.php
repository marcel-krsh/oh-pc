@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center uk-margin-large-bottom  uk-margin-top" uk-scrollspy="target:#login-panel;cls:uk-animation-slide-top-small uk-transform-origin-bottom; delay: 1300">
  <div class="uk-vertical-align-middle login-panel"  id="login-panel">
    <img src="/images/ohfa_logo_large.png" style="width: 200px;height: 200px;">
    <h2 style="margin-top:0px">PLEASE ENTER YOUR CODE BELOW</h2>
    <p class="uk-visible@m">Your verification code is on its way. Please be patient, as it may take up to 5 minutes for it to arrive.</p>
    <form class="uk-panel uk-panel-box uk-form" role="registrationForm" method="POST" action="{{ url('verification') }}" autocomplete="off">
      <div class="alert alert-danger uk-text-danger" style="display:none"></div>
      @if (count($errors) > 0)
      <div class="alert alert-danger uk-text-danger">
        <ul style="padding:0px !important;">
          @foreach ($errors->all() as $error)
          {{ $error }}<br>
          @endforeach
        </ul>
      </div>
      @endif
      {{ csrf_field() }}
      <div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
        <input class="uk-input uk-width-1-1 uk-form-large" placeholder="Verification Code" id="verification_code" type="text" name="verification_code" required autofocus autocomplete="false" style="font-size:24px;" onfocus="$('#verify-button').html('Verify');" >
        @if ($errors->has('verification_code'))
        
        @endif
      </div>
      <div class="uk-form-row" uk-scrollspy="target:.uk-input;cls:uk-animation-slide-top-small; delay: 1500">
        <input id="device_name" type="text" class="uk-input uk-width-1-1 uk-form-large {{ $errors->has('device_name') ? ' uk-form-danger uk-animation-shake' : '' }}" name="device_name" value="{{ old('device_name') }}" required placeholder="Device Name" onfocus="$('#verify-button').slideDown();$('#verify-button').html('Verify');">
        @if ($errors->has('device_name'))
        <span class="uk-align-left uk-alert uk-alert-danger uk-width-1-1 uk-margin-top-remove uk-animation-fade">
          <strong>{{ $errors->first('device_name') }}</strong>
        </span>
        @endif
      </div>
      <div class="uk-form-row" >
        <button id="verify-button" type="submit" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onClick="$(this).html('Verifying...');" style="@if(!old('device_name'))display:none;@endIf">Verify</button>
        {{-- <a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitCompleteRegistration()">Create Password</a> --}}
      </div>
    </form>
    
</div>
<script src="https://unpkg.com/imask"></script>
<script>

  var codeMask = IMask(
  document.getElementById('verification_code'),
  {
    mask: '000-000-000'
  });
</script>
@endsection
