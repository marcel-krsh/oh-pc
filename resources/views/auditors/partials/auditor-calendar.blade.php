	@php $days=[0=>'MON',1=>'TUE',2=>'WED',3=>'THU',4=>'FRI',5=>'SAT',6=>'SUN']; $day = 0; @endphp
	@if($beforeafter != 'before' && $beforeafter != 'after')
	<div>
		<ul id="auditor-calendar" class="uk-child-width-1-1 uk-grid">

			<li id="auditor-calendar-{{$data['summary']['ref-previous']}}" class="grid-schedule-availability" style="display:none;">
				<div class="auditor-calendar-header grid-schedule-availability-header">
					<div class="week-spacer"></div>
					@foreach($data['calendar-previous']['header'] as $header_date)
					<div class="week-day">{{$days[$day]}} @php $day++; if($day > 6){$day = 0;} @endphp {{$header_date}}</div>
					<div class="week-spacer"></div>

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
					@foreach($data['calendar-previous']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif  use-hand-cursor" onclick="setDate('{{$day['date_formatted']}}', '{{$day['date_formatted_name']}}');">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else

						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}" uk-tooltip="title: {{$event['start_time']}} - {{$event['end_time']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}" onclick="deleteAvailability({{$event['id']}})"></i>
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
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar-previous']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center flatpickr selectday"><input type="text" placeholder="Select Date.." data-input style="display:none"> <i class="a-calendar-pencil" data-toggle uk-tooltip="GO TO WEEK..."></i></div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-next']}}">{{$data['calendar-previous']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>
	@endif
			<li id="auditor-calendar-{{$data['summary']['ref']}}" class="grid-schedule-availability" @if($beforeafter == 'before' || $beforeafter == 'after') style="display:none" @endif>
				<div class="auditor-calendar-header grid-schedule-availability-header">
					<div class="week-spacer"></div>
					@php $day = 0 ; @endphp
					@foreach($data['calendar']['header'] as $header_date)
					<div class="week-day">{{$days[$day]}} @php $day++; if($day > 6){$day = 0;} @endphp {{$header_date}}</div>
					<div class="week-spacer"></div>
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
					<div class="day @if($day['no_availability']) no-availability @endif use-hand-cursor" onclick="setDate('{{$day['date_formatted']}}', '{{$day['date_formatted_name']}}');">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-plus"></i>
						</div>
						@else

						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}" uk-tooltip="title: {{$event['start_time']}} - {{$event['end_time']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}" onclick="deleteAvailability({{$event['id']}})"></i>
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
						<div class="uk-width-1-3 uk-text-center flatpickr selectday"><input type="text" placeholder="Select Date.." data-input style="display:none"> <i class="a-calendar-pencil" data-toggle uk-tooltip="GO TO WEEK..."></i></div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-next']}}">{{$data['calendar']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
				<script>
					flatpickr(".selectday", {
					    weekNumbers: true,
					    defaultDate:"today",
					    altFormat: "F j, Y",
					    dateFormat: "Ymd",
					    "locale": {
					        "firstDayOfWeek": 1 // start week on Monday
					    	}
					});
					$('.flatpickr.selectday').change(function(){
						loadCalendar($(this).val());
					});
				</script>
			</li>
	@if($beforeafter != 'before' && $beforeafter != 'after')
			<li id="auditor-calendar-{{$data['summary']['ref-next']}}" class="grid-schedule-availability" style="display:none;">
				<div class="auditor-calendar-header grid-schedule-availability-header">
					<div class="week-spacer"></div>
					@php $day = 0 ; @endphp
					@foreach($data['calendar-next']['header'] as $header_date)
					<div class="week-day ">{{$days[$day]}} @php $day++; if($day > 6){$day = 0;} @endphp {{$header_date}}</div>
					<div class="week-spacer"></div>
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
					@foreach($data['calendar-next']['content'] as $day)
					<div class="day @if($day['no_availability']) no-availability @endif  use-hand-cursor" onclick="setDate('{{$day['date_formatted']}}', '{{$day['date_formatted_name']}}');">
						@if($day['no_availability'])
						<div class="event">
							<i class="a-circle-cross"></i>
						</div>
						@else

						@foreach($day['events'] as $event)
						<div class="event {{$event['status']}} {{$event['class']}} @if(Auth::user()->id == $event['lead']) isLead @endif" data-start="{{$event['start']}}" data-span="{{$event['span']}}" uk-tooltip="title: {{$event['start_time']}} - {{$event['end_time']}}">
							@if($event['icon'] != '')<i class="{{$event['icon']}}" onclick="deleteAvailability({{$event['id']}})"></i>
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
						<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-next']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar-next']['footer']['previous']}}</div>
						<div class="uk-width-1-3 uk-text-center flatpickr selectday"><input type="text" placeholder="Select Date.." data-input style="display:none"> <i class="a-calendar-pencil" data-toggle uk-tooltip="GO TO WEEK..."></i></div>
						<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-next']['footer']['ref-next']}}">{{$data['calendar-next']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
					</div>
				</div>
			</li>

		</ul>
	</div>
	@endif