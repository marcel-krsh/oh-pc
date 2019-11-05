@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h2 class="uk-text-uppercase uk-text-emphasis">Add Another Address</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('user.add-address-to-user', $user->id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Address Line 1<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="address_line_1" placeholder="Enter Address Line 1">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Address Line 2 :</label>
					<input type="text" class="uk-input uk-width-1-1" name="address_line_2" placeholder="Enter Address Line 2">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">City<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="city" placeholder="Enter City">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="role">State<span class="uk-text-danger uk-text-bold">*</span> :<br /></label>
					<select name="state_id" class="uk-width-1-1 uk-select">
						<option value="">Select State<span class="uk-text-danger uk-text-bold">*</span> :</option>
						@foreach($states as $state)
						<option value="{{ $state->id }}" >{{ $state->state_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Zip<span class="uk-text-danger uk-text-bold">*</span> :</label> <br>
					<input type="number" class="uk-input uk-width-1-3" name="zip" placeholder="xxxxx">
					<input id="zip_4" type="number" class="uk-input uk-width-1-3" name="zip_4" placeholder="xxxx">
				</div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewAddress()"><span uk-icon="save"></span> SAVE</a>
			</div>
		</div>
	</form>

<script type="text/javascript">
	function submitNewAddress() {
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
			url: "{{ URL::route("user.add-address-to-user", $user->id) }}",
			method: 'post',
			data: {
				user_id: {{ $user->id }},
				project_id: {{ $project_id }},
				address_line_1: data['address_line_1'],
				address_line_2: data['address_line_2'],
				city: data['city'],
				state_id: data['state_id'],
				zip: data['zip'],
				zip_4: data['zip_4'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('I have added address to user',{stack: true});
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
