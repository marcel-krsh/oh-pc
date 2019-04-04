<br />
@if(count($allFindingTypes))
@if($type == 'all')
<div class="" style="background: #333; color:#fff; position:fixed; width: 43%">
	<small>SHOWING ALL POSSIBLE FINDINGS FOR @if($amenityLocationType == 'b') BUILDINGS @elseif($amenityLocationType == 's') SITE LEVEL @else UNITS @endif</small>
</div>
<hr >
@endif
@foreach($allFindingTypes as $findingType)

<div class="uk-width-1-1 uk-padding-small indented use-hand-cursor " onClick="dynamicModalLoad('add/finding/{{$findingType->id}}/amenity_inspection/{{$amenityInspectionId}}?finding_date='+window.findingModalSelectedAmenityDate+'&amenity_increment='+window.findingModalSelectedAmenityIncrement,0,0,0,2)">
	<i class="@if($findingType->type == 'lt')a-skull @endIf @if($findingType->type == 'nlt')a-booboo @endIf @if($findingType->type == 'file')a-folder @endIf  "></i> @if($findingType->building_exterior)<span uk-tooltip title="Building Exterior"> BE </span>|@endif @if($findingType->building_system)<span uk-tooltip title="Building System"> BS </span>|@endif @if($findingType->site)<span uk-tooltip title="Site"> S </span>|@endif @if($findingType->common_area)<span uk-tooltip title="Common Area"> CA </span>|@endif @if($findingType->unit)<span uk-tooltip title="Unit"> U </span>|@endif @if($findingType->file)<span uk-tooltip title="File"> F </span>|@endif {{$findingType->name}}
</div>

@endforeach
@elseif($search != '')
<h3 class="uk-width-1-1 uk-margin-top">Sorry, I was not able to match findings to the term "{{$search}}".</h3>
@else
<h2 class="uk-width-1-1 uk-margin-top">Sorry, it doesn't appear the admin has assigned any
	@if($type == 'file') File
	@elseif($type == 'nlt') Non Life Threatening
	@elseif($type == 'lt') Life Threatening
@endif findings to this amenity.</h2>

<p class="uk-margin-top">It is possible the HUD areas assigned to this amenity do not have any of that finding type.</p>
<p> Please contact your admin to get this resolved, or click the <span style="background: #111; color: #fff; width: 40px; height: 20px; padding-top: 1px; display: inline-block;"><a onclick="$('#all-filter-button').trigger('click');"><i class="uk-icon-asterisk uk-contrast " style="    padding-top: 2px;
margin-left: 13px;"></i></a></span> filter to see all possible findings for @if($amenityLocationType == 'b') buildings @elseif($amenityLocationType == 's') site level @else units @endif .</p>
@endif