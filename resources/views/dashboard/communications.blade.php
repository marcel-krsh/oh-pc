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
				<button id="user-draft-messages" class="uk-button-large @if(session('communication_draft'))uk-button-success @else uk-button-default @endif" onclick="showDraftMessages();" aria-checked="false" uk-tooltip="pos:top-left;title:View draft messages">
					<i class="a-file-pencil-2"></i>
				</button>
				<button class="uk-button-large @if(session('communication_sent'))uk-button-default @else uk-button-success @endif" onclick="switchInbox();" aria-checked="false" uk-tooltip="pos:top-left;title:VIEW INBOX">
					<i class="a-folder-box"></i>
				</button>
				<button class="uk-button-large @if(session('communication_list'))uk-button-default @else uk-button-success @endif" onclick="switchListView();" aria-checked="false" uk-tooltip="pos:top-left;title:SWITCH CONVERSATOIN VIEW/LIST VIEW">
					<i class="a-file-hierarchy"></i>
				</button>
				<button class="uk-button-large @if(session('communication_sent'))uk-button-success @else uk-button-default @endif  uk-margin-right"  onclick="switchSentMessages();" aria-checked="false" uk-tooltip="pos:top-left;title:View sent messages">
					<i class="a-paper-plane"></i>
				</button>
			</div>

		</div>

		{{-- Group 2, Attachments and conversation list view --}}
		<div class=" uk-width-1-1@s uk-width-1-5@m">
			<div uk-grid>
				<button id="main-communication-document-button" class="uk-button-large uk-button-default filter-attachments-button uk-width-1-5 {{ session('filter-attachment') ? 'uk-button-success' : '' }}" uk-tooltip="pos:top-left;title:Show results with attachments" onclick="filterMainCommunications('attachment')">
					<i class="a-paperclip-2"></i>
				</button>
				<input id="communications-search" name="communications-search" type="text" value="{{ Session::get('filter-search') }}" class="uk-width-4-5 uk-input" placeholder="Search Messages Or Audit ID (press enter)">
			</div>
		</div>

		<div class="uk-width-1-1@s uk-width-1-5@m" id="recipient-dropdown" style="vertical-align: top;">
			<select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterMainCommunications();">
				<option value="all" selected="">
					FILTER BY RECIPIENT
				</option>
				@foreach ($message_recipients as $owner)
				<option {{ (session()->has('filter-recipient') && session()->get('filter-recipient') == 'staff-' . $owner['id']) ? 'selected=selected' : ''  }} value="staff-{{ $owner['id'] }}"><a class="uk-dropdown-close">{{ $owner['name'] }}</a></option>
				@endforeach
			</select>
		</div>

		@if(count($projects_array) > 0)
		<div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align: top;">
			<select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="filterMainCommunications();">
				<option value="all" selected="">
					FILTER BY PROJECT
				</option>
				@foreach ($projects_array as $projects)
				<option {{ (session()->has('filter-project') && session()->get('filter-project') == 'program-' . $projects->id) ? 'selected=selected' : ''  }} value="program-{{ $projects->id }}"><a  class="uk-dropdown-close">{{ $projects->project_name }}</a></option>
				@endforeach
			</select>
		</div>
		@endif
		<div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align:top">
			<a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-outbound-email-entry/')">
				<span class="a-envelope-4"></span>
				<span>NEW MESSAGE</span>
			</a>
		</div>
		<hr class="uk-width-1-1 uk-margin-top">
		<div class="uk-width-2-3 uk-margin-top" id="main-communications-tab-pages-and-filters" >
			{{ $messages->links() }}
		</div>
		<div class="uk-width-1-3  uk-text-right">
			@php
			$unreadCount = 0;
			foreach ($messages as $urc) {
				if ($urc->recipients->where('user_id', $current_user->id)->where('seen', null)->count()) {
					$unreadCount++;
				}
			}
			@endphp
			<div class="uk-align-right uk-label  uk-margin-top ">{{ $unreadCount }} UNREAD MESSAGES </div>
		</div>
	</div>
	<div id="main_communications-table">
		@if(count($messages))
		<div uk-grid class="uk-margin-top uk-visible@m">
			<div class="uk-width-1-1">
				<div uk-grid>
					<div class=" uk-width-1-5@m uk-width-1-1@s">
						<div class="uk-margin-small-left"><small><strong>RECIPIENTS</strong></small></div>
					</div>
					<div class="uk-width-1-5@m uk-width-1-1@s">
						<div class="uk-margin-small-left"><small><strong>AUDIT | PROJECT</strong></small></div>
					</div>
					<div class="uk-width-2-5@m uk-width-1-1@s">
						<div class="uk-margin-small-left"><small><strong>SUMMARY</strong></small></div>
					</div>
					<div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
						<div class="uk-margin-right"><small><strong>DOCUMENTS</strong></small></div>
					</div>
				</div>
			</div>
		</div>
		@endif

		<div uk-grid class="uk-container uk-grid-collapse uk-margin-top uk-container-center" id="communication-list" style="width: 98%">
			@if(count($messages))
			@foreach ($messages as $message)
			<div id="main-communication-row-{{ $message->id }}" style="width: 100%">
				@include('projects.partials.main-communication-row')
			</div>
			@endforeach
			@endif
		</div>
		<div class="uk-width-1-1 uk-margin-top uk-margin-left" id="main-communications-tab-pages-and-filters-2" >
			{{ $messages->links() }}
		</div>
	</div>
	<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
		<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
	</div>
