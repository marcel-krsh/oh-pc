@if($waiver != 1 && $useGISAddress != 1  && $updateAddress != 1 && $resolutionId < 1)
<tr id="parcel-{{$record}}" class="uk-animation-fade">
@endIf
	<td><small uk-tooltip="@if(Auth::user()->id == 1)
			| Total Count: {{session('validation_totalCount')}}
			| AddressCount: {{session('validation_addressCount') }}
            | OH Senate Count {{session('validation_ohSenateCount')}}
            | OH House Count {{session('validation_ohHouseCount')}}
            | US House Count {{session('validation_usHouseCount')}}
            | Processed Count {{session('validation_processedCount')}}
            | Percent Complete {{session('validation_percentComplete')}}
            | Identical Count {{session('validation_identicalCount')}}
            | Historic Count {{session('validation_historicCount')}}
            | HHF Count {{session('validation_hhfCount')}} @endif"><a onclick="window.open('/viewparcel/{{$parcel->id}}', '_blank')" uk-tooltip="OPEN THIS PARCEL IN A NEW WINDOW">{{$parcel->parcel_id}}</a></small></td>
	<?php 
	
	$validatedAddress = '<td><small><span class="a-circle-checked" uk-tooltip="Address Validated"></span> '.$parcel->{"street_address"}.', '.$parcel->{"city"}.', '.$parcel->{"state_name"}." ".$parcel->zip.'</small></td>';
	?>
	<?php 
	$suppliedAddress = $parcel->street_address.", ".$parcel->city.", ".$parcel->state_name." ".$parcel->zip;
	$gisAddress = "";
	if(isset($geoDataUpdateCorrection['street_number'])){
		$gisAddress .= $geoDataUpdateCorrection['street_number'];
	}
	if(isset($geoDataUpdateCorrection['street_name'])){
		$gisAddress .=" ".$geoDataUpdateCorrection['street_name'].",";
	}
	if(isset($geoDataUpdateCorrection['city'])){
		$gisAddress .=" ".$geoDataUpdateCorrection['city'].",";
	}
	if(isset($geoDataUpdateCorrection['state_name'])){
		$gisAddress .= " ".$geoDataUpdateCorrection['state_name'];
	}
	if(isset($geoDataUpdateCorrection['zip'])){
		$gisAddress .= " ".$geoDataUpdateCorrection['zip'];
	}
	

