@extends('layouts.simplerAllita')

@section('head')
<title>REQ: {{$request->id}} </title>
@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script> -->
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
			<h6 class="uk-panel-title uk-text-center"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />REIMBURSEMENT REQUEST {{$request->id}} </h6>
		</div>
		<div class="uk-panel uk-panel-header uk-visible@m">
			<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
			<h6 class="uk-panel-title uk-text-center uk-text-left-small"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />REIMBURSEMENT REQUEST {{$request->id}}</h6>

		</div>

		<div class="uk-panel no-padding-bottom">
			<div uk-grid class="uk-grid-collapse">
				<div class="uk-margin-top uk-width-1-2@m uk-width-3-4@l uk-width-1-1@s">
					{{$request->entity->entity_name}}
					@if($request->legacy == 1)
					<div class=" uk-label uk-label-warning">Legacy</div>
					@endif
					@if($request->status != null)
					<div class=" uk-label">{{$request->status->invoice_status_name}}</div>
					@endif
				</div>
			</div>
		</div>

		<div class="uk-panel uk-panel-divider">

			<div uk-grid >
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
				        	@foreach ($request->parcels as $parcel)
				            <tr>
				                <td><a onclick="window.open('/viewparcel/{{$parcel->id}}', '_blank')" class="uk-link-muted" uk-tooltip="OPEN parcel">{{$parcel->parcel_id}}</a></td>
				                <td class="uk-text-right">{{$parcel->requested_total_formatted}}</td>
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
					<div uk-grid>
						<div class="uk-width-1-1 uk-margin-top uk-margin-bottom">
							<div class="uk-panel uk-panel-box">
							    <div class="uk-panel-badge uk-badge">
							    	{{$stat['Total_Parcels'] ?? 'n/a'}}
							    </div>
							    <h3 class="uk-panel-title">
							    	{{$request->program->program_name ?? 'n/a'}}
							    </h3>
								<hr class="dashed-hr" class="uk-margin-bottom">

								<!-- This is the container of the content items -->
								<ul class="uk-list">
								    <li>
								    	Account ID: {{$request->account->id ?? 'n/a'}}
								    	<ul>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-checkbox-plus"></span> Deposits:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Deposits_Made'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-checkbox-plus"></span> Recaptures Received:
								    				<span class="parcel-district-format-number">
								    					{{money_format('%(8n', $stat['Recaptures_Received'])}}
								    				</span>
								    			</span>
								    		</li>
								    		<li>
								    			<span class="parcel-district-format">
								    				<span class="a-checkbox-plus"></span> Dispositions Received:
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
						<div id="request_notes">
							@foreach ($request->notes as $note)
					        <div class="uk-width-1-1 note-list-item" id="note-{{ $note->id}}" >
					            <div uk-grid>
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
				        <form name="newNoteForm" id="newNoteForm" method="post" class="no-print">
				        <div class="uk-width-1-1">
						    <textarea id="request-note" rows="8" name="request-note" placeholder="Enter note here."></textarea>
						</div>
						<div class="uk-width-1-1" uk-grid>
							<div class="uk-width-1-3@m uk-width-1-1@s"><a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitRequestNote()"><span uk-icon="save"></span> SAVE</a></div>

							<div class="uk-width-1-3"></div>
							<div class="uk-width-1-3"></div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">GENERAL INFORMATION</h6>
		</div>

		<div class="uk-panel">
			<div uk-grid class="uk-margin-top">
				<div class="uk-width-1-2@s">
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
				<div class="uk-width-1-2@s">
					<span class="uk-text-bold uk-text-small">Name and Address of NIP Partner</span><br />
					{{$request->entity->entity_name}}<br />
					{{$request->entity->address1}}<br />
					@if($request->entity->address2) {{$request->entity->address2}}<br /> @endif
					{{$request->entity->city}}, {{$request->entity->state->state_acronym}} {{$request->entity->zip}}<br />
					{{$request->entity->phone}}<br />
					{{$request->entity->user->email}}<br /><br />

					<span class="uk-text-bold uk-text-small">FTI Number</span><br />
					@if(!$request->entity->fti) N/A @endif {{$request->entity->fti}}<br />
				</div>
			</div>
		</div>

