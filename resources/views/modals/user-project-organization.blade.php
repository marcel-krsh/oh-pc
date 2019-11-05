<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
 --}}
@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

  <h2 class="uk-text-uppercase uk-text-emphasis">Add Another Organization</h2>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
  <div class="alert alert-danger uk-text-danger" style="display:none"></div>
  <form id="userForm" action="{{ route('user.add-organization-to-user', $user->id) }}" method="post" role="userForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="uk-grid">
      <div class="uk-width-1-2">
        <div class="uk-width-1-1">
          <label for="role">Organization : <br /></label>
          <select id="organization" name="organization" class="uk-width-1-1 uk-select">
            <option value="">Select Organization</option>
            @foreach($organizations as $org_id => $organization)
            <option value="{{ $org_id }}" >{{ $organization }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="uk-grid">
      <div class="uk-width-1-4">
        <a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
      </div>
      <div class="uk-width-1-4 ">
        <a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewOrganization()"><span uk-icon="save"></span> SAVE</a>
      </div>
    </div>
  </form>

<script type="text/javascript">
	$(document).ready(function() {
    $('#organization').select2();
	});
  function submitNewOrganization() {
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
    url: "{{ URL::route("user.add-organization-to-user", $user->id) }}",
    method: 'post',
    data: {
      organization_id: data['organization'],
      project_id: {{ $project_id }},
      '_token' : '{{ csrf_token() }}'
    },
    success: function(data){
      $('.alert-danger' ).empty();
      if(data == 1) {
        UIkit.modal.alert('I have added organization to user',{stack: true});
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
