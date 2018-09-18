<?php setlocale(LC_MONETARY, 'en_US'); ?>
<script>
    // disable infinite scroll:
    window.getContentForListId = 0;
    window.openedCommunication = 0;
    window.currentCommunication = "";
    window.restoreLastCommunicationItem = "";
    window.resetOpenCommunicationId == 0;
</script>

<?php $totalaccounting = count($accounting); ?>
<?php 
//dd($accounts,$accounting,$accountingTotals,$programs,$statuses,$currentUser, $accounting_sorted_by_query,$accountingAscDesc, $accountingAscDescOpposite,$accountingProgramFilter,$accountingStatusFilter);
/*


    
*/ ?>
<style type="text/css">
	.filter-drops{
					    -webkit-appearance: none;
					    -moz-appearance: none;
					    margin: 0;
					    border: none;
					    overflow: visible;
					    font: inherit;
					    color: #3a3a3a;
					    text-transform: none;
					    display: inline-block;
					    box-sizing: border-box;
					    padding: 0 12px;
					    background-color: #f5f5f5;
					    vertical-align: middle;
					    line-height: 28px;
					    min-height: 30px;
					    font-size: 1rem;
					    text-decoration: none;
					    text-align: center;
					    border: 1px solid rgba(0, 0, 0, 0.06);
					    border-radius: 4px;
					    text-shadow: 0 1px 0 #ffffff;
					    background: url(/images/select_icon.png) no-repeat;
					    background-position: 5px 7px;
					    text-indent: 13.01px;
					    background-size: 18px;
					    background-color: #f5f5f5;
				}
				select::-ms-expand {
				    display: none;
				}

</style>
<div uk-grid class="uk-child-width-1-1 uk-child-width-1-5@s">
@if(Auth::user()->entity_type == "hfa")
	<div class="uk-margin-top ">
		

			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by={{ $accounting_sorted_by_query }}&accounting_asc_desc={{ $accountingAscDesc }}&accounting_program_filter='+this.value)">
				<option value="ALL"
					@if ($accountingProgramFilter == '%%')
					 selected  
					@endif
					>
					FILTER BY PROGRAM  
					</option>
				@foreach ($programs as $program)
					
					<option value="{{$program->id}}"

					@if ($accountingProgramFilter == $program->id)
					 selected
					<?php $programFiltered = $program->program_name; ?>
					@endif
					>	
						{{$program->program_name}}

					</option>          
				@endforeach
			</select>
	</div>
