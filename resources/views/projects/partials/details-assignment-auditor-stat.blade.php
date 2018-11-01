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

		<div class="uk-width-1-1 uk-padding-remove" style="margin-top: 25px; margin-bottom: 30px;">
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
						<div class="uk-width-3-5">
							<div class="leaders uk-width-1-1">
			    				<div>
			    					<span>Preferred longest single drive time</span>
			    					<span>{{$data['summary']['preferred_longest_drive']}}</span>
			    				</div>
			    			</div>
						</div>
					</div>
				</div>
				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left uk-text-bold">
					<div uk-grid>
						<div class="uk-width-3-5">
							<div class="leaders uk-width-1-1">
			    				<div>
			    					<span>Preferred lunch time</span>
			    					<span>{{$data['summary']['preferred_lunch']}}</span>
			    				</div>
			    			</div>
						</div>
					</div>
				</div>
			</div>
			 
		</div>
	</div>
</div>

<div id="project-details-info-assignment-auditor-calendar" class="grid-schedule uk-width-1-1 uk-margin">
	<div class="grid-schedule-header">
		<div class="week-spacer"></div>
		<div class="week-day">12/18</div>
		<div class="week-spacer"></div>
		<div class="week-day">12/19</div>
		<div class="week-spacer"></div>
		<div class="week-day">12/20</div>
		<div class="week-spacer"></div>
		<div class="week-day">12/21</div>
		<div class="week-spacer"></div>
		<div class="week-day selected">12/22</div>
		<div class="week-spacer"></div>
		<div class="week-day">12/23</div>
		<div class="week-spacer"></div>
		<div class="week-day">12/24</div>  
		<div class="week-spacer"></div>
		<div class="week-day">12/25</div>  
		<div class="week-spacer"></div>
		<div class="week-day">12/26</div> 
		<div class="week-spacer"></div> 
	</div>
	<div class="grid-schedule-sidebar">
		<div>6a</div>
		<div></div>
		<div>8a</div>
		<div></div>
		<div>10a</div>
		<div></div>
		<div>12p</div>
		<div></div>
		<div>2p</div>
		<div></div>
		<div>4p</div>
		<div></div>
		<div>6p</div>
		<div></div>
		<div>8p</div>
	</div>
	<div class="grid-schedule-content">
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
			<div class="event event-start event-end" data-start="2" data-span="2">Class</div>
			<div class="event event-start event-end" data-start="5">Interview</div>
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
			<div class="event event-start event-end" data-start="6" data-span="4">Dinner</div>
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
			<div class="event event-start event-end" data-start="1" data-span="2">School</div>
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
			<div class="event event-start" data-start="2" data-span="4">Meeting</div>
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day selected">
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
		<div class="day">
		</div>
		<div class="day-spacer">
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
			<div></div><div></div><div></div><div></div><div></div><div></div><div></div>
		</div>
	</div>
	<div class="grid-schedule-footer">
		Navigation here
	</div>


	<style>
	.grid-schedule {
		display: grid;
		grid-gap: 0px;
		grid-template-columns: 45px 9fr;
	    grid-template-areas: 
	      "header header "
	      "sidebar content "
	      "footer footer";
	}
	.grid-schedule-header {
	  grid-area: header;
	  display: grid;
	  grid-template-columns: repeat(9, 30px 1fr) 30px;
	  grid-auto-flow: dense;
	  grid-gap: 0px;
	  padding-left: 46px;
      text-align: center;
	}
	.grid-schedule-header .week-day {

	  
	}
	.grid-schedule-sidebar {
		grid-area: sidebar;
		padding-top: 15px;
	}
	.grid-schedule-content {
		grid-area: content;
		display: grid;
		  /*grid-template-columns: repeat(9, 1fr);*/
		  grid-template-columns: repeat(9, 30px 1fr) 30px;
		  grid-auto-flow: dense;
		  grid-gap: 0;
	}
	.grid-schedule-footer {
		grid-area: footer;
	}
	.day, .day-spacer {
		display: grid;
		grid-row-start: 1;
		grid-row-end: 15;
		grid-template-rows: repeat(15, 1fr);
		grid-auto-flow: dense;
		grid-gap: 0px 10px;
	}
	.day {
	    border: 2px solid #ccc;
	}
	.day-spacer div {
	    border-top: 2px solid #ccc;
	}
	.day-spacer div:first-child {
		border-top: 1px solid #ccc;
	}
	.day-spacer div:last-child {
		border-bottom: 1px solid #ccc;
	}
	.day-spacer div:nth-child(even) {
		background-color: #ddd;
	}
	.grid-schedule-sidebar > div {
    	height: 20px;
	}	

	.week-day, .event, .grid-schedule-sidebar > div {
	  padding: 0px 10px;
	}
	.week-day {
		padding-bottom: 15px;
		padding-top: 5px;
	}
	.week-day.selected {
	    border-top: 2px solid #2a2a2a;
	    border-left: 2px solid #2a2a2a;
	    border-right: 2px solid #2a2a2a;
	}
	.day.selected {
		border-top: 0px;
		border-left: 2px solid #2a2a2a;
	    border-right: 2px solid #2a2a2a;
	    border-bottom: 2px solid #2a2a2a;
	}

	.event {
	  background-color: #CCC;
	}

	.event-end { 
	  /*border-bottom-left-radius: 10px; 
	  border-bottom-right-radius: 10px; */
	}

	.event-start { 
	  /*border-top-left-radius: 10px; 
	  border-top-right-radius: 10px; */
	}

	[data-start="1"] { grid-row-start: 1; }
	[data-start="2"] { grid-row-start: 2; }
	[data-start="3"] { grid-row-start: 3; }
	[data-start="4"] { grid-row-start: 4; }
	[data-start="5"] { grid-row-start: 5; }
	[data-start="6"] { grid-row-start: 6; }
	[data-start="7"] { grid-row-start: 7; }
	[data-start="8"] { grid-row-start: 8; }
	[data-start="9"] { grid-row-start: 9; }
	[data-start="10"] { grid-row-start: 10; }
	[data-start="11"] { grid-row-start: 11; }
	[data-start="12"] { grid-row-start: 12; }
	[data-start="13"] { grid-row-start: 13; }
	[data-start="14"] { grid-row-start: 14; }
	[data-start="15"] { grid-row-start: 15; }

	[data-span="1"] { grid-row-end: span 1; }
	[data-span="2"] { grid-row-end: span 2; }
	[data-span="3"] { grid-row-end: span 3; }
	[data-span="4"] { grid-row-end: span 4; }
	[data-span="5"] { grid-row-end: span 5; }
	[data-span="6"] { grid-row-end: span 6; }
	[data-span="7"] { grid-row-end: span 7; }
	[data-span="8"] { grid-row-end: span 8; }
	[data-span="9"] { grid-row-end: span 9; }
	[data-span="10"] { grid-row-end: span 10; }
	[data-span="11"] { grid-row-end: span 11; }
	[data-span="12"] { grid-row-end: span 12; }
	[data-span="13"] { grid-row-end: span 13; }
	[data-span="14"] { grid-row-end: span 14; }
	[data-span="15"] { grid-row-end: span 15; }

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