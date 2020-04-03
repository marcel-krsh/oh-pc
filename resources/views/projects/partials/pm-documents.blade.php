
<div id="project-documents" uk-grid>
	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
		<a class="uk-button uk-width-1-1" id="project-documents-button-2" onclick="if($(this).hasClass('active') || window.projectDocumentsLoaded  == 0){documentsLocal('{{ $project->id }}', '{{ $audit_id }}')} else {projectDocumentsShow();}">
			<span class="a-file"></span>
			<span>PROJECT DOCUMENTS</span>
		</a>
	</div>
	<div class=" uk-width-1-1@s uk-width-1-5@m ">
				<a class="uk-button uk-width-1-1" id="project-documents-button-3" onclick="if($(this).hasClass('active') || window.projectDocumentUploadLoaded == 0){documentUpload('{{ $project->id }}', '{{ $audit_id }}')} else {projectDocumentUploadShow();}"> <i class="a-file-plus" ></i> UPLOAD NEW DOCUMENTS</a>
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
	window.projectDocumentUploadLoaded = 0;
	window.projectDocumentsLoaded  = 0;

	$( document ).ready(function() {
		

		checkRequestedDocuments();

	});
	function checkRequestedDocuments(){
		if(window.fromAudit == 1) {
			window.fromAudit = 0;
			// window.fromBuilding = building;
			// 	window.fromUnit = unit;
			$('#project-documents-button-2').addClass('active');
			$('#project-documents-button-3').trigger("click");
			console.log('pmDocuments fromAudit Scrolling');
			$('html, body').animate({
								scrollTop:0
							}, 1000);
		} else {
			$('#project-documents-button-2').addClass('active');
			$('#project-documents-button-2').trigger("click");
			console.log('pmDocuments NOT fromAudit Scrolling');
			$('html, body').animate({
								scrollTop: 0
							}, 1000);
		}
	}
	function documentsLocal(project_id, audit_id = null, filter = null) {
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		$('#allita-documents').html(tempdiv);
		$('#project-documents-button-3').removeClass('uk-button-success green-button');
		$('#project-documents-button-2').addClass('uk-button-success green-button active');
		$('#document-upload').hide();
		$('#allita-documents').fadeIn();
		var url = '/pm-projects/'+project_id+'/local-documents/';
		if(audit_id != null) {
			url = url+ audit_id;
		}
		if(filter != null) {
			url = url+ filter;
		}
	    $.get(url, {
	        }, function(data) {
	            if(data=='0'){
	                UIkit.modal.alert("There was a problem getting the project information.");
	            } else {
					$('#allita-documents').html(data);

	        	}
	    });
	}
	function projectDocumentsShow(){
		$('#document-upload').hide();
		$('#allita-documents').fadeIn();
		$('#project-documents-button-3').removeClass('uk-button-success green-button active');
		$('#project-documents-button-2').addClass('uk-button-success green-button active');
		console.log('projectDocumentsShow Scrolling');
		$('html, body').animate({
						scrollTop: 0
					}, 1000);
	}

	function projectDocumentUploadShow(){
		$('#allita-documents').hide();
		$('#document-upload').fadeIn();

		$('#project-documents-button-2').removeClass('uk-button-success green-button active');
		$('#project-documents-button-3').addClass('uk-button-success green-button active');
		console.log('projectDocumentUploadShow Scrolling');
		$('html, body').animate({
						scrollTop: 0
					}, 1000);
	}

function documentUpload(project_id, audit_id = null, filter = null) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#document-upload').html(tempdiv);
	$('#project-documents-button-2').removeClass('uk-button-success green-button active');
	$('#project-documents-button-3').addClass('uk-button-success green-button active');
	$('#allita-documents').hide();
	$('#document-upload').fadeIn();
	var url = '/pm-projects/'+project_id+'/document-upload/';
	if(audit_id != null) {
		url = url+ audit_id;
	}
	if(filter != null) {
		url = url+ filter;
	}
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the uploader.");
            } else {
				$('#document-upload').html(data);

        	}
    });
}
window.project_detail_tab_2_loaded = 1;
console.log('project_detail_tab_2 Scrolling');
$('html, body').animate({
						scrollTop: 0
					}, 1000);
</script>
