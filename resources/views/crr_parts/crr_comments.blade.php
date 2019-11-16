
<button class = "uk-button" onclick="dynamicModalLoad('/report/{{$report->id}}/comments/{{$part}}/add');">ADD COMMENT</button>
@if(!is_null($comments))
	<div uk-grid>
		
	@forEach($comments as $c)
		<?php //dd($f); ?>
		
	@endForEach
	</div>
@else
<hr class="dashed-hr">
<h3>NO COMMENTS</h3>
@endIf
