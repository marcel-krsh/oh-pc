@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif

	<h2 class="uk-text-uppercase uk-text-emphasis">Edit User Phone Number</h2>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('user.add-phone-to-user', $user->id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="uk-width-1-1 uk-margin-top">
          <label for="name">Business Phone Number :</label><br>
          <input id="business_phone_number" type="text" class="uk-input uk-width-1-3" name="business_phone_number" placeholder="Format: xxx-xxx-xxxx">
          <input id="phone_extension" type="number" class="uk-input uk-width-1-3" name="phone_extension" placeholder="xxxx">
        </div>
			</div>
		</div>
		<div class="uk-grid">
			<div class="uk-width-1-4">
				<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
			</div>
			<div class="uk-width-1-4 ">
				<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewPhone()"><span uk-icon="save"></span> SAVE</a>
			</div>
		</div>
	</form>

<script type="text/javascript">

	function submitNewPhone() {
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
			url: "{{ URL::route("user.add-phone-to-user", $user->id) }}",
			method: 'post',
			data: {
				user_id: {{ $user->id }},
				project_id: {{ $project_id }},
				business_phone_number: data['business_phone_number'],
				phone_extension: data['phone_extension'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					UIkit.modal.alert('I have added phone number to user',{stack: true});
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


  function phone_formatting(ele,restore) {
    var new_number,
    selection_start = ele.selectionStart,
    selection_end = ele.selectionEnd,
    number = ele.value.replace(/\D/g,'');
    // automatically add dashes
    if (number.length > 2) {
      // matches: 123 || 123-4 || 123-45
      new_number = number.substring(0,3) + '-';
      if (number.length === 4 || number.length === 5) {
        // matches: 123-4 || 123-45
        new_number += number.substr(3);
      }
      else if (number.length > 5) {
        // matches: 123-456 || 123-456-7 || 123-456-789
        new_number += number.substring(3,6) + '-';
      }
      if (number.length > 6) {
        // matches: 123-456-7 || 123-456-789 || 123-456-7890
        new_number += number.substring(6);
      }
    }
    else {
      new_number = number;
    }

    // if value is heigher than 12, last number is dropped
    // if inserting a number before the last character, numbers
    // are shifted right, only 12 characters will show
    ele.value =  (new_number.length > 12) ? new_number.substring(0,12) : new_number;

    // restore cursor selection,
    // prevent it from going to the end
    // UNLESS
    // cursor was at the end AND a dash was added

    if (new_number.slice(-1) === '-' && restore === false && (new_number.length === 8 && selection_end === 7) || (new_number.length === 4 && selection_end === 3)) {
      selection_start = new_number.length;
      selection_end = new_number.length;
    }
    else if (restore === 'revert') {
      selection_start--;
      selection_end--;
    }
    ele.setSelectionRange(selection_start, selection_end);
  }

  function business_phone_number_check(field,e) {
    var key_code = e.keyCode,
    key_string = String.fromCharCode(key_code),
    press_delete = false,
    dash_key = 189,
    delete_key = [8,46],
    direction_key = [33,34,35,36,37,38,39,40],
    selection_end = field.selectionEnd;

    // delete key was pressed
    if (delete_key.indexOf(key_code) > -1) {
      press_delete = true;
    }

    // only force formatting is a number or delete key was pressed
    if (key_string.match(/^\d+$/) || press_delete) {
      phone_formatting(field,press_delete);
    }
    // do nothing for direction keys, keep their default actions
    else if(direction_key.indexOf(key_code) > -1) {
      // do nothing
    }
    else if(dash_key === key_code) {
      if (selection_end === field.value.length) {
        field.value = field.value.slice(0,-1)
      }
      else {
        field.value = field.value.substring(0,(selection_end - 1)) + field.value.substr(selection_end)
        field.selectionEnd = selection_end - 1;
      }
    }
    // all other non numerical key presses, remove their value
    else {
      e.preventDefault();
      //    field.value = field.value.replace(/[^0-9\-]/g,'')
      phone_formatting(field,'revert');
    }
  }

  document.getElementById('business_phone_number').onkeyup = function(e) {
    business_phone_number_check(this,e);
  }

 

</script>
