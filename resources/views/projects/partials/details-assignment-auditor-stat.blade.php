@include('projects.templates.calendar')
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
				    					<span class="use-hand-cursor"><i class="{{$data['itinerary-start']['icon']}}"></i> {{$data['itinerary-start']['name']}}</span>
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">STREET:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['address']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">UNIT:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['unit']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">CITY:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['city']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">STATE:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['state']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">ZIP:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['zip']}}">
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
				    					<span class="use-hand-cursor">{{$data['itinerary-start']['average']}}</span>
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">START:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">END:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								<span>{{$data['itinerary-start']['end']}}</span>
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
				    					<span class="@if(Auth::user()->id == $itinerary['lead']) use-hand-cursor @endif">{{$itinerary['average']}}</span>
				    					@if(Auth::user()->id == $itinerary['lead'])
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">START:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">END:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
										@endif
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								<span class="">{{$itinerary['end']}}</span>
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
				    					<span class="@if(Auth::user()->id == $itinerary['lead']) use-hand-cursor @endif">{{$child['average']}}</span>
				    					@if(Auth::user()->id == $itinerary['lead'])
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">START:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">END:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
										@endif
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								<span >{{$child['end']}}</span>
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
				    					<span class="use-hand-cursor"><i class="{{$data['itinerary-end']['icon']}}"></i> {{$data['itinerary-end']['name']}}</span>
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">STREET:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['address']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">UNIT:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['unit']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">CITY:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['city']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">STATE:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['state']}}">
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">ZIP:</label>
												        <div class="uk-form-controls">
												            <input class="uk-input" type="text" value="{{$data['itinerary-start']['zip']}}">
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
				    					<span class="use-hand-cursor">{{$data['itinerary-end']['average']}}</span>
				    					<div class="" uk-drop="mode: click">
										    <div class="uk-card uk-card-body uk-card-rounded">
										        <ul class="uk-list no-hover uk-form-horizontal ">
						                        	<li onclick="">
												        <label class="uk-form-label">START:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
						                        	<li onclick="">
												        <label class="uk-form-label">END:</label>
												        <div class="uk-form-controls">
												            <select class="uk-select">
												                <option>8:30 AM</option>
												                <option>8:45 AM</option>
												                <option>9:00 AM</option>
												                <option>9:15 AM</option>
												                <option>9:30 AM</option>
												                <option>9:45 AM</option>
												                <option>10:00 AM</option>
												                <option>10:15 AM</option>
												            </select>
												        </div>
						                        	</li>	
							                    </ul>
										    </div>
										</div>
				    				</div>
				    			</div>
							</div>
							<div class="uk-width-1-5 uk-text-right uk-padding-right">
								<span>{{$data['itinerary-end']['end']}}</span>
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

