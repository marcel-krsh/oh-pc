
	<div id="dynamic-modal-content">
		<script>
		resizeModal(95);
		</script>

		<form name="newNoteForm" id="newNoteForm" method="post">
			<!-- begin communication opened template -->
			<div class="uk-container uk-container-center">

				<!--BEGIN top row-->
				<div class="uk-grid uk-grid-small">
					<div class="uk-width-8-10">Notice</div>
				</div>
				<div class="uk-grid ">
					<!--END second row-->		
					<div class="uk-width-1-1">
						<div class="uk-grid">
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
									<div class="field-box" style="min-height:3em;">
										<div class="uk-grid uk-grid-collapse">
											<div class="uk-width-1-10"><label>TO:</label></div>
											<div class="uk-width-9-10"><select multiple id="users" name="users" style="height: 100px;">
												@php $previousEntity = ""; $previousOpened = 0; @endphp
												@forEach($users as $user)
													@if($previousEntity != $user->entity_name)
														@if($previousOpened == 1)
														</optgroup>
														@endIf
														@php $previousEntity = $user->entity_name; $previousOpened = 1; @endphp
														<optgroup label="{{$user->entity_name}}">
													@endIf
												<option value="{{$user->id">
																										
												</option>
												@endForEach
												</optgroup>
											</select></div>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
									<div class="field-box" style="min-height:3em;">
										<div class="uk-grid uk-grid-collapse">
											<div class="uk-width-1-10"><label>SUBJECT</label></div>
											<div class="uk-width-9-10"><input type="text" id="subject" name="subject" placeholder="Enter notice subject here."></div>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-1 uk-margin-bottom">
								<div class="uk-width-1-1">
									<div class="field-box" style="min-height:3em;">
										<div class="uk-grid uk-grid-collapse">
											<div class="uk-width-1-10"><label>BODY</label></div>
											<div class="uk-width-9-10"><textarea id="file-note" rows="8" name="file-note" placeholder="Enter notice here."></textarea></div>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-2">
							</div>
							<div class="uk-width-1-4">
								<a class="uk-button uk-button-default uk-width-1-1" onclick="dynamicModalClose()"><span uk-icon="times-circle"></span> CANCEL</a>
							</div>
							<div class="uk-width-1-4 ">
								<a class="uk-button uk-width-1-1 uk-button-primary" onclick="submitNewNotice()"><span uk-icon="paper-plane"></span> SEND</a>
							</div>
						</div>
					</div>
					<!-- end note opened template -->
				</div>
			</div>
		</form>
	</div>	
	
	<script type="text/javascript">
	function submitNewNotice(id) {
		var form = $('#newNoteForm');

		data = form.serialize(); 
		$.post('{{ URL::route("notice.newNotice") }}', {
	            'parcel' : form.find( "input[name='parcel']" ).val(),
	            'file-note' : form.find( "textarea[name='file-note']" ).val(),
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data!='1'){ 
	                    UIkit.modal.alert(data);
	                } else {
	                    UIkit.modal.alert('Your note has been saved.');                                                                           
	                }
		} );
	        
		// by nature this note is it's history note - so no need to ask them for a comment.
		loadParcelSubTab('notes',id);
		dynamicModalClose();
		
	}	
	</script>