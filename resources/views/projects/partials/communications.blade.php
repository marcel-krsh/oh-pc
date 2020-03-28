<div id="project_communications_tab">
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

		{{-- Group 2, Attachments and conversation list view --}}

		<div class=" uk-width-1-1@s uk-width-2-5@m">
			<div uk-grid>
				<button class="uk-button-large uk-button-default filter-attachments-button uk-width-1-6" uk-tooltip="pos:top-left;title:Show results with attachments">
					<i class="a-paperclip-2"></i>
				</button>
				<input id="communications-project-search" name="communications-project-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-5-6 uk-input" placeholder="Search Messages Or Audit ID (press enter)">
			</div>
		</div>

		<div class="uk-width-1-1@s uk-width-1-5@m" id="recipient-dropdown" style="vertical-align: top;">
			<select id="filter-by-owner-project" class="uk-select filter-drops uk-width-1-1" onchange="filterByOwnerProject();">
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
	</div>

		<div class="uk-width-1-1 uk-margin-top uk-margin-left" id="communications-tab-pages-and-filters-2" >
			{{ $messages->links() }}
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

			function filterByOwnerProject(session = 1){
				// debugger;
				var myGrid = UIkit.grid($('#communication-list-project'), {
					controls: '#message-filters',
					animation: false
				});
				var textinput = $("#filter-by-owner-project").val();

				@if($current_user->isFromEntity(1))
				// $('#filter-by-program').prop('selectedIndex',0);
				@endif
				// filterElement(textinput, '.filter_element_project');
				if(session == 1) {
					$.post('{{ URL::route("communications.filter-recipient-project") }}', {
						'filter_recipient_project' : $("#filter-by-owner-project").val(),
						'_token' : '{{ csrf_token() }}'
					}, function(data) {
						if(data[0]!='1'){
							UIkit.modal.alert(data);
						}
						searchMessages();
					});
				}

			}

			function filterElement(filterVal, filter_element_project){
				if (filterVal === 'all') {
					$(filter_element_project).show();
				}
				else {
					$(filter_element_project).hide().filter('.' + filterVal).show();
				}
				UIkit.update(event = 'update');
			}

			function filterByProgram(){
				var myGrid = UIkit.grid($('#communication-list-project'), {
					controls: '#message-filters',
					animation: false
				});
				var textinput = $("#filter-by-program").val();
				$('#filter-by-owner-project').prop('selectedIndex',0);
				filterElement(textinput, '.filter_element_project');
			}

			function searchMessages(){
				$.post('{{ URL::route("communications.search") }}', {
					'communications-search' : $("#communications-project-search").val(),
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data[0]!='1'){
						UIkit.modal.alert(data);
					} else {
						$('#project-detail-tab-2').trigger('click');
					}
				} );
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
		 	$('.filter-attachments-button').click(function () {
		 		if( $(this).hasClass('uk-button-success') ){
		 			$(this).removeClass('uk-button-success');
		 			$('#filter-none').trigger('click');
		 		}else{
		 			$(this).addClass('uk-button-success');
		 			$('#filter-attachments').trigger('click');
		 		}
		 	});

		 	filterByOwnerProject(0);
		 	$('#communications-project-search').keydown(function (e) {
		 		if (e.keyCode == 13) {
		 			searchMessages();
		 			e.preventDefault();
		 			return false;
		 		}
		 	});


		 	var $filteredElements = $('.filter_element_project');
		 	$('.filter_link').click(function (e) {
		 		e.preventDefault();
	        // get the category from the attribute
	        var filterVal = $(this).data('filter');
	        filterElement(filterVal, '.filter_element_project');

	        // reset dropdowns
	        $('#filter-by-owner-project').prop('selectedIndex',0);
	        @if($current_user->isFromEntity(1))
	        $('#filter-by-program').prop('selectedIndex',0);
	        @endif
	      });

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
