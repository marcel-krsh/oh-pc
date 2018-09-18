<?php setlocale(LC_MONETARY, 'en_US');
$showDocDisclaimer = 0;
$parcelRulesAlert = '';

?>

<?php


//////////////////////////////////////// SET DONUT DATA
$TotalDonut = 0;
$dataArray = "parcelData";
if($parcelData->total_invoiced > 0){
$donutUse = "Invoiced";
} else if($parcelData->total_approved > 0){
$donutUse = "Approved";
} else if($parcelData->total_requested > 0){
$donutUse = "Requested";
} else if($parcelData->total_cost > 0){
$donutUse = "Cost";
} else {
$donutUse = "";
}
if($donutUse != "" && ($parcel->lb_validated == 1)) {
	// get the max reimbursement
	if(count($reimbursement_rules)){
        $min_units_value = $reimbursement_rules->minimum_units;
        $max_units_value = $reimbursement_rules->maximum_units;
        $max_reimbursement_value = $reimbursement_rules->maximum_reimbursement;
    }else{
        $min_units_value = '';
        $max_units_value = '';
        $max_reimbursement_value = '';
    }

	$AcquisitionDonutChartArray = 'Acquisition'.$donutUse; // usage ${$dataArray}->{$AcquisitionDonutChart}
	$AcquisitionAdvanceDonutChartArray = 'AcquisitionAdvance'.$donutUse; // usage ${$dataArray}->{$AcquisitionDonutChart}

	$NIPLoanPayoffDonutChartArray= 'NIPLoan'.$donutUse;
	$NIPLoanPayoffAdvanceDonutChartArray= 'NIPLoanAdvance'.$donutUse;

	$PreDemoDonutChartArray='PreDemo'.$donutUse;
	$PreDemoDonutAdvanceChartArray='PreDemoAdvance'.$donutUse;

	$DemolitionDonutChartArray='Demolition'.$donutUse;
	$DemolitionAdvanceDonutChartArray='DemolitionAdvance'.$donutUse;

	$GreeningDonutChartArray='Greening'.$donutUse;
	$GreeningAdvanceDonutChartArray='GreeningAdvance'.$donutUse;

	$MaintenanceDonutChartArray='Maintenance'.$donutUse;
	$MaintenanceAdvanceDonutChartArray='MaintenanceAdvance'.$donutUse;

	$AdminDonutChartArray='Administration'.$donutUse;
	$AdminAdvanceDonutChartArray='AdministrationAdvance'.$donutUse;

	$OtherDonutChartArray='Other'.$donutUse;
	$OtherAdvanceDonutChartArray='OtherAdvance'.$donutUse;
	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////SET THE VARIABLES
	///////////////////////////////////////////////////////////////////////
	$AcquisitionDonutChart = ${$dataArray}->{$AcquisitionDonutChartArray} ;
	$AcquisitionAdvanceDonutChart = ${$dataArray}->{$AcquisitionAdvanceDonutChartArray};
	if($AcquisitionDonutChart < $parcelData->acquisition_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Acquisition amount.</li>";
	$acquisition_bg_color = 'red';
	}

	$NIPLoanPayoffDonutChart= ${$dataArray}->{$NIPLoanPayoffDonutChartArray};
	$NIPLoanPayoffAdvanceDonutChart= ${$dataArray}->{$NIPLoanPayoffAdvanceDonutChartArray};
	if($NIPLoanPayoffDonutChart < $parcelData->nip_loan_payoff_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required NIP Loan Payoff amount.</li>";
	$nip_loan_payoff_bg_color = 'red';
	}

	$PreDemoDonutChart= ${$dataArray}->{$PreDemoDonutChartArray};
	$PreDemoAdvanceDonutChart= ${$dataArray}->{$PreDemoDonutAdvanceChartArray};
	if($PreDemoDonutChart < $parcelData->pre_demo_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Pre-Demo amount.</li>";
	$pre_demo_bg_color = 'red';
	}

	$DemolitionDonutChart=${$dataArray}->{$DemolitionDonutChartArray};
	$DemolitionAdvanceDonutChart=${$dataArray}->{$DemolitionAdvanceDonutChartArray};
	if($DemolitionDonutChart < $parcelData->demolition_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Demolition amount.</li>";
	$demolition_bg_color = 'red';
	}

	$GreeningDonutChart=${$dataArray}->{$GreeningDonutChartArray};
	$GreeningAdvanceDonutChart=${$dataArray}->{$GreeningAdvanceDonutChartArray};
	if($GreeningDonutChart + $GreeningAdvanceDonutChart < $parcelData->greening_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Greening amount.</li>";
	$greening_bg_color = 'red';
	}

	$MaintenanceDonutChart=${$dataArray}->{$MaintenanceDonutChartArray};
	$MaintenanceAdvanceDonutChart=${$dataArray}->{$MaintenanceAdvanceDonutChartArray};
	if($MaintenanceDonutChart < $parcelData->maintenance_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Maintenance amount.</li>";
	$maintenance_bg_color = 'red';
	}

	$AdminDonutChart=${$dataArray}->{$AdminDonutChartArray};
	$AdminAdvanceDonutChart=${$dataArray}->{$AdminAdvanceDonutChartArray};
	if($AdminDonutChart < $parcelData->admin_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Admin amount.</li>";
	$admin_bg_color = 'red';
	}

	$OtherDonutChart=${$dataArray}->{$OtherDonutChartArray};
	$OtherAdvanceDonutChart=${$dataArray}->{$OtherAdvanceDonutChartArray};
	if($OtherDonutChart < $parcelData->other_min){
	$parcelRulesAlert .= "<li>This parcel does not meet the minimum required Other amount.</li>";
	$other_bg_color = 'red';
	}

	$TotalDonut = ${$dataArray}->{'total_'.strtolower($donutUse)};
	if($TotalDonut > $max_reimbursement_value){
	$parcelRulesAlert ="<li ><strong class=\"attention\">THIS PARCEL IS OVER BUDGET</strong><br><hr />Because this parcel is over budget, any budget that is dependent on the availble balance will show as $".number_format((($max_reimbursement_value - $TotalDonut ) * -1),2)." over budget. Reduce any of your expenses by that amount, and the available balance dependent categories will show as within the total budget.<hr /></li>".$parcelRulesAlert;

	}

	//////////////////// DO AN ERROR CHECK
	$totalPossibleAdmin = ($parcelData->PreDemoCost + $parcelData->PreDemoAdvanceCost + $parcelData->DemolitionCost + $parcelData->DemolitionAdvanceCost + $parcelData->MaintenanceCost + $parcelData->MaintenanceAdvanceCost + $parcelData->GreeningCost + $parcelData->GreeningAdvanceCost) * $parcelData->admin_max_percent;
	// if($totalPossibleAdmin > ($parcelData->parcel_total_max * $parcelData->admin_max_percent)){
	// $totalPossibleAdmin = $parcelData->parcel_total_max * $parcelData->admin_max_percent;

	// } /// commenting out based on new rules.
	if($totalPossibleAdmin == 0){
		$totalPossibleAdmin = 0.00001;
	}
	//dd($totalPossibleAdmin);

//dd($parcelData->parcel_total_max);
	$RemainingBalanceDonutChart= $max_reimbursement_value - $TotalDonut;

	if($RemainingBalanceDonutChart == 0){
	///prevent division by zero but preserve the negative number.
	$RemainingBalanceDonutChart = 0.00001;
	}

	//////////////////// SET PROGRAM MAXES
	if($parcelData->acquisition_max != 0) {
	$acquisitionMax = $parcelData->acquisition_max;

	$acquisitionShowRelativeMaxIcon = 0;
	} else {
		//A zero means can use avalable balance
		$acquisitionMax = $RemainingBalanceDonutChart + $AcquisitionDonutChart;
		if($acquisitionMax > $max_reimbursement_value) {
			$acquisitionMax = $max_reimbursement_value;
		}

		$acquisitionShowRelativeMaxIcon = 1;
	}
	
	if($AcquisitionDonutChart > $acquisitionMax){
		$parcelRulesAlert .= "<li>This parcel\'s Acquistion amount exceeds the max allowed by the program.</li>";
		$acquisition_bg_color = 'red';
	}

	if($parcelData->pre_demo_max != 0) {
		$preDemoMax = $parcelData->pre_demo_max;
		$preDemoShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$preDemoMax = $RemainingBalanceDonutChart + $PreDemoDonutChart;
	if($preDemoMax > $max_reimbursement_value) {
	$preDemoMax = $max_reimbursement_value;
	}

	$preDemoShowRelativeMaxIcon = 1;
	}
	if($PreDemoDonutChart > $preDemoMax){
	$parcelRulesAlert .= "<li>This parcel\'s Pre-Demo amount exceeds the max allowed by the program.</li>";
	$pre_demo_bg_color = 'red';
	}

	if($parcelData->demolition_max != 0) {
	$demolitionMax = $parcelData->demolition_max;
	$demolitionShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$demolitionMax = $RemainingBalanceDonutChart + $DemolitionDonutChart;
	if($demolitionMax > $max_reimbursement_value) {
	$demolitionMax = $max_reimbursement_value;
	}

	$demolitionShowRelativeMaxIcon = 1;
	}
	if($DemolitionDonutChart > $demolitionMax){
	$parcelRulesAlert .= "<li>This parcel\'s Demolition amount exceeds the max allowed by the program.</li>";
	$demolition_bg_color = 'red';
	}

	if($parcelData->greening_max != 0) {
	$greeningMax = $parcelData->greening_max;
	$greeningShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$greeningMax = $RemainingBalanceDonutChart + $GreeningDonutChart;
	if($greeningMax > $max_reimbursement_value) {
	$greeningMax = $max_reimbursement_value;
	}

	$greeningShowRelativeMaxIcon = 1;
	}
	if($GreeningDonutChart > $greeningMax){
	$parcelRulesAlert .= "<li>This parcel\'s Greening amount exceeds the max allowed by the program.</li>";
	$greening_bg_color = 'red';
	}

	if($parcelData->maintenance_max != 0) {
	$maintenanceMax = $parcelData->maintenance_max;
	$maintenanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$maintenanceMax = $RemainingBalanceDonutChart + $MaintenanceDonutChart;
	if($maintenanceMax > $max_reimbursement_value) {
	$maintenanceMax = $max_reimbursement_value;
	}

	$maintenanceShowRelativeMaxIcon = 1;
	}
	if($MaintenanceDonutChart > $maintenanceMax){
	$parcelRulesAlert .= "<li>This parcel\'s Acquistion amount exceeds the max allowed by the program.</li>";
	$maintenance_bg_color = 'red';
	}

	if($parcelData->other_max != 0) {
	$otherMax = $parcelData->other_max;
	$otherShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance

	$otherMax = $RemainingBalanceDonutChart + $OtherDonutChart;
	if($otherMax > $max_reimbursement_value) {
	$otherMax = $max_reimbursement_value;
	}

	$otherShowRelativeMaxIcon = 1;
	}
	if($OtherDonutChart > $otherMax){
	$parcelRulesAlert .= "<li>This parcel\'s Other amount exceeds the max allowed by the program.</li>";
	$other_bg_color = 'red';
	}

	if($parcelData->nip_loan_payoff_max != 0) {
	$nipLoanPayoffMax = $parcelData->nip_loan_payoff_max;
	$nipLoanPayoffShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$nipLoanPayoffMax = $RemainingBalanceDonutChart + $NIPLoanPayoffDonutChart;
	if($nipLoanPayoffMax > $max_reimbursement_value) {
	$nipLoanPayoffMax = $max_reimbursement_value;
	}

	$nipLoanPayoffShowRelativeMaxIcon = 1;
	}
	if($NIPLoanPayoffDonutChart > $nipLoanPayoffMax){
	$parcelRulesAlert .= "<li>This parcel\'s NIP Loan Payoff amount exceeds the max allowed by the program.</li>";
	$nip_loan_payoff_bg_color = 'red';
	}

	//////////////////////// SET PROGRAM ADVANCE MAXES
	if($parcelData->acquisition_advance == 1) {
	$acquisition_advance_bar = 1;
	if($parcelData->acquisition_max_advance != 0) {
	$acquisitionMaxAdvance= $parcelData->acquisition_max_advance;
	$acquisitionAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$acquisitionMaxAdvance= $RemainingBalanceDonutChart + $AcquisitionAdvanceDonutChart;
	if($acquisitionMaxAdvance > $max_reimbursement_value) {
	$acquisitionMaxAdvance = $max_reimbursement_value;
	}

	$acquisitionAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$acquisition_advance_bar = 0;
	}
	if($acquisition_advance_bar == 1 && $AcquisitionAdvanceDonutChart > $acquisitionMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Acquistion Advance amount exceeds the max allowed by the program.</li>";
	$acquisition_advance_bg_color = 'red';
	}

	if($parcelData->pre_demo_advance == 1) {
	$pre_demo_advance_bar = 1;
	if($parcelData->pre_demo_max_advance != 0) {
	$preDemoMaxAdvance= $parcelData->pre_demo_max_advance;
	$preDemoAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$preDemoMaxAdvance= $RemainingBalanceDonutChart + $PreDemoAdvanceDonutChart;
	if($preDemoMaxAdvance > $max_reimbursement_value) {
	$preDemoMaxAdvance = $max_reimbursement_value;
	}

	$preDemoAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$pre_demo_advance_bar = 0;
	}
	if($pre_demo_advance_bar == 1 && $PreDemoAdvanceDonutChart > $preDemoMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Pre-Demo Advance amount exceeds the max allowed by the program.</li>";
	$pre_demo_advance_bg_color = 'red';
	}

	if($parcelData->demolition_advance == 1) {
	$demolition_advance_bar = 1;
	if($parcelData->demolition_max_advance != 0) {
	$demolitionMaxAdvance= $parcelData->demolition_max_advance;
	$demolitionAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$demolitionMaxAdvance= $RemainingBalanceDonutChart + $DemolitionAdvanceDonutChart;
	if($demolitionMaxAdvance > $max_reimbursement_value) {
	$demolitionMaxAdvance = $max_reimbursement_value;
	}

	$demolitionAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$demolition_advance_bar = 0;
	}
	if($demolition_advance_bar == 1 && $DemolitionAdvanceDonutChart > $demolitionMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Demolition Advance amount exceeds the max allowed by the program.</li>";
	$demolition_advance_bg_color = 'red';
	}

	if($parcelData->greening_advance == 1) {
	$greening_advance_bar = 1;
	if($parcelData->greening_max_advance != 0) {
	$greeningMaxAdvance= $parcelData->greening_max_advance;
	$greeningAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$greeningMaxAdvance= $RemainingBalanceDonutChart + $GreeningAdvanceDonutChart;
	if($greeningMaxAdvance > $max_reimbursement_value) {
	$greeningMaxAdvance = $max_reimbursement_value;
	}

	$greeningAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$greening_advance_bar = 0;
	}
	if($greening_advance_bar == 1 && $GreeningAdvanceDonutChart > $greeningMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Greening Advance amount exceeds the max allowed by the program.</li>";
	$greening_advance_bg_color = 'red';
	}

	if($parcelData->maintenance_advance == 1) {
	$maintenance_advance_bar = 1;
	if($parcelData->maintenance_max_advance != 0) {
	$maintenanceMaxAdvance= $parcelData->maintenance_max_advance;
	$maintenanceAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$maintenanceMaxAdvance= $RemainingBalanceDonutChart + $MaintenanceAdvanceDonutChart;
	if($maintenanceMaxAdvance > $max_reimbursement_value) {
	$maintenanceMaxAdvance = $max_reimbursement_value;
	}
	$maintenanceAdvanceShowRelativeMaxIcon = 1;

	}

	} else {
	$maintenance_advance_bar = 0;
	}
	if($maintenance_advance_bar == 1 && $MaintenanceAdvanceDonutChart > $maintenanceMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Maintenance Advance amount exceeds the max allowed by the program.</li>";
	$maintenance_advance_bg_color = 'red';
	}

	if($parcelData->administration_advance == 1) {
	$administration_advance_bar = 1;
	if($parcelData->administration_max_advance != 0) {
	$administrationMaxAdvance= $parcelData->administration_max_advance;
	$administrationAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$administrationMaxAdvance= $RemainingBalanceDonutChart + $AdminAdvanceDonutChart;
	if($administrationMaxAdvance > $max_reimbursement_value) {
	$administrationMaxAdvance = $max_reimbursement_value;
	}

	$administrationAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$administration_advance_bar = 0;
	}
	if($administration_advance_bar == 1 && $AdminAdvanceDonutChart > $administrationMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Admin Advance amount exceeds the max allowed by the program.</li>";
	$admin_advance_bg_color = 'red';
	}

	if($parcelData->other_advance == 1) {
	$other_advance_bar = 1;
	if($parcelData->other_max_advance != 0) {
	$otherMaxAdvance= $parcelData->other_max_advance;
	$otherAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$otherMaxAdvance= $RemainingBalanceDonutChart + $OtherAdvanceDonutChart;
	if($otherMaxAdvance > $max_reimbursement_value) {
	$otherMaxAdvance = $max_reimbursement_value;
	}

	$otherAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$other_advance_bar = 0;
	}
	if($other_advance_bar == 1 && $OtherAdvanceDonutChart > $otherMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s Other Advance amount exceeds the max allowed by the program.</li>";
	$other_advance_bg_color = 'red';
	}

	if($parcelData->nip_loan_payoff_advance == 1) {
	$nip_loan_payoff_advance_bar = 1;
	if($parcelData->nip_loan_payoff_max_advance != 0) {
	$nipLoanPayoffMaxAdvance= $parcelData->nip_loan_payoff_max_advance;
	$nipLoanPayoffAdvanceShowRelativeMaxIcon = 0;
	} else {
	//A zero means can use avalable balance
	$nipLoanPayoffMaxAdvance = $RemainingBalanceDonutChart + $NIPLoanPayoffAdvanceDonutChart;
	if($nipLoanPayoffMaxAdvance > $max_reimbursement_value) {
	$nipLoanPayoffMaxAdvance = $max_reimbursement_value;
	}

	$nipLoanPayoffAdvanceShowRelativeMaxIcon = 1;
	}

	} else {
	$nip_loan_payoff_advance_bar = 0;
	}
	if($nip_loan_payoff_advance_bar == 1 && $NIPLoanPayoffAdvanceDonutChart > $nipLoanPayoffMaxAdvance){
	$parcelRulesAlert .= "<li>This parcel\'s NIP Loan Payoff Advance amount exceeds the max allowed by the program.</li>";
	$nip_loan_payoff_advance_bg_color = 'red';
	}
	//dd($RemainingBalanceDonutChart, $parcelData->nip_loan_payoff_advance, $nip_loan_payoff_advance_bar, $parcelData->nip_loan_payoff_max_advance, $nipLoanPayoffMaxAdvance, $nipLoanPayoffAdvanceShowRelativeMaxIcon);

}

	// check for zeros and fix

 ?>
<div id="notifications"></div>
<script>
	$('.detail-tab-1-text').html('<span class="a-home-2"></span> PARCEL: {{$parcelData->parcel_name}} :: @if(Auth::user()->entity_type == "landbank"){{strtoupper($parcelData->LB_Status)}} @else LB: {{strtoupper($parcelData->LB_Status)}} | HFA: {{strtoupper($parcelData->HFA_Status)}} @endif ');
	$('#main-option-text').html('Parcel: {{$parcelData->parcel_name}}');
	$('#main-option-icon').attr('uk-icon','arrow-circle-o-left');
</script>

<div uk-grid>
	<div class="uk-width-1-1">
		<div class="uk-panel uk-panel-divider no-padding-bottom uk-margin-top">
			<div class="guidesteps uk-grid-divider uk-grid-small" uk-height-match uk-grid hidden>
				<div class="uk-width-1-1 uk-width-1-6@m uk-margin-bottom">
					<div class="uk-panel active">
					    <h3 class="uk-panel-title uk-text-center">STEP 1 - (LAND BANK)</h3>
					    <p class="uk-text-center uk-text-primary uk-text-bold">Prepare Parcel</p>
					    <ul class="uk-list uk-list-space">
						    <li uk-tooltip="{{$guide_help[24]}}"><span class="@if(guide_check_step(24, $parcel->id) || guide_check_step(23, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(24, $parcel->id) || guide_check_step(23, $parcel->id)){{$guide_name[24]['name_completed']}} @else {{$guide_name[24]['name']}}@endif</li>
						    <li><span  uk-tooltip="{{$guide_help[25]}}"><span class="@if(guide_check_step(25, $parcel->id) || guide_check_step(23, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(25, $parcel->id) || guide_check_step(23, $parcel->id)){{$guide_name[25]['name_completed']}} @else {{$guide_name[25]['name']}}@endif</span> </li>
						    <li uk-tooltip="{{$guide_help[26]}}"><span class="@if(guide_check_step(26, $parcel->id) || guide_check_step(23, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(26, $parcel->id) || guide_check_step(23, $parcel->id)){{$guide_name[26]['name_completed']}} @else {{$guide_name[26]['name']}}@endif</li>
						    <li uk-tooltip="{{$guide_help[27]}}"><span class="@if(guide_check_step(27, $parcel->id) || guide_check_step(23, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(27, $parcel->id) || guide_check_step(23, $parcel->id)){{$guide_name[27]['name_completed']}} @else {{$guide_name[27]['name']}}@endif 
						    	<a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
						    </li>
						</ul>
					</div>
				</div>
				<div class="uk-width-1-1 uk-width-1-6@m uk-margin-bottom">
					<div class="uk-panel active">
					    <h3 class="uk-panel-title uk-text-center">STEP 2 - (LAND BANK)</h3>
					    <p class="uk-text-center uk-text-primary uk-text-bold">Review & Approve Reimbursement Request</p>
					    <ul class="uk-list uk-list-space">
						    <li uk-tooltip="{{$guide_help[29]}}"><span class="@if(guide_check_step(29, $parcel->id) || guide_check_step(28, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(29, $parcel->id) || guide_check_step(28, $parcel->id)){{$guide_name[29]['name_completed']}} @else {{$guide_name[29]['name']}}@endif
						    @if($parcel->associatedRequest)
						    <a onclick="window.open('/requests/{{$parcel->associatedRequest->reimbursement_request_id}}', '_blank')" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
						    @endif
						    </li>
						    <li uk-tooltip="{{$guide_help[30]}}"><span class="@if(guide_check_step(30, $parcel->id) || guide_check_step(28, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(30, $parcel->id) || guide_check_step(28, $parcel->id)){{$guide_name[30]['name_completed']}} @else {{$guide_name[30]['name']}}@endif
						    @if($parcel->associatedRequest)
						    <a onclick="window.open('/requests/{{$parcel->associatedRequest->reimbursement_request_id}}', '_blank')" class="uk-link-muted"><span class="a-upload uk-text-muted"></span></a>
						    @endif
						    </li>
						    <li uk-tooltip="{{$guide_help[31]}}"><span class="@if(guide_check_step(31, $parcel->id) || guide_check_step(28, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(31, $parcel->id) || guide_check_step(28, $parcel->id)){{$guide_name[31]['name_completed']}} @else {{$guide_name[31]['name']}}@endif</li>
						</ul>
					</div>
				</div>
				<div class="uk-width-1-1 uk-width-1-6@m uk-margin-bottom">
					<div class="uk-panel active">
					    <h3 class="uk-panel-title uk-text-center">STEP 3 - (HFA)</h3>
					    <p class="uk-text-center uk-text-primary uk-text-bold">Review Request</p>
							    <ul class="uk-list uk-list-space">
								    <li @if($current_user->isFromEntity(1)) uk-tooltip="{{$guide_help[33]}}"@endif><span class="@if(Auth::user()->isHFAAdmin()) uk-link step33 @endif @if(guide_check_step(33, $parcel->id)) a-checkbox-checked @else a-checkbox @endif" @if(Auth::user()->isHFAAdmin()) onclick="validate_parcel_hfa()" @endif></span> 
								    	<span class="step33name @if(!guide_check_step(33, $parcel->id)) uk-hidden @endif">
								    		{{$guide_name[33]['name_completed']}}
								    	</span> 
								    	<span class="step33name @if(guide_check_step(33, $parcel->id)) uk-hidden @endif">
								    		{{$guide_name[33]['name']}}
								    	</span>
								    </li>
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[34]}}"@endif><span class="@if(guide_check_step(34, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> 
								    	@if(guide_check_step(34, $parcel->id) || guide_check_step(32, $parcel->id))
								    	{{$guide_name[34]['name_completed']}} 
								    	@else 
								    	{{$guide_name[34]['name']}}
								    	@endif
								    	<a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
								    </li>
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[35]}}"@endif><span class="@if(guide_check_step(35, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(35, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[35]['name_completed']}} @else {{$guide_name[35]['name']}}@endif</li>
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[55]}}"@endif><span class="@if(guide_check_step(55, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(55, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[55]['name_completed']}} @else {{$guide_name[55]['name']}}@endif
									    @if($parcel->associatedPo)
									    <a onclick="window.open('/po/{{$parcel->associatedPo->purchase_order_id}}', '_blank')" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
									    @endif
								    </li>
								     <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[36]}}"@endif><span class="@if(guide_check_step(36, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(36, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[36]['name_completed']}} @else {{$guide_name[36]['name']}}@endif

									    @if($parcel->associatedPo)
									    <a onclick="window.open('/po/{{$parcel->associatedPo->purchase_order_id}}', '_blank')" class="uk-link-muted" uk-tooltip=""><span class="a-upload uk-text-muted"></span></a>
									    @endif
									</li>
								    @if( ($parcel->compliance || $parcel->compliance_manual) && !is_null($parcel->sf_parcel_id))
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[37]}}"@endif><span class="@if(guide_check_step(37, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(37, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[37]['name_completed']}} @else {{$guide_name[37]['name']}}@endif</li>
								    @endif
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[38]}}"@endif><span class="@if(guide_check_step(38, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(38, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[38]['name_completed']}} @else {{$guide_name[38]['name']}}@endif

									    @if($parcel->associatedPo)
									    <a onclick="window.open('/po/{{$parcel->associatedPo->purchase_order_id}}', '_blank')" class="uk-link-muted"><span class="a-upload uk-text-muted"></span></a>
									    @endif
									</li>
								    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[39]}} (sent to role: 'Approves Invoices for Land Bank')"@endif><span class="@if(guide_check_step(39, $parcel->id) || guide_check_step(32, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(39, $parcel->id) || guide_check_step(32, $parcel->id)){{$guide_name[39]['name_completed']}} @else {{$guide_name[39]['name']}}@endif
									    @if($parcel->associatedPo)
									    <a onclick="window.open('/po/{{$parcel->associatedPo->purchase_order_id}}', '_blank')" class="uk-link-muted"><span class="a-upload uk-text-muted"></span></a>
									    @endif
									</li>
								</ul>
					</div>
				</div>
				<div class="uk-width-1-1 uk-width-1-2@m ">
					<div class="uk-grid-divider" uk-grid>
						<div class="uk-width-1-1 uk-width-1-1@m uk-margin-bottom">
							<div class="uk-panel active">
							    <h3 class="uk-panel-title uk-text-center">ANY POINT AFTER STEP 3 - (LAND BANK & HFA)</h3>
							    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Pay and Document Advances & Retainages</p>
							    @if(count($parcel->costItemsWithAdvance) || count($parcel->retainages))
							    <div class="uk-grid">
							    	@if(count($parcel->costItemsWithAdvance))
							    	@foreach($parcel->costItemsWithAdvance as $step_advance)
								    <div class="uk-width-1-2@m uk-margin-bottom">
								    	<p class="uk-text-bold" uk-tooltip="pos:top-left;title:Advance">{{$step_advance->description}}</p>
								    	<ul class="uk-list uk-list-space ">
										    <li uk-tooltip="pos:top-left;title:{{$guide_help[41]}}">
										    	<span class="@if(guide_parcel_advance_paid_docs_uploaded($parcel, $step_advance->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_parcel_advance_paid_docs_uploaded($parcel, $step_advance->id)){{$guide_name[41]['name_completed']}} @else {{$guide_name[41]['name']}}@endif
											    <a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
											</li>  
										    <li @if($current_user->isFromEntity(1))uk-tooltip="pos:top-left;title="{{$guide_help[42]}}"@endif>
										    	<span class="@if(guide_parcel_advance_paid_docs_reviewed($parcel, $step_advance->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_parcel_advance_paid_docs_reviewed($parcel, $step_advance->id)){{$guide_name[42]['name_completed']}} @else {{$guide_name[42]['name']}}@endif
											    <a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted"><span class="a-upload uk-text-muted"></span></a>
											</li> 
										    <li @if($current_user->isFromEntity(1))uk-tooltip="pos:top-left;title:{{$guide_help[43]}}"@endif>
										    	<span class="@if(Auth::user()->isHFAAdmin()) uk-link step_a{{$step_advance->id}} @endif" class="@if($step_advance->advance_paid == 1 ) a-checkbox-checked @else a-checkbox @endif" @if(Auth::user()->isHFAAdmin()) onclick="mark_advance_paid_hfa({{$step_advance->id}})" @endif></span> 
										    	<span class="step_a{{$step_advance->id}}_name @if($step_advance->advance_paid != 1 ) uk-hidden @endif">
										    		HFA: Advance Paid
										    	</span> 
										    	<span class="step_a{{$step_advance->id}}_name @if($step_advance->advance_paid == 1 ) uk-hidden @endif">
										    		{{$guide_name[43]['name']}}
										    	</span>
										    </li> 	
										</ul>
								    </div>
								    @endforeach
								    @endif
								    @if(count($parcel->retainages))
								    @foreach($parcel->retainages as $step_retainage)
							        <div class="uk-width-1-2@m uk-margin-bottom" >
								    	<p class="uk-text-bold" uk-tooltip="pos:top-left;title:Retainage">@if($step_retainage->expense_category){{$step_retainage->expense_category->expense_category_name}}@endif</p>
								    	<ul class="uk-list uk-list-space ">
										    <li uk-tooltip="pos:top-left;title:{{$guide_help[41]}}">
										    	<span class="@if(guide_parcel_retainage_paid_docs_uploaded($parcel, $step_retainage->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_parcel_retainage_paid_docs_uploaded($parcel, $step_retainage->id)){{$guide_name[41]['name_completed']}} @else {{$guide_name[41]['name']}}@endif
											    <a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted"><span class="a-upload uk-text-muted"></span></a>
											</li>  
										    <li @if($current_user->isFromEntity(1))uk-tooltip="pos:top-left;title:{{$guide_help[42]}}"@endif>
										    	<span class="@if(guide_parcel_retainage_paid_docs_reviewed($parcel, $step_retainage->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_parcel_retainage_paid_docs_reviewed($parcel, $step_retainage->id)){{$guide_name[42]['name_completed']}} @else {{$guide_name[42]['name']}}@endif
											    <a onclick="loadParcelSubTab('documents',{{$parcel->id}})" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
											</li> 
										    <li @if($current_user->isFromEntity(1))uk-tooltip="pos:top-left;title:{{$guide_help[43]}}"@endif>
										    	<span class="@if(Auth::user()->isHFAAdmin()) uk-link step_r{{$step_retainage->id}} @endif" class="@if($step_retainage->paid ) a-checkbox-checked @else a-checkbox @endif" @if(Auth::user()->isHFAAdmin()) onclick="mark_retainage_paid_hfa({{$step_retainage->id}})" @endif></span> 
										    	<span class="step_r{{$step_retainage->id}}_name @if(!$step_retainage->paid == 1 ) uk-hidden @endif">
										    		HFA: Retainage Paid
										    	</span> 
										    	<span class="step_r{{$step_retainage->id}}_name @if($step_retainage->paid == 1 ) uk-hidden @endif">
										    		{{$guide_name[43]['name']}}
										    	</span>
										    </li> 	
										</ul>
								    </div> 
								    @endforeach
							    	@endif
								</div>
								@else
								<p>There are no retainages/advances at the moment.</p>
							    @endif
							</div>
						</div>
					</div>
					<div class="uk-grid-divider" uk-grid>
						<div class="uk-width-1-1 uk-width-1-3@m uk-margin-bottom">
							<div class="uk-grid">
								<div class="uk-width-1-1 uk-width-1-1@l">
									<div class="uk-panel active">
									    <h3 class="uk-panel-title uk-text-center">STEP 4 - (LAND BANK)</h3>
									    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Approve Invoice, Send to HFA</p>
									    <ul class="uk-list uk-list-space">
										    <li uk-tooltip="{{$guide_help[45]}}"><span class="@if(guide_check_step(45, $parcel->id) || guide_check_step(44, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(45, $parcel->id) || guide_check_step(44, $parcel->id)){{$guide_name[45]['name_completed']}} @else {{$guide_name[45]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[46]}}"@endif><span class="@if(guide_check_step(46, $parcel->id) || guide_check_step(44, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(46, $parcel->id) || guide_check_step(44, $parcel->id)){{$guide_name[46]['name_completed']}} @else {{$guide_name[46]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[47]}}"@endif><span class="@if(guide_check_step(47, $parcel->id) || guide_check_step(44, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(47, $parcel->id) || guide_check_step(44, $parcel->id)){{$guide_name[47]['name_completed']}} @else {{$guide_name[47]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" ><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="uk-width-1-1 uk-width-1-3@m uk-margin-bottom">
							<div class=" uk-grid-divider" uk-grid>
								<div class="uk-width-1-1 uk-width-1-1@l">
									<div class="uk-panel active">
									    <h3 class="uk-panel-title uk-text-center">STEP 5 - (HFA)</h3>
									    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Remit Invoice Payment</p>
									    <ul class="uk-list uk-list-space">
										   <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[49]}}"@endif><span class="@if(guide_check_step(49, $parcel->id) || guide_check_step(48, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(49, $parcel->id) || guide_check_step(48, $parcel->id)){{$guide_name[49]['name_completed']}} @else {{$guide_name[49]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip=""><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										   <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[50]}}"@endif><span class="@if(guide_check_step(50, $parcel->id) || guide_check_step(48, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(50, $parcel->id) || guide_check_step(48, $parcel->id)){{$guide_name[50]['name_completed']}} @else {{$guide_name[50]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip=""><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										   <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[51]}}"@endif><span class="@if(guide_check_step(51, $parcel->id) || guide_check_step(48, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(51, $parcel->id) || guide_check_step(48, $parcel->id)){{$guide_name[51]['name_completed']}} @else {{$guide_name[51]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip=""><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										   <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[52]}}"@endif><span class="@if(guide_check_step(52, $parcel->id) || guide_check_step(48, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(52, $parcel->id) || guide_check_step(48, $parcel->id)){{$guide_name[52]['name_completed']}} @else {{$guide_name[52]['name']}}@endif</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						
						<div class="uk-width-1-1 uk-width-1-3@m uk-margin-bottom">
							<div class="uk-grid-divider" uk-grid>
								<div class="uk-width-1-1 uk-width-1-1@l">
									<div class="uk-panel active">
									    <h3 class="uk-panel-title uk-text-center">STEP 6 - (FISCAL AGENT)</h3>
									    <p class="uk-text-center uk-text-primary uk-text-bold doubleheight">Remit Invoice Payment</p>
									    <ul class="uk-list uk-list-space">
										    <li @if($current_user->isFromEntity(1))uk-tooltip="{{$guide_help[54]}}"@endif><span class="@if(guide_check_step(54, $parcel->id) || guide_check_step(53, $parcel->id)) a-checkbox-checked @else a-checkbox @endif"></span> @if(guide_check_step(54, $parcel->id) || guide_check_step(53, $parcel->id)){{$guide_name[54]['name_completed']}} @else {{$guide_name[54]['name']}}@endif
											    @if($parcel->associatedInvoice)
											    <a onclick="window.open('/invoices/{{$parcel->associatedInvoice->reimbursement_invoice_id}}', '_blank')" class="uk-link-muted" uk-tooltip=""><span class="a-upload uk-text-muted"></span></a>
											    @endif
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-1-1">
					<div class="uk-align-right">
						<button class="uk-button uk-button-default guidesteps" hidden uk-toggle="target:.guidesteps"><span class="a-checklist-2"></span> Hide Guide</button>
					</div>
				</div>
			</div>
			<div class="uk-align-right">
				<button class="uk-button uk-button-default guidesteps " uk-toggle="target: .guidesteps"><span class="a-checklist-2"></span> Show Guide</button>
			</div>
		</div>
	</div>
</div>
@if($donutUse != ""  && ($parcel->lb_validated == 1))
<div class="uk-grid uk-margin-top">
	<div class="uk-width-1-1 uk-width-1-6@m parcel-donut-chart">

		<h2 class="uk-visible@s uk-hidden@m uk-text-center uk-margin-top-remove"><span class="a-home-2"></span>: {{$parcelData->parcel_name}}</h2>
		<iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;">

		</iframe>
		<canvas id="allocationsChart" width="250" height="250" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
		<h3 class=" uk-text-center uk-margin-bottom-large">{{money_format('%-8n', $TotalDonut)}} / {{money_format('%-8n', $max_reimbursement_value)}} ({{round(($TotalDonut*100)/($max_reimbursement_value + .0001))}}%)</h3>
		<small><p align='center'>BASED ON {{strtoupper($donutUse)}} AMOUNTS</p></small>
		<script type="text/javascript">
			//var insertTotalAllcationForFileHere = 16799;

			Chart.defaults.global.legend.display = false;
			Chart.defaults.global.tooltips.enabled = true;

			// THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE  - to make this simple - this tab is reloaded on form submission of new payment/ payment edits //
			var allocationsOptions = {
				//Boolean - Whether we should show a stroke on each segment
				segmentShowStroke : false,
				legendPosition : 'bottom',


				//String - The colour of each segment stroke
				segmentStrokeColor : "#fff",

				//Number - The width of each segment stroke
				segmentStrokeWidth : 0,

				//The percentage of the chart that we cut out of the middle.
				cutoutPercentage : 67,

				easing: "easeOutBounce",

				duration: 100,

				tooltips: {
					enabled: true,
					mode: 'single',
					callbacks: {
						label: function(tooltipItem, data) {
							var label = data.labels[tooltipItem.index];
							var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
							return label + ': $' + addCommas(datasetLabel) ;
						}
					}
				}


			}
			function addCommas(nStr)
			{
				nStr += '';
				x = nStr.split('.');
				x1 = x[0];
				x2 = x.length > 1 ? '.' + x[1] : '';
				var rgx = /(\d+)(\d{3})/;
				while (rgx.test(x1)) {
					x1 = x1.replace(rgx, '$1' + ',' + '$2');
				}
				return x1 + x2;
			}

			// The last data point is the ballance left of the maximum amount they can allocate (for 2016 HHF this is 35K).
			// It does not get a label - rather it appears to show a gap in the full circle indicating they have not maxed out the allocation
			var data = {
				datasets: [{
					data: [
						{{$AcquisitionAdvanceDonutChart}},
						{{$AcquisitionDonutChart}},

						{{$NIPLoanPayoffAdvanceDonutChart}},
						{{$NIPLoanPayoffDonutChart}},

						{{$PreDemoAdvanceDonutChart}},
						{{$PreDemoDonutChart}},

						{{$DemolitionAdvanceDonutChart}},
						{{$DemolitionDonutChart}},

						{{$GreeningAdvanceDonutChart}},
						{{$GreeningDonutChart}},

						{{$MaintenanceAdvanceDonutChart}},
						{{$MaintenanceDonutChart}},

						{{$AdminAdvanceDonutChart}},
						{{$AdminDonutChart}},

						{{$OtherAdvanceDonutChart}},
						{{$OtherDonutChart}},

						<?php
                        if($RemainingBalanceDonutChart <= 0){
                        ///prevent division by zero but preserve the negative number.
                        echo 0;
                        } else {
                            echo $RemainingBalanceDonutChart;
                        }
                        ?>

                    ],
					backgroundColor: [

						"#5a7c92","#005186",


						"#bfb28c","#B2912F",


						"#696868","#4D4D4D",


						"#adc7da","#5DA5DA",


						"#8bad8e","#60BD68",


						"#d0b18a","#FAA43A",


						"#ea9aa7","#F17CB0",


						"#c3b0c3","#B276B2",

						"rgba(245,245,245,1)"

					],
					label: 'My dataset' // for legend
				}],
				labels: [
					"Acquisition Advance",
					"Acquisition",

					"NIP Loan Payoff",
					"NIP Loan Payoff",

					"Pre-demo Advance",
					"Pre-demo",

					"Demolition Advance",
					"Demolition",

					"Greening Advance",
					"Greening",

					"Maintenance Advance",
					"Maintenance",

					"Admin Advance",
					"Admin",

					"Other Advance",
					"Other",

					"Balance Remaining"

				]
			};



			//Get the context of the Doughnut Chart canvas element we want to select
			var ctx = document.getElementById("allocationsChart").getContext("2d");

			// Create the Doughnut Chart
			var myDoughnutChart = new Chart(ctx, {
				type: 'doughnut',
				data: data,
				options: allocationsOptions
			});
		</script>
		


	</div>
	<div class="uk-width-1-1 uk-width-1-3@m uk-margin-large-bottom" >
		<div class="uk-grid uk-grid-small less-margin-top">
			<div class="uk-width-1-1">
				<ul class="uk-list no-shade uk-text-small" style="line-height: 22px; margin-top: 28px; font-size: 10px;">
					@if($acquisition_advance_bar == 1)
						<li onclick="dynamicModalLoad('expense-categories-details/0/2/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#acquisition-advance-aggregates').toggleClass('acquisition-advance-aggregate-hovered');" onmouseout="$('#acquisition-advance-aggregates').toggleClass('acquisition-advance-aggregate-hovered');">
							{{--<div  id="acquisition-advance-bar" class="uk-progress uk-text-center" >--}}
								{{--<div  class="uk-progress-bar @if(isset($acquisition_advance_bg_color)) attention @endif" style="width:{{round(($AcquisitionAdvanceDonutChart*100)/($acquisitionMaxAdvance + .0001))}}%; margin:0px;--}}
										{{--background:@if(isset($acquisition_advance_bg_color))--}}
								{{--{{$acquisition_advance_bg_color}};--}}
										{{--width:100%;--}}
								{{--@elseif(round(($AcquisitionAdvanceDonutChart*100)/($acquisitionMaxAdvance  + .0001)) == 0)--}}
										{{--none;--}}
								{{--@else--}}
										{{--#5a7c92;--}}
								{{--@endif--}}
										{{--">ACQUISITION ADVANCE( {{money_format('%-8n', $AcquisitionAdvanceDonutChart)}} OF {{money_format('%-8n',$acquisitionMaxAdvance)}}--}}
									{{--@if($acquisitionAdvanceShowRelativeMaxIcon == 1)--}}
										{{--<sup><span class='a-info-circle'></span></sup>--}}
										{{--@endif--}}
                                       {{--)</div>--}}
							{{--</div>--}}
						</li>
						@endif
						<li onclick="dynamicModalLoad('expense-categories-details/0/2/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#acquisition-aggregates').toggleClass('acquisition-aggregate-hovered');" onmouseout="$('#acquisition-aggregates').toggleClass('acquisition-aggregate-hovered');">
							<div  id="acquisition-bar" class="uk-progress uk-text-center" >
								<div  class="uk-progress-bar @if(isset($acquisition_bg_color)) attention @endif" style="width: {{round(($AcquisitionDonutChart*100)/$acquisitionMax)}}%; margin:0px;
										background:
								@if(isset($acquisition_bg_color))
								{{$acquisition_bg_color}};
										width:100%;
								@elseif(round(($AcquisitionDonutChart)*100)/($acquisitionMax + .0001) > 0)
										#005186;
								@else
										none;
								@endif
										">ACQUISITION ( {{money_format('%-8n', $AcquisitionDonutChart)}} OF {{money_format('%-8n',($acquisitionMax - $AcquisitionAdvanceDonutChart) )}}
									@if($acquisitionShowRelativeMaxIcon == 1 || $acquisition_advance_bar == 1)
										<sup><span class="a-info-circle"></span></sup>
										@endif
										@if($minimumRules['acquisition'])
										@if($AcquisitionDonutChart + $AcquisitionAdvanceDonutChart >  $minimumRules['acquisition']->amount)
											<?php $showDocDisclaimer = 1; ?>
											<sup><span class="a-file-left"></span></sup>
										@endif
										@endif
                                        )
								</div>
							</div>
						</li>

						@if($nip_loan_payoff_advance_bar == 1)
							<li onclick="dynamicModalLoad('expense-categories-details/0/9/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#nip-loan-payoff-advance-aggregates').toggleClass('nip-loan-payoff-advance-aggregate-hovered');" onmouseout="$('#nip-loan-payoff-advance-aggregates').toggleClass('nip-loan-payoff-advance-aggregate-hovered');">
								<div  id="nip-loan-payoff-advance-bar" class="uk-progress uk-text-center" >
									<div  class="uk-progress-bar @if(isset($nip_loan_payoff_advance_bg_color)) attention @endif" style="width: {{round(($NIPLoanPayoffAdvanceDonutChart*100)/($nipLoanPayoffMaxAdvance + .0001))}}%; margin:0px;
											background:
									@if(isset($nip_loan_payoff_advance_bg_color))
									{{$nip_loan_payoff_advance_bg_color}};
											width:100%;
									@elseif(round(($NIPLoanPayoffAdvanceDonutChart*100)/($nipLoanPayoffMaxAdvance + .0001)) == 0)
											none;
									@else
											#bfb28c;
									@endif
											">NIP LOAN PAYOFF ADVANCE ( {{money_format('%-8n', $NIPLoanPayoffAdvanceDonutChart)}} OF {{money_format('%-8n',$nipLoanPayoffMaxAdvance)}}
										@if($nipLoanPayoffAdvanceShowRelativeMaxIcon == 1)
											<sup><span class='a-info-circle'></span></sup>
											@endif
                                            )</div>
								</div>
							</li>
							@endif
							<li onclick="dynamicModalLoad('expense-categories-details/0/9/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#nip-loan-payoff-aggregates').toggleClass('nip-loan-payoff-aggregate-hovered');" onmouseout="$('#nip-loan-payoff-aggregates').toggleClass('nip-loan-payoff-aggregate-hovered');">
								<div  id="nip-loan-payoff-bar" class="uk-progress uk-text-center" >
									<div  class="uk-progress-bar @if(isset($nip_loan_payoff_bg_color)) attention @endif" style="width: {{round(($NIPLoanPayoffDonutChart*100)/(($nipLoanPayoffMax - $NIPLoanPayoffAdvanceDonutChart) + .0001))}}%; margin:0px; background:
									@if(isset($nip_loan_payoff_bg_color))
									{{$nip_loan_payoff_bg_color}};
											width:100%;
									@elseif(round(($NIPLoanPayoffDonutChart*100)/(($nipLoanPayoffMax - $NIPLoanPayoffAdvanceDonutChart) + .0001)) <= 0)
											none;
									@else
											#B2912F;
									@endif
											">NIP LOAN PAYOFF ( {{money_format('%-8n', $NIPLoanPayoffDonutChart)}} OF {{money_format('%-8n',($nipLoanPayoffMax - $NIPLoanPayoffAdvanceDonutChart) )}}
										@if($nipLoanPayoffShowRelativeMaxIcon == 1 || $nip_loan_payoff_advance_bar == 1)
											<sup><span class='a-info-circle'></span></sup>
											@endif
											@if($minimumRules['nip'])
											@if($NIPLoanPayoffDonutChart + $NIPLoanPayoffAdvanceDonutChart > $minimumRules['nip']->amount)
												<?php $showDocDisclaimer = 1; ?>
												<sup><span class="a-file-left"></span></sup>

										@endif
										@endif
                                            )
									</div>
								</div>
							</li>

							@if($pre_demo_advance_bar == 1)
								<li onclick="dynamicModalLoad('expense-categories-details/0/3/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#pre-demo-advance-aggregates').toggleClass('pre-demo-advance-aggregate-hovered');" onmouseout="$('#pre-demo-advance-aggregates').toggleClass('pre-demo-advance-aggregate-hovered');">
									<div  id="pre-demo-advance-bar" class="uk-progress uk-text-center" >
										<div  class="uk-progress-bar @if(isset($pre_demo_advance_bg_color)) attention @endif" style="width: {{round(($PreDemoAdvanceDonutChart*100)/($preDemoMaxAdvance + .0001))}}%; margin:0px;
												background:
										@if(isset($pre_demo_advance_bg_color))
										{{$pre_demo_advance_bg_color}};
												width:100%;
										@elseif(round(($PreDemoAdvanceDonutChart*100)/($preDemoMaxAdvance + .0001)) == 0)
												none;
										@else
												#5a7c92;
										@endif
												">PRE-DEMO ADVANCE ( {{money_format('%-8n', $PreDemoAdvanceDonutChart)}} OF {{money_format('%-8n',$preDemoMaxAdvance)}}
											@if($preDemoAdvanceShowRelativeMaxIcon == 1)
												<sup><span class='a-info-circle'></span></sup>
												@endif
                                                )</div>
									</div>
								</li>
								@endif
								<li onclick="dynamicModalLoad('expense-categories-details/0/3/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#pre-demo-aggregates').toggleClass('pre-demo-aggregate-hovered');" onmouseout="$('#pre-demo-aggregates').toggleClass('pre-demo-aggregate-hovered');"><div class="uk-progress">
										<div id="pre-demo-bar" class="uk-progress-bar @if(isset($pre_demo_bg_color)) attention @endif" style="width: {{round(($PreDemoDonutChart*100)/(($preDemoMax - $PreDemoAdvanceDonutChart) + .0001))}}%; margin:0px; background:
										@if(isset($pre_demo_bg_color))
										{{$pre_demo_bg_color}};
												width:100%;
										@elseif(round(($PreDemoDonutChart*100)/(($preDemoMax - $PreDemoAdvanceDonutChart) + .0001)) == 0)
												none;
										@else
												#4D4D4D;
										@endif
												">PRE-DEMO ( {{money_format('%-8n', $PreDemoDonutChart)}} of {{money_format('%-8n',($preDemoMax - $PreDemoAdvanceDonutChart) )}}
											@if($preDemoShowRelativeMaxIcon == 1 || $pre_demo_advance_bar == 1)
												<sup><span class='a-info-circle'></span></sup>
												@endif
												@if($minimumRules['pre_demo'])
												@if($PreDemoDonutChart + $PreDemoAdvanceDonutChart > $minimumRules['pre_demo']->amount)
													<?php $showDocDisclaimer = 1; ?>
													<sup><span class="a-file-left"></span></sup>
											@endif
											@endif
                                                )
										</div>
									</div>
								</li>

								@if($demolition_advance_bar == 1)
									<li onclick="dynamicModalLoad('expense-categories-details/0/4/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#demo-advance-aggregates').toggleClass('demo-advance-aggregate-hovered');" onmouseout="$('#demo-advance-aggregates').toggleClass('demo-advance-aggregate-hovered');">
										<div  id="demolition-advance-bar" class="uk-progress uk-text-center" >
											<div  class="uk-progress-bar @if(isset($demolition_advance_bg_color)) attention @endif" style="width: {{round(($DemolitionAdvanceDonutChart*100)/($demolitionMaxAdvance  + .0001))}}%; margin:0px;
													background:
											@if(isset($demolition_advance_bg_color))
											{{$demolition_advance_bg_color}};
													width:100%;
											@elseif(round(($DemolitionAdvanceDonutChart*100)/($demolitionMaxAdvance + .0001)) == 0)
													none;
											@else
													#5a7c92;
											@endif
													">DEMOLITION ADVANCE ( {{money_format('%-8n', $DemolitionAdvanceDonutChart)}} OF {{money_format('%-8n',$demolitionMaxAdvance)}}
												@if($demolitionAdvanceShowRelativeMaxIcon == 1)
													<sup><span class='a-info-circle'></span></sup>
													@endif
                                                    )</div>
										</div>
									</li>
									@endif
									<li onclick="dynamicModalLoad('expense-categories-details/0/4/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#demo-aggregates').toggleClass('demo-aggregate-hovered');" onmouseout="$('#demo-aggregates').toggleClass('demo-aggregate-hovered');">
										<div class="uk-progress">
											<div class="uk-progress-bar @if(isset($demolition_bg_color)) attention @endif" style="width: {{round(($DemolitionDonutChart*100)/(($DemolitionDonutChart + $RemainingBalanceDonutChart) + .0001))}}%; margin:0px; background:
											@if(isset($demolition_bg_color))
											{{$demolition_bg_color}};
													width:100%;
											@elseif(round(($DemolitionDonutChart*100)/(($DemolitionDonutChart + $RemainingBalanceDonutChart) + .0001)) == 0)
													none;
											@else
													#5DA5DA;
											@endif
													">DEMOLITION ( {{money_format('%-8n', $DemolitionDonutChart)}} of {{money_format('%-8n',($demolitionMax - $DemolitionAdvanceDonutChart) )}}
												@if($demolitionShowRelativeMaxIcon == 1 || $demolition_advance_bar == 1)
													<sup><span class='a-info-circle'></span></sup>
													@endif
													@if($minimumRules['demolition'])
													@if($DemolitionDonutChart + $DemolitionAdvanceDonutChart > $minimumRules['demolition']->amount)
														<?php $showDocDisclaimer = 1; ?>
														<sup><span class="a-file-left"></span></sup>
												@endif
												@endif

                                                     )
											</div>
										</div>
									</li>

									@if($greening_advance_bar == 1)
										<li onclick="dynamicModalLoad('expense-categories-details/0/5/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#greening-advance-aggregates').toggleClass('greening-advance-aggregate-hovered');" onmouseout="$('#greening-advance-aggregates').toggleClass('greening-advance-aggregate-hovered');">
											<div class="uk-progress">
												<div class="uk-progress-bar @if(isset($greening_advance_bg_color)) attention @endif" style="width: {{round(($GreeningAdvanceDonutChart*100)/($greeningMaxAdvance + .0001))}}%; margin:0px; background:
												@if(isset($greening_advance_bg_color))
												{{$greening_advance_bg_color}};
														width:100%;
												@elseif(round(($GreeningAdvanceDonutChart*100)/($greeningMaxAdvance + .0001))==0)
														none;
												@else
														#8bad8e
												@endif
														">
													GREENING ADVANCE ( {{money_format('%-8n', $GreeningAdvanceDonutChart)}} of {{money_format('%-8n',$greeningMaxAdvance)}}
													@if($greeningAdvanceShowRelativeMaxIcon == 1)
														<sup><span class='a-info-circle'></span></sup>
														@endif
                                                        )

												</div>
											</div>
										</li>
										@endif

										<li onclick="dynamicModalLoad('expense-categories-details/0/5/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#greening-aggregates').toggleClass('greening-aggregate-hovered');" onmouseout="$('#greening-aggregates').toggleClass('greening-aggregate-hovered');">
											<div class="uk-progress">
												<div class="uk-progress-bar @if(isset($greening_bg_color)) attention @endif" style="width: {{round(($GreeningDonutChart*100)/(($parcelData->greening_max - $GreeningAdvanceDonutChart) + .0001))}}%; margin:0px; background:
												@if(isset($greening_bg_color))
												{{$greening_bg_color}};
														width:100%;
												@elseif(round(($GreeningDonutChart*100)/(($parcelData->greening_max - $GreeningAdvanceDonutChart) + .0001))==0)
														none;
												@else
														#60BD68;
												@endif
														">
													GREENING  ( {{money_format('%-8n', $GreeningDonutChart)}} of {{money_format('%-8n',($greeningMax - $GreeningAdvanceDonutChart) )}}
													@if($greeningShowRelativeMaxIcon == 1 || $greening_advance_bar == 1)
														<sup><span class='a-info-circle'></span></sup>
														@endif
														@if($minimumRules['greening'])
														@if($GreeningDonutChart + $GreeningAdvanceDonutChart > $minimumRules['greening']->amount)
															<?php $showDocDisclaimer = 1; ?>
															<sup><span class="a-file-left"></span></sup>
													@endif
													@endif

                                                        )
												</div>
											</div>
										</li>

										@if($maintenance_advance_bar == 1)
											<li onclick="dynamicModalLoad('expense-categories-details/0/6/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#maintenance-advance-aggregates').toggleClass('maintenance-advance-aggregate-hovered');" onmouseout="$('#maintenance-advance-aggregates').toggleClass('maintenance-advance-aggregate-hovered');">
												<div  id="maintenance-advance-bar" class="uk-progress uk-text-center" >
													<div  class="uk-progress-bar @if(isset($maintenance_advance_bg_color)) attention @endif" style="width: {{round(($MaintenanceAdvanceDonutChart*100)/($maintenanceMaxAdvance + .0001))}}%; margin:0px;
															background:
													@if(isset($maintenance_advance_bg_color))
													{{$maintenance_advance_bg_color}};
															width:100%;
													@elseif(round(($MaintenanceAdvanceDonutChart*100)/($maintenanceMaxAdvance + .0001)) == 0)
															none;
													@else
															#d0b18a;
													@endif
															">MAINTENANCE ADVANCE ( {{money_format('%-8n', $MaintenanceAdvanceDonutChart)}} OF {{money_format('%-8n',$maintenanceMaxAdvance)}}
														@if($maintenanceAdvanceShowRelativeMaxIcon == 1)
															<sup><span class='a-info-circle'></span></sup>
															@endif
                                                           )
													</div>
												</div>
											</li>
											@endif
											<li onclick="dynamicModalLoad('expense-categories-details/0/6/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#maintenance-aggregates').toggleClass('maintenance-aggregate-hovered');" onmouseout="$('#maintenance-aggregates').toggleClass('maintenance-aggregate-hovered');">
												<div class="uk-progress">
													<div class="uk-progress-bar @if(isset($maintenance_bg_color)) attention @endif" style="width: {{round(($MaintenanceDonutChart*100)/100)}}%; margin:0px;
															background:
													@if(isset($maintenance_bg_color))
													{{$maintenance_bg_color}};
															width:100%;
													@elseif(round(($MaintenanceDonutChart*100)/($maintenanceMax + .0001)) == 0)
															none;
													@else
															#FAA43A ;
													@endif

															">MAINTENANCE ( {{money_format('%-8n', $MaintenanceDonutChart)}} of {{money_format('%-8n',($maintenanceMax - $MaintenanceAdvanceDonutChart) )}}
														@if($maintenanceShowRelativeMaxIcon == 1 || $maintenance_advance_bar == 1)
															<sup><span class="a-info-circle"></span></sup>
															@endif
															@if($minimumRules['maintenance'])
															@if($MaintenanceDonutChart + $MaintenanceAdvanceDonutChart > $minimumRules['maintenance']->amount)
																<?php $showDocDisclaimer = 1; ?>
																<sup><span class="a-file-left"></span></sup>
															@endif
															@endif

                                                              )
													</div>
												</div>
											</li>

											@if($administration_advance_bar == 1)
												<li onclick="dynamicModalLoad('expense-categories-details/0/7/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#admin-advance-aggregates').toggleClass('admin-advance-aggregate-hovered');" onmouseout="$('#admin-advance-aggregates').toggleClass('admin-advance-aggregate-hovered');">
													<div  id="administration-advance-bar" class="uk-progress uk-text-center" >
														<div  class="uk-progress-bar @if(isset($admin_advance_bg_color)) attention @endif" style="width: {{round(($AdminAdvanceDonutChart*100)/($administrationMaxAdvance + .0001))}}%; margin:0px;
																background:
														@if(isset($admin_advance_bg_color))
														{{$admin_advance_bg_color}};
																width:100%;
														@elseif(round(($AdminAdvanceDonutChart*100)/($administrationMaxAdvance + .0001)) == 0)
																none;
														@else
																#d0b18a;
														@endif
																">ADMIN ADVANCE ( {{money_format('%-8n', $AdminAdvanceDonutChart)}} OF {{money_format('%-8n',$administrationMaxAdvance)}}
															@if($administrationAdvanceShowRelativeMaxIcon == 1)
																<sup><span class="a-info-circle"></span></sup>
																@endif
                                                                )
														</div>
													</div>
												</li>
												@endif
												<li onclick="dynamicModalLoad('expense-categories-details/0/7/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#admin-aggregates').toggleClass('admin-aggregate-hovered');" onmouseout="$('#admin-aggregates').toggleClass('admin-aggregate-hovered');">
													<div class="uk-progress">
														<div class="uk-progress-bar @if($totalPossibleAdmin - ($AdminAdvanceDonutChart + $AdminDonutChart) < 0) attention @endif" style="width: {{round(($AdminDonutChart*100)/($totalPossibleAdmin + .0001))}}%; margin:0px; background:
														@if($totalPossibleAdmin - ($AdminAdvanceDonutChart + $AdminDonutChart) < 0 )
																red;
																width:100%;
														@elseif(round(($AdminDonutChart*100)/($totalPossibleAdmin + .0001))==0)
																none;
														@else
																#F17CB0;
														@endif
																">
															<?php
															$adminBalance = $totalPossibleAdmin - $AdminAdvanceDonutChart;
															if($adminBalance < 0){
															$adminBalance = 0;

															}
															?>
															ADMIN ( {{money_format('%-8n', $AdminDonutChart)}} of {{money_format('%-8n', ($adminBalance))}}

															@if($administration_advance_bar == 1)
																<sup><span class="a-info-circle"></span></sup>
																@endif
																@if($minimumRules['administration'])
																@if($AdminDonutChart + $AdminAdvanceDonutChart > $minimumRules['administration']->amount)
																	<?php $showDocDisclaimer = 1; ?>
																	<sup><span class="a-file-left"></span></sup>
																@endif
																@endif
                                                                )
														</div>
													</div>
												</li>

												@if($other_advance_bar == 1)
													<li onclick="dynamicModalLoad('expense-categories-details/0/8/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#other-advance-aggregates').toggleClass('other-advance-aggregate-hovered');" onmouseout="$('#other-advance-aggregates').toggleClass('other-advance-aggregate-hovered');">
														<div  id="other-advance-bar" class="uk-progress uk-text-center" >
															<div  class="uk-progress-bar @if(isset($other_advance_bg_color)) attention @endif" style="width: {{round(($OtherAdvanceDonutChart*100)/($otherMaxAdvance + .0001))}}%; margin:0px;
																	background:
															@if(isset($other_advance_bg_color))
															{{$other_advance_bg_color}};
																	width:100%;
															@elseif(round(($OtherAdvanceDonutChart*100)/($otherMaxAdvance + .0001)) == 0)
																	none;
															@else
																	#d0b18a;
															@endif
																	">OTHER ADVANCE ( {{money_format('%-8n', $OtherAdvanceDonutChart)}} OF {{money_format('%-8n',$otherMaxAdvance)}}
																@if($otherAdvanceShowRelativeMaxIcon == 1)
																	<sup><span class="a-info-circle"></span></sup>
																	@endif
                                                                    )
															</div>
														</div>
													</li>
													@endif
													<li onclick="dynamicModalLoad('expense-categories-details/0/8/{{$parcel->program_id}}/{{$parcel->id}}')" onmouseover="$('#other-aggregates').toggleClass('other-aggregate-hovered');" onmouseout="$('#other-aggregates').toggleClass('other-aggregate-hovered');"><div class="uk-progress">
															<div class="uk-progress-bar @if(isset($other_bg_color)) attention @endif" style="width: {{round(($OtherDonutChart*100)/($otherMax + .0001))}}%; margin:0px;
																	background:
															@if(isset($other_bg_color))
															{{$other_bg_color}};
																	width:100%;
															@elseif(round(($OtherDonutChart*100)/($otherMax + .0001)) == 0)
																	none;
															@else
																	#B276B2
															@endif
																	;

																	">OTHER ( {{money_format('%-8n', $OtherDonutChart)}} of {{money_format('%-8n', ($otherMax-$OtherAdvanceDonutChart))}}
																@if($otherShowRelativeMaxIcon == 1 || $other_advance_bar == 1)
																	<sup><span class="a-info-circle"></span></sup>
																	@endif
																	@if($minimumRules['other'])
																	@if($OtherDonutChart + $OtherAdvanceDonutChart > $minimumRules['other']->amount)
																		<?php $showDocDisclaimer = 1; ?>
																		<sup><span class="a-file-left"></span></sup>
																@endif
																@endif
                                                                     )</div></li>
				</ul>
				<p class=" uk-margin-bottom-remove uk-text-center"><small><sup><span class="a-info-circle"></span></sup> MAXIMUM AMOUNT AVAILABLE IF ALL OTHERS REMAIN THE SAME. &nbsp;
						@if($showDocDisclaimer == 1)
							<sup><span class="a-file-left"></span></sup>&nbsp;REQUIRES DOCUMENTATION (SEE DOCUMENTS TAB).
						@endif
					</small></p>
			</div>
		</div>
	</div>
	<div class="uk-width-1-1 uk-width-1-2@m" >
		<div class="uk-overflow-container">
			<table class="uk-table uk-table-hover uk-table-condensed uk-margin-bottom-large" >
				<THEAD>
				<tr>
					<th width="1">
					</th>
					<th class="uk-text-right ">
						<small>COST</small>
					</th>
					<th class="uk-text-right"><small>REQUESTED</small></th>
					<TH class="uk-text-right"><small>APPROVED</small></TH>
					<th class="uk-text-right"><small>INVOICED</small></th>

				</tr>
				</THEAD>
				<tbody>
				@if($acquisition_advance_bar == 1)
					<tr id="acquisition-advance-aggregates">
						<td style="background: #5a7c92" uk-tooltip="ACQUISITION ADVANCE">

						</td>
						<td class="uk-text-right cost-column">
							{{money_format('%-8n', $parcelData->AcquisitionAdvanceCost)}}
						</td>
						<td class="uk-text-right requested-column">
							{{money_format('%-8n', $parcelData->AcquisitionAdvanceRequested)}}
						</td>
						<td class="uk-text-right approved-column">
							{{money_format('%-8n', $parcelData->AcquisitionAdvanceApproved)}}
						</td>
						<td class="uk-text-right invoiced-column">
							{{money_format('%-8n', $parcelData->AcquisitionAdvanceInvoiced)}}
						</td>
					</tr>
					@endif
					<tr id="acquisition-aggregates">
						<td style="background: #005186" uk-tooltip="ACQUISITION">

						</td>
						<td class="uk-text-right cost-column">
							{{money_format('%-8n', $parcelData->AcquisitionCost)}}
						</td>
						<td class="uk-text-right requested-column">
							{{money_format('%-8n', $parcelData->AcquisitionRequested)}}
						</td>
						<td class="uk-text-right approved-column">
							{{money_format('%-8n', $parcelData->AcquisitionApproved)}}
						</td>
						<td class="uk-text-right invoiced-column">
							{{money_format('%-8n', $parcelData->AcquisitionInvoiced)}}
						</td>
					</tr>
					@if($nip_loan_payoff_advance_bar == 1)
						<tr id="nip-loan-payoff-advance-aggregates">
							<td style="background: #bfb28c" uk-tooltip="NIP LOAN PAYOFF ADVANCE">

							</td>
							<td class="uk-text-right cost-column">
								{{money_format('%-8n', $parcelData->NIPLoanAdvanceCost)}}
							</td>
							<td class="uk-text-right requested-column">
								{{money_format('%-8n', $parcelData->NIPLoanAdvanceRequested)}}
							</td>
							<td class="uk-text-right approved-column">
								{{money_format('%-8n', $parcelData->NIPLoanAdvanceApproved)}}
							</td>
							<td class="uk-text-right invoiced-column">
								{{money_format('%-8n', $parcelData->NIPLoanAdvanceInvoiced)}}
							</td>
						</tr>
					@endif
					<tr id="nip-loan-payoff-aggregates">
						<td style="background: #B2912F" uk-tooltip="NIP LOAN PAYOFF">

						</td>
						<td class="uk-text-right cost-column">
							{{money_format('%-8n', $parcelData->NIPLoanCost)}}
						</td>
						<td class="uk-text-right requested-column">
							{{money_format('%-8n', $parcelData->NIPLoanRequested)}}
						</td>
						<td class="uk-text-right approved-column">
							{{money_format('%-8n', $parcelData->NIPLoanApproved)}}
						</td>
						<td class="uk-text-right invoiced-column">
							{{money_format('%-8n', $parcelData->NIPLoanInvoiced)}}
						</td>
					</tr>

					@if($pre_demo_advance_bar == 1)
						<tr id="pre-demo-advance-aggregates">
							<td style="background: #696868" uk-tooltip="PRE-DEMO ADVANCE">

							</td>
							<td class="uk-text-right cost-column">
								{{money_format('%-8n', $parcelData->PreDemoAdvanceCost)}}
							</td>
							<td class="uk-text-right requested-column">
								{{money_format('%-8n', $parcelData->PreDemoAdvanceRequested)}}
							</td>
							<td class="uk-text-right approved-column">
								{{money_format('%-8n', $parcelData->PreDemoAdvanceApproved)}}
							</td>
							<td class="uk-text-right invoiced-column">
								{{money_format('%-8n', $parcelData->PreDemoAdvanceInvoiced)}}
							</td>
						</tr>
						@endif
						<tr id="pre-demo-aggregates">
							<td style="background: #4D4D4D" uk-tooltip="PRE-DEMO">

							</td>
							<td class="uk-text-right cost-column">
								{{money_format('%-8n', $parcelData->PreDemoCost)}}
							</td>
							<td class="uk-text-right requested-column">
								{{money_format('%-8n', $parcelData->PreDemoRequested)}}
							</td>
							<td class="uk-text-right approved-column">
								{{money_format('%-8n', $parcelData->PreDemoApproved)}}
							</td>
							<td class="uk-text-right invoiced-column">
								{{money_format('%-8n', $parcelData->PreDemoInvoiced)}}
							</td>
						</tr>

						@if($demolition_advance_bar == 1)
							<tr id="demo-advance-aggregates">
								<td style="background: #adc7da" uk-tooltip="DEMOLITION ADVANCE">

								</td>
								<td class="uk-text-right cost-column">
									{{money_format('%-8n', $parcelData->DemolitionAdvanceCost)}}
								</td>
								<td class="uk-text-right requested-column">
									{{money_format('%-8n', $parcelData->DemolitionAdvanceRequested)}}
								</td>
								<td class="uk-text-right approved-column">
									{{money_format('%-8n', $parcelData->DemolitionAdvanceApproved)}}
								</td>
								<td class="uk-text-right invoiced-column">
									{{money_format('%-8n', $parcelData->DemolitionAdvanceInvoiced)}}
								</td>
							</tr>
						@endif
						<tr id="demo-aggregates">
							<td style="background: #5DA5DA" uk-tooltip="DEMOLITION">

							</td>
							<td class="uk-text-right cost-column">
								{{money_format('%-8n', $parcelData->DemolitionCost)}}
							</td>
							<td class="uk-text-right requested-column">
								{{money_format('%-8n', $parcelData->DemolitionRequested)}}
							</td>
							<td class="uk-text-right approved-column">
								{{money_format('%-8n', $parcelData->DemolitionApproved)}}
							</td>
							<td class="uk-text-right invoiced-column">
								{{money_format('%-8n', $parcelData->DemolitionInvoiced)}}
							</td>
						</tr>

						@if($greening_advance_bar == 1)
							<tr id="greening-advance-aggregates">
								<td style="background: #8bad8e" uk-tooltip="GREENING ADVANCE">

								</td>
								<td class="uk-text-right cost-column">
									{{money_format('%-8n', $parcelData->GreeningAdvanceCost)}}
								</td>
								<td class="uk-text-right requested-column">
									{{money_format('%-8n', $parcelData->GreeningAdvanceRequested)}}
								</td>
								<td class="uk-text-right approved-column">
									{{money_format('%-8n', $parcelData->GreeningAdvanceApproved)}}
								</td>
								<td class="uk-text-right invoiced-column">
									{{money_format('%-8n', $parcelData->GreeningAdvanceInvoiced)}}
								</td>
							</tr>
							@endif
							<tr id="greening-aggregates">
								<td style="background: #60BD68" uk-tooltip="GREENING">

								</td>
								<td class="uk-text-right cost-column">
									{{money_format('%-8n', $parcelData->GreeningCost)}}
								</td>
								<td class="uk-text-right requested-column">
									{{money_format('%-8n', $parcelData->GreeningRequested)}}
								</td>
								<td class="uk-text-right approved-column">
									{{money_format('%-8n', $parcelData->GreeningApproved)}}
								</td>
								<td class="uk-text-right invoiced-column">
									{{money_format('%-8n', $parcelData->GreeningInvoiced)}}
								</td>
							</tr>

							@if($maintenance_advance_bar == 1)
								<tr id="maintenance-advance-aggregates">
									<td style="background: #d0b18a" uk-tooltip="MAINTENANCE ADVANCE">

									</td>
									<td class="uk-text-right cost-column">
										{{money_format('%-8n', $parcelData->MaintenanceAdvanceCost)}}
									</td>
									<td class="uk-text-right requested-column">
										{{money_format('%-8n', $parcelData->MaintenanceAdvanceRequested)}}
									</td>
									<td class="uk-text-right approved-column">
										{{money_format('%-8n', $parcelData->MaintenanceAdvanceApproved)}}
									</td>
									<td class="uk-text-right invoiced-column">
										{{money_format('%-8n', $parcelData->MaintenanceAdvanceInvoiced)}}
									</td>
								</tr>
								@endif
								<tr id="maintenance-aggregates">
									<td style="background: #FAA43A" uk-tooltip="MAINTENANCE">

									</td>
									<td class="uk-text-right cost-column">
										{{money_format('%-8n', $parcelData->MaintenanceCost)}}
									</td>
									<td class="uk-text-right requested-column">
										{{money_format('%-8n', $parcelData->MaintenanceRequested)}}
									</td>
									<td class="uk-text-right approved-column">
										{{money_format('%-8n', $parcelData->MaintenanceApproved)}}
									</td>
									<td class="uk-text-right invoiced-column">
										{{money_format('%-8n', $parcelData->MaintenanceInvoiced)}}
									</td>
								</tr>

								@if($administration_advance_bar == 1)
									<tr id="admin-advance-aggregates">
										<td style="background: #ea9aa7" uk-tooltip="ADMIN ADVANCE">

										</td>
										<td class="uk-text-right cost-column">
											{{money_format('%-8n', $parcelData->AdministrationAdvanceCost)}}
										</td>
										<td class="uk-text-right requested-column">
											{{money_format('%-8n', $parcelData->AdministrationAdvanceRequested)}}
										</td>
										<td class="uk-text-right approved-column">
											{{money_format('%-8n', $parcelData->AdministrationAdvanceApproved)}}
										</td>
										<td class="uk-text-right invoiced-column">
											{{money_format('%-8n', $parcelData->AdministrationAdvanceInvoiced)}}
										</td>
									</tr>
								@endif
								<tr id="admin-aggregates">
									<td style="background: #F17CB0" uk-tooltip="ADMIN">

									</td>
									<td class="uk-text-right cost-column">
										{{money_format('%-8n', $parcelData->AdministrationCost)}}
									</td>
									<td class="uk-text-right requested-column">
										{{money_format('%-8n', $parcelData->AdministrationRequested)}}
									</td>
									<td class="uk-text-right approved-column">
										{{money_format('%-8n', $parcelData->AdministrationApproved)}}
									</td>
									<td class="uk-text-right invoiced-column">
										{{money_format('%-8n', $parcelData->AdministrationInvoiced)}}
									</td>
								</tr>

								@if($other_advance_bar == 1)
									<tr id="other-advance-aggregates">
										<td style="background: #c3b0c3" uk-tooltip="OTHER ADVANCE">

										</td>
										<td class="uk-text-right cost-column">
											{{money_format('%-8n', $parcelData->OtherAdvanceCost)}}
										</td>
										<td class="uk-text-right requested-column">
											{{money_format('%-8n', $parcelData->OtherAdvanceRequested)}}
										</td>
										<td class="uk-text-right approved-column">
											{{money_format('%-8n', $parcelData->OtherAdvanceApproved)}}
										</td>
										<td class="uk-text-right invoiced-column">
											{{money_format('%-8n', $parcelData->OtherAdvanceInvoiced)}}
										</td>
									</tr>
									@endif
									<tr id="other-aggregates">
										<td style="background: #B276B2" uk-tooltip="OTHER">

										</td>
										<td class="uk-text-right cost-column">
											{{money_format('%-8n', $parcelData->OtherCost)}}
										</td>
										<td class="uk-text-right requested-column">
											{{money_format('%-8n', $parcelData->OtherRequested)}}
										</td>
										<td class="uk-text-right approved-column">
											{{money_format('%-8n', $parcelData->OtherApproved)}}
										</td>
										<td class="uk-text-right invoiced-column">
											{{money_format('%-8n', $parcelData->OtherInvoiced)}}
										</td>
									</tr>



									<tr >
										<td uk-tooltip="TOTAL" style="border-bottom: none; padding-top: 9px; background: white;">

										</td>
										<td class="uk-text-right" style="border-bottom: 2px black solid;padding-top: 9px;" >
											<strong ><a onmouseover="$('.cost-column').css('background','#f9f9f9').css('color','black');" onmouseout="$('.cost-column').css('background','').css('color','#7f7f7f');">{{money_format('%-8n', $parcelData->total_cost)}}</a></strong>
										</td>
										<td class="uk-text-right" style="border-bottom: 2px black solid;padding-top: 9px;">
											<strong ><a onclick="loadDetailTab('/request/','{{$parcelData->req_id}}','2',0,0)" uk-tooltip="VIEW REQUEST {{$parcelData->req_id}}" onmouseover="$('.requested-column').css('background','#f9f9f9').css('color','black');" onmouseout="$('.requested-column').css('background','').css('color','#7f7f7f');">{{money_format('%-8n', $parcelData->total_requested)}}</a></strong>
										</td>
										<td class="uk-text-right" style="border-bottom: 2px black solid;padding-top: 9px;">
											<strong ><a onclick="loadDetailTab('/po/','{{$parcelData->po_id}}','3',0,0)" uk-tooltip="VIEW PURCHASE ORDER {{$parcelData->po_id}}" onmouseover="$('.approved-column').css('background','#f9f9f9').css('color','black');" onmouseout="$('.approved-column').css('background','').css('color','#7f7f7f');">{{money_format('%-8n', $parcelData->total_approved)}}</a></strong>
										</td>
										<td class="uk-text-right" style="border-bottom: 2px black solid;padding-top: 9px;">
											<strong ><a onclick="loadDetailTab('/invoice/','{{$parcelData->invoice_id}}','4',0,0)" uk-tooltip="VIEW INVOICE {{$parcelData->invoice_id}}" onmouseover="$('.invoiced-column').css('background','#f9f9f9').css('color','black');" onmouseout="$('.invoiced-column').css('background','').css('color','#7f7f7f');">{{money_format('%-8n', $parcelData->total_invoiced)}}</a></strong>
										</td>
									</tr>
				</tbody>
			</table>
			@if(strtotime($parcel->created_at) < strtotime('April 8, 2016') )
			<hr /><a href="http://dynamo.solutions/ohfa_sample/?id={{ str_replace(' ', '',str_replace('-','',$parcelData->parcel_name)) }}" target="_blank" class="uk-link-muted uk-button uk-button-default uk-width-1-1 ">
				<span class="a-chart-bar-v"></span> VIEW IMPACT REPORT BY DYNAMO METRICS (BETA)</a>

				
			<hr />
			@endif
		</div>

	</div>
