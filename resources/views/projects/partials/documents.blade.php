
<div id="project-documents" uk-grid>
	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
		<a class="uk-button uk-width-1-1" id="project-documents-button-1" onclick="documentsDocuware('{{$project->id}}')">
			{{-- <span class="a-envelope-4"></span> --}}
			<img src="{{ asset('images/docuware.gif') }}" style="width:22px;height:22px;">
			<span>Docuware</span>
		</a>
	</div>
	<div class="uk-width-1-1@s uk-width-1-5@m " style="vertical-align:top">
		<a class="uk-button uk-width-1-1" id="project-documents-button-2" onclick="documentsLocal('{{$project->id}}')">
			{{-- <span class="a-envelope-4"></span> --}}
			<img src="{{ asset('images/Allita-Blight-Icon.png') }}" style="width:22px;height:22px;">
			<span>Allita</span>
		</a>
	</div>
</div>
<div id="project-documents-container">
	{{-- Docuware documents --}}
	<div id="docuware-documents">

	</div>
	{{-- Allita documents --}}
	<div id="allita-documents">

	</div>
</div>


<script>

	$( document ).ready(function() {
		$('#project-documents-button-2').trigger("click");

		// if($('#docuware-documents').html() == ''){
		// 	$('#project-documents-button-1').trigger("click");
		// }
		// documentsDocuware('{{$project->id}}')
	});
</script>
