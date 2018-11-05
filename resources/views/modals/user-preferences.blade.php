<div class="modal-user-preferences">
    <div class="">
	    <div uk-grid> 
	  		<div class="user-preference-col-1  uk-padding-remove uk-margin-small-top">
	  			<div uk-grid> 
	  				<div class="uk-width-1-1 uk-padding-remove-left">
			  			<h3><span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:Brian Greenwood;" class="user-badge user-badge-green user-badge-bigger no-float uk-link">
										BG
									</span> Brian Greenwood <i class="a-pencil-2" onclick=""></i></h3>
			  			<form id="modal-user-contact" class="uk-margin-small-top">
							<fieldset class="uk-fieldset">
								<div class="uk-margin-small-top uk-grid">
						            <input type="text" class="uk-input" value="brian@greenwood360.com"  placeholder="Email"/>
						        </div>
								<div class="uk-margin-small-top uk-grid">
						            <input type="text" class="uk-input" value="(888) 888-8888"  placeholder="Phone"/>
						        </div>
								<div class="uk-margin-small-top uk-grid">
						            <input type="text" class="uk-input" value=""  placeholder=""/>
						        </div>
								<div class="uk-margin uk-grid uk-grid-small uk-margin-small-top" style="margin-left:0;">
									<div class="uk-width-1-2 uk-padding-remove">
							            <select class="uk-select">
							                <option selected>ACTIVE</option>
							                <option>NOT ACTIVE</option>
							            </select>
							        </div>
									<div class="uk-width-1-2">
						            	<input type="text" class="uk-input" value=""  placeholder=""/>
						            </div>
						        </div>
							</fieldset>
						</form>
					</div>
	  				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left">
	  					<hr />
	  					<h3 class="uk-margin-small-top">Addresses <i class="a-circle-plus use-hand-cursor" style="vertical-align: middle; padding-left: 10px;" onclick="auditorAddAddress();"></i></h3>
	  					<div class="uk-grid-small uk-margin-remove" uk-grid>
	  						<div class="uk-width-1-2 uk-padding-remove">
	  							<div class="address">
									<i class="a-mailbox"></i> 12333 Sesame Street<br>Suite 12345<br>City2 State2, 22222
								</div>
	  						</div>
	  						<div class="uk-width-1-2">
	  							<div class="address">
									<i class="a-mailbox"></i> 12333 Sesame Street<br>Suite 12345<br>City2 State2, 22222
								</div>
	  						</div>
	  					</div>
	  				</div>

	  				<div class="uk-width-1-1 uk-margin-small-top uk-padding-remove-left">
	  					<hr />
	  					<h3 class="uk-margin-small-top">Set Availability <i class="a-calendar-pencil use-hand-cursor" style="vertical-align: middle; padding-left: 10px;" onclick="expandModal(this);"></i></h3>
	  					<div class="uk-grid-small uk-margin-remove" uk-grid>
	  						<div class="uk-width-1-3 uk-padding-remove">
	  							<label class="uk-text-small">Max Hours/Day</label>
						        <input type="text" class="uk-input" value="6:00"  placeholder=""/>
	  						</div>
	  						<div class="uk-width-1-3">
	  							<label class="uk-text-small">Lunch</label>
						        <input type="text" class="uk-input" value="0:30"  placeholder=""/>
	  						</div>
	  						<div class="uk-width-1-3">
	  							<label class="uk-text-small">Max Driving/Day</label>
						        <input type="text" class="uk-input" value="2:00"  placeholder=""/>
	  						</div>
	  					</div>
	  				</div>

	  				<div class="uk-width-1-1 uk-margin-remove uk-padding-remove-left">
	  					<hr />
	  					<h3 class="uk-margin-small-top">Companies <i class="a-circle-plus use-hand-cursor" style="vertical-align: middle; padding-left: 10px;" onclick="auditorAddCompany();"></i></h3>
	  					<div class="uk-grid-small uk-margin-remove" uk-grid>
	  						<div class="uk-width-1-2 uk-padding-remove">
	  							<div class="address">
	  								<h4>OHFA</h4>
									<i class="a-mailbox"></i> 12333 Sesame Street<br>Suite 12345<br>City2 State2, 22222
								</div>
	  						</div>
	  						<div class="uk-width-1-2">
	  							<div class="address">
	  								<h4>COMPANY NAME</h4>
									<i class="a-mailbox"></i> 12333 Sesame Street<br>Suite 12345<br>City2 State2, 22222
								</div>
	  						</div>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	  		<div class="user-preference-col-2 uk-padding-remove uk-margin-small-top" style="display:none">
	  			<div uk-grid>
	  				<div class="uk-width-1-1 uk-padding-remove">
	  					<div uk-grid>
	  						<div class="uk-width-1-3">
	  							<label>PRESET</label><br />
	  							<label>S<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>M<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>T<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>W<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>T<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>F<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  							<label>S<br /><input class="uk-checkbox" type="checkbox" checked></label>
	  						</div>
	  						<div class="uk-width-1-6">
	  							<label class="uk-form-label" for="form-stacked-text">START</label>
						        <div class="uk-form-controls">
						            <input class="uk-input uk-form-small" id="form-stacked-text" type="text" placeholder="">
						        </div>
	  						</div>
	  						<div class="uk-width-1-6">
	  							<label class="uk-form-label" for="form-stacked-text">END</label>
						        <div class="uk-form-controls">
						            <input class="uk-input uk-form-small" id="form-stacked-text" type="text" placeholder="">
						        </div>
	  						</div>
	  						<div class="uk-width-1-6">
	  							<label class="uk-form-label" for="form-stacked-text">FROM</label>
						        <div class="uk-form-controls">
						            <input class="uk-input uk-form-small" id="form-stacked-text" type="text" placeholder="">
						        </div>
	  						</div>
	  						<div class="uk-width-1-6">
	  							<label class="uk-form-label" for="form-stacked-text">TO</label>
						        <div class="uk-form-controls">
						            <input class="uk-input uk-form-small" id="form-stacked-text" type="text" placeholder="">
						        </div>
	  						</div>
	  					</div>
	  				</div>
	  				<div class="uk-width-1-1 uk-margin-top">
						<div id="auditor-availability-calendar" class="uk-padding-remove uk-margin-top" >
							<div class="uk-position-relative">
								<ul id="auditor-calendar" class="uk-child-width-1-1 uk-grid">
									<li id="auditor-calendar-{{$data['summary']['ref-previous']}}" class="grid-schedule-availability" style="display:none;">
										<div class="auditor-calendar-header grid-schedule-header">
											<div class="week-spacer"></div>
											@foreach($data['calendar-previous']['header'] as $header_date)
											<div class="week-day @if($loop->iteration == 4) selected @endif">{{$header_date}}</div>
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
										<div class="auditor-calendar-content grid-schedule-content">
											<div class="day-spacer"></div>
											@foreach($data['calendar-previous']['content'] as $day)
											<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 4) selected @endif">
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
										<div class="grid-schedule-availability-footer">
											<div uk-grid>
												<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar-previous']['footer']['previous']}}</div>
												<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar-previous']['footer']['today']}}</div>
												<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar-previous']['footer']['ref-next']}}">{{$data['calendar-previous']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
											</div>
										</div>
									</li>
									<li id="auditor-calendar-{{$data['summary']['ref']}}" class="grid-schedule-availability">
										<div class="auditor-calendar-header grid-schedule-availability-header">
											<div class="week-spacer"></div>
											@foreach($data['calendar']['header'] as $header_date)
											<div class="week-day @if($loop->iteration == 4) selected @endif">{{$header_date}}</div>
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
											<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 4) selected @endif">
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
										<div class="grid-schedule-availability-footer">
											<div uk-grid>
												<div class="uk-width-1-3 uk-padding-remove use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-previous']}}"><i class="a-arrow-left-2"></i> {{$data['calendar']['footer']['previous']}}</div>
												<div class="uk-width-1-3 uk-text-center"><i class="a-calendar-pencil"></i> {{$data['calendar']['footer']['today']}}</div>
												<div class="uk-width-1-3 uk-text-right use-hand-cursor auditor-calendar-nav" onclick="fetchCalendar(this);" data-target="{{$data['calendar']['footer']['ref-next']}}">{{$data['calendar']['footer']['next']}} <i class="a-arrow-right-2_1"></i></div>
											</div>
										</div>
									</li>
									<li id="auditor-calendar-{{$data['summary']['ref-next']}}" class="grid-schedule-availability" style="display:none;">
										<div class="auditor-calendar-header grid-schedule-header">
											<div class="week-spacer"></div>
											@foreach($data['calendar-next']['header'] as $header_date)
											<div class="week-day @if($loop->iteration == 4) selected @endif">{{$header_date}}</div>
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
										<div class="auditor-calendar-content grid-schedule-content">
											<div class="day-spacer"></div>
											@foreach($data['calendar-next']['content'] as $day)
											<div class="day @if($day['no_availability']) no-availability @endif @if($loop->iteration == 4) selected @endif">
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
										<div class="grid-schedule-availability-footer">
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
	  				</div>
	  				<div class="uk-width-1-1 uk-margin-top">
	  					<h4>Exclude:</h4>
	  					<div class="uk-child-width-1-4" uk-grid>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>Saturdays</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>Sundays</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>Holidays</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>...</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>...</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>...</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>...</label></div>
	  						<div class="uk-margin-top"><input class="uk-checkbox" type="checkbox" checked> <label>...</label></div>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	    </div>
	</div>
