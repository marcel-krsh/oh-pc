<div id="modal-project-summary" class="uk-padding-remove uk-margin-bottom">
	<div class="modal-project-summary-left">
		<div uk-grid>
			<div class="uk-width-1-1 uk-padding-remove " style="min-height:200px; margin-top:30px;">
				<canvas id="chartjs-modal-summary" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-1-1 uk-padding-remove uk-text-center">
				<h3>{{$data['project']['name']}}<br /><small>Project # | Audit #</small></h3>
				<table class="uk-table uk-table-small uk-text-left noline small-padding">
					<tbody>
						<tr>
							<td></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						<tr>
							<td>
								<div uk-leader><strong>TOTAL UNITS</strong></div>
							</td>
							<td class="uk-text-center border-right">2,000</td>
							<td class="uk-text-center">2,000</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center border-right">500</td>
							<td class="uk-text-center">500</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">375</td>
							<td class="uk-text-center">375</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">125</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">250</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">250</td>
							<td class="uk-text-center">125</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="modal-project-summary-right">
		
		<div style="position: fixed;
		    top: 80px;
		    height: 100%;
		    width: 45%;">
		<div class="modal-project-summary-right-bottom">
			<div id="modal-project-summary-units" class="uk-padding-remove" uk-grid>
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
			            		@if($unit['status'] != 'not-inspectable')
			            		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle">
			            			@if($program['physical_audit_checked'] == 'true' && $program['file_audit_checked'] == 'true')
			            			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}});"></i>
			            			@else
			            			<i class="a-circle" onclick="projectSummarySelection(this, {{$unit['id']}}, {{$program['id']}});"></i>
			            			@endif
			            		</div>
			            		@endif
			            		<div class="modal-project-summary-unit-program-info">
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
			            			
			            			{{$program['name']}}
			            		</div>
			            	</div>
			            	@endforeach
			            </div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		</div>
		<div class="modal-project-summary-right-top">
			<div class="uk-padding-remove uk-margin-top uk-flex uk-flex-between" >
				<button id="" class="uk-button uk-button-default uk-button-small button-filter button-filter-selected button-filter-wide" onclick="" type="button">ALL</button>
				<button id="" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="" type="button">SELECTED</button>
				<button id="" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="" type="button">UNSELECTED</button>
				<button id="" class="uk-button uk-button-default uk-button-small button-filter" onclick="" type="button"><i class="fas fa-filter"></i></button>
				<div class="uk-dropdown uk-dropdown-bottom filter-dropdown filter-program-dropdown" uk-dropdown=" flip: false; pos: bottom-right;" style="top: 26px; left: 0px;">
        			<form id="modal-project-summary-program-filter-form">
        				<fieldset class="uk-fieldset">
        					<div id="modal-project-summary-program-filters" class="uk-margin uk-child-width-auto uk-grid">
        						@foreach($data['programs'] as $program)
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

	function projectSummarySelection(element, unitid, programid, type="both"){
		// ajax call here

		// we know which project {{$data["project"]["id"]}}
		// we need to know which unit, which program, if file or physical audit and whether it is checked or unchecked
		
		if(type == 'physical'){
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
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle').addClass('a-circle-checked');
			}else{
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
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle').addClass('a-circle-checked');
			}else{
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-quick-toggle i').removeClass('a-circle-checked').addClass('a-circle');
			}

		}else {
			// selecting or deselecting both audits
			if($(element).hasClass('a-circle')){
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon-status i.a-circle').toggleClass("a-circle").toggleClass("a-circle-checked");
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon').addClass("inspectable-selected");

		 		$(element).toggleClass("a-circle");
		 		$(element).toggleClass("a-circle-checked");
			}else{
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon-status i.a-circle-checked').toggleClass("a-circle").toggleClass("a-circle-checked");
				$(element).closest('.modal-project-summary-unit-program').find('.modal-project-summary-unit-program-icon').removeClass("inspectable-selected");

		 		$(element).toggleClass("a-circle-checked");
		 		$(element).toggleClass("a-circle");
			}

			// AJAX CALL HERE
			
		}
	}

	var modalSummaryChart = new Chart(document.getElementById("chartjs-modal-summary"),{
		"type":"doughnut",
		"options": summaryOptions,
		
		"data":{
			"labels": ["Required","Selected","Needed","Inspected", "To Be Inspected"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[0,0,0,30,70],
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
					"data":[0,30,50,0,0],
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
					"data":[100,0,0,0,0],
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