<script>
	//resizeModal(95);

</script>
<style type="text/css">
	#modal-size-3 {
		height: 85%;
	}
</style>
<div id="modal-project-summary" class="uk-padding-remove uk-margin-bottom">
	<div class="modal-project-summary-left" id="modal-left-summary">
		@include('dashboard.partials.project-summary-left')
	</div>
	<div class="modal-project-summary-right">
		<div style="position: fixed; top: 80px; height: 100%;width: 45%;">
			<div class="modal-project-summary-right-bottom">
				<div id="modal-project-summary-units" class="uk-padding-remove" uk-grid>
					@include('dashboard.partials.project-summary-unit')
				</div>
			</div>
		</div>
		<div class="modal-project-summary-right-top">
			<div class="uk-padding-remove uk-margin-top uk-flex uk-flex-between" >
				<button id="summary-btn-all" class="uk-button uk-button-default uk-button-small button-filter button-filter-selected button-filter-wide" onclick="filterAll();" type="button">ALL</button>
				<button id="summary-btn-selected" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="filterSelected();" type="button">SELECTED</button>
				<button id="summary-btn-unselected" class="uk-button uk-button-default uk-button-small button-filter button-filter-wide" onclick="filterUnselected();" type="button">UNSELECTED</button>
				<button id="" class="uk-button uk-button-default uk-button-small button-filter" onclick="" type="button"><i class="fas fa-filter"></i></button>
				<div class="uk-dropdown uk-dropdown-bottom filter-dropdown filter-program-dropdown" uk-dropdown=" flip: false; pos: bottom-right;" style="top: 26px; left: 0px;">
					<form id="modal-project-summary-program-filter-form">
						<fieldset class="uk-fieldset">
							<div id="modal-project-summary-program-filters" class="uk-margin uk-child-width-auto uk-grid">
								@foreach($actual_programs as $program)
									<!-- @if(session('project-summary-program-{{$program["id"]}}') == 1)
									<input id="filter-project-summary-program-{{$program['id']}}" value="{{ $program['id'] }}" type="checkbox" checked/>
									<label for="filter-project-summary-program-{{$program['id']}}">{{$program['name']}}</label>
									@else -->
									<input id="filter-project-summary-program-{{ $program['program_key'] }}" value="{{ $program['program_key'] }}" type="checkbox"/>
									<label for="filter-project-summary-program-{{ $program['program_key'] }}">{{ $program['program_name'] }} ({{ $program['group_names'] }})</label>
									<!-- @endif -->
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

	function filterUnselected(){
		$('[id^=summary-btn-]').removeClass('button-filter-selected');
		$('#summary-btn-unselected').addClass('button-filter-selected');
		$('.has-selected').fadeOut( "slow", function() {
			$('.no-selection').fadeIn( "slow", function() {});
		});
	}
	function filterBuilding(buildingKey){
		// remove all and then display only selected
		$('[id^=summary-btn-]').removeClass('button-filter-selected');
		window.buildingKey = buildingKey;
		$('.unit-group').hide( 0, function() {
			$('.'+window.buildingKey).fadeIn( "slow");
			console.log('fading in '+window.buildingKey);
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

			$.post('/modals/projects/{{$data["project"]["id"]}}/programs/0/summary', {
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

	function projectSummarySelection(element, unitid, programid=null, grouptypes=null, type="both", buildingkey="none"){
		// ajax call here
		var spinner = '<div style="height:127px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
		// debugger;
		if(buildingkey != 'none') {
			var targetRow = '#program-selection-'+grouptypes+'-'+buildingkey;
			var targetClass = '.program-selection-'+grouptypes+'-'+buildingkey;
		} else {
			var targetRow = '#program-selection-'+grouptypes;
			var targetClass = '.program-selection-'+grouptypes;
		}
		if($(targetRow).length == 0) {
			var targetRow = '#program-selection-'+grouptypes;
			var targetClass = '.program-selection-'+grouptypes;
		}
		console.log('Updating target row '+targetRow);
		$(targetRow).html(spinner);
		$(targetClass).remove();
		$.post('/modals/projects/{{$data["project"]["id"]}}/programs/save-program-unit-inspections', {
			'unit_id' : unitid,
			'program_key' : programid,
			'group_ids' : grouptypes,
			'type' : type,
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			$(targetRow).fadeOut( "slow", function() {
				$(targetRow).replaceWith(data).fadeIn();
			});
			//loadTypes(1);
			//filterProgramSummary();
		}
		);

		// we know which project {{$data["project"]["id"]}}
		// we need to know which unit, which program, if file or physical audit and whether it is checked or unchecked

		// icon clicked at the unit level to toggle all inspectable programs on/off
		debugger;
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



</script>
