
		<div id="">
			 <div id="inspect-areas" class="uk-width-1-1 uk-inline building-type">
				<ul class="uk-list">
					@foreach($areas as $detail_id=>$area)
					<li>{{$area['name']}}</li>
					@endforeach
				</ul>
			</div>
			<div id="inspect-tools" class="">
				Tools here 
			</div>
		</div>
		