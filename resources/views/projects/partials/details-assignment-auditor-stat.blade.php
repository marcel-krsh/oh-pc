<div id="project-details-info-assignment-auditor-schedule" class="uk-width-2-3 uk-margin-top uk-padding-remove">
	<div uk-grid>
		<div class="uk-width-1-1 itinerary-header">
			<div uk-grid>
				<div class="uk-width-3-5">
					<h3><span uk-tooltip="title:VIEW AUDITOR STATS & DETAILED SCHEDULE;" title="" aria-expanded="false" class="user-badge user-badge-{{$data['summary']['color']}} no-float uk-link" >{{$data['summary']['initials']}}</span> {{$data['summary']['name']}} <i class="a-pencil-2 use-hand-cursor" onclick="" uk-tooltip="title:EDIT;"></i></h3>
				</div>
				<div class="uk-width-1-5 uk-padding-remove uk-text-right">
					AVERAGE<br />HOURS
				</div>
				<div class="uk-width-1-5 uk-text-right uk-padding-right">
					PROJECTED END TIME
				</div>
			</div>
		</div>
		<div  class="uk-width-1-1 uk-margin-small">
			<div class="uk-width-1-1 {{$data['itinerary-start']['status']}} itinerary-container isLead">
				<div uk-grid>
					<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left itinerary itinerary-start">
						<div uk-grid>
							<div class="uk-width-4-5">
								<div class="leaders uk-width-1-1">
				    				<div>
				    					<span><i class="{{$data['itinerary-start']['icon']}}"></i> {{$data['itinerary-start']['name']}}</span>
				    					<span>{{$data['itinerary-start']['average']}}</span>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
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
							<div class="uk-width-4-5 @if($itinerary['type'] != 'start' && $itinerary['type'] != 'end') itinerary-drag @else uk-sortable-nodrag @endif">
								<div class="leaders uk-width-1-1">
				    				<div>
				    					<span><i class="{{$itinerary['icon']}}"></i> {{$itinerary['name']}}</span>
				    					<span>{{$itinerary['average']}}</span>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								{{$itinerary['end']}}
							</div>
						</div>
					</div>
					@if(count($itinerary['itinerary']))
					@foreach($itinerary['itinerary'] as $child)
					<div class="uk-width-1-1 uk-margin-remove itinerary itinerary-child @if($child['type']) itinerary-child-{{$child['type']}} @endif">
						<div uk-grid>
							<div class="uk-width-4-5">
								<div class="leaders uk-width-1-1">
				    				<div>
				    					<span><i class="{{$child['icon']}}"></i> {{$child['name']}}</span>
				    					<span>{{$child['average']}}</span>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
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
							<div class="uk-width-4-5">
								<div class="leaders uk-width-1-1">
				    				<div>
				    					<span><i class="{{$data['itinerary-end']['icon']}}"></i> {{$data['itinerary-end']['name']}}</span>
				    					<span>{{$data['itinerary-end']['average']}}</span>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								{{$data['itinerary-end']['end']}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="uk-width-1-1 uk-padding-remove" style="margin-top: 25px;">
			<div uk-grid>
				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left uk-text-bold" style="color:#56b285;">
					<div uk-grid>
						<div class="uk-width-4-5">
							<div class="leaders uk-width-1-1">
			    				<div>
			    					<span>TOTAL <span class="italic underlined">ESTIMATED</span> TIME COMMITMENT {{$data['summary']['date']}}</span>
			    					<span>{{$data['summary']['total_estimated_commitment']}}</span>
			    				</div>
			    			</div>
						</div>
						<div class="uk-width-1-5 uk-text-center">
						</div>
					</div>
				</div>
				<div class="uk-width-1-1 uk-padding-remove-left uk-text-bold uk-margin-small">
					<div uk-grid>
						<div class="uk-width-4-5">
							<div class="leaders uk-width-1-1">
			    				<div>
			    					<span>Preferred longest single drive time</span>
			    					<span>{{$data['summary']['preferred_longest_drive']}}</span>
			    				</div>
			    			</div>
						</div>
						<div class="uk-width-1-5 uk-text-center">
						</div>
					</div>
				</div>
				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left uk-text-bold">
					<div uk-grid>
						<div class="uk-width-4-5">
							<div class="leaders uk-width-1-1">
			    				<div>
			    					<span>Preferred lunch time</span>
			    					<span>{{$data['summary']['preferred_lunch']}}</span>
			    				</div>
			    			</div>
						</div>
						<div class="uk-width-1-5 uk-text-right">
						</div>
					</div>
				</div>
			</div>
			 
		</div>
	</div>
</div>

<div id="project-details-info-assignment-auditor-calendar" class="uk-width-1-1 uk-margin">
	<div class="week">
	  <div class="week-day">Sunday</div>
	  <div class="week-day">Monday</div>
	  <div class="week-day">Tuesday</div>
	  <div class="week-day">Wednesday</div>
	  <div class="week-day">Thursday</div>
	  <div class="week-day">Friday</div>
	  <div class="week-day">Saturday</div>  
	</div>
	<div class="week">
	  <div class="day">
	    <h3 class="day-label">1</h3>
	    <div class="event event-start event-end" data-span="2">Class</div>
	    <div class="event event-end">Interview</div>
	  </div>
	  <div class="day">
	    <h3 class="day-label">2</h3>
	    <div class="event event-start event-end" data-span="1">Dinner</div>
	  </div>
	  <div class="day">
	    <h3 class="day-label">3</h3>
	    <div class="event event-start event-end" data-span="2">School</div>
	  </div>
	  <div class="day">
	    <h3 class="day-label">4</h3>
	    <div class="event event-start" data-span="4">Meeting</div>
	  </div>
	  <div class="day">
	    <h3 class="day-label">5</h3>
	  </div>
	  <div class="day">
	    <h3 class="day-label">6</h3>
	  </div>
	  <div class="day">
	    <h3 class="day-label">7</h3>
	  </div>
	</div>


	<style>
	.week {
	  display:grid;
	  grid-template-columns: repeat(7, 1fr);
	  grid-auto-flow: dense;
	  grid-gap: 2px 10px;
	}

	.day {
	  display:contents;
	  background-color: #DDD; /* if display contents, this won't color */
	}
	.day-label {
	  grid-row-start: 1;
	  text-align: right;
	  margin:0;
	}

	.week-day, .day-label, .event {
	  padding: 4px 10px;
	}

	.event {
	  background-color: #CCC;
	}

	.event-end { 
	  border-top-right-radius: 10px; 
	  border-bottom-right-radius: 10px; 
	}

	.event-start { 
	  border-top-left-radius: 10px; 
	  border-bottom-left-radius: 10px; 
	}

	.day:nth-child(1) > .event { grid-column-start: 1; }
	.day:nth-child(2) > .event { grid-column-start: 2; }
	.day:nth-child(3) > .event { grid-column-start: 3; }
	.day:nth-child(4) > .event { grid-column-start: 4; }

	[data-span="1"] { grid-column-end: span 1; }
	[data-span="2"] { grid-column-end: span 2; }
	[data-span="3"] { grid-column-end: span 3; }
	[data-span="4"] { grid-column-end: span 4; }
	[data-span="5"] { grid-column-end: span 5; }
	[data-span="6"] { grid-column-end: span 6; }
	[data-span="7"] { grid-column-end: span 7; }

	</style>
</div>

<style>
#project-details-info-assignment-auditor-schedule h3 {
	font-weight: bold;
	margin-top: 10px;
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
    padding: 0;
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
    padding: 7px 0px 5px;
}
.itinerary.itinerary-child {
	margin-top: 5px;
}
.itinerary.itinerary-child .uk-width-4-5 {
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
.uk-padding-right {
	padding-right:15px;
}
</style>