<br />
@if(count($allFindingTypes))
@foreach($allFindingTypes as $findingType)
						
							<div class="uk-width-1-1 uk-padding-small indented">
					            <input id="filter-findings-filter-{{$findingType->id}}" value="" type="checkbox" onclick="newFinding({{$findingType->id}});"/>
								<label for="filter-findings-filter-{{$findingType->id}}" ><i class="@if($findingType->type == 'lt')a-skull @endIf @if($findingType->type == 'nlt')a-booboo @endIf @if($findingType->type == 'file')a-folder @endIf  "></i> @if($findingType->building_exterior)<span uk-tooltip title="Building Exterior"> BE </span>|@endif @if($findingType->building_system)<span uk-tooltip title="Building System"> BS </span>|@endif @if($findingType->site)<span uk-tooltip title="Site"> S </span>|@endif @if($findingType->common_area)<span uk-tooltip title="Common Area"> CA </span>|@endif @if($findingType->unit)<span uk-tooltip title="Unit"> U </span>|@endif @if($findingType->file)<span uk-tooltip title="File"> F </span>|@endif {{$findingType->name}} </label>
							</div>
						
@endforeach
@else
<h3 class="uk-width-1-1 uk-margin-top">Sorry, it doesn't appear the admin has assigned any HUD areas to this amenity. Please contact them to get this resolved.</h3>
@endif
