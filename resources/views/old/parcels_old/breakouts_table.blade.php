<?php setlocale(LC_MONETARY, 'en_US'); ?>


<?php /*
     +"date": null
    +"description": "NIP Loan Payoff Invoiced Aggregate"
    +"notes": "Legacy Parcel - No Break Out Available, No Dates Available."
    +"invoice_id": 88
    +"invoice_item_id": 463
    +"po_id": 88
    +"approved_item_id": 463
    +"approved_amount": 100.0
    +"req_id": 88
    +"requested_item_id": 463
    +"requested_amount": 463
    +"cost_item_id": 463
    +"cost_amount": 100.0
    +"vendor_id": 1
    +"vendor_name": "Legacy Vendor"
    +"breakout_item_status_id": 2
    +"breakout_item_status_name": "Approved"
    +"breakout_type_id": 1
    +"breakout_type_name": "Landbank Reimbursement"
    +"expense_category_id": 9
    +"expense_category_name": "NIP Loan Payoff"
*/ ?>

<div class="uk-overflow-container" style="min-height:300px;" class="uk-animation-fade">
	<div class="uk-width-1-1 ">
		<div class="uk-panel">
			<ul class="uk-subnav uk-list">
                <li class="uk-text-small"><button class="uk-button uk-button-default uk-button-small" onclick="dynamicModalLoad('cost/{{$parcel_id}}/add');"><span class="a-circle-plus"></span> ADD COSTS</button></li>
                <li class="uk-text-small"><button class="uk-button uk-button-default uk-button-small" onclick="loadParcelSubTab('documents',{{$parcel_id}});"><span class="a-higher"></span> UPLOAD DOCUMENTS</button></li>
                
                <li class="uk-text-small" aria-haspopup="true" aria-expanded="false" class="">
                    <button class="uk-button uk-button-default uk-button-small"><span class="a-check"></span> CHECKS & ACTIONS <span class="a-arrow-small-down"></span></button>
                    <div class="uk-dropdown  uk-dropdown-bottom" uk-dropdown="mode: click; flip: false;" aria-hidden="true" tabindex="" style="top: 26px; left: 0px;">
                        <ul class="uk-nav uk-nav-dropdown uk-text-small uk-list">
                            
	                        @if(!$parcel->hasCostItems())
	                        <li><button onclick="dynamicModalLoad('cost/{{$parcel_id}}/add');" class="uk-button uk-button-small uk-text-left uk-button-link"><span class="a-checkbox"></span> PLEASE ADD COSTS</button></li>
	                        @else
	                        <li class="uk-disable">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" disabled><span class="a-checkbox-checked"></span>  COSTS HAVE BEEN ENTERED</button>
                            </li>
	                        @endif
	                        @if(!$parcel->hasSupportingDocuments())
                			<li class="uk-text-small"><button onclick="$('#smoothscrollLink').trigger('click');loadParcelSubTab('documents',{{$parcel_id}});" class="uk-button uk-text-left uk-button-small uk-button-link"><span class="a-checkbox"></span> UPLOAD SUPPORTING DOCUMENTS</button></li>
                			@else
	                        <li class="uk-disable">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="$('#smoothscrollLink').trigger('click');loadParcelSubTab('documents',{{$parcel_id}});"><span class="a-checkbox-checked"></span>  HAS SUPPORTING DOCUMENTS</button>
                            </li>
                			@endif
	                        @if($requestedAmountsAreMissing)
                			<li class="uk-disable">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" disabled><span class="a-checkbox"></span> ENTER REQUEST AMOUNTS</button>
                            </li>
                			@else
	                        <li class="uk-disable">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" disabled><span class="a-checkbox-checked"></span>  ALL REQUEST AMOUNTS ENTERED</button>
                            </li>
                			@endif
                			@if( (Auth::user()->isLandbankParcelApprover() || Auth::user()->isHFAAdmin()) && !$requestedAmountsAreMissing && !$parcelAlreadyInRequest && $parcel->hasRequestItems())
                            <li class="">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="add_parcel_to_request();"><span class="a-checkbox-plus"></span> ADD THIS PARCEL TO OPEN REQUEST</button> 
                            </li>
	                        @elseif($parcelAlreadyInRequest)
	                        <li class="uk-disable">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" disabled><span class="a-checkbox-checked"></span> THIS PARCEL IS PART OF A REQUEST</button>
                            </li>
                            <li class="">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="remove_parcel_from_request();"><span class="a-checkbox-minus"></span> REMOVE THIS PARCEL FROM THE REQUEST</button> 
                            </li>
	                        @else
	                        <li class="">
                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" disabled><span class="a-checkbox-plus"></span> ADD THIS PARCEL TO OPEN REQUEST</button>
                            </li>
	                        @endif 
	                        @if(Auth::user()->isHFAPOApprover())
	                        <li class="uk-nav-divider"></li>
	                            @if($all_documents_approved) 
		                        <li class="">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small"><span class="a-checkbox-checked"></span> ALL DOCUMENTS ARE REVIEWED</button> 
	                            </li>
	                            @else
	                            <li class="uk-disable">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="loadParcelSubTab('documents',{{$parcel->id}});" ><span class="a-checkbox-minus"></span> UNREVIEWED OR PENDING DOCUMENTS</button> 
	                            </li>
	                            @endif
	                            @if($parcel->isHfaApproved())
		                        <li class="uk-disable">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small"  disabled><span class="a-checkbox-checked"></span> PARCEL APPROVED IN PO</button> 
	                            </li>
	                            @elseif($parceltopo)
	                            <li class="">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="hfa_approve_parcel();"><span class="a-checkbox-minus"></span> HFA: APPROVE THIS PARCEL</button> 
	                            </li>
	                            @endif
	                           
	                            @if($parcel->hfa_property_status && $parcel->hfa_property_status->id == 23 )
		                        <li class="uk-disable">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small"  disabled><span class="a-checkbox-minus"></span> THE HFA HAS DECLINED THIS PARCEL</button> 
	                            </li>
	                            @elseif($parceltopo)
	                            <li class="">
	                            	<button  class="uk-button uk-text-left uk-button-link uk-button-small" onclick="hfa_decline_parcel();"><span class="a-checkbox-minus"></span> HFA: DECLINE THIS PARCEL</button> 
	                            </li>
	                            @endif
	                        @endif
                        </ul>
                    </div>
                </li>
                <li class="uk-margin-left">
                	<span class="uk-text-small uk-display-inline">Current status:</span> <div class="uk-label @if($parcel->landbankPropertyStatus->id == 7) uk-label-success @else uk-label-warning @endif uk-display-inline">{{$parcel->landbankPropertyStatus->option_name}}</div>
                </li>
                @if($parceltorequest)
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-success uk-display-inline" onclick="window.open('/requests/{{$parceltorequest->reimbursement_request_id}}', '_blank')" style="cursor:pointer;">Added to request #{{$parceltorequest->reimbursement_request_id}}</div> 

                	@if(Auth::user()->canReassignParcels()) 	
    					<div class="">
                            <button class="uk-button uk-button-default uk-button-small"><span class="a-pencil-2"></span></button>
                            <div class="uk-dropdown  uk-dropdown-bottom" uk-dropdown="mode: click">

                                <ul class="uk-nav uk-nav-dropdown">
                                    @forEach($availableRequests as $data)
                                    <li><a onclick="reassignParcel({{$data->req_id}})">Request {{$data->req_id}}</a></li>
                                    @endForEach
                                </ul>
                            </div>
                        </div>
                	@endif
                </li>
                @else
                	@if(Auth::user()->canReassignParcels()) 
		                <li class="uk-margin-left">
		                	<div class="uk-label uk-label-success uk-display-inline" style="cursor:pointer;">ASSIGN TO EXISTING REQUEST</div> 
		    					<div data-uk-dropdown="{mode:'click'}" class="">
		                            <button class="uk-button uk-button-default uk-button-small"><span class="a-checkbox-plus"></span></button>
		                            <div class="uk-dropdown  uk-dropdown-bottom">

		                                <ul class="uk-nav uk-nav-dropdown">
		                                    @forEach($availableRequests as $data)
		                                    <li><a onclick="reassignParcel({{$data->req_id}})">Request {{$data->req_id}}</a></li>
		                                    @endForEach
		                                </ul>
		                            </div>
		                        </div>
		                </li>
					@endif
                @endif
                @if($parceltopo)
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-success uk-display-inline" onclick="window.open('/po/{{$parceltopo->purchase_order_id}}', '_blank')" style="cursor:pointer;">Added to PO #{{$parceltopo->purchase_order_id}}</div> 
                </li>
                @endif
                @if($parceltoinvoice)
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-success uk-display-inline" onclick="window.open('/invoices/{{$parceltoinvoice->reimbursement_invoice_id}}', '_blank')" style="cursor:pointer;">Added to Invoice #{{$parceltoinvoice->reimbursement_invoice_id}}</div> 
                </li>
                @endif

                @if(Auth::user()->isHFAComplianceAuditor() && $compliance_started_and_not_all_approved == 1)
                <li class="uk-margin-left">
                	<div class="uk-label uk-label-warning uk-display-inline">Requires compliance audit</div> 
                </li>
                @endif
            </ul>
		</div>
		<hr />
	</div>
	<!-- <pre>
		<?php //print_r($parcelRules); ?>
	</pre> -->
	@if(isset($breakouts))
	 <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" style="min-width: 1420px">
	 	<thead>
	 		<th>
	 			<small>DATE ENTERED</small>
	 		</th>
	 		<th>
	 			<small>TYPE</small>
	 		</th>
	 		<th>
	 			<small>CATEGORY</small>
	 		</th>
	 		<th>
	 			<small>VENDOR</small>
	 		</th>
	 		<th>
	 			<small>DESCRIPTION</small>
	 		</th>
	 		<th class="uk-text-right">
	 			<small>COST</small>
	 		</th>
	 		<th class="uk-text-right">
	 			<small>REQUESTED</small>
	 		</th>
	 		@if($parceltopo)
	 		<th class="uk-text-right">
	 			<small>APPROVED</small>
	 		</th>
	 		@endif
	 		@if($parcelAlreadyInInvoice)
	 		<th class="uk-text-right">
	 			<small>INVOICED</small>
	 		</th>
	 		@endif
	 		<th class="uk-text-center" hidden>
	 			<small>STATUS</small>
	 		</th>
	 			
	 		<th class="">
	 			<small>ACTION</small>
	 		</th>
	 	</thead>
	 	<tbody>
