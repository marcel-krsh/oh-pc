							<!-- validat_hhf_retention_parcel_row -->
							<tr>
								<td><small><? echo $parcel->{"File Number"} ?></small></td>
								<?php 
								if($parcel->{"Property State"} == "OH") {
									 $parcel->{"Property State"} = "Ohio"; 
									}
									?>
								
								@if(
									(
									$parcel->{"street_address"}
									.", ".
									$parcel->{"Property City"}
									.", ".
									$parcel->{"Property State"}
									." ". 
									$parcel->{"Property Zip"}
									) == (
									 $geoDataUpdateCorrection['street_number']
									." ".
									 $geoDataUpdateCorrection['street_name']
									.", ".
									 $geoDataUpdateCorrection['city']
									.", ".
									 $geoDataUpdateCorrection['state_name']
									." ".
									 $geoDataUpdateCorrection['zip']
									)
								)
								<td><small><? echo $parcel->{"street_address"}
									.", ".
									$parcel->{"Property City"}
									.", ".
									$parcel->{"Property State"}
									." ". 
									$parcel->{"Property Zip"}; ?></small></td>
								@else
								<td style="background-color:lightpink">
								<small>
									<span uk-tooltip="This is the address provided in your export.">A: <? echo $parcel->{"street_address"}
									.", ".
									$parcel->{"Property City"}
									.", ".
									$parcel->{"Property State"}
									." ". 
									$parcel->{"Property Zip"}; ?><br />
									<span uk-tooltip="This is the actual address returned from the GIS system." >G:</span> <? echo $geoDataUpdateCorrection['street_number']." ".$geoDataUpdateCorrection['street_name'].", ".$geoDataUpdateCorrection['city'].", ".$geoDataUpdateCorrection['state_name']." ".$geoDataUpdateCorrection['zip']; ?>
								</small></td>
								@endIf
								<td><small><? echo $parcel->{"Property County"}; ?></small></td>
								<td><small>{{$lat}}</small></td>

								<td><small>{{$lon}}</small></td>
								<td><small>{{$congressional}}</small></td>
								<td><small>{{$ohHouse}}</small></td>
								<td><small>{{$ohSenate}}</small></td>
								<td><small><? echo $parcel->{"Status"}; ?></small></td>

								

								<script type="text/javascript">

									<?php /*
									updateTotals(list,record,addressCount,usHouseCount,ohHouseCount,ohSenateCount,identicalCount,historicCount,hhfCount,totalCount,processedCount,percentComplete,rowNum);
									*/?>
									updateTotals(0,0,{{0+$updateTotals['addressCount']}},{{0+$updateTotals['usHouseCount']}},{{0+$updateTotals['ohHouseCount']}},{{0+$updateTotals['ohSenateCount']}},{{0+$updateTotals['identicalCount']}},{{0+$updateTotals['historicCount']}},{{0+$updateTotals['hhfCount']}},{{0+$updateTotals['totalCount']}},{{0+$updateTotals['processedCount']}},{{0+$updateTotals['percentComplete']}},{{0+$updateTotals['rowNum']}});
								</script>
								</td>
								
							</tr>						