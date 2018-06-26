<script>
    // disable infinite scroll:
    window.getContentForListId = 0;
    window.openedCommunication = 0;
    window.currentCommunication = "";
    window.restoreLastCommunicationItem = "";
    window.resetOpenCommunicationId == 0;
</script>
<div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">
    
            <div uk-grid class="uk-grid-collapse uk-visible@m">
                <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="all">
                    ALL
                </a>
                <a class="uk-button uk-button-default filter_link" data-filter="attachment-true" uk-tooltip="pos:top-left;title:Show results with attachments">
                <i class="a-paperclip-2"></i>
                </a>
                <a style="display:none" class="uk-button uk-button-default" aria-checked="false" uk-tooltip="pos:top-left;title:Show sent messages">
                <i uk-icon="send" style="font-size:16px; line-height: 17px;"></i>
                </a>
               
            </div>
            <div class="uk-width-1-1@s uk-hidden@m">
                <a class="uk-button uk-button-default filter_link" data-filter="all">
                    ALL
                </a>
                <a class="uk-button uk-button-default filter_link" data-filter="attachment-true" uk-tooltip="pos:top-left; title:Show results with attachments" >
                <i class="a-paperclip-2"></i>
                </a>
                <a style="display:none" class="uk-button uk-button-default" aria-checked="false" uk-tooltip="pos:top-left;title:Show sent messages">
                <i uk-icon="send" style="font-size:16px; line-height: 17px;"></i>
                </a>
               
            </div>
            <div class=" uk-width-1-1@s  uk-width-1-5@m">
                <input id="communications-search" name="communications-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-1-1 uk-input" placeholder="Search Within Messages Or By Parcel ID (press enter)">                                
            </div>
            
            <div class="uk-width-1-1@s uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
                <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterByOwner();">
                    <option value="all" selected="">
                        FILTER BY RECIPIENT 
                    </option>
                    @foreach ($owners_array as $owner)
                    <option value="staff-{{$owner['id']}}"><a class="uk-dropdown-close">{{$owner['name']}}</a></option>    
                    @endforeach
                </select>
                
            </div>
            @if(Auth::user()->isFromEntity(1))
            <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align: top;">
                <select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="filterByProgram();">
                    <option value="all" selected="">
                        FILTER BY PROGRAM 
                        </option>
                        @foreach ($programs as $program)
                        <option value="program-{{$program->id}}"><a  class="uk-dropdown-close">{{$program->program_name}}</a></option>    
                        @endforeach       
                    </select>
            </div>
            @endif
        {{--     <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align:top">
                <a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-outbound-email-entry/')">
                    <span class="a-envelope-4"></span> 
                    <span>NEW MESSAGE</span>
                </a>    
            </div> --}}
</div>

@if(count($messages))
<div uk-grid class="uk-margin-top uk-visible@m">
    <div class="uk-width-1-1">
        <div uk-grid>
            <div class=" uk-width-1-5@m uk-width-1-1@s">
                <div class="uk-margin-small-left"><small><strong>RECIPIENTS</strong></small></div>
            </div>
            <div class="uk-width-1-5@m uk-width-1-1@s">
                <div class="uk-margin-small-left"><small><strong>PARCEL</strong></small></div>
            </div>
            <div class="uk-width-2-5@m uk-width-1-1@s">
                <div class="uk-margin-small-left"><small><strong>SUMMARY</strong></small></div>
            </div>
            <div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
                <div class="uk-margin-right"><small><strong>DATE</strong></small></div>
            </div>
        </div>
    </div>
