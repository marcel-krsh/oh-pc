@php
	$submit_name = 'submitNewDraftCommunication'. rand();
@endphp
@if(session()->has('old_communication_modal'))
<script>
	window.oldCommunicationModal = "{{ session()->get('old_communication_modal') }}";
	$( document ).ready(function() {
		var modal = UIkit.modal('#dynamic-modal', {
			escClose: false,
			bgClose: false
		});

	});
</script>
@else
<script>
	window.oldCommunicationModal = "";
</script>
@endif
@php
$random = rand();
session(['old_communication_modal' => $random]);
@endphp
<div id="communication-modal-{{ $random }}">
	<script>
		resizeModal(80);
		window.currentCommunicationModal = "{{ $random }}";
	</script>

	<form name="newOutboundEmailFormDraft" id="newOutboundEmailFormDraft" method="post">
		@if(!is_null($project))<input type="hidden" name="project_id" value="{{ $project->id }}">@endif
		@if(!is_null($audit))<input type="hidden" name="audit" value="{{ $audit->id }}">@endif
		<div class="uk-container uk-container-center"> <!-- start form container -->
			<div uk-grid class="uk-grid-small ">
				<div class="uk-width-1-1 uk-padding-small">
					@if(!is_null($project))
					@if(!is_null($audit))
					@if(!is_null($finding))
					<h3>Message for Project: <span id="current-file-id-dynamic-modal">{{ $project->project_number }} : {{ $project->project_name }} | Audit: {{ $audit->id }} Findings Response</span></h3>
					@else
					<h3>Message for Project: <span id="current-file-id-dynamic-modal">{{ $project->project_number }} : {{ $project->project_name }} | Audit: {{ $audit->id }}</span></h3>
					@endIf
					@else
					<h3>Message for Project: <span id="current-file-id-dynamic-modal">{{ $project->project_number }} : {{ $project->project_name }}</span></h3>
					@endIf
					@else
					<h3>New Message</h3>
					@endif
				</div>
			</div>
			<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
			<div uk-grid class="uk-grid-collapse">
				<div class="uk-width-1-5 " style="padding:18px;"><div style="width:25px; display: inline-block;"><i uk-icon="user"></i></div> &nbsp;FROM:</div>
				<div class="uk-width-4-5 " style="border-bottom:1px #111 dashed; padding:18px; padding-left:27px;">{{ Auth::user()->full_name() }}</div>
				<div class="uk-width-1-5 " style="padding:18px;"><div style="width:25px;display: inline-block;"><i uk-icon="users" class=""></i></div> &nbsp;TO: </div>
				@if($single_recipient)
				<?php $recipient = $recipients->first()->first();?>
				<div class="uk-width-4-5 "  id="recipients-box-draft" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
					<li class="recipient-list-item {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }} {{ strtolower($recipient->first_name) }} {{ strtolower($recipient->last_name) }}">
						<input name="recipientsdraft[]" id="recipient-id-draft-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" checked="checked" onclick="return false;">
						<label for="recipient-id-draft-{{ $recipient->id }}">
							{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}
						</label>
					</li>
				</div>
				@else
				<div class="uk-width-4-5 "  id="recipients-box-draft" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
					<div id="add-recipients-button-draft" class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showRecipientsDraft()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD RECIPIENT</div><div id="done-adding-recipients-button-draft" class="uk-button uk-button-success uk-button-small" style="padding-top: 2px; display: none;" onClick="showRecipientsDraft()"><i class="a-circle-cross"></i> &nbsp;DONE ADDING RECIPIENTS</div>
					<div id='recipient-template-draft' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox recipient-selector"><span class=
						'recipient-name'></span>
					</div>
				</div>
				<div class="uk-width-1-5 recipient-list" style="display: none;"></div>
				<div class="uk-width-4-5 recipient-list" id='recipients' style="border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:18px; padding-left:25px; position: relative;top:0px; display: none">
					<!-- RECIPIENT LISTING -->
					<div class="communication-selector uk-scrollable-box">
						<ul class="uk-list document-menu">
							@if(count($recipients_from_hfa) > 0)
							<li class="recipient-list-item ohfa "><strong>OHFA STAFF</strong></li>
							<hr class="recipient-list-item dashed-hr uk-margin-bottom">
							@foreach ($recipients_from_hfa as $recipient_from_hfa)
							<li class="recipient-list-item ohfa {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient_from_hfa->organization_name))))) }} {{ strtolower($recipient_from_hfa->first_name) }} {{ strtolower($recipient_from_hfa->last_name) }}">
								<input name="" id="list-recipient-id-draft-{{ $recipient_from_hfa->id }}" value="{{ $recipient_from_hfa->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipientDraft(this.value,'{{ ucwords($recipient_from_hfa->first_name) }} {{ ucwords($recipient_from_hfa->last_name) }}')">
								<label for="recipient-id-draft-{{ $recipient_from_hfa->id }}">
									{{ ucwords($recipient_from_hfa->first_name) }} {{ ucwords($recipient_from_hfa->last_name) }} | {{ $recipient_from_hfa->email }}
								</label>
							</li>
							@endforeach
							@endif
							{{-- @php $currentOrg = ''; @endphp --}}
							@foreach ($recipients as $key => $orgs)
							<hr class="recipient-list-item dashed-hr uk-margin-bottom">
							<li class="recipient-list-item  {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$key))))) }}"><strong>{{ $key }}</strong>
							</li>

							@foreach($orgs as $recipient)
							<li class="recipient-list-item {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }} {{ strtolower($recipient->first_name) }} {{ strtolower($recipient->last_name) }}">
								<input name="" id="list-recipient-id-draft-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipientDraft(this.value,'{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}')">
								<label for="recipient-id-draft-{{ $recipient->id }}">
									{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }} | {{ $recipient->email }}
								</label>
							</li>
							@endforeach
							<hr class="recipient-list-item dashed-hr uk-margin-bottom">
							{{--  @if($currentOrg != $recipient->organization_name)
							<li class="recipient-list-item @if(count($recipients_from_hfa) > 0 || $currentOrg != '') uk-margin-large-top @endif {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }}"><strong>{{ $recipient->organization_name }}</strong></li>
							<hr class="recipient-list-item dashed-hr uk-margin-bottom">
							@php $currentOrg = $recipient->organization_name; @endphp
							@endIf
							<li class="recipient-list-item {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }} {{ strtolower($recipient->first_name) }} {{ strtolower($recipient->last_name) }}">
								<input name="" id="list-recipient-id-draft-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipientDraft(this.value,'{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}')">
								<label for="recipient-id-draft-{{ $recipient->id }}">
									{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}
								</label>
							</li>
							--}}
							@endforeach
							<hr class="recipient-list-item dashed-hr uk-margin-bottom">
						</ul>
					</div>
					{{-- <div class="uk-form-row">
						<input style="width: 100%" type="text" id="recipient-filter" class="uk-input uk-width-1-1" placeholder="Filter Recipients">
					</div> --}}
					<script>
            // CLONE RECIPIENTS
            function addRecipientDraft(formValue,name){
              //alert(formValue+' '+name);
              if($("#list-recipient-id-draft-"+formValue).is(':checked')){
              	var recipientClone = $('#recipient-template-draft').clone();
              	recipientClone.attr("id", "recipient-id-draft-"+formValue+"-holder");
              	recipientClone.prependTo('#recipients-box-draft');

              	$("#recipient-id-draft-"+formValue+"-holder").slideDown();
              	$("#recipient-id-draft-"+formValue+"-holder input[type=checkbox]").attr("id","recipient-id-draft-"+formValue);
              	$("#recipient-id-draft-"+formValue+"-holder input[type=checkbox]").attr("name","recipientsdraft[]");
              	$("#recipient-id-draft-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeRecipient("+formValue+");");

              	$("#recipient-id-draft-"+formValue+"-holder input[type=checkbox]").val(formValue);
              	$("#recipient-id-draft-"+formValue+"-holder span").html('&nbsp; '+name+' ');
              } else {
              	$("#recipient-id-draft-"+formValue+"-holder").slideUp();
              	$("#recipient-id-draft-"+formValue+"-holder").remove();
              }
            }
            function removeRecipient(id){
            	$("#recipient-id-draft-"+id+"-holder").slideUp();
            	$("#recipient-id-draft-"+id+"-holder").remove();
            	$("#list-recipient-id-draft-"+id).prop("checked",false)
            }
          </script>
          <!-- END RECIPIENT LISTING -->
        </div>
        @endif
        @if(null !== $findings && count($findings)>0 && ($all_findings > 0 || !empty($all_findings)))
        <div class="uk-width-1-5 " style="padding:18px;">
        	<div style="width:25px;display: inline-block;"><i uk-icon="users" class=""></i></div> &nbsp;FINDINGS:
        </div>
        <div class="uk-width-4-5 "  id="findings-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
        	@foreach(json_decode($all_findings) as $finding_id)
        	Finding-{{ $finding_id }}{{ $loop->last ? '':', ' }}
        	@endforeach
        </div>
        @endif

        @if(!is_null($draft->documents))
        <div class="uk-width-1-5 " style="padding:18px;">
        	<div style="width:25px;display: inline-block;"><i uk-icon="a-paperclip-2" class=""></i></div> &nbsp;DOCUMENTS:
        </div>
        <div class="uk-width-4-5 "  id="findings-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
        	@foreach($draft->getSelectedDocuments() as $document) {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} <br>
        	@endforeach
        </div>
        @endif

        <div class="uk-width-1-5 " style="padding:18px;padding-top:27px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;SUBJECT:</div>
        <div class="uk-width-4-5"  style="padding:18px; border-bottom:1px #111 dashed;">
        	<fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
        		<div uk-grid class="uk-grid-collapse">
        			<div class="uk-width-1-1">
        				<input type="text" name="subject" class="uk-width-1-1 uk-input uk-form-large uk-form-blank" placeholder="Recipients will see your subject in their notifications." id="findings_based_subject" value="{{ $draft->subject }}">
        			</div>
        		</div>
        	</fieldset>
        </div>
        <div class="uk-width-1-5 " style="padding:18px; padding-top:40px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;MESSAGE:</div>
        <div class="uk-width-4-5 " style="padding:18px;">
        	<fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
        		<div uk-grid class="uk-grid-collapse">
        			<div class="uk-width-1-1">
        				<textarea id="message-body" style="min-height: 100px;padding-left: 10px; border:none;" rows="11" class="uk-width-1-1 uk-form-large uk-input uk-form-blank uk-resize-vertical" name="messageBody" value="" placeholder="Recipients will have to log-in to view your message.">{{ $draft->message }}</textarea>
        			</div>
        		</div>
        	</fieldset>
        </div>
        <hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-margin-top">
        <div class="uk-width-1-3">&nbsp;</div>
        <div class="uk-width-1-3"><a class="uk-width-5-6 uk-button uk-align-right " onclick="dynamicModalCommunicationClose();"><i class="a-circle-cross"></i> CANCEL</a></div>
        <div class="uk-width-1-3"><a class="uk-width-5-6 uk-align-right uk-button uk-button-success" onclick="{{ $submit_name }}()"><i class="a-paper-plane"></i> SEND</a>
        </div>
      </div>
    </div>
  </form>



