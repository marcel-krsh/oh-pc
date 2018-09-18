@extends('modals.container')
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@section('content')
		<script>
		resizeModal(95);
		$=jQuery;
		$(document).ready(function() {
			$("#userform").submit(function(e) {
			   e.preventDefault();
		       $.ajax({
                  type: "POST",
                  url: '/modals/user/edit/{{ $editUser->id }}',
                  data: $("#userform").serialize(),
                  dataType: 'json',
                  success: function(data)
                  {
                  	if(data.status == 0){
                  		UIkit.modal.alert(data.message);
                  	}else if (data.status > 0) {
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
		<form id='userform' action="/modals/user/edit/{{ $editUser->id }}" method="post" role="form">
				{{csrf_field()}}
	        	
						@php $userRoles = array(); @endphp
						@foreach ($editUser->roles as $role)
						@php $userRoles[] = $role->id; @endphp
                        @endforeach
                        
	        	<div class="uk-form-row">
					<div class="uk-width-1-1">
						<H3>EDIT ASSIGNED ROLES</H3>
						<hr class="uk-margin-top-remove">

						<div class="uk-grid">
						@if(Auth::user()->entity_type == 'hfa')
							@if($editUser->entity_type === 'hfa')
							<div class="uk-width-1-1@m uk-width-1-1@s">
								<label for="name">HFA ROLES:<br /></label>
								@foreach($hfa_roles as $role)
								<div class="uk-margin-small-top">
									<div style="display: inline-block; width:15px; vertical-align: top;"> <input type="checkbox" name="role[{{$role->id}}]" class="uk-checkbox uk-margin-small-right" 
			                        <?php if(in_array($role->id,$userRoles)) { echo "CHECKED"; } ?>>
			                        </div>&nbsp;
			                        <div style="display: inline-block; width:440px; vertical-align: top;"> <?php echo $role->role_name; ?></div>
			                    </div>
								@endforeach
							</div>
							@endif
							@if($editUser->entity_type === 'landbank')
							<div class="uk-width-1-1@m uk-width-1-1@s">
								<label for="name">LANDBANK ROLES:<br /></label>
								@foreach($lb_roles as $role)
								<div class="uk-margin-small-top">
									<div style="display: inline-block; width:15px; vertical-align: top;"> <input type="checkbox" name="role[{{$role->id}}]" class="uk-checkbox uk-margin-small-right" 
			                        <?php if(in_array($role->id,$userRoles)) { echo "CHECKED"; } ?>>
			                        </div>&nbsp;
			                        <div style="display: inline-block; width:440px; vertical-align: top;"><?php echo $role->role_name;?></div>
			                        </div>
								@endforeach
							</div>
							@endif
						@else
							<div class="uk-width-1-1@m uk-width-1-1@s">
								<label for="name">LANDBANK ROLES:<br /></label>
								@foreach($lb_roles as $role)
								<div class="uk-margin-small-top">
									<div style="display: inline-block; width:15px; vertical-align: top;"> <input type="checkbox" name="role[{{$role->id}}]" class="uk-checkbox uk-margin-small-right" 
			                        <?php if(in_array($role->id,$userRoles)) { echo "CHECKED"; } ?>>
			                        </div>&nbsp;
			                        <div style="display: inline-block; width:440px; vertical-align: top;"><?php echo $role->role_name;?></div>
			                        </div>
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
					    
					    <select name="entity_type" class="uk-select uk-width-1-1 uk-form uk-form-large">
					    	<option value="blue">Please Select Usertype</option>
					    	<option value="landbank" {{ $editUser->entity_type === 'landbank' ? 'SELECTED' : ''}}>Landbank User</option>
					    	<option value="hfa" {{ $editUser->entity_type === 'hfa' ? 'SELECTED' : '' }}>Housing Finance Agency</option>
					    </select>
				    </div>
				</div>
				@endif
				<div class="uk-form-row">
					<div class="uk-width-1-1@m uk-width-1-1@s">
						<label for="name">USER ENTITY:<br /></label> 
						<hr class="uk-margin-top-remove">
						<select name="entity_id" class="uk-select uk-width-1-1 uk-form uk-form-large">
				    		<option value="blue">Please Select Your Entity</option>
							@foreach($entities as $entity)
							<option value="{{$entity->id}}" @if($entity->id == $editUser->entity_id) SELECTED @endIf >{{$entity->entity_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="uk-form-row">
				<H3>USER INFORMATION</H3>
					<hr class="uk-margin-top-remove">
				    <div class="uk-width-1-1">
				    	<label for="name">NAME:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="text" class="uk-input uk-form-large uk-width-1-1" name="name" value="{{$editUser->name}}" required="">
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">EMAIL:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="text" class="uk-input uk-form-large uk-width-1-1" name="email" value="{{$editUser->email}}" required="">
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">PASSWORD:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password" value="" placeholder="Only Enter A Password To Change The Current One" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" >
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
				    	<label for="name">API TOKEN:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="text" class="uk-input uk-form-large uk-width-1-1" name="api_token" value="{{$editUser->api_token}}" placeholder="Check the box to generate a new API Token." readonly="true" >
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">GENERATE NEW API TOKEN:</label>
					    <hr class="uk-margin-top-remove">
					    <input type="checkbox" name="api_token_reset" class="uk-checkbox" value="1"> YES, GENERATE A NEW TOKEN
				    </div>
				</div>
				<div class="uk-form-row">
				    <div class="uk-width-1-1">
				    	<label for="name">BADGE COLOR:</label>
					    <hr class="uk-margin-top-remove">
					    
					    <select name="badge_color" class="uk-select uk-width-1-1 uk-form uk-form-large">
					    	<option value="blue">Please Select Your Color</option>
					    	<option value="blue" {{ $editUser->badge_color === 'blue' ? 'SELECTED' : ''}}>Blue</option>
					    	<option value="green" {{ $editUser->badge_color === 'green' ? 'SELECTED' : '' }}>Green</option>
					    	<option value="orange" {{ $editUser->badge_color === 'orange' ? 'SELECTED' : '' }}>Orange</option>
					    	<option value="pink" {{ $editUser->badge_color === 'pink' ? 'SELECTED' : '' }}>Pink</option>
					    	<option value="sky" {{ $editUser->badge_color === 'sky' ? 'SELECTED' : '' }}>Sky</option>
					    	<option value="red" {{ $editUser->badge_color === 'red' ? 'SELECTED' : '' }}>Red</option>
					    	<option value="purple" {{ $editUser->badge_color === 'purple' ? 'SELECTED' : '' }}>Purple</option>
					    	<option value="yellow" {{ $editUser->badge_color === 'yellow' ? 'SELECTED' : '' }}>Yellow</option>
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