<div id="project_communications_tab">
	<div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">

		<a id="filter-none" class="filter_link" data-filter="all" hidden>all</a>
		<a id="filter-attachments" class="filter_link" data-filter="attachment-true" hidden>attachments</a>


		<div class=" uk-width-1-1@s uk-width-2-5@m">
			<div uk-grid>
				<button id="project-communication-document-button" class="uk-button-large uk-button-default filter-attachments-button uk-width-1-6 {{ session('project-filter-attachment') ? 'uk-button-success' : '' }}" uk-tooltip="pos:top-left;title:Show results with attachments" onclick="filterProjectCommunications('attachment')">
					<i class="a-paperclip-2"></i>
				</button>
				<input id="communications-project-search" name="communications-project-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-5-6 uk-input" placeholder="Search Messages Or Audit ID (press enter)">
			</div>
		</div>

		<div class="uk-width-1-1@s uk-width-1-5@m" id="recipient-dropdown" style="vertical-align: top;">
			<select id="filter-by-owner-project" class="uk-select filter-drops uk-width-1-1" onchange="filterProjectCommunications();">
				<option value="all" selected="">
					FILTER BY RECIPIENT
				</option>
				@foreach ($message_recipients as $owner)
				<option  {{ (session()->has('filter-recipient-project') && session()->get('filter-recipient-project') == 'staff-' . $owner['id']) ? 'selected=selected' : ''  }} value="staff-{{ $owner['id'] }}"><a class="uk-dropdown-close">{{ $owner['name'] }}</a></option>
				@endforeach
			</select>
		</div>

		<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
			<a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoadLocal('new-outbound-email-entry/{{ $project->id }}/{{ $audit }}/null/null/null/1/0/projects')">
				<span class="a-envelope-4"></span>
				<span>NEW MESSAGE</span>
			</a>
		</div>
		<div class=" uk-width-1-1@s uk-width-1-6@m uk-text-right">

			<div class="uk-align-right uk-label  uk-margin-top ">{{ ($messages_count) }}  MESSAGES </div>

		</div>
	</div>

	<hr class="uk-width-1-1 uk-margin-top">
	<div class="uk-width-1-1 uk-margin-top uk-margin-left" id="communications-tab-pages-and-filters" >
		{{ $messages->links() }}
	</div>
	<div id="project_communications-table">
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

		<div uk-grid class="uk-container uk-grid-collapse uk-margin-top uk-container-center" id="communication-list-project" style="width: 98%">
			@if(count($messages))
			@foreach ($messages as $message)
			<div id="communication-row-{{ $message->id }}" style="width: 100%">
				@include('projects.partials.communication-row')
			</div>
			@endforeach
			@endif
		</div>
		<div class="uk-width-1-1 uk-margin-top uk-margin-left" id="communications-tab-pages-and-filters-2" >
			{{ $messages->links() }}
		</div>
	</div>


	<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
		<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
	</div>
</div>
<script>
	window.project_detail_tab_2 = 1;

	$(document).ready(function(){
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		$('#communications-tab-pages-and-filters .page-link').click(function(){
			$('#project_communications-table').html(tempdiv);
			$('#project_communications_tab').load($(this).attr('href'));
					// window.currentDocumentsPage = $(this).attr('href');
					return false;
				});

		$('#communications-tab-pages-and-filters-2 .page-link').click(function(){
			$('#project_communications-table').html(tempdiv);
			$('#project_communications_tab').load($(this).attr('href'));
			return false;
		});
	});

	function filterProjectCommunications(from = '') {
		if(from == 'attachment' && !$("#project-communication-document-button").hasClass("uk-button-success")) {
			var filterAttachment = 1;
		} else if(from != 'attachment' && $("#project-communication-document-button").hasClass("uk-button-success")) {
			var filterAttachment = 1;
		} else {
			var filterAttachment = 0;
		}
		var filterBySearch = $("#communications-project-search").val();
		var filterByReceipient = $("#filter-by-owner-project").val();
		var myGrid = UIkit.grid($('#communication-list'), {
			controls: '#message-filters',
			animation: false
		});

		$.post('{{ URL::route("communications.project-filters") }}', {
			'filter_recipient' : filterByReceipient,
			'filter_attachment' : filterAttachment,
			'filter_search' : filterBySearch,
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data[0]!='1'){
				UIkit.modal.alert(data);
			}
			$('#project-detail-tab-2').trigger('click');
		});
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


		 // process search
		 $(document).ready(function() {

		 	// filterByOwnerProject(0);
		 	$('#communications-project-search').keydown(function (e) {
		 		if (e.keyCode == 13) {
		 			filterProjectCommunications();
		 			// searchMessages();
		 			e.preventDefault();
		 			return false;
		 		}
		 	});


		 	var $filteredElements = $('.filter_element_project');

		 });
		</script>

		<script>
			function dynamicModalLoadLocal(modalSource) {
				var newmodalcontent = $('#dynamic-modal-content-communications');
				$(newmodalcontent).html('<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>');
				$(newmodalcontent).load('/modals/'+modalSource, function(response, status, xhr) {
					if (status == "error") {
						if(xhr.status == "401") {
							var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
						} else if( xhr.status == "500"){
							var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
						} else {
							var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
						}
						UIkit.modal.alert(msg);
					}
				});
				var modal = UIkit.modal('#dynamic-modal-communications', {
					escClose: false,
					bgClose: false
				});
				modal.show();
			}

			function updateCommunicationRow(communicationId){
				var newContent = $('#communication-row-'+communicationId);
				var tempdiv = '<div style="height:200px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
				$(newContent).html(tempdiv);
				$(newContent).load("{{ url('communications/single-communication') }}/"+communicationId, function(response) {
					if (response == "error") {
						var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
						UIkit.modal(msg, {center: true, bgclose: false, keyboard:false,  stack:true}).show();
					} else {
						// debugger;
						$(newContent).html(response);
					}
				});
			}
		</script>