</div>

@elseIf($parcel->lb_validated != 1)
<div class="uk-width-1-1 uk-margin-large-top">
<h2 class="uk-text-center">This Parcel Needs Validated.</h2>
 @if(isset($parcel->importId->import_id))
	<a href="/validate_parcels?import_id={{$parcel->importId->import_id}}&resetValidation=1" class="uk-button uk-button-success uk-button-large uk-align-center uk-width-1-2@m uk-margin-large-bottom">
		Finish Validating Import {{$parcel->importId->import_id}}
	</a>
@else
	<a>I am unable to determine this parcel's import</a>
@endif
</div> 
@else 
<div class="uk-width-1-1 uk-margin-large-top">
	@if($parcelData->landbank_property_status_id != 48 && $parcelData->landbank_property_status_id != 11)
	<h2 class="uk-text-center">I'm ready for you to enter your actual costs.</h2>
	<a onclick="dynamicModalLoad('cost/{{ $parcel->id }}/add');" class="uk-button uk-button-success uk-button-large uk-align-center uk-width-1-2@m uk-margin-large-bottom">ENTER COSTS</a>
	@else
	<h2 class="uk-text-center">This Parcel Has Been Withdrawn.</h2>
	<div class="uk-grid uk-block">
		<div class="uk-width-1-2@m uk-align-center">
		<p>Because it was withdrawn, it will need to go through the entire validation process again before you can request a reimbursement again. Costs, notes, supporting documents, communications, and history were all retained. </p><p>All other items have been removed.</p>
		<p>If you'd like to start processing this parcel again, please click the button below to get started.</p>

		<a href="/change_to_validate/{{$parcel->id}}" class="uk-button uk-button-success uk-button-large uk-align-center uk-width-1-2@m">START RESUBMISSION</a>
	</div>
	@endif
