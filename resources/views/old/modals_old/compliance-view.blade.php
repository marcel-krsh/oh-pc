<script>
resizeModal(95);
</script>

<div class="uk-container">
	<div class="uk-grid">
		<div class="uk-width-1-1">
            <h2>Compliance Review {{$compliance->id}} | Parcel {{$compliance->parcel->parcel_id}}</h2>
        </div>
        <div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
        	<dl class="uk-description-list-horizontal">
			    <dt>Program</dt>
			    <dd>
			    	@if($compliance->program)
			    	{{$compliance->program->program_name}}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Property</dt>
			    <dd>{{$compliance->parcel->parcel_id}}</dd>
			    <dt>Creator</dt>
			    <dd>
			    	@if($compliance->creator)
			    	{{$compliance->creator->name}}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Audit Date</dt>
			    <dd>
			    	@if($compliance->audit_date_formatted !== NULL  && $compliance->audit_date != "-0001-11-30 00:00:00" && $compliance->audit_date != "0000-00-00 00:00:00")
			    	{!!$compliance->audit_date_formatted!!}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Random Audit</dt>
			    <dd>
			    	@if($compliance->random_audit)
			    	Yes
			    	@else
			    	No
			    	@endif
			    </dd>
			    
			</dl>
        </div>
        <div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
        	<dl class="uk-description-list-horizontal">
			    
			    <dt>Analyst</dt>
			    <dd>
			    	@if($compliance->analyst)
			    	{{$compliance->analyst->name}}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Auditor</dt>
			    <dd>
			    	@if($compliance->auditor)
			    	{{$compliance->auditor->name}}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Score</dt>
			    <dd>
			    	@if($compliance->score===NULL)
			    	In Progress
			    	@elseif($compliance->score == "Fail" || $compliance->score == "0")
			    	<div class="uk-badge uk-badge-warning">FAIL</div>
			    	@else
			    	<div class="uk-badge uk-badge-success">PASS</div>
			    	@endif
			    </dd>
			    <dt>If failed, corrected?</dt>
			    <dd>
			    	@if($compliance->if_fail_corrected && ($compliance->score == 0 || $compliance->score == "Fail"))
			    	Yes
			    	@elseif(!$compliance->if_fail_corrected && ($compliance->score == 0 || $compliance->score == "Fail") && $compliance->score !== NULL)
			    	No
			    	@else
			    	N/A
			    	@endif
			    </dd>
			    <dt>Items reimbursed</dt>
			    <dd>
			    	@if($compliance->items_Reimbursed !== NULL)
			    	{!!$compliance->items_Reimbursed!!}
			    	@else
			    	N/A
			    	@endif
			    </dd>
			</dl>
            
		</div>
	</div>
	<hr class="uk-grid-divider">
	<div class="uk-grid uk-margin-top">
		<div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
            <h3>Required Items</h3>
            <dl class="uk-description-list-line">
			    <dt>Funding Limits @if($compliance->funding_limits_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->funding_limits_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Reimbursement limits are not exceeded.</p>
			    	@if($compliance->funding_limits_notes !== NULL)
			    	<blockquote>{{$compliance->funding_limits_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Loan Requirements @if($compliance->loan_requirements_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->loan_requirements_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Partner is seeking full payoff of the loan (compare reimbursement coversheet to the loan) and loan is from a non-federal source. Loan is dated before the demolition was complete (compare to invoices, photos).</p>
			    	@if($compliance->loan_requirements_notes !== NULL)
			    	<blockquote>{{$compliance->loan_requirements_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Property @if($compliance->property_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->property_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Property is 1-4 units with at least one residential unit.</p>
			    	@if($compliance->property_notes !== NULL)
			    	<blockquote>{{$compliance->property_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Right to Demo @if($compliance->right_to_demo_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->right_to_demo_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Partner is the owner of the Parcel.</p>
			    	@if($compliance->right_to_demo_notes !== NULL)
			    	<blockquote>{{$compliance->right_to_demo_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Reimbursement Documentation @if($compliance->reimbursement_doc_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->reimbursement_doc_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>There is an invoice and proof of payment for all reimbursed costs with the exception of Maintenance, Administration (unless over $1,000), Loan Payoff, Retainage, and Greening Advances. A signed demolition contract was provided.</p>
			    	@if($compliance->reimbursement_doc_notes !== NULL)
			    	<blockquote>{{$compliance->reimbursement_doc_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Consolidated Certifications @if($compliance->consolidated_certs_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->consolidated_certs_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Certifications are present and all boxes are checked.</p>
			    	@if($compliance->consolidated_certs_notes !== NULL)
			    	<blockquote>{{$compliance->consolidated_certs_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Note & Mortgage @if($compliance->note_mortgage_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->note_mortgage_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Partner & Parcel information is correct on the Draft Note and Mortgage. Note and Mortgage were not altered to be less than $25,000. There is a property description.</p>
			    	@if($compliance->note_mortgage_notes !== NULL)
			    	<blockquote>{{$compliance->note_mortgage_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>SDO @if($compliance->sdo_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->sdo_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Property was not previously funded under the Save the Dream. Check Fuzzy Look Up Sheet in Monthly Invoicing Folder.</p>
			    	@if($compliance->sdo_notes !== NULL)
			    	<blockquote>{{$compliance->sdo_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Payment Processing @if($compliance->payment_processing_pass === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->payment_processing_pass == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>The coversheet was signed and dated by OHFA. The invoice correctly lists the amount of reimbursement approved.</p>
			    	@if($compliance->payment_processing_notes !== NULL)
			    	<blockquote>{{$compliance->payment_processing_notes}}</blockquote>
			    	@endif
			    </dd>
			</dl>      	  
		</div>

		<div class="uk-width-1-2@m uk-width-1-1@s uk-margin-top">
            <h3>Information Area</h3>
            <dl class="uk-description-list-line">
			    <dt>Property @if($compliance->property_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->property_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Unit appears vacant and blighted.</p>
			    	@if($compliance->property_yes_notes !== NULL)
			    	<blockquote>{{$compliance->property_yes_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Photos @if($compliance->photos_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->photos_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Photos match those posted on Google Street view. Photos match asbestos survey.</p>
			    	@if($compliance->photos_notes !== NULL)
			    	<blockquote>{{$compliance->photos_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Checklist @if($compliance->checklist_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->checklist_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>All items on associate checklist are marked as complete.</p>
			    	@if($compliance->checklist_notes !== NULL)
			    	<blockquote>{{$compliance->checklist_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Ineligible Costs @if($compliance->inelligible_costs_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->inelligible_costs_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Parner did not seek Marketing or Non-tax-foreclosure Litigation Costs.</p>
			    	@if($compliance->inelligible_costs_notes !== NULL)
			    	<blockquote>{{$compliance->inelligible_costs_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Target Area @if($compliance->target_area_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->target_area_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Parcel is in Partner's defined target area. You must check the approved target areas and compare to a map of the property address.</p>
			    	@if($compliance->target_area_notes !== NULL)
			    	<blockquote>{{$compliance->target_area_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Environmental @if($compliance->environmental_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->environmental_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Asbestos survey and EPA Notification are present, EPA Notice Sec. 18 is signed by the Owner. Sec. 17 is signed if asbestos is present.</p>
			    	@if($compliance->environmental_notes !== NULL)
			    	<blockquote>{{$compliance->environmental_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Contractors @if($compliance->contractors_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->contractors_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Associate screened the demolition and other subcontractors to ensure they are not on the APLS List. All contractors have an entry on the Subcontractor EPLS Search Record list.</p>
			    	@if($compliance->contractors_notes !== NULL)
			    	<blockquote>{{$compliance->contractors_notes}}</blockquote>
			    	@endif
			    </dd>
			    <dt>Allita @if($compliance->salesforce_yes === NULL)
					<div class="uk-badge uk-badge-warning uk-float-right">N/A</div>
					@elseif($compliance->salesforce_yes == 1)
			    	<div class="uk-badge uk-badge-success uk-float-right">PASS</div>
			    	@else
			    	<div class="uk-badge uk-badge-warning uk-float-right">FAIL</div>
			    	@endif</dt>
			    <dd><p>Address and Reimbursement amounts were correctly entered. Compare Allita to Deed and Per Property Reimbursement cover sheet.</p>
			    	@if($compliance->salesforce_notes !== NULL)
			    	<blockquote>{{$compliance->salesforce_notes}}</blockquote>
			    	@endif
			    </dd>
			</dl>
		</div>
	</div>


	<div class="uk-width-1-1">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-bottom">
				
				<hr />
			</div>		
		</div>
	</div>
</div>