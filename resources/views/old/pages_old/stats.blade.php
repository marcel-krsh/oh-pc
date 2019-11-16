<?php setlocale(LC_MONETARY, 'en_US'); ?>
@if(Auth::user()->entity_type == 'hfa')
<div >
	<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-margin-top uk-margin-bottom" uk-grid>
		<div>
			<ul class="uk-subnav uk-subnav-pill uk-width-1-1" uk-switcher>
			    <li onclick="$('.stat-switcher-parcels').trigger('click');"><a href="" class="uk-active " ><span class="a-home-2"></span> PARCELS</a></li>
			    <li onclick="$('.stat-switcher-actions').trigger('click');"><a href="" class="" ><span class="a-gear-3" ></span> ACTIONS</a></li>
			    
			    <li onclick="$('.stat-switcher-breakouts').trigger('click');"><a href="" class=" " ><span class="a-checklist"></span> BREAKOUTS</a></li>
			    <li onclick="$('.stat-switcher-accounting').trigger('click');"><a href="" class="" ><span class="a-money-bag_1"></span> ACCOUNTING</a></li>	
			</ul>
		</div>
		<div uk-grid class="uk-flex-right@s uk-flex-center uk-child-width-1-2 uk-grid-small">
			<div>
				<a class="uk-button uk-button-default uk-align-right@s" href="/dashboard/map" target="_blank"><span class="a-map-marker-2"></span> View Parcel Map</a>
			</div>
			<div>
				<a class="uk-button uk-button-default uk-align-right@s" href="/dashboard/map?sdo=1" target="_blank"><span class="a-map-marker-2"></span> View SDO Parcel Map</a>
			</div>
		</div>
	</div>
