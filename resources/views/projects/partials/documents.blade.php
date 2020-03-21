
<div id="project-documents" uk-grid>
	@if($auditor_access)
	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
		<a class="uk-button uk-width-1-1" id="project-documents-button-1" onclick="documentsDocuware('{{ $project->id }}')">
			<span class="a-file-shield"></span>
			
			<span>DOCUWARE PROJECT DOCS</span>
		</a>
	</div>
	@endIf
	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
		<a class="uk-button uk-width-1-1" id="project-documents-button-2" onclick="documentsLocal('{{ $project->id }}', '{{ $audit_id }}')">
			<span class="a-file"></span>
			
			<span>PROJECT DOCUMENTS</span>
		</a>
	</div>
	<div class=" uk-width-1-1@s uk-width-1-5@m ">
				<a class="uk-button uk-width-1-1" id="project-documents-button-3" onclick="documentUpload('{{ $project->id }}', '{{ $audit_id }}')"> <i class="a-file-plus" ></i> UPLOAD NEW DOCUMENTS</a>
	</div>
</div>
<div id="project-documents-container">
	{{-- Docuware documents --}}
	@if($auditor_access)
	<div id="docuware-documents">

	</div>
	@endIf
	{{-- Allita documents --}}
	<div id="allita-documents">

	</div>
	<div id="document-upload">

	</div>
</div>


<script>

	$( document ).ready(function() {
		$('#project-documents-button-2').trigger("click");
	});
</script>
