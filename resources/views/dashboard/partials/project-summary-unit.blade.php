				@foreach($data["units"] as $unit)
				<div class="modal-project-summary-unit uk-width-1-1 {{$unit['status']}}">
					<div class="modal-project-summary-unit-status">
						@if($unit['status'] == 'inspectable')
						<i class="a-circle-checked" uk-tooltip="title:INSPECTABLE;" title="" aria-expanded="false"></i>
						@elseif($unit['status'] == 'not-inspectable')
						<i class="a-circle-cross" uk-tooltip="title:NOT INSPECTABLE;" title="" aria-expanded="false"></i>
						@else
						<i class="a-circle-plus" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" title="" aria-expanded="false"></i>
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
			            		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle">
			            			<i class="a-circle"></i>
			            		</div>
			            		<div class="modal-project-summary-unit-program-info">
			            			<div class="modal-project-summary-unit-program-icon">
			            				<i class="a-mobile"></i>
			            				<div class="modal-project-summary-unit-program-icon-status">
			            					@if($program['physical_audit_checked'] == 'true')
					            			<i class="a-circle-checked"></i>
					            			@else
					            			<i class="a-circle"></i>
					            			@endif
			            				</div>
			            			</div>
			            			<div class="modal-project-summary-unit-program-icon">
			            				<i class="a-folder"></i>
			            				<div class="modal-project-summary-unit-program-icon-status">
			            					@if($program['file_audit_checked'] == 'true')
					            			<i class="a-circle-checked"></i>
					            			@else
					            			<i class="a-circle"></i>
					            			@endif
			            				</div>
			            			</div>
			            			
			            			{{$program['name']}}
			            		</div>
			            	</div>
			            	@endforeach
			            </div>
					</div>
				</div>
				@endforeach