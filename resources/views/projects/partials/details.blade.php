<div id="project-details" uk-grid>
	<div id="project-details-general" class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-2-3">
				<h3>{{$stats['project_name']}}</h3>
				Project #: {{$stats['project_id']}}
			</div>
			<div class="uk-width-1-3">
				Last Audit Completed: {{$stats['last_audit_completed']}}<br />
				Next Audit Due By: {{$stats['next_audit_due']}}<br />
				Current Project Score : {{$stats['score_percentage']}} / {{$stats['score']}}
			</div>
		</div>
	</div>
	<div id="project-details-stats" class="uk-width-1-1" style="margin-top:20px;">
		<div uk-grid>
			<div class="uk-width-1-3">
				<ul class="leaders" style="margin-right:30px;">
					<li><span>Total Buildings</span> <span>{{$stats['total_building']}}</span></li>
					<li><span class="indented">Total Building Common Areas</span> <span>{{$stats['total_building_common_areas']}}</span></li>
					<li><span>Total Project Common Areas</span> <span>{{$stats['total_project_common_areas']}}</span></li>
					<li><span>Total Units</span> <span>{{$stats['total_units']}}</span></li>
					<li><span class="indented">• Market Rate</span> <span>{{$stats['market_rate']}}</span></li>
					<li><span class="indented">• Subsidized</span> <span>{{$stats['subsidized']}}</span></li>
					<li><span>Total Programs</span> <span>{{count($stats['programs'])}}</span></li>
					@foreach($stats['programs'] as $program) 
					<li><span class="indented">• {{$program['name']}}</span> <span>{{$program['units']}}</span></li>
					@endforeach
				</ul>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>OWNER: {{$owner['name']}}</strong></h5>
				<div class="address">
					<i class="a-avatar"></i> {{$owner['poc']}}<br />
					<i class="a-phone-5"></i> {{$owner['phone']}} <i class="a-fax-2" style="margin-left:10px"></i> {{$owner['fax']}} <br />
					<i class="a-mail-send"></i> {{$owner['email']}}<br />
					<i class="a-mailbox"></i> {{$owner['address']}}<br />{{$owner['address2']}}<br />{{$owner['city']}} {{$owner['state']}} {{$owner['zip']}}
				</div>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>Managed By: {{$manager['name']}}</strong></h5>
				<div class="address">
					<i class="a-avatar"></i> {{$manager['poc']}}<br />
					<i class="a-phone-5"></i> {{$manager['phone']}} <i class="a-fax-2" style="margin-left:10px"></i> {{$manager['fax']}} <br />
					<i class="a-mail-send"></i> {{$manager['email']}}<br />
					<i class="a-mailbox"></i> {{$manager['address']}}<br />{{$manager['address2']}}<br />{{$manager['city']}} {{$manager['state']}}, {{$manager['zip']}}
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
<div id="project-details-buttons" class="" uk-grid>
	<div class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4">
						<button id="project-details-button-1" class="uk-button uk-link action-needed active" onclick="projectDetailsInfo({{$stats['project_id']}}, 'compliance');" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-2" class="uk-button uk-link critical" onclick="projectDetailsInfo({{$stats['project_id']}}, 'assignment');" type="button"><i class="a-avatar-fail"></i> ASSIGNMENT</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-3" class="uk-button uk-link action-required" onclick="projectDetailsInfo({{$stats['project_id']}}, 'findings');" type="button"><i class="a-mobile-info"></i> FINDINGS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-4" class="uk-button uk-link ok-actionable" onclick="projectDetailsInfo({{$stats['project_id']}}, 'followups');" type="button"><i class="a-bell-ring"></i> FOLLOW-UPS</button>
					</div>
				</div>
			</div>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4">
						<button id="project-details-button-5" class="uk-button uk-link in-progress" onclick="projectDetailsInfo({{$stats['project_id']}}, 'reports');" type="button"><i class="a-file-chart-3"></i> REPORTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-6" class="uk-button uk-link no-action" onclick="projectDetailsInfo({{$stats['project_id']}}, 'documents');" type="button"><i class="a-file-clock"></i> DOCUMENTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-7" class="uk-button uk-link" onclick="projectDetailsInfo({{$stats['project_id']}}, 'comments');" type="button"><i class="a-comment-text"></i> COMMENTS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-8" class="uk-button uk-link" onclick="projectDetailsInfo({{$stats['project_id']}}, 'photos');" type="button"><i class="a-picture"></i> PHOTOS</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details-info-container"></div>

<div id="project-details-buildings-container"></div>

<script>

function loadProjectDetailsBuildings(id, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	$('#project-details-buildings-container').html(tempdiv);

	var url = '{{route("audit.buildings", ["audit" => "xi", "target" => "ti"])}}';
	url = url.replace('xi', id);
	url = url.replace('ti', target);
    $.get(url, {
        '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data=='0'){ 
                UIkit.modal.alert("There was a problem getting the buildings' information.");
            } else {
            	var newdiv = '<div uk-grid><div id="auditstable" class="uk-width-1-1 uk-overflow-auto"><table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="min-width: 1420px;"><tr>';
            	newdiv = newdiv + data;
            	newdiv = newdiv + '</tr></table></div></div>'
				$('#project-details-buildings-container').html(newdiv);
        	}
    });
}

function projectDetailsInfo(id, type) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	$('#project-details-info-container').html(tempdiv);

	var url = '{{route("project.details.info", ["project" => "xi", "type" => "ti"])}}';
	url = url.replace('xi', id);
	url = url.replace('ti', type);
    $.get(url, {
        '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data=='0'){ 
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	
				$('#project-details-info-container').html(data);
        	}
    });
}

$( document ).ready(function() {
	if($('#project-details-info-container').html() == ''){
		$('#project-details-button-1').trigger("click");
	}	
	loadProjectDetailsBuildings( {{ $stats['project_id'] }}, {{ $stats['project_id'] }} ) ;
});
</script>
	    