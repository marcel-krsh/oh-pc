@extends('layouts.simplerAllita')
@section('head')
<title>INV: {{$invoice->id}}</title>
@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script> -->
<style>
#invoicepanel .uk-panel-box-white {background-color:#ffffff;}
#invoicepanel .uk-panel-box .uk-panel-badge {}
#invoicepanel .green {color:#82a53d;}
#invoicepanel .blue {color:#005186;}
#invoicepanel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#invoicepanel table tfoot tr td {border: none;}
#invoicepanel textarea {width:100%;}
#invoicepanel .note-list-item:last-child { border: none;}
#invoicepanel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#invoicepanel .property-summary {margin-top:0;}
</style>
<div id="invoicepanel">
	<div class="uk-panel uk-panel-box uk-panel-box-white">
		<div class="uk-panel uk-panel-header uk-hidden@m uk-hidden@l" style="text-align:center;">
			<img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px;" />
			<h6 class="uk-panel-title uk-text-center"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />REIMBURSEMENT INVOICE {{$invoice->id}}</h6>
		</div>
		<div class="uk-panel uk-panel-header uk-visible@m">
			<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
			<h6 class="uk-panel-title uk-text-center uk-text-left-small"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />REIMBURSEMENT INVOICE {{$invoice->id}}</h6>

		</div>

		<div class="uk-panel no-padding-bottom">
			<div uk-grid>
				<div class="uk-width-1-2@m uk-width-3-4@l uk-width-1-1@s uk-margin-top">
					{{$invoice->entity->entity_name}}
					@if($invoice->legacy == 1)
					<div class=" uk-label uk-label-warning">Legacy</div>
					@endif
					@if($invoice->status != null)
					<div class=" uk-label">{{$invoice->status->invoice_status_name}}</div>
					@endif
				</div>
			</div>
		</div>

		<div class="uk-panel uk-panel-divider">

			<div uk-grid>
				<div class="uk-width-1-2@s uk-panel">
					<div class="uk-panel uk-panel-header">
						<h6 class="uk-panel-title">SUMMARY OF PROPERTIES</h6>
					</div>
					<table class="uk-table property-summary">
				        <thead>
				            <tr>
				                <th class="uk-width-3-4">Parcel</th>
				                <th class="uk-width-1-4 uk-text-right">Total Draw Amount</th>
				            </tr>
				        </thead>
				        <tbody>
				        	@foreach ($invoice->parcels as $parcel)
				            <tr>
				                <td><a onclick="window.open('/viewparcel/{{$parcel->id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN parcel">{{$parcel->parcel_id}}</a></td>
				                <td class="uk-text-right">{{money_format('%n', $parcel->invoicedTotal())}}</td>
				            </tr>
				            @endforeach
				        </tbody>
				        <tfoot>
				            <tr>
				                <td>
				                	<strong>Total</strong>
			                	</td>
				                <td class="uk-text-right"><strong>{{$total}}</strong></td>
				            </tr>
				        </tfoot>
				    </table>
				</div>
				<div class="uk-width-1-2@s uk-panel">
					<div class="uk-panel uk-panel-header">
						<h6 class="uk-panel-title">ACCOUNT SUMMARY</h6>
					</div>
					<div class="uk-grid">
						<div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
							<div class="uk-panel uk-panel-box">
							    <div class="uk-panel-badge uk-label">
							    	{{$stat['Total_Parcels'] ?? 'n/a'}}
							    </div>
							    <h3 class="uk-panel-title">
							    	{{$invoice->program->program_name ?? 'n/a'}}
							    </h3>
								<hr class="dashed-hr" class="uk-margin-bottom">

								<!-- This is the container of the content items -->
								<ul class="uk-list">
								    <li>
								    	Account ID: {{$invoice->account->id ?? 'n/a'}}
								    	<ul>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span uk-icon="plus-square-o"></span> Deposits:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Deposits_Made'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span uk-icon="plus-square-o"></span> Recaptures Received:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Recaptures_Received'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span uk-icon="plus-square-o"></span> Dispositions Received:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Dispositions_Received'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span uk-icon="minus-square-o"></span> Reimbursments Paid:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Reimbursements_Paid'])}} )
								    				</span>
								    			</span>
								    		</li>

								    		<li>
								    			<span class="parcel-district-format">
								    				<span uk-icon="minus-square-o"></span> Transfers:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Transfers_Made'])}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
								    				<span uk-icon="minus-square-o"></span> Line of Credit:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Line_Of_Credit'])}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format"  style="border-bottom:1px solid black;">
								    				<span uk-icon="square-o"></span> <strong>BALANCE:</strong>
								    				<span class="parcel-district-format-number">
								    					<?php
$accountingBalance = ($stat['Deposits_Made'] + $stat['Recaptures_Received'] + $stat['Dispositions_Received']) - ($stat['Transfers_Made'] + $stat['Reimbursements_Paid'] + $stat['Line_Of_Credit']);
?>
								    					<strong>{{money_format('%(8n', $accountingBalance)}}</strong>
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
								    				&nbsp;&nbsp;&nbsp;<span uk-icon="minus-square-o"></span> Pending Reimbursements:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', ($stat['Total_Invoiced'] - $stat['Reimbursements_Paid']))}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format"  style="border-bottom:2px solid black;">
								    				<span uk-icon="square-o"></span> <strong>AVAILABLE BALANCE:</strong>
								    				<span class="parcel-district-format-number">
								    					<?php
