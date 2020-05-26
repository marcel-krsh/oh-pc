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
	<title>Project Contacts</title>
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
					project_id
				</th>
				<th>
					project_name
				</th>
				<th>
					owner_name
				</th>
				<th>
					manager_name
				</th>
				<th>
					user_name
				</th>
				<th>
					user_email
				</th>
				<th>
					name/email match
				</th>
				<th>
					email/manager match
				</th>
			</tr>
		</thead>
		<tbody>
			@php
				$i = 1;
			@endphp
			@forEach($projects as $project)
			@forEach($project->contactRoles as $contact)
			@php
				$details = $project->details();
			@endphp
			<tr>
				<td>
					{{ $project->id }}
				</td>
				<td>
					{{ $project->project_name }}
				</td>
				<td>
					{{ $details->owner_name }}
				</td>
				<td>
					{{ $details->manager_name }}
				</td>
				<td>
					@if($contact->person) {{ $contact->person->first_name }} {{ $contact->person->last_name }} @else NA @endif
				</td>
				<td>
					@if($contact->person && $contact->person->email) {{ $contact->person->email->email_address }} @endif
				</td>
				<td>
					@php
					$i++;
						$cell1 = 'E' .  $i;
						$cell2 = 'F' .  $i;
					@endphp
					=IF({{ $cell1 }}={{ $cell2 }},"EXACT",IF(SUBSTITUTE({{ $cell1 }},"-"," ")=SUBSTITUTE({{ $cell2 }},"-"," "),"Hyphen",IF(LEN({{ $cell1 }})>LEN({{ $cell2 }}),IF(LEN({{ $cell1 }})>LEN(SUBSTITUTE({{ $cell1 }},{{ $cell2 }},"")),"Whole String",IF(MID({{ $cell1 }},1,1)=MID({{ $cell2 }},1,1),1,0)+IF(MID({{ $cell1 }},2,1)=MID({{ $cell2 }},2,1),1,0)+IF(MID({{ $cell1 }},3,1)=MID({{ $cell2 }},3,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }}),1)=MID({{ $cell2 }},LEN({{ $cell2 }}),1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-1,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-1,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-2,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-2,1),1,0)&amp;"°"),IF(LEN({{ $cell2 }})>LEN(SUBSTITUTE({{ $cell2 }},{{ $cell1 }},"")),"Whole String",IF(MID({{ $cell1 }},1,1)=MID({{ $cell2 }},1,1),1,0)+IF(MID({{ $cell1 }},2,1)=MID({{ $cell2 }},2,1),1,0)+IF(MID({{ $cell1 }},3,1)=MID({{ $cell2 }},3,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }}),1)=MID({{ $cell2 }},LEN({{ $cell2 }}),1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-1,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-1,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-2,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-2,1),1,0)&amp;"°"))))

				</td>
				<td>
					=IF({{ $cell1 }}={{ $cell2 }},"EXACT",IF(SUBSTITUTE({{ $cell1 }},"-"," ")=SUBSTITUTE({{ $cell2 }},"-"," "),"Hyphen",IF(LEN({{ $cell1 }})>LEN({{ $cell2 }}),IF(LEN({{ $cell1 }})>LEN(SUBSTITUTE({{ $cell1 }},{{ $cell2 }},"")),"Whole String",IF(MID({{ $cell1 }},1,1)=MID({{ $cell2 }},1,1),1,0)+IF(MID({{ $cell1 }},2,1)=MID({{ $cell2 }},2,1),1,0)+IF(MID({{ $cell1 }},3,1)=MID({{ $cell2 }},3,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }}),1)=MID({{ $cell2 }},LEN({{ $cell2 }}),1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-1,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-1,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-2,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-2,1),1,0)&amp;"°"),IF(LEN({{ $cell2 }})>LEN(SUBSTITUTE({{ $cell2 }},{{ $cell1 }},"")),"Whole String",IF(MID({{ $cell1 }},1,1)=MID({{ $cell2 }},1,1),1,0)+IF(MID({{ $cell1 }},2,1)=MID({{ $cell2 }},2,1),1,0)+IF(MID({{ $cell1 }},3,1)=MID({{ $cell2 }},3,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }}),1)=MID({{ $cell2 }},LEN({{ $cell2 }}),1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-1,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-1,1),1,0)+IF(MID({{ $cell1 }},LEN({{ $cell1 }})-2,1)=MID({{ $cell2 }},LEN({{ $cell2 }})-2,1),1,0)&amp;"°"))))
				</td>
			</tr>
			@endForEach
			@endForEach
		</tbody>
	</table>


</body>
</html>


