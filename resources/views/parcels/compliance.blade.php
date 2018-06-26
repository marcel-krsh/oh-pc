<?php setlocale(LC_MONETARY, 'en_US'); ?>

<div class="uk-grid-collapse" uk-grid>
	<div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
		<div class="uk-panel">
			<ul class="uk-subnav">
				@if(Auth::user()->isHFAComplianceAuditor() || Auth::user()->isHFAAdmin())
                <li class="uk-text-small"><button class="uk-button uk-button-default uk-button-small" onclick="createCompliance();"><span class="a-circle-plus"></span> NEW COMPLIANCE REVIEW</button></li>
                @endif
                <li class="uk-margin-left">
                	<span class="uk-text-small" style="margin-top:5px;">Current compliance status:</span>
                </li>

                @if($parcel->compliance == 1 && ($parcel->compliance_score != "1" && $parcel->compliance_score != "Pass"))
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-warning" style="margin-top:5px;">Requires random compliance audit</div> 
                </li>
                @endif

                @if($parcel->compliance_manual == 1 && ($parcel->compliance_score != "1" && $parcel->compliance_score != "Pass"))
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-warning" style="margin-top:5px;">Manual compliance audit in progress</div> 
                </li>
                @endif

                @if($parcel->compliance_score == "1" || $parcel->compliance_score == "Pass")
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-success" style="margin-top:5px;">Parcel PASSED</div> 
                </li>
                @elseif(($parcel->compliance_score == '0' || $parcel->compliance_score == 'Fail') && $parcel->compliance_score !== null)
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-warning" style="margin-top:5px;">Parcel FAILED</div>
                </li>
                @endif
            </ul>
		</div>
		<hr />
	</div>

	<div class="uk-width-1-1 uk-margin-top  uk-margin-bottom">
		<div class="uk-panel">
			@if(count($parcel->compliances))
			<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" >
			 	<thead>
			 		<th>
			 			<small>DATE CREATED</small>
			 		</th>
			 		<th>
			 			<small>CREATOR</small>
			 		</th>
			 		<th>
			 			<small>DATE AUDITED</small>
			 		</th>
			 		<th>
			 			<small>AUDITOR</small>
			 		</th>
			 		<th>
			 			<small>RANDOM</small>
			 		</th>
			 		<th>
			 			<small>STATUS</small>
			 		</th>
			 		<th>
			 			<small>ACTIONS</small>
			 		</th>
			 	</thead>
			 	<tbody>
			 		@foreach($parcel->compliances as $compliance)
					<tr>
						<td>
							@if($compliance->created_at !== null && $compliance->created_at != "-0001-11-30 00:00:00" && $compliance->created_at != "0000-00-00 00:00:00")
							<div class="date">
								<p class="m-d">{{ date('m',strtotime($compliance->created_at)) }}/{{ date('d',strtotime($compliance->created_at)) }}</p><span class="year">{{ date('Y',strtotime($compliance->created_at)) }}</span>
							</div>
							@else
							N/A
							@endif
						</td>
						<td><small>{{$compliance->creator->name}}</small></td>
						<td>
							@if($compliance->audit_date !== null && $compliance->audit_date != "-0001-11-30 00:00:00" && $compliance->audit_date != "0000-00-00 00:00:00")
							<div class="date">
								<p class="m-d">{{ date('m',strtotime($compliance->audit_date)) }}/{{ date('d',strtotime($compliance->audit_date)) }}</p><span class="year">{{ date('Y',strtotime($compliance->audit_date)) }}</span>
							</div>
							@else
							N/A
							@endif
						</td>
						<td>
							@if($compliance->auditor)
							<small>{{$compliance->auditor->name}}</small>
							@endif
						</td>
						<td>
							@if($compliance->random_audit)
							<small>Yes</small>
							@else
							<small>No</small>
							@endif
						</td>
						<td>
							@if($compliance->score === NULL || $compliance->score == '')
							<small>In progress</small> 
							@elseif($compliance->score == '0')
							<div class="uk-badge uk-badge-warning">FAIL</div> 
							@else
							<div class="uk-badge uk-badge-success">PASS</div>
							@endif
						</td>
						<td>
			 				<a class="a-file-copy-2" onclick="dynamicModalLoad('compliance/{{$compliance->id}}', '_blank')" uk-tooltip="View Compliance"></a> 
						@if(Auth::user()->isHFAComplianceAuditor() || Auth::user()->isHFAAdmin())

			 				<a class="a-pencil-2" onclick="window.open('/compliance/{{$parcel->id}}/{{$compliance->id}}/edit', '_blank')" uk-tooltip="Edit Compliance"></a> 

			 				<a class="a-trash-4" onclick="deleteCompliance('{{$compliance->id}}');" title="Delete" uk-tooltip="Delete Compliance"></a> 
			 			@endif
						</td>
					</tr>
					@endforeach
			 	</tbody>
			</table>
			@else
			This parcel doesn't have any compliance reviews at this time.
			@endif
		</div>
		<hr />
	</div>

</div>
<script type="text/javascript">
@if(Auth::user()->isHFAComplianceAuditor() || Auth::user()->isHFAAdmin())
	function deleteCompliance(id){
		UIkit.modal.confirm("Are you sure you want to delete this compliance review?").then(function() {
	        $.post('{{ URL::route("compliance.delete", [$parcel->id]) }}', {
				'_token' : '{{ csrf_token() }}',
				'compliance_id' : id
			}, function(data) {
				if(data['message']!='' && data['error']!=1){
					$('#compliance-tab-content').load('/compliance/{{$parcel->id}}');
				}else if(data['message']!='' && data['error']==1){
					UIkit.modal.alert(data['message']);
					
					$('#compliance-tab-content').load('/compliance/{{$parcel->id}}');
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
			
	    });
	}

	function createCompliance(){
		$.post('{{ URL::route("compliance.create", [$parcel->id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#compliance-tab-content').load('/compliance/{{$parcel->id}}');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				
				$('#compliance-tab-content').load('/compliance/{{$parcel->id}}');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
    }
@endif
</script>