@endif
	<div class="uk-margin-top ">
		
			<select  class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by={{ $accounting_sorted_by_query }}&accounting_asc_desc={{ $accountingAscDesc }}&accounting_status_filter='+this.value)">
					<option value="ALL" 
					@if ($accountingStatusFilter == '%%')
					 selected  
					
					@endif
					>
					FILTER BY STATUS
					</option>
				@foreach ($statuses as $status)
					
					<option value="{{$status->id}}"

					@if ($accountingStatusFilter == $status->id)
					 selected
					<?php $statusFiltered = $status->status_name; ?>
					
					@endif	
					>
						{{$status->status_name}}

					</option>          
				@endforeach
			</select>
		
	</div>
	@if(Auth::user()->entity_type == "hfa")
	<div class="uk-margin-top ">
		
			<script>
				function addTransaction(transactionType = 'invoice'){
					if(transactionType == 'invoice'){
						closeForms();
						console.log('viewing invoice selection');
						$('#invoice-selection').slideToggle();
					}
					if(transactionType == 'disposition-invoice'){
						closeForms();

						console.log('viewing disposition invoice selection');
						$('#disposition-selection').fadeIn();
					}
					if(transactionType == 'recapture-invoice'){
						closeForms();

						console.log('viewing recapture invoice selection');
						$('#recapture-selection').slideDown();
					}
					if(transactionType == 'balance-credit'){
						closeForms();
						dynamicModalLoad('transaction/balance-credit');
					}
					if(transactionType == 'balance-debit'){
						closeForms();
						
						dynamicModalLoad('transaction/balance-debit');
					}
					if(transactionType == 'funding-award'){
						closeForms();
						
						dynamicModalLoad('transaction/funding-award');
					}
					if(transactionType == 'funding-reduction'){
						closeForms();
						
						dynamicModalLoad('transaction/funding-reduction');
					}
					if(transactionType == 'landbank-credit'){
						closeForms();
						
						dynamicModalLoad('transaction/landbank-credit');
					}
					//window.accountingTabAddTransaction = transactionType;
				}
				function closeForms() {

						//console.log('closing forms');
					$('#invoice-selection').slideUp();
					$('#disposition-selection').slideUp();
					$('#recapture-selection').slideUp();
					// $('#transactio-type-selection').val('0');
				}

			</script>
			<select class="uk-select filter-drops uk-width-1-1" id="transactio-type-selection" onchange="addTransaction(this.value);">
				<option value="0" >
					ADD A TRANSACTION
					</option>
					<optgroup label="Payments on Invoices">
						<option value="invoice">	
							Add a Payment for an Unpaid Reimbursement Invoice
						</option>
						<option value="disposition-invoice">	
							Add a Payment for an Unpaid Disposition Invoice
						</option>
						<option value="disposition-invoice">	
							Add a Payment for an Unpaid Recapture Invoice
						</option>
					</optgroup>
					<optgroup label="Funding Credit and Debits">
						<option value="balance-credit">	
							Credit Funds to a Program from HFA
						</option>
						<option value="balance-debit">	
							Return Funds to HFA from Program
						</option>
						<option value="funding-award">	
							Funding Award to HFA
						</option>
						<option value="funding-reduction">	
							Funding Reduction to HFA
						</option>
					</optgroup>
					<optgroup label="Overpayment Adjustments">
						<option value="landbank-credit">	
							Reimbursement to HFA from Landbank
						</option>
					</optgroup>          
				
			</select>
	</div>
	<div class="uk-width-3-4 uk-margin-top " id="invoice-selection" style="display: none" >
		
		<select class="uk-select filter-drops uk-width-1-1" onChange="dynamicModalLoad('transaction/newFromInvoice/'+this.value+'?accountTab=1');$('#transactio-type-selection').val('0');$('#invoice-selection').toggle();">
			<option value="">
				Please Select an Unpaid Invoice To Add a Transaction To:
			</option>
			<?php $unpaid_invoice_program = ""; $unpaid_invoice_program_open = 0; ?>
			@forEach($unpaidReimbursementInvoices as $invoice)
			@if($invoice->program->program_name != $unpaid_invoice_program)
				@if($unpaid_invoice_program_open == 1)
				</optgroup>
				@endIf
				<optgroup label="{{$invoice->program->program_name}}">
				<?php $unpaid_invoice_program = $invoice->program->program_name; $unpaid_invoice_program_open = 1; ?>
			@endIf
				<option value="{{$invoice->id}}">INVOICE {{$invoice->id}} |
				
					{{date('m/d/Y',strtotime($invoice->created_at))}} | {{money_format('%(8n', $invoice->totalPaid())}} of {{money_format('%(8n', $invoice->totalAmount())}} DUE | BALNCE: {{money_format('%(8n',($invoice->totalAmount() - $invoice->totalPaid()))}} ({{$invoice->invoiceItems->count()}} Parcels)</option>
			@endForEach
			</optgroup>

		</select>	
	</div>
	<div class="uk-width-3-4 uk-margin-top " id="disposition-selection" style="display: none;">
		<select class="uk-select filter-drops uk-width-1-1" onChange="if(this.value != ''){dynamicModalLoad('transaction/newFromDispositionInvoice/'+this.value);$(this).val('');}">
			<option value="">
				Please Select an Unpaid Disposition Invoice To Add a Transaction To:
			</option>
			<?php $unpaid_invoice_program = ""; $unpaid_invoice_program_open = 0; ?>
			@forEach($unpaid_disposition_invoices as $invoice)
			@if($invoice->program->program_name != $unpaid_invoice_program)
				@if($unpaid_invoice_program_open == 1)
				</optgroup>
				@endIf
				<optgroup label="{{$invoice->program->program_name}}">
				<?php $unpaid_invoice_program = $invoice->program->program_name; $unpaid_invoice_program_open = 1; ?>
			@endIf
				<option value="{{$invoice->id}}">INVOICE {{$invoice->id}} |
				
					{{date('m/d/Y',strtotime($invoice->created_at))}} | {{money_format('%(8n', $invoice->totalPaid())}} of {{money_format('%(8n', $invoice->totalAmount())}} DUE | BALANCE: {{money_format('%(8n', $invoice->balance())}} ({{$invoice->invoiceItems->count()}} Dispositions)</option>
			@endForEach
			</optgroup>

		</select>	
	</div>
	<div class="uk-width-3-4 uk-margin-top " id="recapture-selection" style="display: none;">
		<select class="uk-select filter-drops uk-width-1-1" onChange="dynamicModalLoad('/modals/transaction/newFromRecaptureInvoice/'+this.value)">
			<option value="">
				Please Select a Recapture Invoice To Add a Transaction To:
			</option>
			<optgroup label="LB NAME">
				<option>INVOICE #### | MM/DD/YYYY | $000,000.00 of $000,000.00 DUE</option>
			</optgroup>
		</select>	
	</div>
