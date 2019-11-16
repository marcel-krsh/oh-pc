@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

	@if(is_null($uo->organization->organization_key))
  <h2 class="uk-text-uppercase uk-text-emphasis">Edit/Remove User Organization</h2>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
  <form id="userForm" action="{{ route('user.edit-organization-of-user', $uo->organization_id) }}" method="post" role="userForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="uk-grid">
    	<div class="uk-width-1-1 uk-margin-top">
	      <label for="organization">Organization Name<span class="uk-text-danger uk-text-bold">*</span> :</label>
        <input type="text" name="organization_name" class="uk-input uk-width-1-1" placeholder="Organization Name" value="{{ $uo->organization->organization_name }}">
          <div class="alert alert-danger uk-text-danger" style="display:none"></div>
	    </div>
    </div>
    <div class="uk-grid">
      <div class="uk-width-1-4">
        <a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
      </div>
      <div class="uk-width-1-4 ">
        <a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitEditOrganization()"><span uk-icon="save"></span> SAVE</a>
      </div>
    </div>
  </form>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
  @endif
  <h2 class="uk-text-uppercase uk-text-emphasis">Remove User Organization</h2>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> <br>
  <div class="uk-grid">
    <div class="uk-width-1-4">
      <a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
    </div>
    <div class="uk-width-1-4 ">
      <a class="uk-button uk-width-1-1 uk-button uk-button-danger" onclick="submitRemoveOrganization()"><span uk-icon="save"></span> REMOVE</a>
    </div>
  </div>


<script type="text/javascript">

  function submitEditOrganization() {
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
    url: "{{ URL::route("user.edit-organization-of-user", $uo->organization_id) }}",
    method: 'post',
    data: {
      organization_name: data['organization_name'],
      organization_id : {{ $uo->organization_id }},
      project_id: {{ $project_id }},
      '_token' : '{{ csrf_token() }}'
    },
    success: function(data){
      $('.alert-danger' ).empty();
      if(data == 1) {
        UIkit.modal.alert('I have edited organization name',{stack: true});
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

 function submitRemoveOrganization() {
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
    url: "{{ URL::route("user.remove-organization-of-user", $uo->organization_id) }}",
    method: 'post',
    data: {
      project_id: {{ $project_id }},
      organization_id : {{ $uo->id }},
      '_token' : '{{ csrf_token() }}'
    },
    success: function(data){
      $('.alert-danger' ).empty();
      if(data == 1) {
        UIkit.modal.alert('I have removed organization from user',{stack: true});
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
