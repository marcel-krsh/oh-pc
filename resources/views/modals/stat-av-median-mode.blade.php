@extends('modals.container')
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@section('content')
<ul class="uk-subnav uk-subnav-pill" uk-switcher="{connect:'#stats-breakdown',swiping:false}">
	<li class="uk-active"><a>AVERAGES</a></li>
	<li><a>MEDIANS</a></li>
	<li><a>PECENTAGES</a></li>
</ul>
<ul id="stats-breakdown" uk-switcher >
	<li>
		<div class="uk-overflow-container">
			<table class="uk-table uk-table-condensed uk-table-striped">
				<thead>
					<tr>
						<th><small>CATEGORY</small></th>
						<th class="uk-text-right"><small uk-tooltip="COST">COST</small></th>
						<th class="uk-text-right"><small uk-tooltip="REQUESTED">REQUESTED</small></th>
						<th class="uk-text-right"><small uk-tooltip="APPROVED">APPROVED</small></th>
						<th class="uk-text-right"><small uk-tooltip="INVOICED">INVOICED</small></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td ><small>AQUISITION</small></td>
						<td class="uk-text-right" uk-tooltip="AVG {{money_format('%(8n', $data->Acquisition_Cost_Average)}}"><small>{{money_format('%(8n', $data->Acquisition_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>NIP LOAN</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td ><small>Pre-Demo</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>DEMOLITION</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>GREENING</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>MAINTENANCE</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>ADMIN</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>OTHER</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Invoiced_Average)}}</small></td>
					</tr>
					<tr>
						<td><small>TOTALS</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Cost_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Requested_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Approved_Average)}}</small></td>
						<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Invoiced_Average)}}</small></td>
					</tr>
				</tbody>
			</table>
		</div>
	</li>
</ul>
@stop