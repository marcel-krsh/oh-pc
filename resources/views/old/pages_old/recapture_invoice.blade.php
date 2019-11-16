@extends('layouts.simplerAllita')
@section('head')
<title>RECAPTURE INVOICE: {{$invoice->id}}</title>
@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
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
			<h6 class="uk-panel-title uk-text-center"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />RECAPTURE INVOICE {{$invoice->id}}</h6>
		</div>
		<div class="uk-panel uk-panel-header uk-visible@m">
			<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
			<h6 class="uk-panel-title uk-text-center uk-text-left-small"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />RECAPTURE INVOICE {{$invoice->id}}</h6>

		</div>

		<div class="uk-panel no-padding-bottom">
			<div uk-grid">
				<div class="uk-width-1-1@s uk-width-1-2@m uk-width-3-4@l uk-margin-top">
					{{$invoice->entity->entity_name}}
					@if($legacy == 1)
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
						<h6 class="uk-panel-title">SUMMARY</h6>
					</div>
					<table class="uk-table property-summary">
				        <thead>
				            <tr>
				                <th class="uk-width-1-4">Parcel</th>
				                <th class="uk-width-1-4">Category</th>
				                <th class="uk-width-1-4">Description</th>
				                <th class="uk-width-1-4 uk-text-right">Total Amount</th>
				            </tr>
				        </thead>
				        <tbody>
				        @if($invoice->RecaptureItem)
				        	@foreach ($invoice->RecaptureItem as $recapture)
				            <tr>
				                <td><a onclick="window.open('/viewparcel/{{$recapture->parcel_id}}/recaptures-tab', '_blank')" class="uk-link-muted" uk-tooltip="OPEN PARCEL">{{$recapture->parcel['parcel_id']}}</a></td>
				                <td>{{$recapture->expenseCategory->expense_category_name}}</td>
				                <td>{{$recapture->description}}</td>
				                @if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())

								@endif
				                <td class="uk-text-right">{{money_format('%n', $recapture->amount)}}</td>


				            @endforeach
				        @endif
				        </tbody>
				        <tfoot>
				            <tr>
				            	@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())
				                <td colspan="3">
				                @else
				                 <td colspan="3">
				                @endif
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
					<div uk-grid>
						<div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
							<div class="uk-panel uk-panel-box">
							    <div class="uk-panel-badge uk-badge">
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
								    				<span class="a-circle-plus"></span> Deposits:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Deposits_Made'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-circle-plus"></span> Recaptures Received:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Recaptures_Received'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-circle-plus"></span> Dispositions Received:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Dispositions_Received'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-circle-minus"></span> Reimbursments Paid:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Reimbursements_Paid'])}} )
								    				</span>
								    			</span>
								    		</li>

								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-circle-minus"></span> Transfers:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Transfers_Made'])}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format" style="border-bottom:1px solid black;">
								    				<span class="a-circle-minus"></span> Line of Credit:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', $stat['Line_Of_Credit'])}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format"  style="border-bottom:1px solid black;">
								    				<span class="a-minus"></span> <strong>BALANCE:</strong>
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
								    				&nbsp;&nbsp;&nbsp;<span class="a-circle-minus"></span> Pending Reimbursements:
								    				<span class="parcel-district-format-number">
								    					( {{money_format('%(8n', ($stat['Total_Invoiced'] - $stat['Reimbursements_Paid']))}} )
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format"  style="border-bottom:2px solid black;">
								    				<span class="a-minus"></span> <strong>AVAILABLE BALANCE:</strong>
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
					            <div uk-grid>
					                <div class="uk-width-1-5 uk-width-2-5@m note-type-and-who ">
					                    <span uk-tooltip="pos:top-left;title:{{ $note->owner->name}}" class="no-print">
					                        <div class="user-badge user-badge-note-item user-badge-{{ $note->owner->badge_color}} no-float">{{ $note->initials}}</div>
					                    </span>
					                    <span class="print-only">{{ $note->owner->name}}<br></span>
					                    <span class=" note-item-date-time">{{ date('m/d/Y', strtotime($note->created_at)) }} </span>
					                </div>
					                <div class="uk-width-3-5 uk-width-3-5@m note-item-excerpt">
					                     {{ $note->note}}
					                </div>
					                <div class="uk-width-1-5">
					                </div>
					            </div>
					        </div>
					        @endforeach
					    </div>
				        <form name="newNoteForm" id="newNoteForm" method="post" class="no-print">
				        <div class="uk-width-1-1 no-print">
						    <textarea id="invoice-note" rows="8" name="invoice-note" class="uk-textarea" placeholder="Enter note here."></textarea>
						</div>
						<div class="uk-width-1-1 uk-grid no-print">
							<div class="uk-width-1-3@m uk-width-1-1@s uk-margin-top"><a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitRecaptureInvoiceNote({{$invoice->id}})"><span class="a-floppy"></span> SAVE</a></div>

							<div class="uk-width-1-3"></div>
							<div class="uk-width-1-3"></div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
