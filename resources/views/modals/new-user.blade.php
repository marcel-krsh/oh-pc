@extends('modals.container')
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@section('content')
		<script>
		//resizeModal(95);
		$=jQuery;
		$(document).ready(function() {
			$("#userform").submit(function(e) {
			   e.preventDefault();
		       $.ajax({
                  type: "POST",
                  url: '/modals/createuser',
                  data: $("#userform").serialize(),
                  dataType: 'json',
                  success: function(data)
                  {
                  	if (data.status > 0) {
                  		//success
                  		$('#userform').html(data.message);
                  		$('#dash-subtab-7').trigger('click');
                  		
                  	} else {
                  		alert('Failed to update user roles');
                  	}
                  }
               });
			   
			});
		});
		</script>
		@if (count($errors) > 0)
		    <div class="uk-panel uk-margin-top uk-margin-bottom">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
		<form id='userform' action="/modals/createuser" method="post" role="form">
				{{csrf_field()}}
	        	
	        	<div class="uk-form-row">
					<div class="uk-width-1-1">
						<H3>Assigned Roles</H3>
						<hr class="uk-margin-top-remove">

						<div class="uk-grid">
						@if(Auth::user()->entity_type == 'hfa')
							<div class="uk-width-1-2@m uk-width-1-1@s">
								<label for="name">HFA ROLES:<br /></label>
								@foreach($hfa_roles as $role)
								<input type="checkbox" name="role[{{$role->id}}]" class="uk-input uk-margin-small-right" >&nbsp;<?php echo $role->role_name; ?><br />
								@endforeach
							</div>
							<div class="uk-width-1-2@m uk-width-1-1@s">
								<label for="name">LANDBANK ROLES:<br /></label>
								@foreach($lb_roles as $role)
								<input type="checkbox" name="role[{{$role->id}}]" class="uk-input uk-margin-small-right" >&nbsp;<?php echo $role->role_name; ?><br />
								@endforeach
							</div>
						@else
							<div class="uk-width-1-2@m uk-width-1-1@s">
								<label for="name">LANDBANK ROLES:<br /></label>
								@foreach($lb_roles as $role)
								<input type="checkbox" name="role[{{$role->id}}]" class="uk-input uk-margin-small-right" >&nbsp;<?php echo $role->role_name; ?><br />
								@endforeach
							</div>
						@endif
						</div>		
				    </div>
				</div>
				@if(Auth::user()->entity_type == 'hfa')
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">USER TYPE:</label>
					    <hr class="uk-margin-top-remove">
					    
					    <select name="entity_type" class="uk-width-1-1 uk-select uk-form-large">
					    	<option value="blue">Please Select Usertype</option>
					    	<option value="landbank" >Landbank User</option>
					    	<option value="hfa">Housing Finance Agency</option>
					    </select>
				    </div>
				</div>
				@endif
				@if(Auth::user()->entity_type == 'hfa')
				<div class="uk-form-row">
					<div class="uk-width-1-1">
						<H3>ENTITY</H3>
						<hr class="uk-margin-top-remove">

						<div class="uk-grid">
							<div class="uk-width-1-2@m uk-width-1-1@s">
								<label for="name">NAME:<br /></label>
								
								<select name="entity_id" class="uk-width-1-1 uk-select uk-form-large">
							    	<option value="">Please Select An Entity</option>
							    	@foreach($entities as $entity)
							    	<option value="{{$entity->id}}" >{{$entity->entity_name}}</option>
							    	@endforeach
							    </select>
							</div>
						</div>		
				    </div>
				</div>
				@endif
				<div class="uk-form-row">
				<H3>USER INFORMATION</H3>
					<hr class="uk-margin-top-remove">
				    <div class="uk-width-1-1">
				    	<label for="name">NAME:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="text" class="uk-input uk-form-large uk-width-1-1" name="name" value="" required="">
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">EMAIL:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="text" class="uk-input uk-form-large uk-width-1-1" name="email" value="" required="">
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">PASSWORD:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password" value="" placeholder="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" >
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">CONFIRM PASSWORD:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password_confirmation" value="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');">
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">BADGE COLOR:</label>
					    <hr class="uk-margin-top-remove">
					    
					    <select name="badge_color" class="uk-width-1-1 uk-select uk-form-large">
					    	<option value="blue">Please Select Your Color</option>
					    	<option value="blue" >Blue</option>
					    	<option value="green" >Green</option>
					    	<option value="orange" >Orange</option>
					    	<option value="pink" >Pink</option>
					    	<option value="sky" >Sky</option>
					    	<option value="red" >Red</option>
					    	<option value="purple" >Purple</option>
					    	<option value="yellow" >Yellow</option>
					    </select>
				    </div>
				</div>
				<div class="uk-form-row">

			    </div>
	        	<div class="uk-grid">
					<div class="uk-width-1-1">
					<hr class="uk-margin-top-remove">
						<button id='saveuser' class="uk-button uk-button-success uk-width-1-1">Save Changes</button>
				    </div>
			    </div>
		</form>
@stop