</div>

@endIf
<hr >
<div class="uk-grid  uk-grid-divider">
	<div class="uk-width-1-1 uk-width-1-2@m">
		<div class="uk-grid">
			<div class="uk-width-1-3@s uk-margin-large-bottom">
				<figure class="uk-overlay uk-overlay-hover uk-align-center">
					<img src="https://maps.googleapis.com/maps/api/streetview?size=400x400&location={{$parcel->latitude}},{{$parcel->longitude}}
							&fov=90&pitch=10
                            &key=AIzaSyAMB5fHlZyAet2TnsuU3bBX7miYyDMBLSg" class="uk-overlay-scale uk-align-center">
					<figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">OPEN IN STREET VIEW?</figcaption>
					<a class="uk-position-cover uk-align-center" target="_blank" href="http://maps.google.com/maps?q=&layer=c&cbll={{$parcel->latitude}},{{$parcel->longitude}}&cbp=11,0,0,0,0"></a>
				</figure>
				<p align="center"><label><input type="checkbox" value="1" name="match" @if($parcelData->matches_street_view == 1) CHECKED @endif onclick="matchesStreetView({{$parcelData->allita_system_id}});"> MATCHES STREET VIEW</label></p>

			</div>

			<div class="uk-width-1-3@s uk-margin-large-bottom"><figure class="uk-overlay uk-overlay-hover uk-align-center">
					<!-- <img src="/images/support_images/no-image.jpg" class="uk-overlay-scale uk-align-center"> -->
					<!-- <figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">Upload a New Image?</figcaption> -->
					<!-- <a class="uk-position-cover uk-align-center" onclick="dynamicModalLoad('/')"></a> -->
				</figure><p align="center"><label><input type="checkbox" value="1" name="ugly_house" <?php if($parcel->ugly_house == 1) { echo 'CHECKED="TRUE"'; } ?> onclick="toggleUgly({{$parcelData->allita_system_id}});"> USE AS SAMPLE BEFORE</label></p></div>

			<div class="uk-width-1-3@s uk-margin-large-bottom uk-align-center"><figure class="uk-overlay uk-overlay-hover uk-align-center">
					<!-- <img src="/images/support_images/no-image.jpg" class="uk-overlay-scale uk-align-center"> -->
					<!-- <figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">Upload a New Image?</figcaption>
					<a class="uk-position-cover uk-align-center" onclick="dynamicModalLoad('/')"></a> -->
				</figure><p align="center"><label><input type="checkbox" value="1" name="pretty_house" <?php if($parcel->pretty_lot == 1) { echo 'CHECKED="TRUE"'; } ?> onclick="togglePretty({{$parcelData->allita_system_id}});"> USE AS SAMPLE AFTER</label></p></div>
		</div>
	</div>
	<div class="uk-width-1-1 uk-width-1-4@m">
		@if(strlen($parcel->sf_parcel_id) > 1)
		<div class="uk-width-1-1 uk-label" >LEGACY PARCEL || SFID:{{$parcel->sf_parcel_id}}</div>
		@endif
		<hr class="uk-visible@s uk-hidden@m">
		<h2>PARCEL: <span uk-tooltip="System ID: {{$parcel->id}} @if(isset($parcel->importId->import_id)) , a parcel in import # {{$parcel->importId->import_id}} @endif .">{{$parcelData->parcel_name}}</span></h2>

		<div uk-grid class="uk-grid-medium">
			<div class="uk-width-4-5">
				<p>{{$parcel->street_address}}<br />
					{{$parcel->city}}, {{$parcelData->state_name}} {{$parcel->zip}}<br />
					{{$parcelData->county_name}} County<br />
				<hr class="dashed-hr">
				<div class="uk-margin-top uk-align-left @if(!$within_unit_limits) attention red @endif" >{{$parcelData->units}} Unit<?php if($parcelData->units > 1) {echo "s";} ?></div>
				<hr class="dashed-hr">
				<div class="uk-margin-top uk-align-left" ><span class="a-location-flag uk-icon-justify" uk-tooltip="Target Area"></span> {{$parcelData->target_area_name}} </div>
				<hr class="dashed-hr">
				</p>
				<p>
					<span class="parcel-district-format">Sale Price: <span class="parcel-district-format-number">{{money_format('%-8n',$parcel->sale_price)}}</span></span><br />
					<span class="parcel-district-format">Acquired By: <span class="parcel-district-format-number">{{$parcelData->how_acquired}}
							@if($parcel->how_acquired_explanation != NULL && $parcel->how_acquired_explanation != 'NA')
								<span class="a-file-pencil_1" uk-tooltip="{{htmlspecialchars($parcel->how_acquired_explanation)}}"></span>
							@endif
					</span></span><br />



					<span class="parcel-district-format">OH House District: <span class="parcel-district-format-number">{{$parcel->oh_house_district}}</span></span><br />
					<span class="parcel-district-format">OH Senate District: <span class="parcel-district-format-number">{{$parcel->oh_senate_district}}</span></span><br />
					<span class="parcel-district-format">US House District: <span class="parcel-district-format-number"></span></span>
				</p>
				<label>@if($parcel->historic_significance_or_district == 1)
						<span class="a-warning"></span> Protected
					@else
						This is Not a
					@endif
					Historic Property</label><br /><a href="http://nr.ohpo.org/" class="uk-label uk-label-warning" target="_blank">CHECK THE DATABASE</a> 
				@if($parcel->historic_waiver_approved == 0 && $parcel->historic_significance_or_district == 1)
					<script>UIkit.modal.alert('Please take note! This parcel is a historic property and its approval to be demolished has not been received.');</script>
					<br /><span class="a-warning attention" style="color: red"></span> <span class="attention" style="color:red">NO WAIVER</span>
				@elseif($parcel->historic_waiver_approved == 1 && $parcel->historic_significance_or_district == 1)
					<br /><span class="a-checkbox-checked" style="color: green"></span> <span class="" style="color:green">Historic Waiver Approved</span>
				@endif
				@if($hasResolutions > 0)
				<hr class="dashed-hr uk-margin-bottom">
					<span class="a-checkbox-checked" style="color: green"></span> <span class="" style="color: green"><a onclick="resolve({{$parcelData->allita_system_id}},{{$parcel->latitude}},{{$parcel->longitude}});">Cleared Matches</a></span> 
					<script type="text/javascript">
						function resolve(parcel_id,lat,lon){
								dynamicModalLoad('resolve_validation/'+parcel_id+'?lat='+lat+'&lon='+lon);
								window.parcel_id = parcel_id;
							}
					</script>
				@endif

				@if(count($retainage)>0 || count($costs)>0)
					<hr class="dashed-hr uk-margin-bottom">
					<strong>Retainage:</strong>
					@if(count($retainage)>0)
						<ul>
						@forEach($retainage as $retained)
							<li>
								@if($retained->expense_category){{$retained->expense_category->expense_category_name}}@endif: @if($retained->vendor){{$retained->vendor->vendor_name}}@endif ${{$retained->retainage_amount}} 
								@if($retained->paid != 1)
								 | <a onclick=" pay_retainage({{$retained->id}})" uk-tooltip="Mark This Retainage Paid" class="uk-link-muted"><span class="a-checkbox"></span> UNPAID</a>
								@else
								 | <span class="a-checkbox-checked"></span> PAID 
								@endif
								@if(count($retained->documents))
								| <span uk-tooltip="Retainage Payment Documents uploaded"><span class="a-file-left"></span></span>
								@else
								| <span uk-tooltip="No Retainage Payment Documents uploaded yet" class="uk-link" onclick="loadParcelSubTab('documents',{{$parcel->id}});"><span class="a-file-tool"></span></span>
								@endif
								 | <a onclick=" remove_retainage({{$retained->id}})" uk-tooltip="Delete This Retainage." class="uk-link-muted"><span class="a-trash-4"></span></a>
							</li>
						@endForEach
						</ul>
					@endif
					@if(count($costs)>0)
						<a class="uk-button uk-button-small uk-width-1-1 retainage" uk-toggle="target: .retainage"><span class="a-circle-plus" ></span> ADD RETAINAGE</a>
						<form id="retainage" method="post" class="retainage" hidden>
						<div class="uk-form-row">
							<label for="cost_id">SELECT EXPENSE</label>
							<select name="cost_id" id="cost-id" style="font-size:10px;" class="uk-select">
								<option>Please Select an Expense</option>
								@foreach($costs as $cost)
								<option value="{{$cost->id}}">{{$cost->expense_category_name}}: {{$cost->vendor_name}} for ${{$cost->amount}}</option>
								@endforeach
							</select>
						</div>
						<div class="uk-form-row">
							<label for="retainage_amount">AMOUNT RETAINED</label>
							<input id="retainage-amount" name="retainage_amount" value="" placeholder="0.00" class="uk-input uk-width-1-1">
						</div>
						<div class="uk-form-row">
							<a class="uk-button uk-button-small" onclick="save_retainage()"><span class="a-circle-plus"></span> ADD RETAINAGE</a>
						</div>
						</form>
					@endif
					<script>
						function save_retainage(){
							
						        $.post('/parcels/retainage/store/{{$parcel->id}}', {
									'_token' : '{{ csrf_token() }}',
									'cost_id' : $('#cost-id').val(),
									'retainage_amount' : $('#retainage-amount').val(),
								}, function(data) {
									if(data['message']!='' && data['error']!=1){
										$('#parcel-subtab-1').trigger('click');
										UIkit.modal.alert(data['message']);
										
									}else if(data['message']!='' && data['error']==1){
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> <p>This is what I was told:</p><p>'+data['message']+'</p>');
										
									}else{
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> And unfortunately the server didn\'t give me a reason.<p>');
									}
								});
								
						}
						function remove_retainage(id){
							UIkit.modal.confirm("Are you sure you want to delete this retainage?").then(function() {
						        $.post('/parcels/retainage/remove/'+id, {
									'_token' : '{{ csrf_token() }}',
									'retainage_id' : id
								}, function(data) {
									if(data['message']!='' && data['error']!=1){
										$('#parcel-subtab-1').trigger('click');
										UIkit.modal.alert(data['message']);
										
									}else if(data['message']!='' && data['error']==1){
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> <p>This is what I was told:</p><p>'+data['message']+'</p>');
										
									}else{
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> And unfortunately the server didn\'t give me a reason.<p>');
									}
								} );
							});
						}
						function pay_retainage(id){
							UIkit.modal.confirm("Are you sure you want to pay this retainage?").then(function() {
						        $.post('/parcels/retainage/pay/'+id, {
									'_token' : '{{ csrf_token() }}',
									'retainage_id' : id
								}, function(data) {
									if(data['message']!='' && data['error']!=1){
										$('#parcel-subtab-1').trigger('click');
										UIkit.modal.alert(data['message']);
										
									}else if(data['message']!='' && data['error']==1){
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> <p>This is what I was told:</p><p>'+data['message']+'</p>');
										
									}else{
										UIkit.modal.alert('<h2>Well, that didn\'t work.</h2> And unfortunately the server didn\'t give me a reason.<p>');
									}
								} );
							});
						}
					</script>
				@endif
			</div>
			<div class="uk-width-1-5">
				@if(Auth::user()->canDeleteParcels())
				<a uk-tooltip="Delete this Parcel" class="a-trash-4 uk-icon-button uk-margin-bottom" onclick="deleteParcelDetail({{$parcel->id}})"></a><br />
				
				@endIf
				<a uk-tooltip="Open Street View" href=http://maps.google.com/maps?q=&layer=c&cbll={{$parcel->latitude}},{{$parcel->longitude}}&cbp=11,0,0,0,0 target="_blank" class="a-picture uk-icon-button"></a><br />
				<a uk-tooltip="View on Map" href="http://maps.google.com/?q={{$parcel->latitude}},{{$parcel->longitude}}" target="_blank" class="a-map-marker-2 uk-icon-button uk-margin-top"></a><br />
				@if(!is_null($parcel->auditor_site))
				<a uk-tooltip="Open Auditor Website" href="{{$parcel->auditor_site}}" target="_blank" class="uk-icon-button  uk-margin-top a-magnify-2"></a><br />
				@else
				<a uk-tooltip="Auditor Website Unavailable" target="_blank" class="uk-icon-button uk-margin-top a-magnify-2" onclick="UIkit.modal.alert('<h2>Sorry!</h2> <p>Unfortunately, this county doesn\'t have an auditor website entered for it in my database.</p><p>Please contact your HFA and ask them to update your county to include your auditor\'s website.</p>');"></a><br />
				@endif
				@if(Auth::user()->canEditParcels())
				 <a uk-tooltip="Edit Parcel Information" onclick="dynamicModalLoad('parcels/create/{{$parcel->id}}')"  class="uk-icon-button uk-margin-top a-pencil-2"></a>
				 @endif 
			</div>
		</div>
	</div>
	<div class="uk-width-1-1 uk-width-1-4@m">
		<hr class="uk-visible@s uk-hidden@m">
		<h2>{{$parcelData->program_name}}</h2>

		<div class="uk-align-left" style="width: 70%">
			<p>{{$program_owner->entity_name}}<hr class="dashed-hr" />
			{{$program_owner->address1}}<br />
			@if(strlen($program_owner->address2)>0)
				{{$program_owner->address2}}<br />
			@endif
			{{$program_owner->city}}, {{$program_owner->state_name}} {{$program_owner->zip}}<br />
			{{$program_owner->county_name}} County
			</p>
			<p><hr class="dashed-hr">
			@if($program_owner->entity_name != $program_owner->name)
				Contact: {{$program_owner->name}}
			@endif
			<a href="mailto:{{$program_owner->email}}">{{$program_owner->email}}</a><br />
			{{$program_owner->phone}}<br />
			@if(strlen($program_owner->fax)>0)
				{{$program_owner->fax}} <small>FAX</small>
				@endif
				</p>
		</div>
		<div class="uk-align-right" style="width: 18%">
			@if(strlen($program_owner->web_address)>0)
				<a uk-tooltip="Open Agency Website" href="{{$program_owner->web_address}}" target="_blank"  class="uk-icon-button a-globe-4"></a><br />
			@endif
			<!-- <a uk-tooltip="Change Program / Update Owner Information" onclick="dynamicModalLoad('/edit/entity/{{$program_owner->entity_id}}')"  class=" uk-icon-button uk-icon-pencil uk-margin-top"></a> -->

		</div>
	</div>
