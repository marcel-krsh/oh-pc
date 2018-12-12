<div id="project-details" uk-grid>
	<div id="project-details-general" class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-2-3">
				<h3>{{$details['project_name']}}</h3>
				Project #: {{$details['project_id']}}
			</div>
			<div class="uk-width-1-3">
				Last Audit Completed: {{$details['last_audit_completed']}}<br />
				Next Audit Due By: {{$details['next_audit_due']}}<br />
				Current Project Score : {{$details['score_percentage']}} / {{$details['score']}}
			</div>
		</div>
	</div>
	<div id="project-details-stats" class="uk-width-1-1" style="margin-top:20px;">
		<div uk-grid>
			<div class="uk-width-1-3">
				<ul class="leaders" style="margin-right:30px;">
					<li><span>Total Buildings</span> <span>{{$details['total_building']}}</span></li>
					<li><span class="indented">Total Building Common Areas</span> <span>{{$details['total_building_common_areas']}}</span></li>
					<li><span>Total Project Common Areas</span> <span>{{$details['total_project_common_areas']}}</span></li>
					<li><span>Total Units</span> <span>{{$details['total_units']}}</span></li>
					<li><span class="indented">• Market Rate</span> <span>{{$details['market_rate']}}</span></li>
					<li><span class="indented">• Subsidized</span> <span>{{$details['subsidized']}}</span></li>
					<li><span>Total Programs</span> <span>{{count($details['programs'])}}</span></li>
					@foreach($details['programs'] as $program) 
					<li><span class="indented">• {{$program['name']}}</span> <span>{{$program['units']}}</span></li>
					@endforeach
				</ul>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>OWNER: {{$details['name']}}</strong></h5>
				<div class="address">
					<i class="a-avatar"></i> {{$details['poc']}}<br />
					<i class="a-phone-5"></i> {{$details['phone']}} <i class="a-fax-2" style="margin-left:10px"></i> {{$details['fax']}} <br />
					<i class="a-mail-send"></i> {{$details['email']}}<br />
					<i class="a-mailbox"></i> {{$details['address']}}<br />{{$details['address2']}}<br />{{$details['city']}} {{$details['state']}} {{$details['zip']}}
				</div>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>Managed By: {{$details['name']}}</strong></h5>
				<div class="address">
					<i class="a-avatar"></i> {{$details['poc']}}<br />
					<i class="a-phone-5"></i> {{$details['phone']}} <i class="a-fax-2" style="margin-left:10px"></i> {{$details['fax']}} <br />
					<i class="a-mail-send"></i> {{$details['email']}}<br />
					<i class="a-mailbox"></i> {{$details['address']}}<br />{{$details['address2']}}<br />{{$details['city']}} {{$details['state']}}, {{$details['zip']}}
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="ok-actionable">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-2 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;">
									<i class="a-square-right-2"></i>
								</div>
								<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
									<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:Brian Greenwood;" title="" aria-expanded="false" class="user-badge user-badge-blue no-float uk-link">
										BG
									</span>
								</div>
								<div class="uk-width-3-5" style="padding-right:0">
									<h3 id="audit-project-name-1" class="uk-margin-bottom-remove uk-link" uk-tooltip="title:Open Audit Details in Tab;">19200114</h3>
					            	<small class="uk-text-muted" uk-tooltip="title:View Project's Audit Details;" style="font-size: 0.7em;">AUDIT 2015697</small>
								</div>
							</div>
						</div>
						<div class="uk-width-4-5">
							<div uk-grid>
								<div class="uk-width-1-2 uk-padding-remove">
									<div class="uk-vertical-align-top uk-display-inline-block fadetext">
					            		<h3 class="uk-margin-bottom-remove">Great American Apartments</h3>
						            	<small class="uk-text-muted">THE NOT SO LONG PROPERTY MANAGER NAME</small>
					            	</div>
								</div>
								<div class="uk-width-1-2 uk-padding-remove">
					            	<div class="divider"></div>
									<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
					            		<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;"></i>
					            	</div> 
					            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad hasdivider fadetext">
					            		<h3 class="uk-margin-bottom-remove">3045 Cumberland Woods Street, Suite 202</h3>
						            	<small class="uk-text-muted">COLUMBUS, OH 43219</small>
					            	</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-1-2 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-3 uk-padding-remove">
						            <div class="divider"></div>
									<div class="uk-text-center hasdivider" uk-grid>
						            	<div class="uk-width-1-2 uk-padding-remove" uk-grid>
						            		<div class="uk-width-1-3 iconpadding">
						            			<i class="a-mobile-repeat action-needed" uk-tooltip="title:Inspection in progress;"></i>
						            		</div>
						            		<div class="uk-width-2-3 uk-padding-remove">
							            		<h3 class="uk-link uk-margin-remove" uk-tooltip="title:Click to reschedule audits;">12/21</h3>
							            		<div class="dateyear">2018</div>
						            		</div>
						            	</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-right">0* /</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-left">72</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-left">
						            		<i class="a-circle-checked ok-actionable"  uk-tooltip="title:Audit Compliant;"></i>
						            	</div>
						            </div>
								</div>
								<div class="uk-width-1-3 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div class="uk-width-1-3">
						            		<i class="a-bell-2" uk-tooltip="title:No followups;"></i>
						            	</div> 
						            	<div class="uk-width-2-3">
						            		<i class="a-calendar-pencil" uk-tooltip="title:New followup;"></i>
						            	</div> 
						            </div>
								</div>
							</div>
						</div>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div class="uk-width-1-4">
						            		<i class="a-star-3"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-star-3"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-star-3"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-star-3"></i>
						            	</div> 
						            </div>
								</div>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div class="uk-width-1-4">
						            		<i class="a-avatar action-needed" uk-tooltip="title:Auditors / schedule conflicts / unasigned items;"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-envelope-4 action-required" uk-tooltip="title:;"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-files ok-actionable" uk-tooltip="title:Document status;"></i>
						            	</div> 
						            	<div class="uk-width-1-4">
						            		<i class="a-person-clock" uk-tooltip="title:NO/VIEW HISTORY;"></i>
						            	</div> 
						            </div>
								</div>
								<div class="uk-width-1-5 iconpadding">
									<i class="a-calendar-7"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="project-details-buttons" class="project-details-buttons" uk-grid>
	<div class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4">
						<button id="project-details-button-1" class="uk-button uk-link ok-actionable active" onclick="projectDetailsInfo({{$details['project_id']}}, 'compliance', this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-2" class="uk-button uk-link critical" onclick="projectDetailsInfo({{$details['project_id']}}, 'assignment', this);" type="button"><i class="a-avatar-fail"></i> ASSIGNMENT</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-3" class="uk-button uk-link action-required" onclick="projectDetailsInfo({{$details['project_id']}}, 'findings', this);" type="button"><i class="a-mobile-info"></i> FINDINGS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-4" class="uk-button uk-link action-needed" onclick="projectDetailsInfo({{$details['project_id']}}, 'followups', this);" type="button"><i class="a-bell-ring"></i> FOLLOW-UPS</button>
					</div>
				</div>
			</div>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4">
						<button id="project-details-button-5" class="uk-button uk-link in-progress" onclick="projectDetailsInfo({{$details['project_id']}}, 'reports', this);" type="button"><i class="a-file-chart-3"></i> REPORTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-6" class="uk-button uk-link no-action" onclick="projectDetailsInfo({{$details['project_id']}}, 'documents', this);" type="button"><i class="a-file-clock"></i> DOCUMENTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-7" class="uk-button uk-link" onclick="projectDetailsInfo({{$details['project_id']}}, 'comments', this);" type="button"><i class="a-comment-text"></i> COMMENTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-8" class="uk-button uk-link" onclick="projectDetailsInfo({{$details['project_id']}}, 'photos', this);" type="button"><i class="a-picture"></i> PHOTOS</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details-info-container"></div>

