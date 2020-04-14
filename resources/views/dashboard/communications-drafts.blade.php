<div id="communications_tab">
	<div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">

		<a id="filter-none" class="filter_link" data-filter="all" hidden>all</a>
		<a id="filter-attachments" class="filter_link" data-filter="attachment-true" hidden>attachments</a>
		{{-- Total of 6 groups
		* Group 1
		* 	View inbox
		* 	View sent messages
		* Group 2
		* 	Show results with attachments
		* 	Show Conversation list view
		* Group 3
		* 	Search within message or Audit? Input text field
		* Group 4
		* 	Filter by recepient
		* Group 5
		* 	Filter by project
		* Group 6
		* 	New Message
		*
		--}}
		<div uk-grid class="uk-grid-collapse uk-visible@m uk-width-1-5@m">
			{{-- Group 1, Inbox and Sent message --}}
			<div class="uk-button-group uk-margin-medium-left">
				<button id="user-comm-read-toggle" class="uk-button-large" onclick="toggleReadMessages(this);" aria-checked="false" uk-tooltip="pos:top-left;title:SHOW / HIDE READ MESSAGES">
					<i class="a-star"></i>
				</button>
				<button id="user-draft-messages" class="uk-button-large uk-button-success" onclick="showDraftMessages();" aria-checked="false" uk-tooltip="pos:top-left;title:View draft messages">
					<i class="a-file-pencil-2"></i>
				</button>
				<button class="uk-button-large uk-button-default " onclick="switchInbox();" aria-checked="false" uk-tooltip="pos:top-left;title:VIEW INBOX">
					<i class="a-folder-box"></i>
				</button>
				<button class="uk-button-large uk-button-default" onclick="switchListView();" aria-checked="false" uk-tooltip="pos:top-left;title:SWITCH CONVERSATOIN VIEW/LIST VIEW">
					<i class="a-file-hierarchy"></i>
				</button>
				<button class="uk-button-large uk-button-default  uk-margin-right"  onclick="switchSentMessages();" aria-checked="false" uk-tooltip="pos:top-left;title:View sent messages">
					<i class="a-paper-plane"></i>
				</button>
			</div>

		</div>

		{{-- Group 2, Attachments and conversation list view --}}
		<div class=" uk-width-1-1@s uk-width-1-5@m">
			<div uk-grid>
				<button class="uk-button-large uk-button-default filter-attachments-button uk-width-1-5" uk-tooltip="pos:top-left;title:Show results with attachments">
					<i class="a-paperclip-2"></i>
				</button>
			</div>
		</div>
	</div>
</div>

@if(count($messages))
<div uk-grid class="uk-margin-top uk-visible@m">
	<div class="uk-width-1-1">
		<div uk-grid>
			{{-- <div class=" uk-width-1-5@m uk-width-1-1@s">
				<div class="uk-margin-small-left"><small><strong>RECIPIENTS</strong></small></div>
			</div> --}}
			<div class="uk-width-1-5@m uk-width-1-1@s">
				<div class="uk-margin-small-left"><small><strong>AUDIT | PROJECT</strong></small></div>
			</div>
			<div class="uk-width-2-5@m uk-width-1-1@s">
				<div class="uk-margin-small-left"><small><strong>SUMMARY</strong></small></div>
			</div>
			<div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
				<div class="uk-margin-right"><small><strong>REPORT</strong></small></div>
			</div>
			<div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
				<div class="uk-margin-large-right"><small><strong>DOCUMENTS</strong></small></div>
			</div>
		</div>
	</div>
</div>
@endif

<div uk-grid class="uk-container uk-grid-collapse uk-margin-top uk-container-center" id="communication-list" style="width: 98%">
	@if(count($messages))
	@foreach ($messages as $message)
	<div class="filter_element uk-width-1-1 communication-list-item @if($message->project)program-{{ $message->project->id }}@endif  @if(!is_null($message->documents)) attachment-true @endif" uk-filter="outbound-phone" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;" >
		<div uk-grid class="communication-summary @if($message->unseen) communication-unread @endif">

			{{-- project --}}
			<div class="uk-width-1-5@m communication-item-parcel uk-visible@m" onclick="dynamicModalLoad('communication/open-draft/{{ $message->id }}'); ">
				@if($message->audit_id && $message->audit && $message->audit->cached_audit)
				<p style="margin-bottom:0"><a class="uk-link-muted">{{ $message->audit_id }} | {{ $message->project->project_number }} : {{ $message->project->project_name }}</a></p>
				<p class="uk-visible@m" style="margin-top:0"   >
					<small>{{ $message->audit->cached_audit->address }},
						{{ $message->audit->cached_audit->city }}, @if($message->audit->cached_audit->state){{ $message->audit->cached_audit->state }} @endif {{ $message->audit->cached_audit->zip }}
					</small>
				</p>
				@endif
			</div>

			{{-- Summary --}}
			<div class="uk-width-2-5@m communication-item-parcel uk-visible@m">
				<div class="uk-width-1-1 communication-item-excerpt" onclick="dynamicModalLoad('communication/open-draft/{{ $message->id }}'); "> @if($message->subject){{ $message->subject }}:<hr class="dashed-hr" /> @else NA @endif {!! str_replace('<br /><br />', '</p><p>',nl2br(substr($message->message,0,100))) !!} @if(strlen($message->message) > 100)...@endif
				</div>
			</div>

			{{-- report --}}
			<div class="uk-width-1-5@s communication-type-and-who uk-text-right " >
				<div class="uk-margin-right">
					@if(!is_null($message->report_id))
					<small><a target="report-{{ $message->report_id }}" href="{{ url('report/' . $message->report_id) }}"><i class="a-file-chart-3"></i> #{{ $message->report_id }}</a></small>
					@else
					NA
					@endif
				</div>
			</div>

			{{-- Documents --}}
			<div class="uk-width-1-5@m communication-item-excerpt " onclick="dynamicModalLoad('communication/open-draft/{{ $message->id }}'); " >
				@if(!is_null($message->selected_documents))
				<div uk-grid class="uk-grid-collapse">
					<div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt uk-align-center" onclick="dynamicModalLoad('communication/open-draft/{{ $message->id }}'); " >
						<div class="communication-item-attachment uk-margin-large-left">
							<span uk-tooltip="pos:top-left;title:@foreach($message->getSelectedDocuments() as $document) {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} <br> @endforeach">
								<i class="a-paperclip-2"></i>
							</span>
						</div>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>

	@endforeach

	@else
	<div uk-grid class="communication-summary ">
		<strong>No drafts found</strong>
	</div>
	@endif
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
	<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
