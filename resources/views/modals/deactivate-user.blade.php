@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h3 class="uk-text-uppercase">Deactivate User: <span class="uk-text-primary">{{ $user->name }}</span> </h3>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="deactivateUser" action="{{ url('modals/deactivateuser', $user->id ) }}" method="post" role="deactivateUser">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-2">
				<div class="uk-width-1-1 uk-margin-top">
					<p class="uk-text-large"> Are you sure you want to deactive this user? </p>
				</div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-danger" onclick="submitDeactivateUser()"><span uk-icon="save"></span> YES, DEACTIVATE</a>
			</div>
		</div>
	</form>

<script type="text/javascript">

	function submitDeactivateUser() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});

		var form = $('#deactivateUser');

		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			url: "{{ url("modals/deactivateuser", $user->id) }}",
			method: 'post',
			data: {
				'_token' : '{{ csrf_token() }}',
				user_id: {{ $user->id }}
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('User has been successfully deactivated.',{stack: true});
					dynamicModalClose();
					$('#users-tab').trigger('click');
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

</script>
