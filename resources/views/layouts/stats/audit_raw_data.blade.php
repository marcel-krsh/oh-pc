<?php
function decimalHours($time)
{
	$hms = explode(":", $time);
	$hours = ($hms[0] + ($hms[1] / 60) + ($hms[2] / 3600));
	return (number_format((float) $hours, 2, '.', ''));
}

?>
<html>
<head>
<title>Audit Stats - Ugly Output</title>
<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/audits.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/findings.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/communications.js{{ asset_version() }}"></script>

<script type="text/javascript" src="/js/jquery.mask.js"></script>


</head>
<body>


		<table class="greyGridTable" width="3500" >
			<thead>
				<tr>
				<th>
					Audit Number
				</th>
				<th>
					Lead Auditor
				</th>
				<th>
					Inspection Date
				</th>
				<th>
					Project Manager
				</th>

				<th>
					Project Number
				</th>
				<th>
					Project Name
				</th>
				<th>
					Address
				</th>
				<th>
					City
				</th>
				<th>
					State
				</th>
				<th>
					Zip
				</th>
				<th>
					Buildings Inspected
				</th>
				<th>
					Units Inspected
				</th>
				<th>
					Estimated Time
				</th>
				<th>
					Unscheduled
				</th>
				<th>
					File Findings
				</th>
				<th>
					Unresolved File Findings
				</th>
				<th>
					NLT Findings
				</th>
				<th>
					Unresolved NLT Findings
				</th>
				<th>
					LT Findings
				</th>
				<th>
					Unresolved LT Findings
				</th>

				<th>
					CAR Status
				</th>

				<th>
					EHS Status
				</th>
				</tr>
			</thead>
			<tbody>
				<tr >
					<td>
						TOTALS
					</td>
					<td>

					</td>
					<td>

					</td>
					<td>

					</td>
					<td >
						{{$cachedAudits->count()}}
						PROJECTS

					</td>
					<td>
					</td>
					<td >
					</td>
					<td >
					</td>
					<td >
					</td>
					<td >
					</td>
					<td>
						{{number_format($cachedAudits->sum('total_buildings'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('total_units'))}}
					</td>
					<td>
						{{number_format($totalEstimatedTime/3600)}} HOURS
					</td>
					<td>
						{{number_format($totalEstimatedTimeNeeded/3600)}} HOURS
					</td>
					<td>
						{{number_format($cachedAudits->sum('file_findings_count'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('unresolved_file_findings_count'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('nlt_findings_count'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('unresolved_nlt_findings_count'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('lt_findings_count'))}}
					</td>
					<td>
						{{number_format($cachedAudits->sum('unresolved_nlt_findings_count'))}}
					</td>
					<td >



					</td>
					<td >


					</td>
				</tr>
				@forEach($cachedAudits as $ca)
				<tr>
					<td>
						{{$ca->audit_id}}
					</td>
					<td>

						{{$ca->lead_json->name}}
					</td>
					<td>
						{{date('m-d-Y', strtotime($ca->inspection_schedule_date))}}
					</td>
					<td>
						{{$ca->pm}}
					</td>

					<td>
						{{$ca->project_ref}}
					</td>
					<td>
						{{$ca->title}}
					</td>
					<td>
						{{$ca->address}}

					</td>
					<td>
						{{$ca->city}}

					</td>
					<td>
						{{$ca->state}}

					</td>
					<td>
						{{$ca->zip}}

					</td>
					<td>
						{{$ca->total_buildings}}
					</td>
					<td>
						{{$ca->total_units}}
					</td>
					<td>
						@if(!is_null($ca->estimated_time))
							{{decimalHours($ca->estimated_time)}}
						@else
						0.0
						@endif
					</td>
					<td>
						@if(!is_null($ca->estimated_time_needed))
							{{decimalHours($ca->estimated_time_needed)}}
						@else
						0.0
						@endif
					</td>
					<td>
						{{$ca->file_findings_count}}
					</td>
					<td>
						{{$ca->unresolved_file_findings_count}}
					</td>
					<td>
						{{$ca->nlt_findings_count}}
					</td>
					<td>
						{{$ca->unresolved_nlt_findings_count}}
					</td>
					<td>
						{{$ca->lt_findings_count}}
					</td>
					<td>
						{{$ca->unresolved_nlt_findings_count}}
					</td>

					<td>
						@if(!is_null($ca->car_id)) {{$ca->car_status_text}} @else NA @endif
					</td>

					<td>
						@if(!is_null($ca->ehs_id)) {{$ca->ehs_status_text}} @else NA @endif
					</td>
				</tr>
				@endForEach

			</tbody>


		</table>


</body>
</html>