<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////// BREAKOUT ITEMS
$noteIncrement = 0;
$i = 0;
?>
	 		
	 	@if(isset($breakouts))
	 		@foreach($breakouts as $data)
	 		<?php $noteIncrement++;
	 			  $i++;
	 		 ?>
	 		<tr>
	 			<td><small>
	 			@if($data->date)
	 				{{date("m/d/Y",strtotime($data->date))}}
	 			@else
	 			NA
	 			@endif
	 			</small></td>
	 			<td><small>{{$data->breakout_type_name}}</small></td>

	 			<td><small><a onclick="dynamicModalLoad('expense-categories-details/0/{{$data->expense_category_id}}/{{$parcel->entity_id}}/{{$parcel_id}}')" class="uk-link-muted">{{$data->expense_category_name}}</a></small></td>
	 			
	 			<td><small><a onclick="dynamicModalLoad('expense-categories-vendor-details/{{$data->vendor_id}}/{{$parcel->id}}/{{$parcel->program->id}}')" class="uk-link-muted">{{$data->vendor_name}}</a></small></td>
	 			
	 			<td><small>{{$data->description}}</small>
	 			@if(strlen($data->notes)>0)
	 			<hr class="dashed-hr uk-margin-remove"><div id="notes{{$noteIncrement}}" class="breakout-note"><span class="a-file-pencil_1"></span> <small>{{$data->notes}}</small></div>
	 			@endif
	 			</td>

	 			<td class="uk-text-right">
	 			@if(isset($data->cost_amount))	
	 				<div class="cost-amount-edit-{{$data->cost_item_id}}" style="cursor: pointer;" uk-toggle="target: .cost-amount-edit-{{$data->cost_item_id}}">
	 					<small id="cost-amount-value-{{$data->cost_item_id}}">{{money_format('%(8n', $data->cost_amount)}}</small>
	 				</div>
	 			@else
	 			<small ><a onclick="dynamicModalLoad('cost/{{$parcel_id}}/add');" class="uk-button uk-button-default uk-button-small">ENTER COSTS</a></small>
	 			@endif
	 				<div class="cost-amount-edit-{{$data->cost_item_id}}" @if(isset($data->cost_amount)) hidden @endif>
						<div class="uk-inline">
							<input type="text" id="cost-amount-{{$data->cost_item_id}}" name="cost-amount-{{$data->cost_item_id}}" class="uk-input uk-form-small uk-form-smaller uk-form-width-small" style="width:40px;" value="{{$data->cost_amount}}" /> <button onclick="save_cost_amount({{$data->cost_item_id}});" class="uk-button uk-button-default uk-button-small" >Save</button>
							@if(isset($data->cost_amount))
							<button uk-toggle="target: .cost-amount-edit-{{$data->cost_item_id}}" class="uk-button uk-button-default uk-button-small" >x</button>
							@endif
						</div>
					</div>
					
	 			 </td>

	 			
	 			<td class="uk-text-right">
	 			@if(isset($data->requested_item_id))	
	 				<div class="request-amount-edit-{{$data->cost_item_id}}" style="cursor: pointer;" uk-toggle="target: .request-amount-edit-{{$data->cost_item_id}}">
	 					<small id="request-amount-value-{{$data->cost_item_id}}">{{money_format('%(8n', $data->requested_amount)}}</small>
	 				</div>
	 			@else
	 				<div class="request-amount-edit-{{$data->cost_item_id}}" style="cursor: pointer;" uk-toggle="target: .request-amount-edit-{{$data->cost_item_id}}" hidden>
	 					<small id="request-amount-value-{{$data->cost_item_id}}"></small>
	 				</div>
	 			@endif
	 				<div class="request-amount-edit-{{$data->cost_item_id}}" @if(isset($data->requested_amount)) hidden @endif>
						<div class="uk-inline">
							<input type="text" id="request-amount-{{$data->cost_item_id}}" name="request-amount-{{$data->cost_item_id}}" class="uk-input uk-form-small uk-form-smaller uk-form-width-small" style="width:40px;" value="{{$data->requested_amount ?? $data->cost_amount}}" @if(!isset($data->requested_amount) && !isset($firstRequest)) <?php $firstRequest = "request-amount-$data->cost_item_id"; ?> @endif /> <button onclick="save_request_amount({{$data->cost_item_id}});" class="uk-button uk-button-default uk-button-small" >Save</button>
							@if(isset($data->requested_amount))
							<button uk-toggle="target: .request-amount-edit-{{$data->cost_item_id}}" class="uk-button uk-button-default uk-button-small" >x</button>
							@endif
						</div>
					</div>
					
	 			 </td>
	 			 @if($parceltopo)
	 			<td class="uk-text-right">
	 			@if(Auth::user()->isHFAPOApprover() || Auth::user()->isHFAAdmin())
	 				@if(isset($data->approved_item_id))
	 				<div class="po-amount-edit-{{$data->requested_item_id}}" style="cursor: pointer;" uk-toggle="target: .po-amount-edit-{{$data->requested_item_id}}">
	 					<small id="po-amount-value-{{$data->requested_item_id}}">{{money_format('%(8n', $data->approved_amount)}}</small>
	 				</div>
	 				@elseif(isset($data->requested_item_id))
	 				<div class="po-amount-edit-{{$data->requested_item_id}}" style="cursor: pointer;" uk-toggle="target: .po-amount-edit-{{$data->requested_item_id}}" hidden>
	 					<small id="po-amount-value-{{$data->requested_item_id}}"></small>
	 				</div>
	 				@endif
	 				@if(isset($data->requested_item_id))
	 				<div class="po-amount-edit-{{$data->requested_item_id}}" @if(isset($data->approved_amount)) hidden @endif>
						<div class="uk-inline">
							<input type="text" id="po-amount-{{$data->requested_item_id}}" name="po-amount-{{$data->requested_item_id}}" class="uk-input uk-form-small uk-form-smaller uk-form-width-small" style="width:40px;" value="{{$data->approved_amount ?? $data->requested_amount}}"/> <button onclick="save_approved_amount({{$data->requested_item_id}});" class="uk-button uk-button-default uk-button-small" >Save</button>
							@if(isset($data->approved_amount))
							<button uk-toggle="target: .po-amount-edit-{{$data->requested_item_id}}" class="uk-button uk-button-default uk-button-small" >x</button>
							@endif
						</div>
					</div>
					@endif
				@else
					<small >{{money_format('%(8n', $data->approved_amount)}}</small>
				@endif
	 			</td>
	 			@endif

	 			@if($parcelAlreadyInInvoice)
	 			<td class="uk-text-right">
	 			@if(Auth::user()->isHFAAdmin() || Auth::user()->isHFAPrimaryInvoiceApprover() || Auth::user()->isHFASecondaryInvoiceApprover() || Auth::user()->isHFATertiaryInvoiceApprover())
	 				@if(isset($data->invoice_item_id))
	 				<div class="invoice-amount-edit-{{$data->approved_item_id}}" style="cursor: pointer;" uk-toggle="target: .invoice-amount-edit-{{$data->approved_item_id}}">
	 					<small id="invoice-amount-value-{{$data->approved_item_id}}">{{money_format('%(8n', $data->invoice_amount)}}</small>
	 				</div>
	 				@elseif(isset($data->approved_item_id))
	 				<div class="invoice-amount-edit-{{$data->approved_item_id}}" style="cursor: pointer;" uk-toggle="target: .invoice-amount-edit-{{$data->approved_item_id}}" hidden>
	 					<small id="invoice-amount-value-{{$data->approved_item_id}}"></small>
	 				</div>
	 				@endif
	 				@if(isset($data->approved_item_id))
	 				<div class="invoice-amount-edit-{{$data->approved_item_id}}" @if(isset($data->invoice_amount)) hidden @endif>
						<div class="uk-inline">
							<input type="text" id="invoice-amount-{{$data->approved_item_id}}" name="invoice-amount-{{$data->approved_item_id}}" class="uk-input uk-form-small uk-form-smaller uk-form-width-small" style="width:40px;" value="{{$data->invoice_amount ?? $data->approved_amount}}" /> <button onclick="save_invoice_amount({{$data->approved_item_id}});" class="uk-button uk-button-default uk-button-small" >Save</button>
							@if(isset($data->approved_amount))
							<button uk-toggle="target: .invoice-amount-edit-{{$data->approved_item_id}}" class="uk-button uk-button-default uk-button-small" >x</button>
							@endif
						</div>
					</div>
					@endif
				@else
					<small >{{money_format('%(8n', $data->invoice_amount)}}</small>
				@endif
	 			</td>
	 			@endif
	 			
	 			@if(isset($data->breakout_item_status_name))
	 			<td class="uk-text-center" hidden><small>{{$data->breakout_item_status_name}}</small></td>
	 			@else
	 			<td class="uk-text-right" hidden><small>NA</small></td>
	 			@endif
	 			
	 			<td>	
	 				@if(Auth::user()->isLandbankAdmin() || Auth::user()->isHFAAdmin())
	 					@if($data->retainage_id)
	 					<i class="a-trash-4" style="color:grey" uk-tooltip="A retainage prevents this item from being deleted"></i>
	 					@else
	 					<a class="a-trash-4" onclick="deleteBreakOutItem('{{$data->cost_item_id}}');" uk-tooltip="Delete"></a>
	 					@endif
		 			@endif

		 			@if(Gate::allows('view-recapture') || Auth::user()->entity_id == 1)
		 			@if($invoice !== null)
            			@if($invoice->status_id == 6)
		 				<a class="a-dollar-rotation-2" onclick="dynamicModalLoad('breakout_item/recapture/{{$parcel_id}}/{{$data->cost_item_id}}');" uk-tooltip="Recapture"></a>
		 				@endif
		 			@endif
		 			@endif
		 			
		 			@if($data->advance && (Auth::user()->isLandbankAdmin() || Auth::user()->isHFAAdmin()))
		 			<a class="a-file-left" onclick="$('#parcel-subtab-2').trigger('click');" uk-tooltip="Upload advance documents"></a>
		 			@endif
	 			</td>
	 			@if(isset($data->breakout_type_name))
	 			<td hidden>						 				
	 				@if($data->breakout_type_name == "costs")
	 				<a class="uk-button uk-button-default uk-button-small uk-margin-left" onclick="addAmount({{$data->cost_item_id}},'request_amount_{{$i}}-form','request');" id="request-amount-{{$i}}-save"><span class="a-floppy"></span> SAVE</a></small>
	 				@else
	 					@php /*if($data->breakout_type_name != "invoiced" && $data->breakout_item_status_name != "Paid") */ @endphp
	 					<a class="a-pencil-2" onclick="dynamicModalLoad('/breakout_item/edit/{{$data->cost_item_id}}');" title="Edit"></a>
		 				
		 				<a class="a-trash-4" onclick="deleteBreakOutItem('{{$data->cost_item_id}}');" title="Delete"></a>


		 				@php /* else */ @endphp
		 				<span class="a-locked-2" uk-tooltip="Sorry after an item has been reimbursed, it is no longer editable."></span>
		 				@php /* endif  */ @endphp
	 				@endif
	 			</td>
	 			@else
	 			<td hidden><small>NA</small></td>
	 			@endif
	 		</tr>
	 		@endforeach
	 	@endif

	 	</tbody>
	 </table>
	 @else
	 <a class="uk-button uk-button-large uk-button-success uk-width-1-1 uk-text-center uk-align-center uk-margin-top" onclick="dynamicModalLoad('cost/{{$parcel_id}}/add');">PLEASE ENTER YOUR COSTS</a>
	 @endif
				 
