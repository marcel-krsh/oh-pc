@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h3 class="uk-text-uppercase">Reset Password: <span class="uk-text-primary">{{ $user->name }}</span></h3>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ url('modals/resetpassword', $user->id ) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-2">
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Password<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="password" class="uk-input uk-form-large uk-width-1-1" name="password" placeholder="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" >
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Confirm Password<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="password" class="uk-input uk-form-large uk-width-1-1" name="password_confirmation" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');">
				</div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitResetPasword()"><span uk-icon="save"></span> SAVE</a>
			</div>
		</div>
	</form>

<script type="text/javascript">
	function submitResetPasword() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});

		var form = $('#userForm');

		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			url: "{{ url("modals/resetpassword", $user->id) }}",
			method: 'post',
			data: {
				password: data['password'],
				password_confirmation: data['password_confirmation'],
				'_token' : '{{ csrf_token() }}',
				user_id: {{ $user->id }}
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('User password been updated.',{stack: true});
					dynamicModalClose();
					//$('#users-tab').trigger('click');
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

</script>