</div>
<hr class="dashed-hr uk-hidden@m" style="margin-bottom: 10px;">
<a name="breakouts"></a>
@if($parcelData->landbank_property_status_id != 48 && $parcelData->landbank_property_status_id != 11 && ($parcel->lb_validated == 1))
<div class="uk-grid">
	<div class="uk-width-1-1 ">
		<ul class="uk-tab uk-visible@m" uk-switcher>
			<li id="breakout-tab" onclick="$('#breakout-tab-content').load('/breakouts/parcel/{{$parcel->id}}');"><a>Breakouts</a></li>
			<li id="compliance-tab" onclick="$('#compliance-tab-content').load('/compliance/{{$parcel->id}}');"><a>Compliance</a></li>
			<li id="dispositions-tab" onclick="$('#disposition-tab-content').load('/dispositions/{{$parcel->id}}/all/tab');"><a>Disposition</a></li>
			<li id="recaptures-tab" onclick="$('#recaptures-tab-content').load('/recaptures/{{$parcel->id}}/all/tab');"><a>Recaptures</a></li>
			@if(Auth::user()->canViewSiteVisits())
			<li id="sitevisits-tab" onclick="$('#sitevisits-tab-content').load('/sitevisits/{{$parcel->id}}');"><a>Site Visits</a></li>
			@endif
		</ul>


		<a uk-toggle="#parcel-items-offcanvas" class="uk-link-muted uk-visible uk-hidden@m parcel-options-mobile-header">
			<span class="a-menu"></span>
			<span id="parcel-items-option-text"> Cost Breakouts</span>
		</a>
		<hr class="dashed-hr uk-visible@s uk-hidden@m">

		<div id="parcel-items-offcanvas" uk-offcanvas="overlay:true; flip:true">
			<div class="uk-offcanvas-bar">
				<ul class="uk-nav uk-nav-default" uk-nav uk-switcher="connect: #parcel-items">
					<li onClick="$('#parcel-items-option-text').html(' Breakouts');UIkit.offcanvas('#parcel-items-offcanvas').hide();$('#breakout-tab').trigger('click');" >
						<a>Breakouts</a>
					</li>
					<li>
						<a  onClick="$('#parcel-items-option-text').html(' Compliance');$('#compliance-tab').trigger('click');UIkit.offcanvas('#parcel-items-offcanvas').hide();" >Compliance</a>
					</li>
					<?php /*<li onClick="$('#parcel-items-option-text').html(' Site Visits');UIkit.offcanvas.hide();$('#site-visit-tab').trigger('click');" >
						<a>Site Visits</a>
						</li> */?>
					<li onClick="$('#parcel-items-option-text').html(' Dispositions');$('#dispositions-tab').trigger('click');UIkit.offcanvas('#parcel-items-offcanvas').hide();" >
						<a>Disposition</a>
					</li>
					@if(Auth::user()->canViewSiteVisits())
					<li onClick="$('#parcel-items-option-text').html(' Site Visits');$('#sitevisits-tab').trigger('click');UIkit.offcanvas('#parcel-items-offcanvas').hide();" >
						<a>Site Visits</a>
					</li>
					@endif
					<?php /* <li onClick="$('#parcel-items-option-text').html(' Recaptures');UIkit.offcanvas.hide();$('#recaptures-tab').trigger('click');" >
						<a>Recaptures</a>
					</li> */ ?>
				</ul>
			</div>
		</div>
		<ul id="parcel-items" class="uk-list uk-switcher">
			<li id="breakout-tab-content">
				<script type="text/javascript">
					//load this tab by default.
					$('#breakout-tab').trigger('click');
				</script>
			</li>
			<li id="compliance-tab-content">
				
			</li>

			<li id="disposition-tab-content">
				
			</li>
			<li id="recaptures-tab-content">
				
			</li>
			<li id="sitevisits-tab-content">
				
			</li>
		</ul>

	</div>
