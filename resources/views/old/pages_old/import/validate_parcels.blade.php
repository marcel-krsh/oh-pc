@extends('layouts.allita')

@section('content')
<?php //echo phpinfo(); ?>
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('#list-tab-text').html(' : Validate Your Parcels');
	
		$('#detail-tab-1-icon').addClass('a-check');
		// display the tab
		$('#list-tab').show();
	</script>
	@if(session('validationNotice') != 1)
	<script>
		UIkit.modal.alert('<h1 >Let\'s validate!</h1><p>I am about to make everyone\'s life a little bit easier :). What used to take hours – and sometimes days – we\'re going to do in just a few minutes.</p><p>That said, any parcels that I can\'t validate, you will need to address before submitting them for reimbursement.</p>');
	</script>
	<?php session(['validationNotice'=>1]); ?>
	@endif
	<style>
	#tabs > li {
	    border-top: 0px solid #ddd;
	}
	</style>
	<div uk-grid>
		<div class="uk-width-1-1" style="padding-left: 0;">
			
				
				<div class="uk-article">
					<div class="uk-block uk-block-primary uk-dark uk-light">
                        <div class="uk-container">
                        	<div uk-grid>
	                        	<div class="uk-width-1-5@m">
	                        		<p class="uk-text-center"><span class="a-check blue-shadow" style="font-size: 150px;"></span></p>
	                        	</div>
	                            <div class="uk-width-4-5@m"><h1>VALIDATION</h1><hr style="border-top: 2px dotted;" /><br />
		                            <div uk-grid>
		                            	<div class="uk-width-1-3@m  uk-row-first">
		                                    <div class="uk-panel">
		                                        <h3>Ready?</h3><p> Select which import to validate and then click the "START VALIDATION" button. You can also choose "ALL" to validate all parcels that have not yet been validated.</p>
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>
		                                        	<span id="valid-address"><span class="a-check"></span></span> 
		                                        	<span id="valid-address-status">Validate Address</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-us-house"><span class="a-check"></span></span> 
		                                        	<span id="valid-us-house-status">US House District</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-oh-house"><span class="a-check"></span></span> 
		                                        	<span id="valid-oh-house-status">OH House District</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-oh-senate"><span class="a-check"></span></span> 
		                                        	<span id="valid-oh-senate-status">OH Senate District</span>
		                                        </p>
		                                        
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>
		                                        	<span id="valid-identical-parcels"><span class="a-check"></span></span> 
		                                        	<span id="valid-identical-parcels-status">Matching Parcels</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-historic"><span class="a-check"></span></span> 
		                                        	<span id="valid-historic-status">Historic Parcels Without Waivers</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-previous"><span class="a-check"></span></span> 
		                                        	<span id="valid-previous-status">Previous HHF Funding</span>
		                                        </p>
		                                    </div>
		                                </div>
		                                
		                            </div>
	                            </div>
	                        </div>
                        </div>
                    </div>
                </div>

				<div uk-grid>
					<div class="uk-width-1-1 uk-text-center">
					@if(count($importList)>0)
					<select id="validation-list" class="uk-select uk-align-center uk-width-1-1 uk-width-1-3@m uk-margin-top uk-margin-bottom">
						<option value="0">SELECT IMPORT TO VALIDATE</option>
						<!-- <option value="all">[ ALL UNRUN IMPORTS ]</option> -->
						@forEach($importList as $data)
							<option value="{{$data->id}}" 
								@if(isset($importId))
									@if($data->id == $importId)
									 SELECTED
									 @endif
								@endif
							>[ 
							{{ date('F jS, Y \a\t h:i:s A', strtotime($data->created_at)) }} 
							IMPORT {{$data->id}} ] {{$data->name}} 
							@if(Auth::user()->entity_type == 'hfa')
							FOR {{ $data->entity_name }} 
							@endIf
							</option>
						@endForEach
					</select>
					<div id="validation-progress" class="uk-progress uk-progress-striped uk-active" style="display: none;">
                                <div id="progress-bar" class="uk-progress-bar" style="width: 5%;"><p align="center"><span id="processed-count">0</span> / <span id="total-count"><span class="a-refresh-2 uk-icon-spin"></span></span></p></div>
                            </div>
					<a id="start-validation" class="uk-margin-top uk-button uk-button-default uk-button-large uk-width-1-1 uk-width-1-3@m uk-align-center" onclick="if($('#validation-list').val() != 0) { startValidation(); } else { UIkit.modal.alert('Please select an import to process first.'); }">START VALIDATION</a>

					<a id="rerunValidation" class="uk-margin-top uk-button uk-button-default uk-button-large uk-width-1-1 uk-width-1-3@m uk-align-center" onclick="startValidation();" style="display: none;">RERUN VALIDATION</a>
					@else
					<h2 class="uk-text-center uk-margin-top">All imports have been validated.</h2>
					@endIf
					</div>
				</div>
					<style type="text/css">
						.uk-progress-bar {
							background: #005186;
						}
					</style>
					<script type="text/javascript">
						function startValidation(){
								$('#validation-results').html('');
								$('#rerunValidation').slideUp();
								$('#validation-list').slideUp();
								$('#start-validation').slideUp();
								$('#validation-progress').slideDown();
								$('#valid-previous').html('');
								$('#valid-previous').html('<span class="a-circle"></span>');
								$('#valid-historic').html('');
								$('#valid-historic').html('<span class="a-circle"></span>');
								$('#valid-identical-parcels').html('');
								$('#valid-identical-parcels').html('<span class="a-circle"></span>');
								$('#valid-oh-senate').html('');
								$('#valid-oh-senate').html('<span class="a-circle"></span>');
								$('#valid-oh-house').html('');
								$('#valid-oh-house').html('<span class="a-circle"></span>');
								$('#valid-us-house').html('');
								$('#valid-us-house').html('<span class="a-circle"></span>');
								$('#valid-address').html('');
								$('#valid-address').html('<span class="a-circle"></span>');

								// initiate the validation - we start at 0
								loadNextResult($('#validation-list').val(),0,1);
							
						}

						function updateTotals(list,record,addressCount,usHouseCount,ohHouseCount,ohSenateCount,identicalCount,historicCount,hhfCount,totalCount,processedCount,percentComplete,rowNum){
							// update values
							percentComplete = (processedCount/totalCount) * 100;
							percentCompleted = percentComplete+"%";
							//alert('processedCount = '+processedCount+'; totalCount = '+totalCount);
							if(processedCount >= 1 && fadedIn != 1){
								//only run this once
								var fadedIn = 1;
								$('#validation-results-table').fadeIn();
							}
							if(addressCount > 0){
								$('#valid-address').html('');
								$('#valid-address').html(addressCount);
								$('#valid-address-status').html('');
								if(addressCount == 1){
									$('#valid-address-status').html('Invalid Address Found');
								} else {
									$('#valid-address-status').html('Invalid Addresses Found');
								}
							}
							if(usHouseCount > 0){
								$('#valid-us-house').html('');
								$('#valid-us-house').html(usHouseCount);
								$('#valid-us-house-status').html('');
								if(usHouseCount == 1){
									$('#valid-us-house-status').html('Undetermined US House District');
								} else {
									$('#valid-us-house-status').html('Undeterimined US House Districts');
								}
							}
							if(ohHouseCount > 0){
								$('#valid-oh-house').html('');
								$('#valid-oh-house').html(ohHouseCount);
								$('#valid-oh-house-status').html('');
								if(ohHouseCount == 1){
									$('#valid-oh-house-status').html('Undetermined OH House District');
								} else {
									$('#valid-oh-house-status').html('Undeterimined OH House Districts');
								}
							}if(ohSenateCount > 0){
								$('#valid-oh-senate').html('');
								$('#valid-oh-senate').html(ohSenateCount);
								$('#valid-oh-senate-status').html('');
								if(ohSenateCount == 1){
									$('#valid-oh-senate-status').html('Undetermined OH Senate District');
								} else {
									$('#valid-oh-senate-status').html('Undeterimined OH Senate Districts');
								}
							}if(identicalCount > 0){
								$('#valid-identical-parcels').html('');
								$('#valid-identical-parcels').html(identicalCount);
								$('#valid-identical-parcels-status').html('');
								if(identicalCount == 1){
									$('#valid-identical-parcels-status').html('Matched Parcel');
								} else {
									$('#valid-identical-parcels-status').html('Matched Parcels');
								}
							}if(historicCount > 0){
								$('#valid-historic').html('');
								$('#valid-historic').html(historicCount);
								$('#valid-historic-status').html('');
								if(historicCount == 1){
									$('#valid-historic-status').html('Historic Parcel Without a Waiver');
								} else {
									$('#valid-historic-status').html('Historic Parcels Without Waivers');
								}
							}

							if(hhfCount > 0){
								$('#valid-previous').html('');
								$('#valid-previous').html(hhfCount);
								$('#valid-previous-status').html('');
								if(hhfCount == 1){
									$('#valid-previous-status').html('Previous HHF Parcel');
								} else {
									$('#valid-previous-status').html('Previous HHF Parcels');
								}
							}
							if(totalCount > 0){
								$('#total-count').html('');
								$('#total-count').html(totalCount);
							}
							if(processedCount > 0){
								$('#processed-count').html('');
								$('#processed-count').html(processedCount);
							}
							if(percentComplete > 5){
								$('#progress-bar').css('width',percentCompleted);
							} else {
							
							}
							/// load next if not done.
							if(percentComplete < 100){
								loadNextResult(list,rowNum,0);
							} else {
								/// Finish it out
								if(addressCount == 0){
									$('#valid-address').html('<span class="a-circle-checked"></span>');
									$('#valid-address-status').html('All Addresses Are Valid.');
								}
								if(usHouseCount == 0){
									$('#valid-us-house').html('<span class="a-circle-checked"></span>');
									$('#valid-us-house-status').html('Found All US Districts.');
								}
								if(ohHouseCount == 0){
									$('#valid-oh-house').html('<span class="a-circle-checked"></span>');
									$('#valid-oh-house-status').html('Found All OH House Districts.');
								}
								if(ohSenateCount == 0){
									$('#valid-oh-senate').html('<span class="a-circle-checked"></span>');
									$('#valid-oh-senate-status').html('Found All OH Senate Districts.');
								}
								if(identicalCount == 0){
									$('#valid-identical-parcels').html('<span class="a-circle-checked"></span>');
									$('#valid-identical-parcels-status').html('No Matches Found.');
								}
								if(historicCount == 0){
									$('#valid-historic').html('<span class="a-circle-checked"></span>');
									$('#valid-historic-status').html('No Missing Waivers.');
								}
								
								if(hhfCount == 0){
									$('#valid-previous').html('<span class="a-circle-checked"></span>');
									$('#valid-previous-status').html('No previously HHF funded parcels detected.');

								}
								$('#progress-bar').delay(500).fadeOut()
								$('#validation-progress').delay(900).slideUp();
								
							}

						}
						function loadNextResult(list,rowNum,newRequest){
							var listURI = '/validate_parcel?list='+list+'&rowNum='+rowNum+'&newRequest='+newRequest+'&resetValidation='+window.resetImportValidation;
							
							$.get(listURI, function(response, status, xhr) {
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
								  	$("#validation-results").prepend(response);
								  }
								});




							
							console.log('Requested validation via ajax: '+listURI);
			
							//take back to top
							$('#smoothscrollLink').trigger("click");
							return;
						}
						function have_historic_waiver(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("This simply confirms that you have a waiver. You will need to provide a copy of the waiver in the supporting documents. Processing "+parcel_id).then(function() {
									    var reloadURI = '/validate_parcel?waiver=1&parcelId='+window.parcel_id;
									    
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
										  	console.log('loaded into row parcel-'+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#rerunValidation').slideDown();
										  }
									});
								}
							);
						}
						function useGISAddress(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("Are you sure you want to update this parcel to use the GIS Address?").then(function() {
									    var reloadURI = '/validate_parcel?useGISAddress=1&parcelId='+window.parcel_id;
									    
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
										  	console.log('loaded into row parcel-'+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#rerunValidation').fadeIn();
										  	if(window.rerunWarning != 1){
										  		UIkit.modal.alert('<h1>Don\'t Forget to Rerun!</h1><p>It looks like you made a change. Go ahead and finish your changes on your parcels, then click on the RERUN VALIDATION button to revalidate.');
										  		window.rerunWarning = 1;
										  	}
										  }
									});
								}
							);
						}

						function useProvidedAddress(parcel_id){
								window.parcel_id = parcel_id
								UIkit.modal.confirm("Are you sure you want to NOT use the GIS Address?").then(function() {
									    var reloadURI = '/validate_parcel?useProvidedAddress=1&parcelId='+window.parcel_id;
									    
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
										  	console.log('loaded into row parcel-'+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#rerunValidation').fadeIn();
										  	if(window.rerunWarning != 1){
										  		UIkit.modal.alert('<h1>Don\'t Forget to Rerun!</h1><p>It looks like you made a change. Go ahead and finish your changes on your parcels, then click on the RERUN VALIDATION button to revalidate.');
										  		window.rerunWarning = 1;
										  	}
										  }
									});
								}
							);
						}
						function deleteParcel(parcel_id){
	UIkit.modal.confirm("<p>Are you sure you want to delete this parcel and EVERYTHING associated with it?</p><p> This includes: <ul><li>Supporting Documents</li><li>Communications</li><li>Notes</li><li>Compliances</li><li>Cost,Request,Approved/PO,Invoice Items</li><li>Dispositions</li><li>Recaptures</li><li>Retainages</li><li>Site Visits</li></ul></p><p>If this parcel was a part of an import it will clear itself and any validations from that import. If the parcel was the only parcel on a request (po and invoice) it and its associated approvals will also be deleted.</p> <p><strong>Any transactions in accounting WILL NOT BE DELETED. Those will need to be manually reconciled/deleted.</strong></p><p>NOTE: Clicking OK will delete the parcel and rerun the validation.</p>").then(function() {
        $.get('/parcels/delete/'+parcel_id, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#rerunValidation').trigger('click');
				dynamicModalClose();

				
				UIkit.modal.alert(data['message']);
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);
				
			}else{
				UIkit.modal.alert('Something went wrong. Please contact Brian at Greenwood 360 and let him know that the parcel deletion failed and which parcel on which it failed. brian@greenwood360.com');
			}
		} );
		
    });
}
							window.askedConfirmValid = 0;
							function forceValidation(parcel_id){
								window.parcel_id = parcel_id
								if(window.askedConfirmValid == 0){
									window.askedConfirmValid = 1;
									UIkit.modal.confirm("Are you sure you want to force this parcel to a valid status?").then(function() {
										    var reloadURI = '/force_validate?parcelId='+window.parcel_id;
										    
										    $.get(reloadURI, function(response, status, xhr) {
											  if (status == "error") {
											  	if(xhr.status == "401") {
											  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
											  	} else if( xhr.status == "500"){
											  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request. Please contact support and let them know which record was requested to be processed.</p>";
											  	} else {
											  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
											  	}
											    
											    UIkit.modal.alert(msg);
											  } else {
											  	$("#parcel-"+window.parcel_id).html('');

											  	$("#parcel-"+window.parcel_id).prepend(response);
											  	console.log('loaded into row parcel-'+window.parcel_id);
											  	window.parcel_id = '';
											  	$('#rerunValidation').fadeIn();
											  	if(window.rerunWarning != 1){
											  		UIkit.modal.alert('<h1>Don\'t Forget to Rerun!</h1><p>It looks like you made a change. Go ahead and finish your changes on your parcels, then click on the RERUN VALIDATION button to revalidate.');
											  		window.rerunWarning = 1;
											  	}
											  }
										});
									}
								);
									
							} else {
								var reloadURI = '/force_validate?parcelId='+window.parcel_id;
										    
										    $.get(reloadURI, function(response, status, xhr) {
											  if (status == "error") {
											  	if(xhr.status == "401") {
											  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
											  	} else if( xhr.status == "500"){
											  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request. Please contact support and let them know which record was requested to be processed.</p>";
											  	} else {
											  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
											  	}
											    
											    UIkit.modal.alert(msg);
											  } else {
											  	$("#parcel-"+window.parcel_id).html('');

											  	$("#parcel-"+window.parcel_id).prepend(response);
											  	console.log('loaded into row parcel-'+window.parcel_id);
											  	window.parcel_id = '';
											  	$('#rerunValidation').fadeIn();
											  	if(window.rerunWarning != 1){
											  		UIkit.modal.alert('<h1>Don\'t Forget to Rerun!</h1><p>It looks like you made a change. Go ahead and finish your changes on your parcels, then click on the RERUN VALIDATION button to revalidate.');
											  		window.rerunWarning = 1;
											  	}
											  }
											});
							}
						}
						

						function correctAddress(parcel_id,lat,lon){
							dynamicModalLoad('correct_parcel_address/'+parcel_id+'?lat='+lat+'&lon='+lon);
							window.parcel_id = parcel_id;
						}
						function resolve(parcel_id,lat,lon){
							dynamicModalLoad('resolve_validation/'+parcel_id+'?lat='+lat+'&lon='+lon);
							window.parcel_id = parcel_id;
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

						
						function applyCorrection(parcel_id){
							var street_address = $('#street-address-'+parcel_id).val();
							var city = $('#city-'+parcel_id).val();
							var state_id = $('#state-id-'+parcel_id).val();
							var zip = $('#zip-'+parcel_id).val();
							var reloadURI = '/validate_parcel?updateAddress=1&parcelId='+window.parcel_id+"&street_address="+encodeURI(street_address)+"&city="+encodeURI(city)+"&state_id="+encodeURI(state_id)+"&zip="+encodeURI(zip);
									    
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
										  	console.log('loaded into row parcel-'+window.parcel_id);
										  	window.parcel_id = '';
										  	$('#rerunValidation').fadeIn();
										  	dynamicModalClose();
										  	if(window.rerunWarning != 1){
										  		UIkit.modal.alert('<h1>Don\'t Forget to Rerun!</h1><p>It looks like you made a change. Go ahead and finish your changes on your parcels, then click on the RERUN VALIDATION button to revalidate.');
										  		window.rerunWarning = 1;
										  	}
										  }
										});

									
						}

					</script>
					
					<div id="validation-results-table" class="uk-overflow-container" style="display: none;">
					<table class="uk-table uk-table-condensed uk-table-striped">
						<thead>
							<tr>
								<th><small>PARCEL ID</small></th>

								<th><small>ADDRESS</small></th>


								<th><small>LATITUDE</small></th>

								<th><small>LONGITUDE</small></th>

								<th><small>DISTRICTS</small></th>

								<th><small>UNIQUE</small></th>

								<th><small>HHF</small></th>

								<th><small>HISTORIC</small></th>
								<th><small>STATUS</small></th>
							</tr>
						</thead>
						<tbody id="validation-results">
							
									
						</tbody>
					</table>
				</div>
				@if(isset($importId))
				<script>
							window.resetImportValidation = 1;
							$('#start-validation').trigger('click');
							//automatically running the import fed in.
				</script>
						@endif
				

				{{ csrf_field() }}

				
				
			
		</div>
	@include('partials.helpers.landbank.reimbursement_steps')
@stop