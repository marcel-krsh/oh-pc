<div id="modal-auditor-amenity-assignment" class="uk-padding-remove uk-margin-bottom uk-overflow-auto">
	<h2>Assign Auditor to {{$name}} / {{$amenity->amenity->amenity_description}}</h2>

	<div class="uk-margin-large-top uk-margin-large-bottom">
		@if($auditors)
		<select class="uk-select uk-grid-margin uk-first-column" id="auditor_id" name="auditor_id" onchange="saveAuditorToAmenity({{$amenity_id}}, {{$audit_id}}, {{$building_id}}, {{$unit_id}})">
			<option value="">SELECT AUDITOR</option>
			@foreach($auditors as $auditor)
            <option value="{{$auditor->user_id}}" @if($amenity->auditor_id == $auditor->user_id) selected @endif>{{$auditor->user->full_name()}}</option>
            @endforeach
        </select>
		@else
		<p>There are no auditors in the system yet.</p>
		@endif
	</div>

	<div class="project-details-info-assignment-summary uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-1-1 uk-padding-remove uk-text-right">
			<button class="uk-button uk-button-primary" onclick="dynamicModalClose();" type="button">CLOSE WINDOW</button>
		</div>
	</div>
</div>
<script>
	function saveAuditorToAmenity(amenity_id, audit_id, building_id, unit_id, element){
		$.post('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign', {
			'auditor_id' : $('#auditor_id').val(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data==0){ 
                UIkit.modal.alert(data,{stack: true});
            } else {
                UIkit.notification('<span uk-icon="icon: check"></span> Auditor Assigned', {pos:'top-right', timeout:1000, status:'success'});
                // reload inspection screen
                var target = $('#{{$element}}').html(data);

                dynamicModalClose();
            }
        } );
	}

</script>