<style type="text/css">
	.on-phone-pd {
		position: relative;
		left: -4px;
		top: -28px;
		font-size: 0.95rem;
		font-weight: bolder;
	}

	.finding-number-pd {
		font-size: 15px;
		background: #666;
		padding: 0px 4px 0px;
		border: 0px;
		min-width: 24px;
		max-height: 24px;
		line-height: 1.5;
	}
	.manager-fail {
		color:red;
	}
	#detail-tab-4-content.p, #detail-tab-4-content.div, #detail-tab-4-content {
		font-size: 13pt;
		line-height: 26px;
	}
	#detail-tab-4-content.h1 {
		font-size: 24pt;
		line-height: 32px;
	}
	#detail-tab-4-content.h2 {
		font-size: 20pt;
		line-height: 28px;
	}
	#detail-tab-4-content.h3 {
		font-size: 16pt
		line-height: 24px;
	}
	#detail-tab-4-content.h4,#detail-tab-4-content.h5 {
		font-size: 14pt;
		line-height: 22px;
	}
	#project-details-main-row .pd-findings-column i  {
		font-size: 36px;
		line-height: 37px;
	}

</style>

<?php

// if ($selected_audit->update_cached_audit()) {
// 	$selected_audit->refresh();
// }
if (in_array($selected_audit->step_id, $pmCanViewFindingsStepIds)) {
	$fileCount = $selected_audit->file_findings_count;
	$correctedFileCount = $fileCount - $selected_audit->unresolved_file_findings_count;
	$nltCount = $selected_audit->nlt_findings_count;
	$correctedNltCount = $nltCount - $selected_audit->unresolved_nlt_findings_count;

	$ltCount = $selected_audit->lt_findings_count;
	$correctedLtCount = $fileCount - $selected_audit->unresolved_lt_findings_count;
} else {

	$fileCount = "NA";
	$correctedFileCount = "NA";
	$nltCount = "NA";
	$correctedNltCount = "NA";

	$ltCount = "NA";
	$correctedLtCount = "NA";
}