</div>

 <script>
 	$( document ).ready(function() {
		fillSpacers();
	});

 	function expandModal(element) {
 		if($(element).closest('.uk-modal-body')[0].style.width != '50%') {
 			$('.user-preference-col-2').fadeOut("slow", function(){
	 			$('.user-preference-col-1').animate({ width: "100%" }, 1000, function(){
	 				
	 			});
	 			$(element).closest('.uk-modal-body').animate({ width: "50%" });
	 			$(element).closest('.uk-modal-body').toggleClass("modal-wide");
	 		});
	 		
 		}else{
 			$(element).closest('.uk-modal-body').animate({ width: "90%" });
	 		$(element).closest('.uk-modal-body').toggleClass("modal-wide");

	 		$('.user-preference-col-1').animate({ width: "30%" }, 1000, function(){
	 			$('.user-preference-col-2').css("width", "70%").fadeIn("slow");
	 		});

	 		// $('.user-preference-col-1').switchClass('uk-width-1-1', 'uk-width-1-2', 500, 'swing', function(item){
	 		// 	$('.user-preference-col-1').toggleClass('uk-width-1-2');
	 		// });
 		}
 	}

 	$('.uk-modal-body').animate({
 			width: "50%"
 	});

 	$(document).on('beforehide', '.uk-modal-body', function (item) {
		$(item).removeAttr('style');
	});

	function auditorAddAddress(){
		console.log("adding address");
	}

 	
 </script>
 <style>
 .user-preference-col-1{
 	width:100%;
 }
 .modal-wide {}
.modal-wide .user-preference-col-1 {
	/*width:50%;*/
}
.user-preference-col-2 label {
    display: inline-block;
    margin-right: 8px;
}
.user-preference-col-2 .uk-form-controls {
    margin-top: 6px;
}
.user-preference-col-2 > div {
	padding-left:20px;
}
</style>