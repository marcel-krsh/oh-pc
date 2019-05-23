<script>
	resizeModal(85);
	reloadUnseenMessages();
</script>
<?php if (!isset($project)) {$project = null;}?>
<div class="uk-container">
	<div uk-grid class="uk-grid-collapse">
		<div class="uk-width-1-1">
			<h3 class="communication-direction-text uk-text-lead uk-text-center">
				<span class="uk-float-left">MESSAGE</span>
				<span class="uk-text-emphasis">@if($audit)&nbsp;&nbsp;&nbsp;{{ $audit->project->project_number }}:{{ $audit->project->project_name }} | Audit: {{ $audit->id }}@endif </span>
			</h3>
		</div>
		<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
	</div>

	<div uk-grid class="uk-grid-collapse open-communication-bottom-rules uk-margin-small-top uk-margin-bottom">
		<div class="uk-width-1-5 uk-width-1-5@s communication-type-and-who ">
			<span class=" communication-item-date-time">
				<span class="uk-text-muted">Date: </span>  <br>
				{{ date('F d, Y h:i', strtotime($message->created_at)) }}
			</span>
		</div>
		<div class="uk-width-1-5 uk-width-1-5@s communication-item-excerpt uk-margin-bottom">
			<span class="uk-text-muted">From: </span> <br>
			{{-- <div style="margin-top: -5px" class="user-badge user-badge-communication-item user-badge-{{ $message->owner->badge_color }} no-float">
				{{ $message->initials }}
			</div> --}}
			{{ $message->owner->full_name() }}
		</div>
		<div class="uk-width-1-5 uk-width-1-5@s communication-item-tt-to-from">
			@if(count($message->recipient_details))
			<span class="uk-text-muted">To: </span> <br>
			@foreach ($message->recipient_details as $recipient)
			{{ $recipient->full_name() }}@if(!$loop->last), @endif <br>
			@endforeach
			@endif
		</div>
		{{-- Dcouments section --}}
		<div class="uk-width-2-5 uk-width-2-5@s">
			<span class="uk-text-muted">Documents: </span> <br>
			@foreach($message->local_documents as $document)
			<a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file<br />{{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}">
				<i class="a-paperclip-2"></i> {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}
			</a>
			<br>
			@endforeach
			@foreach($message->docuware_documents as $document)
			<a href="{{ url('/document', $document->docuware_doc_id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file<br />{{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }}">
				<i class="a-paperclip-2"></i> {{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }}
			</a>
			<br>
			@endforeach
		</div>
	</div>
	<!-- Start content of communication -->
	<div class="uk-width-1-1"><!--used to be uk-width-9-10, but Linda changed it-->
		<div uk-grid class="uk-grid-collapse uk-grid">
			<div class="uk-width-1-6 uk-margin-bottom">
				<span class="uk-text-muted">Subject: </span>
			</div>
			<div class="uk-width-5-6 uk-text-left uk-margin-bottom">
				@if($message->subject)<strong>{{ $message->subject }}</strong><br /> @endif
			</div>
			<div class="uk-width-1-6 uk-margin-bottom">
				<span class="uk-text-muted">Message: </span>
			</div>
			<div class="uk-width-5-6">
				<div><p>{{ str_replace('<br /><br />','</p><p>',nl2br($message->message))  }}</p></div>
			</div>
		</div>
	</div>
</div>

@if(count($replies))
<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
<h3 class="uk-text-primary">REPLIES</h3>
<div class="uk-container uk-margin-top" id="communication-list" style="position: relative; height: 222.5px; margin-left:5px;">
	@foreach ($replies as $reply)
	<div class="uk-width-1-1 communication-list-item normal-cursor" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
		<div uk-grid class="communication-summary">
			<div class="uk-width-1-1 uk-width-1-5@s communication-type-and-who ">
				<span uk-tooltip="pos:top-left;title:{{ $reply->owner->full_name() }};">
					<div style="margin-top: -3px" class="user-badge user-badge-communication-item user-badge-{{ $reply->owner->badge_color }} no-float">
						{{ $reply->initials }}
					</div>
				</span>
				<span class=" communication-item-date-time">
					{{ date('F d, Y h:i', strtotime($reply->created_at)) }}
				</span>
			</div>
			<div class="uk-width-1-1 uk-width-2-5@s communication-item-excerpt">
				<pre>{{ $reply->message }}</pre>
			</div>
			<div class="uk-width-1-1 uk-width-2-5@s">
				@foreach($reply->local_documents as $document)
				<a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-margin-left uk-margin-small-bottom" uk-tooltip title="Download file<br />{{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}">
					<i class="a-paperclip-2"></i> {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}
				</a>
				<br>
				@endforeach
				@foreach($reply->docuware_documents as $document)
				<a href="{{ url('/document', $document->docuware_doc_id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-margin-left uk-margin-small-bottom" uk-tooltip title="Download file<br />{{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }}">
					<i class="a-paperclip-2"></i> {{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }}
				</a>
				<br>
				@endforeach
			</div>
		</div>
	</div>
	@endforeach
</div>
@endif

<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
<div class="uk-container uk-grid-collapse uk-margin-top" id="communication-list" uk-grid style="position: relative; height: 222.5px; border-bottom:0px;">
	<button class="uk-button uk-button-success uk-width-1-3@m uk-width-1-1@s toggle-form" onclick="this.style.visibility = 'hidden';" uk-toggle="target: #newOutboundEmailForm">Write a reply</button>
	<form name="newOutboundEmailForm" id="newOutboundEmailForm" method="post" class="uk-margin-top toggle-form uk-width-1-1" hidden>
		@if($audit)<input type="hidden" name="audit" value="{{ $audit->id }}">@endif
		@if(!is_null($project))<input type="hidden" name="project_id" value="{{ $project->id }}">@endif
		<input type="hidden" name="communication" value="{{ $message->id }}">
		<div class="uk-container uk-container-center"> <!-- start form container -->
			<div uk-grid class="uk-grid-collapse">
				<div class="uk-width-1-1" uk-grid>
					<div class="uk-width-1-5">
						<h4>Reply Message:</h4>
					</div>
					<div class="uk-width-4-5">
						<fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
							<div uk-grid class="uk-grid-collapse">
								<div class="uk-width-1-1">
									<textarea id="message-body" style="min-height: 100px; border:none;" rows="11" class=" uk-form-large uk-input uk-form-blank uk-resize-vertical" name="messageBody" value="" placeholder="Recipients will have to log-in to view your message."></textarea>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
			@if($audit)
			<div uk-grid>
				@include('modals.partials.communication-documents')
			</div>
			@endif
		</div>
		<hr>
		<div uk-grid>
			<div class="uk-width-1-1">
				<div id="applicant-info-update">
					<div uk-grid class="uk-margin">
						<div class="uk-width-1-3 uk-push-1-3">
							<a class="uk-button uk-width-1-1" onclick="dynamicModalClose();"> <i class="a-circle-cross"></i> CANCEL</a>
						</div>
						<div class="uk-width-1-3 uk-push-1-3">
							<a class="uk-button uk-button-success uk-width-1-1@m uk-width-1-1" onclick="submitNewCommunication()"> <i uk-icon="mail" class="uk-margin-left"></i> SEND &nbsp;</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div id="dialog-comment-modal" style="display:none;">
		<p>I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.</p>
		<input name="comment" type="text" value=""/>
		<input type="submit" class="submit" value="Add comment" />
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

	    function showDocuments() {
	    	$('.documents-list').slideToggle();
	    	$('#add-documents-button').toggle();
	    	$('#done-adding-documents-button').toggle();
	    }

	    function submitNewCommunication() {
	    	var form = $('#newOutboundEmailForm');
	    	var no_alert = 1;
    	
    	
    		$.post('{{ URL::route("communication.create") }}', {
    			'inputs' : form.serialize(),
    			'_token' : '{{ csrf_token() }}'
    		}, function(data) {
    			if(data!=1){
    				UIkit.modal.alert(data,{stack: true});
    			} else {
    				//UIkit.modal.alert('Your message has been saved.',{stack: true});
                    @if(!$project || Auth::user()->cannot('access_auditor'))
                    $('#detail-tab-2').trigger('click');
                    @endIf
    			}
    		} );

	    	@if($project && Auth::user()->can('access_auditor'))
	    		var id = {{$project->id}};
	        loadTab('/projects/'+{{$project->id}}+'/communications/', '2', 0, 0, 'project-', 1);
	        //loadParcelSubTab('communications',id);
	        @else
	        //loadDashBoardSubTab('dashboard','communications');
	        @endif
	        dynamicModalClose();
      	}
    
  

		// function submitNewCommunication() {
		// 	var form = $('#newOutboundEmailForm');

		// 	$.post('{{ URL::route("communication.create") }}', {
		// 		'inputs' : form.serialize(),
		// 		'_token' : '{{ csrf_token() }}'
		// 	}, function(data) {
		// 		if(data!=1){
		// 			UIkit.modal.alert(data);
		// 		} else {
		// 			UIkit.modal.alert('Your message has been saved.');
		// 		}
		// 	} );


		// 		@if($audit)
		  //       // var id = {{ $audit->id }};
		  //       // if (typeof loadAuditSubTab === "function"){
		  //       // 	loadAuditSubTab('communications',id);
		  //       // }
		  //       //debugger;
		  //       loadTab('/projects/'+{{$project->id}}+'/communications/', '2', 0, 0, 'project-', 1);
		  //       @else
		  //       // $('#dash-subtab-10').trigger('click');
		  //       // loadDashBoardSubTab('dashboard','communications');
		  //       @endif

		  //       dynamicModalClose();


		  //     }
    </script>
  </div>
