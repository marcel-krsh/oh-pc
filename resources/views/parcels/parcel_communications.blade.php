<div id="detail-subtabs-content">
	<script>
		// disable infinite scroll:
		window.getContentForListId = 0;
		window.openedCommunication = 0;
		window.currentCommunication = "";
		window.restoreLastCommunicationItem = "";
		window.resetOpenCommunicationId == 0;
	</script>
<script>
	$('.detail-tab-1-text').html('<span class="a-home-2"></span> PARCEL: {{$parcel->parcel_id}} :: Communications ');
	$('#main-option-text').html('Parcel: {{$parcel->parcel_id}}');
	$('#main-option-icon').attr('uk-icon','arrow-circle-o-left');

    var subTabType = window.subTabType;
    if(subTabType == 'communications'){
        delete window.subTabType;
        
        $('#parcel-subtab-1').attr("aria-expaned", "false");
        $('#parcel-subtab-1').removeClass("uk-active");
        $('#parcel-subtab-3').attr("aria-expaned", "true");
        $('#parcel-subtab-3').addClass("uk-active");
    }
</script>
	<div class="uk-container uk-margin-top">
		<div uk-grid class="uk-grid-collapse uk-margin-top">

			<div class="uk-width-1-1 uk-margin-small-top uk-button-group" id="message-filters" data-uk-button-radio="">
				<a class="uk-button uk-button-default filter_link" data-filter="all" aria-checked="false">
					ALL
				</a>
				<a class="uk-button uk-button-default filter_link" data-filter="attachment-true" aria-checked="false">
					<span class="a-paperclip-2" style="font-size:16px; line-height: 17px;"></span>
				</a>
				<div class="uk-button uk-button-default uk-width-1-4 " aria-checked="false">
					<input id="communications-search" name="communications-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-1-1" placeholder="Search Within Messages (press enter)">                                
				</div>
				<!-- display current info - read only -->
				@foreach ($owners_array as $owner)
                <a class="uk-button uk-button-default no-text-shadow user-badge-{{$owner['color']}} uk-dark uk-light filter_link" uk-tooltip="pos:top-left; title:{{$owner['name']}}" data-filter="staff-{{$owner['id']}}">
                    {{$owner['initials']}}
                </a>
                @endforeach
			</div>
		</div>

	</div>
	<!-- start comm list -->

	<div class="uk-container uk-grid-collapse uk-margin-top" id="communication-list" uk-grid style="position: relative; height: 222.5px;">
		@foreach ($messages as $message)
		<div class="filter_element uk-width-1-1 communication-list-item outbound-phone staff-{{ $message->owner->id}} attachment<?php if(count($message->documents)){?>-true<?php } ?>" id="communication-{{ $message->id }}" onclick="dynamicModalLoad('communication/{{$parcel->id}}/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')"  data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
			<div uk-grid class="communication-summary">
				<div class="uk-width-1-5 communication-type-and-who ">
					<span uk-tooltip="pos:top-left;title:{{ $message->owner->name}}">
						<div class="user-badge user-badge-communication-item user-badge-{{ $message->owner->badge_color}} no-float">
							{{ $message->initials}}
						</div>
					</span>
					<span class=" communication-item-date-time">
						{{ date('F d, Y h:i', strtotime($message->created_at)) }}
					</span>
					<div style="clear:both;padding-left: 30px;">
						<small>{{ $message->replies}} @if($message->replies>1)messages @else message @endif / {{ $message->unseen}} unread</small>
					</div>
				</div>
				<div class="uk-width-1-5 communication-item-tt-to-from">
					@if(count($message->recipient_details))
					To: <br />
					@foreach ($message->recipient_details as $recipient)
					{{ $recipient->name }}<br />
					@endforeach
					@endif
				</div>
				<div class="@if(count($message->documents)) uk-width-2-5 @else uk-width-3-5 @endif communication-item-excerpt">
					@if($message->subject)<strong>{{ $message->subject }}</strong><hr /> @endif
					{{ $message->summary}}
				</div>
				@if(count($message->documents))
				<div class="uk-width-1-5">
					<div class="uk-align-right communication-item-attachment uk-margin-right">
						<span uk-tooltip="pos:top-left;title:@foreach($message->documents as $document) {{$document->document->filename}} @endforeach">
						
						<span class="a-paperclip-2"></span>
						
						</span>
					</div>
				</div>
				@endif
			</div>
		</div>
        @endforeach

	</div>
	<div id="detail-tab-bottom-bar" class="uk-vertical-align">
		<table class="action-bar">
			<tbody>
				<tr>
					<td width="3.5%">
					</td>
					<td width="18.6%">
					</td>
					<td width="18.6%">
					</td>
					<td width="18.6%">          
					</td>
					<td width="18.6%">
						<a class="uk-button uk-button-success green-button uk-width-1-1 uk-padding-remove" onclick="dynamicModalLoad('new-outbound-email-entry/{{$parcel->id}}')">
							<span class="a-envelope-4"></span> 
							<span class="a-arrow-right-2_1"></span> 
							<span class="uk-text-small">NEW MESSAGE</span>
						</a>			
					</td>
					<td width="3.5%">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- end comm list -->
	<script>
	//loadCommunications(window.currentDetailId);

	// process search
    $(document).ready(function() {
        $('#communications-search').keydown(function (e) {
          if (e.keyCode == 13) {
            $.post('{{ URL::route("communications.search", $parcel->id) }}', {
                'communications-search' : $("#communications-search").val(),
                '_token' : '{{ csrf_token() }}'
                }, function(data) {
                    if(data!='1'){ 
                        UIkit.modal.alert(data);
                    } else {
                        loadParcelSubTab('communications',{{$parcel->id}});                                                                           
                    }
            } );
            e.preventDefault();
            return false; 
          }
        });

        @if (session()->has('dynamicModalLoad') && session('dynamicModalLoad') != '' )
        
        var dynamicModalLoadid = '';
        $.get( "/session/dynamicModalLoad", function( data ) {
                dynamicModalLoadid = data;
                console.log('Loading Message Id: '+dynamicModalLoadid);

		        if(dynamicModalLoadid != ''){
		        	dynamicModalLoad("communication/{{$parcel->id}}/replies/"+dynamicModalLoadid);
		        }
            });

		@endif

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
    function filterElement(filterVal, filter_element){
        if (filterVal === 'all') {
         $(filter_element).show();
        }
        else {
         // hide all then filter the ones to show
         $(filter_element).hide().filter('.' + filterVal).show();
        }
        UIkit.update(event = 'update');
    }
    function closeOpenMessage(){
		$('.communication-list-item').removeClass('communication-open');
		$('.communication-details').attr('hidden','');
		$('.communication-summary').removeAttr('hidden');
	}
    function openMessage(communicationId){
		closeOpenMessage();
		$("#communication-"+communicationId).addClass('communication-open');
		$("#communication-"+communicationId+"-details").removeAttr('hidden');
		$("#communication-"+communicationId+"-summary").attr('hidden','');
	}


	</script>
</div>