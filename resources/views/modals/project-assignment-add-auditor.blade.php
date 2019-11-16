<div id="modal-project-assignment-add-auditor" class="uk-padding-remove uk-margin-bottom uk-overflow-auto">
	<h2>Schedule Auditors for {{formatDate($day->date, 'l F d, Y')}}</h2>

	<div id="project-assignment-add-auditor-table" class="uk-margin-large-top uk-margin-large-bottom">
		@if($auditors)
		<div id="project-assignment-add-auditor-table-header" uk-grid>
			<div class="uk-width-3-5 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-1 uk-padding-remove">
				            </div>
						</div>
					</div>
					<div class="uk-width-5-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
									<div class="uk-width-1-1 uk-padding-remove-left">
										AUDITOR NAME<hr />
									</div>
								</div>
							</div>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
					            	<div class="uk-width-1-1">
										TIME AVAILABLE THIS DAY<hr />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uk-width-2-5 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								OPEN<hr />
							</div>
						</div>
					</div>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								STARTING<hr />
							</div>
						</div>
					</div>
					<div class="uk-width-1-2 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								DISTANCE TO PROJECT<hr />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="auditorListScroller" class="uk-overflow-auto uk-margin-top">
			@foreach($auditors as $auditor)
			@if(!$auditor->isAuditorOnAudit($audit->audit_id))
			<div class="project-assignment-add-auditor-row" uk-grid>
				<div class="uk-width-3-5 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-6 uk-padding-remove uk-text-center">
							<div uk-grid>
								<div class="uk-width-1-1 uk-padding-remove">
									<i id="auditor-{{$auditor->id}}" class="@if($auditor->isAuditorOnAudit($audit->audit_id)) a-circle-checked @else a-circle-plus @endif large use-hand-cursor @if($auditor->isScheduled($audit->audit_id, $day->id)) ok-actionable @endif" onclick="addAuditorToAudit({{$auditor->id}});" uk-tooltip="title:@if($auditor->isScheduled($audit->audit_id, $day->id)) CLICK TO REMOVE AUDITOR @else CLICK TO ADD AUDITOR @endif;"></i>
					            </div>
							</div>
						</div>
						<div class="uk-width-5-6 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-1-2 uk-padding-remove">
									<div class="leaders uk-width-1-1">
					    				<div>
					    					<span>{{$auditor->full_name()}}</span>
					    				</div>
					    			</div>
								</div>
								<div class="uk-width-1-2">
									@if(!$auditor->availabilityOnDay($day->id))
									No availability 
									@else
									Available {{$auditor->availabilityOnDay($day->id)[0]}} - {{$auditor->availabilityOnDay($day->id)[1]}}
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-2-5 uk-padding-remove">
					@if($auditor->availabilityOnDay($day->id))
					<div uk-grid>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:8 HOURS ARE OPEN FOR SCHEDULING;">
								@if($auditor->timeAvailableOnDay($day->id))
								{{$auditor->timeAvailableOnDay($day->id)}}
								@endif
							</span>
						</div>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:{{$auditor['starting_tooltip']}};">
								@if($auditor->availabilityOnDay($day->id))
								{{$auditor->availabilityOnDay($day->id)[0]}}
								@endif
							</span>
						</div>
						<div class="uk-width-1-2 uk-text-center" >
							{{$auditor->distanceAndTime($audit->audit_id)[1]}} | {{$auditor->distanceAndTime($audit->audit_id)[0]}} | <i class="a-home-marker" uk-tooltip="title: {!!$auditor->default_address()!!} ;"></i>
						</div>
					</div>
					@endif
				</div>
			</div>
			@endif
			@endforeach
		</div>
		@else
		There are no auditors in the system yet.
		@endif
	</div>

	<div class="project-details-info-assignment-summary uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-1-1 uk-padding-remove uk-text-right">
			<button class="uk-button uk-button-primary" onclick="dynamicModalClose();" type="button">CLOSE WINDOW</button>
		</div>
	</div>

	@if($auditors)
	<div id="project-details-info-assignment-stats" class="uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-2-3" uk-padding-remove>
			<div class="uk-card uk-card-info uk-card-body">
				<div class="uk-grid-small uk-flex-top" uk-grid>
		            <div class="uk-width-1-6">
		                <i class="a-info-circle"></i>
		            </div>
		            <div class="uk-width-5-6">
			            <p>Clicking the <i class="a-circle-plus"></i> icon will add the auditor to your audit.</p> 			            
			            <p>"Time Available This Day" is the time period the auditor stated they are available to be scheduled for audits on the selected day.</p>
			            <p>"Open" is the number of hours that the auditor has left on the selected day that can be scheduled for this audit.</p> 
			            <p>"Starting" is the approximate time they would be available to start their travel to this audit.</p>
			            <p>"Distance to Project" shows the time, miles, and an icon designating if they will be traveling from their default starting point (<i class="a-home-marker"></i>) or from another audit(<i class="a-marker-basic"></i>).</p>
		            </div>
		        </div>
	        </div>
		</div>
	</div>
	@endif
</div>
<script>
	function addAuditorToAudit(auditorid){
		$.post("auditors/"+auditorid+"/addtoaudit/{{$audit->audit_id}}", {
            'dayid' : {{$day->id}},
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!=1){ 
                UIkit.modal.alert(data,{stack: true});
            } else {
            	if($('#auditor-'+auditorid).hasClass('a-circle-plus')){
            		$('#auditor-'+auditorid).removeClass('a-circle-plus')
            		$('#auditor-'+auditorid).addClass('a-circle-checked');
            	}
                UIkit.notification('<span uk-icon="icon: check"></span> Auditor Added', {pos:'top-right', timeout:1000, status:'success'});
                $('#project-details-button-2').trigger( "click" );
            }
        } );
	}

</script>