<?php /////////////////////////////// GRAND TOTALS USING  /////// ?>


	<div uk-grid class="uk-flex-center">
		<div class="uk-width-1-1 uk-width-3-5@s uk-margin-top uk-margin-bottom">
			<div class="uk-card uk-card-default uk-card-body">
			    <div class="uk-card-badge uk-badge">
			    	{{number_format($sumStatData['Total_Parcels'])}}
			    </div>
			    <h3 class="uk-card-title">
			    	GRAND TOTALS
			    </h3>
			    <ul class="uk-subnav uk-subnav-pill" uk-switcher="{connect:'#program-totals',swiping:false}">
				    <li class="stat-switcher-parcels"><a href="" class="uk-active stat-switcher-parcels" ><span class="a-home-2"></span></a></li>
				    <li class="stat-switcher-actions"><a href="" class="stat-switcher-actions" ><span class="a-gear-3" ></span></a></li>
				    
				    <li class="stat-switcher-breakouts"><a href="" class=" stat-switcher-breakouts"><span class="a-checklist"></span></a></li>
				    <li class="stat-switcher-accounting"><a href="" class="stat-switcher-accounting"><span class="a-money-bag_1"></span></a></li>
				</ul>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				<!-- This is the container of the content items -->
				<ul id="program-totals" class="uk-switcher">
				    <li>
				    	<ul>
				    		<li><span class="parcel-district-format">Total Parcels: <span class="parcel-district-format-number">{{number_format($sumStatData['Total_Parcels'])}}</span></span></li>
				    		<?php /*
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Processing']) == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Processing'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Corrections_Requested_To_LB'] == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Corrections Requested: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Corrections_Requested_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Ready_For_Signators_In_HFA'] == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Signatures Needed: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Ready_For_Signators_In_HFA'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Reimbursement_Denied_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Denied: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Reimbursement_Denied_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Withdrawn_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Withdrawn by OHFA: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Withdrawn_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Reimbursement_Approved_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Approved: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Reimbursement_Approved_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__PO_Sent_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">PO Sent: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__PO_Sent_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Declined_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Declined: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Declined_To_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Invoice_Received_From_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Received: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Invoice_Received_From_LB'])}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Paid_Reimbursement'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Paid: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Paid_Reimbursement'])}}</span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($sumStatData['HFA__Disposition_Requested_By_LB'] == 0) 
				    		color:lightgray; @endif">Disposition Requested: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Disposition_Requested_By_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Disposition_Approved_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Approved: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Disposition_Approved_To_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Disposition_Invoiced_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Invoiced: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Disposition_Invoiced_To_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Disposition_Paid_By_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Paid: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Disposition_Paid_By_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Disposition_Released_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Released: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Disposition_Released_To_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Repayment_Invoiced_To_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Invoiced: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Repayment_Invoiced_To_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Repayment_Received_From_LB'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Paid: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Repayment_Received_From_LB'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Compliance_Review'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Compliance Review: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Compliance_Review'])}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($sumStatData['HFA__Unsubmitted'] == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{number_format($sumStatData['HFA__Unsubmitted'])}}</span></span></li>
							*/?> <li>Remaining Stat Logic is Being Updated. Please check back soon.</li>
				    	</ul>
				    </li>
				    <li>
				   	<a onclick="loadDashBoardSubTab('dashboard','request_list');" class="uk-button uk-button-default uk-width-1-1">VIEW REQUESTS</a><hr class="dashed-hr">
				    <a onclick="loadDashBoardSubTab('dashboard','po_list');" class="uk-button uk-button-default uk-width-1-1">VIEW PURCHASE ORDERS</a><hr class="dashed-hr">
				    <a onclick="loadDashBoardSubTab('dashboard','invoice_list');" class="uk-button uk-button-default uk-width-1-1">VIEW INVOICES</a><hr class="dashed-hr">
				    
				    @if(Auth::user()->canManageUsers())
				    <a onClick="dynamicModalLoad('createuser');" class="uk-button uk-button-default uk-width-1-1">ADD USER</a><hr class="dashed-hr">
				    @endIf
						<a href="/import_parcels" class="uk-button uk-button-default uk-width-1-1">UPLOAD PARCELS</a><hr class="dashed-hr">
						<a href="/parcels/export" class="uk-button uk-button-default uk-width-1-1">EXPORT PARCEL DATA</a><hr class="dashed-hr">
				    <a onclick="dynamicModalLoad('/accounting/create/deposit')" class="uk-button uk-button-default uk-width-1-1">DEPOSIT FUNDS</a><hr class="dashed-hr">
				    <a onclick="dynamicModalLoad('/communications/new/notification')" class="uk-button uk-button-default uk-width-1-1">SEND MASS NOTIFICATION</a>
				    </li>
				    <li>
				    	<div class="uk-overflow-auto">
				    	<table class="uk-table uk-table-small uk-table-striped">
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
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Acquisition_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Acquisition_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Acquisition_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Acquisition_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>NIP LOAN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['NIP_Loan_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['NIP_Loan_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['NIP_Loan_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['NIP_Loan_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>Pre-Demo</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['PreDemo_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['PreDemo_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['PreDemo_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['PreDemo_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>DEMOLITION</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Demolition_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Demolition_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Demolition_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Demolition_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>GREENING</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Greening_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Greening_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Greening_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Greening_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>MAINTENANCE</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Maintenance_Cost'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Maintenance_Requested'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Maintenance_Approved'])}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Maintenance_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>ADMIN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Administration_Cost'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Administration_Requested'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Administration_Approved'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Administration_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>OTHER</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Other_Cost'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Other_Requested'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Other_Approved'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Other_Invoiced'])}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>TOTALS</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Total_Cost'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Total_Requested'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Total_Approved'])}}</small></td>
				    				<td  class="uk-text-right"><small>{{money_format('%(8n', $sumStatData['Total_Invoiced'])}}</small></td>
				    			</tr>
				    		</tbody>
				    	</table>
				    	</div>
				    	

				    </li>
				    <li>
				    	All Accounts Total
				    	<ul>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Deposits: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $sumStatData['Deposits_Made'])}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Recaptures Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $sumStatData['Recaptures_Received'])}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Dispositions Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $sumStatData['Dispositions_Received'])}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Reimbursments Paid: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $sumStatData['Reimbursements_Paid'])}} )
				    				</span>
				    			</span>
				    		</li>
				    		
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Transfers: 
				    				<span class="parcel-district-format-number" uk-tooltip="These funds are neither a debit or credit, but funds that were moved between accounts.">
				    					[ {{money_format('%(8n', $sumStatData['Transfers_Made'])}} ]
				    				</span>
				    			</span>
				    		</li>
				    		<!-- <li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				<span class="a-circle-minus"></span> Line of Credit: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $sumStatData['Line_Of_Credit'])}} )
				    				</span>
				    			</span>
				    		</li> -->
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:1px solid black;">
				    				<span class="a-checkbox"></span> <strong>BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = ($sumStatData['Deposits_Made'] + $sumStatData['Recaptures_Received'] + $sumStatData['Dispositions_Received']) - ($sumStatData['Reimbursements_Paid'] + $sumStatData['Line_Of_Credit'])
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				&nbsp;&nbsp;&nbsp;<span class="a-circle-minus"></span> Pending Reimbursements: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', ($sumStatData['Total_Invoiced'] - $sumStatData['Reimbursements_Paid']))}} )
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:2px solid black;">
				    				<span class="a-checkbox"></span> <strong>AVAILABLE BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = $accountingBalance - ($sumStatData['Total_Invoiced'] - $sumStatData['Reimbursements_Paid'])
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>

				    	</ul>

				    </li>
				    

				</ul>

			</div>
		</div>
	</div>
