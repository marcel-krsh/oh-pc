
	<script>
	resizeModal(60);
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-2@m uk-width-1-1@s">
				<H3>Correct Information for {{$parcel->parcel_id}}</H3>
				<form id='parcelForm' class="uk-form-horizontal" role="form">
					<input type="hidden" name="parcel" value="{{$parcel->id}}">
					
					<div class="uk-form-row">
                        <label class="uk-form-label" for="street_address">Street Address</label>
                        <div class="uk-form-controls">
                        	<input class="uk-input uk-form-small uk-width-1-1" type="text" id="street-address-{{$parcel->id}}" name="street_address" value="{{$parcel->street_address}}">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="city">City</label>
                        <div class="uk-form-controls">
                        	<input class="uk-input uk-form-small uk-width-1-1" type="text" id="city-{{$parcel->id}}" name="city" value="{{$parcel->city}}">
                        </div>
                    </div>
                    
					<div class="uk-form-row">
                        <label class="uk-form-label" for="state">State</label>
                        <div class="uk-form-controls">
                            <div class="uk-button uk-button-default uk-form-select" data-uk-form-select>
    							<span></span>
							    <select name="state_id" id="state-id-{{$parcel->id}}" class="uk-select uk-width-1-1 uk-form-small">
	                                <option>Select a state</option>
	                            	@foreach($states as $state)
	                                <option value="{{$state->id}}" @if($parcel->state_id == $state->id) selected @endif >{{$state->state_name}}</option>
									@endforeach
	                            </select>
	                        </div>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="zip">Zip</label>
                        <div class="uk-form-controls">
                        	<input class="uk-input uk-form-small uk-width-1-1" type="text" id="zip-{{$parcel->id}}" name="zip" value="{{$parcel->zip}}">
                        </div>
                    </div>
					
				</form>
			</div>
			<div class="uk-width-1-2@m uk-width-1-1@s">
			<figure class="uk-overlay uk-overlay-hover uk-align-center">
					<img src="https://maps.googleapis.com/maps/api/streetview?size=400x400&location={{$lat}},{{$lon}}
							&fov=90&pitch=10
                            &key=AIzaSyAMB5fHlZyAet2TnsuU3bBX7miYyDMBLSg" class="uk-overlay-scale uk-align-center">
					<figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">OPEN IN STREET VIEW?</figcaption>
					<a class="uk-position-cover uk-align-center" target="_blank" href="http://maps.google.com/maps?q=&layer=c&cbll={{$lat}},{{$lon}}&cbp=11,0,0,0,0"></a>
				</figure>
			</div>
		</div>
	</div> 
	<hr>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div id="applicant-info-update">
				<div class="uk-grid uk-margin">
					<div class="uk-width-1-3 uk-push-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <i uk-icon="times-circle" class=" uk-margin-left"></i> CANCEL</a>
					</div>
					<div class="uk-width-1-3 uk-push-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="applyCorrection({{$parcel->id}});"> <i class="uk-margin-left"></i> UPDATE &nbsp;</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
