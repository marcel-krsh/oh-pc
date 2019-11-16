<template>
	<tr>
		<td :id="'audit-c-1-'+audit.auditId" class="uk-text-center audit-td-lead use-hand-cursor">
        	<span :id="'audit-avatar-badge-'+audit.auditId" v-on:click="openAssignment" :uk-tooltip="audit.tooltipLead" title="" aria-expanded="false" :class="{[audit.userBadgeColor]:true, 'user-badge-v2':true, 'no-float':true, 'uk-link': true }">
				<span v-html="audit.initials"></span>
			</span>
			<span :id="'audit-rid-'+audit.auditId" style="position: relative; top: 27px; left: -15px;"><small>#<span v-html="auditIndex"></span></small></span>
        </td>
        <td :id="'audit-c-2-'+audit.auditId" class="audit-td-project">
        	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
        		<span :id="'audit-i-project-detail-'+audit.auditId" v-on:click="openProjectDetails" uk-tooltip="pos:top-left;title:VIEW BUILDINGS AND COMMON AREAS;" class="uk-link"><i class="a-menu uk-text-muted"></i></span>
        	</div>
        	<div class="uk-vertical-align-middle uk-display-inline-block use-hand-cursor"  v-on:click="openProjectDetails" >
        		<h3 :id="'audit-project-name-'+audit.auditId" class="uk-margin-bottom-remove uk-link filter-search-project" uk-tooltip="title:VIEW BUILDINGS AND COMMON AREAS;"><span v-html="audit.projectRef"></span></h3>
            	<small :id="'audit-project-aid-'+audit.auditId" class="uk-text-muted faded filter-search-project" uk-tooltip="title:VIEW BUILDINGS AND COMMON AREAS;">AUDIT <span v-html="audit.auditId"></span></small>
            </div>
        </td>
        <td class="audit-td-name ">
        	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
        		<i class="a-info-circle uk-text-muted uk-link" v-on:click="openContactInfo" uk-tooltip="title:VIEW CONTACT DETAILS;"></i>
        	</div>
        	<div class="uk-vertical-align-top uk-display-inline-block fadetext use-hand-cursor"  v-on:click="openProject" uk-tooltip="title:VIEW PROJECT DETAILS;">
        		<h3 class="uk-margin-bottom-remove filter-search-pm" v-html="audit.title"></h3>
            	<small class="uk-text-muted faded filter-search-pm" v-html="audit.pm"></small>
        	</div>
        </td>
        <td class="hasdivider audit-td-address uk-text-truncate">
        	<div class="divider"></div>
        	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
        		<i class="a-marker-basic uk-text-muted uk-link" v-on:click="openMapLink" uk-tooltip="title:VIEW ON MAP;"></i>
        	</div>
        	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext" v-on:click="openMapLink">
        		<h3 class="uk-margin-bottom-remove filter-search-address" v-html="audit.address"></h3>
            	<small class="uk-text-muted faded filter-search-address" v-html="audit.address2"></small>
        	</div>
        </td>
        <td class="hasdivider audit-td-scheduled">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-margin-small-top uk-text-center fullwidth" uk-grid>
            	<div class="uk-width-1-1 uk-padding-remove-top" v-bind:class="{'uk-width-1-2' : audit.auditor_access}" uk-grid>
            		
            		<div class="uk-width-1-2" v-if="audit.inspectionScheduleDateYear" v-on:click="openAssignment">
	            		<h3 class="uk-link" :uk-tooltip="audit.tooltipInspectionSchedule" v-html="audit.inspectionScheduleDate"></h3>
	            		<div class="dateyear" v-html="audit.inspectionScheduleDateYear"></div>
            		</div>
                    <div class="uk-width-1-2" v-on:click="openAssignment" v-else>
                        <i class="a-calendar-7 action-needed use-hand-cursor" uk-tooltip="Click to schedule audits"></i>
                    </div>
            	</div>

                	<div class="uk-width-1-6 uk-text-right uk-padding-remove" style="margin-top: -4px;" uk-tooltip="0 UNITS ASSIGNED TO YOU" v-html="audit.buildingCount+' /'" v-if="audit.auditor_access && audit.unitCount < 1 "></div>
                    <div class="uk-width-1-6 uk-text-right uk-padding-remove" style="margin-top: -4px;" :uk-tooltip="audit.tooltipInspectableItems" v-html="audit.inspectableItems+' /'" v-if="audit.auditor_access && audit.inspectableItems > 0"></div>

                	<div v-if="audit.auditor_access" class="uk-width-1-6 uk-text-left uk-padding-top-remove" style="margin-top: -4px;" :uk-tooltip="audit.totalItems + ' TOTAL BUILDINGS PROJECT AMENITIES AND UNITS THAT WILL BE INSPECTED'" v-html="audit.totalItems"></div>
                	

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
	            		<h3 class="uk=link" uk-tooltip="title: CLICK TO VIEW FOLLOW-UP;" v-html="audit.followupDate"></h3>
		            	<div class="dateyear" v-html="audit.followupDateYear"></div>
            		</div>
            		<div v-else>
            			<i v-if="audit.auditor_access" class="a-calendar-pencil use-hand-cursor" uk-tooltip="title:NEW FOLLOWUP;"></i>
            		</div>
            	</div>
            </div>
        </td>
        <td class="hasdivider">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.fileAuditStatusClass]:true}" :uk-tooltip="audit.tooltipFileAuditStatus" v-on:click="openFindings(this, audit.auditId, null, null, 'file')">
            		<i :class="{[audit.fileAuditIconClass]:true}"></i>
            	</div>
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.nltAuditStatusClass]:true}" :uk-tooltip="audit.tooltipNltAuditStatus" v-on:click="openFindings(this, audit.auditId, null, null, 'nlt')">
            		<i :class="{[audit.nltAuditIconClass]:true}"></i>
            	</div>
            	<div :class="{'uk-width-1-3':true, 'use-hand-cursor':true, [audit.ltAuditStatusClass]:true}" :uk-tooltip="audit.tooltipLtAuditStatus"  v-on:click="openFindings(this, audit.auditId, null, null, 'lt')" >
            		<i :class="{[audit.ltAuditIconClass]:true}"></i>
            	</div>
            </div>
        </td>
        <td class="hasdivider">
        	<div class="divider"></div>
        	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
                <div v-if="audit.auditor_access" class="uk-width-1-4 uk-text-left">
                        <i :class="{[audit.complianceIconClass]:true, [audit.complianceStatusClass]:true}" :uk-tooltip="audit.tooltipComplianceStatus" v-on:click="rerunCompliance"></i>
                    </div>
            	<div class="uk-width-1-4">
            		<i  v-on:click="openAssignment" :class="{[audit.auditorStatusIconClass]:true, 'use-hand-cursor':true, [audit.auditorStatusClass]:true}" :uk-tooltip="audit.tooltipAuditorStatus"></i>
            	</div>
            	<div class="uk-width-1-4">
            		<i :class="{[audit.messageStatusIconClass]:true, 'use-hand-cursor':true, [audit.messageStatusClass]:true,}" :uk-tooltip="audit.tooltipMessageStatus"></i>
            	</div>
            	<div class="uk-width-1-4">
            		<i :class="{[audit.documentStatusIconClass]:true, 'use-hand-cursor':true, [audit.documentStatusClass]:true,}" :uk-tooltip="audit.tooltipDocumentStatus"></i>
            	</div>

            </div>
        </td>
        <td>
        	<div class="uk-margin-top" uk-grid>
        		<div class="uk-width-1-1  uk-padding-remove-top">
            		<i :class="{[audit.stepStatusIconClass]:true, 'use-hand-cursor':true, [audit.stepStatusClass]:true}" :uk-tooltip="audit.tooltipStepStatus" v-on:click="updateStep"></i>
				</div>
        	</div>
        </td>
	</tr>
