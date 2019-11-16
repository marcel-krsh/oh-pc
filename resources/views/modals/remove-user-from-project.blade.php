@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h2 class="uk-text-uppercase uk-text-emphasis">Remove user from the project</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('project.remove-user', $project_id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<h4>{{ $message }}</h4>
    </div>
    <div class="uk-grid">
    	<div class="uk-width-1-4">
    		<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
    	</div>
    	@if($status)
    	<div class="uk-width-1-4 ">
    		<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitRemoveUser()"><span uk-icon="save"></span> REMOVE</a>
    	</div>
    	@endif
    </div>
  </form>



  <script type="text/javascript">

  	function submitRemoveUser() {
  		jQuery.ajaxSetup({
  			headers: {
  				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
  			}
  		});
  		var form = $('#userForm');
  		var data = { };

  		jQuery.ajax({
  			url: "{{ URL::route("project.remove-user", $project_id) }}",
  			method: 'post',
  			data: {
  				user_id: {{ $user_id }},
  				project_id: {{ $project_id }},
  				'_token' : '{{ csrf_token() }}'
  			},
  			success: function(data){
  				$('.alert-danger' ).empty();
  				if(data == 1) {
  					UIkit.modal.alert('I removed the user from project.',{stack: true});
  					dynamicModalClose();
		    		loadTab('/project/'+{{$project_id}}+'/contacts/', '7', 0, 0, 'project-', 1);
  				}
  				jQuery.each(data.errors, function(key, value){
  					jQuery('.alert-danger').show();
  					jQuery('.alert-danger').append('<p>'+value+'</p>');
  				});
  			}
  		});
  	}

  </script>