<div id="project-details-buildings-container"></div>

<script>
	var chartColors = {
		  required: '#191818',
		  selected: '#0099d5',
		  needed: '#d31373',
		  inspected: '#21a26e',
		  tobeinspected: '#e0e0df'
		};
	Chart.defaults.global.legend.display = false;
    Chart.defaults.global.tooltips.enabled = true;

    // THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE  - to make this simple - this tab is reloaded on form submission of new payment/ payment edits //
    var summaryOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke : false,
        legendPosition : 'bottom',

        "cutoutPercentage":40,
			"legend" : {
				"display" : false
			},
			"responsive" : true,
			"maintainAspectRatio" : false,

        //String - The colour of each segment stroke
        segmentStrokeColor : "#fff",

        //Number - The width of each segment stroke
        segmentStrokeWidth : 0,

        //The percentage of the chart that we cut out of the middle.
        // cutoutPercentage : 67,

        easing: "linear",

        duration: 100000,

        tooltips: {
            enabled: true,
            mode: 'single',
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index];
                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    return label + ': ' + addCommas(datasetLabel) + ' units' ;
                }
            }
        }


    }
    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>

<script>



$( document ).ready(function() {
	if($('#project-details-info-container').html() == ''){
		$('#project-details-button-1').trigger("click");
	}	
	loadProjectDetailsBuildings( {{ $details['project_id'] }}, {{ $details['project_id'] }} ) ;
});
</script>
	    