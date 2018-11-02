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

<div id="project-details-info-assignment-auditor-calendar" class="grid-schedule uk-padding-remove uk-width-1-1 uk-margin">
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
		<div>6a</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>8a</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>10a</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>12p</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>2p</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>4p</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>6p</div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div>8p</div><div></div><div></div><div></div>
	</div>
	<div class="grid-schedule-content">
		<div class="day-spacer"></div>
		<div class="day">
			<div class="event beforetime" data-start="1" data-span="8"></div>
			<div class="event event-start event-end action-required" data-start="9" data-span="24"><i class="a-mobile-not"></i></div>
			<div class="event breaktime" data-start="33"></div>
			<div class="event event-start event-end isLead no-border-bottom" data-start="34" data-span="12"><i class="a-mobile-checked"></i></div>
			<div class="event aftertime" data-start="46" data-span="4"></div>
		</div>
		<div class="day-spacer"></div>
		<div class="day">
			<div class="event beforetime" data-start="1" data-span="8"></div>
			<div class="event event-start event-end isLead" data-start="9" data-span="12"><i class="a-mobile-not"></i></div>
			<div class="event breaktime" data-start="21"></div>
			<div class="event available no-border-top no-border-bottom" data-start="22" data-span="24"><i class="a-circle-plus"></i></div>
			<div class="event aftertime" data-start="46" data-span="4"></div>
		</div>
		<div class="day-spacer"></div>
		<div class="day">
			<div class="event beforetime" data-start="1" data-span="8"></div>
			<div class="event event-start event-end action-required isLead" data-start="9" data-span="12"><i class="a-mobile-not"></i></div>
			<div class="event breaktime" data-start="21" data-span="4"></div>
			<div class="event available no-border-top no-border-bottom" data-start="25" data-span="21"><i class="a-circle-plus"></i></div>
			<div class="event aftertime" data-start="46" data-span="4"></div>
		</div>
		<div class="day-spacer"></div>
		<div class="day">
			<div class="event beforetime" data-start="1" data-span="8"></div>
			<div class="event available no-border-top" data-start="9" data-span="16"><i class="a-circle-plus"></i></div>
			<div class="event available no-border-bottom" data-start="30" data-span="16"><i class="a-circle-plus"></i></div>
			<div class="event aftertime" data-start="46" data-span="4"></div>
		</div>
		<div class="day-spacer"></div>
		<div class="day selected">
			<div class="event beforetime" data-start="1" data-span="8"></div>
			<div class="event in-progress isLead" data-start="9" data-span="16"><i class="a-mobile-checked"></i></div>
			<div class="event breaktime" data-start="25"></div>
			<div class="event isLead" data-start="26" data-span="12"><i class="a-folder"></i></div>
			<div class="event no-border-bottom isLead" data-start="38" data-span="8"><i class="a-folder"></i></div>
			<div class="event aftertime" data-start="46" data-span="4"></div>
		</div>
		<div class="day-spacer"></div>
		<div class="day no-availability">
			<div class="event event-start event-end" data-start="1" data-span="15">
				<i class="a-circle-cross"></i>
			</div>
		</div>
		<div class="day-spacer"></div>
		<div class="day no-availability">
			<div class="event event-start event-end" data-start="1" data-span="15">
				<i class="a-circle-cross"></i>
			</div>
		</div>
		<div class="day-spacer"></div>
		<div class="day no-availability">
			<div class="event event-start event-end" data-start="1" data-span="15">
				<i class="a-circle-cross"></i>
			</div>
		</div>
		<div class="day-spacer"></div>
		<div class="day no-availability">
			<div class="event event-start event-end" data-start="1" data-span="15">
				<i class="a-circle-cross"></i>
			</div>
		</div>
		<div class="day-spacer"></div>
	</div>
	<div class="grid-schedule-footer">
		<div uk-grid>
			<div class="uk-width-1-3 uk-padding-remove"><i class="a-arrow-left-2"></i> DECEMBER 13, 2018</div>
			<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> DECEMBER 22, 2018</div>
			<div class="uk-width-1-3 uk-text-right">DECEMBER 31, 2018 <i class="a-arrow-right-2_1"></i></div>
		</div>
	</div>

<script>

	$( document ).ready(function() {
		var TotalRows = 60;
		var i = 0;
		var spacers = '';
		for (i = 0; i < TotalRows; i++) { 
		    spacers = spacers+"<div></div>";
		}
		$('.day-spacer').html(spacers);
	});
