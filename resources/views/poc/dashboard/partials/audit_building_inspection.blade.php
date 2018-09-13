<div class="uk-overflow-auto" uk-grid style="min-height:1000px">
	<div id="inspect-areas" class="uk-width-1-2">
		<ul class="uk-list">
			@foreach($areas as $key=>$area)
			<li>{{$area['name']}}</li>
			@endforeach
		</ul>
	</div>
	<div id="inspect-tools" class="uk-width-1-2">
	</div>
</div>