$accountingBalance = $accountingBalance - ($stat['Total_Invoiced'] - $stat['Reimbursements_Paid']);
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
					<div class="uk-panel uk-panel-header uk-panel-divider">
						<h6 class="uk-panel-title">NOTES</h6>
					</div>
					<div class="uk-form-row">
						<div id="invoice_notes">
							@foreach ($invoice->notes as $note)
					        <div class="uk-width-1-1 note-list-item" id="note-{{ $note->id}}" >
					            <div class="uk-grid">
					                <div class="uk-width-1-6 uk-width-1-3@m note-type-and-who ">
					                    <span uk-tooltip="pos:top-left;title:{{ $note->owner->name}}" class="no-print">
					                        <div class="user-badge user-badge-note-item user-badge-{{ $note->owner->badge_color}} no-float">{{ $note->initials}}</div>
					                    </span>
					                    <span class="print-only">{{ $note->owner->name}}<br></span>
					                    <span class=" note-item-date-time">{{ date('m/d/Y', strtotime($note->created_at)) }} </span>
					                </div>
					                <div class="uk-width-2-3 uk-width-1-2@m note-item-excerpt">
					                     {{ $note->note}}
					                </div>
					                <div class="uk-width-1-6">
					                </div>
					            </div>
					        </div>
					        @endforeach
					    </div>
				        <form name="newNoteForm" id="newNoteForm" method="post">
				        <div class="uk-width-1-1">
						    <textarea id="invoice-note"  class="uk-textarea" rows="8" name="invoice-note" placeholder="Enter note here."></textarea>
						</div>
						<div class="uk-width-1-1 uk-grid">
							<div class="uk-width-1-3@m uk-width-1-1@s uk-margin-top"><a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitInvoiceNote({{$invoice->id}})"><span uk-icon="pull"></span> SAVE</a></div>

							<div class="uk-width-1-3"></div>
							<div class="uk-width-1-3"></div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
@if(count($invoice->transactions) || Auth::user()->isHFAAdmin() || Auth::user()->isHFAFiscalAgent())
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">TRANSACTIONS</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid class="approvals">
				<div class="uk-width-1-1">
					<table class="uk-table uk-overflow-container" id="lb-approvers-list">
						<thead>
							<th><small>Date</small></th>
							<th><small>Transaction #</small></th>
							<th><small>Note</small></th>
							<th><small>Amount</small></th>
							<th><small>Status</small></th>
							<th><small>Actions</small></th>
						</thead>
						<tfoot>
							<tr>
								<td class="uk-text-right" colspan="3">Balance</td>
								<td>{{money_format('%(8n', ($balance))}}</td>
								<td></td>
							</tr>
						</tfoot>
						<tbody>
						@foreach($invoice->transactions as $transaction)
							<tr>
								<td>
									@if($transaction->date_entered && $transaction->date_entered != "0000-00-00")
						 				{{date('m/d/Y',strtotime($transaction->date_entered))}}
						 			@else
						 			NA
						 			@endif
								</td>
								<td>{{$transaction->id}}</td>
								<td>
									@if($transaction->transaction_note)
									<a onclick="UIkit.modal.alert('{{addslashes(json_encode($transaction->transaction_note))}}')" class="uk-link-muted">{{substr($transaction->transaction_note,0,85)}}...</a>
									@endif
								</td>
								<td>
									@if($transaction->credit_debit == 'c')
						 			{{money_format('%(8n', $transaction->amount)}}
						 			@else
						 			{{money_format('%(8n', ($transaction->amount * -1))}}
						 			@endif
								</td>
								<td>
									@if($transaction->status)
									{{$transaction->status->status_name}}
									@endif
								</td>
								<td><a uk-icon="pencil" onclick="dynamicModalLoad('transaction/edit/{{$transaction->id}}/reload');" uk-tooltip="Edit"></a></td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@if(Auth::user()->isHFAAdmin() || Auth::user()->isHFAFiscalAgent())
				<div class="uk-width-1-1">
					<button style="margin-top:5px" class="uk-button uk-button-success uk-float-right uk-width-1-3@m uk-width-1-1@s" type="button" onclick="dynamicModalLoad('transaction/newFromInvoice/{{$invoice->id}}')"><span uk-icon="credit-card"></span> NEW PAYMENT</button>
				</div>
				@endif
			</div>
		</div>
