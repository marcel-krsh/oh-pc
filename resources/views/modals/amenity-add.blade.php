<div class="modal-amenity-add">
    <div class="">
	    <div uk-grid> 
	  		<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">
	  			
	  			<h3>New Amenity Inspectable Area</h3>
	  			<form id="modal-amenity-form" class="uk-margin-medium-top" onsubmit="saveAmenity()">
					<fieldset class="uk-fieldset">
						<div uk-grid>
							<div class="uk-width-2-5">
								<p>Select one or more areas below.</p>
								<div class="uk-margin-small-top">
									<input id="modal-amenity-form-amenity" autofocus type="text" name="q" class="uk-width-1-1 uk-input" placeholder="Amenity Inspectable Area">
						        </div>
							</div>
							<div class="uk-width-2-5">
								<p>Select an auditor below.</p>
								<div class="uk-margin-small-top">
									<input id="modal-amenity-form-auditor"  type="text" name="q" class="uk-width-1-1 uk-input" placeholder="Auditor Name" style="width:100%;max-width:600px;outline:0">
						        </div>
							</div>
							<div class="uk-width-1-5 uk-margin-small">
								<input type="hidden" id="tmp-amenity-auditor-name" value="" />
								<input type="hidden" id="tmp-amenity-auditor-id" value="" />
								<input type="hidden" id="tmp-amenity-name" value="" />
								<input type="hidden" id="tmp-amenity-id" value="" />
								<button class="uk-button uk-button-default bordered squared" style="margin-top: 25px; line-height: 29px;" onclick="addAmenityToList()">Add to List</button>
							</div>
							<div class="uk-width-1-1 uk-margin-small">
						    	<ul id="amenity-to-create" class="uk-list">
						    		
								</ul>
						    </div>
						    <div class="uk-width-1-1 uk-margin-small">
								<button class="uk-button uk-button-primary">Save & Close</button>
							</div>
					    </div>
					</fieldset>
				</form>
	  		</div>
	    </div>
	</div>
</div>
<style>
#amenity-to-create {

}
#amenity-to-create li {
	border:1px solid #ddd;
	cursor: pointer;
	float:left;
	margin-top: 0;
    margin-right: 10px;
    padding:3px 19px 3px 17px;
    border-radius: 7px;
    margin-bottom:5px;
}
#amenity-to-create li:hover {
	padding-right:6px;
}
#amenity-to-create li:hover:after{
  display: inline-block;
  content: "\00d7"; /* This will render the 'X' */
  padding-left: 4px;
  width: 9px;
}
button.bordered {
	border:1px solid #ddd;
}
button.squared {
	border-radius: 0px;
}
</style>
<script>
	var selectAuditor = new autoComplete({
            selector: '#modal-amenity-form-auditor',
            minChars: 0,
            source: function(term, suggest){
                term = term.toLowerCase();
                var choices = {!!$auditors!!};
                var suggestions = [];
                for (i=0;i<choices.length;i++)
                    if (~(choices[i][0]+' '+choices[i][1]).toLowerCase().indexOf(term)) suggestions.push(choices[i]);
                suggest(suggestions);
            },
            renderItem: function (item, search){
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&amp;');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                return '<div class="autocomplete-suggestion" data-auditor="'+item[1]+'" data-name="'+item[0]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item){
                $('#modal-amenity-form-auditor').val(item.getAttribute('data-name'));

                $("#tmp-amenity-auditor-id").val(item.getAttribute('data-auditor'));
                $("#tmp-amenity-auditor-name").val(item.getAttribute('data-name'));

                //$("#new-amenity-auditor").val(item.getAttribute('data-auditor'));
            }
        });

     var selectAmenities = new autoComplete({
            selector: '#modal-amenity-form-amenity',
            minChars: 1,
            source: function(term, suggest){
                term = term.toLowerCase();
                // var choices = [['Bedroom', '1'], ['Stairs', '2'], ['Living Room', '3']];
                var choices = {!!$amenities!!};
                var suggestions = [];
                for (i=0;i<choices.length;i++)
                    if (~(choices[i][0]+' '+choices[i][1]).toLowerCase().indexOf(term)) suggestions.push(choices[i]);
                suggest(suggestions);
            },
            renderItem: function (item, search){
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&amp;');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                return '<div class="autocomplete-suggestion" data-name="'+item[0]+'" data-amenity="'+item[1]+'" data-val="'+search+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item){     
                $('#modal-amenity-form-amenity').val(item.getAttribute('data-name'));      

                // save temporary value until combination is added
                $("#tmp-amenity-id").val(item.getAttribute('data-amenity'));
                $("#tmp-amenity-name").val(item.getAttribute('data-name'));

                //$("#new-amenity").val(item.getAttribute('data-amenity'));
            }
        });

