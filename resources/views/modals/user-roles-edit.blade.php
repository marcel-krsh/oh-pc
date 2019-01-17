@if($user)
<div class="modal-user-roles">
    <div class="">
	    <div uk-grid> 
	  		<div class="uk-padding-remove uk-margin-small-top">
	  			<div uk-grid> 
	  				<div class="uk-width-1-1 uk-padding-remove-left">
			  			<h3>{{$user->person->first_name}} {{$user->person->last_name}}</h3>
					</div>

	  				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left">
	  					<hr />
	  					<h3 class="uk-margin-small-top">Roles</h3>
	  					<div class="uk-grid-small uk-margin-remove" uk-grid>
	  						<form id="user-roles-form" method="post" class="uk-width-1-1 uk-margin-bottom">
	  							<div class="uk-grid-small" uk-grid>
	  								<div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
	  									@if($roles)
	  									@foreach($roles as $role)
							            <label><input class="uk-checkbox" type="checkbox" name="roles[]" value="{{$role->id}}" @if($user->hasRole($role->id)) checked @endif> {{$role->role_name}}</label>
							            @endforeach
							            @endif
							        </div>
			  						<div class="uk-width-1-1 uk-margin-small-top">
			  							<button class="uk-button uk-button-primary" style="height: 100%; width: 100%;" onclick="saveRoles(event);">SAVE</button>	
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
	            UIkit.notification('<span uk-icon="icon: check"></span> Roles Saved', {pos:'top-right', timeout:1000, status:'success'});
	            $('#users-tab').trigger('click');
            
            }
        } );
	}

 </script>
 @else
 <h2>No User Loaded..</h2>
 @endif

