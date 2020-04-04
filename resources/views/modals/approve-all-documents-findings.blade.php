
<div class="modal-report-dates">
	<h1>Are You Sure?</h1>
	<p>This will approve all documents and resolve all unresolved findings attached to them with the date you select below.</p>
	{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<div class="uk-width-1-1 uk-margin-remove">
		<div uk-grid class="uk-text-small">
			<div class="uk-width-1-5">
				<span>
					<button class="uk-button uk-link uk-margin-small-left uk-width-1-1"> RESOLVED AT:</button>
				</span>
			</div>
			<div class="uk-width-1-3">
				<input id="resolved-all-documents-date-finding" class="uk-input " readonly type="text" placeholder="DATE" onchange="marAllDocumentsApprovedAndResolved()"  >
				@push('flatPickers')
				$('#resolved-all-documents-date-finding').flatpickr('{dateFormat: "m-d-Y"}');
				@endpush
			</div>
			{{-- <span id="resolved-text-{{ $document_id }}" class="uk-text-danger attention" style="font-size: 15px"></span> --}}
		</div>
	</div>

</div>
<script type="text/javascript">
	@stack('flatPickers')
</script>
<script>

	function marAllDocumentsApprovedAndResolved() {
		dynamicModalClose();
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		// $('#local-documents').html(tempdiv);
		$('#allita-documents').html(tempdiv);
		$.post('{{ URL::route("documents.update-status", $project_id) }}', {
			'project_id' : "{{ $project_id }}",
			'document_ids': window.filteredDocumentIds,
			'option' : 2,
			'date' : $("#resolved-all-documents-date-finding").val(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data != 1 ) {
				console.log("processing");
				UIkit.modal.alert(data);
			} else {
				dynamicModalClose();
			}
			documentsLocal('{{ $project_id }}');
		});
	}

	</script>