</div>
@endif
<div uk-grid>
	<div class="uk-width-1-1">
		<hr class="dashed-hr">
			<div class="uk-badge  uk-text-right " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totalaccounting) }} Transactions&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by={{ $accounting_sorted_by_query }}&accounting_asc_desc={{ $accountingAscDesc }}&accounting_program_filter=ALL')" class="uk-dark uk-light"><i  class="a-circle-cross"></i> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by={{ $accounting_sorted_by_query }}&accounting_asc_desc={{ $accountingAscDesc }}&accounting_status_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			
			<?php /*
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">COST: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">REQ: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PO: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">INV: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PAID: </div>
			*/ ?>
<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////// GROUPING TRANSACTIONS BY ACCOUNT NAME - will always be sorted by account first - then whatever order is selected.
/// SET LAST ACCOUNT TO 0 SO TO TRIGGER INITIAL ACCOUNTS HEADERS
$lastAccount = 0;
/// SET OUTPUT STARTED TO 0 TO PREVENT THE END OF THE TABLE FROM BEING PRINTED
$accountOutputStarted = 0;

$accountEndOutput = '</tbody></table></div>';


//dd($accountEndOutput);
?>
<?php $counted = 0;  ?>						 		
@foreach($accounting as $data)

	<?php
		if($counted >= 0){
		$counted++;
		$currentAccount = $data->account_id;
	?>

	@if($currentAccount !== $lastAccount)


		@if($accountOutputStarted == 1)
			<!-- Ending Account strtoupper({{$data->account_name}}) -->
			<?php echo $accountEndOutput; ?>

			

		@endif
		<?php 
			///// SET OUTPUT STARTED SO IT CLOSES IT FOR EACH OF THE FOLLOWING NOW ON.
			$accountOutputStarted = 1; 
			?>

		<?php 
		////////////////////////////////////// SET LAST ACCOUNT ID SO THIS DOESN'T OPEN AGAIN UNTIL THE ACCOUNT CHANGES 


		$lastAccount = $data->account_id; 
		
		$programID = $data->owner_id;


		?>
		<!-- START ACCOUNT {{$data->account_name}} -->
			<hr class="dashed-hr">
			<h3><strong>{{strtoupper($data->program_name)}} </strong><small> | ACCT #{{$data->account_id}} | 
				<?php 	

			    					$accountingBalance = ($accountingTotals[$programID]->Deposits_Made + $accountingTotals[$programID]->Recaptures_Received + $accountingTotals[$programID]->Dispositions_Received) - ($accountingTotals[$programID]->Transfers_Made + $accountingTotals[$programID]->Reimbursements_Paid +$accountingTotals[$programID]->Line_Of_Credit);

			    					$accountingAvailableBalance = $accountingBalance - ($accountingTotals[$programID]->Total_Invoiced - $accountingTotals[$programID]->Reimbursements_Paid);

			    					
			    					?>
			    					{{money_format('%(8n', $accountingBalance)}} <a onClick="alert('Deposits Made:{{money_format('%(8n', $accountingTotals[$programID]->Deposits_Made)}} \n + Recaptures Received: {{money_format('%(8n',$accountingTotals[$programID]->Recaptures_Received)}}\n + Dispositions Received: {{ money_format('%(8n',$accountingTotals[$programID]->Dispositions_Received)}}\n - Transfers Made: {{money_format('%(8n',$accountingTotals[$programID]->Transfers_Made)}}\n - Reimbursements Paid {{money_format('%(8n',$accountingTotals[$programID]->Reimbursements_Paid)}}\n ------------------------------- \n TOTAL: {{money_format('%(8n',$accountingBalance)}}\n ------------------------------- \n - Pending Invoices: {{money_format('%(8n',($accountingTotals[$programID]->Total_Invoiced - $accountingTotals[$programID]->Reimbursements_Paid))}} \n ------------------------------- \n TOTAL AVAILBLE {{money_format('%(8n',$accountingAvailableBalance) }}');" uk-tooltip="Click here to see breakout"  >( {{money_format('%(8n', $accountingAvailableBalance)}} AVAILABLE )</a>
			    					
			</small>

			</h3>
			<div class="uk-overflow-auto">
							 <table class="uk-table uk-table-hover uk-table-striped uk-table-small small-table-text" style="min-width: 1420px">
							 	<thead>
							 		<th>
							 			<!-- 12 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=1&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >DATE ENTERED<i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-date-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		<th>
							 			<!-- 2 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=2&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >TRANS ID <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-invoice-number-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		<th class="uk-text-center">
							 			<!-- 2 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=3&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >TYPE <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-invoice-number-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		
							 		<th>
							 			<!-- 3 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=4&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >CATEGORY <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-account-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		<th>
							 			<!-- 6 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=5&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >NOTE <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-number-parcels-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		
							 		
							 		<th class="uk-text-right">
							 			<!-- 5 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=6&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >AMOUNT <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-entity-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 		
							 		
							 		
							 		<th class="uk-text-center">
							 			<!-- 10 -->
							 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','accounting?accounting_sort_by=10&accounting_asc_desc={{ $accountingAscDescOpposite }}');" >STATUS <i class="{{ $accountingAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $accountingAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} accounting-status-arrow-{{ $accounting_sorted_by_query }}"></i></small>
							 		</th>
							 			
							 		<th>
							 			<!-- 11 -->
							 			<small>ACTION </small>
							 		</th>
							 	</thead>
							 	<tbody>
	@endif

						 		
						 		
						 		<tr>
						 		
						 			<td><small>
						 			@if($data->date_entered && $data->date_entered != "0000-00-00")
						 				{{date('m/d/Y',strtotime($data->date_entered))}}
						 			@else
						 			NA
						 			@endif
						 			</small></td>
						 			<td class="uk-text-left"><small> {{$data->id}}</small></td>
						 			<td class="uk-text-center">
						 				<small>
						 				@if($data->type_id == 1)
						 				<!-- Reimbursement Invoice -->
						 					<a onclick="window.open('/invoices/{{$data->link_to_type_id}}', '_blank')" class="uk-link-muted display-block" uk-tooltip="INVOICE #{{$data->link_to_type_id}}">{{$data->type_name}}</a>
						 				@elseif($data->type_id == 2)
						 				<!-- Disposition Invoice -->
						 					<a onclick="" class="uk-link-muted display-block" uk-tooltip="DISPOSITION INVOICE #{{$data->link_to_type_id}}">{{$data->type_name}}</a>
						 				@elseif($data->type_id == 3)
						 				<!-- DEPOSIT -->
						 					<span class="uk-link-muted display-block" uk-tooltip="DEPOSIT MADE TO ACCOUNT {{$data->link_to_type_id}}">{{$data->type_name}}</span>
						 				@elseif($data->type_id == 5)
						 				<!-- TRANSFER TO ANOTHER ACCOUNT -->
						 					<span onclick="" class="uk-link-muted display-block" uk-tooltip="TRANSFER TO ACCOUNT: {{strtoupper($accounts[$data->link_to_type_id]->account_name)}}">{{$data->type_name}}</span>
						 				@elseif($data->type_id == 4)
						 				<!-- LINE OF CREDIT -->
						 					<a onclick="" class="uk-link-muted display-block" uk-tooltip="VIEW LINE OF CREDIT ACCOUNT: {{$data->link_to_type_id}}">{{$data->type_name}}</a>

						 				@elseif($data->type_id == 6)
						 				<!-- RECAPTURE Invoice -->
						 					<a onclick="" class="uk-link-muted display-block" uk-tooltip="OPEN RECAPTURE INVOICE #{{$data->link_to_type_id}}">{{$data->type_name}}</a>
						 				@endif



						 				</small></td>

						 			<td><small><a onclick="dynamicModalLoad('/program/{{$data->program_id}}')" class="uk-link-muted">{{$data->category_name}}</a></small></td>
						 			
						 			<td><small><a onclick="UIkit.modal.alert('{{addslashes(json_encode($data->transaction_note))}}')" class="uk-link-muted">{{substr($data->transaction_note,0,85)}}...</a></small></td>
						 			
						 			
						 			</td>
						 			<td class="uk-text-right"><small > 
						 			@if($data->credit_debit == 'c')
						 			{{money_format('%(8n', $data->amount)}}
						 			@else
						 			{{money_format('%(8n', ($data->amount * -1))}}
						 			@endif
						 			</small></td>
						 			<td class="uk-text-center"><small>{{$data->status_name}}</small></td>
						 			<td>
						 				<a class="a-file-pencil_1" onclick="dynamicModalLoad('transaction/edit/{{$data->id}}');" uk-tooltip="Edit"></a>
						 				<!-- <a class="uk-icon-file-o " onclick="dynamicModalLoad('/invoice/post/{{$data->po_id}}');" uk-tooltip="INVOICE"></a> 
						 				<a class="uk-icon-times-circle " onclick="dynamicModalLoad('/invoice/cancel/{{$data->invoice_id}}');" uk-tooltip="Cancel"></a> 
						 				<a class="uk-icon-trash " onclick="dynamicModalLoad('/invoice/delete/{{$data->invoice_id}}');" uk-tooltip="Delete"></a> -->
						 			</td>
						 			
						 		</tr>
	<?php } else {
		$counted++;
	}?>
						 		
@endforeach
<?php echo $accountEndOutput; ?>
<div id="list-tab-bottom-bar" class="uk-flex-middle" style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>
<script>
	if(typeof window.accountingTabAddTransaction !== 'undefined'){
		window.addTransaction(window.accountingTabAddTransaction);
	}
</script>

						 	
