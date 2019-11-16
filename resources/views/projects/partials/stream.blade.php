<?php // place holder 
$data = null;
$findingStream = null;
?>
<!-- override styles for project tab -->
<style>
	#project-tab-findings > .modal-findings-right {
		padding-left:15px !important;
	}
	#project-tab-findings > .modal-findings-right,#project-tab-findings > .modal-findings-right > .modal-findings-right-top, #project-tab-findings >.modal-findings-right > .modal-findings-right-bottom-container {
		position:relative !important;
		width: 99% !important;
	}
</style>
<div id="project-tab-findings" class="uk-margin-top"  >
@include('audit_stream.audit_stream');

</div>