$canViewFI = in_array($selected_audit->step_id, $pmFileInspectionsOnlyStepIds);
$canViewSI = in_array($selected_audit->step_id, $pmSiteInspectionsOnlyStepIds);
$canViewBoth = in_array($selected_audit->step_id, $pmBothInspectionsOnlyStepIds);
if ($canViewBoth) {
	$canViewFI = true;
	$canViewSI = true;
}
?>
<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="{{$selected_audit->status}}">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-6 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;" onclick="UIkit.modal('#modal-select-audit').show();">
							<i class="a-square-right-2"></i>
						</div>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
							<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$selected_audit->lead_json->name}};" title="" aria-expanded="false" class="user-badge user-badge-{{$selected_audit->lead_json->color}} uk-link" style="height: 48px;
							width: 48px;
							line-height: 48px; font-size: 27px; margin-top: .2em">
							{{$selected_audit->lead_json->initials}}
						</span>



					</div>
					<div class="uk-width-3-5" style="padding-left:1.2em">
						<h3 id="audit-project-name-1" class="uk-margin-bottom-remove uk-text-align-center" style="font-size: 1.5em; padding-top: .5em;">{{$selected_audit->project_ref}}</h3>
						<small class="uk-text-muted" style="font-size: 1em;">AUDIT {{$selected_audit->audit_id}}</small>
					</div>
				</div>
			</div>
			<div class="uk-width-5-6 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-2 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-2-5 uk-padding-remove">
								<div class="divider"></div>
								<div class="uk-text-center hasdivider" uk-grid>
									<div class="uk-width-1-2 uk-padding-remove" uk-grid>

										<div class="uk-width-1-1 uk-padding-remove" uk-tooltip @if(!is_null($selected_audit->inspection_schedule_date) && count($selected_audit->days) > 0) title="AUDIT WILL TAKE {{count($selected_audit->days)}} @if( count($selected_audit->days) < 1 || count($selected_audit->days) > 1) DAYS @else DAY @endIf" @elseIf(!is_null($selected_audit->inspection_schedule_date) && count($selected_audit->days) == 0) title="SCHEDULE UNAVAILABLE" @endIf>
											@if(!is_null($selected_audit->inspection_schedule_date))
											<div class="dateyear" style="margin-top:5px; text-transform: uppercase;">{{date('l',strtotime($selected_audit->inspection_schedule_date))}}</div>
											<h3 class="uk-link" style="margin: 9px 10px 6px 0px !important;">{{date('M jS',strtotime($selected_audit->inspection_schedule_date))}}</h3>
											<div class="dateyear">{{date('Y',strtotime($selected_audit->inspection_schedule_date))}}</div>
											@else
											<i class="a-calendar-fail"  uk-tooltip="title:SCHEDULE IS UNAVAILABLE;"></i>
											{{-- project-details-button-2 scrollTo --}}
											@endif

										</div>
									</div>
									<div class="uk-width-1-2 uk-padding-remove">
										<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{$selected_audit->total_buildings}} @if($selected_audit->total_buildings > 1 || $selected_audit->total_buildings) < 1) BUILDINGS @else BUILDING @endIf" style="margin-top: 8px;"><i class="a-buildings" style="font-size: 25px;"></i> : {{$selected_audit->total_buildings}}</div>
										<hr class="uk-width-1-1" style="margin-bottom: 8px; margin-top: 0px" >
										<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{$selected_audit->total_units}} @if($selected_audit->total_units > 1 || $selected_audit->total_units < 1) UNITS @else UNIT @endIf"><i class="a-buildings-2" style="font-size: 25px;"></i> : {{$selected_audit->total_units}}</div>

									</div>

								</div>
							</div>
							<div class="uk-width-3-5 uk-padding-remove">
								<div class="divider"></div>
								<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>

									<div class="uk-width-1-3">

										<?php
											$carIcon = $selected_audit->car_icon;
											$ehsIcon = $selected_audit->ehs_icon;
											$_8823Icon = $selected_audit->_8823_icon;
											$carId = $selected_audit->car_id;
											$ehsId = $selected_audit->ehs_id;
											$_8823Id = $selected_audit->_8823_id;
											$carStatus = $selected_audit->car_status_text;
											$ehsStatus = $selected_audit->ehs_status_text;
											$_8823Status = $selected_audit->_8823_status_text;
										?>
											  @if($carIcon)
											  <a class="uk-link-mute" href="/report/{{$carId}}" target="report-{{$carId}}" uk-tooltip="title:VIEW CAR {{$carId}} : {{strtoupper($carStatus)}}"><i class="{{$carIcon}}" style="font-size: 30px;"></i></a>
											  <br /><small>CAR #{{$carId}}</small>

											  @else
											  <i class="a-file-fail" uk-tooltip="title:CAR UNAVAILABLE"></i><br /><small>CAR</small>
											  @endIf
											</div><div class="uk-width-1-3">
												@if(($ehsIcon))

											  		<a class="uk-link-mute" href="/report/{{$ehsId}}" target="report-{{$ehsId}}" uk-tooltip="title:{{$ehsStatus}}"><i class="{{$ehsIcon}}" style="font-size: 30px;" ></i></a>


											 		 <br /><small>EHS #{{$ehsId}}</small></a>
											  @else
											  <i class="a-file-fail" uk-tooltip="title:EHS UNAVAILABLE" ></i><br /><small>EHS</small>
											  @endIf
											</div>
											<div class="uk-width-1-3">
												@if(env('APP_ENV') != 'production')
												@if(($_8823Icon))

											  <a class="uk-link-mute" href="/report/{{$_8823Id}}" target="report-{{$_8823Id}}" uk-tooltip="title:{{$_8823Status}}" ><i class="{{$_8823Icon}}" style="font-size: 30px;"></i></a>


											  <br /><small>8823 #{{$_8823Id}}</small></a>
											  @else
											   <i class="a-file-fail" uk-tooltip="title:8823 UNAVAILABLE" ></i><br /><small>8823</small>
											  @endIf
											  @endIf
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
									<div class="uk-width-2-5 uk-padding-remove">

										<div class="divider"></div>
										<div class="uk-text-center hasdivider uk-margin-top" uk-grid>
											<div class="uk-width-1-3  uk-first-column pd-findings-column" title="" aria-expanded="false" ><i class="a-folder"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedFileCount < $fileCount) attention @endIf" uk-tooltip title="{{$correctedFileCount}} / {{$fileCount}} @if($fileCount < 1 || $fileCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$fileCount}}</span></div>
											<div  class="uk-width-1-3  pd-findings-column" title="" aria-expanded="false" ><i class="a-booboo"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedNltCount < $nltCount) attention @endIf" uk-tooltip title="{{$correctedNltCount}} / {{$nltCount}} @if($nltCount < 1 || $nltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$nltCount}}</span></div>
											<div  class="uk-width-1-3 pd-findings-column" title="" aria-expanded="false"><i class="a-skull"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedLtCount < $ltCount) attention @endIf" uk-tooltip title="{{$correctedLtCount}} / {{$ltCount}} @if($ltCount < 1 || $ltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$ltCount}}</span></div>
										</div>

									</div>
									<div class="uk-width-1-5 uk-padding-remove">
										<div class="divider"></div>
										<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
											<div class="uk-width-1-2 pd-findings-column">
												<i onclick="openCommunication()" class="{{$selected_audit->message_status_icon}} use-hand-cursor" uk-tooltip="title:{{$selected_audit->message_status_text}};"></i>
											</div>
											<div class="uk-width-1-2 pd-findings-column">
												<i onclick="openDocuments()" class="{{$selected_audit->document_status_icon}} use-hand-cursor" uk-tooltip="title:{{$selected_audit->document_status_text}}"></i>
											</div>


										</div>
									</div>
									<div class="uk-width-1-5 iconpadding  pd-findings-column uk-text-center" onclick="gotoDocumentUploader()" {{-- onclick="dynamicModalLoad('audits/{{$selected_audit->audit_id}}/updateStep?details_refresh=1',0,0,0);" --}}>

										<i class="a-file-up use-hand-cursor" uk-tooltip title="UPLOAD DOCUMENTS" ></i><br />
										<small>UPLOAD</small>

									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="project-details" uk-grid>
		<div id="project-details-general" class="uk-width-1-1">
			<div uk-grid>
				<div class="uk-width-2-3">
					<h3>{{$selected_audit->title}}<br /><small>Project {{$selected_audit->project_ref}} @if($selected_audit->audit_id)| Current Audit {{ $selected_audit->audit_id }}@endif</small></h3>
				</div>
				<div class="uk-width-1-3">
					<div class="audit-info" style="width: 80%;float: left;">

					</div>

				</div>
			</div>
		</div>
		<div id="project-details-stats" class="uk-width-1-1" style="margin-top:20px;">
			@include('projects.partials.details-project-details', ['details', $details])
		</div>

	</div>


	<div id="project-details-buttons" class="project-details-buttons" uk-grid>
		<div class="uk-width-1-1">

			<div uk-grid>
				<div class="uk-width-1-4">
					<button id="project-details-button-3" class="uk-button uk-link  active" onclick="pmProjectDetailsInfo({{$selected_audit->project_id}}, 'selections', {{$selected_audit->audit_id}},this);" type="button">
						<i class="a-mobile"></i> <i class="a-folder"></i>

					INSPECTIONS</button>
				</div>
				{{-- <div class="uk-width-1-4">
					<button id="project-details-button-1" class="uk-button uk-link " onclick="pmProjectDetailsInfo({{$project->id}}, 'compliance', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
				</div> --}}
				{{--
				@if(!is_null($selected_audit->inspection_schedule_date))
				<div class="uk-width-1-4">
					<button id="project-details-button-2" class="uk-button uk-link" onclick="pmProjectDetailsInfo({{$project->id}}, 'assignment', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-calendar-person"></i> SCHEDULE</button>
				</div>
				@else
					<div class="uk-width-1-4">
					<button id="project-details-button-2" class="uk-button "  type="button"><i class="a-calendar-fail"></i> SCHEDULE UNAVAILABLE</button>
				</div>
				@endIf
				--}}
			</div>

		</div>
	</div>

	<div id="project-details-info-container"></div>

	<div id="modal-select-audit" uk-modal>
		<div class="uk-modal-dialog uk-modal-body">
			<h2 class="uk-modal-title">Select another audit</h2>
			<select name="audit-selection" id="audit-selection" class="uk-select">
				@foreach($audits as $audit)
				<option value="{{$audit->audit_id}}" @if($audit->audit_id == $selected_audit->audit_id) selected @endif>Audit {{$audit->audit_id}} @if($audit->completed_date) | Completed on {{formatDate($audit->completed_date)}}@endif</option>
				@endforeach
			</select>
			<p class="uk-text-right">
				<button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
				<button class="uk-button uk-button-primary" onclick="changeAudit();" type="button">Select</button>
			</p>
		</div>
	</div>

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

    function changeAudit(){
    	console.log("changing audit");

    	var nextAudit = $('#audit-selection').val();

    	var tempdiv = '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% auto;"></div></div>';
    	$('#project-detail-tab-1-content').html(tempdiv);

    	UIkit.modal('#modal-select-audit').hide();
    	$('#modal-select-audit').remove();
    	dynamicModalClose();
    	$.post("/pmsession/project.{{$project->id}}.selectedaudit/"+nextAudit, {
    		'_token' : '{{ csrf_token() }}'
    	}, function(data) {
    		loadTab('{{ route('pm-project.details', $project->id) }}', '1', 0, 0, 'project-',1);

    	} );


    }







		$.fn.scrollView = function () {
			return this.each(function () {
				$('html, body').animate({
					scrollTop: $(this).offset().top-80
				}, 1000);
			});
		}


		$(document ).ready(function() {
			if($('#project-details-info-container').html() == ''){
				$('#project-details-button-3').trigger("click");
			}
			if(window.subtab == 'communications') {
				openCommunication();
				window.subtab = '';
			} else if(window.subtab == 'documents') {
				openDocuments();
				window.subtab = '';
			}
			pmLoadProjectDetailsBuildings( {{$project->id}}, {{$project->id}} ) ;
			UIkit.dropdown('#car-dropdown-{{$selected_audit->audit_id}}', 'mode:click');

		});


		function openSchedule() {
			$('#project-details-button-2').trigger('click');
			$('html, body').animate({
				scrollTop: 400
			}, 1000);
		}

		function openCommunication() {
			$('#project-detail-tab-2').trigger('click');
		}

		function openDocuments() {
			$('#project-detail-tab-3').trigger('click');
		}

		function openCompliance() {
			$('#project-details-button-1').trigger('click');
			$('html, body').animate({
				scrollTop: 400
			}, 1000);
		}

		function gotoDocumentUploader(building = '', unit = '') {
				window.fromAudit = 1;
				window.fromBuilding = building;
				window.fromUnit = unit;
				$('#project-detail-tab-2').trigger('click');
				$('#project-documents-button-3').trigger('click');
		}


	</script>
