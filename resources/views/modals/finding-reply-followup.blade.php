	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> FINDING #{{$from->id}} <small>ADD FOLLOW-UP</small></div>
		
	</div>
	<form id="add-followup-finding-form" method="post">
		<input type="hidden" name="id" value="{{$from->id}}">
		<input type="hidden" name="fromtype" value="{{$fromtype}}">
		<input type="hidden" name="type" value="followup">
	<div class="form-default-followup uk-margin-top" uk-grid>
        
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            
                <label>DUE IN: 
           
        
            
                <input type="number" name="due" min="1" max="31" value="24" pattern="[0-9]*"  class="uk-form-small followup-number " style="height: 20px; width:36%; border-width: 1px; border-style: solid; border-color: rgb(229, 229, 229);"></label>
           
        </div>
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            <select class="uk-select uk-form-small followup-duration" name="duration">
                <option value="hours" selected>Hours</option>
                <option value="days">Days</option>
                <option value="weeks">Weeks</option>
                <option value="months">Months</option>
            </select>
        </div>
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            <select id="followup-assignee" class="uk-select uk-form-small followup-assignment" name="assignee">
                <option value="">Select Assignee</option>
                @if($owner_id)
                <option value="{{$owner_id}}">{{$owner_name}} (Owner)</option>
                @endif
                @if($pm_id)
                <option value="{{$pm_id}}">{{$pm_name}} (Property Manager)</option>
                @endif
                @foreach($auditors as $auditor)
                <option value="{{$auditor->user_id}}">{{$auditor->user->full_name()}}</option>
                @endforeach
            </select>
        </div>
        <div class="uk-width-1-2  uk-margin-small-top uk-margin-small-bottom">
            <input type="text" value="" name="description" placeholder="Follow-up Description" class="uk-input uk-form-small followup-description" id="followup-description">
        </div>
        
        <div class="uk-width-1-2  uk-margin-top">Actions Required</div>
        <div class="uk-width-1-2  uk-margin-top"><span class="doc-cats" >Document Categories</span></div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label id="followup-reply-label"><input id="followup-reply" class="uk-checkbox followup-reply" name="comment" type="checkbox" value="1" > Comment</label><br /><br />
            
        </div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label id="followup-photo-label" ><input id="followup-photo" class="uk-checkbox followup-photo" name="photo" type="checkbox" value="1" > Upload Photo</label>
        </div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label id="followup-doc-label"><input id="followup-doc" class="uk-checkbox followup-doc" name="doc" type="checkbox" value="1" > Upload a Doc</label>
        </div>
        <div class="uk-width-1-2  uk-margin-small-top doc-cats">
            @if(count($document_categories))
            <div class="uk-width-1-1 uk-width-2-3@m uk-scrollable-box" style="width:100%; height:300px;">
                <ul class="uk-list">
                    @foreach($document_categories as $cat)
                    <li><label><input class="uk-checkbox followup-cat" type="checkbox" name="categories[]" value="{{$cat->id}}"> {{ucwords($cat->document_category_name)}}</label></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        
        <div class="uk-width-1-1">

            
		    <hr class="dashed-hr uk-margin-bottom">
		    <div class="uk-margin-bottom" uk-grid>
		    	<!-- <div class="uk-width-1-2">
		    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
		    	</div> -->
		    	
		    	<div class="uk-width-1-2">
		    		<a class="uk-button uk-button-primary uk-width-1-1" onclick="dynamicModalClose(2);"><i class="a-circle-cross"></i> CANCEL</a>
		    	</div>
		    	<div class="uk-width-1-2">
		    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFindingFollowup(event);"><i class="a-file-pen"></i> SAVE FOLLOW UP</button>
		    	</div>
		    </div>
        </div>
    </div>
	</form>
	<script type="text/javascript">

	function saveFindingFollowup(e){
		e.preventDefault();
		var form = $('#add-followup-finding-form');

		var thereAreErrors = 0;

		// checks
		if($("#followup-assignee").val() == '') {
		    $("#followup-assignee").addClass('uk-form-danger');
		        thereAreErrors = 1;
		}else{
			$("#followup-assignee").removeClass('uk-form-danger');
		}
		if($("#followup-description").val().length === 0) {
		    $("#followup-description").addClass('uk-form-danger');
		        thereAreErrors = 1;
		}else{
			$("#followup-description").removeClass('uk-form-danger');
		}
		if(!$("#followup-doc").prop('checked') && !$("#followup-reply").prop('checked') && !$("#followup-photo").prop('checked')){
			thereAreErrors = 1;
			$("#followup-doc-label").addClass('uk-form-danger');
			$("#followup-reply-label").addClass('uk-form-danger');
			$("#followup-photo-label").addClass('uk-form-danger');
		}else{
			$("#followup-doc-label").removeClass('uk-form-danger');
			$("#followup-reply-label").removeClass('uk-form-danger');
			$("#followup-photo-label").removeClass('uk-form-danger');
		}
		if($("#followup-doc").prop('checked')){
			console.log('doc selected');
			var noDocSelection = 1;
			$('.followup-cat').each(function() {
    			if($(this).prop('checked')){
    				noDocSelection = 0;
    			}
    		});
    		if(noDocSelection == 1){
    			$(".doc-cats").addClass('uk-form-danger');
    			thereAreErrors = 1;
    		}else{
    			$(".doc-cats").removeClass('uk-form-danger');
    		}
		}

		if(thereAreErrors == 1){
			return false;
		}

		$.post("/findings/reply", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){ 
                UIkit.modal.alert(data,{stack: true});
            } else {
                dynamicModalClose(2);
	            UIkit.notification('<span uk-icon="icon: check"></span> Follow-up Saved', {pos:'top-right', timeout:1000, status:'success'});
	            $('#finding-modal-audit-stream-refresh').trigger('click');
            
            }
        } );
	}

 </script>