</script>
<script>

	function addAmenityToList() {
		event.preventDefault();

		var amenityId = $('#tmp-amenity-id').val();
        var amenityName = $('#tmp-amenity-name').val();
        var auditorId = $('#tmp-amenity-auditor-id').val();
        var auditorName = $('#tmp-amenity-auditor-name').val();

        if(amenityId.length) {
        	if(auditorName.length){
        		$("ul#amenity-to-create").append('<li onclick="$(this).remove();" data-auditor="'+auditorId+'" data-amenity="'+amenityId+'">'+amenityName+' :: '+auditorName+'</li>');
        	}else{
        		$("ul#amenity-to-create").append('<li onclick="$(this).remove();" data-auditor="'+auditorId+'" data-amenity="'+amenityId+'">'+amenityName+'</li>');
        	}
        }
	}

	function saveAmenity() {
		event.preventDefault();

		var newAmenities = [];
		var dataAuditor;
		var dataAmenity;

		$('ul#amenity-to-create li').each(function(index, element){
			dataAuditor = element.getAttribute('data-auditor');
			dataAmenity = element.getAttribute('data-amenity');
			
			newAmenities.push({
				"amenity_id":dataAmenity,
				"auditor_id":dataAuditor
			});
		});

		var spinner = '<div style="height:200px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
        $('.modal-amenity-add').html(spinner);
		
		$.post('/modals/amenities/save', {
				'project_id' : '{{$data['project_id']}}',
				'building_id' : '{{$data['building_id']}}',
				'unit_id' : '{{$data['unit_id']}}',
				'new_amenities' : newAmenities,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
			@if($data['project_id'])

				// reload list of buildings
				projectDetails({{$data['project_id']}}, {{$data['project_id']}}, data.length, 1);
				dynamicModalClose();

			@else 

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
					newarea = newarea.replace(/areaName/g, area.name); // missing
					newarea = newarea.replace(/areaStatus/g, area.status);  // missing
					newarea = newarea.replace(/areaAuditorId/g, area.auditor_id);  // missing
					newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);  // missing
					newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);  // missing
					newarea = newarea.replace(/areaCompletedIcon/g, area.completed_icon);  
					newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);  // missing
					newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
					newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
					newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
					newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
					newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
					newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);

					newarea = newarea.replace(/areaDataAudit/g, area.audit_id);
					newarea = newarea.replace(/areaDataBuilding/g, area.building_id);
					newarea = newarea.replace(/areaDataArea/g, area.unit_id);
					newarea = newarea.replace(/areaDataAmenity/g, area.id);

					areas = areas + newarea.replace(/areaAuditorColor/g, area.auditor_color);

					
				});

				// data.forEach(function(area) {
				// 	newarea = inspectionAreaTemplate;
				// 	newarea = newarea.replace(/areaContext/g, context);
				// 	newarea = newarea.replace(/areaRowId/g, area.id);
				// 	newarea = newarea.replace(/areaName/g, area.name);
				// 	newarea = newarea.replace(/areaStatus/g, area.status);
				// 	newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);
				// 	newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);

				// 	newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);
				// 	newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
				// 	newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
				// 	newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
				// 	newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
				// 	newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
				// 	newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);
					
				// 	newarea = newarea.replace(/areaDataAudit/g, area.audit_id);
				// 	newarea = newarea.replace(/areaDataBuilding/g, area.building_id);
				// 	newarea = newarea.replace(/areaDataArea/g, area.unit_id);
				// 	newarea = newarea.replace(/areaDataAmenity/g, area.id);

				// 	areas = areas + newarea.replace(/areaAuditorColor/g, area.auditor_color);
				// });

				$('#'+mainDivId).html(inspectionMainTemplate);
				$('#'+mainDivId+' .inspection-areas').html(areas);
				$('#'+mainDivContainerId).fadeIn( "slow", function() {
				    // Animation complete
				    console.log("Area list updated");
				  });

				dynamicModalClose();

			@endif
			} 
		);

		
	}

</script>