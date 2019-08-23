	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> {{$finding->finding_type->name}} (FN#{{$finding->id}}) <small><i class="uk-margin-left a-info-circle" uk-tooltip="title:Tied to HUD Areas<br > @foreach($finding->finding_type->huds() as $hud) {{$hud->name}} @endforeach <br >Nominal Item Weight {{$finding->finding_type->nominal_item_weight}} <br >Criticality {{$finding->finding_type->criticality}}; pos:bottom"></i></small><h4 style="line-height: 0px; margin-top: 10px; margin-left: 35px;">ON {{strtoupper($finding->amenity_inspection->amenity->amenity_description)}}</h4></div>
		<?php //dd($finding); ?>
	</div>
	<form id="edit-finding-form" method="post">
		Change finding type:<br /><br />
		<select class="uk-select" id="finding_type_id" name="finding_type_id" onchange="alert('Please save and reopen the finding to update levels associated with this finding type.');">

			@foreach($finding->finding_types() as $finding_type)
			<option value="{{$finding_type->id}}" @if($finding_type->id == $finding->finding_type->id) selected @endif>{{$finding_type->name}}</option>
			@endforeach	
		</select>
	<hr class="dashed-hr uk-margin-bottom">
	Change date of finding:<br /><br />
		<input type="text" id="date" name="date" value="{{formatDate($finding->date_of_finding, 'F j, Y')}}" class="uk-input flatpickr flatpickr-input active"/>
	<hr class="dashed-hr uk-margin-bottom">	
	<div class="uk-form">
		<div class="uk-form-row">
			@if($finding->finding_type->one) <label class="use-hand-cursor"><input type="radio" name="level" class="uk-radio" value="1"  @if($finding->level == 1) checked @endIf > LEVEL 1</label> &nbsp; &nbsp; <div style="display:inline-block; width: 90%; float: right; margin-bottom:16px;"> {{$finding->finding_type->one_description}}</div>@endif
			@if($finding->finding_type->two)<hr class="dashed-hr uk-margin-bottom"> <label class="use-hand-cursor"><input type="radio" name="level" class="uk-radio" value="2" @if($finding->level == 2) checked @endIf> LEVEL 2</label>  &nbsp; &nbsp; <div style="display:inline-block; width: 90%; float: right; margin-bottom:16px;"> {{$finding->finding_type->two_description}}</div>@endif
			@if($finding->finding_type->three)<hr class="dashed-hr uk-margin-bottom">  <label class="use-hand-cursor" ><input type="radio" name="level" class="uk-radio" value="3"  @if($finding->level == 3) checked @endIf> LEVEL 3</label>  &nbsp; &nbsp; <div style="display:inline-block; width: 90%; float: right; margin-bottom:16px;"> {{$finding->finding_type->three_description}}</div>@endif
		</div>
	</div>
	 <hr class="dashed-hr uk-margin-bottom">
	    <div class="uk-margin-bottom" uk-grid>
	    	<!-- <div class="uk-width-1-2">
	    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	</div> -->
	    	
	    	<div class="uk-width-1-2@m uk-visible@m">
	    		
	    	</div>
	    	<div class="uk-width-1-2@m uk-width-1-1@s">
	    		<input type="hidden" id="finding_id" name="finding_id" value="{{$finding->id}}" />
	    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFinding(event);"><i class="a-file-pen"></i> SAVE FINDING</button>
	    	</div>
	    </div>
	</form>
	<script type="text/javascript">
		// set cursor focus to the end of the current comment:


	function saveFinding(e){
		e.preventDefault();
		var form = $('#edit-finding-form');

		$.post("/findings/edit", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){ 
                UIkit.modal.alert(data,{stack: true});
            } else {
                dynamicModalClose(2);
	            UIkit.notification('<span uk-icon="icon: check"></span> Finding Saved', {pos:'top-right', timeout:1000, status:'success'});
	            $('#finding-modal-audit-stream-refresh').trigger('click');
            
            }
        } );
	}

	flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;

	flatpickr("#date", {
	    altFormat: "F j, Y",
	    dateFormat: "F j, Y",
	    "locale": {
	        "firstDayOfWeek": 1 // start week on Monday
	    	}
	});

	

 </script>