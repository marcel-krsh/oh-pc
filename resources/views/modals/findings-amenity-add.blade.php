<div class="modal-amenity-add">
	<div class="">
		<div uk-grid>
			<div class="uk-width-1-1 uk-padding-remove uk-margin-small-top">

				<h3>New Amenity Inspectable Area</h3>
				<form id="modal-amenity-form" class="uk-margin-medium-top">
					<fieldset class="uk-fieldset">
						<div uk-grid>
							<div class="uk-width-2-5">
								<p>Select one or more areas below.</p>
								<div class="uk-margin-small-top">
									<input id="modal-amenity-form-amenity"  type="text" name="q" class="uk-width-1-1 uk-input" placeholder="Amenity Inspectable Area">
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
							<div class="uk-width-1-1 uk-margin-small uk-text-right">
								<button class="uk-button uk-button-primary" onclick="saveAmenity(event)">Save & Close</button>
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
	.autocomplete-suggestions {
		max-height: 400px;
		overflow-y: auto;
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
			var choices = {!! $auditors !!};
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
		minChars: 0,
		source: function(term, suggest){
			term = term.toLowerCase();
                // var choices = [['Bedroom', '1'], ['Stairs', '2'], ['Living Room', '3']];
                var choices = {!! $amenities !!};
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

          		$('#modal-amenity-form-amenity').val('');
          		$('#modal-amenity-form-auditor').val('');
          	}

          	function saveAmenity(event) {
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

          		if(newAmenities.length == 0){
          			dynamicModalClose(2);
          		}else{

          			var spinner = '<div style="height:200px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
          			$('.modal-amenity-add').html(spinner);
          			building_id = "{{ $data['building_id'] }}";
          			unit_id = "{{ $data['unit_id'] }}";
          			audit_id = "{{ $data['audit_id'] }}";
          			$.post('/modals/amenities/save', {
          				'project_id' : '{{ $data['project_id'] }}',
          				'audit_id' : '{{ $data['audit_id'] }}',
          				'building_id' : '{{ $data['building_id'] }}',
          				'unit_id' : '{{ $data['unit_id'] }}',
          				'new_amenities' : newAmenities,
          				'amenity_id' : 0,
          				'_token' : '{{ csrf_token() }}'
          			}, function(data) {
          				dynamicModalClose(2)
          				if(building_id != '' && unit_id == '')
          					filterBuildingAmenities(building_id);
          				else if(unit_id != '')
          					filterUnitAmenities(unit_id);
          				else if(unit_id == '' && building_id == '')
          					filterSiteAmenities(audit_id)
          			}
          			);
          		}
          	}

          </script>