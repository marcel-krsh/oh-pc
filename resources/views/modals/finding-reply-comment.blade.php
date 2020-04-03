	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> {{strtoupper($fromtype)}} #{{$from->id}} <small>ADD COMMENT</small></div>

	</div>
	<form id="add-comment-finding-form" method="post">
		<input type="hidden" name="id" value="{{$from->id}}">
		<input type="hidden" name="fromtype" value="{{$fromtype}}">
		<input type="hidden" name="type" value="{{ $type }}">
	<hr class="dashed-hr uk-margin-bottom">
	<p>COMMENT:</p>
	<div class="findings-new-add-comment" data-finding-id="tplFindingId">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea" id="finding-comment" name="comment" placeholder="Enter your comment here. If you leave it blank, no comment will be added.">{{ $type == 'comment-edit' ? $from->comment : '' }} </textarea><!--
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
	    <div>
	    	<input class="uk-checkbox" type="checkbox" name="hide_on_reports"> Do Not Display on Reports
	    </div>
	    <hr class="dashed-hr uk-margin-bottom">
	    <div class="uk-margin-bottom" uk-grid>
	    	<!-- <div class="uk-width-1-2">
	    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	</div> -->

	    	<div class="uk-width-1-2">
	    		<a class="uk-button uk-button-primary uk-width-1-1" onclick="@if($fromtype == 'photo')dynamicModalClose(3); @else dynamicModalClose(2); @endif"><i class="a-circle-cross"></i> CANCEL</a>
	    	</div>
	    	<div class="uk-width-1-2">
	    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveCommentFinding(event);"><i class="a-file-pen"></i> SAVE COMMENT</button>
	    	</div>
	    </div>
	</div>
	</form>
	<script type="text/javascript">

	function saveCommentFinding(e){
		e.preventDefault();
		var form = $('#add-comment-finding-form');

		$.post("/findings/reply", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){
                UIkit.modal.alert(data,{stack: true});
            } else {
            	@if($type == 'subcommentfromphoto')
                openFindingPhoto({{$from->finding_id}},{{$from->photo_id}},{{$from->photo_id}},2);
                dynamicModalClose({{$level}});
                @elseif($fromtype == 'photo')
                openFindingPhoto({{$from->finding_id}},{{$from->id}},{{$from->id}},2);
                dynamicModalClose({{$level}});
                @else
                dynamicModalClose({{$level}});
                @endif
	            UIkit.notification('<span uk-icon="icon: check"></span> Comment Saved', {pos:'top-right', timeout:1000, status:'success'});
	            $('#finding-modal-audit-stream-refresh').trigger('click');

	            if(window.from_document_findings_modal > 0) {
	            	openFindingDetails(window.from_document_findings_modal);
	            }
            }
        } );
	}

 </script>