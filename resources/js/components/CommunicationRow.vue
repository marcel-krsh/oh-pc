<template>
	<div :class="{ [message.staffId]: true, [message.programId]:true, [message.hasAttachment]:true, 'filter_element': true, 'uk-width-1-1': true, 'communication-list-item': true }" uk-filter="outbound-phone" :id="message.communicationId" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;" v-on:click.self="openCommunication">
		<div uk-grid :class="{'communication-summary': true, [message.communicationUnread]: true, 'uk-grid':true}">
			<div class="uk-width-1-5@m uk-width-3-6@s communication-item-tt-to-from uk-margin-small-bottom" v-on:click.self="openCommunication">
                <div class="communication-item-date-time">
                    <small v-html="message.createdDate"></small>
                </div>
                <span v-html="message.recipients"></span>
                <div :class="{ [message.userBadgeColor]: true, 'uk-label': true, 'no-text-shadow': true}" :uk-tooltip="message.tooltip" v-if="message.unseen" v-html="message.unseen"></div>
            </div>
		
			<div class="uk-width-1-5@s communication-type-and-who uk-hidden@m uk-text-right " >
	            <div class="uk-margin-right">
	            	<p v-if="message.auditId" style="margin-bottom:0">
	            		<a v-on:click.self="openAudit" class="uk-link-muted" uk-tooltip="OPEN AUDIT" v-html="message.auditId"></a>
	            	</p>
	            	<p v-if="message.auditId" class="uk-visible@m" style="margin-top:0" :uk-tooltip="message.tooltipOrganization">
	            		<small v-html="message.organizationAddress"></small>
	            	</p>
	            </div>
	        </div> 
	        <div class="uk-width-1-5@m communication-item-parcel uk-visible@m">
	            <p v-if="message.auditId" style="margin-bottom:0">
	        		<a v-on:click.self="openAudit" class="uk-link-muted" uk-tooltip="OPEN AUDIT" v-html="message.auditId"></a>
	        	</p>
	        	<p v-if="message.auditId" class="uk-visible@m" style="margin-top:0" :uk-tooltip="message.tooltipOrganization">
	        		<small v-html="message.organizationAddress"></small>
	        	</p>
	        </div>
	        <div class="uk-width-2-5@m uk-width-1-1@s communication-item-excerpt " v-on:click.self="openCommunication">
	        	<div uk-grid class="uk-grid-collapse" v-if="message.hasAttachment == 'attachment-true'">
	                <div class="uk-width-5-6@m uk-width-1-1@s communication-item-excerpt" v-on:click.self="openCommunication">
	                    <span v-if="message.subject" v-html="message.subject"></span><br />
	                    <span v-html="message.summary" style="font-size: 0.9em;"></span>
	                </div>
	                <div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt" v-on:click.self="openCommunication" v-if="message.hasAttachment == 'attachment-true'">
	                    <div class="uk-align-right communication-item-attachment uk-margin-right">
	                        <span :uk-tooltip="message.tooltipFilenames">
	                        	<i class="a-lower"></i>
	                        </span>
	                    </div>
	                </div>
	            </div>
	            <div v-else-if="message.subject">
	            	<span v-if="message.subject" v-html="message.subject"></span><br />
	                <span v-html="message.summary" style="font-size: 0.9em;"></span>
	            </div>
	        </div>
	        <div class="uk-width-1-5@m uk-width-1-1@s communication-type-and-who uk-text-right uk-visible@m" v-on:click.self="openCommunication">
	            <div class="uk-margin-right communication-item-date-time" v-html="message.createdDateRight"></div>
	        </div>
	    </div>
    </div>
</template>

<script>
    export default {
	    props: ['message'],
	    methods: {
            openCommunication(event) {

                if(this.message.parentId){
                    dynamicModalLoad('communication/0/replies/'+this.message.parentId);
                }else{
                    dynamicModalLoad('communication/0/replies/'+this.message.id);
                }
            },
            openAudit(event) {
            	window.open('/viewparcel/'+this.message.auditId, '_blank')
            }
        }
    }
</script>