</template>

<script>
    export default {
	    props: ['audit','index'],
	    methods: {
            openFindings: function (element, auditid, buildingid, unitid='null', type='null',amenity='null') {
                dynamicModalLoad('findings/'+type+'/audit/'+auditid+'/building/'+buildingid+'/unit/'+unitid+'/amenity/'+amenity,1,0,1);
            },
            rerunCompliance: function() {
                rerunCompliance(this.audit.auditId);
            },
            updateStep: function() {
                dynamicModalLoad('audits/'+this.audit.auditId+'/updateStep',0,0,0);
            },
            openContactInfo: function() {
                dynamicModalLoad('projects/'+this.audit.projectId+'/contact',0,0,0);
            },
            openProject: function() {
            	loadTab('/projects/view/'+this.audit.projectKey+'/'+this.audit.auditId, '4', 1, 1, '', 1, this.audit.auditId);
            },
            openProjectDetails: function() {
            	projectDetails(this.audit.auditId, this.audit.auditId, this.audit.total_buildings);
            },
            scheduleAudit: function() {
                loadTab('/projects/view/'+this.audit.projectRef+'/'+this.audit.auditId, '4', 1, 1, '', 1, this.audit.auditId);
            },
            openMapLink: function() {
                window.open(this.mapLink);
            },
            openAssignment: function() {
                loadTab('/projects/view/'+this.audit.projectKey+'/'+this.audit.auditId, '4', 1, 1, '', 1, this.audit.auditId);
                // dynamicModalLoad('projects/'+this.audit.projectKey+'/assignments/addauditor',1,0,1);
            }
        },
        computed: {
        	auditIndex: function() {
        		return this.index + 1;
                // return this.audit.auditId;
        	},
            mapLink: function() {
                return "https://maps.google.com/maps?q="+this.audit.address+"+"+this.audit.address2;
            }
        }
    }
</script>