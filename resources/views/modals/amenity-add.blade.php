<div class="modal-amenity-add">
    <div class="">
	    <div uk-grid> 
	  		<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">
	  			
	  			<h3>New Amenity Inspectable Area</h3>

	  			<form id="modal-amenity-form" class="uk-margin-small-top">
					<fieldset class="uk-fieldset">
						<div class="uk-margin-small-top uk-grid">
				            <input type="text" class="uk-input" value=""  placeholder="Name"/>
				        </div>
						<div class="uk-margin uk-grid uk-grid-small uk-margin-small-top" style="margin-left:0;">
							<div class="uk-width-1-1 uk-padding-remove">
					            <select class="uk-select">
					                <option>Auditor name 1</option>
					                <option>Auditor name 2</option>
					                <option>Auditor name 3</option>
					            </select>
					        </div>
				        </div>
				        <button class="uk-button uk-button-default" onclick="saveAmenity()">Submit</button>
					</fieldset>
				</form>
	  		</div>
	    </div>
	</div>
</div>
<script>
	function saveAmenity() {
		event.preventDefault();
		
		var spinner = '<div style="height:200px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
        $('.modal-amenity-add').html(spinner);

		var form = $('#modal-amenity-form');
		
		$.post('/modals/amenities/save', {
				'project_id' : '{{$data['project_id']}}',
				'building_id' : '{{$data['building_id']}}',
				'unit_id' : '{{$data['unit_id']}}',
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				// locate where to update data
				var mainDivId = $('.inspection-areas').parent().attr("id"); 
				var mainDivContainerId = $('#'+mainDivId).parent().attr("id"); 

				// also get context
				var context = $('.inspection-areas').first().attr("data-context");

				// show spinner
				$('#'+mainDivId).html(spinner);
				
				// add a row in .inspection-areas
				var inspectionMainTemplate = $('#inspection-areas-template').html();
				var inspectionAreaTemplate = $('#inspection-area-template').html();

				var areas = '';
				var newarea = '';

				data.forEach(function(area) {
					newarea = inspectionAreaTemplate;
					newarea = newarea.replace(/areaContext/g, context);
					newarea = newarea.replace(/areaRowId/g, area.id);
					newarea = newarea.replace(/areaName/g, area.name);
					newarea = newarea.replace(/areaStatus/g, area.status);
					newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);
					newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);

					newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);
					newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
					newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
					newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
					newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
					newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
					newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);

					areas = areas + newarea.replace(/areaAuditorColor/g, area.auditor_color);
				});

				$('#'+mainDivId).html(inspectionMainTemplate);
				$('#'+mainDivId+' .inspection-areas').html(areas);
				$('#'+mainDivContainerId).fadeIn( "slow", function() {
				    // Animation complete
				    console.log("Area list updated");
				  });

				dynamicModalClose();
			} 
		);

		
	}
</script>