{{-- </div></div></div></div> --}}

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

    function showRecipientsDraft() {
    	$('.recipient-list').slideToggle();
    	$('#add-recipients-button-draft').toggle();
    	$('#done-adding-recipients-button-draft').toggle();
    }

    function showDocuments() {
    	$('.documents-list').slideToggle();
    	$('#add-documents-button').toggle();
    	$('#done-adding-documents-button').toggle();
    }

    function {{ $submit_name }}() {
    	var form = $('#newOutboundEmailFormDraft');
    	var no_alert = 1;
    	var recipients_array = [];
    	$("input[name='recipientsdraft[]']:checked").each(function (){
    		recipients_array.push(parseInt($(this).val()));
    	});
    	var inputsMain = form.serializeArray();
    	var error = '';
    	$.each( inputsMain,function(index, value) {
    		if(value['name'] == 'subject') {
    			if(value['value'] == ""){
    				error = error + 'Subject cannot be empty. ';
		    	}
    		}
    		if(value['name'] == 'messageBody') {
    			if(value['value'] == ""){
    				error = error + 'Message cannot be empty. ';
		    	}
    		}
    	});
    	if(error != ''){
    		if(recipients_array.length === 0){
	    		error = error + 'You must select a recipient.';
	    	}
    		no_alert = 0;
    		UIkit.modal.alert(error,{stack: true});
    	} else if(recipients_array.length === 0){
    		no_alert = 0;
    		UIkit.modal.alert('You must select a recipient.',{stack: true});
    	}
    	if(no_alert){
    		$.post('{{ URL::route("communication.draft-save", $draft->id) }}', {
    			'inputs' : form.serialize(),
    			'draft_id' : "{{ $draft->id }}",
    			'_token' : '{{ csrf_token() }}'
    		}, function(data) {
    			if(data!=1){
    				UIkit.modal.alert(data,{stack: true});
    			} else {
    				$('#user-draft-messages').trigger('click');
    				UIkit.modal.alert('Your message has been saved.');
    			}
    		} );

    		@if($project && Auth::user()->can('access_auditor') && $location == 'projects')
    		var id = {{ $project->id }};
    		loadTab('/projects/'+{{ $project->id }}+'/communications/', '2', 0, 0, 'project-', 1);
        //loadParcelSubTab('communications',id);
        @else
        //loadDashBoardSubTab('dashboard','communications');
        @endif
        dynamicModalCommunicationClose();
      }
    }

    function updateMessage() {
    	@if($audit)
    	var audit = "{{ $audit->id }}";
    	var projectNumber = "{{ $project->project_number }}";
    	var projectName = "{{ $project->project_name }}";
    	var findings_array = [];
    	$("input[name='findings[]']:checked").each(function (){
    		findings_array.push(parseInt($(this).val()));
    	});
    	if(findings_array.length > 1) {
    		subject = 'Findings: '+findings_array.join(", ");
    		message = 'Owner response for findings '+findings_array.join(", ")+' on audit # '+audit+' for '+projectNumber+' : '+projectName;
    	} else if(findings_array.length == 1) {
    		subject = 'Finding: '+findings_array.toString();
    		message = 'Owner response for finding '+findings_array.toString()+' on audit # '+audit+' for '+projectNumber+' : '+projectName;
    	} else {
    		subject = '';
    		message = '';
    	}
    	$('#findings_based_subject').attr('value', subject);
    	$('textarea#message-body').val(message);
    	// debugger;
    	@endIf
    }
  </script>
  <script>
  	function communicationClose() {
  		UIkit.modal.confirm("Are you sure you want to cancel this message? Your message and any documents you uploaded will be deleted.", {center: true,  keyboard:false,  stack:true}).then(function() {
  			window.communicationActive = 0;
  			$.post("/commmunication-draft/{{$draft->id}}/delete", {
  				'_token' : '{{ csrf_token() }}'
  			}, function(data) {
  				if(data!=1){
  					UIkit.modal.alert('Not able to delete draft, contact admin',{stack: true});
  					dynamicModalCommunicationClose();
  				} else {
  					UIkit.notification('<span uk-icon="icon: check"></span> Successfully deleted draft.', {pos:'top-right', timeout:1000, status:'success'});
  					dynamicModalCommunicationClose();
  				}
  			});
  		}, function () {
  			console.log('Rejected.')
  		});
  	}

  	function dynamicModalCommunicationClose() {
  		UIkit.modal('#dynamic-modal').hide();
  	}
  </script>
  <script>
  	// function updateCommunicationDraft() {
  	// 	if(window.communicationActive) {
  	// 		var form = $('#newOutboundEmailFormDraft');
  	// 		var no_alert = 1;
  	// 		var recipients_array = [];
  	// 		$("input[name='recipientsdraft[]']:checked").each(function (){
  	// 			recipients_array.push(parseInt($(this).val()));
  	// 		});
  	// 		$.post('{{ URL::route("communication.update-draft", $draft->id) }}', {
  	// 			'inputs' : form.serialize(),
  	// 			'_token' : '{{ csrf_token() }}'
  	// 		}, function(data) {
  	// 			if(data==1){
  	// 				console.log( "updated draft!" );
  	// 			} else {
  	// 				console.log( "updated NOT draft!" );
  	// 			}
  	// 		});
  	// 	}
  	// }



  	// $( document ).ready(function() {
  	// 	window.communicationActive = 1;
  	// 	console.log( "update sraft!" );
  	// 	window.setInterval(function(){
  	// 		updateCommunicationDraft();
  	// 	}, 5000);

  	// });
  </script>
</div>