@endif
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">GENERAL INFORMATION</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid>
				<div class="uk-width-1-2@s uk-margin-top">
					<span class="uk-text-bold uk-text-small">Submit to:</span><br />
					NIP Program Manager<br />
					{{$nip->entity_name}}<br />
					{{$nip->address1}}<br />
					@if($nip->address2) {{$nip->address2}}<br /> @endif
					{{$nip->city}}, {{$nip->state->state_acronym}} {{$nip->zip}}<br /><br />

					<span class="uk-text-bold uk-text-small">Contact Person</span><br />
					{{$nip->user->name}}<br />
					{{$nip->phone}}<br />
					{{$nip->user->email}}<br />
				</div>
				<div class="uk-width-1-2@s uk-margin-top">
					<span class="uk-text-bold uk-text-small">Name and Address of NIP Partner</span><br />
					{{$invoice->entity->entity_name}}<br />
					{{$invoice->entity->address1}}<br />
					@if($invoice->entity->address2) {{$invoice->entity->address2}}<br /> @endif
					{{$invoice->entity->city}}, {{$invoice->entity->state->state_acronym}} {{$invoice->entity->zip}}<br />
					{{$invoice->entity->phone}}<br />
					{{$invoice->entity->user->email}}<br /><br />

					<span class="uk-text-bold uk-text-small">FTI Number</span><br />
					@if(!$invoice->entity->fti) N/A @endif {{$invoice->entity->fti}}<br />
				</div>
			</div>
		</div>
