@extends('layouts.allita')


@section('content')

<?php 
ini_set('memory_limit','1000M');
/*///////////////////////////////////////////////////////////////////////////////////////////////////
/*////////////// VARIABLE REFERENCE //////////////////////////////////////////////////////////////////////
/*///////////////////////////////////////////////////////////////////////////////////////////////////////

allita_system_id": 1
    +"sales_force_id": "a07t000000094o1"
    +"parcel_name": "12914088"
    +"street_address": "2788 East 118th St."
    +"city": "Cleveland"
    +"state_id": 36
    +"county_id": 18
    +"state_name": "Ohio"
    +"county_name": "Cuyahoga"
    +"zip": "44120"
    +"program_name": "NIP Cuyahoga"
    +"entity_id": 8
    +"entity_name": "Cuyahoga County Land Reutilization Corporation."
    +"entity_user_id": 9
    +"entity_street_address": "323 W Lakeside Ave, Suite 160"
    +"entity_street_address_2": ""
    +"entity_city": "Cleveland"
    +"entity_state": "Ohio"
    +"entity_zip": "44113"
    +"entity_phone": "(216) 698-8853"
    +"entity_fax": "(216) 698-8972"
    +"entity_contact": "Cuyahoga County Land Reutilization Corporation"
    +"entity_contact_email": "bwhitney@cuyahogalandbank.org"
    +"program_id": 8
    +"cost_parcel_id": 1
    +"NIPLoanCost": 100.0
    +"AcquisitionCost": null
    +"PreDemoCost": 640.0
    +"DemolitionCost": 8550.0
    +"GreeningCost": 550.0
    +"MaintenanceCost": 1200.0
    +"AdministrationCost": 1000.0
    +"OtherCost": 0.0
    +"TotalCost": 12040.0
    +"reimbursement_request_id": 96
    +"request_parcel_id": 1
    +"NIPLoanRequest": 100.0
    +"AcquisitionRequest": 0.0
    +"PreDemoRequest": 640.0
    +"DemolitionRequest": 8550.0
    +"GreeningRequest": 550.0
    +"MaintenanceRequest": 1200.0
    +"AdministrationRequest": 1000.0
    +"OtherRequest": 0.0
    +"total_requested": 12040.0
    +"purchase_order_id": 96
    +"poi_parcel_id": 1
    +"NIPLoanApproved": 100.0
    +"AcquisitionApproved": null
    +"PreDemoApproved": 640.0
    +"DemolitionApproved": 8550.0
    +"GreeningApproved": 550.0
    +"MaintenanceApproved": 1200.0
    +"AdministrationApproved": 1000.0
    +"OtherApproved": 0.0
    +"total_approved": 12040.0
    +"reimbursement_invoice_id": 96
    +"invoice_parcel_id": 1
    +"NIPLoanInvoiced": 100.0
    +"AcquisitionInvoiced": 0.0
    +"PreDemoInvoiced": 640.0
    +"DemolitionInvoiced": 8550.0
    +"GreeningInvoiced": 550.0
    +"MaintenanceInvoiced": 1200.0
    +"AdministrationInvoiced": 1000.0
    +"OtherInvoiced": 0.0
    +"total_invoiced": 12040.0
    +"recapture_invoice_id": null
    +"disposition_id": null
    +"HFA_Status": "Paid"
    +"id": 1
    +"PropertyIDRecordID": "a07t000000094o1"
    +"PropertyIDParcelID": "12914088"
    +"PropertyIDPropertyName": "12914088"
    +"ProgramProgramName": "NIP Cuyahoga"
    +"BatchNumber": 40
    +"ReimbursementID": "a08t0000000D26k"
    +"ReimbursementCreatedDate": "2015-05-08 00:00:00"
    +"DatePaid": "2015-05-28 00:00:00"
    +"ReimbursementReimbursementName": "12914088"
    +"GreeningAdvanceDocumented": 0
    +"GreeningAdvanceOption": 0
    +"GreeningRequested": 550.0
    +"GreeningPaid": 550.0
    +"PreDemoRequested": 640.0
    +"PreDemoPaid": 640.0
    +"MaintenanceRequested": 1200.0
    +"MaintenancePaid": 1200.0
    +"DemolitionRequested": 8550.0
    +"DemolitionPaid": 8550.0
    +"AdministrationRequested": 1000.0
    +"AdministrationPaid": 1000.0
    +"AcquisitionRequested": null
    +"AcquisitionPaid": null
    +"NIPLoanPayoffCost": 100.0
    +"NIPLoanPayoffRequested": 100.0
    +"NIPLoanPayoffApproved": 100.0
    +"NIPLoanPayoffPaid": 100.0
    +"TotalRequested": 12040.0
    +"TotalApproved": 12040.0
    +"TotalPaid": 12040.0
    +"ProcessDate": null
    +"Retainage": 875.0
    +"RetainagePaid": 1
    +"ReturnedFundsExplanation": null
    +"ProgramIncome": null
    +"NetProceeds": null
    +"RecapturedOwed": null
    +"RecapturePaid": 0
    
    /*///////////////////////////////////////////////////////////////////////////////////////////////////
