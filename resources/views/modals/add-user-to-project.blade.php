@if (count($errors) > 0)
<div class="uk-panel uk-margin-top uk-margin-bottom">
	<ul>
		@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<div id="dynamic-modal-content">
	<h2 class="uk-text-uppercase uk-text-emphasis">Add user to the project</h2>
  <small class="uk-margin-top uk-margin-bottom">PRESS CONTROL + F TO SEARCH NAMES IN USER LIST</small>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<form id="userForm" action="{{ route('project.add-user', $project_id) }}" method="post" role="userForm">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="uk-grid">
			<div class="uk-width-4-5 "  id="recipients-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
				
				<div id='recipient-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox recipient-selector"><span class=
					'recipient-name'></span>
				</div>
			</div>

			<div class="uk-width-1-5 recipient-list" style="display: none;"></div>
      
			<div class="uk-width-4-5 recipient-list" id='recipients' style="border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:18px; padding-left:25px; position: relative;top:0px;">
				<!-- RECIPIENT LISTING -->
				<div class="communication-selector uk-scrollable-box">
					<ul class="uk-list document-menu">
						@php $currentOrg = ''; @endphp
						@foreach ($recipients as $recipient)
						@if($currentOrg != $recipient->organization_name)
						<li class="recipient-list-item {{strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name)))))}}"><strong>{{$recipient->organization_name}}</strong></li>
						<hr class="recipient-list-item dashed-hr uk-margin-bottom">
						@php $currentOrg = $recipient->organization_name; @endphp
						@endIf
						<li class="recipient-list-item {{strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name)))))}} {{ strtolower($recipient->first_name) }} {{ strtolower($recipient->last_name) }}">
							<input name="" id="list-recipient-id-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipient(this.value,'ID: {{ $recipient->id }} | NAME: {{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}')">
							<label for="recipient-id-{{ $recipient->id }}">
							ID: {{ $recipient->id }} | NAME: {{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }} | {{ ucwords($recipient->email) }}
							</label>
						</li>
						@endforeach
					</ul>
				</div>
				{{-- <div class="uk-form-row">
					<input style="width: 100%" type="text" id="recipient-filter" class="uk-input uk-width-1-1" placeholder="Filter Users">
				</div> --}}
				<script>
          // CLONE RECIPIENTS
          function addRecipient(formValue,name){
            //alert(formValue+' '+name);
            if($("#list-recipient-id-"+formValue).is(':checked')){
            	var recipientClone = $('#recipient-template').clone();
            	recipientClone.attr("id", "recipient-id-"+formValue+"-holder");
            	recipientClone.prependTo('#recipients-box');

            	$("#recipient-id-"+formValue+"-holder").slideDown();
            	$("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("id","recipient-id-"+formValue);
            	$("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("name","recipients[]");
            	$("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeRecipient("+formValue+");");

            	$("#recipient-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
            	$("#recipient-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
            } else {
            	$("#recipient-id-"+formValue+"-holder").slideUp();
            	$("#recipient-id-"+formValue+"-holder").remove();
            }
          }
          function removeRecipient(id){
          	$("#recipient-id-"+id+"-holder").slideUp();
          	$("#recipient-id-"+id+"-holder").remove();
          	$("#list-recipient-id-"+id).prop("checked",false)
          }
        </script>
        <!-- END RECIPIENT LISTING -->
      </div>
    </div>
    <div class="uk-grid">
    	<div class="uk-width-1-4">
    		<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
    	</div>
    	<div class="uk-width-1-4 ">
    		<a class="uk-button uk-width-1-1 uk-button uk-button-success" onclick="submitNewUser()"><span uk-icon="save"></span> SAVE</a>
    	</div>
    </div>
  </form>
</div>


<script type="text/javascript">
    // filter recipients based on class
    $('#recipient-filter').on('keyup', function () {
    	var searchString = $(this).val().toLowerCase();
    	if(searchString.length > 0){
    		$('.recipient-list-item').hide();
    		$('.recipient-list-item[class*="' + searchString + '"]').show();
    	}else{
    		$('.recipient-list-item').show();
    	}
    });

    function showRecipients() {
    	$('.recipient-list').slideToggle();
    	$('#add-recipients-button').toggle();
    	$('#done-adding-recipients-button').toggle();
    }
  </script>
  <script type="text/javascript">

  	function submitNewUser() {
  		jQuery.ajaxSetup({
  			headers: {
  				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
  			}
  		});
  		var form = $('#userForm');
  		var data = { };
  		var recipients_array = [];
  		$.each($('form').serializeArray(), function() {
  			data[this.name] = this.value;
  		});
  		$("input[name='recipients[]']:checked").each(function (){
  			recipients_array.push(parseInt($(this).val()));
  		});
  		if(recipients_array.length === 0){
  			no_alert = 0;
  			UIkit.modal.alert('You must select a user.',{stack: true});
  		}
  		jQuery.ajax({
  			url: "{{ URL::route("project.add-user", $project_id) }}",
  			method: 'post',
  			data: {
  				recipients_array: recipients_array,
  				'_token' : '{{ csrf_token() }}'
  			},
  			success: function(data){
  				$('.alert-danger' ).empty();
  				if(data == 1) {
  					UIkit.modal.alert('I added the user(s) to the project',{stack: true});
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
