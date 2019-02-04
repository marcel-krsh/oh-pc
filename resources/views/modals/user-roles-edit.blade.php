@if($user)
<div class="modal-user-roles">
    <div class="">
	    <div uk-grid> 
	  		<div class="uk-padding-remove uk-margin-small-top">
	  			<div uk-grid> 
	  				<div class="uk-width-1-1 uk-padding-remove-left">
			  			<h3 uk-tooltip title="DEV|CO KEY: {{$user->devco_key}}<br />ALLITA ID: {{$user->id}}<br />PERSON ID:{{$user->person_id}}">{{$user->full_name()}}</h3>
					</div>
					

	  				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left">
	  					<hr />
	  					<h3 class="uk-margin-small-top">Roles</h3>
	  					<div class="uk-grid-small uk-margin-remove" uk-grid>
	  						<form id="user-roles-form" method="post" class="uk-width-1-1 uk-margin-bottom">
	  							<div class="uk-grid-small" uk-grid>
	  								<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
	  									
	  									@if($roles)
	  									@php $checked=0; @endphp
	  									@foreach($roles as $role)
							            <label><input class="uk-radio" type="radio" name="roles" value="{{$role->id}}" @if($user->hasRole($role->id)) @php $checked = 1; @endphp checked @endif> {{$role->role_name}}</label>
							            @endforeach
							            <label><input class="uk-radio" type="radio" name="roles" value="0" @if($checked != 1) checked @endif> No Access</label>
							            @endif
							        </div>
							    </div>
							        <div class="uk-width-1-1 uk-margin">
							        	<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
								        	<input type="checkbox" value="1" name="allowed_tablet" @if($user->allowed_tablet == 1) checked @endif  onClick="if($('input[type=checkbox]').prop('checked')) { $('#tablet-settings').slideDown(); }"><label for="allowed_tablet">Allow Tablet Access</label>
		  									<div id="tablet-settings" style=" @if($user->allowed_tablet != 1) display:none @endif"> 
			  								
				  								<hr class="dashed-hr">

				  								<div class="uk-input uk-form-blank"> API KEY: 
				  									<a class="uk-mute" onClick="getNewApiKey()" id="api-key">@if($user->api_key == '' || is_null($user->api_key)) REQUEST API KEY @else <span uk-tooltip title="Click to Request a New API Key"> $user->api_key </span> @endIf</a>
										        </div>
									    	</div>

									    </div>
									</div>
			  						<div class="uk-width-1-1 uk-margin-small-top">
			  							<button class="uk-button uk-button-primary" style=" width: 100%;" onclick="saveRoles(event);">SAVE</button>	
			  						</div>
			  					</div>
		  					</form>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	    </div>
	</div>
</div>

 <script>

	function saveRoles(e){
		e.preventDefault();
		var form = $('#user-roles-form');

		$.post("/admin/users/{{$user->id}}/saveroles", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){ 
                UIkit.modal.alert(data,{stack: true});
            } else {
                dynamicModalClose();
	            UIkit.notification('<span uk-icon="icon: check"></span> User Settings Saved', {pos:'top-right', timeout:1000, status:'success'});
	            $('#users-tab').trigger('click');
            
            }
        } );
	}

 </script>
 @else
 <h2>No User Loaded..</h2>
 @endif

