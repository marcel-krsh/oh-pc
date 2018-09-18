
<script>
	resizeModal(65);
</script>

<div uk-grid>

	<div class="uk-width-1-1 uk-animation-fade">	
		@if($totalResolutions == 1)
		<h2 class="uk-margin-top">Looks Like I Found a Matching Parcel.</h2>
		@elseif($totalResolutions > 1)
		<h2 class="uk-margin-top">Looks Like I Found Some Matching Parcels.</h2>
		@else
		<h2 class="uk-margin-top">That's weird... I can't find any of the noted matches.</h2>
		@endif
@if($totalResolutions > 0)
		<hr style="clear:both;" />
			<p class="uk-margin-bottom">Please review those parcels I found that were within 500ft of your parcel. If the matching parcels are a part of shared parcel, you can select "Shared Parcel" for that matching parcel. </p>
			<p>Those with a different Parcel ID are likely a different address, we just need you to confirm that's the case.</p>
			<hr />
				
	</div>
	
	

	
	
	<div class="uk-width-1-1 uk-margin-top"  style="margin-bottom:20px">
		
		<h3>POTENTIAL MATCH<?php if($totalResolutions > 1){ echo "ES"; } ?></h3>
		<hr />
	</div>
	
			
</div><?php $i = 0; 
			//dd($resolutionOutput);
			?>
			<script>window.resolutionCount{{$parcel->id}} = {{$totalResolutions}};</script>
			@forEach($resolutionOutput as $d)
			<div class="uk-grid uk-animation-fade" id="resolution-{{$d[$i]['resolution_id']}}">
				<div class="uk-width-1-1" style="margin-bottom:20px">
					
					@if(isset($d[$i]['resolution_system_notes']))
						<p>{{$d[$i]['resolution_system_notes']}}</p>
					@endif
					
					@if(isset($d[$i]['shared_parcels']))
						@if(count($d[$i]['shared_parcels'])>0)
						<p>It also appears this parcel may be a part of this group:</p>
								<ul>
									@forEach($d[$i]['shared_parcels'] as $shared)<br />
										<li>PARCEL ID: <?php echo $shared->parcel_id; ?></li>
									@endForEach
								</ul>
						@endif
							
					@endif

				</div>
				<div class="uk-width-1-3@m">

					@if(isset($d[$i]['resolution_type']) && isset($d[$i]['lb_resolved']))
						@if($d[$i]['lb_resolved'] == 0)
						
				        	@if($d[$i]['resolution_type'] == "parcels")
				        	<ul class="uk-list">
				            <li>
				            	<a onclick="
									resolveItem({{$d[$i]['resolution_id']}},'NO, THIS IS NOT THE MATCHED PARCEL',1,{{$parcel->id}});
									window.resolutionCount{{$parcel->id}}--;
									
									if(window.resolutionCount{{$parcel->id}} == 0){
										UIkit.modal.alert('<h2>Finished with that parcel!</h2><p> Next? Click \'OK\' Done?</p><a onclick=\'rerunNow();\' class=\'uk-button uk-button-default\'>CLICK HERE TO RE-RUN VALIDATION?</a></p><p>Or click OK to close window.</p><script>function rerunNow(){$( \'#rerunValidation\').trigger(\'click\'); $(\'.uk-modal-close-default\').trigger(\'click\');}</script>');
										dynamicModalClose(); 
										}">
										NO, THIS IS NOT THE MATCHED PARCEL
										</a>
				            </li>
					        <li>
					            	<a onclick="resolveItem({{$d[$i]['resolution_id']}},'THESE PARCELS ARE A GROUP',2,{{$parcel->id}});
										window.resolutionCount{{$parcel->id}}--;
										
										if(window.resolutionCount{{$parcel->id}} == 0){
											UIkit.modal.alert('<h2>Finished with that parcel!</h2><p> Next? Click \'OK\' Done?</p><a onclick=\'rerunNow();\' class=\'uk-button uk-button-default\'>CLICK HERE TO RE-RUN VALIDATION?</a></p><p>Or click OK to close window.</p><script>function rerunNow(){$( \'#rerunValidation\').trigger(\'click\'); $(\'.uk-modal-close-default\').trigger(\'click\');}</script>');
											dynamicModalClose(); 
											}
									">THESE PARCELS ARE A GROUP<br /><small>(CREATES A NEW GROUP AND ADDS THESE PARCELS TO IT)</small></a></li>
									@if(isset($d[$i]['shared_parcels']))
										@if(count($d[$i]['shared_parcels'])>0)
										<li><a onclick="resolveItem({{$d[$i]['resolution_id']}},'THIS IS A PART OF THE GROUP LISTED',4,{{$parcel->id}});dynamicModalClose();
										window.resolutionCount{{$parcel->id}}--;
										if(window.resolutionCount{{$parcel->id}} == 0){
											UIkit.modal.alert('<h2>Finished with that parcel!</h2><p> Next? Click \'OK\' Done?</p><a onclick=\'rerunNow();\' class=\'uk-button uk-button-default\'>CLICK HERE TO RE-RUN VALIDATION?</a></p><p>Or click OK to close window.</p><script>function rerunNow(){$( \'#rerunValidation\').trigger(\'click\'); $(\'.uk-modal-close-default\').trigger(\'click\');}</script>');
											dynamicModalClose(); 
											}
										">THIS IS A PART OF THE GROUP LISTED </a></li>
										@endIf
									@endIf
									<li><a onclick="deleteParcel({{$parcel->id}});
										
										$('#rerunValidation').slideDown();
											
										">DELETE THIS PARCEL<br ><small>IT IS A DUPLICATE</small></a></li>
								
								@else
								
									<li><a onclick="resolveItem({{$d[$i]['resolution_id']}},'NO, THIS IS NOT THE MATCHED HHF PARCEL.',5,{{$parcel->id}});
									window.resolutionCount{{$parcel->id}}--;">NO, THIS IS NOT THE MATCHED HHF PARCEL</a></li>


									<li><a onclick="
										deleteParcel({{$parcel->id}});
										
										$('#rerunValidation').slideDown();
										
									">DELETE THIS PARCEL<br /><small>IT IS A PREVIOUSLY FUNDED HHF PROPERTY</small></a></li>
								
								@endIF
							</ul>
						
						@elseif($d[$i]['lb_resolved'] == 1)
						<div class="uk-alert uk-alert-success"><span class="a-circle-checked"></span> {{$d[$i]['resolution_lb_notes']}}</div>
						@endif
					@endif

				</div>
				<div class="uk-width-1-3@m">
					<div class="uk-width-1-1 uk-badge">
						SUBMITTED PARCEL
					</div>
							<figure class="uk-overlay uk-overlay-hover uk-align-center">
									<img src="https://maps.googleapis.com/maps/api/streetview?size=400x400&location={{$lat}},{{$lon}}
											&fov=90&pitch=10
				                            &key=AIzaSyAMB5fHlZyAet2TnsuU3bBX7miYyDMBLSg" class="uk-overlay-scale uk-align-center">
									<figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">OPEN IN STREET VIEW?</figcaption>
									<a class="uk-position-cover uk-align-center" target="_blank" href="http://maps.google.com/maps?q=&layer=c&cbll={{$lat}},{{$lon}}&cbp=11,0,0,0,0"></a>
							</figure>
						
					
						<dl class="uk-description-list-vertical uk-description-list-line">
							<dt><small><strong>PARCEL ID</strong></small> </dt>
							
							<dd>{{$parcel->parcel_id}}</dd>
						
							<dt><small><strong>ADDRESS:</strong></small> </dt>
							<dd>
							{{$parcel->street_address}}<br />
							{{$parcel->city}}, OH {{$parcel->zip}}
							</dd>
						</dl>
								
					</div>
				<div class="uk-width-1-3@m">
				<div class="uk-width-1-1 uk-badge uk-badge-warning">
						POTENTIALLY DUPLICATE PARCEL
					</div>
				@if(isset($d[$i]['matching_parcel_info']->latitude) && isset($d[$i]['matching_parcel_info']->longitude))
					<figure class="uk-overlay uk-overlay-hover uk-align-center">
						<img src="https://maps.googleapis.com/maps/api/streetview?size=400x400&location={{$d[$i]['matching_parcel_info']->latitude}},{{$d[$i]['matching_parcel_info']->longitude}}
								&fov=90&pitch=10
			                    &key=AIzaSyAMB5fHlZyAet2TnsuU3bBX7miYyDMBLSg" class="uk-overlay-scale uk-align-center">
						<figcaption class="uk-overlay-panel  uk-overlay-background  uk-overlay-bottom uk-overlay-slide-bottom uk-align-center">OPEN IN STREET VIEW?</figcaption>
						<a class="uk-position-cover uk-align-center" target="_blank" href="http://maps.google.com/maps?q=&layer=c&cbll={{$d[$i]['matching_parcel_info']->latitude}},{{$d[$i]['matching_parcel_info']->longitude}}&cbp=11,0,0,0,0"></a>
					</figure>
				@else
				<small class="uk-margin-top uk-margin-bottom uk-text-center">NO STREET VIEW AVAILABLE</small>
				
				@endif
				<dl class="uk-description-list-vertical uk-description-list-line">
					<dt><small><strong>
							@if(isset($d[$i]['resolution_type']))
								@if($d[$i]['resolution_type'] == "parcels")
									PARCEL ID
								@else
									FILE NUMBER
								@endif
							@endif
							</strong></small>
					</dt>
					<dd>
							@if(isset($d[$i]['resolution_type']))
								@if($d[$i]['resolution_type'] == "parcels")
									{{$d[$i]['matching_parcel_info']->parcel_id}}
								@else
								<?php echo $d[$i]['matching_parcel_info']->{"File Number"}; ?>
								@endif
							@endIf
					</dd>
					<dt><strong><small>ADDRESS</small></strong>
					</dt>
					<dd>		
							@if(isset($d[$i]['resolution_type']))
								@if($d[$i]['resolution_type'] == "parcels")
								{{$d[$i]['matching_parcel_info']->street_address}}<br />
								{{$d[$i]['matching_parcel_info']->city}}, OH {{$d[$i]['matching_parcel_info']->zip}}


								@else
								<?php echo $d[$i]['matching_parcel_info']->street_address; ?><br />
								<?php echo $d[$i]['matching_parcel_info']->{"Property City"}; ?>, <?php echo $d[$i]['matching_parcel_info']->{"Property State"}; ?> <?php echo $d[$i]['matching_parcel_info']->{"Property Zip"}; ?>
								@endif
							@endif
					</dd>
				</dl>

				</div>

					
			</div>
			@endForEach
		</form>
		<hr class="uk-margin-large-top" />
@endIf
	</div>



</div>
@if(isset($message))
	@if(strlen($message)>0)
		<script>
			UIkit.modal.alert('{{$message}}');
			
		</script>
	@endif
@endIf
