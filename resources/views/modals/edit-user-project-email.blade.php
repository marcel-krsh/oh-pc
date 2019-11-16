@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h2 class="uk-text-uppercase uk-text-emphasis">@if($up->email_address->email_address)Edit @else Add @endIf User Email Address <a class="uk-button uk-button-danger uk-margin-large-left" onclick="submitRemoveEmail()"><span uk-icon="save"></span> CLICK TO REMOVE EMAIL</a></h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
		<p>Click the remove email button above to remove this email from the user OR edit the email below </p>
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('user.edit-email-of-user', $up->user->id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="uk-width-1-1 uk-margin-top">
          <label for="name">Email Address :</label><br>
          <input id="email_address" type="text" class="uk-input uk-width-1-3" name="email_address" placeholder="Enter Email Address" value="{{ $up->email_address->email_address }}">
        </div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewEmail()"><span uk-icon="save"></span> SAVE</a>
			</div>
		</div>
	</form>

<script type="text/javascript">

	function submitNewEmail() {
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
			url: "{{ URL::route("user.edit-email-of-user", $up->user->id) }}",
			method: 'post',
			data: {
				user_id: {{ $up->user->id }},
				project_id: {{ $project_id }},
				email_address_id: {{ $up->email_address->id }},
				email_address: data['email_address'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('I have edited email address of user',{stack: true});
					dynamicModalClose();
					loadTab('/project/'+{{ $project_id }}+'/contacts/', '7', 0, 0, 'project-', 1);
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}

	function submitRemoveEmail() {
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
			url: "{{ URL::route("user.remove-email-of-user", $up->id) }}",
			method: 'post',
			data: {
				project_id: {{ $project_id }},
				email_id : {{ $up->id }},
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('I have removed email from user',{stack: true});
					dynamicModalClose();
					loadTab('/project/'+{{ $project_id }}+'/contacts/', '7', 0, 0, 'project-', 1);
				}
				jQuery.each(data.errors, function(key, value){
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}


</script>