</div>
@endif
<div uk-grid class="uk-container uk-grid-collapse uk-margin-top" id="communication-list" style="position: relative; height: 222.5px;" uk-scrollspy="target:.communication-summary;cls:uk-animation-slide-top-small uk-animation-fast; delay: 100">
    @if(count($messages))
    @foreach ($messages as $message)

        <div class="filter_element uk-width-1-1 communication-list-item @if($message->owner)staff-{{ $message->owner->id}}@endif @if($message->parcel)program-{{ $message->parcel->program_id}}@endif attachment<?php if(count($message->documents)){?>-true<?php } ?>" uk-filter="outbound-phone" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">

            <div uk-grid class="communication-summary @if($message->unseen) communication-unread @endif">

                @if($message->owner == $current_user)
                <div class="uk-width-1-5@m uk-width-1-2@s communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
                    <div class="communication-item-date-time">
                        <small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
                    </div>
                    @php
                    echo "Me"; //blade spacing issues... grrrrr
                    if(count($message->recipient_details)){
                        foreach ($message->recipient_details as $recipient){
                            if($recipient != $current_user ){
                                echo ", ".$recipient->name;
                            }
                        }
                    }
                    @endphp
                    @if($message->unseen > 0)
                    <div class="uk-label no-text-shadow user-badge-{{ Auth::user()->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen}} unread messages">{{ $message->unseen}}</div>
                    @endif
                </div>
                @else
                <div class="uk-width-1-5@m uk-width-3-6@s communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
                    <div class="communication-item-date-time">
                        <small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
                    </div>
                    @php
                    echo $message->owner->name;
                    if(count($message->recipient_details)){
                        foreach ($message->recipient_details as $recipient){
                            if($recipient != $current_user && $message->owner != $recipient && $recipient->name != ''){
                                echo ", ".$recipient->name;
                            }elseif($recipient == $current_user){
                                echo ", me";
                            }
                        }
                    }
                    @endphp
                    @if($message->unseen > 0)
                    <div class="uk-label no-text-shadow user-badge-{{ Auth::user()->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen}} unread messages">{{ $message->unseen}}</div>
                    @endif
                </div>
                @endif
                <div class="uk-width-1-5@s communication-type-and-who uk-hidden@m uk-text-right " >
                    <div class="uk-margin-right">
                        @if($message->parcel_id && $message->parcel)
                        <p style="margin-bottom:0"><a onclick="window.open('/viewparcel/{{$message->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL">{{ $message->parcel->parcel_id}}</a></p>
                        <p class="uk-visible@m" style="margin-top:0" uk-tooltip="pos:left;title:@if(Auth::user()->isFromEntity(1)) {{$message->parcel->entity->entity_name}} @endif"><small>{{$message->parcel->street_address}},
                        {{$message->parcel->city}}, @if($message->parcel->state){{$message->parcel->state->state_name}} @endif {{$message->parcel->zip}}
                        @if($message->parcel->county)<br />{{$message->parcel->county->county_name}} County @endif
                        </small></p>
                        @endif
                    </div>
                </div>

                <div class="uk-width-1-5@m communication-item-parcel uk-visible@m">
                    @if($message->parcel_id && $message->parcel)
                    <p style="margin-bottom:0"><a onclick="window.open('/viewparcel/{{$message->parcel_id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL">{{ $message->parcel->parcel_id}}</a></p>
                    <p class="uk-visible@m" style="margin-top:0" uk-tooltip="pos:left" title="@if(Auth::user()->isFromEntity(1)) {{$message->parcel->entity->entity_name}} @endif"><small>{{$message->parcel->street_address}},
                    {{$message->parcel->city}}, @if($message->parcel->state){{$message->parcel->state->state_name}} @endif {{$message->parcel->zip}}
                    @if($message->parcel->county)<br />{{$message->parcel->county->county_name}} County @endif
                    </small></p>
                    @endif
                </div>


                <div class="uk-width-2-5@m uk-width-1-1@s communication-item-excerpt " onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >

                    @if(count($message->all_docs))

                    <div uk-grid class="uk-grid-collapse">
                        <div class="uk-width-5-6@m uk-width-1-1@s communication-item-excerpt" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
                            @if($message->subject)<strong>{{$message->subject}}</strong><hr /> @endif
                            {{ $message->summary}}
                        </div>
                        <div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
                            <div class="uk-align-right communication-item-attachment uk-margin-right">
                                <span uk-tooltip="pos:top-left;title:@foreach($message->all_docs as $document) {{$document->document->filename}} @endforeach">

                                <i class="a-lower"></i>

                                </span>
                            </div>
                        </div>
                    </div>

                    @else
                    @if($message->subject)<strong>{{$message->subject}}</strong><br />@endif
                    {{ $message->summary}}
                    @endif

                </div>
                <div class="uk-width-1-5@m uk-width-1-1@s communication-type-and-who uk-text-right uk-visible@m" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
                    <div class="uk-margin-right communication-item-date-time">
                        {{ date("m/d/y", strtotime($message->created_at)) }}<br />
                        {{ date('h:i a', strtotime($message->created_at)) }}
                    </div>
                </div>

            </div>
        </div>
    @endforeach

    {{ $messages->links() }}
    @endif
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>
<script>
    //loadCommunications(window.currentDetailId);

    function searchMessages(){
        $.post('{{ URL::route("communications.search") }}', {
                'communications-search' : $("#communications-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!='1'){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#dash-subtab-10').trigger('click');                                                                          
                }
        } );
    }

    // process search
    $(document).ready(function() {
        $('#communications-search').keydown(function (e) {
          if (e.keyCode == 13) {
            searchMessages();
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
                    dynamicModalLoad("communication/0/replies/"+dynamicModalLoadid);
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

    function filterByOwner(){
        var myGrid = UIkit.grid($('#communication-list'), {
            controls: '#message-filters',
            animation: false
        });
        var textinput = $("#filter-by-owner").val();

        @if(Auth::user()->isFromEntity(1))
        $('#filter-by-program').prop('selectedIndex',0);
        @endif

        // filter grid items
        //myGrid.filter(textinput);
        filterElement(textinput, '.filter_element');
    }   

    @if(Auth::user()->isFromEntity(1))
    function filterByProgram(){
        var myGrid = UIkit.grid($('#communication-list'), {
            controls: '#message-filters',
            animation: false
        });
        var textinput = $("#filter-by-program").val();

        $('#filter-by-owner').prop('selectedIndex',0);

        // filter grid items
        //myGrid.filter(textinput);
        filterElement(textinput, '.filter_element');
    }  
    @endif 

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


    </script>