@if($invoice->legacy != 1)
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">LANDBANK APPROVALS</h6>
		</div>

		@if(!$hasApprovals['landbank'])
		<div class="uk-panel">
			<div class="uk-grid approvals">
				@if($invoice->legacy == 1)
				<p>This is a legacy invoice.</p>
				@else
				<p>This invoice is pending approval.</p>
				@endif
			</div>
		</div>
		@endif

		<div class="uk-panel">
			<div uk-grid class="approvals">
				<div class="uk-width-1-1">
		            <table class="uk-table uk-overflow-container" id="lb-approvers-list">
						<thead>
							<th><small>Name</small></th>
							<th><small>Decisions</small></th>
							@if( $isApprover['landbank'] || Auth::user()->isHFAAdmin())
							<th class="no-print"><small>Action</small></th>
							@else
							<th class="no-print"></th>
							@endif
						</thead>
						<tfoot class="no-print">
							<tr>
								<td></td>
								<td colspan="2">
									@if( $isApprover['landbank'] )
									<div>
										<h4>If an approver is not able to login and click "approve", please print the invoice using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="window.print()">Print Invoice</button>
													</h4>
										<h4>Select approvers who signed the document you are about to upload.</h4>
										<div class="communication-selector">
								            <ul class="uk-subnav document-menu">
								            	@foreach ($approvals['landbank'] as $approval)
					                            <li>
					                                <input  class="uk-checkbox" name="landbank-approvers-id-checkbox" id="landbank-approvers-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox">
					                                <label for="approver-id-{{ $approval->approver->id }}">
					                                    @if($approval->approver->entity_id == 1) [HFA] @endif {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach
					                        </ul>
					                    </div>
									</div>
										<div class="uk-display-inline" id="list-item-upload-box">

						                    <div id="upload-drop-lb" class="js-upload uk-placeholder uk-text-center">
						                        <span class="a-higher"></span>
						                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
						                        <div uk-form-custom>
						                            <input type="file" multiple>
						                            <span class="uk-link">by browsing and selecting it here.</span>
						                        </div>
						                    </div>

						                    <progress id="js-progressbar-lb" class="uk-progress" value="0" max="100" hidden></progress>

						                    <script>
						                    $(function(){
						                        var bar = document.getElementById('js-progressbar-lb');

						                        settings    = {

						                            url: '{{ URL::route("approval.uploadInvoiceSignature", $invoice->id) }}',
						                            multiple: false,
						                            allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

						                            headers : {
						                                'enctype' : 'multipart/form-data'
						                            },

						                            beforeSend: function () {
						                                // console.log('beforeSend', arguments);
						                            },
						                            beforeAll: function (settings) {
						                                // console.log('beforeAll', arguments);
						                                var approverArray = [];
				                                        $("input:checkbox[name=landbank-approvers-id-checkbox]:checked").each(function(){
				                                                approverArray.push($(this).val());
				                                            });
				                                        settings.params.approvers = approverArray;
				                                        settings.params._token = '{{ csrf_token() }}';
				                                        approvers = approverArray;
				                                        if(approverArray.length > 0){
				                                            console.log('Approvers selected: '+approverArray);
				                                        }else{
				                                            UIkit.modal.alert('You must select at least one approver.',{stack: true});
				                                            return false;
				                                        }

														settings.params.approvaltype = 4;
						                            },
						                            load: function () {
						                                // console.log('load', arguments);
						                            },
						                            error: function () {
						                                // console.log('error', arguments);
						                            },
						                            complete: function () {
						                                // console.log('complete', arguments);
						                            },

						                            loadStart: function (e) {
						                                // console.log('loadStart', arguments);

						                                bar.removeAttribute('hidden');
						                                bar.max = e.total;
						                                bar.value = e.loaded;
						                            },

						                            progress: function (e) {
						                                // console.log('progress', arguments);

						                                bar.max = e.total;
						                                bar.value = e.loaded;
						                            },

						                            loadEnd: function (e) {
						                                // console.log('loadEnd', arguments);

						                                bar.max = e.total;
						                                bar.value = e.loaded;
						                            },

						                            completeAll: function (response) {

						                                var documentids = response.response;

					                                    setTimeout(function () {
						                                    bar.setAttribute('hidden', 'hidden');
						                                }, 250);

					                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
					                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{stack: true}).then(function(val){
					                                        $.post('{{ URL::route("approval.uploadInvoiceSignatureComments", $invoice->id) }}', {
					                                                'postvars' : documentids,
					                                                'comment' : val,
					                                                '_token' : '{{ csrf_token() }}'
					                                                }, function(data) {
					                                                    if(data!='1'){
					                                                        UIkit.modal.alert(data,{stack: true});
					                                                    } else {
					                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
					                                                            location.reload();
					                                                    }
					                                        });
					                                    });
						                            }

						                        };

						                        var select = UIkit.upload('.js-upload', settings);

						                    });
						                    </script>
						            	</div>
						            	@endif
								</td>
							</tr>
						</tfoot>
						<tbody>
							@php $lb_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
							@foreach ($approvals['landbank'] as $approval)
							@php $lb_approver_count = $lb_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}_4">
								<td class="uk-width-2-5">@if($approval->approver->entity_id == 1) [HFA] @endif {{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="action_{{$approval->approver->id}}_4">
									@if(!count($approval->actions))
									<small class="no-action">No action yet.</small>
									@endif
									@foreach($approval->actions as $action)
									@if($action->action_type->id == 1)
									<div class="uk-label uk-label-success">Approved</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 4)
									<div class="uk-label uk-label-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 5)
									<div class="uk-label uk-label-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
									@endif
									@endforeach
								</td>
								@if( $isApprover['landbank'] || Auth::user()->isHFAAdmin())
								<td class="uk-width-1-5 no-print">
									@if(Auth::user()->isLandbankInvoiceApprover() || Auth::user()->isHFAAdmin())
									@if((Auth::user()->isLandbankSimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())

										@if( Auth::user()->id == $approval->approver->id )
										@php $user_is_in_approvers_list = 1; @endphp
										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveInvoice(4);">Approve</button>
										@endif

										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineInvoice(4);">Decline</button>
										@endif
										@endif
										@if( $isApprover['landbank'] )
				 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}},4);" class="uk-button uk-button-small uk-button-warning"><span uk-icon="trash"></span></button>
			 							@endif
			 						@endif
			 						@endif
			 					</td>
			 					@else
			 					<td class="uk-width-1-5 no-print">
			 					</td>
			 					@endif
							</tr>
							@endforeach
							@if($pending_approvers['landbank'])
							@foreach($pending_approvers['landbank'] as $pending_approver)
							<tr class="no-print">
								<td class="uk-text-muted">
									{{$pending_approver->name}}
								</td>
								<td></td>
								@if( $isApprover['landbank'] || Auth::user()->isHFAAdmin() || Auth::user()->id == $pending_approver->id)
								<td>
									@if((Auth::user()->isLandbankSimpleApproval() && $pending_approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())
									<button class="uk-button uk-button-small uk-button-success" onclick="addLBApprover({{$pending_approver->id}});">Add as approver</button>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
							@endif
							@if(Auth::user()->isHFAAdmin() && !$isApprover['landbank'] && $user_is_in_approvers_list == 0)
							<tr class="no-print">
								<td class="uk-text-muted">
									[HFA] {{Auth::user()->name}}
								</td>
								<td></td>
								<td>
									<button class="uk-button uk-button-small uk-button-success" onclick="addLBApprover({{Auth::user()->id}});">Add me as approver</button>
								</td>
							</tr>
						@endif
						</tbody>
					</table>
		        </div>
			</div>
		</div>


@if($isApproved['landbank'] == 1 && $invoice->status_id == 2)
<div class="uk-panel uk-panel-divider">
	<div uk-grid>
		<div class="uk-width-1-1@s uk-margin-top no-print">
			<button style="margin-top:5px" class="uk-button uk-button-success uk-float-right uk-width-1-3@m uk-width-1-1@s" type="button" onclick="submitInvoice();">Submit Invoice to HFA</button>
		</div>
	</div>
</div>
@endif
@if($invoice->status_id == 5)
<div class="uk-panel uk-panel-divider">
	<div uk-grid>
		<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
			<div class="uk-alert-warning">This invoice has been declined by HFA.</div>
		</div>
	</div>
</div>
@elseif($invoice->status_id == 3)
<div class="uk-panel uk-panel-divider">
	<div uk-grid>
		<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
			<div class="uk-alert">This invoice has been submitted to HFA for approval.</div>
		</div>
	</div>
</div>
@elseif($invoice->status_id == 4)
<div class="uk-panel uk-panel-divider">
	<div uk-grid>
		<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
			<div class="uk-alert">This invoice is pending payment.</div>
		</div>
	</div>
</div>
@elseif($invoice->status_id == 6)
<div class="uk-panel uk-panel-divider">
	<div uk-grid>
		<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
			<div class="uk-alert">This invoice has been fully paid.</div>
		</div>
	</div>
</div>
@endif


	@if($isApproved['landbank'] == 1 && ($invoice->status_id == 3 || $invoice->status_id == 5 || $invoice->status_id == 4 || $invoice->status_id == 6))
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">HFA APPROVALS</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid class="approvals">
				<div class="uk-width-1-1">
		            <table class="uk-table uk-overflow-container" id="lb-approvers-list">
						<thead>
							<th><small>Name</small></th>
							<th><small>Decisions</small></th>
							@if( $isApprover['hfa_primary'] || Auth::user()->isHFAAdmin())
							<th class="no-print"><small>Action</small></th>
							@else
							<th class="no-print"></th>
							@endif
						</thead>
						@if(!$isApproved['hfa_primary']  || !$isApproved['hfa_secondary'] || !$isApproved['hfa_tertiary'] || !$isReadyForPayment)
						<tfoot class="no-print">
							<tr>
								<td></td>
								<td colspan="2">

									<div>
										<h4>If an approver is not able to login and click "approve", please print the invoice using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="window.print()">Print Invoice</button>
													</h4>
										<h4>Select approvers who signed the document you are about to upload.</h4>
										<div class="communication-selector">
								            <ul class="uk-subnav document-menu">

					                        	@foreach ($approvals['hfa_primary'] as $approval)
					                            <li>
					                                <input  class="uk-checkbox" name="hfa1-approvers-id-checkbox" id="hfa1-approvers-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox">
					                                <label for="hfa1-approvers-id-{{ $approval->approver->id }}">
					                                    @if($approval->approver->entity_id == 1) [PRIMARY] @endif {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach

					                        	@foreach ($approvals['hfa_secondary'] as $approval)
					                            <li>
					                                <input name="hfa1-approvers-id-checkbox" id="hfa1-approvers-id-{{ $approval->approver->id }}"  class="uk-checkbox" value="{{ $approval->approver->id }}" type="checkbox">
					                                <label for="hfa1-approvers-id-{{ $approval->approver->id }}">
					                                    @if($approval->approver->entity_id == 1) [SECONDARY] @endif {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach

					                            @foreach ($approvals['hfa_tertiary'] as $approval)
					                            <li>
					                                <input  class="uk-checkbox" name="hfa1-approvers-id-checkbox" id="hfa1-approvers-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox">
					                                <label for="hfa1-approvers-id-{{ $approval->approver->id }}">
					                                    @if($approval->approver->entity_id == 1) [TERTIARY] @endif {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach

					                        </ul>
					                    </div>
									</div>
										<div class="uk-display-inline no-print" id="list-item-upload-box">

						                    <div id="upload-drop-hfa1" class="js-upload-hfa1 uk-placeholder uk-text-center">
						                        <span class="a-higher"></span>
						                        <span class="uk-text-middle"> Please upload your signed document by dropping it here or</span>
						                        <div uk-form-custom>
						                            <input type="file" multiple>
						                            <span class="uk-link">by browsing and selecting it here.</span>
						                        </div>
						                    </div>

						                    <progress id="js-progressbar-hfa1" class="uk-progress" value="0" max="100" hidden></progress>

						                    <script>
						                    $(function(){
						                        var bar2 = document.getElementById('js-progressbar-hfa1');

						                        settings2    = {

						                            url: '{{ URL::route("approval.uploadInvoiceSignature", $invoice->id) }}',
						                            multiple: false,
						                            allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

						                            headers : {
						                                'enctype' : 'multipart/form-data'
						                            },

						                            beforeSend: function () {
						                                // console.log('beforeSend', arguments);
						                            },
						                            beforeAll: function (settings2) {
						                                // console.log('beforeAll', arguments);

				                                        var approverArray = [];
				                                        $("input:checkbox[name=hfa1-approvers-id-checkbox]:checked").each(function(){
				                                                approverArray.push($(this).val());
				                                            });
				                                        settings2.params.approvers = approverArray;
			                                        	settings2.params._token = '{{ csrf_token() }}';
				                                        approvers = approverArray;
				                                        if(approverArray.length > 0){
				                                            console.log('Approvers selected: '+approverArray);
				                                        }else{
				                                            UIkit.modal.alert('You must select at least one approver.',{stack: true});
				                                            return false;
				                                        }

														@if($isApproved['hfa_secondary'] && $isApproved['hfa_primary'])
														settings2.params.approvaltype = 10;
														@elseif($isApproved['hfa_primary'])
														settings2.params.approvaltype = 9;
														@else
														settings2.params.approvaltype = 8;
														@endif
						                            },
						                            load: function () {
						                                // console.log('load', arguments);
						                            },
						                            error: function () {
						                                // console.log('error', arguments);
						                            },
						                            complete: function () {
						                                // console.log('complete', arguments);
						                            },

						                            loadStart: function (e) {
						                                // console.log('loadStart', arguments);

						                                bar2.removeAttribute('hidden');
						                                bar2.max = e.total;
						                                bar2.value = e.loaded;
						                            },

						                            progress: function (e) {
						                                // console.log('progress', arguments);

						                                bar2.max = e.total;
						                                bar2.value = e.loaded;
						                            },

						                            loadEnd: function (e) {
						                                // console.log('loadEnd', arguments);

						                                bar2.max = e.total;
						                                bar2.value = e.loaded;
						                            },

						                            completeAll: function (response) {

						                                var documentids = response.response;

					                                    setTimeout(function () {
						                                    bar2.setAttribute('hidden', 'hidden');
						                                }, 250);

					                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
					                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{stack: true}).then(function(val){
					                                        $.post('{{ URL::route("approval.uploadInvoiceSignatureComments", $invoice->id) }}', {
					                                                'postvars' : documentids,
					                                                'comment' : val,
					                                                '_token' : '{{ csrf_token() }}'
					                                                }, function(data) {
					                                                    if(data!='1'){
					                                                        UIkit.modal.alert(data,{stack: true});
					                                                    } else {
					                                                        UIkit.modal.alert('Your comment has been saved.',{stack: true});
					                                                            location.reload();
					                                                    }
					                                        });
					                                    });
						                            }

						                        };

						                        var select = UIkit.upload('.js-upload-hfa1', settings2);

						                    });
						                    </script>
						            	</div>

								</td>
							</tr>
						</tfoot>
						@endif
						<tbody>
							@php $lb_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
							@foreach ($approvals['hfa_primary'] as $approval)

							@php $lb_approver_count = $lb_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}_8">
								<td class="uk-width-2-5">@if($approval->approver->entity_id == 1) [PRIMARY] @endif {{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="action_{{$approval->approver->id}}_8">
									@if(!count($approval->actions))
									<small class="no-action">No action yet.</small>
									@endif
									@foreach($approval->actions as $action)
									@if($action->action_type->id == 1)
									<div class="uk-label uk-label-success">Approved</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 4)
									<div class="uk-label uk-label-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 5)
									<div class="uk-label uk-label-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
									@endif
									@endforeach
								</td>
								@if( $isApprover['hfa_primary'] || Auth::user()->isHFAAdmin())
								<td class="uk-width-1-5 no-print">
									@if(Auth::user()->isHFAPrimaryInvoiceApprover() || Auth::user()->isHFAAdmin())
									@if((Auth::user()->isHFASimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())

										@if( Auth::user()->id == $approval->approver->id )
										@php $user_is_in_approvers_list = 1; @endphp
										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveInvoice(8);">Approve</button>
										@endif

										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineInvoice(8);">Decline</button>
										@endif
										@endif
										@if( $isApprover['hfa_primary'] )
				 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}},8);" class="uk-button uk-button-small uk-button-warning"><span uk-icon="trash"></span></button>
			 							@endif
			 						@endif
			 						@endif
			 					</td>
			 					@endif
							</tr>

							@endforeach
							@php $user_is_in_pending_list = 0; @endphp
							@if($pending_approvers['hfa_primary'])
							@foreach($pending_approvers['hfa_primary'] as $pending_approver)
							@if(Auth::user()->id == $pending_approver->id)
							@php $user_is_in_pending_list = 1; @endphp
							@endif
							<tr class="no-print">
								<td class="uk-text-muted">
									{{$pending_approver->name}}
								</td>
								<td></td>
								@if( $isApprover['hfa_primary'] || Auth::user()->isHFAAdmin() || Auth::user()->id == $pending_approver->id)
								<td>
									@if((Auth::user()->isHFASimpleApproval() && $pending_approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())
									<button class="uk-button uk-button-small uk-button-success" onclick="addHFAApprover({{$pending_approver->id}},8);">Add as approver</button>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
							@endif




							@php $lb_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
							@foreach ($approvals['hfa_secondary'] as $approval)
							@php $lb_approver_count = $lb_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}_9">
								<td class="uk-width-2-5">@if($approval->approver->entity_id == 1) [SECONDARY] @endif {{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="action_{{$approval->approver->id}}_9">
									@if(!count($approval->actions))
									<small class="no-action">No action yet.</small>
									@endif
									@foreach($approval->actions as $action)
									@if($action->action_type->id == 1)
									<div class="uk-label uk-label-success">Approved</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 4)
									<div class="uk-label uk-label-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 5)
									<div class="uk-label uk-label-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
									@endif
									@endforeach
								</td>
								@if( $isApprover['hfa_secondary'] || Auth::user()->isHFAAdmin())
								<td class="uk-width-1-5 no-print">
									@if(Auth::user()->isHFASecondaryInvoiceApprover() || Auth::user()->isHFAAdmin())
									@if((Auth::user()->isHFASimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())

										@if( Auth::user()->id == $approval->approver->id )
										@php $user_is_in_approvers_list = 1; @endphp
										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveInvoice(9);">Approve</button>
										@endif

										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineInvoice(9);">Decline</button>
										@endif
										@endif
										@if( $isApprover['hfa_secondary'] )
				 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}},9);" class="uk-button uk-button-small uk-button-warning"><span uk-icon="trash"></span></button>
			 							@endif
			 						@endif
			 						@endif
			 					</td>
			 					@endif
							</tr>
							@endforeach
							@php $user_is_in_pending_list = 0; @endphp
							@if($pending_approvers['hfa_secondary'])
							@foreach($pending_approvers['hfa_secondary'] as $pending_approver)
							@if(Auth::user()->id == $pending_approver->id)
							@php $user_is_in_pending_list = 1; @endphp
							@endif
							<tr class="no-print">
								<td class="uk-text-muted">
									{{$pending_approver->name}}
								</td>
								<td></td>
								@if( $isApprover['hfa_secondary'] || Auth::user()->isHFAAdmin() || Auth::user()->id == $pending_approver->id)
								<td>
									@if((Auth::user()->isHFASimpleApproval() && $pending_approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())
									<button class="uk-button uk-button-small uk-button-success" onclick="addHFAApprover({{$pending_approver->id}},9);">Add as approver</button>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
							@endif



							@php $lb_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
							@foreach ($approvals['hfa_tertiary'] as $approval)
							@php $lb_approver_count = $lb_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}_10">
								<td class="uk-width-2-5">@if($approval->approver->entity_id == 1) [TERTIARY] @endif {{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="action_{{$approval->approver->id}}_10">
									@if(!count($approval->actions))
									<small class="no-action">No action yet.</small>
									@endif
									@foreach($approval->actions as $action)
									@if($action->action_type->id == 1)
									<div class="uk-label uk-label-success">Approved</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 4)
									<div class="uk-label uk-label-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 5)
									<div class="uk-label uk-label-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
									@endif
									@endforeach
								</td>
								@if( $isApprover['hfa_tertiary'] || Auth::user()->isHFAAdmin())
								<td class="uk-width-1-5 no-print">
									@if(Auth::user()->isHFATertiaryInvoiceApprover() || Auth::user()->isHFAAdmin())
									@if((Auth::user()->isHFASimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())

										@if( Auth::user()->id == $approval->approver->id )
										@php $user_is_in_approvers_list = 1; @endphp
										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveInvoice(10);">Approve</button>
										@endif

										@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
										<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineInvoice(10);">Decline</button>
										@endif
										@endif
										@if( $isApprover['hfa_tertiary'] )
				 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}},10);" class="uk-button uk-button-small uk-button-warning"><span uk-icon="trash"></span></button>
			 							@endif
			 						@endif
			 						@endif
			 					</td>
			 					@endif
							</tr>
							@endforeach
							@php $user_is_in_pending_list = 0; @endphp
							@if($pending_approvers['hfa_tertiary'])
							@foreach($pending_approvers['hfa_tertiary'] as $pending_approver)
							@if(Auth::user()->id == $pending_approver->id)
							@php $user_is_in_pending_list = 1; @endphp
							@endif
							<tr class="no-print">
								<td class="uk-text-muted">
									{{$pending_approver->name}}
								</td>
								<td></td>
								@if( $isApprover['hfa_tertiary'] || Auth::user()->isHFAAdmin() || Auth::user()->id == $pending_approver->id)
								<td>
									@if((Auth::user()->isHFASimpleApproval() && $pending_approver->id == Auth::user()->id) || !Auth::user()->isLandbankSimpleApproval())
									<button class="uk-button uk-button-small uk-button-success" onclick="addHFAApprover({{$pending_approver->id}},10);">Add as approver</button>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
							@endif


						</tbody>
					</table>
		        </div>
			</div>
		</div>
		@endif