@if( ((count($invoice->transactions) || Auth::user()->isHFAAdmin() || Auth::user()->isHFAFiscalAgent()) && $isReadyForPayment === 1) || ($legacy == 1 && Auth::user()->isHFAAdmin() || Auth::user()->isHFAFiscalAgent()))
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
								<td><a class="a-pencil-2" onclick="dynamicModalLoad('transaction/edit/{{$transaction->id}}/reload');" uk-tooltip title="Edit"></a></td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
				@if(Auth::user()->isHFAAdmin() || Auth::user()->isHFAFiscalAgent())
				@if($invoice->status_id == 4 || $invoice->status_id == 8 || $invoice->status_id == 7)
				<div class="uk-width-1-1 no-print">
					<button style="margin-top:5px" class="uk-button uk-button-success uk-float-right uk-width-1-3@m uk-width-1-1@s" type="button" onclick="dynamicModalLoad('transaction/newFromRecaptureInvoice/{{$invoice->id}}')"><span class="a-credit-card-plus"></span> NEW PAYMENT</button>
				</div>
				@else
				<div class="uk-width-1-1 no-print">

					<button uk-tooltip="@if($invoice->status_id == 1) This invoice is still a draft. Draft invoices cannot have payments recorded against them. @elseif($invoice->status_id == 2) This invoice is pending approval. Invoices that have not been approved cannot have payments recorded against them. @elseif($invoice->status_id == 3) This invoice requires full HFA approval before payments can be entered. @elseif($invoice->status_id == 5) This invoice was declined and cannot have payments recorded against it. @elseif($invoice->status_id == 6) This invoice has been paid in full and cannot have additional payments recorded against it. @endif "
					style="margin-top:5px" class="uk-button uk-button-default gray-button uk-float-right uk-width-1-3@m uk-width-1-1@s" type="button" ><span class="a-info-circle"></span> PAYMENT ENTRY DISABLED</button>
				</div>
				@endif
				@endif
			</div>
		</div>
@endif
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">GENERAL INFORMATION</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid>
				<div class="uk-width-1-1 uk-width-1-2@s">
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
				<div class="uk-width-1-1 uk-width-1-2@s">
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

