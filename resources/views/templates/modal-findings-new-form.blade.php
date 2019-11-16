	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> {{ $findingtypeid->name }}<small><i class="uk-margin-left a-info-circle" uk-tooltip="title:Tied to HUD Areas<br > @foreach($findingtypeid->huds() as $hud) {{ $hud->name }} @endforeach <br >Nominal Item Weight {{ $findingtypeid->nominal_item_weight }} <br >Criticality {{ $findingtypeid->criticality }}; pos:bottom"></i></small><h4 style="line-height: 0px; margin-top: 10px; margin-left: 35px;">ON {{ strtoupper($amenityinspectionid->amenity->amenity_description) }}  {{ $amenityincrement }}</h4>
		</div>
	</div>
	<form id="add-finding-form" method="post">
		<input type="hidden" name="amenity_inspection_id" value="{{ $amenityinspectionid->id }}">
		<input type="hidden" name="finding_type_id" value="{{ $findingtypeid->id }}">
	<hr class="dashed-hr uk-margin-bottom">
	<div class="uk-form">
		<div class="uk-form-row">
			@if($findingtypeid->one) <label class="use-hand-cursor"><input id="level-radio-one" type="radio" name="level" class="uk-radio" value="1" @if(!$findingtypeid->two && !$findingtypeid->three)  @endif onClick="$('#finding-comment').focus();"> LEVEL 1 </label>
			<span class=" uk-margin-small-top">{{ $findingtypeid->one_description }}  <hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-margin-top">
			</span> &nbsp; &nbsp;@endif

			@if($findingtypeid->two) <label class="use-hand-cursor"><input id="level-radio-two" type="radio" name="level" class="uk-radio" value="2" @if(!$findingtypeid->one && !$findingtypeid->three)  @endif onClick="$('#finding-comment').focus();"> LEVEL 2  </label>  &nbsp; &nbsp;<span class=" uk-margin-small-top">{{ $findingtypeid->two_description }}  <hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-margin-top"></span>@endif

			@if($findingtypeid->three) <label class="use-hand-cursor" ><input id="level-radio-three" type="radio" name="level" class="uk-radio" value="3" @if(!$findingtypeid->two && !$findingtypeid->one)  @endif onClick="$('#finding-comment').focus();"> LEVEL 3   </label>  &nbsp; &nbsp;<span class=" uk-margin-small-top">{{ $findingtypeid->three_description }} </span>@endif
		</div>
	</div>
	<hr class="dashed-hr uk-margin-bottom">
	<p>COMMENT:</p>
	<div class="findings-new-add-comment" data-finding-id="tplFindingId">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea" id="finding-comment" name="comment" placeholder="Enter your comment here. If you leave it blank, no comment will be added."></textarea><!--
	    	<div class="textarea-status">SAVED</div> -->
	    </div>
	    <div class="findings-new-add-comment-boilerplate-action" uk-grid>
	    	<!-- <button class="uk-width-1-3" onclick="useBoilerplate();"><i class="a-file-text"></i> Use a boilerplate</button> -->
	    	<a class="uk-width-1-3" onclick="clearComment();" style="color:#fff"><i class="a-file-minus"></i> Clear</a>
	    	<!-- <button class="uk-width-1-3" onclick="appendBoilerplate();"><i class="a-file-plus"></i> Append a boilerplate</button> -->
	    </div>
	    <div class="findings-new-add-comment-quick-entry-list uk-hidden">
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="property-manager-contact-name">PROPERTY MANAGER CONTACT NAME</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="address-of-this-building">ADDRESS OF THIS BUILDING</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="date-in-7-days">DATE IN 7 DAYS</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="tomorrow-date">TOMORROW'S DATE</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="head-of-household-name">HEAD OF HOUSEHOLD NAME</span><!--
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="another-tag">ANOTHER QUICK ENTRY BUTTON</span> -->
	    </div>
	    <hr class="dashed-hr uk-margin-bottom">
	    <div class="uk-margin-bottom" uk-grid>
	    	<!-- <div class="uk-width-1-2">
	    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	</div> -->

	    	<div class="uk-width-1-2">
	    		<a onClick="dynamicModalClose(2);" class="uk-button uk-button-primary uk-width-1-1"> CLOSE</a>
	    	</div>

	    	<div class="uk-width-1-2">
	    		<input type="hidden" id="add-finding-form-finding_date" name="date" value="" />
	    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFinding(event);"><i class="a-file-pen"></i> SAVE AND CLOSE</button>
	    	</div>
	    </div>
	</div>
	</form>
	<script type="text/javascript">
		// set cursor focus to the end of the current comment:
		var input = $("#finding-comment");
		$(input).focus();
		input[0].selectionStart = input[0].selectionEnd = input.val().length;

		function clearComment(){
			$("#finding-comment").val('');
			var input = $("#finding-comment");
			$(input).focus();
			input[0].selectionStart = input[0].selectionEnd = input.val().length;

		}


	function saveFinding(e){
		e.preventDefault();
		var form = $('#add-finding-form');

		$('#add-finding-form-finding_date').attr('value', $('#finding-date').val());
		$.post("/findings/create", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){
                UIkit.modal.alert(data+'DUDE!',{stack: true});
            } else {
            	// debugger;
            	if(window.findingModalRightLocation) {
            		// window.addFindingFlag = true;
            		$('#finding-modal-audit-stream-location-sticky').trigger('click');
            	} else {
            		$('#finding-modal-audit-stream-refresh').trigger('click');
            	}

	            dynamicModalClose(2);

            }
        } );
	}

 </script>