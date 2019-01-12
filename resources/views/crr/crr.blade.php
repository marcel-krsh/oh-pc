@extends('layouts.simplerAllita')
@section('head')
<title>Compliance Review Report: {{date('y',strtotime($crr->audit->scheduled_at))}}-{{$crr->audit->id}}.{{str_pad($crr->version, 2, '0', STR_PAD_LEFT)}}</title> 
@stop
@section('content')
<!-- <script src="/js/components/upload.js"></script>
<script src="/js/components/form-select.js"></script>
<script src="/js/components/datepicker.js"></script>
<script src="/js/components/tooltip.js"></script> -->
<style>
#crr-panel .uk-panel-box-white {background-color:#ffffff;}
#crr-panel .uk-panel-box .uk-panel-badge {}
#crr-panel .green {color:#82a53d;}
#crr-panel .blue {color:#005186;}
#crr-panel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#crr-panel table tfoot tr td {border: none;}
#crr-panel textarea {width:100%;}
#crr-panel .note-list-item:last-child { border: none;}
#crr-panel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#crr-panel .property-summary {margin-top:0;}
</style>
<div id="crr-panel">
	<div class="uk-panel uk-panel-box uk-panel-box-white">
		<div class="uk-panel uk-panel-header uk-hidden@m uk-hidden@l" style="text-align:center;">
			<img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px;" />
			<h6 class="uk-panel-title uk-text-center"><span class="blue uk-text-bold	">Ohio Housing Finance Agency</span><br /><span class="green">Compliance Review Report</span><br />{{$crr->audit->project->project_name}}<br />OHFA Tracking Number: {{date('y',strtotime($crr->audit->start_date))}}-{{$crr->audit_id}}<br />Review Date:{{date('m/d/Y',strtotime($audit->start_date))}}</h6>
		</div>
		

		<div class="uk-panel no-padding-bottom">
			<div uk-grid">
				<div class="uk-width-1-1@s uk-width-1-2@m uk-width-3-4@l uk-margin-top">
					<p>{{date('m/d/Y',strtotime($crr->updated_at))}}</p>
					<p>
						{{$crr->audit->owner->organization_name}}<br />
						@if(!is_null($crr->audit->owner->address->line_1)){{$crr->audit->owner->address->line_1}}<br />@endIf
						@if(!is_null($crr->audit->owner->address->line_2))
						{{$crr->audit->owner->address->line_2}}<br />@endIf 
						@if(!is_null($crr->audit->owner->address->city))
						{{$crr->audit->owner->address->city}},@endIf 
						@if(!is_null($crr->audit->owner->address->state))
						{{$crr->audit->owner->address->state}} @endIf
						@if(!is_null($crr->audit->owner->address->zip))
						{{$crr->audit->owner->address->zip}}@endIf 



					</p>
					<p>Project Name: {{$crr->project->project_name}}<br />
						OHFA Tracking Number: {{date('y',strtotime($crr->audit->start_date))}}-{{$crr->audit_id}}
					</p>
					<p>Dear {{$crr->project->owner['name']}}:</p>

					<p>The Ohio Housing Finance Agency (“OHFA”) completed a monitoring review of {{$crr->project->name}} on {{$crr->audit->start_date}}. The review was performed to determine if the development is in compliance with the requirements of the 
						@php
						$totalPrograms = count($crr->project->programs); 
						$printedPrograms = 0; 
						@endphp
						@forEach($crr->project->programs as $program)@php
							@php $printedPrograms++ @endPhp
							{{$program->name}}@if($printedPrograms == ($totalPrograms - 1)) and@elseIf($totalPrograms > 1),@endIf 
						 	@if($totalPrograms > 1) programs. @else program. @endIf
						@endForEach
					</p>

					<p>The attached report summarizes OHFA’s findings of noncompliance and the documentation that must be submitted to correct these findings. This notice begins the corrective action period. You must supply all requested documentation through DEV|CO Inspection at http://devco.ohiohome.org no later than the close of business on {{date('m/d/Y',strtotime($data->corrective_action_due))}}. 
					</p>
					<p>Section 42 of the Internal Revenue Code requires OHFA to report all noncompliance under the HTC program to the Internal Revenue Service (IRS), even if the noncompliance is corrected. Form(s) 8823 (Notice of Noncompliance) will be mailed to the IRS, with a copy to the owner, after the corrective action deadline. It is the owner’s responsibility to maintain compliance.
					</p>


				</div>
				<div class="uk-width-1-2">
					<p>Sincerely,</p>
					<p><img src="{{$crr->lead->signature}}" height="150">
						<br />
						{{$crr->lead->first_name}} {{$crr->lead->last_name}}<br />
						Compliance Analyst<br />
						Office Program Compliance<br />

				</div>
				<div class="uk-width-1-2">
					<p>Approved By:</p>
					<p><img src="{{$crr->manager->signature}}" height="150">
						<br />
						{{$crr->manager->first_name}} {{$crr->manager->last_name}}<br />
						Compliance Manager<br />
						Office Program Compliance<br />
				</div>

			</div>
		</div>

	</div>
</div>
@stop