
<html>
<head>
<title>Audit Stats - Ugly Output</title>
<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/audits.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/findings.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/communications.js{{ asset_version() }}"></script>

<script type="text/javascript" src="/js/jquery.mask.js"></script>
<style type="text/css">
	
	body {
		font-family: sans-serif;
		font-size: 17pt;
	}
	table.greyGridTable {
  border: 0px solid #FFFFFF;
  
  text-align: left;
  border-collapse: collapse;
}
table.greyGridTable td, table.greyGridTable th {
  border: 1px solid #A8A8A8;
  padding: 6px 5px;
}
table.greyGridTable tbody td {
  font-size: 13px;
}
table.greyGridTable td:nth-child(even) {
  background: #EBEBEB;
}
table.greyGridTable thead {
  background: #FFFFFF;
  border-bottom: 4px solid #AAAAAA;
}
table.greyGridTable thead th {
  font-size: 15px;
  font-weight: bold;
  color: #2B2B2B;
  text-align: center;
  border-left: 2px solid #AAAAAA;
}
table.greyGridTable thead th:first-child {
  border-left: none;
}

table.greyGridTable tfoot {
  font-size: 14px;
  font-weight: bold;
  color: #333333;
  border-top: 4px solid #AEAEAE;
}
table.greyGridTable tfoot td {
  font-size: 14px;
}
</style>

</head>
<body>


		<table class="greyGridTable" width="3500" >
			<thead>
				<tr>
				<th width="100">
					Audit Number
				</th>
				<th width="200">
					Lead Auditor
				</th>
				<th width="200">
					Inspection Date
				</th>
				<th width="200">
					Project Manager
				</th>
				
				<th width="200">
					Project Number
				</th>
				<th width="200">
					Project Name
				</th>
				<th width="100">
					Address
				</th>
				<th width="100">
					City
				</th>
				<th width="100">
					State
				</th>
				<th width="100">
					Zip
				</th>
				<th width="100">
					Buildings Inspected
				</th>
				<th width="100">
					Units Inspected
				</th>
				<th width="100">
					Estimated Time
				</th>
				<th width="100">
					Unscheduled
				</th>
				<th width="100">
					File Findings
				</th>
				<th width="100">
					Unresolved File Findings
				</th>
				<th width="100">
					NLT Findings
				</th>
				<th width="100">
					Unresolved NLT Findings
				</th>
				<th width="100">
					LT Findings
				</th>
				<th width="100">
					Unresolved LT Findings
				</th>
				
				<th width="100">
					CAR Status
				</th>
				
				<th width="100">
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
						{{$ca->estimated_time}}
					</td>
					<td>
						{{$ca->estimated_time_needed}}
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


