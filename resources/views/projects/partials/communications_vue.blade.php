<div id="project-communications" uk-grid>
	<div class="uk-width-1-1">
		<div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">
            <!--
	        <div uk-grid class="uk-grid-collapse uk-visible@m">
	            <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="all">
	                ALL
	            </a>
	            <a class="uk-button uk-button-default filter_link" data-filter="attachment-true" uk-tooltip="pos:top-left;title:Show results with attachments">
	            <i class="a-paperclip-2" style="position: relative;top:8px;"></i>
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
	            <input id="communications-audit-search" name="communications-search" type="text" value="{{ Session::get('communications-search') }}" class="uk-width-1-1 uk-input" placeholder="Search Within Messages (press enter)">
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
	       -->
	        <div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
	            <a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-outbound-email-entry/{{$project->id}}')">
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
</div>

</v-container>
<script>
	function searchProjectMessages(project){
        $.post('{{ URL::route("communications.search") }}', {
                'communications-search' : $("#communications-audit-search").val(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!=1){
                    UIkit.modal.alert(data);
                } else {
                	$('#project-detail-tab-2').trigger('click');
                    loadTab('/projects/'+project+'/communications/', '2', 0, 0, 'project-', 1);
                }
        } );
    }

	$(document).ready(function() {
		$('#communications-audit-search').keydown(function (e) {
	      if (e.keyCode == 13) {
	        searchProjectMessages('{{$project}}');
	        e.preventDefault();
	        return false;
	      }
	    });
	});

    new Vue({
        el: '#project-communications',

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
                    axios.get('/projects/{{$project->id}}/communications/'+this.page)
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
            console.log("initializing vue at the project communications element");
            socket.on('communications.'+uid+'.'+sid+':NewMessage', function(data){
                if(data.is_reply){
                    console.log("user " + data.userId + " received a new reply for message "+data.id);
                    var updateddata = [{
                        id: data.id,
                        parentId: data.parent_id,
                        staffId: data.staff_class,
                        programId: data.program_class,
                        hasAttachment: data.attachment_class,
                        communicationId: data.communication_id,
                        communicationUnread: data.communication_unread_class,
                        createdDate: data.created,
                        createdDateRight: data.created_right,
                        recipients: data.recipients,
                        userBadgeColor: data.user_badge_color,
                        tooltip: data.tooltip,
                        unseen: data.unseen,
                        auditId: data.audit_id,
                        tooltipOrganization: data.tooltip_organization,
                        organizationAddress: data.organization_address,
                        tooltipFilenames: data.tooltip_filenames,
                        subject: data.subject,
                        summary: data.summary
                    }];
                    this.messages = this.messages.map(obj => updateddata.find(o => o.id === obj.id) || obj);
                }else{
                    console.log("user " + data.userId + " received a new message.");
                    this.messages.push({
                        id: data.id,
                        parentId: data.parent_id,
                        staffId: data.staff_class,
                        programId: data.program_class,
                        hasAttachment: data.attachment_class,
                        communicationId: data.communication_id,
                        communicationUnread: data.communication_unread_class,
                        createdDate: data.created,
                        createdDateRight: data.created_right,
                        recipients: data.recipients,
                        userBadgeColor: data.user_badge_color,
                        tooltip: data.tooltip,
                        unseen: data.unseen,
                        auditId: data.audit_id,
                        tooltipOrganization: data.tooltip_organization,
                        organizationAddress: data.organization_address,
                        tooltipFilenames: data.tooltip_filenames,
                        subject: data.subject,
                        summary: data.summary
                    });
                }
            }.bind(this));
        }
    });

</script>