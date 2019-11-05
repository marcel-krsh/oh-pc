@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h2 class="uk-text-uppercase uk-text-emphasis">Edit/Remove User Address <a class="uk-button uk-button-danger uk-margin-large-left" onclick="submitRemoveAddress()"><span uk-icon="save"></span> CLICK TO REMOVE ADDRESS</a>
	</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<p>Click the remove address button above to remove this address from the user OR edit the information below </p>
		<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('user.edit-address-of-user', $ua->address_id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-top">
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Address Line 1<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="address_line_1" placeholder="Enter Address Line 1" value="{{ $ua->address->line_1 }}">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Address Line 2 :</label>
					<input type="text" class="uk-input uk-width-1-1" name="address_line_2" placeholder="Enter Address Line 2" value="{{ $ua->address->line_2 }}">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">City<span class="uk-text-danger uk-text-bold">*</span> :</label>
					<input type="text" class="uk-input uk-width-1-1" name="city" placeholder="Enter City" value="{{ $ua->address->city }}">
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="role">State<span class="uk-text-danger uk-text-bold">*</span> :<br /></label>
					<select name="state_id" class="uk-width-1-1 uk-select">
						<option value="">Select State<span class="uk-text-danger uk-text-bold">*</span> :</option>
						@foreach($states as $state)
						<option {{ $ua->address->state_id == $state->id ? 'selected=selected' : '' }} value="{{ $state->id }}" >{{ $state->state_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="uk-width-1-1 uk-margin-top">
					<label for="name">Zip<span class="uk-text-danger uk-text-bold">*</span> :</label> <br>
					<input value="{{ $ua->address->zip }}" type="number" class="uk-input uk-width-1-3" name="zip" placeholder="xxxxx">
					<input value="{{ $ua->address->zip_4 }}" id="zip_4" type="number" class="uk-input uk-width-1-3" name="zip_4" placeholder="xxxx">
				</div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitEditAddress()"><span uk-icon="save"></span> SAVE</a>
			</div>
		</div>
	</form>
	{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<h2 class="uk-text-uppercase uk-text-emphasis">Remove User Address</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> <br>
	<div class="uk-grid">
		<div class="uk-width-1-4">
			<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
		</div>
		<div class="uk-width-1-4 ">
			<a class="uk-button uk-width-1-1 uk-button uk-button-danger" onclick="submitRemoveAddress()"><span uk-icon="save"></span> REMOVE</a>
		</div>
	</div> --}}

<script type="text/javascript">

	function submitEditAddress() {
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
			url: "{{ URL::route("user.edit-address-of-user", $ua->id) }}",
			method: 'post',
			data: {
				address_id: {{ $ua->id }},
				user_id: {{ $ua->user->id }},
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
					UIkit.modal.alert('I have edited address',{stack: true});
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

	function submitRemoveAddress() {
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
			url: "{{ URL::route("user.remove-address-of-user", $ua->address_id) }}",
			method: 'post',
			data: {
				project_id: {{ $project_id }},
				address_id : {{ $ua->id }},
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('I have removed address from user',{stack: true});
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
