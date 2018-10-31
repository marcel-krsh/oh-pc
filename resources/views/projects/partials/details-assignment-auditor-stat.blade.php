<div id="project-details-info-assignment-auditor-schedule" class="uk-width-1-1 uk-margin-top">
	<div class="uk-width-1-1 itinerary-header">
		<div uk-grid>
			<div class="uk-width-3-5">
				<h3><span uk-tooltip="title:VIEW AUDITOR STATS & DETAILED SCHEDULE;" title="" aria-expanded="false" class="user-badge user-badge-{{$data['summary']['color']}} no-float uk-link" >{{$data['summary']['initials']}}</span> {{$data['summary']['name']}} <i class="a-pencil-2 use-hand-cursor" onclick="" uk-tooltip="title:EDIT;"></i></h3>
			</div>
			<div class="uk-width-1-5 uk-text-center">
				AVERAGE HOURS
			</div>
			<div class="uk-width-1-5 uk-text-center">
				PROJECTED END TIME
			</div>
		</div>
	</div>
</div>
<div  class="uk-width-1-1 uk-margin-small">
	<div class="uk-width-1-1 {{$data['itinerary-start']['status']}} itinerary-container isLead">
		<div uk-grid>
			<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left itinerary itinerary-start">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span><i class="{{$data['itinerary-start']['icon']}}"></i> {{$data['itinerary-start']['name']}}</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['itinerary-start']['average']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['itinerary-start']['end']}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="uk-width-1-1 uk-margin-remove" uk-sortable="handle: .itinerary-drag">
	@foreach($data['itinerary'] as $itinerary)
	<div class="uk-width-1-1 {{$itinerary['status']}} itinerary-container @if(Auth::user()->id == $itinerary['lead']) isLead @endif">
		<div uk-grid>
			<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left itinerary @if($itinerary['type']) itinerary-{{$itinerary['type']}} @endif">
				<div uk-grid>
					<div class="uk-width-3-5 @if($itinerary['type'] != 'start' && $itinerary['type'] != 'end') itinerary-drag @else uk-sortable-nodrag @endif">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span><i class="{{$itinerary['icon']}}"></i> {{$itinerary['name']}}</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$itinerary['average']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$itinerary['end']}}
					</div>
				</div>
			</div>
			@if(count($itinerary['itinerary']))
			@foreach($itinerary['itinerary'] as $child)
			<div class="uk-width-1-1 uk-margin-remove itinerary itinerary-child @if($child['type']) itinerary-child-{{$child['type']}} @endif">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span><i class="{{$child['icon']}}"></i> {{$child['name']}}</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$child['average']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$child['end']}}
					</div>
				</div>
			</div>
			@endforeach
			@endif
		</div>
	</div>
	@endforeach
</div>
<div class="uk-width-1-1 uk-margin-remove">
	<div class="uk-width-1-1 {{$data['itinerary-end']['status']}} itinerary-container isLead">
		<div uk-grid>
			<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left itinerary itinerary-end">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span><i class="{{$data['itinerary-end']['icon']}}"></i> {{$data['itinerary-end']['name']}}</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['itinerary-end']['average']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['itinerary-end']['end']}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="uk-width-1-1 uk-margin">
	<div class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left uk-text-bold" style="color:#56b285;">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span>TOTAL <span class="italic underlined">ESTIMATED</span> TIME COMMITMENT {{$data['summary']['date']}}</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['summary']['total_estimated_commitment']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
					</div>
				</div>
			</div>
			<div class="uk-width-1-1 uk-padding-remove-left uk-text-bold uk-margin-small">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span>Preferred longest single drive time</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['summary']['preferred_longest_drive']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
					</div>
				</div>
			</div>
			<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left uk-text-bold">
				<div uk-grid>
					<div class="uk-width-3-5">
						<div class="leaders uk-width-1-1">
		    				<div>
		    					<span>Preferred lunch time</span>
		    				</div>
		    			</div>
					</div>
					<div class="uk-width-1-5 uk-text-center">
						{{$data['summary']['preferred_lunch']}}
					</div>
					<div class="uk-width-1-5 uk-text-center">
					</div>
				</div>
			</div>
		</div>
		 
	</div>
</div>

<style>
#project-details-info-assignment-auditor-schedule h3 {
	font-weight: bold;
}
#project-details-info-assignment-auditor-schedule h3 span.user-badge {
    margin-right: 10px;
    color: #fff;
    padding: 2px;
    margin-top: -4px;
}
.itinerary-header {
	margin-bottom:10px;
	color: #999;
}
.itinerary {
    border: none;
    padding: 7px 0px 5px;
    margin-top: 3px;
    font-weight: bold;
}
.itinerary-container .uk-grid .uk-grid{
	opacity: 0.5;
	margin-bottom: 5px;
}
.itinerary-container.isLead .uk-grid .uk-grid{
	opacity: 1;
}
.itinerary.itinerary-start, .itinerary.itinerary-end {
    border: 1px solid #ddd;
    margin-bottom: 5px;
}
.itinerary.itinerary-child {
	margin-top: 5px;
}
.itinerary.itinerary-child .uk-width-3-5 {
	padding-left:40px;
}

.itinerary.itinerary-start, .itinerary.itinerary-end, .itinerary {
  	position: relative;
  	opacity:1 !important;
}

.itinerary-child:after {
    content: '';
    width: 13px;
    left: 13px;
    position: absolute;
    top: 40%;
  	opacity:1 !important;
}
.itinerary-child:before {
    border-left: 1px solid #e6e7e9;
    width: 1px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 13px;
    content: '';
    top: 40%;
  	opacity:1 !important;
}
.itinerary:not(.itinerary-child):after {
    content: '';
    width: 13px;
    left: 13px;
    border-bottom: 1px solid #e6e7e9;
    position: absolute;
    top: 40%;
  	opacity:1 !important;
}
.itinerary:not(.itinerary-child):before {
    border-left: 1px solid #e6e7e9;
    width: 1px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 13px;
    content: '';
    top: 40%;
  	opacity:1 !important;
}
.itinerary-start:not(.itinerary-child):after {
    content: '';
    width: 12px;
    left: 12px;
    border-bottom: 1px solid #e6e7e9;
    position: absolute;
    top: 50%;
  	opacity:1 !important;
}
.itinerary-start:not(.itinerary-child):before {
    border-left: 1px solid #e6e7e9;
    width: 1px;
    height: 100%;
    position: absolute;
    top: 0;
    left: 12px;
    content: '';
    top: 50%;
    padding-top: 40px;
  	opacity:1 !important;
}
.itinerary-end:not(.itinerary-child):after {
    content: '';
    width: 12px;
    left: 12px;
    border-bottom: 1px solid #e6e7e9;
    position: absolute;
    top: 50%;
  	opacity:1 !important;
}
.itinerary-end:not(.itinerary-child):before {
    border-left: 1px solid #e6e7e9;
    width: 1px;
    height: 50%;
    position: absolute;
    top: 0;
    left: 12px;
    content: '';
  	opacity:1 !important;
}
</style>