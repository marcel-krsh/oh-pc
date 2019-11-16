<script>
    // disable infinite scroll:
    window.getContentForListId = 0;
    window.openedCommunication = 0;
    window.currentCommunication = "";
    window.restoreLastCommunicationItem = "";
    window.resetOpenCommunicationId == 0;
</script>
<div id="communications_tab">
    <div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">

                <div uk-grid class="uk-grid-collapse uk-visible@m">
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="all">
                        ALL
                    </a>
                    <!-- <a class="uk-button uk-button-default filter_link" data-filter="attachment-true" uk-tooltip="pos:top-left;title:Show results with attachments">
                    <i class="a-paperclip-2"></i>
                    </a> -->
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
                    <input id="communications-search" name="communications-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-1-1 uk-input" placeholder="Search Within Messages Or By Audit ID (press enter)">
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

                <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align:top">
                    <a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-outbound-email-entry/')">
                        <span class="a-envelope-4"></span>
                        <span>NEW MESSAGE</span>
                    </a>
                </div>
    </div>

    @if(count((array)$messages))
    <div uk-grid class="uk-margin-top uk-visible@m">
        <div class="uk-width-1-1">
            <div uk-grid>
                <div class=" uk-width-1-5@m uk-width-1-1@s">
                    <div class="uk-margin-small-left"><small><strong>RECIPIENTS</strong></small></div>
                </div>
                <div class="uk-width-1-5@m uk-width-1-1@s">
                    <div class="uk-margin-small-left"><small><strong>AUDIT</strong></small></div>
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
    <div uk-grid class="uk-container uk-grid-collapse uk-margin-top" id="communication-list" v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-immediate-check="false" infinite-scroll-distance="10" style="position: relative; height: 222.5px;" uk-scrollspy="target:.communication-summary;cls:uk-animation-slide-top-small uk-animation-fast; delay: 100" >
        <communication-row v-if="messages" v-for="message in messages.slice().reverse()" :key="message.id" :message="message"></communication-row>
        <div id="spinner" class="uk-width-1-1" style="text-align:center;"></div>
    </div>
</div>

<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">

</div>

<?php
/*
The following div is defined in this particular tab and pushed to the main layout's footer.
*/
?>
<div id="communications-footer-actions" hidden>
    <a href="#top" id="smoothscrollLink" uk-scroll="{offset: 90}" class="uk-button uk-button-default"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
</div>

<script>
    //loadCommunications(window.currentDetailId);

    $( document ).ready(function() {
        // place tab's buttons on main footer
        $('#footer-actions-tpl').html($('#communications-footer-actions').html());
    });

    function searchMessages(){
        $.post('{{ URL::route("communications.search") }}', {
                'communications-search' : $("#communications-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){
                    UIkit.modal.alert(data);
                } else {
                    $('#detail-tab-2').trigger('click');
                    loadTab('/dashboard/communications/', '2', 0, 0, '', 1);
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
                @if(Auth::user()->isOhfa())
                $('#filter-by-program').prop('selectedIndex',0);
                @endif
               });

        });


@if(Auth::user()->auditor_access())

@endif

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

    @if(Auth::user()->isOhfa())
    $('#filter-by-program').prop('selectedIndex',0);
    @endif

    // filter grid items
    //myGrid.filter(textinput);
    filterElement(textinput, '.filter_element');
}

</script>

    <script>

        new Vue({
            el: '#communication-list',

            data: function() {
                 return {
                    messages: {!! json_encode($data) !!},
                    page: 1,
                    loading: 1,
                    busy: false
                }
            },
            created: function() {
                this.loading = 0;
            },
            methods: {
                loadMore: function () {
                    var self = this;
                    self.busy = true;
                    var tempdiv = '<div uk-spinner style="margin: 20px 0;"></div>';
                    $('#spinner').html(tempdiv);
                    var duplicate = 0;

                    setTimeout(() => {
                        axios.get('dashboard/communications/'+this.page)
                            .then((response) => {
                                $('#spinner').html('');
                                $.each(response.data, function(index, value) {
                                    duplicate = 0;
                                    $.each(self.messages, function(mindex, mvalue) {
                                        if(mvalue.id == value.id){
                                            duplicate = 1;
                                        }
                                    });
                                    if(duplicate == 0){
                                        self.messages.unshift(value);
                                    }
                                });
                            });

                        this.page = this.page + 1;
                        this.busy = false;
                    }, 2500);

                  }
            },

            mounted: function() {
                console.log("Communications Mounted");
                //Echo.private('communications.{{Auth::user()->id}}')
                Echo.private('updates.{{Auth::user()->id}}')
                .listen('UpdateEvent', (payload) => {
                    if(payload.data.event == 'communication'){
                        console.log('Communication Event Received.');
                        // if(data.is_reply){
                        //     console.log("user " + data.userId + " received a new reply for message "+data.id);
                        //     var updateddata = [{
                        //         id: data.id,
                        //         parentId: data.parent_id,
                        //         staffId: data.staff_class,
                        //         programId: data.program_class,
                        //         hasAttachment: data.attachment_class,
                        //         communicationId: data.communication_id,
                        //         communicationUnread: data.communication_unread_class,
                        //         createdDate: data.created,
                        //         createdDateRight: data.created_right,
                        //         recipients: data.recipients,
                        //         userBadgeColor: data.user_badge_color,
                        //         tooltip: data.tooltip,
                        //         unseen: data.unseen,
                        //         auditId: data.audit_id,
                        //         tooltipOrganization: data.tooltip_organization,
                        //         organizationAddress: data.organization_address,
                        //         tooltipFilenames: data.tooltip_filenames,
                        //         subject: data.subject,
                        //         summary: data.summary
                        //     }];
                        //     this.messages = this.messages.map(obj => updateddata.find(o => o.id === obj.id) || obj);
                        // }else{
                        //     console.log("user " + data.userId + " received a new message.");
                        //     this.messages.push({
                        //         id: data.id,
                        //         parentId: data.parent_id,
                        //         staffId: data.staff_class,
                        //         programId: data.program_class,
                        //         hasAttachment: data.attachment_class,
                        //         communicationId: data.communication_id,
                        //         communicationUnread: data.communication_unread_class,
                        //         createdDate: data.created,
                        //         createdDateRight: data.created_right,
                        //         recipients: data.recipients,
                        //         userBadgeColor: data.user_badge_color,
                        //         tooltip: data.tooltip,
                        //         unseen: data.unseen,
                        //         auditId: data.audit_id,
                        //         tooltipOrganization: data.tooltip_organization,
                        //         organizationAddress: data.organization_address,
                        //         tooltipFilenames: data.tooltip_filenames,
                        //         subject: data.subject,
                        //         summary: data.summary
                        //     });
                        // }
                    }
                });

            }
        });

    </script>
    <script>
        window.communicationsLoaded = 1;
    </script>
