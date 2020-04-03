<?php $text = $bladeData ?>
<div uk-grid>
	
@if(!is_null($text))
	<div class="uk-width-1-1"><h1>Notes</h1>
	{{$text->content}}
	<hr >
	
	@if($auditor_access)<button onClick="deleteNotes({{$report->id}})"><i class="a-trash-can"></i> DELETE NOTES </button> <button class="uk-button" onclick="dynamicModalLoad('/report/{{$report->id}}/notes')">EDIT NOTES</button>@endIf
	</div>
	
@else
	@if($print != 1)
	@if($auditor_access)
		<button class="uk-button uk-margin-bottom uk-margin-top uk-width-1-1" onclick="dynamicModalLoad('/report/{{$report->id/notes')">ADD NOTES</button>
	@else
		<hr class="uk-margin-top uk-margin-bottom">
	@endIf
	@endIf
@endIf
</div>