</div>

<script>

	$(document).ready(function(){
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		$('#main-communications-tab-pages-and-filters .page-link').click(function(){
			var url = $(this).attr('href');
			if(!(url == '' || url == undefined)) {
				$('#main_communications-table').html(tempdiv);
				$('#communications_tab').load($(this).attr('href'));
				// window.currentDocumentsPage = $(this).attr('href');
				return false;
			}
		});

		$('#main-communications-tab-pages-and-filters-2 .page-link').click(function(){
			var url = $(this).attr('href');
			if(!(url == '' || url == undefined)) {
				$('#main_communications-table').html(tempdiv);
				$('#communications_tab').load($(this).attr('href'));
				return false;
			}
		});
	});

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




	 function filterMainCommunications(from = ''){
	 	// debugger;
	 	if(from == 'attachment' && !$("#main-communication-document-button").hasClass("uk-button-success")) {
		 	var filterAttachment = 1;
	 	} else if(from != 'attachment' && $("#main-communication-document-button").hasClass("uk-button-success")) {
			var filterAttachment = 1;
	 	} else {
	 		var filterAttachment = 0;
	 	}
	 	var filterBySearch = $("#communications-search").val();
	 	var filterByReceipient = $("#filter-by-owner").val();
	 	var filterByProject = $("#filter-by-program").val();
	 	var myGrid = UIkit.grid($('#communication-list'), {
	 		controls: '#message-filters',
	 		animation: false
	 	});

	 	$.post('{{ URL::route("communications.main-filters") }}', {
	 		'filter_recipient' : filterByReceipient,
	 		'filter_attachment' : filterAttachment,
	 		'filter_search' : filterBySearch,
	 		'filter_project' : filterByProject,
	 		'_token' : '{{ csrf_token() }}'
	 	}, function(data) {
	 		if(data[0]!='1'){
	 			UIkit.modal.alert(data);
	 		}
	 		$('#detail-tab-2').trigger('click');
	 	});
	 }

	 $('#communications-search').keydown(function (e) {
	 	if (e.keyCode == 13) {
	 		filterMainCommunications();
	 		e.preventDefault();
	 		return false;
	 	}
	 });


	 	function updateMainCommunicationRow(communicationId){
	 		var newContent = $('#main-communication-row-'+communicationId);
	 		var tempdiv = '<div style="height:200px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	 		$(newContent).html(tempdiv);
	 		$(newContent).load("{{ url('communications/single-communication') }}/"+communicationId, function(response) {
	 			if (response == "error") {
	 				var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
	 				UIkit.modal(msg, {center: true, bgclose: false, keyboard:false,  stack:true}).show();
	 			} else {
						$(newContent).html(response);
					}
				});
	 	}
	 </script>
