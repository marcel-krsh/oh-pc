
<div class="modal-report-dates">
	<h1>Are You Sure?</h1>
	<p>This will approve this file and resolve all unresolved findings attached to it with the date you select below.</p>

	{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<div class="uk-width-1-1 uk-margin-remove">
		<div uk-grid class="uk-text-small">
			<div class="uk-width-1-5">
				<span id="inspec-tools-finding-resolve-{{ $document_id }}">
					<button class="uk-button uk-link uk-margin-small-left uk-width-1-1"> RESOLVED AT:</button>
				</span>
			</div>
			<div class="uk-width-1-3">
				<input id="resolved-document-date-finding-{{ $document_id }}" class="uk-input " readonly type="text" placeholder="DATE" onchange="markApprovedAndResolvedSave({{ $document_id }},$(this).val());"  >
				@push('flatPickers')
				$('#resolved-document-date-finding-{{ $document_id }}').flatpickr('{dateFormat: "m-d-Y"}');
				@endpush
			</div>
			<span id="resolved-text-{{ $document_id }}" class="uk-text-danger attention" style="font-size: 15px"></span>
		</div>
	</div>

</div>
<script type="text/javascript">
	window.from_document_findings_modal =  "{{ $document_id }}";
	@stack('flatPickers')
</script>
<script>
		var findingAllDocumentIds = "{{ $documents->pluck('id')->toJson() }}";

	function markApprovedAndResolvedSave(id) {
    	$.post('{{ url("documents/approve-findings-resolve") }}/'+id, {
    		'id' : id,
    		'date' : $("#resolved-document-date-finding-"+id).val(),
    		'_token' : '{{ csrf_token() }}'
    	}, function(data) {
    		if(data != 1 ) {
    			console.log("processing");
    			UIkit.modal.alert(data);
    		} else {
    			dynamicModalClose();
    		}
    		refreshFindingOfDocumentsRelated();
    	});
    }

    function refreshFindingOfDocumentsRelated() {
			//for these common documents, refresh
			//document-findings-attention=id
			//document-findings-id
			var allDocuments = JSON.parse(window.documentIds);
			var thisDocuments = JSON.parse(findingAllDocumentIds);
			var commonDocuments = _.intersection(allDocuments, thisDocuments);
			//document-findings-content-
			debugger;
			commonDocuments.forEach(function(item){
				if($('#document-'+item+'-findings').is(':hidden')) {
					updateContent('document-row-'+item, 'document/findings-update/'+item);
				} else {
					updateContent('document-row-'+item, 'document/findings-update/'+item, item);
				}

			});
			// $('#document-'+"{{ $document_id }}"+'-findings').slideToggle();

		}
</script>