<div id="project-details-info-assignment-auditor-calendar" class="uk-padding-remove uk-width-1-1 uk-margin" >
	<div class="uk-position-relative">
		<ul id="auditor-calendar" class="uk-child-width-1-1 uk-grid">
			<li id="auditor-calendar-{{$data['summary']['ref-previous']}}" class="grid-schedule" style="display:none;">
				<div class="auditor-calendar-header grid-schedule-header">
					<div class="week-spacer"></div>
					@foreach($data['calendar-previous']['header'] as $header_date)
					<div class="week-day @if($loop->iteration == 5) selected @endif">{{$header_date}}</div>
					<div class="week-spacer"></div>
					@endforeach
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
				<div class="auditor-calendar-content grid-schedule-content">
					<div class="day-spacer"></div>
					@foreach($data['calendar-previous']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 5) selected @endif">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else
						<div class="event beforetime" data-start="{{$day['before_time_start']}}" data-span="{{$day['before_time_span']}}"></div>
						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}"></i>@endif
							@if(Auth::user()->id == $event['lead'] && $event['icon'] != '') 
							<div class="" uk-drop="mode: click">
							    <div class="uk-card uk-card-body uk-card-rounded">
							    	@if($event['modal_type'] == 'choose-filing')
							        <ul class="uk-list">
			                        	<li onclick=""><i class="a-folder"></i> File Audit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i> Site Visit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i><i class="a-folder"></i> Both</li>	
				                    </ul>
				                    @elseif($event['modal_type'] == 'change-date')
				                    <ul class="uk-list no-hover uk-form-horizontal ">
			                        	<li onclick="">
									        <label class="uk-form-label">START:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:00 AM</option>
									                <option>8:15 AM</option>
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									                <option>10:30 AM</option>
									                <option>10:45 AM</option>
									                <option>11:00 AM</option>
									                <option>11:15 AM</option>
									                <option>11:30 AM</option>
									                <option>11:45 AM</option>
									                <option>12:00 PM</option>
									                <option>12:15 PM</option>
									                <option>12:30 PM</option>
									                <option>12:45 PM</option>
									                <option>1:00 PM</option>
									                <option>1:15 PM</option>
									                <option>1:30 PM</option>
									                <option>1:45 PM</option>
									                <option>2:00 PM</option>
									                <option>2:15 PM</option>
									                <option>2:30 PM</option>
									                <option>2:45 PM</option>
									                <option>3:00 PM</option>
									                <option>3:15 PM</option>
									                <option>3:30 PM</option>
									                <option>3:45 PM</option>
									            </select>
									        </div>
			                        	</li>	
			                        	<li onclick="">
									        <label class="uk-form-label">END:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									            </select>
									        </div>
			                        	</li>	
				                    </ul>
				                    @endif
							    </div>
							</div>
							@endif
						</div>
						@endforeach
						<div class="event aftertime" data-start="{{$day['after_time_start']}}" data-span="{{$day['after_time_span']}}"></div>
						@endif
					</div>
					<div class="day-spacer"></div>
					@endforeach		
				</div>
				<div class="grid-schedule-footer">
					<div uk-grid>
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar-previous']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar-previous']['footer']['today']}}</div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-next']}}">{{$data['calendar-previous']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>
			<li id="auditor-calendar-{{$data['summary']['ref']}}" class="grid-schedule">
				<div class="auditor-calendar-header grid-schedule-header">
					<div class="week-spacer"></div>
					@foreach($data['calendar']['header'] as $header_date)
					<div class="week-day @if($loop->iteration == 5) selected @endif">{{$header_date}}</div>
					<div class="week-spacer"></div>
					@endforeach
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
				<div class="auditor-calendar-content grid-schedule-content">
					<div class="day-spacer"></div>
					@foreach($data['calendar']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 5) selected @endif">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else
						<div class="event beforetime" data-start="{{$day['before_time_start']}}" data-span="{{$day['before_time_span']}}"></div>
						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}"></i>@endif
							@if(Auth::user()->id == $event['lead'] && $event['icon'] != '') 
							<div class="" uk-drop="mode: click">
							    <div class="uk-card uk-card-body uk-card-rounded">
							    	@if($event['modal_type'] == 'choose-filing')
							        <ul class="uk-list">
			                        	<li onclick=""><i class="a-folder"></i> File Audit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i> Site Visit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i><i class="a-folder"></i> Both</li>	
				                    </ul>
				                    @elseif($event['modal_type'] == 'change-date')
				                    <ul class="uk-list no-hover uk-form-horizontal ">
			                        	<li onclick="">
									        <label class="uk-form-label">START:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:00 AM</option>
									                <option>8:15 AM</option>
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									                <option>10:30 AM</option>
									                <option>10:45 AM</option>
									                <option>11:00 AM</option>
									                <option>11:15 AM</option>
									                <option>11:30 AM</option>
									                <option>11:45 AM</option>
									                <option>12:00 PM</option>
									                <option>12:15 PM</option>
									                <option>12:30 PM</option>
									                <option>12:45 PM</option>
									                <option>1:00 PM</option>
									                <option>1:15 PM</option>
									                <option>1:30 PM</option>
									                <option>1:45 PM</option>
									                <option>2:00 PM</option>
									                <option>2:15 PM</option>
									                <option>2:30 PM</option>
									                <option>2:45 PM</option>
									                <option>3:00 PM</option>
									                <option>3:15 PM</option>
									                <option>3:30 PM</option>
									                <option>3:45 PM</option>
									            </select>
									        </div>
			                        	</li>	
			                        	<li onclick="">
									        <label class="uk-form-label">END:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									            </select>
									        </div>
			                        	</li>	
				                    </ul>
				                    @endif
							    </div>
							</div>
							@endif
						</div>
						@endforeach
						<div class="event aftertime" data-start="{{$day['after_time_start']}}" data-span="{{$day['after_time_span']}}"></div>
						@endif
					</div>
					<div class="day-spacer"></div>
					@endforeach		
				</div>
				<div class="grid-schedule-footer">
					<div uk-grid>
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar']['footer']['today']}}</div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-next']}}">{{$data['calendar']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>
			<li id="auditor-calendar-{{$data['summary']['ref-next']}}" class="grid-schedule" style="display:none;">
				<div class="auditor-calendar-header grid-schedule-header">
					<div class="week-spacer"></div>
					@foreach($data['calendar-next']['header'] as $header_date)
					<div class="week-day @if($loop->iteration == 5) selected @endif">{{$header_date}}</div>
					<div class="week-spacer"></div>
					@endforeach
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
				<div class="auditor-calendar-content grid-schedule-content">
					<div class="day-spacer"></div>
					@foreach($data['calendar-next']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 5) selected @endif">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else
						<div class="event beforetime" data-start="{{$day['before_time_start']}}" data-span="{{$day['before_time_span']}}"></div>
						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}"></i>@endif
							@if(Auth::user()->id == $event['lead'] && $event['icon'] != '') 
							<div class="" uk-drop="mode: click">
							    <div class="uk-card uk-card-body uk-card-rounded">
							    	@if($event['modal_type'] == 'choose-filing')
							        <ul class="uk-list">
			                        	<li onclick=""><i class="a-folder"></i> File Audit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i> Site Visit Only</li>	
			                        	<li onclick=""><i class="a-mobile-home"></i><i class="a-folder"></i> Both</li>	
				                    </ul>
				                    @elseif($event['modal_type'] == 'change-date')
				                    <ul class="uk-list no-hover uk-form-horizontal ">
			                        	<li onclick="">
									        <label class="uk-form-label">START:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:00 AM</option>
									                <option>8:15 AM</option>
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									                <option>10:30 AM</option>
									                <option>10:45 AM</option>
									                <option>11:00 AM</option>
									                <option>11:15 AM</option>
									                <option>11:30 AM</option>
									                <option>11:45 AM</option>
									                <option>12:00 PM</option>
									                <option>12:15 PM</option>
									                <option>12:30 PM</option>
									                <option>12:45 PM</option>
									                <option>1:00 PM</option>
									                <option>1:15 PM</option>
									                <option>1:30 PM</option>
									                <option>1:45 PM</option>
									                <option>2:00 PM</option>
									                <option>2:15 PM</option>
									                <option>2:30 PM</option>
									                <option>2:45 PM</option>
									                <option>3:00 PM</option>
									                <option>3:15 PM</option>
									                <option>3:30 PM</option>
									                <option>3:45 PM</option>
									            </select>
									        </div>
			                        	</li>	
			                        	<li onclick="">
									        <label class="uk-form-label">END:</label>
									        <div class="uk-form-controls">
									            <select class="uk-select">
									                <option>8:30 AM</option>
									                <option>8:45 AM</option>
									                <option>9:00 AM</option>
									                <option>9:15 AM</option>
									                <option>9:30 AM</option>
									                <option>9:45 AM</option>
									                <option>10:00 AM</option>
									                <option>10:15 AM</option>
									            </select>
									        </div>
			                        	</li>	
				                    </ul>
				                    @endif
							    </div>
							</div>
							@endif
						</div>
						@endforeach
						<div class="event aftertime" data-start="{{$day['after_time_start']}}" data-span="{{$day['after_time_span']}}"></div>
						@endif
					</div>
					<div class="day-spacer"></div>
					@endforeach		
				</div>
				<div class="grid-schedule-footer">
					<div uk-grid>
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-next']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar-next']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar-next']['footer']['today']}}</div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-next']['footer']['ref-next']}}">{{$data['calendar-next']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>
		</ul>
	</div>
