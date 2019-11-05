@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

  <h2 class="uk-text-uppercase uk-text-emphasis">Edit User Name</h2>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
  <div class="alert alert-danger uk-text-danger" style="display:none"></div>
  <form id="userForm" action="{{ route('user.edit-name-of-user', $user->id) }}" method="post" role="userForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="uk-grid">
      <div class="uk-width-1-2">
        <div class="uk-width-1-1">
          <label for="role">Name : <br /></label>
          <input type="text" name="user_name" class="uk-input uk-width-1-1" placeholder="User Name" value="{{ $user->name }}">
        </div>
      </div>
    </div>
    <div class="uk-grid">
      <div class="uk-width-1-4">
        <a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
      </div>
      <div class="uk-width-1-4 ">
        <a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitUserName()"><span uk-icon="save"></span> SAVE</a>
      </div>
    </div>
  </form>

<script type="text/javascript">
	$(document).ready(function() {
    $('#organization').select2();
	});

  function submitUserName() {
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
    url: "{{ URL::route("user.edit-name-of-user", $user->id) }}",
    method: 'post',
    data: {
      user_id: {{ $user->id }},
      user_name: data['user_name'],
      '_token' : '{{ csrf_token() }}'
    },
    success: function(data){
      $('.alert-danger' ).empty();
      if(data == 1) {
        UIkit.modal.alert('I have changed user name',{stack: true});
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