</div>
<div class="uk-child-width-1-1 uk-child-width-1-2@s" uk-grid>
	@foreach($stats as $data)
		<div class="uk-margin-top uk-margin-bottom">
			<div class="uk-card uk-card-default uk-card-body">
			    <div class="uk-card-badge uk-badge">
			    	{{$data->Total_Parcels}}
			    </div>
			    <h3 class="uk-card-title">
			    	{{$data->program_name}}
			    </h3>
			    <ul class="uk-subnav uk-subnav-pill" uk-switcher="{connect:'#program-{{$data->program_id}}',swiping:false}">
				    <li class="stat-switcher-parcels"><a href="" class="uk-active stat-switcher-parcels" ><span class="a-home-2"></span></a></li>
				    <li class="stat-switcher-actions"><a href="" class="stat-switcher-actions" ><span class="a-gear-3" ></span></a></li>
				    
				    <li class="stat-switcher-breakouts"><a href="" class=" stat-switcher-breakouts"><span class="a-checklist"></span></a></li>
				    <li class="stat-switcher-accounting"><a href="" class="stat-switcher-accounting"><span class="a-money-bag_1"></span></a></li>
				</ul>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				<!-- This is the container of the content items -->
				<ul id="program-{{$data->program_id}}" class="uk-switcher">
				    <li>
				    	<ul>
				    		<li><span class="parcel-district-format">Total Parcels: <span class="parcel-district-format-number">{{$data->Total_Parcels}}</span></span></li>
				    		<?php /*
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Processing == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{$data->HFA__Processing}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Corrections_Requested_To_LB == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Corrections Requested: <span class="parcel-district-format-number">{{$data->HFA__Corrections_Requested_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Ready_For_Signators_In_HFA == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Signatures Needed: <span class="parcel-district-format-number">{{$data->HFA__Ready_For_Signators_In_HFA}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Reimbursement_Denied_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Denied: <span class="parcel-district-format-number">{{$data->HFA__Reimbursement_Denied_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Withdrawn_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Withdrawn by OHFA: <span class="parcel-district-format-number">{{$data->HFA__Withdrawn_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Reimbursement_Approved_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Approved: <span class="parcel-district-format-number">{{$data->HFA__Reimbursement_Approved_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__PO_Sent_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">PO Sent: <span class="parcel-district-format-number">{{$data->HFA__PO_Sent_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Declined_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Declined: <span class="parcel-district-format-number">{{$data->HFA__Declined_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Invoice_Received_From_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Received: <span class="parcel-district-format-number">{{$data->HFA__Invoice_Received_From_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Paid_Reimbursement == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Paid: <span class="parcel-district-format-number">{{$data->HFA__Paid_Reimbursement}}</span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Disposition_Requested_By_LB == 0) 
				    		color:lightgray; @endif">Disposition Requested: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Requested_By_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Approved_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Approved: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Approved_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Invoiced_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Invoiced: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Invoiced_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Paid_By_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Paid: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Paid_By_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Released_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Released: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Released_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Repayment_Invoiced_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Invoiced: <span class="parcel-district-format-number">{{$data->HFA__Repayment_Invoiced_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Repayment_Received_From_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Paid: <span class="parcel-district-format-number">{{$data->HFA__Repayment_Received_From_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Compliance_Review == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Compliance Review: <span class="parcel-district-format-number">{{$data->HFA__Compliance_Review}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Unsubmitted == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{$data->HFA__Unsubmitted}}</span></span></li>
				    		*/?> <li>Statistical count of parcels is being updated to the current statuses. Please check back soon for an update. </li>

				    	</ul>
				    </li>
				    <li>
				    <a onclick="dynamicModalLoad('/parcels/upload')" class="uk-button uk-button-default uk-width-1-1">UPLOAD PARCELS</a><hr class="dashed-hr">
				    <a onclick="loadDashBoardSubTab('dashboard','request_list');" class="uk-button uk-button-default uk-width-1-1">VIEW REQUESTS</a><hr class="dashed-hr">
				    <a onclick="loadDashBoardSubTab('dashboard','po_list');" class="uk-button uk-button-default uk-width-1-1">VIEW PURCHASE ORDERS</a><hr class="dashed-hr">
				    <a onclick="loadDashBoardSubTab('dashboard','invoice_list');" class="uk-button uk-button-default uk-width-1-1">VIEW INVOICES</a><hr class="dashed-hr">
				    @if(Auth::user()->canManageUsers())
			    <a onClick="dynamicModalLoad('createuser');" class="uk-button uk-button-default uk-width-1-1">ADD USER</a><hr class="dashed-hr">
			    @endIf
				    <a onclick="dynamicModalLoad('/accounting/create/deposit')" class="uk-button uk-button-default uk-width-1-1">DEPOSIT FUNDS</a><hr class="dashed-hr">
				    <a onclick="dynamicModalLoad('/communications/new/notification')" class="uk-button uk-button-default uk-width-1-1">SEND NOTIFICATION</a>
				    </li>
				    <li>
				    	<div class="uk-overflow-auto">
				    	<table class="uk-table uk-table-small uk-table-striped">
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
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/2/{{$data->program_id}}/')">
				    				<td ><small>AQUISITION</small></td>
				    				<td class="uk-text-right" uk-tooltip="AVG {{money_format('%(8n', $data->Acquisition_Cost_Average)}}"><small>{{money_format('%(8n', $data->Acquisition_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/9/{{$data->program_id}}/')">
				    				<td><small>NIP LOAN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/3/{{$data->program_id}}/')">
				    				<td ><small>Pre-Demo</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/4/{{$data->program_id}}/')">
				    				<td><small>DEMOLITION</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/5/{{$data->program_id}}/')">
				    				<td><small>GREENING</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/6/{{$data->program_id}}/')">
				    				<td><small>MAINTENANCE</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/7/{{$data->program_id}}/')">
				    				<td><small>ADMIN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/8/{{$data->program_id}}/')">
				    				<td><small>OTHER</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Invoiced)}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>TOTALS</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Invoiced)}}</small></td>
				    			</tr>
				    		</tbody>
				    	</table>
				    	</div>
				    	
				    	

				    </li>
				    <li>
				    	Account ID: {{$data->transactions_account_id}}
				    	<ul>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Deposits: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Deposits_Made)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Recaptures Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Recaptures_Received)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Dispositions Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Dispositions_Received)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Reimbursments Paid: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Reimbursements_Paid)}} )
				    				</span>
				    			</span>
				    		</li>
				    		
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Transfers: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Transfers_Made)}} )
				    				</span>
				    			</span>
				    		</li>
				    		<!-- <li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				<span class="a-circle-minus"></span> Line of Credit: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Line_Of_Credit)}} )
				    				</span>
				    			</span>
				    		</li> -->
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:1px solid black;">
				    				<span class="a-checkbox"></span> <strong>BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = ($data->Deposits_Made + $data->Recaptures_Received + $data->Dispositions_Received) - ($data->Transfers_Made + $data->Reimbursements_Paid +$data->Line_Of_Credit)
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				&nbsp;&nbsp;&nbsp;<span class="a-circle-minus"></span> Pending Reimbursements: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', ($data->Total_Invoiced - $data->Reimbursements_Paid))}} )
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:2px solid black;">
				    				<span class="a-checkbox"></span> <strong>AVAILABLE BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = $accountingBalance - ($data->Total_Invoiced - $data->Reimbursements_Paid)
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>

				    	</ul>

				    </li>
				    

				</ul>

			</div>
		</div>
		
	@endforeach
