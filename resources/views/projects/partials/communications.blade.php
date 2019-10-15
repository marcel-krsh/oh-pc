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
			<select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterByOwner();">
				<option value="all" selected="">
					FILTER BY RECIPIENT
				</option>
				@foreach ($owners_array as $owner)
				<option value="staff-{{$owner['id']}}"><a class="uk-dropdown-close">{{$owner['name']}}</a></option>
				@endforeach
			</select>
		</div>

  	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
  		<a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-outbound-email-entry/{{ $project->id }}')">
  			<span class="a-envelope-4"></span>
  			<span>NEW MESSAGE</span>
  		</a>
  	</div>
  	<div class=" uk-width-1-1@s uk-width-1-6@m uk-text-right">

				<div class="uk-align-right uk-label  uk-margin-top ">{{count($messages)}}  MESSAGES </div>

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

<div uk-grid class="uk-container uk-grid-collapse uk-margin-top uk-container-center" id="communication-list" style="width: 98%">
	@if(count($messages))
	@foreach ($messages as $message)
	<div class="filter_element uk-width-1-1 communication-list-item @if($message->owner)staff-{{ $message->owner->id }}@endif @if($message->project)program-{{ $message->project->id }}@endif  @if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0) attachment-true @endif" uk-filter="outbound-phone" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1; @if($message->recipients->where('user_id',Auth::User()->id)->where('seen',null)->count()) font-weight: bold; @endIf" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif'); ">
		<div uk-grid class="communication-summary @if($message->unseen) communication-unread @endif">
			@if($message->owner->id == $current_user->id)
			<div class="uk-width-1-5@m uk-width-1-2@s communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')">
				<div class="communication-item-date-time">
					<small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small><br>
					<span>
						FROM: Me<hr class="dashed-hr uk-margin-bottom uk-width-1-1">
							@if(count($message->recipients))TO:
								<?php $recipients = $message->recipients->where('user_id', '<>', $current_user->id); ?>
								@if(count($recipients)>0)
									@foreach($recipients as $recipient)
										@if($recipient->seen == null)
										<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">
											{{ $recipient->user->name }}</strong>@else {{ $recipient->user->name }}@endif{{ !$loop->last ? ', ': '' }}
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
				FROM: {{ $message->owner->full_name() }}<hr class="dashed-hr uk-margin-bottom uk-width-1-1"> @if(count($message->message_recipients))TO: @foreach ($message->message_recipients as $recipient)
				@if($recipient->id != $current_user->id && $message->owner != $recipient && $recipient->name != '')@if($recipient->seen == null)<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">@endIf{{ $recipient->full_name() }}@if($recipient->seen == null)</strong>@endIf @elseif($recipient->id == $current_user->id) @if(!$loop->first),@endIf Me @endif @endforeach @endif
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
			<div class="uk-width-3-5@m uk-width-1-1@s communication-item-excerpt " onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
				@if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0)
				<div uk-grid class="uk-grid-collapse">
					<div class="uk-width-5-6@m uk-width-1-1@s communication-item-excerpt" >
						@if($message->subject)<strong>{{ $message->subject }}</strong><hr /> @endif
						{{ $message->message }}
					</div>
					<div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt uk-align-center" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
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

	function filterByOwner(){
		var myGrid = UIkit.grid($('#communication-list'), {
			controls: '#message-filters',
			animation: false
		});
		var textinput = $("#filter-by-owner").val();

		@if(Auth::user()->isFromEntity(1))
		$('#filter-by-program').prop('selectedIndex',0);
		@endif
		filterElement(textinput, '.filter_element');
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

	function filterByProgram(){
		var myGrid = UIkit.grid($('#communication-list'), {
			controls: '#message-filters',
			animation: false
		});
		var textinput = $("#filter-by-program").val();
		$('#filter-by-owner').prop('selectedIndex',0);
		filterElement(textinput, '.filter_element');
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


	 	$('#communications-project-search').keydown(function (e) {
	 		if (e.keyCode == 13) {
	 			searchMessages();
	 			e.preventDefault();
	 			return false;
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

	 });
	</script>