@endif

@if($invoice->legacy != 1)
		@if(!$isApproved['landbank'] || !$isApproved['hfa_primary']  || !$isApproved['hfa_secondary'] || !$isApproved['hfa_tertiary'] || !$isReadyForPayment)
		<div class="uk-panel uk-panel-header uk-panel-divider print-only">
			<h6 class="uk-panel-title">SIGNATURES</h6>
		</div>
		<div class="uk-panel uk-margin-top print-only">
			<div uk-grid>
				<div class="uk-width-1-1">

				</div>
			</div>
			@if(!$isApproved['landbank'])
			@foreach ($approvals['landbank'] as $approval)
			<div class="uk-panel uk-panel-header">
				<div class="uk-width-1-1 uk-margin-top ">
					<br /><br /><br />
					<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
						<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
						</p>
					</div>
				</div>
			</div>
			@endforeach
			@endif
			@if($isApproved['landbank'] && !$isApproved['hfa_primary'])
			@foreach ($approvals['hfa_primary'] as $approval)
			<div class="uk-panel uk-panel-header">
				<div class="uk-width-1-1 uk-margin-top ">
					<br /><br /><br />
					<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
						<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
						</p>
					</div>
				</div>
			</div>
			@endforeach
			@endif
			@if($isApproved['landbank'] && $isApproved['hfa_primary'] && !$isApproved['hfa_secondary'])
			@foreach ($approvals['hfa_secondary'] as $approval)
			<div class="uk-panel uk-panel-header">
				<div class="uk-width-1-1 uk-margin-top ">
					<br /><br /><br />
					<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
						<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
						</p>
					</div>
				</div>
			</div>
			@endforeach
			@endif
			@if($isApproved['landbank'] && $isApproved['hfa_primary'] && $isApproved['hfa_secondary'] && !$isReadyForPayment)
			@foreach ($approvals['hfa_tertiary'] as $approval)
			<div class="uk-panel uk-panel-header">
				<div class="uk-width-1-1 uk-margin-top ">
					<br /><br /><br />
					<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
						<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
						</p>
					</div>
				</div>
			</div>
			@endforeach
			@endif
		</div>
		@endif