@if(!$legacy && $invoice->status_id != 1)
		<div class="uk-panel uk-panel-header uk-margin-large-top">
			<h6 class="uk-panel-title">HFA APPROVALS</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid class="approvals">
				<div class="uk-width-1-1">
		            <table class="uk-table uk-overflow-container" id="hfa-approvers-list">
						<thead>
							<th><small>Name</small></th>
							<th><small>Decision</small></th>
							@if( $isApprover || Auth::user()->isHFAAdmin() || Auth::user()->isHFADispositionApprover())
							<th class="no-print">
									<small>Action</small>
							</th>
							@endif
						</thead>
						<tfoot class="no-print">
							<tr>
								<td colspan="3">
									@if( $isApprover )
									<div class="uk-width-8-10 uk-align-center uk-margin-top">
									<div>
										<h4>If an approver is not able to login and click "approve", please print the disposition invoice using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="window.print()">Print Invoice</button>
													</h4>
										<h4>Select approvers who signed the document you are about to upload.</h4>
										<div class="communication-selector">
								            <ul class="uk-subnav document-menu">
								            	@foreach ($approvals as $approval)
					                            <li>
					                                <input name="approvers-hfa-id-checkbox" id="approver-hfa-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox" class="uk-checkbox">
					                                <label for="approver-id-{{ $approval->approver->id }}">
					                                    {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach
					                        </ul>
					                    </div>
									</div>
										<div class="uk-display-inline" id="list-item-upload-box">

					                        <div id="upload-drop-hfa" class="js-upload uk-placeholder uk-text-center">
						                        <span class="a-higher"></span>
						                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
						                        <div uk-form-custom>
						                            <input type="file" >
						                            <span class="uk-link">by browsing and selecting it here.</span>
						                        </div>
						                    </div>

						                    <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

						                    <script>
						                    $(function(){
						                        var bar = document.getElementById('js-progressbar');

						                        settings    = {

						                            url: '{{ URL::route("recapture_invoice.uploadSignature", $invoice) }}',
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
				                                        $("input:checkbox[name=approvers-hfa-id-checkbox]:checked").each(function(){
				                                                approverArray.push($(this).val());
				                                            });
				                                        settings.params.approvers = approverArray;
						                                settings.params._token = '{{ csrf_token() }}';
				                                        approvers = approverArray;
				                                        if(approverArray.length > 0){
				                                            console.log('Approvers selected: '+approverArray);
				                                        }else{
				                                            UIkit.modal.alert('You must select at least one approver.',{
	                                                stack: true});
				                                            return false;
				                                        }
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
					                                        $.post('{{ URL::route("recapture_invoice.uploadSignatureComments", $invoice) }}', {
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
						            </div>
						            	@endif
								</td>
							</tr>
						</tfoot>
						<tbody>
							@php $hfa_approver_count = 0; $user_is_in_approvers_list =0 ; @endphp
							@foreach ($approvals as $approval)
							@php $hfa_approver_count = $hfa_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}">
								<td class="uk-width-2-5">{{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="actionhfa_{{$approval->approver->id}}">
									@if(!count($approval->actions))
									<small class="no-action">No action yet.</small>
									@endif
									@foreach($approval->actions as $action)
									@if($action->action_type->id == 1)
									<div class="uk-badge uk-badge-success">Approved</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 4)
									<div class="uk-badge uk-badge-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
									@elseif($action->action_type->id == 5)
									<div class="uk-badge uk-badge-success">Approved by proxy</div> <small>on {{$action->created_at}}</small><br />
									@endif
									@endforeach
								</td>

								<td class="uk-width-1-5 no-print">
									@if( $isApprover || Auth::user()->isHFAAdmin())
									@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())

										@if((Auth::user()->isHFASimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isHFASimpleApproval())

											@if( Auth::user()->id == $approval->approver->id )
											@php $user_is_in_approvers_list = 1; @endphp

											@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
											<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approve();">Approve</button>
											@endif

											@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
											<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="decline();">Decline</button>
											@endif

											@endif
											@if( $isApprover )
					 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}});" class="uk-button uk-button-small uk-button-warning"><span class="a-trash-4"></span></button>
				 							@endif
				 						@endif
			 						@endif
			 						@endif
			 					</td>

							</tr>
							@endforeach
							@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())

								@if($pending_approvers)
								@foreach($pending_approvers as $pending_approver)
								<tr class="no-print">
									<td class="uk-text-muted">
										{{$pending_approver->name}}
									</td>
									<td></td>
									<td>
										<button class="uk-button uk-button-default uk-button-small button-outline" onclick="addApprover({{$pending_approver->id}});">Add as approver</button>
									</td>
								</tr>
								@endforeach
								@endif
							@endif

						</tbody>
					</table>
		        </div>

			</div>
		</div>
		@if((Auth::user()->isHFADispositionApprover()) || Auth::user()->isHFAAdmin())
			<div class="uk-panel uk-panel-header uk-panel-divider print-only">
				<h6 class="uk-panel-title">SIGNATURES</h6>
			</div>
			<div class="uk-panel uk-margin-top print-only">
				<div uk-grid>
					<div class="uk-width-1-1">
						<p></p>
					</div>
				</div>
				@foreach ($approvals as $approval)
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
		@if($invoice->status_id == 6)
		<div class="uk-panel uk-panel-divider">
			<div uk-grid>
				<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
					<div class="uk-alert">This invoice has been paid.</div>
				</div>
			</div>
		</div>
		@endif
		@if($isDeclined)
		<div class="uk-panel uk-panel-divider">
			<div uk-grid>
				<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top uk-text-center">
					<div class="uk-alert">This invoice is declined, please see notes and/or contact your HFA.</div>
				</div>
			</div>
		</div>
		@endif


	</div>
</div>
@endif

@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isNotifiedOfLienReleaseRequest() || Auth::user()->isHFAAdmin())

@if($invoice->status_id == 1)
<div class="uk-panel uk-panel-divider uk-margin-bottom uk-margin-top uk-margin-left uk-margin-right">
	<div uk-grid>
		<div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-text-center no-print">
			<button class="uk-button uk-button-success uk-width-1-1@s" type="button" onclick="submitForApproval();">Submit This Invoice For HFA Approval</button>
		</div>
	</div>
</div>
@endif

@endif

<script type="text/javascript">
@if(Auth::user()->isHFADispositionApprover() || Auth::user()->isHFAAdmin())

function submitForApproval(){
	UIkit.modal.confirm("Are you sure you want to start the HFA approval process?").then(function() {
	        $.post('{{ URL::route("recapture_invoice.submitForApproval", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			}
		);
	});
}

@if((Auth::user()->isHFAAdmin() || Auth::user()->isHFADispositionApprover()) && $invoice->status->id == 3 && $isApproved == 1)
	function submitInvoice(){
		UIkit.modal.confirm("Are you sure you want to submit the invoice?").then(function() {
	        $.post('{{ URL::route("recapture_invoice.submitInvoice", [$invoice->id]) }}', {
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


	function approve(approval_type = 5){
		UIkit.modal.confirm("Are you sure you want to approve this invoice?").then(function() {
	        $.post('{{ URL::route("recapture_invoice.approve", [$invoice->id]) }}', {
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
	function addApprover(id,approval_type = 5){
		$.post('{{ URL::route("recapture_invoice.addApprover", [$invoice->id]) }}', {
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

	function removeRecapture(id){
		@if($hasApprovals)
		UIkit.modal.alert("If you would like to remove this recapture, please delete all approvals that have been given first.");
		@elseif($legacy == 1)
		UIkit.modal.alert("Because this is a legacy invoice, parcels cannot be removed.");
		@else
		UIkit.modal.confirm("Are you sure you want to remove this recapture from the invoice?").then(function() {
			$.post('{{ URL::route("recapture_invoice.removeRecapture", [$invoice->id]) }}', {
				'disposition' : id,
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
		@endif
	}

	function decline(approval_type = 5){
		UIkit.modal.confirm("Are you sure you want to decline?").then(function() {
	        $.post('{{ URL::route("recapture_invoice.decline", [$invoice->id]) }}', {
				'_token' : '{{ csrf_token() }}',
				'approval_type' : approval_type
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#action_'+data['id']+"_"+data['approval_type']).append('<div class="uk-badge uk-badge-danger">Declined</div> <small></small><br />');
					location.reload();
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function remove(id,approval_type = 5){
		UIkit.modal.confirm("Are you sure you want to remove this approver?").then(function() {
	        $.post('{{ URL::route("recapture_invoice.removeApprover", [$invoice->id]) }}', {
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

	function submitRecaptureInvoiceNote(id) {
		var form = $('#newNoteForm');

		data = form.serialize();
		$.post('{{ URL::route("recapture_invoicenote.create", $invoice->id) }}', {
	            'invoice-note' : form.find( "textarea[name='invoice-note']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {

	            	if(data['id']!='' && data['id']!=null){
	                    // add note to list
	                	newinput = '<div class="uk-width-1-1 note-list-item" id="note-'+data['id']+'">'+
					        '    <div uk-grid>'+
					        '        <div class="uk-width-1-5 uk-width-1-3@m note-type-and-who ">'+
					        '<span uk-tooltip="pos:top-left" title="'+data['name']+'" class="no-print">'+
					        '                <div class="user-badge user-badge-note-item user-badge-'+data['badge_color']+' no-float">'+data['initials']+'</div>'+
					        '            </span>'+
					        '            <span class="print-only">'+data['name']+'<br></span>'+
					        '            <span class=" note-item-date-time">'+data['created_at_formatted']+' </span>'+
					        '        </div>'+
					        '        <div class="uk-width-3-5 uk-width-2-3@m note-item-excerpt">'+
					        '             '+form.find( "textarea[name='invoice-note']" ).val()
					        '        </div>'+
					        '        <div class="uk-width-1-5">'+
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