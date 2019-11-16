<script>
resizeModal(95);
</script>
<div id="modal-project-summary" class="uk-padding-remove uk-margin-bottom">
	<div class="modal-project-summary-left">
		<div uk-grid>
			<div class="uk-width-1-1 uk-padding-remove " style="min-height:200px; margin-top:30px;">
				<canvas id="chartjs-modal-summary" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-1-1 uk-padding-remove uk-text-center">
				<h3  uk-tooltip="title:">{{$data['project']['name']}}<br /><small>Project {{$project->project_number}} | Audit {{$project->selected_audit()->audit_id}}</small></h3>
				<table class="uk-table uk-table-small uk-text-left noline small-padding" style="margin-top:40px">
					<tbody>
						<tr>
							<td>
								<div uk-leader><strong>{{$stats['name']}}</strong></div>
							</td>
							<td class="uk-text-center border-right"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td id="stat-req-site" class="uk-text-center border-right">{{$stats['required_units']}}</td>
							<td id="stat-req-file" class="uk-text-center">{{$stats['required_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td id="stat-sel-site" class="uk-text-center border-right">{{$stats['selected_units']}}</td>
							<td id="stat-sel-file" class="uk-text-center">{{$stats['selected_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td id="stat-need-site" class="uk-text-center border-right">{{$stats['needed_units']}}</td>
							<td id="stat-need-file" class="uk-text-center">{{$stats['needed_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
							</td>
							<td id="stat-insp-site" class="uk-text-center border-right">{{$stats['inspected_units']}}</td>
							<td id="stat-insp-file" class="uk-text-center">{{$stats['inspected_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
							</td>
							<td id="stat-tobeinsp-site" class="uk-text-center border-right">{{$stats['to_be_inspected_units']}}</td>
							<td id="stat-tobeinsp-file" class="uk-text-center">{{$stats['to_be_inspected_units_file']}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="modal-project-summary-right">
		<div style="position: fixed; top: 80px; height: 100%;width: 45%;">
			<div class="modal-project-summary-right-bottom">
				<div id="modal-project-summary-units" class="uk-padding-remove" uk-grid>
					@php
					$current_unitid = 0;
					//$current_programkey = 0;
					@endphp
					@foreach($unitprograms as $unitprogram)

					@if($current_unitid != $unitprogram->unit_id)
					@php
						$current_unitid = $unitprogram->unit_id;
						//$current_programkey = $unitprogram->program_key;
					@endphp
					<div class="modal-project-summary-unit summary-unit-{{$unitprogram->unit_id}} @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif {{$unitprogram->unit->building->building_key}}">
						<div class="modal-project-summary-unit-status">
							<i class="a-circle" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}});"></i>
						</div>
						<div class="modal-project-summary-unit-info">
							<div class="modal-project-summary-unit-info-icon">
								<i class="a-marker-basic uk-text-muted uk-link use-hand-cursor" uk-tooltip="title:View On Map;" title="" aria-expanded="false" onclick="window.open('https://maps.google.com/maps?q={{$unitprogram->unit->building->address->line_1}}+{{$unitprogram->unit->building->address->city}}+{{$unitprogram->unit->building->address->state}}+{{$unitprogram->unit->building->address->zip}}');"></i>
							</div>
							<div class="modal-project-summary-unit-info-main">
			            		<h4 class="uk-margin-bottom-remove">{!!$unitprogram->unit->building->address->formatted_address($unitprogram->unit->unit_name)!!}<br />
			            			{{$unitprogram->unit->most_recent_event()->type->event_type_description}}: {{formatDate($unitprogram->unit->most_recent_event()->event_date)}}</h4>
					        </div>
					    </div>
					</div>
					<div class="modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{$unitprogram->unit_id}} @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif" >
			        	<div class="modal-project-summary-unit-program uk-visible-toggle">
		            		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle @if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}">
		            			@if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection())
		            			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
		            			@else
		            			<i class="a-circle" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
		            			@endif
		            		</div>
		            		<div class="modal-project-summary-unit-program-info">
		            			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasSiteInspection()) inspectable-selected @endif" data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'physical');">
		            				<i class="a-mobile"></i>
		            				<div class="modal-project-summary-unit-program-icon-status">
	            						@if($unitprogram->hasSiteInspection())
				            			<i class="a-circle-checked"></i>
				            			@else
				            			<i class="a-circle"></i>
				            			@endif
		            				</div>
		            			</div>
		            			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'file');">
		            				<i class="a-folder"></i>
		            				<div class="modal-project-summary-unit-program-icon-status">
		            					@if($unitprogram->hasFileInspection())
				            			<i class="a-circle-checked"></i>
				            			@else
				            			<i class="a-circle"></i>
				            			@endif
		            				</div>
		            			</div>
		            			{{$unitprogram->program->program_name}}
		            		</div>
		            	</div>
					</div>
					@else
					<div class="modal-project-summary-unit-programs summary-unit-programs-{{$unitprogram->unit_id}} uk-margin-remove uk-width-1-1 @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif">
			        	<div class="modal-project-summary-unit-program uk-visible-toggle">
		            		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle @if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}">
		            			@if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection())
		            			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
		            			@else
		            			<i class="a-circle" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
		            			@endif
		            		</div>
		            		<div class="modal-project-summary-unit-program-info">
		            			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasSiteInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'physical');">
		            				<i class="a-mobile"></i>
		            				<div class="modal-project-summary-unit-program-icon-status">
	            						@if($unitprogram->hasSiteInspection())
				            			<i class="a-circle-checked"></i>
				            			@else
				            			<i class="a-circle"></i>
				            			@endif
		            				</div>
		            			</div>
		            			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasFileInspection()) inspectable-selected @endif" data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'file');">
		            				<i class="a-folder"></i>
		            				<div class="modal-project-summary-unit-program-icon-status">
		            					@if($unitprogram->hasFileInspection())
				            			<i class="a-circle-checked"></i>
				            			@else
				            			<i class="a-circle"></i>
				            			@endif
		            				</div>
		            			</div>
		            			{{$unitprogram->program->program_name}}
		            		</div>
		            	</div>
			        </div>
					@endif

					@endforeach


				</div>
			</div>
		</div>
		<div class="modal-project-summary-right-top" style="background-color: white;">
			<div class="uk-padding-remove uk-margin-top uk-flex uk-flex-between" >
				<button id="summary-btn-all" class="uk-button uk-button-default uk-button-small button-filter button-filter-selected button-filter-wide" onclick="filterAll();" type="button">ALL</button>
				<button id="summary-btn-selected" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="filterSelected();" type="button">SELECTED</button>
				<button id="summary-btn-unselected" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="filterUnselected();" type="button">UNSELECTED</button>
				<button id="" class="uk-button uk-button-default uk-button-small button-filter" onclick="" type="button"><i class="fas fa-filter"></i></button>
				<div class="uk-dropdown uk-dropdown-bottom filter-dropdown filter-program-dropdown" uk-dropdown=" flip: false; pos: bottom-right;" style="top: 26px; left: 0px;">
        			<form id="modal-project-summary-program-filter-form">
        				<fieldset class="uk-fieldset">
        					<div id="modal-project-summary-program-filters" class="uk-margin uk-child-width-auto uk-grid">
        						@foreach($programs as $program)
        							@if(session('project-summary-program-{{$program["id"]}}') == 1)
						            <input id="filter-project-summary-program-{{$program['id']}}" value="{{ $program['id'] }}" type="checkbox" checked/>
									<label for="filter-project-summary-program-{{$program['id']}}">{{$program['name']}}</label>
									@else
						            <input id="filter-project-summary-program-{{$program['id']}}" value="{{ $program['id'] }}" type="checkbox"/>
									<label for="filter-project-summary-program-{{$program['id']}}">{{$program['name']}}</label>
									@endif
        						@endforeach
					        </div>
					        <div class="uk-margin-remove" uk-grid>
                        		<div class="uk-width-1-1">
                        			<button class="uk-button uk-button-primary uk-width-1-1" onclick="filterProgramSummary()">APPLY SELECTION</button>
                        		</div>
                        	</div>
        				</fieldset>
        			</form>
                </div>
			</div>
		</div>
	</div>
</div>

<script>

	var stats = JSON.parse('{!! json_encode($stats) !!}');

	function filterAll(){
		$('[id^=summary-btn-]').removeClass('button-filter-selected');
		$('#summary-btn-all').addClass('button-filter-selected');
		$('.has-selected, .no-selection').fadeIn( "slow", function() {});
	}

	function filterSelected(){
		// remove all and then display only selected
		$('[id^=summary-btn-]').removeClass('button-filter-selected');
		$('#summary-btn-selected').addClass('button-filter-selected');
		$('.no-selection').fadeOut( "slow", function() {
			$('.has-selected').fadeIn( "slow", function() {});
		});
	}

	function filterBuilding(buildingKey){
		// remove all and then display only selected
		$('[id^=summary-btn-]').removeClass('button-filter-selected');

		$('.has-selected, .no-selection').fadeOut( "slow", function() {
			$('.'+buildingKey).fadeIn( "slow", function() {});
		});
	}

	function filterUnselected(){
		$('[id^=summary-btn-]').removeClass('button-filter-selected');
		$('#summary-btn-unselected').addClass('button-filter-selected');
		$('.has-selected').fadeOut( "slow", function() {
			$('.no-selection').fadeIn( "slow", function() {});
		});
	}

	function filterProgramSummary() {
		event.preventDefault();

		$('.filter-program-dropdown').fadeOut("slow", function() {
			var spinner = '<div style="height:200px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	        $('#modal-project-summary-units').html(spinner);

			var form = $('#modal-project-summary-program-filter-form');
			var programsSelected = [];
	        $("#modal-project-summary-program-filter-form input:checkbox:checked").each(function(){
	            programsSelected.push($(this).val());
	        });

			$.post('/modals/projects/{{$data["project"]["id"]}}/programs/{{$data["project"]["selected_program"]}}/summary', {
					'programs' : programsSelected,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					$('#modal-project-summary-units').fadeOut( "slow", function() {
					    $('#modal-project-summary-units').html(data).fadeIn();
					});
				}
			);
		});

	}

	function projectSummarySelection(element, unitid, programid=null, type="both"){
		// ajax call here

		// we know which project {{$data["project"]["id"]}}
		// we need to know which unit, which program, if file or physical audit and whether it is checked or unchecked

		// icon clicked at the unit level to toggle all inspectable programs on/off
		if(programid == null){
			// change element's color and icon
			$(element).closest('.modal-project-summary-unit').addClass('inspectable-selected');
			$(element).toggleClass("a-circle").toggleClass("a-circle-checked");

			// update all programs by simulating clicks
			$(element).closest('.modal-project-summary-unit').find('.modal-project-summary-unit-program-quick-toggle i').trigger('click');
		}else{
			if(type == 'physical'){
				if($(element).find('.modal-project-summary-unit-program-icon-status i').hasClass('a-circle')){
					$(element).find('.modal-project-summary-unit-program-icon-status i').toggleClass("a-circle").toggleClass("a-circle-checked");
					$(element).addClass("inspectable-selected");
				}else{
					$(element).find('.modal-project-summary-unit-program-icon-status i').toggleClass("a-circle-checked").toggleClass("a-circle");
					$(element).removeClass("inspectable-selected");
				}

				// AJAX CALL HERE (we may be able to combine all ajax calls)


				// if both file and physical are checked, change the status of the "both" icon
				var auditIconDivs = $(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon');
				var bothSelected = 1;
				$.each( auditIconDivs, function( key, e ) {
				  if(!$(e).hasClass('inspectable-selected')){
				  	bothSelected = 0;
				  }
				});
				if(bothSelected){
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle').addClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle').addClass('a-circle-checked');
				}else{
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle').removeClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle-checked').addClass('a-circle');
				}

			}else if(type == 'file'){
				if($(element).find('.modal-project-summary-unit-program-icon-status i').hasClass('a-circle')){
					$(element).find('.modal-project-summary-unit-program-icon-status i').toggleClass("a-circle").toggleClass("a-circle-checked");
					$(element).addClass("inspectable-selected");
				}else{
					$(element).find('.modal-project-summary-unit-program-icon-status i').toggleClass("a-circle-checked").toggleClass("a-circle");
					$(element).removeClass("inspectable-selected");
				}

				// AJAX CALL HERE

				// if both file and physical are checked, change the status of the "both" icon
				var auditIconDivs = $(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon');
				var bothSelected = 1;
				$.each( auditIconDivs, function( key, e ) {
				  if(!$(e).hasClass('inspectable-selected')){
				  	bothSelected = 0;
				  }
				});
				if(bothSelected){
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle').addClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle').addClass('a-circle-checked');
				}else{
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle').removeClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle-checked').addClass('a-circle');
				}

			}else {
				// selecting or deselecting both audits
				if($(element).hasClass('a-circle')){
					$(element).closest('.modal-project-summary-unit-program-quick-toggle').addClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon-status i.a-circle').toggleClass("a-circle").toggleClass("a-circle-checked");
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon').addClass("inspectable-selected");

			 		$(element).toggleClass("a-circle");
			 		$(element).toggleClass("a-circle-checked");
				}else{
					$(element).closest('.modal-project-summary-unit-program-quick-toggle').removeClass('inspectable-selected');
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon-status i.a-circle-checked').toggleClass("a-circle").toggleClass("a-circle-checked");
					$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon').removeClass("inspectable-selected");

			 		$(element).toggleClass("a-circle-checked");
			 		$(element).toggleClass("a-circle");
				}

				// AJAX CALL HERE

			}
		}

		// check if all programs for this unit have been selected and update the main check icon

	}

	var modalSummaryChart = new Chart(document.getElementById("chartjs-modal-summary"),{
		"type":"doughnut",
		"options": summaryOptions,

		"data":{
			"labels": ["Required","Selected","Needed","Inspected", "To Be Inspected"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[0,0,0,{{$stats['inspected_units'] + $stats['inspected_units_file']}},{{$stats['to_be_inspected_units'] + $stats['to_be_inspected_units_file']}}],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 1
				},
				{
					"label":"Program 3",
					"data":[0,{{$stats['selected_units'] + $stats['selected_units_file']}},{{$stats['needed_units'] + $stats['needed_units_file']}},0,0],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 1
				},
				{
					"label":"Program 2",
					"data":[{{$stats['required_units'] + $stats['required_units_file']}},0,0,0,0],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 1
				}
			]
		}
	});

</script>