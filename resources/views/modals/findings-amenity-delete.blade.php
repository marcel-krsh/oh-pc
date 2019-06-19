<div class="modal-amenity-delete">
    <div class="">
	    <div uk-grid>
	  		<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">

	  			<h3>Delete this amenity?</h3>
	  			<form id="modal-amenity-form" class="uk-margin-medium-top" onsubmit="saveDeleteAmenity()">
					<div class="" uk-grid>
						<div class="uk-width-1-1">
							<div class="" style="min-height:3em;">
								<input id="delete-comment" type="text" class="uk-textarea" name="delete-comment" placeholder="Enter a comment here."/>
							</div>
						</div>
						<div class="uk-width-1-2">
						</div>
						<div class="uk-width-1-4 uk-text-right">
							<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose(2)"><span uk-icon="times-circle"></span> CANCEL</a>
						</div>
						<div class="uk-width-1-4 uk-text-right">
							<button class="uk-button uk-button-primary uk-width-1-1">DELETE</button>
						</div>
					</div>
				</form>
	  		</div>
	    </div>
	</div>
</div>
<script>

	function saveDeleteAmenity() {
		event.preventDefault();
		element = "{{ $element }}";
		var comment = $('#delete-comment').val();
		console.log("deleting amenity");
		$.post('/modals/amenities/delete', {
			'comment' : comment,
			'audit_id' : '{{ $audit_id }}',
			'building_id' : '{{ $building_id }}',
			'unit_id' : '{{ $unit_id }}',
			'amenity_id' : '{{ $amenity_id }}',
			'element' : '{{ $element }}',
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			dynamicModalClose(2);
			if(element == 'site') {
				filterSiteAmenities({{ $audit_id }});
			} else if(element == 'unit'){
				filterUnitAmenities({{ $unit_id }});
			} else {
			filterBuildingAmenities({{ $building_id }});
		}
		});
	}

</script>