


		<form name="newNoteForm" id="newNoteForm" method="post">
			<input type="hidden" name="project" value="{{$project->id}}">
			<!-- begin communication opened template -->
			<div class="" uk-grid>

				<!--BEGIN top row-->
				<div class="uk-width-1-1">
					<h3>Note for Project: {{$project->project_number}}</h3>
				</div>
				<div class="uk-width-1-1">
					<div class="" style="min-height:3em;">
						<label>NOTE</label>
						<textarea id="file-note" rows="8" class="uk-textarea" name="file-note" placeholder="Enter note here."></textarea>
					</div>
				</div>
				<div class="uk-width-1-2">
				</div>
				<div class="uk-width-1-4">
					<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
				</div>
				<div class="uk-width-1-4 ">
					<a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitNewNote({{$project->id}})"><span uk-icon="save"></span> SAVE</a>
				</div>
			</div>
		</form>

	
	<script type="text/javascript">
	function submitNewNote(id) {
		var form = $('#newNoteForm');

		data = form.serialize(); 
		$.post('{{ URL::route("note.create") }}', {
	            'project' : form.find( "input[name='project']" ).val(),
	            'file-note' : form.find( "textarea[name='file-note']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data!=1){ 
	                    UIkit.modal.alert(data);
	                } else {
	                    UIkit.modal.alert('Your note has been saved.');                                                                           
	                }
		} );
	        
		// by nature this note is it's history note - so no need to ask them for a comment.
		$('#project-detail-tab-4').trigger("click");
		dynamicModalClose();
		
	}	
	</script>