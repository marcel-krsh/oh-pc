@extends('layouts.allita')

@section('content')
	<script>
		// Update Tab Title - note the refresh button in tab text script.
		$('#list-tab-text').html(' : Validate Your Parcels');

		$('#detail-tab-1-icon').attr('uk-icon','check-circle');
		// display the tab
		$('#list-tab').show();
	</script>

	<script>
		UIkit.modal.alert('<h1 >Let\'s validate!</h1><p>I am about to make everyone\'s life a little bit easier :). What used to take hours – and sometimes days – we\'re going to do in just a minute or two.</p><p>That said, any parcels that I can\'t validate, you will need to address before submitting them for reimbursement.</p>');
	</script>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			
				
				<div class="uk-article">
					<div class="uk-block uk-block-primary uk-dark uk-light">
                        <div class="uk-container">
                        	<div class="uk-grid">
	                        	<div class="uk-width-1-5@m">
	                        		<p class="uk-text-center"><span class="a-circle-checked blue-shadow" style="font-size: 150px;"></span></p>
	                        	</div>
	                            <div class="uk-width-4-5@m"><h1 class="blue-shadow">VALIDATION</h1><hr style="border-top: 2px dotted;" /><br />
		                            <div class="uk-grid ">
		                            	<div class="uk-width-1-3@m  uk-row-first">
		                                    <div class="uk-panel">
		                                        <h3>Ready?</h3><p> Select which import to validate and then click the "START VALIDATION" button. You can also choose "ALL" to validate all parcels that have not yet been validated.</p>
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>
		                                        	<span id="valid-address"><span class="a-circle"></span></span> 
		                                        	<span id="valid-address-status">Validate Address</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-us-house"><span class="a-circle"></span></span> 
		                                        	<span id="valid-us-house-status">US House District</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-oh-house"><span class="a-circle"></span></span> 
		                                        	<span id="valid-oh-house-status">OH House District</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-oh-senate"><span class="a-circle"></span></span> 
		                                        	<span id="valid-oh-senate-status">OH Senate District</span>
		                                        </p>
		                                        
		                                    </div>
		                                </div>
		                                <div class="uk-width-1-3@m">
		                                    <div class="uk-panel">
		                                        <p>
		                                        	<span id="valid-identical-parcels"><span class="a-circle"></span></span> 
		                                        	<span id="valid-identical-parcels-status">Identical Parcels</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-historic"><span class="a-circle"></span></span> 
		                                        	<span id="valid-historic-status">Historic Parcels</span>
		                                        </p>
		                                        <p>
		                                        	<span id="valid-previous"><span class="a-circle"></span></span> 
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

				<div class="uk-grid">
					<div class="uk-width-1-1">
					<select id="validation-list" class="uk-select uk-align-center uk-width-1-1 uk-width-1-3@m uk-margin-top uk-margin-bottom">
						<option value="0">SELECT IMPORT TO VALIDATE</option>
						<option value="all">[ ALL UNRUN IMPORTS ]</option>
						@forEach($importList as $data)
							<option value="{{$data->id}}">[ IMPORT {{$data->id}} ] {{date('F jS, Y \a\t h:i:s A',strtotime($data->created_at))}}</option>
						@endForEach
					</select>
					<div id="validation-progress" class="uk-progress uk-progress-striped uk-active" style="display: none;">
                                <div class="uk-progress-bar" style="width: 5%;"><p align="center"><span id="processed-count">0</span> / <span id="total-count"><span class="a-refresh-2 uk-icon-spin"></span></span></p></div>
                            </div>
					<a id="start-validation" class="uk-margin-top uk-button uk-button-default uk-button-large uk-width-1-1 uk-width-1-3@m uk-align-center" onclick="startValidation();">START VALIDATION</a>
					</div>
				</div>
					<style type="text/css">
						.uk-progress-bar {
							background: #005186;
						}
					</style>
					<script type="text/javascript">
						function startValidation(){
							if($('#validation-list').val() != 0){
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
								loadNextResult($('#validation-list').val(),0);
							} else {
								UIkit.modal.alert('<h2>Please select an import list to run</h2>');
							}
						}

						function updateTotals(list,record,addressCount,usHouseCount,ohHouseCount,ohSenateCount,identicalCount,historicCount,hhfCount,totalCount,processedCount,percentComplete,rowNum){
							// update values
							if(processedCount == 1){
								//only run this once
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
									$('#valid-identical-parcels-status').html('Identical Parcel');
								} else {
									$('#valid-identical-parcels-status').html('Identical Parcels');
								}
							}if(historicCount > 0){
								$('#valid-historic').html('');
								$('#valid-historic').html(historicCount);
								$('#valid-historic-status').html('');
								if(historicCount == 1){
									$('#valid-historic-status').html('Historic Parcel');
								} else {
									$('#valid-historic-status').html('Historic Parcels');
								}
							}
							if(hhfCount > 0){
								$('#valid-hhf').html('');
								$('#valid-hhf').html(hhfCount);
								$('#valid-hhf-status').html('');
								if(hhfCount == 1){
									$('#valid-hhf-status').html('Previous HHF Parcel');
								} else {
									$('#valid-hhf-status').html('Previous HHF Parcels');
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
								$('.uk-progress-bar').css('width',percentComplete);
							}
							/// load next if not done.
							if(percentComplete < 100){
								loadNextResult(list,rowNum);
							}
						}
						function loadNextResult(list,rowNum){
							var listURI = '/validate_parcel?list='+list+'&rowNum='+rowNum;
							$('#validation-results').load(listURI, function(response, status, xhr) {
								  if (status == "error") {
								  	if(xhr.status == "401") {
								  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
								  	} else if( xhr.status == "500"){
								  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your import. Please contact support and let them know which import you were validating, and what record it last processed.</p>";
								  	} else {
								  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
								  	}
								    
								    UIkit.modal.alert(msg);
								  }
								}).prependTo("table > tbody");
			console.log('Requested List via ajax: '+listURI);
			
			//take back to top
			$('#smoothscrollLink').trigger("click");
			return;
						}
					</script>
					
					<div id="validation-results-table" class="uk-overflow-container" style="display: none;">
					<table class="uk-table uk-table-condensed uk-table-striped">
						<thead>
							<tr>
								<th><small>PARCEL ID</small></th>

								<th><small>ADDRESS</small></th>


								<th><small>LAT</small></th>

								<th><small>LON</small></th>

								<th><small>DISTRICTS</small></th>

								<th><small>UNIQUE</small></th>

								<th><small>NON-HISTORIC</small></th>

								<th><small>HHF</small></th>
								
							</tr>
						</thead>
						<tbody id="validation-results">
							
									
						</tbody>
					</table>
				</div>

				

				{{ csrf_field() }}

				
				
			
		</div>
	@include('partials.helpers.landbank.reimbursement_steps')
@stop