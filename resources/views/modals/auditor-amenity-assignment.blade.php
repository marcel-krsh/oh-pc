<div id="modal-auditor-amenity-assignment" class="uk-padding-remove uk-margin-bottom uk-overflow-auto">
	<h2>Assign Auditor to @if(isset($name)){{ $name }}@endIf @if(isset($amenity) && $amenity) / {{ $amenity->amenity->amenity_description }}@endif @if(!isset($name) && !isset($amenity)) Site Level Inspections @endIf</h2>

	<div class="uk-margin-large-top uk-margin-large-bottom">
		@if($current_auditor)
		<p>You are switching two auditors. This will replace all the assignments from {{ $current_auditor->full_name() }} with your selection below.</p>
		@endif
		{{-- @if($auditors)
		<select class="uk-select uk-grid-margin uk-first-column" id="auditor_id" name="auditor_id" onchange="saveAuditorToAmenity({{ $amenity_id }}, {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, '{{ $element }}', {{ $in_model }})">
			<option value="">SELECT AUDITOR</option>
			@foreach($auditors as $auditor)
			<option value="{{ $auditor->user_id }}" @if($current_auditor) @if($current_auditor->id == $auditor->user_id) selected @endif @endif @if($amenity) @if($amenity->auditor_id == $auditor->user_id) selected @endif @endif>{{ $auditor->user->full_name() }}</option>
			@endforeach
		</select>
		@else
		<p>There are no auditors in the system yet.</p>
		@endif --}}
		@if($audit_users)
		<select class="uk-select uk-grid-margin uk-first-column" id="auditor_id" name="auditor_id" onchange="saveAuditorToAmenity({{ $amenity_id }}, {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, '{{ $element }}', {{ $in_model }})">
			<option value="">SELECT AUDITOR</option>
			@foreach($audit_users as $auditor)
			<option value="{{ $auditor->id }}" @if($current_auditor) @if($current_auditor->id == $auditor->id) selected @endif @endif @if($amenity) @if($amenity->auditor_id == $auditor->id) selected @endif @endif>{{ $auditor->full_name() }}</option>
			@endforeach
		</select>
		@else
		<p>There are no auditors in the system yet.</p>
		@endif
	</div>

	<div class="project-details-info-assignment-summary uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-1-1 uk-padding-remove uk-text-right">
			<button class="uk-button uk-button-primary" onclick="dynamicModalClose({{ $in_model ? 2:'' }});" type="button">CLOSE WINDOW</button>
		</div>
	</div>
