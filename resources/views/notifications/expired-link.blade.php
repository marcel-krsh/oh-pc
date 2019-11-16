@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<h3>Your notification link expired</h3>
	<div class="uk-vertical-align-middle login-panel">
		<form id="requestAccessForm" class="uk-panel uk-panel-box uk-form" role="notificationResendLink">
			{{ csrf_field() }}
			<h5>Click the button below to request a new link to be sent to your email.</h5>
			<input type="hidden" name="user_id" id="user_id" value="{{ $user_id }}">
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;" id="request_access">
				<button type="button" id="submit_resend_link" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitResendLink()"><span uk-icon="save"></span>Request New Link</button>
			</div>
			<p style="display: none" id="success_message" class="uk-width-1-1 uk-button uk-button uk-button-large"></span>New link has been sent to your email.</p>
			<p style="display: none" id="error_message" class="uk-width-1-1 uk-button uk-button uk-button-large uk-text-danger"></span>Something went wrong, please try again later</p>
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

	function submitResendLink() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		var form = $('#notificationResendLink');
		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			url: "{{ url('resend-notification-link') }}",
			method: 'post',
			data: {
				user_id: data['user_id'],
	      '_token' : '{{ csrf_token() }}'
	    },
	    success: function(data) {
	    	// alert(data);
	    	// return;
	    	$("#submit_resend_link").attr("disabled", true);
	    	$('.alert-danger' ).empty();
	    	if(data == 1) {
	    		UIkit.modal.alert('New link has been sent to your email',{stack: true});
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
