@extends('layouts.simplerAllita')

@section('content')

<div id="disposition-form" class="uk-width-1-1">
		
	<style>
.uk-breadcrumb>li:nth-child(n+2):before {
    content: ">";
    display: inline-block;
    margin: 0 8px;
}
.uk-breadcrumb button{cursor: initial;}
.pickListButtons {
  padding: 10px;
  text-align: center;
}

.pickListButtons button {
  margin-bottom: 5px;
}

.pickListSelect {
  height: 100px !important;
  width: 100%;
}
#releaseformpanel .uk-panel-box-white {background-color:#ffffff;}
#releaseformpanel .uk-panel-box .uk-panel-badge {top:0;}
#releaseformpanel .green {color:#82a53d;}
#releaseformpanel .blue {color:#005186;}
#releaseformpanel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#releaseformpanel table tfoot tr td {border: none;}
#releaseformpanel textarea {width:100%;}
#releaseformpanel .note-list-item:last-child { border: none;}
#releaseformpanel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#releaseformpanel .property-summary {margin-top:0;}
</style>
	<div id="releaseformpanel">
			<div class="uk-panel uk-panel-box uk-panel-box-white">
				<div class="uk-panel uk-panel-header">
					<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
					<h6 class="uk-panel-title uk-text-center uk-text-left-small" style="padding-bottom: 15px;"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">EARLY LIEN RELEASE REQUEST FORM</span><br /></h6>
					
				</div>

				<div class="uk-panel no-padding-bottom">
					<div class="uk-grid">
						<div class="uk-width-1-1 uk-width-4-4@l">
							<p>Instructions: NIP Partners may complete this form to release the HHF lien prior to the three-year term in accordance with the NIP guidelines. Partners must attach any supporting documents identified in Section 8(D) of the Guidelines. The decision of whether to grant a release is within OHFA’s discretion. You may wish to contact the NIP Program Manager before completing this form for guidance on eligible dispositions and required attachments.</p>
						</div>
					</div>
				</div>

				<div class="uk-panel uk-panel-divider">
					<div class="uk-grid">
						@if($current_user->isFromEntity(1))
						<div class="uk-width-2-5@m uk-width-1-1@s">
						@else
						<div class="uk-width-1-1@m uk-width-1-1@s">
						@endif
							<dl class="uk-description-list-horizontal">
	                            <dt>Partner</dt>
	                            <dd>{{$entity->entity_name}}</dd>
	                            <dt>Full Property Address</dt>
	                            <dd>@if($parcel->street_address) {{$parcel->street_address}} <br />@endif
										{{$parcel->city}} @if($parcel->state->state_acronym) {{$parcel->state->state_acronym}}@endif @if($parcel->zip) {{$parcel->zip}} @endif</dd>
	                        </dl>
	                        <dl class="uk-description-list-horizontal uk-form">
	                            <dt>Program Income</dt>
	                            <dd>{{$calculation['income']}}</dd>
	                            <dt >Imputed Cost Per Parcel</dt>
	                            <dd>{{$calculation['transaction_cost']}}</dd>
	                            <dt >Permanent Parcel #</dt>
	                            <dd> @if($disposition)@if($disposition->permanent_parcel_id != '') {{$disposition->permanent_parcel_id}} @else {{$parcel->parcel_id}} @endif @endif </dd>
	                        </dl>
	                        
							<hr class="dashed-hr"/>
							<div class="uk-grid uk-form uk-margin-top">
								<div class="uk-width-1-1@m uk-width-1-1@s">
									<p>Please Paste in the Text of the Parcel's Legal Description. </p>
										<p>Parcel Legal Description Included in Supporting Documents <input type="checkbox" name="legal_description_in_documents" value="1" id="legal_description_in_documents"  class="uk-checkbox" @if($disposition) @if($disposition->legal_description_in_documents) checked @endif @endif disabled></p>	
										<div class="uk-form-controls">
											@if($disposition) 
											@if($disposition->legal_description_in_documents == 0) 

											@if($disposition) {{$disposition->full_description}} @endif

											@endif 
											@endif
			                            </div>
								</div>
							</div>
						</div>
						@if($current_user->isFromEntity(1))
						<div class="uk-width-3-5@m uk-width-1-1@s">
							<div class="uk-panel uk-panel-box">
								<h3 class="uk-panel-title">Calculations</h3>
								@if($disposition)
								<table class="uk-table">
									<thead>
										<th>Line Item</th>
										<th class="uk-text-right">Estimated</th>
										<th class="uk-text-right">Ajustments</th>
										<th class="uk-text-right">Actual</th>
									</thead>
									<tbody>
										<tr>
											<td>Income (income)</td>
											<td class="uk-text-right">{{$calculation['income_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_income}}
											</td>
											<td class="uk-text-right">@if($disposition->hfa_calc_income){{$actual['income_formatted']}}@else{{$calculation['income_formatted']}}@endif</td>
										</tr>
										<tr>
											<td><span uk-tooltip="Minimum {{$calculation['rule_min_cost_formatted']}}">Imputed Cost Per Parcel (cost)</span></td>
											<td class="uk-text-right">{{$calculation['transaction_cost_formatted']}}</td>
											<td class="uk-text-right">
												
												{{$disposition->hfa_calc_trans_cost}}
											</td>
											<td class="uk-text-right">@if($disposition->hfa_calc_trans_cost){{$actual['transaction_cost_formatted']}}@else{{$calculation['transaction_cost_formatted']}}@endif</td>
										</tr>
										<tr>
											<td><span uk-tooltip="Total maintenance advance">Maintenance Total</span></td>
											<td class="uk-text-right">{{$calculation['maintenance_total_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_maintenance_total}}
											</td>
											<td class="uk-text-right">@if($disposition->hfa_calc_maintenance_total){{$actual['maintenance_total_formatted']}}@else{{$calculation['maintenance_total_formatted']}}@endif</td>
										</tr>
										<tr>
											<td>
												<span uk-tooltip="Monthly maintenance rate">Monthly Maintenance Rate (rate)</span><br />
												<small>rate = total maintenance / 36</small>
											</td>
											<td class="uk-text-right">{{$calculation['monthly_maintenance_rate']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_monthly_rate}}
											</td>
											<td class="uk-text-right">{{$actual['monthly_maintenance_rate']}}</td>
										</tr>
										<tr>
											<td><span uk-tooltip="Using disposition date minus invoice payment clear date">Months Maintained (months)</span></td>
											<td class="uk-text-right">
												@if($calculation['month_unused']){{$calculation['month_unused']}} @else <span uk-icon="warning"></span> Missing payment date
												@endif
											</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_months}}
											</td>
											<td class="uk-text-right">@if($disposition->hfa_calc_months){{$actual['month_unused']}}@else{{$calculation['month_unused']}}@endif</td>
										</tr>
										<tr>
											<td>
												<span uk-tooltip="Unused maintenance to be repaid">Maintenance To Repay (maint)</span><br />
												<small>maint = total maintenance - (months x rate)</small>
											</td>
											<td class="uk-text-right">{{$calculation['maintenance_unused_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_maintenance_due}}
											</td>
											<td class="uk-text-right">{{$actual['maintenance_unused_formatted']}}</td>
										</tr>
										<tr>
											<td><span uk-tooltip="Total demolition cost (does not include maintenance)">Demolition Reimbursement (demo)</span></td>
											<td class="uk-text-right">{{$calculation['demolition_cost_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_demo_cost}}
											</td>
											<td class="uk-text-right">{{$actual['demolition_cost_formatted']}}</td>
										</tr>
										<tr>
											<td>
												<span uk-tooltip="">Eligible Property Income (epi)</span><br />
												<small>epi = income - cost</small>
											</td>
											<td class="uk-text-right">{{$calculation['eligible_income_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_epi}}
											</td>
											<td class="uk-text-right">{{$actual['eligible_income_formatted']}}</td>
										</tr>
										<tr>
											<td>
												<span uk-tooltip="">Total recapture owed to the HFA (payback)</span><br />
												<small>When epi > demo, payback = demo + maint.<br />Otherwise payback = epi + maint.</small>
											</td>
											<td class="uk-text-right">{{$calculation['payback_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_payback}}
											</td>
											<td class="uk-text-right">{{$actual['payback_formatted']}}</td>
										</tr>
										<tr>
											<td>
												Total capital Gain for the landbank<br />
												<small>gain = epi - demo - maint</small>
											</td>
											<td class="uk-text-right">{{$calculation['gain_formatted']}}</td>
											<td class="uk-text-right">
												{{$disposition->hfa_calc_gain}}
											</td>
											<td class="uk-text-right">{{$actual['gain_formatted']}}</td>
										</tr>
									</tbody>
								</table>
								@endif
							</div>
						</div>
						@endif
					</div>
				</div>

				<div class="uk-panel uk-panel-divider">
					<div class="uk-grid uk-form">
						<div class="uk-width-2-5@m uk-width-1-1@s">
							<p>Special Circumstance Justifying Lien Release:</p>
							<div class="uk-form-controls">
								@foreach($types as $type)
                                <input type="radio" id="" class="uk-radio" name="special" value="{{$type->id}}" @if($disposition) @if($disposition->disposition_type_id == $type->id) checked="checked" @endif @endif disabled> <label>{{$type->disposition_type_name}}</label><br>
                                @endforeach
                            </div>
						</div>
						<div class="uk-width-3-5@m uk-width-1-1@s">
							<p>Description of Proposed New Use and “Special Circumstance”:</p>
							<p>Included in Supporting Documents <input type="checkbox" class="uk-checkbox" name="description_use_in_documents" value="1" id="description_use_in_documents" @if($disposition) @if($disposition->description_use_in_documents) checked @endif @endif disabled></p>	
									
							@if($disposition) 
							@if($disposition->description_use_in_documents == 0) 

							@if($disposition) {{$disposition->special_circumstance}} @endif

							@endif 
							@endif
						</div>
					</div>
				</div>

				@php
				$has_LB_approvers = 0;
				$has_HFA_approvers = 0;
				foreach ($approvals as $approval){
					if($approval->approver->entity_id == 1) $has_HFA_approvers = 1;
					if($approval->approver->entity_id != 1) $has_LB_approvers = 1;
				}
				@endphp

				@if($has_LB_approvers)
				<div class="uk-panel uk-panel-header uk-panel-divider">
					<h6 class="uk-panel-title">LANDBANK SIGNATURES</h6>
				</div>
				<div class="uk-panel">
					<div class="uk-grid uk-margin-small-top">
						<div class="uk-width-1-1 ">
							<table class="uk-table" id="lb-approvers-list">
									<tbody>
										@php $lb_approver_count = 0; @endphp
										@foreach ($approvals as $approval)
										@if($approval->approver->entity_id != 1)
										@php $lb_approver_count = $lb_approver_count + 1; @endphp
										<tr id="ap_{{$approval->approver->id}}">
											<td class="uk-width-3-5">{{$approval->approver->name}}</td>
											<td class="uk-width-1-5"  id="action_{{$approval->approver->id}}">
												@if(!count($approval->actions))
												<small class="no-action">No action yet.</small>
												@endif
												@foreach($approval->actions as $action)
												@if($action->action_type->id == 1 || $action->action_type->id == 5)
												<div class="uk-badge uk-badge-success">Approved</div> <small>on {{$action->created_at}}</small><br />
												@elseif($action->action_type->id == 4)
												<div class="uk-badge uk-badge-danger">Declined</div> <small>on {{$action->created_at}}</small><br />
												@endif
												@endforeach
											</td>
										</tr>
										@endif
										@endforeach
									</tbody>
								</table>	
							@foreach ($approvals as $approval)
							@if($approval->approver->entity_id != 1)
							<div class="uk-panel uk-panel-header">
								<div class="uk-width-1-1 uk-margin-top ">
									<br /><br /><br />
									<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
										<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
										</p>
									</div>
								</div>			
							</div>
							@endif
							@endforeach	
						</div>
					</div>
				</div>
				@endif
				@if($has_HFA_approvers)
				<div class="uk-panel uk-panel-header uk-margin-large-top">
					<h6 class="uk-panel-title">HFA APPROVALS</h6>
				</div>
				<div class="uk-panel">
					<div class="uk-grid uk-margin-small-top">
						<div class="uk-width-1-1 ">
							@foreach ($approvals as $approval)
							@if($approval->approver->entity_id == 1)
							<div class="uk-panel uk-panel-header">
								<div class="uk-width-1-1 uk-margin-top ">
									<br /><br /><br />
									<div style="border-top:1px solid #333;padding-top:10px;" class=" uk-width-1-1">
										<p>Name: {{$approval->approver->name}} <span style="float:right;margin-right:20%;">Date:</span>
										</p>
									</div>
								</div>			
							</div>
							@endif
							@endforeach	
							
						</div>
					</div>
				</div>
				@endif

			</div>
	</div>

</div>

@stop