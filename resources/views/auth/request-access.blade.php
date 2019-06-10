@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<h3>Access to Program Compliace Inspection</h3>
	<h5>You currenlty do not have permission to access this site. You can request access by clicking the button below.</h5>
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<form id="requestAccessForm" class="uk-panel uk-panel-box uk-form" role="verificationForm" method="POST" action="{{ url('/request-access') }}">
			{{ csrf_field() }}
			<input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;" id="request_access">
				<a id="submit_request" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitRequestAccess()"><span uk-icon="save"></span>Request Access</a>
			</div>
			<p style="display: none" id="success_message" class="uk-width-1-1 uk-button uk-button uk-button-large"></span>Access Request Submitted</p>
			<p style="display: none" id="error_message" class="uk-width-1-1 uk-button uk-button uk-button-large uk-text-danger"></span>Something went wrong</p>
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

<script type="text/javascript">

	function submitRequestAccess() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var form = $('#requestAccessForm');
		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		document.getElementById('request_access').style.display='none';
		document.getElementById('success_message').style.display='block';
		jQuery.ajax({
			url: "{{ url('request-access') }}",
			method: 'post',
			data: {
				user_id: data['user_id'],
	      '_token' : '{{ csrf_token() }}'
	    },
	    success: function(data){
	    	$('.alert-danger' ).empty();
	    	if(data == 1) {
	    		UIkit.modal.alert('Request for access has been successfully submitted',{stack: true});
	    		document.getElementById('request_access').style.display='none';
	    		document.getElementById('success_message').style.display='block';
	    	} else {
	    		UIkit.modal.alert(data,{stack: true});
	    		document.getElementById('request_access').style.display='none';
	    		document.getElementById('error_message').style.display='block';
	    	}
	    	jQuery.each(data.errors, function(key, value){
	    		jQuery('.alert-danger').show();
	    		jQuery('.alert-danger').append('<p>'+value+'</p>');
	    	});
	    }
	  });
	}
</script>
@endsection
