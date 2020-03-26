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
				@foreach ($owners_array as $owner)
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

				<div class="uk-align-right uk-label  uk-margin-top ">{{ count($messages) }}  MESSAGES </div>

	</div>
</div>

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
<div class="@if($message->recipients->where('owner_id','<>',$current_user->id)->where('user_id',Auth::User()->id)->where('seen','<>',null)->count())user_comms_read @endIf filter_element_project uk-width-1-1 communication-list-item @if($message->message_recipients) @foreach($message->message_recipients as $mr) staff-{{ $mr->id }} @endforeach @endif @if($message->project)program-{{ $message->project->id }}@endif  @if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0) attachment-true @endif" uk-filter="outbound-phone" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1; @if($message->recipients->where('user_id',Auth::User()->id)->where('seen',null)->count()) font-weight: bold; @endIf" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects'); ">

		<div uk-grid class="communication-summary @if($message->unseen) communication-unread @endif">
			@if($message->owner->id == $current_user->id)
			<div class="uk-width-1-5@m uk-width-1-2@s communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects')">
				<div class="communication-item-date-time">
					<small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small><br>
					<span>
						FROM: Me<hr class="dashed-hr uk-margin-bottom uk-width-1-1">
							@if(count($message->message_recipients))TO:
								<?php $recipients = $message->message_recipients->where('id', '<>', $current_user->id);?>
								@if(count($recipients)>0)
									@foreach($recipients as $recipient)
										@if($recipient->pivot->seen != 1)<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">@endIf
											{{ $recipient->full_name() }}@if($recipient->pivot->seen != 1)</strong>@endif{{ !$loop->last ? ', ': '' }}
									@endforeach
								@else
									Me
								@endIf
							@endIf
					</span>
				</div>
				@if($message->unseen > 0)
				<div class="uk-label no-text-shadow user-badge-{{ Auth::user()->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen }} unread messages">{{ $message->unseen }}</div>
				@endif
			</div>
			@else
			<div class="uk-width-1-5@m uk-width-3-6@s communication-item-tt-to-from uk-margin-small-bottom"  >
				<div class="communication-item-date-time">
					<small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
				</div>
				FROM: {{ $message->owner->full_name() }}<hr class="dashed-hr uk-margin-bottom uk-width-1-1"> @if(count($message->message_recipients))TO:
					@foreach ($message->message_recipients->where('id', '<>', $message->owner_id) as $recipient)
						@if($recipient->id != $current_user->id && $message->owner != $recipient && $recipient->name != '')
					{{-- {{ dd($recipient) }} --}}
							@if($recipient->pivot->seen == null)<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">@endIf
								{{ $recipient->full_name() }}{{ !$loop->last ? ', ': '' }}
							@if($recipient->pivot->seen == null)</strong>@endIf
						@elseif($recipient->id == $current_user->id)
							Me{{ !$loop->last ? ', ': '' }}
						@endIf
					@endforeach
				 @endif
				@if($message->unseen > 0)
				<div class="uk-label no-text-shadow user-badge-{{ Auth::user()->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen }} unread messages">{{ $message->unseen }}</div>
				@endif
			</div>
			@endif
			<div class="uk-width-1-5@s communication-type-and-who uk-hidden@m uk-text-right " >
				<div class="uk-margin-right">
					@if($message->audit_id && $message->audit && $message->audit->cached_audit)
					<p style="margin-bottom:0">{{ $message->audit_id }} | {{ $message->project->project_number }} : {{ $message->project->project_name }}</p>
					<p class="uk-visible@m" style="margin-top:0" >
						<small>{{ $message->audit->cached_audit->address }},
							{{ $message->audit->cached_audit->city }}, @if($message->audit->cached_audit->state){{ $message->audit->cached_audit->state }} @endif {{ $message->audit->zip }}
						</small>
					</p>
					@endif
				</div>
			</div>
			<div class="uk-width-1-5@m communication-item-parcel uk-visible@m">
				@if($message->audit_id && $message->audit && $message->audit->cached_audit)
				<p style="margin-bottom:0"><a class="uk-link-muted">{{ $message->audit_id }} | {{ $message->project->project_number }} : {{ $message->project->project_name }}</a></p>
				<p class="uk-visible@m" style="margin-top:0" uk-tooltip="pos:left" title="{{ $message->audit->cached_audit->title }}">
					<small>{{ $message->audit->cached_audit->address }},
						{{ $message->audit->cached_audit->city }}, @if($message->audit->cached_audit->state){{ $message->audit->cached_audit->state }} @endif {{ $message->audit->cached_audit->zip }}
					</small>
				</p>
				@endif
			</div>
			<div class="uk-width-3-5@m uk-width-1-1@s communication-item-excerpt " onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects')" >
				@if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0)
				<div uk-grid class="uk-grid-collapse">
					<div class="uk-width-5-6@m uk-width-1-1@s communication-item-excerpt" >
						@if($message->subject)<strong>{{ $message->subject }}</strong><hr /> @endif
						{{ $message->message }}
					</div>
					<div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt uk-align-center" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects')" >
						<div class="communication-item-attachment uk-margin-large-left">
							<span uk-tooltip="pos:top-left;title:@foreach($message->local_documents as $document) {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} <br> @endforeach @foreach($message->docuware_documents as $document) {{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }} @endforeach">
								<i class="a-paperclip-2"></i>
							</span>
						</div>
					</div>
				</div>
				@else
				@if($message->subject)<strong>{{ $message->subject }}</strong><br />@endif
				{{ $message->message }}
				@endif
			</div>
		</div>
	</div>
	@endforeach
	@endif
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
	<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
</div>

<script>
	window.project_detail_tab_2 = 1;

	function filterByOwnerProject(session = 1){
		var myGrid = UIkit.grid($('#communication-list-project'), {
			controls: '#message-filters',
			animation: false
		});
		var textinput = $("#filter-by-owner-project").val();

		@if(Auth::user()->isFromEntity(1))
		$('#filter-by-program').prop('selectedIndex',0);
		@endif
		filterElement(textinput, '.filter_element_project');
		if(session == 1) {
			$.post('{{ URL::route("communications.filter-recipient-project") }}', {
				'filter_recipient_project' : $("#filter-by-owner-project").val(),
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data[0]!='1'){
					UIkit.modal.alert(data);
				}
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
            @if(Auth::user()->isFromEntity(1))
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
	</script>
