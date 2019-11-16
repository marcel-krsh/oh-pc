@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

  <h2 class="uk-text-uppercase uk-text-emphasis">Create New User</h2>
  <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
  <div class="alert alert-danger" style="display:none"></div>

  <form id="userForm" action="{{ route('admin.createuser') }}" method="post" role="userForm">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="uk-grid">
      <div class="uk-width-1-2">
        <div class="uk-width-1-1">
          <label for="role">Role*: <br /></label>
          <select name="role" class="uk-width-1-1 uk-select">
            <option value="">Select Role</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" >{{ $role->role_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="uk-width-1-1 uk-margin-top form-group">
          <label for="name">First Name*:</label>
          <input type="text" class="uk-input uk-width-1-1 form-control" name="first_name" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Last Name*:</label>
          <input type="text" class="uk-input uk-width-1-1" name="last_name" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Email*:</label>
          <input type="text" class="uk-input uk-form-large uk-width-1-1" name="email" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Password*:</label>
          <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password" value="" placeholder="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Must have at least 6 characters' : ''); if(this.checkValidity()) form.password_confirmation.pattern = this.value;" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Confirm Password*:</label>
          <input type="password" class="uk-input uk-form-large uk-width-1-1" name="password_confirmation" value="" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');">
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Badge Color:</label>
          <select name="badge_color" class="uk-width-1-1 uk-select">
            <option value="blue">Select Badge</option>
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

      <div class="uk-width-1-2">
        <div class="uk-width-1-1">
          <label for="role">Organization: <br /></label>
          <select name="organization" class="uk-width-1-1 uk-select">
            <option value="">Select Organization</option>
            @foreach($organizations as $organization)
            <option value="{{ $organization->id }}" >{{ $organization->organization_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Phone Number:</label>
          <input type="number" class="uk-input uk-width-1-1" name="phone_number" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Address Line 1:</label>
          <input type="text" class="uk-input uk-width-1-1" name="address_line_1" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Address Line 2:</label>
          <input type="text" class="uk-input uk-width-1-1" name="address_line_2" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">City:</label>
          <input type="text" class="uk-input uk-width-1-1" name="city" value="" >
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="role">State: <br /></label>
          <select name="organization" class="uk-width-1-1 uk-select">
            <option value="">Select State</option>
            @foreach($states as $state)
            <option value="{{ $state->id }}" >{{ $state->state_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="uk-width-1-1 uk-margin-top">
          <label for="name">Zip:</label>
          <input type="number" class="uk-input uk-width-1-1" name="zip" value="" >
        </div>
      </div>
    </div>
    <div class="uk-grid">
      <div class="uk-width-1-4">
        <a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
      </div>
      <div class="uk-width-1-4 ">
        <button class="uk-button uk-width-1-1 uk-button uk-button-success" type="submit"><span uk-icon="save"></span> SAVE</button>
      </div>
    </div>
  </form>

<script type="text/javascript">

  $(document).on('submit', '#userForm', function(submission){
    submission.preventDefault();
    debugger;
    var form   = $(this),
    url    = form.attr('action'),
    submit = form.find('[type=submit]');
    var data        = form.serialize(),
    contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
    var submitOriginal = submit.html();
    submit.html('Please wait...');
    submit.attr("disabled", true);
  // Request.
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: 'json',
    cache: false,
    contentType: contentType,
    processData: false
    // Response.
  }).always(function(response, status) {
      // Reset errors.
      resetModalFormErrors();
      // Check for errors.
      if (response.status == 422) {
        var errors = $.parseJSON(response.responseText);
          // Iterate through errors object.
          $.each(errors, function(field, message) {
              //console.error(field+': '+message);
              var formGroup = $('[name='+field+']', form).closest('.form-group');
              formGroup.addClass('has-error').append('<span class="help-block">'+message+'</span>');
            });
          $.each(errors, function(field, message) {
              //console.error(field+': '+message);
              var formGroup = $('[name="'+field+'[]"]', form).closest('.form-group');
              formGroup.addClass('has-error').append('<span class="help-block">'+message+'</span>');
            });
          // Reset submit.
          if (submit.is('button')) {
            submit.html(submitOriginal);
            submit.attr("disabled", false);
          } else if (submit.is('input')) {
            submit.val(submitOriginal);
            submit.attr("disabled", false);
          }
        }
      });

  });













 //  function submitNewUser() {
 //   jQuery.ajaxSetup({
 //    headers: {
 //      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
 //    }
 //  });
 //   var form = $('#userForm');

 //   var data = { };
 //   $.each($('form').serializeArray(), function() {
 //    data[this.name] = this.value;
 //  });
 //   jQuery.ajax({
 //    url: "{{ URL::route("admin.createuser") }}",
 //    method: 'post',
 //    data: {
 //      first_name: data['first_name'],
 //      last_name: data['last_name'],
 //      role: data['role'],
 //      email: data['email'],
 //      password: data['password'],
 //      password_confirmation: data['password_confirmation'],
 //      badge_color: data['badge_color'],
 //      organization: data['organization'],
 //      phone_number: data['phone_number'],
 //      address_line_1: data['address_line_1'],
 //      address_line_2: data['address_line_2'],
 //      city: data['city'],
 //      state: data['state'],
 //      zip: data['zip'],
 //      '_token' : '{{ csrf_token() }}'
 //    },
 //    success: function(data){
 //      jQuery.each(data.errors, function(key, value){
 //        jQuery('.alert-danger').show();
 //        jQuery('.alert-danger').append('<p>'+value+'</p>');
 //      });
 //    }
 //  });
 // }

 function resetModalFormErrors() {
  $('.form-group').removeClass('has-error');
  $('.form-group').find('.help-block').remove();
}

// function submitNewUserold() {
//   var form = $('#userForm');

//   var data = { };
//   $.each($('form').serializeArray(), function() {
//     data[this.name] = this.value;
//   });
//   $.ajax({
//     type: "POST",
//     url: "{{ URL::route("admin.createuser") }}",
//     headers: {
//       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     },
//     dataType: 'JSON',
//     data: {
//       'first_name': data['first_name'],
//       'last_name': data['last_name'],
//       'role': data['role'],
//       'email': data['email'],
//       'password': data['password'],
//       'password_confirmation': data['password_confirmation'],
//       'badge_color': data['badge_color'],
//       'organization': data['organization'],
//       'phone_number': data['phone_number'],
//       'address_line_1': data['address_line_1'],
//       'address_line_2': data['address_line_2'],
//       'city': data['city'],
//       'state': data['state'],
//       'zip': data['zip'],
//       '_token' : '{{ csrf_token() }}'
//     },
//     success: function (returnval) {
//       $('#users-tab').trigger("click");
//       dynamicModalClose();
//       UIkit.modal.alert('New user has been saved.');
//     },
//     error: function (xhr, ajaxOptions, thrownError) {
//       alert(xhr.status);
//       alert(thrownError);
//     }
//   });

//     $('#users-tab').trigger("click");
//     dynamicModalClose();
    // $.post('{{ URL::route("admin.createuser") }}', {
    //   'first_name': data['first_name'],
    //   'last_name': data['last_name'],
    //   'role': data['role'],
    //   'email': data['email'],
    //   'password': data['password'],
    //   'password_confirmation': data['password_confirmation'],
    //   'badge_color': data['badge_color'],
    //   'organization': data['organization'],
    //   'phone_number': data['phone_number'],
    //   'address_line_1': data['address_line_1'],
    //   'address_line_2': data['address_line_2'],
    //   'city': data['city'],
    //   'state': data['state'],
    //   'zip': data['zip'],
    //   '_token' : '{{ csrf_token() }}'
    // }, function(response) {
    //   if(response != 1){
    //     UIkit.modal.alert(response);
    //   } else {
    //     $('#users-tab').trigger("click");
    //     dynamicModalClose();
    //     UIkit.modal.alert('New user has been saved.');
    //   }
    // } );
  // }

</script>
