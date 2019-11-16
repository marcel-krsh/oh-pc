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
							<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
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
		var comment = $('#delete-comment').val();

		console.log("deleting amenity");

		$.post('/modals/amenities/delete', {
			'comment' : comment,
			'audit_id' : '{{$audit_id}}',
			'building_id' : '{{$building_id}}',
			'unit_id' : '{{$unit_id}}',
			'amenity_id' : '{{$amenity_id}}',
			'element' : '{{$element}}',
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			console.log("processed "+data.element);
			$('#'+$.trim(data.element)).remove();

			@if($building_id || $unit_id)

			if(data.auditor.unit_id == null && data.auditor.building_id != null){
            	console.log('updating building auditors ');

            	if($('#building-auditors-'+data.auditor.building_id).hasClass('hasAuditors')){
            		
            		// we don't know if/which unit is open
                	var unitelement = 'div[id^=unit-auditors-]  .uk-slideshow-items li.uk-active > div';

	                $(unitelement).html('');
	                $.each(data.auditor.unit_auditors, function(index, value){
	                	var newcontent = '<div id="unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+data.auditor.audit_id+', '+data.auditor.building_id+', '+data.auditor.unit_id+', \'unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'\')">'+value.initials+'</div>';
	                	$(unitelement).append(newcontent);
	                });
            	}else{
            		// we don't know if/which unit is open
                	var unitelement = 'div[id^=unit-auditors-]';

                	if(data.auditor.unit_auditors.length > 0){
		                $(unitelement).html('');
		                $.each(data.auditor.unit_auditors, function(index, value){
		                	var newcontent = '<div id="unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+data.auditor.audit_id+', '+data.auditor.building_id+', '+data.auditor.unit_id+', \'unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'\')">'+value.initials+'</div>';
		                	$(unitelement).append(newcontent);
		                });
                	}
            	}       

                var buildingelement = '#building-auditors-'+data.auditor.building_id+' .uk-slideshow-items li.uk-active > div';
               
                if(data.auditor.building_auditors.length > 0){
	                $(buildingelement).html('');
	                $.each(data.auditor.building_auditors, function(index, value){
	                	var newcontent = '<div id="unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+data.auditor.audit_id+', '+data.auditor.building_id+', 0, \'unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'\')">'+value.initials+'</div>';
	                	$(buildingelement).append(newcontent);
	                });
	            }
            }else{
			// update unit auditor's list
				console.log('units auditor list update');

				// var newcontent = '<div id="building-audits-'+area.auditor_id+'-avatar-1" uk-tooltip="pos:top-left;title:'+area.auditor_name+';" title="" aria-expanded="false" class="auditor-badge auditor-badge-'+area.auditor_color+' use-hand-cursor no-float" onclick="swapAuditor('+area.auditor_id+', '+area.audit_id+', '+area.building_id+', '+area.unit_id+', \'building-audits-'+area.auditor_id+'-avatar-1\')">'+area.auditor_initials+'</div>';

				// // var newcontent = '<div class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+area.auditor_name+';" title="" aria-expanded="false" class="auditor-badge '+area.auditor_color+' no-float">'+area.auditor_initials+'</div>';

 

            	var unitelement = '#unit-auditors-'+data.auditor.unit_id+' .uk-slideshow-items li.uk-active > div';

            	if(data.auditor.unit_auditors.length > 0){
	                $(unitelement).html('');
	                //console.log(unitelement);
	                $.each(data.auditor.unit_auditors, function(index, value){
	                	var newcontent = '<div id="unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+data.auditor.audit_id+', '+data.auditor.building_id+', '+data.auditor.unit_id+', \'unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'\')">'+value.initials+'</div>';
	                	$(unitelement).append(newcontent);

	                	if($('#unit-auditors-'+data.auditor.unit_id).hasClass('hasAuditors')){
	                		$(buildingelement).append(newcontent);
	                	}else{
	                		$(buildingelement).html(newcontent);
	                	}
	                });
	            }

                var buildingelement = '#building-auditors-'+data.auditor.building_id+' .uk-slideshow-items li.uk-active > div';
               //console.log(buildingelement);
               if(data.auditor.building_auditors.length > 0){
                $(buildingelement).html('');
                $.each(data.auditor.building_auditors, function(index, value){
                	var newcontent = '<div id="unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+data.auditor.audit_id+', '+data.auditor.building_id+', 0, \'unit-auditor-'+value.id+data.auditor.audit_id+data.auditor.building_id+data.auditor.unit_id+'\')">'+value.initials+'</div>';

                	if($('#building-auditors-'+data.auditor.building_id).hasClass('hasAuditors')){
                		//$('#building-auditors-'+area.building_id).append(newcontent);
                		$(buildingelement).append(newcontent);
                	}else{
                		//$('#building-auditors-'+area.building_id).html(newcontent);
                		$(buildingelement).html(newcontent);
                	}
                	
                });
            }

                $('#unit-amenity-count-'+data.amenity_count_id).html(data.amenity_count + ' AMENITIES');
                console.log('#unit-amenity-count-'+data.amenity_count_id);
            }
            @endif

			dynamicModalClose();
		});
	}

</script>