</div>
@else
<div class="uk-grid">
	<div class="uk-width-1-2@m uk-align-center">
		<h4 class="uk-text-center gray-text uk-margin-large-top uk-margin-large-bottom">SORRY!<br /><small>BREAKOUTS AND OTHER DETAILS ARE NOT AVAILABLE ON THIS PARCEL YET</small></h2>
	</div>
</div>
@endif
<?php /*
<div id="list-tab-bottom-bar" class="parcel-bottom-bar">

	<div class="uk-margin-top uk-align-center" style="max-width: 1420px; margin-left:auto; margin-right-auto; padding-right:10px; padding-left:10px;">
		<br class="uk-visible@s" /><small class="uk-visible@s">&nbsp;</small>
		<small class="uk-visible@m"><span class="uk-align-left uk-dark uk-light use-hand-cursor" onclick="UIkit.modal('#rules').show();">USING {{strtoupper($parcelData->rules_name)}}</span></small>
		<a class="uk-button uk-button-small uk-align-right uk-margin-right uk-visible@m">CALL TO ACTION HFA</a>
		<a class="uk-button uk-button-small uk-visible@s uk-margin-bottom">CALL TO ACTION HFA</a><br clear="uk-visible@s" />
		<small class="uk-visible@s "><span class="uk-dark uk-light use-hand-cursor" onclick="UIkit.modal('#rules').show();">USING {{strtoupper($parcelData->rules_name)}}</span></small>


	</div>


</div>
@if(strlen($parcelRulesAlert) > 0)
	<script>
		UIkit.modal.alert('<h2>Uh oh!</h2> <p>Looks like there are some problems:</p><p><ul><?php echo $parcelRulesAlert; ?></ul></p>');
	</script>
@endif

<script type="text/javascript">
	function changeRules(ruleId){

		UIkit.modal.confirm('Are you sure you want to change the rules associated to this parcel?',function(){
			if(ruleId == "addNewRule"){
				UIkit.modal.alert('I will open up a New Rules window for you. But please note, you will only see the rules you\'ve made when you view this parcel.');
				dynamicModalLoad('new_rules/parcel_only/{{$parcel->id}}');
			}else{
				UIkit.modal.alert('Executed the change for '+ ruleId);
			}
		});
		UIkit.modal('#rules').hide();
		$('#the-rules-options').val('default');

	}
</script>

<div id="rules" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close" uk-close></a>
		<form class="uk-form">
			<div class="uk-grid">
				<div class="uk-width-1-1">

					<small>RULES USED FOR THIS PARCEL:</small>
					<h2 class="uk-margin-top-remove">{{$parcelData->rules_name}}  <a class="uk-button uk-button-small" onclick="dynamicModalLoad('rules/edit/{{$parcelData->program_rules_id}}')"><i class="uk-icon-pencil"></i></a></h2>
					<hr class="uk-margin-top-remove">
					<div class="uk-form-select uk-active" data-uk-form-select="{target:'#rules-drop'}">
						<small><a class="uk-button uk-button-small" id="rules-drop"></a></small>
						<select id="the-rules-options" onchange="changeRules($('#the-rules-options').val())">

							<option  value="default">CLICK HERE TO CHANGE ASSOCIATED RULES</option>
							<optgroup label="Actions">
								<option value="addNewParcelRule">Create Special Rules For This Parcel Only</option>
								<option value="addNewGlobalRule">Create New Rules Accessible to All Parcels</option>

							</optgroup>
							<optgroup label="Available Rules">
								@forEach($rules as $rule)
								<option value="{{$rule->id}}">{{$rule->rules_name}}</option>

								@endForEach
							</optgroup>

						</select>
					</div>


				</div>
			</div>
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Acquisition Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->acquisition_advance == 1) checked @endif >
					@if($parcelData->acquisition_advance == 1)
						<small>MAX $
							@if($parcelData->acquisition_max_advance != 0)
								{{number_format($parcelData->acquisition_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					NIP Loan Payoff Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->nip_loan_payoff_advance == 1) checked @endif >
					@if($parcelData->nip_loan_payoff_advance == 1)
						<small>MAX $
							@if($parcelData->nip_loan_payoff_max_advance != 0)
								{{number_format($parcelData->nip_loan_payoff_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Pre-Demo Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->pre_demo_advance == 1) checked @endif >
					@if($parcelData->pre_demo_advance == 1)
						<small>MAX $
							@if($parcelData->pre_demo_max_advance != 0)
								{{number_format($parcelData->pre_demo_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Demolition Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->demolition_advance == 1) checked @endif >
					@if($parcelData->demolition_advance == 1)
						<small>MAX $
							@if($parcelData->demolition_max_advance != 0)
								{{number_format($parcelData->demolition_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Greening Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->greening_advance == 1) checked @endif >
					@if($parcelData->greening_advance == 1)
						<small>MAX $
							@if($parcelData->greening_max_advance != 0)
								{{number_format($parcelData->greening_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Maintenance Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->maintenance_advance == 1) checked @endif >
					@if($parcelData->maintenance_advance == 1)
						<small>MAX $
							@if($parcelData->maintenance_max_advance != 0)
								{{number_format($parcelData->maintenance_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Admin Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->administration_advance == 1) checked @endif >
					@if($parcelData->administration_advance == 1)
						<small>MAX $
							@if($parcelData->administration_max_advance != 0)
								{{number_format($parcelData->administration_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Other Advance
				</div>
				<div class="uk-width-1-2">
					<input type="checkbox" name="" disabled="true" @if($parcelData->other_advance == 1) checked @endif >
					@if($parcelData->other_advance == 1)
						<small>MAX $
							@if($parcelData->other_max_advance != 0)
								{{number_format($parcelData->other_max_advance)}}
							@else
								Balance of Funds
								@endif
						</small>
						@endif
				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Acquisition
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->acquisition_max != 0)
							{{number_format($parcelData->acquisition_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->acquisition_min,2)}}00

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['acquisition']->amount,2)}}
					</small>
					

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					NIP Loan Payoff
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->nip_loan_payoff_max != 0)
							{{number_format($parcelData->nip_loan_payoff_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->nip_loan_payoff_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['nip']->amount,2)}}

					</small>
					<?php /*{{--@if(strlen($parcelData->nip_loan_payoff_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}}

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Pre-demo
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->pre_demo_max != 0)
							{{number_format($parcelData->pre_demo_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->pre_demo_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['pre_demo']->amount,2)}}

					</small>
					<?php /*{{--@if(strlen($parcelData->pre_demo_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}}

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Demolition
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->demolition_max != 0)
							{{number_format($parcelData->demolition_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->demolition_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['demolition']->amount,2)}}

					</small>
					<?php /*{{--@if(strlen($parcelData->demolition_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}}

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Greening
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->greening_max != 0)
							{{number_format($parcelData->greening_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->greening_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['greening']->amount,2)}}

					</small>
					<?php /*{{--@if(strlen($parcelData->greening_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}}

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Maintenance
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->maintenance_max != 0)
							{{number_format($parcelData->maintenance_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->maintenance_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['maintenance']->amount,2)}}

					</small>
					{{--@if(strlen($parcelData->maintenance_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}} 

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					ADMIN
				</div>
				<div class="uk-width-1-2">
					<small>MAX:
						{{($parcelData->admin_max_percent)* 100}}%
					</small> |
					<small>MIN $

						{{number_format($parcelData->admin_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['administration']->amount,2)}}

					</small>
					<?php /*{{--@if(strlen($parcelData->admin_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}} 

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Other
				</div>
				<div class="uk-width-1-2">
					<small>MAX $
						@if($parcelData->other_max != 0)
							{{number_format($parcelData->other_max,2)}}
						@else
							Balance of Funds
							@endif
					</small> |
					<small>MIN $

						{{number_format($parcelData->other_min,2)}}

					</small>
					<br />
					<small>REQUIRE DOCS @ $
						{{number_format($minimumRules['other']->amount,2)}}
					</small>
					<?php /*{{--@if(strlen($parcelData->other_document_categories)>0)--}}
						{{--SCRIPT TO SHOW CATEGORIES HERE!--}}
						{{--@endif--}}

				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Maximum Total Reimbursement
				</div>
				<div class="uk-width-1-2">
					${{number_format($parcelData->parcel_total_max,2)}}


				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Maintenance Recapture Prorate
				</div>
				<div class="uk-width-1-2">
					{{$parcelData->maintenance_recap_pro_rate}}%


				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<div class="uk-grid">
				<div class="uk-width-1-2">
					Imputed Cost (Dispositions)
				</div>
				<div class="uk-width-1-2">
					${{number_format($parcelData->imputed_cost_per_parcel,2)}}


				</div>
			</div>
			<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
			<?php /*{{--@if(strlen($parcelData->required_document_categories)>0)--}}
				{{--<div class="uk-grid">--}}
					{{--<div class="uk-width-1-2">--}}
						{{--Required Documents--}}
					{{--</div>--}}
					{{--<div class="uk-width-1-2">--}}
						{{--SCRIPT TO PROCESS DOCUMENT CATEGORIES!--}}


					{{--</div>--}}
				{{--</div>--}}
				{{--<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">--}}
				{{--@endif--}} 
				@if(strlen($parcelData->notes)>0)
					<div class="uk-grid">
						<div class="uk-width-1-2">
							Notes About These Rules
						</div>
						<div class="uk-width-1-2">
							{{$parcelData->notes}}


						</div>
					</div>
					<hr class="uk-grid-divider uk-margin-top-remove uk-margin-bottom-remove">
					@endif

		</form>
	</div>
*/ ?>
</div>
<script type="text/javascript">
@if (session('subtab') !== null)
$(document).ready(function(){
    $("#{{session('subtab')}}").click();
    $('html, body').animate({
		scrollTop: $("#{{session('subtab')}}").offset().top
	}, 500, 'linear');
});
<?php session()->forget('subtab'); ?>
@endif

	function matchesStreetView(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("Are you sure you want to update the streetview?").then(function() {
									    var reloadURI = '/toggle_street_view_match/'+window.parcel_id;
									    
									    $.get(reloadURI, function(response, status, xhr) {
										  if (status == "error") {
										  	if(xhr.status == "401") {
										  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
										  	} else if( xhr.status == "500"){
										  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble toggling the streetview match. Please contact support and let them know which parcel you are working so they can trouble shoot it.</p><p>Try reloading the parcel detail by clicking its tab, as it may have actually worked...</p>";
										  	} else {
										  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know that toggling the street view confirmation is misbehaving along with the error you received and the parcel you are working.</br></br>Thanks for your patience and help in making me even better!";
										  	}
										    
										    UIkit.modal.alert(msg);
										  } else {
										  	$("#notifications").html('');

										  	$("#notifications").prepend(response);
										  	console.log('toggled streetview for '+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#parcel-subtab-1').trigger('click');
										  	
										  }
									});
								}
							);
						}
		function togglePretty(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("Are you sure you want mark this as a sample after photo?").then(function() {
									    var reloadURI = '/toggle_pretty/'+window.parcel_id;
									    
									    $.get(reloadURI, function(response, status, xhr) {
										  if (status == "error") {
										  	if(xhr.status == "401") {
										  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
										  	} else if( xhr.status == "500"){
										  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble toggling the after flag. Please contact support and let them know which parcel you are working so they can trouble shoot it.</p><p>Try reloading the parcel detail by clicking its tab, as it may have actually worked...</p>";
										  	} else {
										  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know that toggling the after flag is misbehaving along with the error you received and the parcel you are working.</br></br>Thanks for your patience and help in making me even better!";
										  	}
										    
										    UIkit.modal.alert(msg);
										  } else {
										  	$("#notifications").html('');

										  	$("#notifications").prepend(response);
										  	console.log('toggled after for '+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#parcel-subtab-1').trigger('click');
										  	
										  }
									});
								}
							);
						}
		function toggleUgly(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("Are you sure you want to mark this as a sample before photo?").then(function() {
									    var reloadURI = '/toggle_ugly/'+window.parcel_id;
									    
									    $.get(reloadURI, function(response, status, xhr) {
										  if (status == "error") {
										  	if(xhr.status == "401") {
										  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
										  	} else if( xhr.status == "500"){
										  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble toggling the before flag. Please contact support and let them know which parcel you are working so they can trouble shoot it.</p><p>Try reloading the parcel detail by clicking its tab, as it may have actually worked...</p>";
										  	} else {
										  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know that toggling the before flag is misbehaving along with the error you received and the parcel you are working.</br></br>Thanks for your patience and help in making me even better!";
										  	}
										    
										    UIkit.modal.alert(msg);
										  } else {
										  	$("#notifications").html('');

										  	$("#notifications").prepend(response);
										  	console.log('toggled before flag for '+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#parcel-subtab-1').trigger('click');
										  	
										  }
									});
								}
							);
						}
						function resolveItem(resolution_id,resolution,action,parcel_id){
							window.parcel_id = parcel_id
							var reloadURI = '/validate_parcel?resolve=1&parcelId='+window.parcel_id+"&resolution_id="+encodeURI(resolution_id)+"&resolution="+encodeURI(resolution)+"&action="+encodeURI(action);
									    
									    $.get(reloadURI, function(response, status, xhr) {
										  if (status == "error") {
										  	if(xhr.status == "401") {
										  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
										  	} else if( xhr.status == "500"){
										  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your import. Please contact support and let them know which import you were validating, and what record it last processed.</p>";
										  	} else {
										  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
										  	}
										    
										    UIkit.modal.alert(msg);
										  } else {
										  	$("#parcel-"+window.parcel_id).html('');

										  	$("#parcel-"+window.parcel_id).prepend(response);
										  	$("#resolution-"+resolution_id).fadeOut();
										  	console.log('loaded into row parcel-'+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#rerunValidation').fadeIn();
										  	
										  	
									  		// UIkit.modal.alert('<h1>I updated the parcel.</h1><p>Remember that once you\'re done, to click to rerun the validation. Otherwise the parcels will not show as validated.');
									  		
										  	
										  }
										});

									
						}
</script>
@if(Auth::user()->canDeleteParcels())
<script>
function deleteParcelDetail(parcel_id){
	UIkit.modal.confirm("<p>Are you sure you want to delete this parcel and EVERYTHING associated with it?</p><p> This includes: <ul><li>Supporting Documents</li><li>Communications</li><li>Notes</li><li>Compliances</li><li>Cost,Request,Approved/PO,Invoice Items</li><li>Dispositions</li><li>Recaptures</li><li>Retainages</li><li>Site Visits</li></ul></p><p>If this parcel was a part of an import it will clear itself and any validations from that import. If the parcel was the only parcel on a request (po and invoice) it and its associated approvals will also be deleted.</p> <p><strong>Any transactions in accounting WILL NOT BE DELETED. Those will need to be manually reconciled/deleted.</strong></p>").then(function() {
        $.get('/parcels/delete/'+parcel_id, function(data) {
			if(data['message']!='' && data['error']!=1){
				/// in an effort to keep things clear - hopefully this works
				$('#parcel-'+parcel_id).removeClass('keepMe');
				$('#parcel-'+parcel_id).addClass('hideMe');
				$('#parcel-'+parcel_id).addClass('deletedParcel');
				/// now switch back
				$('#list-tab').trigger('click');
				$('#detail-tab-1').hide();
				$('#detail-tab-1-text').html('');

				
				UIkit.modal.alert(data['message']);
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				
			}else{
				UIkit.modal.alert('Something went wrong. Please contact Brian at Greenwood 360 and let him know that the parcel deletion failed and which parcel on which it failed. brian@greenwood360.com');
			}
		} );
		
    });
}
@if(Auth::user()->isHFAAdmin())
function validate_parcel_hfa(){
    $.post('{{ URL::route("parcel.guide.validateparcelhfa", [$parcel->id]) }}', {
			'_token' : '{{ csrf_token() }}'
	}, function(data) {
		if(data['validated']){
			$('.step33').removeClass('a-checkbox');
			$('.step33').addClass('a-checkbox-checked');
			$('.step33name').toggleClass("uk-hidden");
		}else if(!data['validated']){
			$('.step33').removeClass('a-checkbox-checked');
			$('.step33').addClass('a-checkbox');
			$('.step33name').toggleClass("uk-hidden");
		}else{
			UIkit.modal.alert('Something went wrong.');
		}
	} );

}

function mark_retainage_paid_hfa(id){
	$.post('{{ URL::route("parcel.guide.markretainagepaidhfa", [$parcel->id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'retainage_id' : id
	}, function(data) {
		if(data['paid'] == 1){
			$('.step_r'+id).removeClass('a-checkbox');
			$('.step_r'+id).addClass('a-checkbox-checked');
			$('.step_r'+id+'_name').toggleClass("uk-hidden");
		}else if(data['paid'] == 0){
			$('.step_r'+id).removeClass('a-checkbox-checked');
			$('.step_r'+id).addClass('a-checkbox');
			$('.step_r'+id+'_name').toggleClass("uk-hidden");
		}else{
			UIkit.modal.alert('Something went wrong.');
		}
	} );
}
function mark_advance_paid_hfa(id){
	$.post('{{ URL::route("parcel.guide.markadvancepaidhfa", [$parcel->id]) }}', {
			'_token' : '{{ csrf_token() }}',
			'advance_id' : id
	}, function(data) {
		if(data['paid'] == 1){
			$('.step_a'+id).removeClass('a-checkbox');
			$('.step_a'+id).addClass('a-checkbox-checked');
			$('.step_a'+id+'_name').toggleClass("uk-hidden");
		}else if(data['paid'] == 0){
			$('.step_a'+id).removeClass('a-checkbox-checked');
			$('.step_a'+id).addClass('a-checkbox');
			$('.step_a'+id+'_name').toggleClass("uk-hidden");
		}else{
			UIkit.modal.alert('Something went wrong.');
		}
	} );
}
@endif
</script>
@endIf
