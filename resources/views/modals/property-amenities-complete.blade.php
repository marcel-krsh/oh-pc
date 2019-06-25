<div id="property-complete-confirm">
    <div class="">
	    <div uk-grid>
	  		<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">
	  			<h3>Mark Building Complete Options</h3>
	  			<form id="modal-amenity-form" class="uk-margin-medium-top" onsubmit="markUnitAmenityComplete()">
					<div class="uk-margin-remove uk-grid" uk-grid="">
						<input type="hidden" name="building-complete-baseLink" id="building-complete-baseLink">
						<button class="uk-button uk-button-primary uk-width-1-1" onclick="markBuildingComplete(1);">Mark Building Amenities as Completed
						</button>
						<button class="uk-button uk-button-primary  uk-width-1-1 " onclick="markBuildingComplete(2)">Mark Building Amenities and its Units as Completed</button>
					</div>
				</form>
	  		</div>
	    </div>
	</div>
</div>


<script>

	function markBuildingComplete(level = 0) {
		event.preventDefault();
		var url = 'amenities/{{ $amenity_id }}/audit/{{ $audit_id }}/building/{{ $building_id }}/unit/{{ $unit_id }}/{{ $toplevel }}/complete/'+level;
		console.log("marking propery amenities complete");
		$.post(url, {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			dynamicModalClose(2);
			if(data==0){
				UIkit.modal.alert(data,{stack: true});
			} else {
				if(data.status == 'complete'){
					if(toplevel == 1){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					} else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				} else{
					if(toplevel == 1){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					} else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				}
			}
			loadTypeView = '';
			console.log(data.status);
			loadTypes();
		});
	}

</script>