?>

<?php // use this function to calculate totals --- $value = array_sum(array_column($arr,'f_count'));   ?>
<h4>Sales Force Import Validation</h4>
<p>Columns that have a mismatch in data are highligted pink.</p>

		<?php  ?>
		<table class="uk-table uk-table-hover uk-table-striped" style="min-width: 1420px;">
		<thead >
			<tr >
				<th width="50">SF Batch#</th>
				<th width="220">Entity</th>
				<th width="100">Program </th>
				<th width="100">Allita REQ#</th>
				<th width="100">Allita PO#</th>
				<th width="100">Allita INV#</th>
				<th width="200">Parcel ID Confirm</th>
				<th width="400">Saleforce Reference Links</th>
			</tr>
		</thead>
		<tbody id="results-list">

	
    @foreach ($reqs as $req ) 

    	
    	<tr>
	         <td width="">
	         	@if($req->BatchNumber < 1470000000)
	         	{{ $req->BatchNumber }}
	         	@else
	         	<small>PROCESS DATE:</small><br/>
	         	{{ date('m/d/Y', $req->BatchNumber) }}
	         	@endif
			 </td>
	         <td width="">
	         	{{ $req->entity_name }}

	         </td>

	         <td width="" 
	         @if(trim($req->program_name) !=  trim($req->ProgramProgramName) )
	          style="background-color:pink; color:black;" 
	         @endif
	         >
	         	{{ $req->program_name }}<br />
	         	<small>Name in Salesforce<br />{{ $req->ProgramProgramName }}</small>
	         </td>
	         <td @if(trim($req->total_requested)  !=  trim($req->TotalRequested))
	          style="background-color:pink; color:black;" 
	         @endif >
	         	<small>AL Total Requested<br/>{{ money_format('%(#8n',$req->total_requested) }}</small>
	         	<br />
	         	<small>SF Total Requested<br/>{{ money_format('%(#8n',$req->TotalRequested) }}</small>
	         </td>
	         <td @if(trim($req->total_approved)  !=  trim($req->TotalApproved))
	          style="background-color:pink; color:black;" 
	         @endif >
	         	
	         	<small>AL Total Approved:<br />{{ money_format('%(#8n',$req->total_approved) }}</small><br />
	         	<small>SF Total Approved:<br />{{ money_format('%(#8n',$req->TotalApproved) }}</small>
	         </td>
	         <td @if(trim($req->total_invoiced)  !=  trim($req->TotalPaid))
	          style="background-color:pink; color:black;" 
	         @endif > 
	         	
	         	<small>AL Total INVOICED:<br/>{{ money_format('%(#8n',$req->total_invoiced) }}</small><br />
	         	<small>SF Total Paid:<br/>{{ money_format('%(#8n',$req->TotalPaid) }}</small>

	         </td>
	         <td @if($req->PropertyIDParcelID  !=  $req->parcel_name)
	          style="background-color:pink; color:black;" 
	         @endif> 
	         	
	         	<small>SF Parcel Parcel ID:</small> <br /><strong>{{ $req->PropertyIDParcelID }}</strong><br /><small>SF ID: </small><br/><strong>{{ $req->PropertyIDRecordID }}</strong><br /><small>Allita Parcel ID:</small> <br /><strong>{{ $req->parcel_name }}</strong>
	         </td>
	         <td @if($req->PropertyIDRecordID !=  $req->sales_force_id )
	          style="background-color:pink; color:black;" 
	         @endif
	         >
	         	
	         	Allita Parcel ID: {{$req->allita_system_id}} | A:SF-REF: <a href="https://ohiohome.my.salesforce.com/{{ $req->sales_force_id}}" target="_blank">{{$req->sales_force_id}}</a>
	         </td>
        </tr>
   
	 

    @endforeach
    </tbody>
    </table>
    <?php /* */?>
@stop