if( $parcel->address_validated == 1 ) {
	echo $validatedAddress;
} else if($suppliedAddress == $gisAddress) {
		echo $validatedAddress;
} else if($withdraw != 1) { 
	?>
	<td style="background-color:rgba(255, 0, 0, 0.11)">
	<small>
		<span id="supplied-address-{{$record}}" uk-tooltip="This is the address provided in your export."><span class="a-circle-cross"></span></span> 
		<? echo $parcel->{"street_address"}
		.", ".
		$parcel->{"city"}
		.", ".
		$parcel->{"state_name"}
		." ". 
		$parcel->{"zip"}; ?><br />
		<span uk-tooltip="This is the actual address returned from the GIS system." >
			<span class="a-map-marker-2"></span>
		</span> 
		<? echo $geoDataUpdateCorrection['street_number']." ".$geoDataUpdateCorrection['street_name'].", ".$geoDataUpdateCorrection['city'].", ".$geoDataUpdateCorrection['state_name']." ".$geoDataUpdateCorrection['zip']; ?> @if(!is_null($geoDataUpdateCorrection['google_map_link']))<a href="{{ $geoDataUpdateCorrection['google_map_link'] }}" class="uk-link-muted" target="_blank">VIEW ON MAP</a> @endIf
		<br />
		<a onclick="useGISAddress({{$parcel->id}});" class="uk-button uk-button-default uk-button-small uk-width-1-1">USE GIS ADDRESS</a>
		<a onclick="correctAddress({{$parcel->id}},{{$lat}},{{$lon}});" class="uk-button uk-button-default uk-button-small uk-width-1-1 uk-margin-small-top">CORRECT PROVIDED ADDRESS</a>
	</small></td>
	<?php } else { ?>

			<td >
	<small>
		<span id="supplied-address-{{$record}}" uk-tooltip="Withdrawn."><span class="a-circle-cross"></span></span> <strike>
		<? echo $parcel->{"street_address"}
		.", ".
		$parcel->{"city"}
		.", ".
		$parcel->{"state_name"}
		." ". 
		$parcel->{"zip"}; ?></strike>
	</small></td>
		<?php } ?>

	<td><small>@if($withdraw)NA @else {{$lat}}@endif</small></td>

	<td><small>@if($withdraw)NA @else {{$lon}}@endif</small></td>

	<td><small>@if($withdraw)NA @else US: {{$congressional}}, OH House: {{$ohHouse}}, OH Senate: {{$ohSenate}}@endif</small></td>

	<td @if($unique != 0 ) 
		style="background-color: rgba(255, 0, 0, 0.11);" 
	@endif
	><small>
	
	@if($parcel->landbank_property_status_id == 48)
	<span class="a-circle-cross"></span> WITHDRAWN
	@elseif($unique != 0 && $unresolvedLandBankCount > 0) 
	<a onclick="resolve( {{$parcel->id}},{{$lat}},{{$lon}});" class="uk-link-muted" ><span class="a-circle-cross"></span>&nbsp;RESOLVE {{$unique}} PARCEL<? if($unique > 1){ echo "S"; } ?></a> 
	@else
	<span class="a-circle-checked"></span>&nbsp;Unique 
	@endIf 
	</small></td>

	<td @if($hhf != 0) style="background-color: rgba(255, 0, 0, 0.11); @endIf"><small>
	@if($parcel->landbank_property_status_id == 48)
	<span class="a-circle-cross"></span> WITHDRAWN
	@elseif($hhf != 0  && $unresolvedLandBankCount > 0) <a onclick="resolve({{$record}},{{$lat}},{{$lon}});" class="uk-link-muted" ><span class="a-circle-cross"></span>&nbsp;RESOLVE {{$hhf}} PARCEL<? if($hhf > 1){ echo "S"; } ?></a> 
	
	@else <span class="a-circle-checked"></span>&nbsp;Clear @endIf</small>
	</td>
	<td @if($parcel->historic_significance_or_district == 1 && $parcel->historic_waiver_approved ==0)
			style="background:rgba(255, 0, 0, 0.11)" 
		@endif><small>
		@if($parcel->landbank_property_status_id == 48)
			<span class="a-circle-cross"></span> WITHDRAWN
		@elseif($parcel->historic_significance_or_district == 1)
			<span class="a-circle-cross"></span>&nbsp;YES 
			@if($parcel->historic_waiver_approved == 0)
				<a onClick="have_historic_waiver({{$parcel->id}});" class="uk-button uk-button-default uk-button-small" uk-tooltip="If your property was/is historic - you must have a waiver to be reimbursed." >HAVE WAIVER</a>
			@else 
				<br /><span class="a-circle-checked"></span>&nbsp;Waiver
			@endIf
		@else
			<span uk-tooltip="All properties will be checked against the historic database. Properties that are historic and do not have a waiver will not be eligible for reimbursement.">Not Claimed*</span>
		@endIf
	</small>
	@if($waiver != 1 && $useGISAddress != 1  && $updateAddress != 1 && $resolutionId < 1)
	<script type="text/javascript">

		<?php /*
		updateTotals(list,record,addressCount,usHouseCount,ohHouseCount,ohSenateCount,identicalCount,historicCount,hhfCount,totalCount,processedCount,percentComplete,rowNum);
		*/?>
		if(window.resetImportValidation == 1){
			window.resetImportValidation = 0;
			// we unset this so it does not keep resetting the validation of the import on each request.
		}
		updateTotals({{$list}},{{$record}},{{0+$updateTotals['addressCount']}},{{0+$updateTotals['usHouseCount']}},{{0+$updateTotals['ohHouseCount']}},{{0+$updateTotals['ohSenateCount']}},{{0+$updateTotals['identicalCount']}},{{0+$updateTotals['historicCount']}},{{0+$updateTotals['hhfCount']}},{{0+$updateTotals['totalCount']}},{{0+$updateTotals['processedCount']}},{{0+$updateTotals['percentComplete']}},{{0+$updateTotals['rowNum']}});
	</script>
	@endIf
	</td>
	<td>
	<small>
	<span uk-tooltip="{{$insertedValidationResolution}} New Validation Resolution(s) Added.">
	@if($parcelLandBankStatus == 46)
	READY FOR COSTS<br />
	@elseIf($parcelLandBankStatus == 44)
	UNABLE TO VALIDATE<br />
	
	@elseif($parcelLandBankStatus == 48)
	PARCEL WITHDRAWN<br />

	@else
	{{$parcel->lb_property_status_id}}
	@endIf
	@if($withdraw != 1)
		@if($unresolvedLandBankCount < 1)
			All {{$resolutionLandBankCount}} Matches Resolved.<br />
		@else
			<a onclick="resolve({{$parcel->id}},{{$parcel->latitude}},{{$parcel->longitude}});">{{$unresolvedLandBankCount}} Matches Unresolved.</a><br />
		@endif
		
	@endif
	<? echo $debugMessage; ?>
	</span>
	</small>
	@if($validated == 1)
		<script>
			UIkit.modal.alert('<h2>Validated!</h2><p>Congrats all your parcels have been validated. You can work on each one by clicking on its Parcel ID below, and they will open in a new window for you.</p>.');
		</script>
	@endIf
	@if($parcel->lb_validated == 0 )
		<a class="uk-button uk-button-default uk-button-small uk-margin-left-small attention" onclick="forceValidation({{$parcel->id}});">CONFIRM VALID</a>
	@endIf
	</td>
@if($waiver != 1 && $useGISAddress != 1  && $updateAddress != 1 && $resolutionId < 1)	
</tr>
@endIf