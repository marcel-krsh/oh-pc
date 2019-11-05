
	<script>
		$('#modal-size').css('width', '70%');
	</script>
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>EDIT Document {{$document->filename}}</H3>
				<form id='editDocumentForm' class="uk-form-horizontal" role="form">
					<input type="hidden" name="document" value="{{$document->id}}">
					<div class="uk-form-row">
						<label class="uk-form-label" for="status_id">Categories</label>
						<div class="uk-form-controls">
							<div id="category-list">
								<ul class="uk-list document-category-menu" style="margin-left: 20px">
									@foreach ($document_categories as $category)
									@if($category->active == 1)
									<li>
										<input class="uk-radio" name="category-id-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio" {{ $category->id == $categories_used->id ? 'checked=checked' : '' }}>
										<label for="category-id-{{ $category->id }}">
											{{ $category->document_category_name }}
										</label>
									</li>
									@endif
									@endforeach
								</ul>
							</div>
						</div>
					</div>

					<div class="uk-form-row">
						<label class="uk-form-label" for="transaction_note">Comments</label>
						<div class="uk-form-controls">

							<textarea id="comments" name="comments" class="uk-textarea  uk-width-1-1">{{$document->comment}}</textarea>

						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
	<hr>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div id="applicant-info-update">
				<div class="uk-grid uk-margin">
					<div class="uk-width-2-5">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <i uk-icon="times-circle" class=" uk-margin-left"></i> CANCEL</a>
					</div>
					<div class="uk-width-1-5"></div>
					<div class="uk-width-2-5">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="editdocument({{$document->id}})"> <i class="uk-margin-left"></i> SAVE &nbsp;</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">

		function editdocument(id) {
			var form = $('#editDocumentForm');
			var categoryArray = [];
			$("input:radio[name=category-id-checkbox]:checked").each(function(){
				categoryArray.push($(this).val());
			});
			$.post('/modals/edit-local-document/{{ $document->id }}', {
				'inputs' : form.serialize(),
				'cats' : categoryArray,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data != 1){
					UIkit.modal.alert(data);
				} else {
					documentsLocal('{{$project->id}}');
					dynamicModalClose();
				}
			} );

		//location.reload();



	}
</script>
