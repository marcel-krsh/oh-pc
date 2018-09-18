<script>
    // disable infinite scroll:
    // window.getContentForListId = 0;
    // window.openedCommunication = 0;
    // window.currentCommunication = "";
    // window.restoreLastCommunicationItem = "";
    // window.resetOpenCommunicationId == 0;
</script>
@if(Request::query('page')<2)
<div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">
    <div class="uk-button uk-button-default uk-width-1-1@s uk-width-1-4@m" aria-checked="false">
        <input id="communications-search" name="communications-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-1-1" placeholder="Search Within Messages (press enter)">                                
    </div>
    @if(Auth::user()->isFromEntity(1))
    <div class="uk-width-1-1@s  uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
        <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterByOwner();">
            <option value="" selected="">
                FILTER BY RECIPIENT
            </option>
            @foreach ($owners_array as $owner)
            <option value="staff-{{$owner['id']}}"><a class="uk-dropdown-close">{{$owner['name']}}</a></option>    
            @endforeach
        </select>
        
    </div>
    @endif
</div>

@if(count($messages))
<div uk-grid class="uk-grid-collapse uk-margin-top uk-visible@m">
    <div class="uk-width-1-1">
        <div uk-grid class="uk-child-width-1-4">
            <div class="">
                <div class="uk-margin-small-left"><small><strong>RECIPIENTS</strong></small></div>
            </div>
            <div class="">
                <div class="uk-margin-small-left"><small><strong>TYPE</strong></small></div>
            </div>
            <div class="">
                <div class="uk-margin-small-left"><small><strong>EMAIL SUBJECT</strong></small></div>
            </div>
            <div class=" uk-text-right">
                <div class="uk-margin-right"><small><strong>DATE</strong></small></div>
            </div>
        </div>
    </div>
</div>
@endif
<div uk-grid class="uk-grid-collapse uk-margin-top communication-list" id="results-list" data-uk-grid="{controls: '#message-filters', animation: false}" style="position: relative; height: 222.5px;">
@endif
    @if(count($messages))
    @foreach ($messages as $message)
    <div uk-grid class="uk-width-1-1 communication-list-item" data-uk-filter="outbound-phone,staff-{{ $message->recipient->id}}" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
        
        <div uk-grid class="uk-width-1-1 uk-child-width-1-4 communication-summary">
            @if($message->recipient == $current_user)
            <div class="communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('email_history/{{ $message->id }}')" >
                <div class="communication-item-date-time uk-hidden@s">
                    <small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
                </div>
                Me
            </div>
            @else
            <div class="communication-item-tt-to-from uk-margin-small-bottom" onclick="dynamicModalLoad('email_history/{{ $message->id }}')" >
                <div class="communication-item-date-time uk-hidden@s">
                    <small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
                </div>
                @php 
                echo $message->recipient->name; 
                if($message->recipient->isFromEntity(1)){
                    echo " [HFA]";
                }
                @endphp 
            </div>
            @endif

            <div class="communication-type-and-who uk-hidden@m " >
                <div class="uk-margin-right">
                    @if($message->typeInfo()[0])
                @if($message->typeInfo()[0]->parcel_id)
                <p style="margin-bottom:0">Parcel <a onclick="window.open('{{$message->typeInfo()[3]}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL">{{ $message->typeInfo()[0]->parcel_id}}</a></p>
                    
                @if($message->typeInfo()[1] == 'file')
                <a href="{{ $message->typeInfo()[2] }}" target="_blank"  uk-tooltip="Download file {{ $$message->typeInfo()[0]->filename }}">
                    <i uk-icon="download"></i>
                </a>
                @endif

                @if($message->typeInfo()[1] == 'disposition')
                Disposition <a class="uk-link-muted" onclick="window.open('{{$message->typeInfo()[3]}}', '_blank')" uk-tooltip="OPEN DISPOSITION">{{$message->typeInfo()[0]->id}}</a> 
                @endif

                @endif
                
                @if($message->typeInfo()[1] == 'invoice')
                Invoice <a class="uk-link-muted" onclick="window.open('/invoices/{{$message->typeInfo()[0]->id}}', '_blank')" style="cursor:pointer;">{{$message->typeInfo()[0]->id}}</a>
                @endif

                @endif
                </div>
            </div>
            <div class="communication-item-parcel uk-visible@m">
                @if($message->typeInfo()[0])
                @if($message->typeInfo()[0]->parcel_id)
                <p style="margin-bottom:0">Parcel <a onclick="window.open('{{$message->typeInfo()[3]}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL">{{ $message->typeInfo()[0]->parcel_id}}</a></p>
                    
                @if($message->typeInfo()[1] == 'file')
                <a href="{{ $message->typeInfo()[2] }}" target="_blank"  uk-tooltip="Download file {{ $$message->typeInfo()[0]->filename }}">
                    <i uk-icon="download"></i>
                </a>
                @endif

                @if($message->typeInfo()[1] == 'disposition')
                Disposition <a class="uk-link-muted" onclick="window.open('{{$message->typeInfo()[3]}}', '_blank')" uk-tooltip="OPEN DISPOSITION">{{$message->typeInfo()[0]->id}}</a> 
                @endif

                @endif
                
                @if($message->typeInfo()[1] == 'invoice')
                Invoice <a class="uk-link-muted" onclick="window.open('/invoices/{{$message->typeInfo()[0]->id}}', '_blank')" style="cursor:pointer;">{{$message->typeInfo()[0]->id}}</a>
                @endif

                @if($message->typeInfo()[1] == 'po')
                PO <a class="uk-link-muted" onclick="window.open('{{$message->typeInfo()[2]}}', '_blank')" uk-tooltip="OPEN PO">{{$message->typeInfo()[0]->id}}</a> 
                @endif

                @if($message->typeInfo()[1] == 'request')
                Request <a class="uk-link-muted" onclick="window.open('{{$message->typeInfo()[2]}}', '_blank')" uk-tooltip="OPEN PO">{{$message->typeInfo()[0]->id}}</a> 
                @endif

                @endif
            </div>

            <div class="communication-item-excerpt" onclick="dynamicModalLoad('email_history/{{ $message->id }}')" >
            
                {{ $message->summary}}

            </div>
            <div class="communication-type-and-who uk-text-right uk-visible@m" onclick="dynamicModalLoad('email_history/@if($message->parent_id){{ $message->parent_id }} @else{{ $message->id }} @endif')" >
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

@if(Request::query('page')<2)
</div>
<div id="results-pagination">
<a name="bottom"></a>
</div>
<script>
    //loadCommunications(window.currentDetailId);

    function searchMessages(){
        $.post('{{ URL::route("emails.search") }}', {
                'communications-search' : $("#communications-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!='1'){ 
                    UIkit.modal.alert(data);
                } else {
                    $('#emails-tab').trigger('click');                                                                          
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
                    dynamicModalLoad("email_history/"+dynamicModalLoadid);
                }
            });

        @endif

        
        
    });

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
        myGrid.filter(textinput);
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


    </script>
@endif