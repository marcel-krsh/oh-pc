			<li id="auditor-calendar-{{$data['summary']['ref']}}" class="grid-schedule-availability" style="display:none">
				<div class="auditor-calendar-header grid-schedule-availability-header">
					<div class="week-spacer"></div>
					@php $days=array(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']); $day = 0; @endphp
					@foreach($data['calendar']['header'] as $header_date)
					<div class="week-day ">{{$days[$day]}}<br />{{$header_date}}</div>
					<div class="week-spacer"></div>
					@php $day++; if($day > 6){$day = 0;} @endphp
					@endforeach
				</div>
				<div class="grid-schedule-availability-sidebar">
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
				<div class="auditor-calendar-content grid-schedule-availability-content">
					<div class="day-spacer"></div>
					@foreach($data['calendar']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else
						
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
						
						@endif
					</div>
					<div class="day-spacer"></div>
					@endforeach		
				</div>
				<div class="grid-schedule-availability-footer">
					<div uk-grid>
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar']['footer']['today']}}</div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-next']}}">{{$data['calendar']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>