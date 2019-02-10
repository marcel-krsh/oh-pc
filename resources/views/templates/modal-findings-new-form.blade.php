	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> {{$findingtypeid->name}}<small><i class="uk-margin-left a-info-circle" uk-tooltip="title:Tied to HUD Areas<br > @foreach($findingtypeid->huds() as $hud) {{$hud->name}} @endforeach <br >Nominal Item Weight {{$findingtypeid->nominal_item_weight}} <br >Criticality {{$findingtypeid->criticality}}; pos:bottom"></i></small><h4 style="line-height: 0px; margin-top: 10px; margin-left: 35px;">ON {{strtoupper($amenityinspectionid->amenity->amenity_description)}}  {{$amenityincrement}}</h4></div>
		
	</div>
	<hr class="dashed-hr uk-margin-bottom">
	<div class="uk-form">
		<div class="uk-form-row">
			@if($findingtypeid->one) <label class="use-hand-cursor"><input type="radio" name="level" class="uk-radio" value="1" @if(!$findingtypeid->two && !$findingtypeid->three) checked @endif onClick="$('#finding-comment').focus();"> LEVEL 1</label> &nbsp; &nbsp;@endif
			@if($findingtypeid->two) <label class="use-hand-cursor"><input type="radio" name="level" class="uk-radio" value="2" @if(!$findingtypeid->one && !$findingtypeid->three) checked @endif onClick="$('#finding-comment').focus();"> LEVEL 2</label>  &nbsp; &nbsp;@endif
			@if($findingtypeid->three) <label class="use-hand-cursor" ><input type="radio" name="level" class="uk-radio" value="3" @if(!$findingtypeid->two && !$findingtypeid->one) checked @endif onClick="$('#finding-comment').focus();"> LEVEL 3</label>  &nbsp; &nbsp;@endif
		</div>
	</div>
	<hr class="dashed-hr uk-margin-bottom">
	<p>COMMENT:</p>
	<div class="findings-new-add-comment" data-finding-id="tplFindingId">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea" id="finding-comment" name="comment" placeholder="Enter your comment here. If you leave it blank, no comment will be added."> </textarea><!-- 
	    	<div class="textarea-status">SAVED</div> -->
	    </div>
	    <div class="findings-new-add-comment-boilerplate-action" uk-grid>
	    	<!-- <button class="uk-width-1-3" onclick="useBoilerplate();"><i class="a-file-text"></i> Use a boilerplate</button> -->
	    	<button class="uk-width-1-3" onclick="clearComment();"><i class="a-file-minus"></i> Clear</button>
	    	<!-- <button class="uk-width-1-3" onclick="appendBoilerplate();"><i class="a-file-plus"></i> Append a boilerplate</button> -->
	    </div>
	    <div class="findings-new-add-comment-quick-entry-list">
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
	    		
	    	</div>
	    	<div class="uk-width-1-2">
	    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFinding();"><i class="a-file-pen"></i> SAVE FINDING</button>
	    	</div>
	    </div>
	</div>

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
	</script>