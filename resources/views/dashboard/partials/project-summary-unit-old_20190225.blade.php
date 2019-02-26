				@foreach($data["units"] as $unit)
				<div class="modal-project-summary-unit uk-width-1-1 {{$unit['status']}}">
					<div class="modal-project-summary-unit-status">
						@if($unit['status'] == 'not-inspectable')
						<i class="a-circle-cross" uk-tooltip="title:NOT INSPECTABLE;"></i>
						@else
						<i class="a-circle" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" onclick="projectSummarySelection(this, {{$unit['id']}});"></i>
						@endif
					</div>
					<div class="modal-project-summary-unit-info">
						<div class="modal-project-summary-unit-info-icon">
							<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;" title="" aria-expanded="false"></i>
						</div>
						<div class="modal-project-summary-unit-info-main">
		            		<h4 class="uk-margin-bottom-remove">{{$unit['address']}}<br />{{$unit['address2']}}<br />
			            	Move In Date: {{$unit['move_in_date']}}</h4>
				        </div>
				        <div class="modal-project-summary-unit-programs">
			            	@foreach($unit['programs'] as $program)
			            	<div class="modal-project-summary-unit-program uk-visible-toggle">
			            		@if($unit['status'] != 'not-inspectable' && $program['status'] != 'not-inspectable')
			            		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle @if($program['physical_audit_checked'] == 'true' && $program['file_audit_checked'] == 'true') inspectable-selected @endif">
			            			@if($program['physical_audit_checked'] == 'true' && $program['file_audit_checked'] == 'true')
			            			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}});"></i>
			            			@else
			            			<i class="a-circle" onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}});"></i>
			            			@endif
			            		</div>
			            		@endif
			            		<div class="modal-project-summary-unit-program-info @if($program['status'] == 'not-inspectable') not-inspectable @endif">
			            			@if($program['status'] != 'not-inspectable')
			            			<div class="modal-project-summary-unit-program-icon @if($program['physical_audit_checked'] == 'true') inspectable-selected @endif" @if($unit['status'] != 'not-inspectable') onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}}, 'physical');" @endif>
			            				<i class="a-mobile"></i>
			            				<div class="modal-project-summary-unit-program-icon-status">	
		            					@if($unit['status'] == 'not-inspectable')
		            						<i class="a-circle-cross"></i>
		            					@else
			            					@if($program['physical_audit_checked'] == 'true')
					            			<i class="a-circle-checked"></i>
					            			@else
					            			<i class="a-circle"></i>
					            			@endif
					            		@endif
			            				</div>
			            			</div>
			            			<div class="modal-project-summary-unit-program-icon @if($program['file_audit_checked'] == 'true') inspectable-selected @endif" @if($unit['status'] != 'not-inspectable') onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}}, 'file');" @endif >
			            				<i class="a-folder"></i>
			            				<div class="modal-project-summary-unit-program-icon-status">
			            				@if($unit['status'] == 'not-inspectable')
		            						<i class="a-circle-cross"></i>
		            					@else
			            					@if($program['file_audit_checked'] == 'true')
					            			<i class="a-circle-checked"></i>
					            			@else
					            			<i class="a-circle"></i>
					            			@endif
					            		@endif
			            				</div>
			            			</div>
			            			@else
			            			<div class="modal-project-summary-unit-program-icon">
			            				<i class="a-circle-cross"></i>
			            			</div>
			            			@endif
			            			
			            			{{$program['name']}}
			            		</div>
			            	</div>
			            	@endforeach
			            </div>
					</div>
				</div>
				@endforeach