</div>
<script>
	function saveAuditorToAmenity(amenity_id, audit_id, building_id, unit_id, element, inmodel=0){
		@if($current_auditor)
		var url = 'amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/swap/{{ $current_auditor->id }}';
		@else
		var url = 'amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign';
		@endif
		$.post(url, {
			@if($current_auditor)
			'new_auditor_id' : $('#auditor_id').val(),
			@else
			'auditor_id' : $('#auditor_id').val(),
			@endif
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data==0){
				UIkit.modal.alert(data,{stack: true});
			} else {
				UIkit.notification('<span uk-icon="icon: check"></span> Auditor Assigned', {pos:'top-right', timeout:1000, status:'success'});
        // reload inspection screen
        // debugger;

        if(unit_id != 0 && amenity_id == 0){
        	@if($current_auditor)
        	console.log('1');
        	// auditor-6324 6659 23058 208307 397
        	var newcontent = '<div id="auditor-{{ $current_auditor->id }}{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="auditor-badge '+data.color+' no-float use-hand-cursor" onclick="swapAuditor({{ $current_auditor->id }}, {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-{{ $current_auditor->id }}{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+data.initials+'</div>';
        	$('#{{ $element }}').html(newcontent);

        	var newunitcontent = '<div id="'+element+'" uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="user-badge '+data.color+' no-float use-hand-cursor" onclick="assignAuditor('+audit_id+', '+building_id+', '+unit_id+', '+amenity_id+', \''+element+'\');">'+data.initials+'</div>';
        	$('[id^=auditor-{{ $current_auditor->id }}{{ $audit_id }}{{ $building_id }}]').replaceWith(newunitcontent);

        	var unitelement = '#unit-auditors-'+data.unit_id+' .uk-slideshow-items li.uk-active > div';
        	console.log(unitelement);
        	$(unitelement).html('');
        	$.each(data.unit_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(unitelement).append(newcontent);
        	});

        	var buildingelement = '#building-auditors-'+data.building_id+' .uk-slideshow-items li.uk-active > div';
        	console.log(buildingelement);
        	$(buildingelement).html('');
        	$.each(data.building_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, 0, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(buildingelement).append(newcontent);
        	});


        	@else
        	console.log('2');

        	var newcontent = '<div class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="auditor-badge '+data.color+' no-float">'+data.initials+'</div>';
        	$('#{{ $element }}').html(newcontent);

        	if($('#building-auditors-'+building_id).hasClass('hasAuditors')){
        		$('#building-auditors-'+building_id).append(newcontent);
        	}else{
        		$('#building-auditors-'+building_id).html(newcontent);
        	}

        	var unitelement = 'div[id^=unit-auditors-]  .uk-slideshow-items li.uk-active > div';

        	$(unitelement).html('');
        	$.each(data.unit_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(unitelement).append(newcontent);
        	});

        	var buildingelement = '#building-auditors-'+data.building_id+' .uk-slideshow-items li.uk-active > div';

        	$(buildingelement).html('');
        	$.each(data.building_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, 0, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(buildingelement).append(newcontent);
        	});

        	@endif
        }else if(unit_id == 0 && building_id != 0 && amenity_id == 0){
        	console.log('3');
        	var newcontent = '<div class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="auditor-badge '+data.color+' no-float use-hand-cursor" onclick="swapAuditor('+data.id+', {{ $audit_id }}, {{ $building_id }}, 0, \'unit-auditor-'+data.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+data.initials+'</div>';
        	$('#{{ $element }}').html(newcontent);

        	if($('#building-auditors-'+building_id).hasClass('hasAuditors')){

        		// we don't know if/which unit is open
        		var unitelement = 'div[id^=unit-auditors-]  .uk-slideshow-items li.uk-active > div';

        		$(unitelement).html('');
        		$.each(data.unit_auditors, function(index, value){
        			var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        			$(unitelement).append(newcontent);
        		});
        	}else{
        		// we don't know if/which unit is open
        		var unitelement = 'div[id^=unit-auditors-]';
        		$(unitelement).html('');
        		$.each(data.unit_auditors, function(index, value){
        			var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        			$(unitelement).append(newcontent);
        		});
        	}
        	var buildingelement = '#building-auditors-'+data.building_id+' .uk-slideshow-items li.uk-active > div';
        	$(buildingelement).html('');
        	$.each(data.building_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, 0, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(buildingelement).append(newcontent);
        	});
        }else if(unit_id == 0 && building_id == 0 && amenity_id != 0 && inmodel == 0){
        	console.log('4');
        	console.log('element {{ $element }}');
        	var newcontent = '<div id="building-audits-'+data.id+'-avatar-1" uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="auditor-badge '+data.color+' use-hand-cursor no-float" onclick="swapAuditor('+data.id+', {{ $audit_id }}, 0, 0, \'building-audits-'+data.id+'-avatar-1\', '+amenity_id+')">'+data.initials+'</div>';
        	$('#{{ $element }}').replaceWith(newcontent);
        }else if(inmodel == 0) {
        	console.log('5');
        	console.log("element "+element);
        	var newcontent = '<div id="'+element+'" uk-tooltip="pos:top-left;title:'+data.name+';" title="" aria-expanded="false" class="user-badge '+data.color+' no-float use-hand-cursor" onclick="assignAuditor('+audit_id+', '+building_id+', '+unit_id+', '+amenity_id+', \''+element+'\');">'+data.initials+'</div>';
        	$('#{{ $element }}').replaceWith(newcontent);
        	var unitelement = '#unit-auditors-'+data.unit_id+' .uk-slideshow-items li.uk-active > div';
        	$(unitelement).html('');
        	$.each(data.unit_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, {{ $unit_id }}, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(unitelement).append(newcontent);
        	});
        	var buildingelement = '#building-auditors-'+data.building_id+' .uk-slideshow-items li.uk-active > div';
        	$(buildingelement).html('');
        	$.each(data.building_auditors, function(index, value){
        		var newcontent = '<div id="unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', {{ $audit_id }}, {{ $building_id }}, 0, \'unit-auditor-'+value.id+'{{ $audit_id }}{{ $building_id }}{{ $unit_id }}\')">'+value.initials+'</div>';
        		$(buildingelement).append(newcontent);
        	});
        }
        if(inmodel == 1) {
        	dynamicModalClose(2);
        	var newcontent = '<div class="amenity-auditor uk-margin-remove"  id="building-auditor-'+amenity_id+'-avatar-1"><div uk-tooltip="pos:top-left;title:'+data.name+';" class="auditor-badge '+data.color+' use-hand-cursor no-float" onclick="assignBuildingAuditor({{ $audit_id }}, '+building_id+', 0, '+amenity_id+', \'building-auditor-'+amenity_id+'-avatar-1\', 0, 0, 0, 2);">'+data.initials+'</div></div>';
        	$('#{{ $element }}').replaceWith(newcontent);
        	loadTypes(0,1);
        	filterBuildingAmenities(building_id);
        } else if(inmodel == 2) {
        	dynamicModalClose(2);
        	var newcontent = '<div class="amenity-auditor uk-margin-remove"  id="site-auditor-'+amenity_id+'-avatar-1"><div uk-tooltip="pos:top-left;title:'+data.name+';" class="auditor-badge '+data.color+' use-hand-cursor no-float" onclick="assignSiteAuditor({{ $audit_id }}, 0, 0, '+amenity_id+', \'site-auditor-'+amenity_id+'-avatar-1\', 0, 0, 0, 2);">'+data.initials+'</div></div>';
        	$('#{{ $element }}').replaceWith(newcontent);

        	loadTypes(0,1);
        	filterSiteAmenities(audit_id);
        } else if(inmodel == 3) {
        	dynamicModalClose(2);
        	var newcontent = '<div class="amenity-auditor uk-margin-remove"  id="unit-auditor-'+amenity_id+'-avatar-1"><div uk-tooltip="pos:top-left;title:'+data.name+';" class="auditor-badge '+data.color+' use-hand-cursor no-float" onclick="assignUnitAuditor({{ $audit_id }}, '+building_id+', '+unit_id+', '+amenity_id+', \'unit-auditor-'+amenity_id+'-avatar-1\', 0, 0, 0, 2);">'+data.initials+'</div></div>';
        	$('#{{ $element }}').replaceWith(newcontent);
        	loadTypes(0,1);
          	filterUnitAmenities(unit_id);
          } else if(inmodel == 4) {
          	dynamicModalClose(2);
          	loadTypes(1);
          } else {
          	dynamicModalClose();
          }
        }
      });
		window.donotRefresh = true;
}

</script>