@endif
		@if($isReadyForPayment && $invoice->status_id == 3)
		<div class="uk-panel uk-panel-divider">
			<div uk-grid>
				<div class="uk-width-1-1@s uk-margin-top no-print">
					<button style="margin-top:5px" class="uk-button uk-button-success uk-float-right uk-width-1-3@m uk-width-1-1@s" type="button" onclick="sendForPayment();">Send approved invoice to fiscal agent for payment.</button>
				</div>
			</div>
		</div>
		@endif
		@if($invoice->status_id == 4)
		<div class="uk-panel uk-panel-divider">
				<div uk-grid>
					<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
						<div class="uk-alert">This invoice was approved and is pending payment.</div>
					</div>
				</div>
			</div>
		@endif

	</div>
</div>


<script type="text/javascript">
@if(Auth::user()->isLandbankInvoiceApprover() || Auth::user()->isHFAPrimaryInvoiceApprover() || Auth::user()->isHFASecondaryInvoiceApprover() || Auth::user()->isHFATertiaryInvoiceApprover() || Auth::user()->isHFAAdmin())

@if((Auth::user()->isHFAAdmin() || Auth::user()->isLandbankInvoiceApprover()) && $invoice->status->id == 2 && $isApproved['landbank'] == 1)
	function submitInvoice(){
		UIkit.modal.confirm("Are you sure you want to submit the invoice?").then(function() {
	        $.post('{{ URL::route("invoice.submitInvoice", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}
@endif

	function sendForPayment(){
		UIkit.modal.confirm("Are you sure you want to notify the fiscal agent and send this invoice for payment?").then(function() {
	        $.post('{{ URL::route("invoice.sendForPayment", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function approveInvoice(approval_type = 4){
		UIkit.modal.confirm("Are you sure you want to approve this invoice?").then(function() {
	        $.post('{{ URL::route("invoice.approve", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}',
				'approval_type' : approval_type
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

@if(Auth::user()->isHFAAdmin())
	function addHFAApprover(id,approval_type = 4){
		$.post('{{ URL::route("invoice.addHFAApprover", [$invoice->id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'approval_type' : approval_type,
			'user_id' : id
		}, function(data) {
			if(data['message']!=''){
				UIkit.modal.alert(data['message']);
				location.reload();
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
	}
@endif

	function addLBApprover(id){
		$.post('{{ URL::route("invoice.addLBApprover", [$invoice->id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'user_id' : id
		}, function(data) {
			if(data['message']!=''){
				UIkit.modal.alert(data['message']);
				location.reload();
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
	}

	function declineInvoice(approval_type = 4){
		UIkit.modal.confirm("Are you sure you want to decline?").then(function() {
	        $.post('{{ URL::route("invoice.decline", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}',
				'approval_type' : approval_type
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#action_'+data['id']+"_"+data['approval_type']).append('<div class="uk-label uk-label-danger">Declined</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function remove(id,approval_type = 4){
		UIkit.modal.confirm("Are you sure you want to remove this approver?").then(function() {
	        $.post('{{ URL::route("invoice.removeApprover", [$invoice]) }}', {
				'id' : id,
				'approval_type' : approval_type,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);

				}else{
					$("#ap_"+data['id']+"_"+data['approval_type']).remove();
					location.reload();
				}
			} );
	    });
	}

	function approveInvoice(approval_type = 4){
		UIkit.modal.confirm("Are you ready to approve this invoice?").then(function() {
	        $.post('{{ URL::route("invoice.approve", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}',
				'approval_type' : approval_type
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function submitInvoiceNote(id) {
		var form = $('#newNoteForm');

		data = form.serialize();
		$.post('{{ URL::route("invoicenote.create", $invoice->id) }}', {
	            'invoice-note' : form.find( "textarea[name='invoice-note']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {

	            	if(data['id']!='' && data['id']!=null){
	                    // add note to list
	                	newinput = '<div class="uk-width-1-1 note-list-item" id="note-'+data['id']+'">'+
					        '    <div uk-grid>'+
					        '        <div class="uk-width-1-6 uk-width-1-3@m note-type-and-who ">'+
					        '<span uk-tooltip="pos:top-left;title:'+data['name']+'" class="no-print">'+
					        '                <div class="user-badge user-badge-note-item user-badge-'+data['badge_color']+' no-float">'+data['initials']+'</div>'+
					        '            </span>'+
					        '            <span class="print-only">'+data['name']+'<br></span>'+
					        '            <span class=" note-item-date-time">'+data['created_at_formatted']+' </span>'+
					        '        </div>'+
					        '        <div class="uk-width-2-3 uk-width-1-2@m note-item-excerpt">'+
					        '             '+form.find( "textarea[name='invoice-note']" ).val()
					        '        </div>'+
					        '        <div class="uk-width-1-6">'+
					        '        </div>'+
					        '    </div>'+
					        '</div>';
					  	$("#invoice_notes").append(newinput);
					  	form.find( "textarea[name='invoice-note']" ).val('');
				        UIkit.modal.alert('Your note has been saved.');
	                } else {
	                	UIkit.modal.alert(data);
	                }
		} );
	}
@endif
</script>
@stop