@else

	@foreach($stats as $data)
	<div class="uk-grid">
		<div class="uk-width-1-1 uk-margin-top">
			<a class="uk-button uk-button-default uk-width-1-5@m uk-width-1-1@s uk-align-right" href="/dashboard/map" target="_blank"><span class="a-map-marker-2"></span> View Parcel Map</a>
		</div>
		<div class="uk-width-1-1 uk-width-1-1@m uk-width-1-2@l  uk-margin-top uk-margin-bottom">
			<div class="uk-card uk-card-default uk-card-body">
			    
			    <h3 class="uk-card-title">
			    	{{$data->program_name}}
			    </h3>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				<!-- This is the container of the content items -->
				
				    	<ul>
				    		<li><span class="parcel-district-format">Total Parcels: <span class="parcel-district-format-number">{{$data->Total_Parcels}}</span></span></li>
				    		<?php /*
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Processing == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{$data->HFA__Processing}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Corrections_Requested_To_LB == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Corrections Requested: <span class="parcel-district-format-number">{{$data->HFA__Corrections_Requested_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Ready_For_Signators_In_HFA == 0)
				    		 color:lightgray; 
				    		 @endif
				    		 ">Signatures Needed: <span class="parcel-district-format-number">{{$data->HFA__Ready_For_Signators_In_HFA}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Reimbursement_Denied_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Denied: <span class="parcel-district-format-number">{{$data->HFA__Reimbursement_Denied_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Withdrawn_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Withdrawn by OHFA: <span class="parcel-district-format-number">{{$data->HFA__Withdrawn_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Reimbursement_Approved_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Approved: <span class="parcel-district-format-number">{{$data->HFA__Reimbursement_Approved_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__PO_Sent_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">PO Sent: <span class="parcel-district-format-number">{{$data->HFA__PO_Sent_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Declined_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Reimbursement Declined: <span class="parcel-district-format-number">{{$data->HFA__Declined_To_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Invoice_Received_From_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Received: <span class="parcel-district-format-number">{{$data->HFA__Invoice_Received_From_LB}}</span></span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Paid_Reimbursement == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Invoice Paid: <span class="parcel-district-format-number">{{$data->HFA__Paid_Reimbursement}}</span></li>
				    		<li><span class="parcel-district-format" style="
				    		@if($data->HFA__Disposition_Requested_By_LB == 0) 
				    		color:lightgray; @endif">Disposition Requested: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Requested_By_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Approved_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Approved: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Approved_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Invoiced_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Invoiced: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Invoiced_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Paid_By_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Paid: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Paid_By_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Disposition_Released_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Disposition Released: <span class="parcel-district-format-number">{{$data->HFA__Disposition_Released_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Repayment_Invoiced_To_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Invoiced: <span class="parcel-district-format-number">{{$data->HFA__Repayment_Invoiced_To_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Repayment_Received_From_LB == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Repayment Paid: <span class="parcel-district-format-number">{{$data->HFA__Repayment_Received_From_LB}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Compliance_Review == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Compliance Review: <span class="parcel-district-format-number">{{$data->HFA__Compliance_Review}}</span></span></li>

				    		<li><span class="parcel-district-format" style="@if($data->HFA__Unsubmitted == 0) 
				    		color:lightgray; 
				    		@endif
				    		">Processing: <span class="parcel-district-format-number">{{$data->HFA__Unsubmitted}}</span></span></li>
							*/?> <li>Statiscal logic is being updated. Please check back soon. The next release will have complete counts.</li>
				    	</ul>
			</div>
		</div>
		<div class="uk-width-1-1 uk-width-1-1@m uk-width-1-2@l  uk-margin-top uk-margin-bottom">
			<div class="uk-card uk-card-default uk-card-body">
			    
			    <h3 class="uk-card-title">
			    	Actions
			    </h3>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				    <a href="/import_parcels" class="uk-button uk-button-default uk-width-1-1">UPLOAD PARCELS</a><hr class="dashed-hr">
				    @if(Auth::user()->canManageUsers())
			    <a onClick="dynamicModalLoad('createuser');" class="uk-button uk-button-default uk-width-1-1">ADD USER</a><hr class="dashed-hr">
			    @endIf
				    

			    
			    <h3 class="uk-card-title">
			    	Accounting
			    </h3>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				   
				    	Account ID: {{$data->transactions_account_id}}
				    	<ul>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Deposits: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Deposits_Made)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Recaptures Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Recaptures_Received)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-plus"></span> Dispositions Received: 
				    				<span class="parcel-district-format-number">
				    					{{money_format('%(8n', $data->Dispositions_Received)}}
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Reimbursments Paid: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Reimbursements_Paid)}} )
				    				</span>
				    			</span>
				    		</li>
				    		
				    		<li>
				    			<span class="parcel-district-format">
				    				<span class="a-circle-minus"></span> Transfers: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Transfers_Made)}} )
				    				</span>
				    			</span>
				    		</li>
				    		<!-- <li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				<span class="a-circle-minus"></span> Line of Credit: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', $data->Line_Of_Credit)}} )
				    				</span>
				    			</span>
				    		</li> -->
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:1px solid black;">
				    				<span class="a-checkbox"></span> <strong>BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = ($data->Deposits_Made + $data->Recaptures_Received + $data->Dispositions_Received) - ($data->Transfers_Made + $data->Reimbursements_Paid +$data->Line_Of_Credit)
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
				    				&nbsp;&nbsp;&nbsp;<span class="a-circle-minus"></span> Pending Reimbursements: 
				    				<span class="parcel-district-format-number">
				    					( {{money_format('%(8n', ($data->Total_Invoiced - $data->Reimbursements_Paid))}} )
				    				</span>
				    			</span>
				    		</li>
				    		<li>
				    			<span class="parcel-district-format"  style="border-bottom:2px solid black;">
				    				<span class="a-checkbox"></span> <strong>AVAILABLE BALANCE:</strong> 
				    				<span class="parcel-district-format-number">
				    					<?php
				    					$accountingBalance = $accountingBalance - ($data->Total_Invoiced - $data->Reimbursements_Paid)
				    					?>
				    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
				    				</span>
				    			</span>
				    		</li>

				    	</ul>

			</div>
		</div>
		<div class="uk-width-1-1 uk-width-1-1@m uk-width-1-2@l  uk-width-3-5@m uk-push-1-5 uk-margin-top uk-margin-bottom">
			<div class="uk-card uk-card-default uk-card-body">
			    
			    <h3 class="uk-card-title">
			    	Breakouts
			    </h3>
				<hr class="dashed-hr" class="uk-margin-bottom">
				<br />
				    
				    	<div class="uk-overflow-auto">
				    	<table class="uk-table uk-table-small uk-table-striped">
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
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/2/{{$data->program_id}}/')">
				    				<td ><small>AQUISITION</small></td>
				    				<td class="uk-text-right" uk-tooltip="AVG {{money_format('%(8n', $data->Acquisition_Cost_Average)}}"><small>{{money_format('%(8n', $data->Acquisition_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Acquisition_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/9/{{$data->program_id}}/')">
				    				<td><small>NIP LOAN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->NIP_Loan_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/3/{{$data->program_id}}/')">
				    				<td ><small>Pre-Demo</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->PreDemo_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/4/{{$data->program_id}}/')">
				    				<td><small>DEMOLITION</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Demolition_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/5/{{$data->program_id}}/')">
				    				<td><small>GREENING</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Greening_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/6/{{$data->program_id}}/')">
				    				<td><small>MAINTENANCE</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Maintenance_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/7/{{$data->program_id}}/')">
				    				<td><small>ADMIN</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Administration_Invoiced)}}</small></td>
				    			</tr>
				    			<tr style="cursor: pointer;" onclick="dynamicModalLoad('expense-categories-details/0/8/{{$data->program_id}}/')">
				    				<td><small>OTHER</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Other_Invoiced)}}</small></td>
				    			</tr>
				    			<tr>
				    				<td><small>TOTALS</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Cost)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Requested)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Approved)}}</small></td>
				    				<td class="uk-text-right"><small>{{money_format('%(8n', $data->Total_Invoiced)}}</small></td>
				    			</tr>
				    		</tbody>
				    	</table>
				    	</div>
				    	
				    	

			</div>
		</div>
		
	</div>
	<hr />
		
	@endforeach
@endIf
</div>

<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>
	