</div>
<script>
	$( document ).ready(function() {
		fillSpacers();
	});

	function fetchCalendar(element){
	    
	    var target = $(element).attr('data-target');
	    // hide all 
	    $(element).closest('.grid-schedule').fadeOut("slow", function() {
	    	// fade in new calendar
	    	$('#auditor-calendar-'+target).fadeIn("slow");
	    });

	    // next or previous dates are already loaded, load the next set
		if($('#auditor-calendar-'+target).prev().length){

			// console.log("there is another calendar available before");

		}else{

			// console.log("we need to load a calendar before");

			var url = 'projects/'+{{$data['project']['id']}}+'/assignments/addauditor/'+{{$data['summary']['id']}}+'/loadcal/'+target+'/before';
		    $.get(url, {}, function(data) {
	            if(data=='0'){ 
	                UIkit.modal.alert("There was a problem getting the calendar.");
	            } else {
					$('#auditor-calendar-'+target).before(data);
					fillSpacers();
	        	}
	        });

		}

		if($('#auditor-calendar-'+target).next().length){
			// console.log("there is another calendar available after");
		}else{
			// console.log("we need to load a calendar after");
			var url = 'projects/'+{{$data['project']['id']}}+'/assignments/addauditor/'+{{$data['summary']['id']}}+'/loadcal/'+target+'/after';
		    $.get(url, {}, function(data) {
	            if(data=='0'){ 
	                UIkit.modal.alert("There was a problem getting the calendar.");
	            } else {
					$('#auditor-calendar-'+target).after(data);
					fillSpacers();
	        	}
	        });
		}
	}

</script>