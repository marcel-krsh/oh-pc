@extends('layouts.plain-allita')
@section('content')
<div class="uk-vertical-align uk-text-center">
	<h3>Access to Program Compliace Inspection</h3>
	<h5>Approve the access request To: {{ $user->name }}</h5>
	<div class="uk-vertical-align-middle login-panel" id="login-panel">
		<form id="requestAccessForm" class="uk-panel uk-panel-box uk-form" role="verificationForm" method="POST" action="{{ url('user/approve-access', $user->id) }}">
			<div id="access_fields">
			{{ csrf_field() }}
			<input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
			<div class="uk-grid uk-margin-bottom">
				<label class="uk-width-1-3 uk-margin-small-top" for="role">Select Role<span class="uk-text-danger uk-text-bold">*</span> : <br /></label>
				<select id="role" name="role" class="uk-width-2-3 uk-select">
					@foreach($roles as $role)
						<option value="{{ $role->id }}" >{{ $role->role_name }}</option>
					@endforeach
				</select>
			</div>
			<p style="display: none" id="validation" class="uk-text-danger"></span>Role is required</p>
			<div class="uk-form-row" uk-scrollspy="target:.uk-button;cls:uk-animation-slide-top-small;" id="request_access">
				<a id="submit_request" class="uk-width-1-1 uk-button uk-button-primary uk-button-large" onclick="submitApproveAccess()"><span uk-icon="save"></span>Approve Access</a>
			</div>
			</div>
			<div id="success_message" style="display: none" >
			<p class="uk-width-1-1 uk-button uk-button uk-button-large"></span>Access provided successfully</p>
			<a href="{{ url('/') }}">Go to Dashboard</a>
			</div>
			<p style="display: none" id="error_message" class="uk-width-1-1 uk-button uk-button uk-button-large uk-text-danger"></span>Something went wrong</p>
		</form>
	</div>
</div>

<script type="text/javascript">

	function submitApproveAccess() {
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
		jQuery.ajax({
			url: "{{ url('user/approve-access', $user->id) }}",
			method: 'post',
			data: {
				role: data['role'],
				user_id: data['user_id'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 2) {
					document.getElementById('validation').style.display='block';
				} else if(data == 1) {
					UIkit.modal.alert('Successfully approved the access',{stack: true});
					document.getElementById('access_fields').style.display='none';
					document.getElementById('success_message').style.display='block';
				} else {
					UIkit.modal.alert(data,{stack: true});
					document.getElementById('access_fields').style.display='none';
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
