<?php $text = $bladeData ?>
<div uk-grid>
	
@if(!is_null($text))
	<div class="uk-width-1-1"><h1>Notes</h1>
	{{$text->content}}
	<hr >
	
	@can('access_auditor')<button onClick="deleteNotes({{$report->id}})"><i class="a-trash-can"></i> DELETE NOTES </button> <button class="uk-button" onclick="dynamicModalLoad('/report/{{$report->id}}/notes')">EDIT NOTES</button>@endCan
	</div>
	
@else
	@if($print != 1)
	@can('access_auditor')
		<button class="uk-button uk-margin-bottom uk-margin-top uk-width-1-1" onclick="dynamicModalLoad('/report/{{$report->id/notes')">ADD NOTES</button>
	@else
		<hr class="uk-margin-top uk-margin-bottom">
	@endCan
	@endIf
@endIf
</div>