</div>

<script>

	function switchInbox(){
		var trigger = 'communication_inbox';
		$.get( "/communication/session/"+trigger, function(data) {
			if(data[0]!='1'){
				UIkit.modal.alert(data);
			} else {
				$('#detail-tab-2').trigger('click');
			}
		});
	}

	function switchSentMessages(){
		var trigger = 'communication_sent';
		$.get( "/communication/session/"+trigger, function(data) {
			if(data[0]!='1'){
				UIkit.modal.alert(data);
			} else {
				$('#detail-tab-2').trigger('click');
			}
		});
	}

	function switchListView(){
		var trigger = 'communication_list';
		$.get( "/communication/session/"+trigger, function(data) {
			if(data[0]!='1'){
				UIkit.modal.alert(data);
			} else {
				$('#detail-tab-2').trigger('click');
			}
		});
	}


	function filterElement(filterVal, filter_element){
		if (filterVal === 'all') {
			$(filter_element).show();
		}
		else {
			$(filter_element).hide().filter('.' + filterVal).show();
		}
		UIkit.update(event = 'update');
	}

	function showDraftMessages(){
		loadTab("{{ URL::route('communications.show-draft-messages') }}", '2','','','',1);
	}

	function closeOpenMessage(){
		$('.communication-list-item').removeClass('communication-open');
		$('.communication-details').addClass('uk-hidden');
		$('.communication-summary').removeClass('uk-hidden');
	}

	function openMessage(communicationId){
		closeOpenMessage();
		$("#communication-"+communicationId).addClass('communication-open');
		$("#communication-"+communicationId+"-details").removeClass('uk-hidden');
		$("#communication-"+communicationId+"-summary").addClass('uk-hidden');
	}

	function toggleReadMessages(target){
		console.log('Toggling read messages on/off');
		if(window.user_comms_read == 1){
			window.user_comms_read = 0;
			$(target).removeClass('uk-button-success');
			$('.user_comms_read').slideDown();
		}else{
			window.user_comms_read = 1;
			$(target).addClass('uk-button-success');
			$('.user_comms_read').slideUp();
		}
	}
	 // process search
	 $(document).ready(function() {
	 	$('.filter-attachments-button').click(function () {
	 		if( $(this).hasClass('uk-button-success') ){
	 			$(this).removeClass('uk-button-success');
	 			$('#filter-none').trigger('click');
	 		}else{
	 			$(this).addClass('uk-button-success');
	 			$('#filter-attachments').trigger('click');
	 		}
	 	});


	 	// @if (session()->has('dynamicModalLoad') && session('dynamicModalLoad') != '' )
	 	// var dynamicModalLoadid = '';
	 	// $.get( "/session/dynamicModalLoad", function( data ) {
	 	// 	dynamicModalLoadid = data;
	 	// 	console.log('Loading Message Id: '+dynamicModalLoadid);

	 	// 	if(dynamicModalLoadid != ''){
	 	// 		dynamicModalLoad("communication/0/replies/"+dynamicModalLoadid);
	 	// 	}
	 	// });
	 	// @endif

	 	var $filteredElements = $('.filter_element');
	 	$('.filter_link').click(function (e) {
	 		e.preventDefault();
        // get the category from the attribute
        var filterVal = $(this).data('filter');
        filterElement(filterVal, '.filter_element');

        // reset dropdowns
        $('#filter-by-owner').prop('selectedIndex',0);
        @if(Auth::user()->isFromEntity(1))
        $('#filter-by-program').prop('selectedIndex',0);
        @endif
      });
	 	// reset view based on previous selection
	 	if(window.user_comms_read == 1){
	 		console.log('Hiding Read Messages');
	 		$('#user-comm-read-toggle').addClass('uk-button-success');
	 		$('.user_comms_read').hide();
	 	}

	 });
	</script>
