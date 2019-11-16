@extends('layouts.simplerAllita')
@section('head')
<title>COMPLIANCE REVIEW {{$compliance->id}}</title>
@stop
@section('content')

@if (count($errors) > 0)
    <div class="uk-panel uk-margin-top uk-margin-bottom">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!--
<script src="/js/components/datepicker.js{{ asset_version() }}"></script> -->
<style>
#invoicepanel .uk-panel-box-white {background-color:#ffffff;}
#invoicepanel .uk-panel-box .uk-panel-badge {top:0;}
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
		<form class="uk-form-horizontal" id="complianceform">
			<div class="uk-panel uk-panel-header uk-hidden@m uk-hidden@l" style="text-align:center;">
				<img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px;" />
				<h6 class="uk-panel-title uk-text-center"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />COMPLIANCE REVIEW {{$compliance->id}} | PARCEL {{$compliance->parcel->parcel_id}}</h6>
			</div>
			<div class="uk-panel uk-panel-header uk-visible@m">
				<div class="uk-panel-badge"><img src="/images/ni-program.jpg" alt="NIP Logo" style="height:70px" /></div>
				<h6 class="uk-panel-title uk-text-center uk-text-left-small"><span class="blue uk-text-bold	">OHIO HOUSING FINANCE AGENCY</span><br /><span class="green">NEIGHBORHOOD INITIATIVE PROGRAM</span><br />COMPLIANCE REVIEW {{$compliance->id}} | PARCEL {{$compliance->parcel->parcel_id}}</h6>

			</div>
			<div uk-grid>
		        <div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
		        	<fieldset>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="">Program</label>
	                        <div class="uk-form-controls">
	                            @if($compliance->program)
						    	{{$compliance->program->program_name}}
						    	@else
						    	N/A
						    	@endif
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="">Property</label>
	                        <div class="uk-form-controls">
	                            {{$compliance->parcel->parcel_id}}
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="">Creator</label>
	                        <div class="uk-form-controls">
	                            @if($compliance->creator)
						    	{{$compliance->creator->name}}
						    	@else
						    	N/A
						    	@endif
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="audit_date">Audit Date</label>
	                        <div class="uk-form-controls">
	                            @if($compliance->audit_date === null || $compliance->audit_date == "-0001-11-30 00:00:00" || $compliance->audit_date == "0000-00-00 00:00:00")
							    <input type="text" id="audit_date" name="audit_date" value="" class="uk-input  flatpickr flatpickr-input active" data-id="dateformat"/>
								@else
								<input type="text" id="audit_date" name="audit_date" value="{{$compliance->audit_date_formatted}}" class="uk-input  flatpickr flatpickr-input active" data-id="dateformat"/>
								@endif
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="random_audit">Random Audit?</label>
	                        <div class="uk-form-controls">
	                            <input type="checkbox" value="1" name="random_audit" class="uk-checkbox" id="random_audit" @if($compliance->random_audit) checked @endif> Yes
	                        </div>
	                    </div>
		            </fieldset>
		        </div>
		        <div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
		        	<fieldset>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="">Program</label>
	                        <div class="uk-form-controls">
	                            @if($compliance->program)
						    	{{$compliance->program->program_name}}
						    	@else
						    	N/A
						    	@endif
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="analyst_id">Analyst</label>
	                        <div class="uk-form-controls">
	                        	<select id="analyst_id" name="analyst_id" class="uk-select">
	                        		<option value="">Select an analyst</option>
	                        	@foreach ($hfa_users as $hfa_user)
	                        		@if($compliance->analyst)
	                            		<option value="{{$hfa_user->id}}" @if($hfa_user->id == $compliance->analyst->id) selected @endif>{{$hfa_user->name}}</option>
	                            	@else
							    		<option value="{{$hfa_user->id}}">{{$hfa_user->name}}</option>
							    	@endif
							    @endforeach
							    </select>
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="auditor_id">Auditor</label>
	                        <div class="uk-form-controls">
	                             <select id="auditor_id" name="auditor_id" class="uk-select">
	                        		<option value="">Select an auditor</option>
	                        	@foreach ($hfa_users as $hfa_user)
	                        		@if($compliance->auditor)
	                            		<option value="{{$hfa_user->id}}" @if($hfa_user->id == $compliance->auditor->id) selected @endif>{{$hfa_user->name}}</option>
	                            	@else
							    		<option value="{{$hfa_user->id}}">{{$hfa_user->name}}</option>
							    	@endif
							    @endforeach
							    </select>
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="score">Score</label>
	                        <div class="uk-form-controls">
	                            <select id="score" name="score" class="uk-select">
	                        		<option value="-1" @if($compliance->score === null || $compliance->score == '') selected @endif>In progress</option>
	                        		<option value="Pass" @if($compliance->score == 1 || $compliance->score == "Pass") selected @endif>Pass</option>
	                        		<option value="Fail" @if( ($compliance->score == '0'  || $compliance->score == "Fail") && $compliance->score !== null) selected @endif>Fail</option>
							    </select>
	                        </div>
	                    </div>

		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="if_fail_corrected">If failed, corrected?</label>
	                        <div class="uk-form-controls">
	                            <input type="checkbox" value="1" name="if_fail_corrected" class="uk-checkbox" id="if_fail_corrected" @if($compliance->if_fail_corrected) checked @endif> Yes
	                        </div>
	                    </div>
		        		<div class="uk-form-row">
	                        <label class="uk-form-label" for="items_Reimbursed">Items reimbursed</label>
	                        <div class="uk-form-controls">
	                        	@if($compliance->items_Reimbursed !== NULL)
	                            <textarea id="items_Reimbursed" name="items_Reimbursed" cols="30" rows="3" placeholder="" class="uk-textarea">{!!$compliance->items_Reimbursed!!}</textarea>
	                            @else
						    	<textarea id="items_Reimbursed" name="items_Reimbursed" cols="30" rows="3" placeholder="" class="uk-textarea"></textarea>
						    	@endif
	                        </div>
	                    </div>
		            </fieldset>
				</div>
			</div>
			<hr >
			<div class="uk-grid uk-margin-top">
				<div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
					<h3>Required Items</h3>
					<fieldset>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Funding Limits <span class="uk-float-right"><input type="checkbox" value="1" name="funding_limits_pass" class="uk-checkbox" id="funding_limits_pass" @if($compliance->funding_limits_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Reimbursement limits are not exceeded.</p>
							    	@if($compliance->funding_limits_notes !== NULL)
							    	<textarea id="funding_limits_notes" name="funding_limits_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->funding_limits_notes}}</textarea>
							    	@else
							    	<textarea id="funding_limits_notes" name="funding_limits_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Loan Requirements <span class="uk-float-right"><input type="checkbox" value="1" name="loan_requirements_pass" class="uk-checkbox" id="loan_requirements_pass" @if($compliance->loan_requirements_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Partner is seeking full payoff of the loan (compare reimbursement coversheet to the loan) and loan is from a non-federal source. Loan is dated before the demolition was complete (compare to invoices, photos).</p>
							    	@if($compliance->loan_requirements_notes !== NULL)
							    	<textarea id="loan_requirements_notes" name="loan_requirements_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->loan_requirements_notes}}</textarea>
							    	@else
							    	<textarea id="loan_requirements_notes" name="loan_requirements_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Property <span class="uk-float-right"><input class="uk-checkbox" type="checkbox" value="1" name="property_pass" id="property_pass" @if($compliance->property_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Property is 1-4 units with at least one residential unit.</p>
							    	@if($compliance->property_notes !== NULL)
							    	<textarea id="property_notes" name="property_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->property_notes}}</textarea>
							    	@else
							    	<textarea id="property_notes" name="property_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Right to Demo <span class="uk-float-right"><input type="checkbox" value="1" name="right_to_demo_pass" id="right_to_demo_pass" class="uk-checkbox" @if($compliance->right_to_demo_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Partner is the owner of the Parcel.</p>
							    	@if($compliance->right_to_demo_notes !== NULL)
							    	<textarea id="right_to_demo_notes" name="right_to_demo_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->right_to_demo_notes}}</textarea>
							    	@else
							    	<textarea id="right_to_demo_notes" name="right_to_demo_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Reimbursement Documentation <span class="uk-float-right"><input type="checkbox" value="1" name="reimbursement_doc_pass" class="uk-checkbox" id="reimbursement_doc_pass" @if($compliance->reimbursement_doc_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>There is an invoice and proof of payment for all reimbursed costs with the exception of Maintenance, Administration (unless over $1,000), Loan Payoff, Retainage, and Greening Advances. A signed demolition contract was provided.</p>
							    	@if($compliance->reimbursement_doc_notes !== NULL)
							    	<textarea id="reimbursement_doc_notes" name="reimbursement_doc_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->reimbursement_doc_notes}}</textarea>
							    	@else
							    	<textarea id="reimbursement_doc_notes" name="reimbursement_doc_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Consolidated Certifications <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="consolidated_certs_pass" id="consolidated_certs_pass" @if($compliance->consolidated_certs_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Certifications are present and all boxes are checked.</p>
							    	@if($compliance->consolidated_certs_notes !== NULL)
							    	<textarea id="consolidated_certs_notes" name="consolidated_certs_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->consolidated_certs_notes}}</textarea>
							    	@else
							    	<textarea id="consolidated_certs_notes" name="consolidated_certs_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Note & Mortgage <span class="uk-float-right"><input type="checkbox" value="1" name="note_mortgage_pass" class="uk-checkbox" id="note_mortgage_pass" @if($compliance->note_mortgage_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Partner & Parcel information is correct on the Draft Note and Mortgage. Note and Mortgage were not altered to be less than $25,000. There is a property description.</p>
							    	@if($compliance->note_mortgage_notes !== NULL)
							    	<textarea id="note_mortgage_notes" name="note_mortgage_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->note_mortgage_notes}}</textarea>
							    	@else
							    	<textarea id="note_mortgage_notes" name="note_mortgage_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	SDO <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="sdo_pass" id="sdo_pass" @if($compliance->sdo_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Property was not previously funded under the Save the Dream. Check Fuzzy Look Up Sheet in Monthly Invoicing Folder.</p>
							    	@if($compliance->sdo_notes !== NULL)
							    	<textarea id="sdo_notes" name="sdo_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->sdo_notes}}</textarea>
							    	@else
							    	<textarea id="sdo_notes" name="sdo_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Payment Processing <span class="uk-float-right"><input type="checkbox" value="1" name="payment_processing_pass"  class="uk-checkbox" id="payment_processing_pass" @if($compliance->payment_processing_pass) checked @endif> Pass</span>
							    </dt>
							    <dd><p>The coversheet was signed and dated by OHFA. The invoice correctly lists the amount of reimbursement approved.</p>
							    	@if($compliance->payment_processing_notes !== NULL)
							    	<textarea id="payment_processing_notes" name="payment_processing_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->payment_processing_notes}}</textarea>
							    	@else
							    	<textarea id="payment_processing_notes" name="payment_processing_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                </fieldset>
				</div>

				<div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
					<h3>Information Area</h3>
					<fieldset>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Property <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="property_yes" id="property_yes" @if($compliance->property_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Unit appears vacant and blighted.</p>
							    	@if($compliance->property_notes !== NULL)
							    	<textarea id="property_notes" name="property_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->property_notes}}</textarea>
							    	@else
							    	<textarea id="property_notes" name="property_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Photos <span class="uk-float-right"><input type="checkbox"  class="uk-checkbox"value="1" name="photos_yes" id="photos_yes" @if($compliance->photos_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Photos match those posted on Google Street view. Photos match asbestos survey.</p>
							    	@if($compliance->photos_notes !== NULL)
							    	<textarea id="photos_notes" name="photos_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->photos_notes}}</textarea>
							    	@else
							    	<textarea id="photos_notes" name="photos_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Checklist <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="checklist_yes" id="checklist_yes" @if($compliance->checklist_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>All items on associate checklist are marked as complete.</p>
							    	@if($compliance->checklist_notes !== NULL)
							    	<textarea id="checklist_notes" name="checklist_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->checklist_notes}}</textarea>
							    	@else
							    	<textarea id="checklist_notes" name="checklist_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Ineligible Costs <span class="uk-float-right"><input type="checkbox"  class="uk-checkbox" value="1" name="inelligible_costs_yes" id="inelligible_costs_yes" @if($compliance->inelligible_costs_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Parner did not seek Marketing or Non-tax-foreclosure Litigation Costs.</p>
							    	@if($compliance->inelligible_costs_notes !== NULL)
							    	<textarea id="inelligible_costs_notes" name="inelligible_costs_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->inelligible_costs_notes}}</textarea>
							    	@else
							    	<textarea id="inelligible_costs_notes" name="inelligible_costs_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Target Area <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="target_area_yes" id="target_area_yes" @if($compliance->target_area_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Parcel is in Partner's defined target area. You must check the approved target areas and compare to a map of the property address.</p>
							    	@if($compliance->target_area_notes !== NULL)
							    	<textarea id="target_area_notes" name="target_area_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->target_area_notes}}</textarea>
							    	@else
							    	<textarea id="target_area_notes" name="target_area_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Environmental <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="environmental_yes" id="environmental_yes" @if($compliance->environmental_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Asbestos survey and EPA Notification are present, EPA Notice Sec. 18 is signed by the Owner. Sec. 17 is signed if asbestos is present.</p>
							    	@if($compliance->environmental_notes !== NULL)
							    	<textarea id="environmental_notes" name="environmental_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->environmental_notes}}</textarea>
							    	@else
							    	<textarea id="environmental_notes" name="environmental_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Contractors <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="contractors_yes" id="contractors_yes" @if($compliance->contractors_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Associate screened the demolition and other subcontractors to ensure they are not on the APLS List. All contractors have an entry on the Subcontractor EPLS Search Record list.</p>
							    	@if($compliance->contractors_notes !== NULL)
							    	<textarea id="contractors_notes" name="contractors_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->contractors_notes}}</textarea>
							    	@else
							    	<textarea id="contractors_notes" name="contractors_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                    <div class="uk-form-row">
	                        <dl class="uk-description-list-line">
							    <dt>
							    	Allita <span class="uk-float-right"><input type="checkbox" class="uk-checkbox" value="1" name="salesforce_yes" id="salesforce_yes" @if($compliance->salesforce_yes) checked @endif> Pass</span>
							    </dt>
							    <dd><p>Address and Reimbursement amounts were correctly entered. Compare Allita to Deed and Per Property Reimbursement cover sheet.</p>
							    	@if($compliance->salesforce_notes !== NULL)
							    	<textarea id="salesforce_notes" name="salesforce_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea">{{$compliance->salesforce_notes}}</textarea>
							    	@else
							    	<textarea id="salesforce_notes" name="salesforce_notes" style="width:100%" rows="3" placeholder="" class="uk-textarea"></textarea>
							    	@endif
							    </dd>
							</dl>
	                    </div>
	                </fieldset>
				</div>
			</div>
		</form>

		<div class="uk-width-1-1">
			<div class="uk-grid">
				<div class="uk-width-1-1">
					<div id="applicant-info-update">
						<div class="uk-grid uk-margin">
							<div class="uk-width-1-6@m uk-width-1-1@s uk-push-4-6 uk-margin-top">
								<a class="uk-button uk-button-primary uk-width-1-1" onclick="dynamicModalClose();"> <span uk-icon="times-circle" class="uk-margin-left"></span> CANCEL</a>
							</div>
							<div class="uk-width-1-6@m uk-width-1-1@s uk-push-4-6 uk-margin-top">
								<a class="uk-button uk-button-primary uk-width-1-1" onclick="saveCompliance()"> SAVE &nbsp;</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function saveCompliance() {
		var form = $('#complianceform');

		$.post('/compliance/{{ $compliance->parcel->id }}/{{ $compliance->id }}/edit', {
			'inputs' : form.serialize(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!='1'){
				UIkit.modal.alert(data);
			} else {
				UIkit.modal.alert('This compliance review has been saved.');
				//$('#compliance-tab-content').load('/compliance/{{$compliance->parcel->id}}');
			}
		} );
		//$('#compliance-tab-content').load('/compliance/{{$compliance->parcel->id}}');
		//dynamicModalClose();

	}
</script>
	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");

		var configs = {
		    dateformat: {
		        dateFormat: "m/d/Y",
		    }
		}
	</script>
@stop
