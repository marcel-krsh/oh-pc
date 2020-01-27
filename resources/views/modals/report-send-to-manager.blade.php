<script>
	resizeModal(80);
</script>

<form name="newOutboundEmailForm" id="newOutboundEmailForm" method="post">
	@if(!is_null($project))<input type="hidden" name="project_id" value="{{ $project->id }}">@endif
	@if(!is_null($audit))<input type="hidden" name="audit" value="{{ $audit }}">@endif
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div uk-grid class="uk-grid-small ">
			<div class="uk-width-1-1 uk-padding-small">
				@if($project)
				<h3>Report Message: <span id="current-file-id-dynamic-modal">{{ $project->project_number }}: {{ $project->project_name }}</span></h3>
				<input type="hidden" name="report" value="{{$report->id}}">
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
			<div class="uk-width-4-5 "  id="recipients-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
				@if(!is_null($audit))
				@cannot('access_auditor')

				@else
				<div id="add-recipients-button" class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showRecipients()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD RECIPIENT</div><div id="done-adding-recipients-button" class="uk-button uk-button-success uk-button-small" style="padding-top: 2px; display: none;" onClick="showRecipients()"><i class="a-circle-cross"></i> &nbsp;DONE ADDING RECIPIENTS</div>
				<div id='recipient-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox recipient-selector"><span class=
					'recipient-name'></span>
				</div>
				@endCannot
				@else
				<div id="add-recipients-button" class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showRecipients()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD RECIPIENT</div><div id="done-adding-recipients-button" class="uk-button uk-button-success uk-button-small" style="padding-top: 2px; display: none;" onClick="showRecipients()"><i class="a-circle-cross"></i> &nbsp;DONE ADDING RECIPIENTS</div>
				<div id='recipient-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox recipient-selector"><span class=
					'recipient-name'></span>
				</div>
				@endIf

			</div>
			<div class="uk-width-1-5 recipient-list" style="display: none;"></div>
			<div class="uk-width-4-5 recipient-list" id='recipients' style="border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:18px; padding-left:25px; position: relative;top:0px; display: none">
				<!-- RECIPIENT LISTING -->
				<div class="communication-selector uk-scrollable-box">
					<ul class="uk-list document-menu">
						@php $currentOrg = ''; @endphp
						@foreach ($recipients as $recipient)
						@if($currentOrg != $recipient->organization_name)
						<li class="recipient-list-item {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }}"><strong>{{ $recipient->organization_name }}</strong></li>
						<hr class="recipient-list-item dashed-hr uk-margin-bottom">
						@php $currentOrg = $recipient->organization_name; @endphp
						@endIf
						<li class="recipient-list-item {{ strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name))))) }} {{ strtolower($recipient->name) }}">
							<input name="" id="list-recipient-id-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipient(this.value,'{{ ucwords($recipient->name) }} ')">
							<label for="recipient-id-{{ $recipient->id }}">
								{{ ucwords($recipient->name) }}
							</label>
						</li>
						@endforeach
						<input type="hidden" name="notification_triggered_type" value="3">
						<input type="hidden" name="notification_model_id" value="{{ $report_id }}">
						<input type="hidden" name="report_approval_type" value="2">
					</ul>
				</div>
				<div class="uk-form-row">
					<input type="text" id="recipient-filter" class="uk-input uk-width-1-1" placeholder="Filter Recipients">
				</div>
				<script>
            // CLONE RECIPIENTS
            @if($audit)
            @cannot('access_auditor')
                    // add the user selection
                    var recipientClone = $('#recipient-template').clone();
                    recipientClone.attr("id", "recipient-id-{{$audit->lead_user_id}}-holder");
                    recipientClone.prependTo('#recipients-box');

                    $("#recipient-id-"+formValue+"-holder").slideDown();
                    $("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("id","recipient-id-"+formValue);
                    $("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("name","recipients[]");
                    $("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeRecipient("+formValue+");");

                    $("#recipient-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
                    $("#recipient-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
                    function removeRecipient(id){
                    	UIkit.modal.alert('<h1>Sorry</h1><h2>You cannot remove this recipient</h2>');
                    }

                    @else
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

                    @endCannot
                    @else
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
            @endIf
          </script>
          <!-- END RECIPIENT LISTING -->
        </div>

        <div class="uk-width-1-5 " style="padding:18px;padding-top:27px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;SUBJECT:</div>
        <div class="uk-width-4-5"  style="padding:18px; border-bottom:1px #111 dashed;">
        	<fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
        		<div uk-grid class="uk-grid-collapse">
        			<div class="uk-width-1-1">
        				<input type="text" name="subject" class="uk-width-1-1 uk-input uk-form-large uk-form-blank" placeholder="Recipients will see your subject in their notifications." value="Report ready for {{ $project->project_number }} : {{ $project->project_name }}">
        			</div>
        		</div>
        	</fieldset>
        </div>

        <div class="uk-width-1-5 " style="padding:18px; padding-top:40px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;MESSAGE:</div>
        <div class="uk-width-4-5 " style="padding:18px;">
        	<fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
        		<div uk-grid class="uk-grid-collapse">
        			<div class="uk-width-1-1">
        				<textarea id="message-body" style="min-height: 100px;padding-left: 10px; border:none;" rows="11" class="uk-width-1-1 uk-form-large uk-input uk-form-blank uk-resize-vertical" name="messageBody" value="" placeholder="Recipients will have to log-in to view your message.">Please go to the reports tab and click on report # {{$report->id}} to view your report.</textarea>
        			</div>
        		</div>
        	</fieldset>
        </div>
        <hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-margin-top">
        <div class="uk-width-1-3">&nbsp;</div>
        <div class="uk-width-1-3"><a class="uk-width-5-6 uk-button uk-align-right " onclick="$('#crr-report-row-{{$report->id}}').slideDown();dynamicModalClose();"><i class="a-circle-cross"></i> CANCEL</a></div>
        <div class="uk-width-1-3"><a class="uk-width-5-6 uk-align-right uk-button uk-button-success" onclick="submitNewCommunication()"><i class="a-paper-plane"></i> SEND</a>
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

    function showRecipients() {
    	$('.recipient-list').slideToggle();
    	$('#add-recipients-button').toggle();
    	$('#done-adding-recipients-button').toggle();
    }

    function submitNewCommunication() {
    	var form = $('#newOutboundEmailForm');
    	var no_alert = 1;
    	var recipients_array = [];
    	$("input[name='recipients[]']:checked").each(function (){
    		recipients_array.push(parseInt($(this).val()));
    	});
    	if(recipients_array.length === 0){
    		no_alert = 0;
    		UIkit.modal.alert('You must select a recipient.',{stack: true});
    	}
    	if(no_alert){
    		$.post('{{ URL::route("communication.create") }}', {
    			'inputs' : form.serialize(),
    			'_token' : '{{ csrf_token() }}'
    		}, function(data) {
    			if(data!=1){
    				UIkit.modal.alert(data,{stack: true});
    			} else {
    				if($('#project-detail-tab-1').hasClass('uk-active')){
    					$('#project-detail-tab-1').trigger('click');
    				}else{
    					updateStatus({{ $report_id }}, 6);
    				}
    			}
    		} );

    		@if($project)
    		var id = {{ $project->id }};
    		loadTab('/projects/'+{{ $project->id }}+'/communications/', '2', 0, 0, 'project-', 1);
        //loadParcelSubTab('communications',id);
        @else
        //loadDashBoardSubTab('dashboard','communications');
        @endif
        dynamicModalClose();
      }
    }
  </script>
</div>