</script>

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
	  grid-template-columns: repeat(9, 2% 1fr) 2%;
	  grid-auto-flow: dense;
	  grid-gap: 0px;
	  padding-left: 45px;
      text-align: center;
      color: #999;
	}

	.grid-schedule-header .week-day {

	  
	}
	.grid-schedule-sidebar {
		grid-area: sidebar;
		color: #999;
	}
	.grid-schedule-content {
		grid-area: content;
		display: grid;
		  /*grid-template-columns: repeat(9, 1fr);*/
		  grid-template-columns: repeat(9, 2% 1fr) 2%;
		  grid-auto-flow: dense;
		  grid-gap: 0;
	}
	.grid-schedule-footer {
		grid-area: footer;
		padding: 6px 15px;
    	background-color: #aaa;
    	color: #666;
    	margin-bottom: 50px;
    	margin-top: 2px;
    	margin-left: 45px;
	}
	.grid-schedule-footer i {
		vertical-align: middle;
    	font-size: 18px;
	}
	.day, .day-spacer {
		display: grid;
		grid-row-start: 1;
		grid-row-end: 60;
		grid-template-rows: repeat(60, 8px);
		grid-auto-flow: dense;
		grid-gap: 0px 10px;
	}
	.day {
	    border: 2px solid #ccc;
	}
	.day-spacer div:nth-child(2n+1) {
	    border-top: 2px solid #eee;
	}
	.day-spacer div:nth-child(4n+1) {
	    border-top: 2px solid #ccc;
	}
	.day-spacer div:first-child {
		border-top: 1px solid #ccc;
	}
	.day-spacer div:last-child {
		border-bottom: 1px solid #ccc;
	}
	.day-spacer div:nth-child(8n), .day-spacer div:nth-child(8n-1),
	.day-spacer div:nth-child(8n-2), .day-spacer div:nth-child(8n-3) {
		background-color: #ddd;
	}
	.day-spacer:first-child div {
		border-left: 1px solid #ccc;
	}
	.day-spacer:last-child div {
		border-right: 1px solid #ccc;
	}
	.grid-schedule-sidebar > div {
    	height: 8px;
	}	

	.week-day, .event, .grid-schedule-sidebar > div {
	  padding: 0px 10px;
	}
	.week-day {
		padding-bottom: 15px;
		padding-top: 5px;
	}
	.week-day.selected {
	    border-top: 2px solid #7b7b7b;
	    border-left: 2px solid #7b7b7b;
	    border-right: 2px solid #7b7b7b;
	}
	.day.selected {
		border-top: 0px;
		border-left: 2px solid #7b7b7b;
	    border-right: 2px solid #7b7b7b;
	    border-bottom: 2px solid #7b7b7b;
	}
	.day.no-availability {
		border-top: 2px solid #ddd;
		border-left: 1px solid #eee;
	    border-right: 1px solid #eee;
	    border-bottom: 2px solid #ddd;
	    display: flex;
		align-items: center;
		justify-content: center;
	}
	.event {
	  	background-color: #888;
    	font-size: 16px;
	    align-items: center;
	    display: flex;
	    justify-content: center;
	    border-bottom: 2px solid #fff;
	    color:#fff;
	}

	.event-end { 
	  /*border-bottom-left-radius: 10px; 
	  border-bottom-right-radius: 10px; */
	}

	.event-start { 
	  /*border-top-left-radius: 10px; 
	  border-top-right-radius: 10px; */
	}
	.event.beforetime {
	    background-color: #fff;
	    border-bottom: 2px solid #56b285;
	}
	.event.aftertime {
	    background-color: #fff;
	    border-top: 2px solid #56b285;
	}
	.event.available {
	    background-color: rgb(86, 178, 133, 0.2);
	    border-top: 2px solid rgb(86, 178, 133);
	    border-bottom: 2px solid rgb(86, 178, 133);
	    color: rgb(86, 178, 133);
	}

	.event.no-border-top {
		border-top: none !important;
	}
	.event.no-border-bottom {
		border-bottom: none !important;
	}

	.day.no-availability .event {
		background-color:#ddd;
		width: 100%;
    	text-align: center;
    	height: 100%;
    	display: initial;
	}
	.day.no-availability .event i {
		position: relative;
    	top: 45%;
    	color: #fff;
    	font-size: 16px;
	}

	.event.no-action, .event.no-action.uk-badge { color:#fff; background-color:#939598; opacity: 0.5;}
	.event.action-needed { color:#fff; background-color:#76338b; opacity: 0.5; }
	.event.action-required, .event.critical { color:#fff; background-color:#da328a; opacity: 0.5; }
	.event.ok-actionable, .event.ok-actionable { color:#fff; background-color:#56b285; opacity: 0.5; }
	.event.in-progress, .event.in-progress { color:#fff; background-color:#49ade9; opacity: 0.5; }

	.event.isLead, .event.available, .event.beforetime, .event.aftertime, .event.breaktime, .no-availability .event {
		opacity: 1;
	}

	[data-start="1"] { grid-row-start: 1;}
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
	[data-start="16"] { grid-row-start: 16;}
	[data-start="17"] { grid-row-start: 17; }
	[data-start="18"] { grid-row-start: 18; }
	[data-start="19"] { grid-row-start: 19; }
	[data-start="20"] { grid-row-start: 20; }
	[data-start="21"] { grid-row-start: 21; }
	[data-start="22"] { grid-row-start: 22; }
	[data-start="23"] { grid-row-start: 23; }
	[data-start="24"] { grid-row-start: 24; }
	[data-start="25"] { grid-row-start: 25; }
	[data-start="26"] { grid-row-start: 26; }
	[data-start="27"] { grid-row-start: 27; }
	[data-start="28"] { grid-row-start: 28; }
	[data-start="29"] { grid-row-start: 29; }
	[data-start="30"] { grid-row-start: 30; }
	[data-start="31"] { grid-row-start: 31;}
	[data-start="32"] { grid-row-start: 32; }
	[data-start="33"] { grid-row-start: 33; }
	[data-start="34"] { grid-row-start: 34; }
	[data-start="35"] { grid-row-start: 35; }
	[data-start="36"] { grid-row-start: 36; }
	[data-start="37"] { grid-row-start: 37; }
	[data-start="38"] { grid-row-start: 38; }
	[data-start="39"] { grid-row-start: 39; }
	[data-start="40"] { grid-row-start: 40; }
	[data-start="41"] { grid-row-start: 41;}
	[data-start="42"] { grid-row-start: 42; }
	[data-start="43"] { grid-row-start: 43; }
	[data-start="44"] { grid-row-start: 44; }
	[data-start="45"] { grid-row-start: 45; }
	[data-start="46"] { grid-row-start: 46; }
	[data-start="47"] { grid-row-start: 47; }
	[data-start="48"] { grid-row-start: 48; }
	[data-start="49"] { grid-row-start: 49; }
	[data-start="50"] { grid-row-start: 50; }
	[data-start="51"] { grid-row-start: 51;}
	[data-start="52"] { grid-row-start: 52; }
	[data-start="53"] { grid-row-start: 53; }
	[data-start="54"] { grid-row-start: 54; }
	[data-start="55"] { grid-row-start: 55; }
	[data-start="56"] { grid-row-start: 56; }
	[data-start="57"] { grid-row-start: 57; }
	[data-start="58"] { grid-row-start: 58; }
	[data-start="59"] { grid-row-start: 59; }
	[data-start="60"] { grid-row-start: 60; }

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
	[data-span="16"] { grid-row-end: span 16; }
	[data-span="17"] { grid-row-end: span 17; }
	[data-span="18"] { grid-row-end: span 18; }
	[data-span="19"] { grid-row-end: span 19; }
	[data-span="20"] { grid-row-end: span 20; }
	[data-span="21"] { grid-row-end: span 21; }
	[data-span="22"] { grid-row-end: span 22; }
	[data-span="23"] { grid-row-end: span 23; }
	[data-span="24"] { grid-row-end: span 24; }
	[data-span="25"] { grid-row-end: span 25; }
	[data-span="26"] { grid-row-end: span 26; }
	[data-span="27"] { grid-row-end: span 27; }
	[data-span="28"] { grid-row-end: span 28; }
	[data-span="29"] { grid-row-end: span 29; }
	[data-span="30"] { grid-row-end: span 30; }
	[data-span="31"] { grid-row-end: span 31; }
	[data-span="32"] { grid-row-end: span 32; }
	[data-span="33"] { grid-row-end: span 33; }
	[data-span="34"] { grid-row-end: span 34; }
	[data-span="35"] { grid-row-end: span 35; }
	[data-span="36"] { grid-row-end: span 36; }
	[data-span="37"] { grid-row-end: span 37; }
	[data-span="38"] { grid-row-end: span 38; }
	[data-span="39"] { grid-row-end: span 39; }
	[data-span="40"] { grid-row-end: span 40; }
	[data-span="41"] { grid-row-end: span 41; }
	[data-span="42"] { grid-row-end: span 42; }
	[data-span="43"] { grid-row-end: span 43; }
	[data-span="44"] { grid-row-end: span 44; }
	[data-span="45"] { grid-row-end: span 45; }
	[data-span="46"] { grid-row-end: span 46; }
	[data-span="47"] { grid-row-end: span 47; }
	[data-span="48"] { grid-row-end: span 48; }
	[data-span="49"] { grid-row-end: span 49; }
	[data-span="50"] { grid-row-end: span 50; }
	[data-span="51"] { grid-row-end: span 51; }
	[data-span="52"] { grid-row-end: span 52; }
	[data-span="53"] { grid-row-end: span 53; }
	[data-span="54"] { grid-row-end: span 54; }
	[data-span="55"] { grid-row-end: span 55; }
	[data-span="56"] { grid-row-end: span 56; }
	[data-span="57"] { grid-row-end: span 57; }
	[data-span="58"] { grid-row-end: span 58; }
	[data-span="59"] { grid-row-end: span 59; }
	[data-span="60"] { grid-row-end: span 60; }

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