</div>
<script>
window.addedBreakouts = 0;

@if(Auth::user()->isHFAAdmin() || Auth::user()->isHFAPrimaryInvoiceApprover() || Auth::user()->isHFASecondaryInvoiceApprover() || Auth::user()->isHFATertiaryInvoiceApprover())
function save_invoice_amount(id){
	//UIkit.modal.confirm("Are you sure you want to save the cost amount?").then(function() {
        $.post('{{ URL::route("breakouts.addInvoiced", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'po_id' : id,
			'amount' : $('#invoice-amount-'+id).val(),
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    //});
}
@endif

@if(Auth::user()->isHFAPOApprover())

function hfa_decline_parcel(){
	UIkit.modal.confirm("Are you sure you want to decline this parcel?").then(function() {
        $.post('{{ URL::route("breakouts.declineParcel", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}

function hfa_approve_parcel(){
	UIkit.modal.confirm("Are you sure you want to approve this parcel?").then(function() {
        $.post('{{ URL::route("breakouts.approveParcel", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}

@endif

function deleteBreakOutItem(id){
	UIkit.modal.confirm("Are you sure you want to delete this cost amount?").then(function() {
        $.post('{{ URL::route("breakouts.deleteCost", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'cost_id' : id
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}

function save_cost_amount(id){
	//UIkit.modal.confirm("Are you sure you want to save the cost amount?").then(function() {
        $.post('{{ URL::route("breakouts.editCost", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'cost_id' : id,
			'amount' : $('#cost-amount-'+id).val(),
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				if(data['new_amount'] >= 0){
					new_amount = '$' + data['new_amount'].toFixed(2);
					$('#cost-amount-value-'+id).html(new_amount);
				}
				UIkit.toggle('.cost-amount-edit-'+id).toggle();
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    //});
}
function save_request_amount(id){
	//UIkit.modal.confirm("Are you sure you want to save the requested amount?").then(function() {
        $.post('{{ URL::route("breakouts.addRequested", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'cost_id' : id,
			'amount' : $('#request-amount-'+id).val(),
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				if(data['new_amount'] >= 0){
					new_amount = '$' + data['new_amount'].toFixed(2);
					$('#request-amount-value-'+id).html(new_amount);
				}
				UIkit.toggle('.request-amount-edit-'+id).toggle();
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    //});
}
function save_approved_amount(id){
	//UIkit.modal.confirm("Are you sure you want to save the approved amount?").then(function() {
        $.post('{{ URL::route("breakouts.addApproved", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'request_id' : id,
			'amount' : $('#po-amount-'+id).val(),
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				if(data['new_amount'] >= 0){
					new_amount = '$' + data['new_amount'].toFixed(2);
					$('#po-amount-value-'+id).html(new_amount);
				}
				UIkit.toggle('.po-amount-edit-'+id).toggle();
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    //});
}

@if(Auth::user()->isLandbankParcelApprover() || Auth::user()->isHFAAdmin())
function add_parcel_to_request(){
	UIkit.modal.confirm("Are you sure you want to submit this parcel to the current reimbursement request?").then(function() {
        $.post('{{ URL::route("approval.lb_parcel_to_request", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#breakout-tab').trigger('click');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab').trigger('click');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}

function remove_parcel_from_request(){
	UIkit.modal.confirm("Are you sure you want to remove this parcel from the current reimbursement request?").then(function() {
        $.post('{{ URL::route("approval.lb_remove_parcel_from_request", [$parcel_id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#breakout-tab-content').load('/breakouts/parcel/{{$parcel_id}}');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab-content').load('/breakouts/parcel/{{$parcel_id}}');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}

function reassignParcel(reqId){
	UIkit.modal.confirm("Are you sure you want to reassign this parcel to request "+reqId+"?").then(function() {
        $.get('/parcels/reassign/{{$parcel_id}}?requestId='+reqId, function(data) {
			if(data['message']!='' && data['error']!=1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab-content').load('/breakouts/parcel/{{$parcel_id}}');
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				$('#breakout-tab-content').load('/breakouts/parcel/{{$parcel_id}}');
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
		
    });
}
@endif

function addAmount(itemId,formId,type){
	var addedWarning = "";
	if(window.addedBreakouts > 1){
		addedWarning = " Note that I am only saving the amount for this row. Any other edits on other rows will be lost."
	}
	//UIkit.modal.confirm("Are you sure you want to save this amount?"+addedWarning, function(){
    	// will be executed on confirm.

    	 $('#'+formId).submit();
    	// var frm = $('#'+formId);
		   //  frm.submit(function (ev) {
		   //      $.ajax({
		   //          type: frm.attr('method'),
		   //          url: frm.attr('action'),
		   //          data: frm.serialize(),
		   //          success: function (data) {
		   //              alert('ok');
		   //          }
		   //      });

		   //      ev.preventDefault();
		   //  });
		//});
        
	
}
@if(isset($firstRequest))
	$('#{{$firstRequest}}').focus;
@endif
</script>
