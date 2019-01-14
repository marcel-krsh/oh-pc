<template>
	<tr>
		<td :id="'audit-c-1-'+auditIndex" class="uk-text-center audit-td-lead">
        	<span id="audit-avatar-badge-1" v-on:click="openAssignment" :uk-tooltip="audit.tooltipLead" title="" aria-expanded="false" :class="{[audit.userBadgeColor]:true, 'user-badge':true, 'no-float':true, 'uk-link': true }">
				<span v-html="audit.initials"></span>
			</span>
			<span :id="'audit-rid-'+auditIndex"><small>#<span v-html="auditIndex"></span></small></span>
        </td>
        <td :id="'audit-c-2-'+auditIndex" class="audit-td-project">
        	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
        		<span :id="'audit-i-project-detail-'+auditIndex" v-on:click="openProjectDetails" uk-tooltip="pos:top-left;title:VIEW BUILDINGS AND COMMON AREAS;" class="uk-link"><i class="a-menu uk-text-muted"></i></span>
        	</div>
        	<div class="uk-vertical-align-middle uk-display-inline-block">
        		<h3 :id="'audit-project-name-'+auditIndex" class="uk-margin-bottom-remove uk-link filter-search-project" uk-tooltip="title:OPEN AUDIT DETAILS IN TAB;" v-on:click="openProject"><span v-html="audit.projectRef"></span></h3>
            	<small :id="'audit-project-aid-'+auditIndex" class="uk-text-muted faded filter-search-project" uk-tooltip="title:VIEW PROJECT AUDIT DETAILS;">AUDIT <span v-html="audit.id"></span></small>
            </div>
        </td>
        <td class="audit-td-name">
        	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
        		<i class="a-info-circle uk-text-muted uk-link" v-on:click="openContactInfo" uk-tooltip="title:VIEW CONTACT DETAILS;"></i>
        	</div> 
        	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
        		<h3 class="uk-margin-bottom-remove filter-search-pm" v-html="audit.title"></h3>
            	<small class="uk-text-muted faded filter-search-pm" v-html="audit.pm"></small>
        	</div>
        </td>
        <td class="hasdivider audit-td-address">
        	<div class="divider"></div>
        	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
        		<i class="a-marker-basic uk-text-muted uk-link" v-on:click="openMapLink" uk-tooltip="title:VIEW ON MAP;"></i>
        	</div> 
        	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
        		<h3 class="uk-margin-bottom-remove filter-search-address" v-html="audit.address"></h3>
            	<small class="uk-text-muted faded filter-search-address" v-html="audit.address2"></small>
        	</div>
        </td>
        <td class="hasdivider audit-td-scheduled">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-margin-small-top uk-text-center fullwidth" uk-grid>
            	<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
            		<div class="uk-width-1-3">
            			<i :class="{[audit.inspectionStatus]:true, 'use-hand-cursor':true, 'a-mobile-repeat':true}" :uk-tooltip="audit.tooltipInspectionStatus"></i>
            		</div>
            		<div class="uk-width-2-3 uk-padding-remove uk-margin-small-top" v-if="audit.inspectionScheduleDateYear">
	            		<h3 class="uk-link" :uk-tooltip="audit.tooltipInspectionSchedule" v-html="audit.inspectionScheduleDate"></h3>
	            		<div class="dateyear" v-html="audit.inspectionScheduleDateYear"></div>
            		</div>
                    <div class="uk-width-2-3" v-else>
                        <i class="a-calendar-7 action-needed use-hand-cursor" uk-tooltip="Click to schedule audits"></i>
                    </div>
            	</div> 
            	<div class="uk-width-1-6 uk-text-right uk-padding-remove" uk-tooltip="0 UNITS ASSIGNED TO YOU" v-html="audit.inspectableItems+' /'" v-if="audit.inspectableItems < 1 "></div>
                <div class="uk-width-1-6 uk-text-right uk-padding-remove" :uk-tooltip="audit.tooltipInspectableItems" v-html="audit.inspectableItems+' /'" v-if="audit.inspectableItems > 0"></div> 
            	<div class="uk-width-1-6 uk-text-left uk-padding-remove" :uk-tooltip="audit.totalItems + ' TOTAL UNITS WILL BE INSPECTED'" v-html="audit.totalItems"></div> 
            	<div class="uk-width-1-6 uk-text-left">
            		<i :class="{[audit.complianceIconClass]:true, [audit.complianceStatusClass]:true}" :uk-tooltip="audit.tooltipComplianceStatus"></i>
            	</div>
            </div>
        </td>
        <td class="hasdivider audit-td-due">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-margin-small-top uk-text-center fullwidth" uk-grid>
            	<div class="uk-width-1-3">
            		<i :class="{'a-bell-2':true, [audit.followupStatusClass]:true}" :uk-tooltip="audit.tooltipFollowupStatus"></i>
            	</div> 
            	<div class="uk-width-2-3 uk-padding-remove uk-margin-small-top">
            		<div v-if="audit.followupDate">
	            		<h3 class="uk=link" uk-tooltip="title:CLICK TO RESCHEDULE AUDITS;" v-html="audit.followupDate"></h3>
		            	<div class="dateyear" v-html="audit.followupDateYear"></div>
            		</div>
            		<div v-else>
            			<i class="a-calendar-pencil use-hand-cursor" uk-tooltip="title:NEW FOLLOWUP;"></i>
            		</div>
            	</div> 
            </div>
        </td>
        <td class="hasdivider">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.fileAuditStatusClass]:true}" :uk-tooltip="audit.tooltipFileAuditStatus">
            		<i :class="{[audit.fileAuditIconClass]:true}"></i>
            	</div> 
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.nltAuditStatusClass]:true}" :uk-tooltip="audit.tooltipNltAuditStatus">
            		<i :class="{[audit.nltAuditIconClass]:true}"></i>
            	</div> 
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.ltAuditStatusClass]:true}" :uk-tooltip="audit.tooltipLtAuditStatus">
            		<i :class="{[audit.ltAuditIconClass]:true}"></i>
            	</div> 
            </div>
        </td>
        <td class="hasdivider">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
            	<div class="uk-width-1-4">
            		<i  v-on:click="openAssignment" :class="{[audit.auditorStatusIconClass]:true, 'use-hand-cursor':true, [audit.auditorStatusClass]:true}" :uk-tooltip="audit.tooltipAuditorStatus"></i>
            	</div> 
            	<div class="uk-width-1-4">
            		<i :class="{[audit.messageStatusIconClass]:true, 'use-hand-cursor':true, [audit.messageStatusClass]:true,}" :uk-tooltip="audit.tooltipMessageStatus"></i>
            	</div> 
            	<div class="uk-width-1-4">
            		<i :class="{[audit.documentStatusIconClass]:true, 'use-hand-cursor':true, [audit.documentStatusClass]:true,}" :uk-tooltip="audit.tooltipDocumentStatus"></i>
            	</div> 
            	<div class="uk-width-1-4">
            		<i :class="{[audit.historyStatusIconClass]:true, 'use-hand-cursor':true, [audit.historyStatusClass]:true,}" :uk-tooltip="audit.tooltipHistoryStatus"></i>
            	</div> 
            </div>
        </td>
        <td>
        	<div class="uk-margin-top" uk-grid>
        		<div class="uk-width-1-1  uk-padding-remove-top">
            		<i :class="{[audit.stepStatusIconClass]:true, 'use-hand-cursor':true, [audit.stepStatusClass]:true}" :uk-tooltip="audit.tooltipStepStatus" v-on:click="updateStep"></i>
                    <div class="uk-dropdown uk-dropdown-bottom filter-dropdown" uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px;">
                        <form class="update-step" method="post">
                            <fieldset class="uk-fieldset">
                                <div class="uk-margin uk-child-width-auto uk-grid">
                                yo
                                </div>
                                <div class="uk-margin-remove" uk-grid>
                                    <div class="uk-width-1-2">
                                        <button onclick="updateAuditStepSelection(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
                                    </div>
                                    <div class="uk-width-1-2">
                                        <button onclick="$('.update-step').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                        
                    </div>
				</div>
        	</div>
        </td>
	</tr>
</template>

<script>
    export default {
	    props: ['audit','index'],
	    methods: {
            openContactInfo: function() {
                dynamicModalLoad('projects/'+this.audit.projectId+'/contact',0,0,0);
            },
            openProject: function() {
            	loadTab('/projects/'+this.audit.projectKey, '4', 1, 1, '', 1);
            },
            openProjectDetails: function() {
            	projectDetails(this.audit.auditId, this.index, this.audit.total_buildings);
            },
            scheduleAudit: function() {
                loadTab('/projects/'+this.audit.projectRef, '4', 1, 1, '', 1);
            },
            openMapLink: function() {
                window.open(this.mapLink);
            },
            openAssignment: function() {
                dynamicModalLoad('projects/'+this.audit.projectKey+'/assignments/addauditor',1,0,1);
            }
        },
        computed: {
        	auditIndex: function() {
        		return this.index + 1;
        	},
            mapLink: function() {
                return "https://maps.google.com/maps?q="+this.audit.address+"+"+this.audit.address2;
            }
        }
    }
</script>