@if($request->legacy != 1)
		<div class="uk-panel uk-panel-header uk-panel-divider">
			<h6 class="uk-panel-title">APPROVALS</h6>
		</div>

		@if(!$hasApprovals)
		<div class="uk-panel">
			<div uk-grid class="approvals">
				@if($request->legacy == 1)
				<p>This is a legacy request.</p>
				@else
				<p>This request is pending approval.</p>
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
							@if( $isApprover || Auth::user()->isHFAAdmin())
							<th class="no-print"><small>Action</small></th>
							@endif
						</thead>
						<tfoot  class="no-print">
							<tr>
								<td></td>
								<td colspan="2">
									@if( $isApprover )
									<div>
										<h4>If an approver is not able to login and click "approve", please print the request using the print button below, and they can physically sign the printed copy of the document on their respective signature line.<br /><button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="window.print()">Print Request</button>
													</h4>
										<h4>Select approvers who signed the document you are about to upload.</h4>
										<div class="communication-selector">
								            <ul class="uk-subnav document-menu">
								            	@foreach ($approvals as $approval)
					                            <li>
					                                <input name="approvers-id-checkbox" class="uk-checkbox" id="approver-id-{{ $approval->approver->id }}" value="{{ $approval->approver->id }}" type="checkbox">
					                                <label for="approver-id-{{ $approval->approver->id }}">
					                                    @if($approval->approver->entity_id == 1) [HFA] @endif {{$approval->approver->name}}
					                                </label>
					                            </li>
					                            @endforeach
					                        </ul>
					                    </div>
									</div>
										<div class="uk-display-inline" id="list-item-upload-box">
						                    <div class="js-upload uk-placeholder uk-text-center">
						                        <span class="a-higher"></span>
						                        <span class="uk-text-middle"> Please upload your document by dropping it here or</span>
						                        <div uk-form-custom>
						                            <input type="file" multiple>
						                            <span class="uk-link">by browsing and selecting it here.</span>
						                        </div>
						                    </div>

						                    <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

						                    <script>
						                    $(function(){
						                        var bar = document.getElementById('js-progressbar');

						                        settings    = {

						                            url: '{{ URL::route("approval.uploadSignature", $request->id) }}',
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
				                                        $("input:checkbox[name=approvers-id-checkbox]:checked").each(function(){
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
					                                        $.post('{{ URL::route("approval.uploadSignatureComments", $request->id) }}', {
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
							@foreach ($approvals as $approval)
							@php $lb_approver_count = $lb_approver_count + 1; @endphp
							<tr id="ap_{{$approval->approver->id}}">
								<td class="uk-width-2-5">@if($approval->approver->entity_id == 1) [HFA] @endif {{$approval->approver->name}}</td>
								<td class="uk-width-2-5" id="action_{{$approval->approver->id}}">
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
								@if( $isApprover || Auth::user()->isHFAAdmin())
								<td class="uk-width-1-5 no-print">
									@if(Auth::user()->isLandbankRequestApprover() || Auth::user()->isHFAAdmin())
										@if((Auth::user()->isLandBankSimpleApproval() && $approval->approver->id == Auth::user()->id) || !Auth::user()->isLandBankSimpleApproval())
											@if( Auth::user()->id == $approval->approver->id )
											@php $user_is_in_approvers_list = 1; @endphp

											@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id == 4))
											<button style="margin-top:5px" class="uk-button uk-button-small uk-button-success" onclick="approveRequest();">Approve</button>
											@endif

											@if(!$approval->last_action() || ($approval->last_action() && $approval->last_action()->approval_action_type_id != 4))
											<button style="margin-top:5px" class="uk-button uk-button-small uk-button-warning" onclick="declineRequest();">Decline</button>
											@endif
											@endif
											@if( $isApprover )
					 						<button style="margin-top:5px" onclick="remove({{$approval->approver->id}});" class="uk-button uk-button-small uk-button-warning"><span uk-icon="trash"></span></button>
				 							@endif
				 						@endif
			 						@endif
			 					</td>
			 					@endif
							</tr>
							@endforeach
							@foreach($pending_approvers as $pending_approver)
							<tr class="no-print">
								<td class="uk-text-muted">
									{{$pending_approver->name}}
								</td>
								<td></td>
								<td>
									<button class="uk-button uk-button-small uk-button-success" onclick="addLBApprover({{$pending_approver->id}});">Add as approver</button>
								</td>
							</tr>
							@endforeach
						@if(Auth::user()->isHFAAdmin() && $user_is_in_approvers_list == 0)
							<tr class="no-print">
								<td class="uk-text-muted">
									[HFA] {{Auth::user()->name}}
								</td>
								<td></td>
								<td>
									<button class="uk-button uk-button-small uk-button-success" onclick="addHFAApprover();">Add me as approver</button>
								</td>
							</tr>
						@endif
						</tbody>
					</table>
		        </div>
		        @if($isApproved && $request->status->id == 1)
		        <div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top no-print">
		        	<button style="margin-top:5px" class="uk-button uk-button-success" onclick="submitRequest();">PROCEED WITH THE REQUEST SUBMISSION TO HFA</button>
		        </div>
		        @elseif($submitted_on_formatted !== null)
		        <div class="uk-panel uk-width-1-2@m uk-width-1-1@s uk-container-center uk-margin-top no-print uk-text-center">
		        	<div class="uk-alert">This request was submitted to HFA on {{$submitted_on_formatted}}.</div>
		        </div>
		        @endif
			</div>
		</div>

@if((Auth::user()->isLandbankRequestApprover()) || Auth::user()->isHFAAdmin())
		<div class="uk-panel uk-panel-header uk-panel-divider print-only">
			<h6 class="uk-panel-title">SIGNATURES</h6>
		</div>
		<div class="uk-panel uk-margin-top print-only">
			<div uk-grid>
				<div class="uk-width-1-1">
					<p>I certify that this Request for Payment was drawn in accordance with the terms and conditions of the Funding Agreement(s) cited and that the amount drawn is proper for payment to the drawer's depository.  I also certify that the data reported above is correct and that the amount of the Request for Payment is not in excess of current needs.  I further certify that the statements made in the Consolidated Certifications form are true and accurate and based upon reasonable and relable information.  </p>
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
@endif
	</div>
</div>
<div class="uk-width-1-1" hidden>
	@if(Auth::user()->isLandbankAdmin()) isLandbankAdmin<br /> @endif
	@if(Auth::user()->isLandbankParcelApprover()) isLandbankParcelApprover<br /> @endif
  	@if(Auth::user()->isLandbankRequestApprover()) isLandbankRequestApprover<br /> @endif
  	@if(Auth::user()->isLandbankInvoiceApprover()) isLandbankInvoiceApprover<br /> @endif
  	@if(Auth::user()->isLandbankMemberApprover()) isLandbankMemberApprover<br /> @endif
  	@if(Auth::user()->isLandbankDispositionReviewer()) isLandbankDispositionReviewer<br /> @endif
  	@if(Auth::user()->isLandbankDispositionApprover()) isLandbankDispositionApprover<br /> @endif
  	@if(Auth::user()->isLandbankDispositionManager()) isLandbankDispositionManager<br /> @endif

  	@if(Auth::user()->isHFAAdmin()) isHFAAdmin<br /> @endif
  	@if(Auth::user()->isHFAPOApprover()) isHFAPOApprover<br /> @endif
  	@if(Auth::user()->isHFAComplianceAuditor()) isHFAComplianceAuditor<br /> @endif
  	@if(Auth::user()->isHFAPrimaryInvoiceApprover()) isHFAPrimaryInvoiceApprover<br /> @endif
  	@if(Auth::user()->isHFASecondaryInvoiceApprover()) isHFASecondaryInvoiceApprover<br /> @endif
  	@if(Auth::user()->isHFATertiaryInvoiceApprover()) isHFATertiaryInvoiceApprover<br /> @endif
  	@if(Auth::user()->isHFAFiscalAgent()) isHFAFiscalAgent<br /> @endif
	@if(Auth::user()->isHFALandbankApprover()) isHFALandbankApprover<br /> @endif
	@if(Auth::user()->isHFADispositionApprover()) isHFADispositionApprover<br /> @endif
	@if(Auth::user()->isHFALienManager()) isHFALienManager<br /> @endif
	@if(Auth::user()->isHFADispositionReviewer()) isHFADispositionReviewer<br /> @endif

	@if(Gate::check('hfa-review-disposition'))
0
	@endif

		<br />User id: {{Auth::user()->id}}<br />
		@can('create-disposition')
1
		@endIf
		@can('authorize-disposition-request')
2
		@endIf
		@can('submit-disposition')
3
		@endIf
		@can('hfa-review-disposition')
4
		@endIf
		@can('hfa-sign-disposition')
5
		@endIf
		@can('hfa-release-disposition')
6
		@endIf
	</div>
<script type="text/javascript">

@if(Auth::user()->isHFAAdmin())
	function addHFAApprover(){
		$.post('{{ URL::route("request.addHFAApprover", [$request->id]) }}', {
			'_token' : '{{ csrf_token() }}'
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
		$.post('{{ URL::route("request.addLBApprover", [$request->id]) }}', {
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

	function declineRequest(){
		UIkit.modal.confirm("Are you sure you want to decline?").then(function() {
	        $.post('{{ URL::route("request.decline", [$request->id]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);
					$(".no-action").remove();
					$('#action_'+data['id']).append('<div class="uk-badge uk-badge-danger">Declined</div> <small></small><br />');
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
	    });
	}

	function approveRequest(){
		UIkit.modal.confirm("I certify that this Request for Payment was drawn in accordance with the terms and conditions of the Funding Agreement(s) cited and that the amount drawn is proper for payment to the drawer's depository.  I also certify that the data reported above is correct and that the amount of the Request for Payment is not in excess of current needs.  I further certify that the statements made in the Consolidated Certifications form are true and accurate and based upon reasonable and relable information.").then(function() {
	        $.post('{{ URL::route("request.approve", [$request->id]) }}', {
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

	function submitRequest(){
        $.post('{{ URL::route("request.submit", [$request->id]) }}', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data['message']!='' && data['error']!=1){
				UIkit.modal.alert(data['message']);
				location.reload();
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
			}else{
				UIkit.modal.alert('Something went wrong.');
			}
		} );
	}

	function submitRequestNote() {
		var form = $('#newNoteForm');

		data = form.serialize();
		$.post('{{ URL::route("requestnote.create", $request->id) }}', {
	            'request-note' : form.find( "textarea[name='request-note']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {

	            	if(data['id']!='' && data['id']!=null){
	                    // add note to list
	                	newinput = '<div class="uk-width-1-1 note-list-item" id="note-'+data['id']+'">'+
					        '    <div class="uk-grid">'+
					        '        <div class="uk-width-1-6 uk-width-1-3@m note-type-and-who ">'+
					        '<span uk-tooltip="pos:top-left;title:'+data['name']+'" class="no-print">'+
					        '                <div class="user-badge user-badge-note-item user-badge-'+data['badge_color']+' no-float">'+data['initials']+'</div>'+
					        '            </span>'+
					        '            <span class="print-only">'+data['name']+'<br></span>'+
					        '            <span class=" note-item-date-time">'+data['created_at_formatted']+' </span>'+
					        '        </div>'+
					        '        <div class="uk-width-2-3 uk-width-1-2@m note-item-excerpt">'+
					        '             '+form.find( "textarea[name='request-note']" ).val()
					        '        </div>'+
					        '        <div class="uk-width-1-6">'+
					        '        </div>'+
					        '    </div>'+
					        '</div>';
					  	$("#request_notes").append(newinput);
					  	form.find( "textarea[name='request-note']" ).val('');
				        UIkit.modal.alert('Your note has been saved.');
	                } else {
	                	UIkit.modal.alert(data);
	                }




		} );

	}

	function remove(id){
		UIkit.modal.confirm("Are you sure you want to remove this approver?").then(function() {
	        $.post('{{ URL::route("request.removeApprover", [$request]) }}', {
				'id' : id,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!=''){
					UIkit.modal.alert(data['message']);

				}else{
					$("#ap_"+data['id']).remove();
					location.reload();
				}
			} );
	    });
	}
</script>

@stop