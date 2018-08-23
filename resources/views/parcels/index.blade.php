<?php // 'parcels','totalParcels', 'currentUser', 'parcels_sorted_by_query','parcelsAscDesc', 'parcelsAscDescOpposite','programs' ?>
<?php setlocale(LC_MONETARY, 'en_US'); ?>
@if(Request::query('page') < 2)
		<?php
		session(['previousParcelID' => '']);

		session(['previousAddress' => '']);
		?>

<script>
    // enable infinite scroll:
    window.getContentForListId = 1;
    window.gettingHtml = 0;
</script>
<STYLE>
         		.date {
         			width: 40px;
         			background: #fff;
					text-align: center;
					font-family: 'Helvetica', sans-serif;
					position: relative;
         		}
         		.year {
         			display: block;
         			background-color: lightgray;
         			color: white;
         			font-size: 12px;
         		}
         		.m-d {
         			font-size: 14px;
         			line-height: 14px;
				    padding-top: 7px;
				    margin-top: 0px;
				    padding-bottom: 7px;
				    margin-bottom: 0px;

         		}

				.keepMe {
					background-color: #ffecec !important;
				}
				.deletedParcel {
					opacity: .25;
    				text-decoration: line-through;
				}
				.filter-drops{
					    -webkit-appearance: none;
					    -moz-appearance: none;
					    margin: 0;
					    border: none;
					    overflow: visible;
					    font: inherit;
					    color: #3a3a3a;
					    text-transform: none;
					    display: inline-block;
					    box-sizing: border-box;
					    padding: 0 12px;
					    background-color: #f5f5f5;
					    vertical-align: middle;
					    line-height: 28px;
					    min-height: 30px;
					    font-size: 1rem;
					    text-decoration: none;
					    text-align: center;
					    border: 1px solid rgba(0, 0, 0, 0.06);
					    border-radius: 4px;
					    text-shadow: 0 1px 0 #ffffff;
					    background: url(/images/select_icon.png) no-repeat;
					    background-position: 5px 7px;
					    text-indent: 13.01px;
					    background-size: 18px;
					    background-color: #f5f5f5;
				}
				select::-ms-expand {
				    display: none;
				}
				.countFlag{
					position: relative;
				    left: -2px;
				    top: -9px;
				    width: 10px;
				    height: 10px;
				    border-radius: 5px;
				    display: inline-block;
				    font-size: 9px;
				    text-align: center;
				    line-height: 9px;
				    color: white;
				    background-color: grey;
				}
				.gray-flag {
					background-color: lightgrey;
				}

         	</STYLE>


    <?php $filtered = 0; // use this to show or hide the filters when it is first loaded. ?>
	<div id="parcelFilters" class="uk-child-width-1-1 uk-child-width-1-4@s" style="display:none;" uk-grid>

		@if(Auth::user()->entity_type == "hfa")

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_program_filter='+this.value);">
				<option value="ALL"
					@if ($parcelsProgramFilter == '%%')
					 selected

					@endIf
					>
					FILTER BY PROGRAM
				</option>

				@if($targetAreaFilter != '%%')
					<optgroup label="NOTE: Applying a program filter will clear your target area filter."></optgroup>
				@endif
				@foreach ($programs as $program)

					<option value="{{$program->id}}"
					@if ($parcelsProgramFilter == $program->id)
					selected
					<?php $programFiltered = $program->program_name; if(Auth::User()->entity_type == 'hfa') {$filtered = 1;} ?>
					@endif
					>
						{{$program->program_name}}

					</option>
				@endforeach
			</select>
		</div>
		@endIf
		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_status_filter='+this.value)">

						<option value="ALL"
						@if ($parcelsStatusFilter == '%%')
						 selected

						@endif
						>
						FILTER BY @if(Auth::user()->entity_type == 'hfa') LANDBANK @endIf STATUS
						</option>
					@foreach ($statuses as $status)
						<option value="{{$status->id}}"

						@if ($parcelsStatusFilter == $status->id)
						 selected
						 <?php $statusFiltered = $status->option_name;  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;} ?>
						@endif
						 >
							{{$status->option_name}}

						</option>
					@endforeach
				</select>

		</div>
		@if(Auth::user()->entity_type == 'hfa')

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&hfa_parcels_status_filter='+this.value)">
					<option value="ALL"
						@if ($hfaParcelsStatusFilter == '%%')
						 selected

						@endif
						>
						FILTER BY HFA STATUS
						</option>
					@foreach ($hfaStatuses as $hfaStatus)

						<option value="{{$hfaStatus->id}}"
						@if ($hfaParcelsStatusFilter == $hfaStatus->id)
						 selected
						 <?php $hfaStatusFiltered = $hfaStatus->option_name;  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;} ?>
						@endif
						>
							{{$hfaStatus->option_name}}

						</option>
					@endforeach
				</select>

		</div>

		@endIf

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_next_filter='+this.value)">
					<option value="ALL"
						@if ($parcelsNextFilter == '%%')
						 selected

						@endif
						>
						FILTER BY NEXT STEP
						</option>
						@php
						$previousStep = ""; $previousStepOpen = 0; $previousType = ""; $stepsOutput = 0; 
						@endphp

					@foreach ($nextSteps as $nextStep)
						@if($previousType != $nextStep->guide_step_type_id)
							@if($previousStepOpen == 1)
								</optgroup>
								@if($stepsOutput == 0)
									<optgroup label="(NO PARCELS AT THIS STEP)"></optgroup>
									<optgroup></optgroup>
								@endIf
							@endIf
							<optgroup label="==================================="></optgroup>
							<optgroup label="{{strtoupper($nextStep->type_name)}}"></optgroup>
							<optgroup label="==================================="></optgroup>
							<?php $previousType = $nextStep->guide_step_type_id; $stepsOutput = 0; $previousStepOpen = 0; 
							?>
						@endif

						@if($previousStep != $nextStep->id && is_null($nextStep->parent_id))

							@if($previousStepOpen == 1)
								</optgroup>
								@if($stepsOutput == 0)
									<optgroup label="(NO PARCELS AT THIS STEP)"></optgroup>
								@endIf
							@endIf
							<optgroup label=""></optgroup>
							<optgroup label="{{$nextStep->name}}">
							<?php 
							$previousStep = $nextStep->id; $previousStepOpen = 1; $stepsOutput = 0; 
							?>
						@endif

						@if(!is_null($nextStep->parent_id))
							@if(Auth::user()->entity_type == "hfa")
								@if($nextStep->isNextStep()->count() > 0)
									<option value="{{$nextStep->id}}"
									@if ($parcelsNextFilter == $nextStep->id)
									 selected
									 <?php $nextStepFiltered = $nextStep->name; ?>
									@endif
									>
									{{$nextStep->name}}

									</option>
									<?php $stepsOutput++; ?>
								@endif
							@else
								@if($nextStep->isNextStep()->where('entity_id',Auth::user()->entity_id)->count() > 0)
									<option value="{{$nextStep->id}}"
									@if ($parcelsNextFilter == $nextStep->id)
									 selected
									 <?php $nextStepFiltered = $nextStep->name; ?>
									@endif
									>
									@if($nextStep->hfa == 1) HFA:@endIf {{$nextStep->name}}

									</option>

									<?php $stepsOutput++; ?>
								@endif
							@endIf
						@endIf
					@endforeach
					</optgroup>
					@if($stepsOutput == 0)
							<optgroup label="(NO PARCELS AT THIS STEP)"></optgroup>
					@endIf

				</select>

		</div>

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&retainage_filter='+this.value)">

						<option value="ALL"
						@if ($retainageFilter == '%%')
						 selected

						@endif
						>
						FILTER BY RETAINAGE STATUS
						</option>




						<option value="1"
						@if ($retainageFilter == 1)
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Unpaid Retainage Only
						</option>
						<option value="2"
						@if ($retainageFilter == 2)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Paid Retainage Only
						</option>
						<option value="3"
						@if ($retainageFilter == 3)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							No Retainage Only
						</option>

				</select>

		</div>

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&advance_filter='+this.value)">

						<option value="ALL"
						@if ($advanceFilter == '%%')
						 selected

						@endif
						>
						FILTER BY ADVANCED PAYMENTS
						</option>




						<option value="1"
						@if ($advanceFilter == 1)
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Unpaid Advances Only
						</option>
						<option value="2"
						@if ($advanceFilter == 2)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Paid Advances Only
						</option>
						<option value="3"
						@if ($advanceFilter == 3)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							No Advances Only
						</option>

				</select>

		</div>
		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&target_area_filter='+this.value)">
					<option value="ALL"
						@if ($targetAreaFilter == '%%')
						 selected

						@endif
						>
						FILTER BY
						@if($programFiltered)
							{{ strtoupper($programFiltered)}}
						@endIf TARGET AREA
						@if(is_null($programFiltered))
							(ACTIVE ONLY)
						@endIf
						</option>

						<?php $previousTAGroup = ""; $previousTAGroupOpened = 0; ?>
					@foreach ($targetAreas as $targetArea)

						@if(Auth::User()->entity_type == 'hfa')
							@if($previousTAGroup != $targetArea->program_name)
								@if($previousTAGroupOpened == 1)
									</optgroup>
									<optgroup label="–––––––––––––––––––––––––––––––––––––––––––––––––"></optgroup>
								@endIf
								<optgroup label="{{strtoupper($targetArea->program_name)}}">
									<optgroup label=" "></optgroup>
								<?php $previousTAGroup = $targetArea->program_name; $previousTAGroupOpened = 1; ?>
							@endIf
						@endif

						<option value="{{$targetArea->id}}"
						@if ($targetAreaFilter == $targetArea->id)
						 selected
						 <?php
						 if(Auth::User()->entity_type == 'hfa' && is_null($programFiltered))
						 	{
							 	$targetAreaFiltered = $targetArea->program_name.' : '.$targetArea->target_area_name;
							 	if(!is_null($programFiltered)){

							 		$targetAreaFiltered = $targetArea->target_area_name;
							 	}
							 } else {
							 	$targetAreaFiltered = $targetArea->target_area_name;
							 }
						 	if(Auth::User()->entity_type == 'hfa') {$filtered = 1;} ?>
						@endif
						>
							{{$targetArea->target_area_name}}

						</option>
					@endforeach

					@if(Auth::User()->entity_type == 'hfa')

						@if($previousTAGroupOpened == 1)
							</optgroup>
						@endIf

					@endIf

				</select>

		</div>
		<?php /*
		@if(Auth::user()->entity_type == 'hfa')
		<div class="uk-width-1-1 uk-width-medium-1-4 uk-margin-top ">
			<select class="uk-form filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&compliance_filter='+this.value)">

						<option value="ALL"
						@if ($complianceFilter == '%%')
						 selected

						@endif
						>
						FILTER BY COMPLIANCE STATUS
						</option>




						<option value="1"
						@if ($complianceFilter == 1)
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							All Reviewed
						</option>
						<option value="2"
						@if ($complianceFilter == 2)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Incomplete Reviews Only
						</option>
						<option value="3"
						@if ($complianceFilter == 3)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Completed Reviews Only
						</option>
						<option value="4"
						@if ($complianceFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Has Passed Reviews Only
						</option>
						<option value="4"
						@if ($complianceFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Has Failed Reviews Only
						</option>
						<option value="4"
						@if ($complianceFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							No Reviews Only
						</option>

				</select>

		</div>
		<div class="uk-width-1-1 uk-width-medium-1-4 uk-margin-top ">
			<select class="uk-form filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&site_visit_filter='+this.value)">

						<option value="ALL"
						@if ($siteVisitFilter == '%%')
						 selected

						@endif
						>
						FILTER BY SITE VISIT STATUS
						</option>




						<option value="1"
						@if ($siteVisitFilter == 1)
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							All Visited
						</option>
						<option value="2"
						@if ($siteVisitFilter == 2)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Incomplete Visits Only
						</option>
						<option value="3"
						@if ($siteVisitFilter == 3)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Completed Visits Only
						</option>
						<option value="4"
						@if ($siteVisitFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Has Passed Visits Only
						</option>
						<option value="4"
						@if ($siteVisitFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							Has Failed Visits Only
						</option>
						<option value="4"
						@if ($siteVisitFilter == 4)
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						 >
							No Visits Only
						</option>

				</select>

		</div>
		@endIf

			*/ ?>

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&disposition_filter='+this.value)">
					<option value="ALL"
						@if ($dispositionFilter == '%%')
						 selected

						@endif
						>
						FILTER BY DISPOSITION STATUS
						</option>
						<option value="9"
						@if ($dispositionFilter == '9')
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						>
						Has Dispostions
						</option>
					<option value="10"
						@if ($dispositionFilter == '10')
						 selected
						 <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						>
						No Dispostions
						</option>

					@foreach ($dispositionStatuses as $dispositionStatus)

						<option value="{{$dispositionStatus->id}}"
						@if ($dispositionFilter == $dispositionStatus->id)
						 selected
						 <?php $dispositionFiltered = $dispositionStatus->invoice_status_name; ?><?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>
						@endif
						>
							{{$dispositionStatus->invoice_status_name}}

						</option>
					@endforeach
					<option value="11"
						@if ($dispositionFilter == '11')
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>

						@endif
						>
						Released Requested
						</option>
					<option value="12"
						@if ($dispositionFilter == '12')
						 selected <?php  if(Auth::User()->entity_type == 'hfa') {$filtered = 1;}  ?>

						@endif
						>
						Released
						</option>
				</select>

		</div>
{{--
		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&step_completed_filter='+this.value)">
				<option value="ALL" @if ($stepCompletedFilter == '0') selected @endif >
					FILTER BY STEP COMPLETED
				</option>
				@foreach ($steps as $step)
					<option value="{{$step->id}}" @if($stepCompletedFilter == $step->id) selected @endif >
						{{$step->name_completed}}
					</option>
				@endforeach
			</select>
		</div>

		<div class="uk-margin-top ">
			<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&step_not_completed_filter='+this.value)">
				<option value="ALL" @if ($stepNotCompletedFilter == '0') selected @endif >
					FILTER BY STEP NOT COMPLETED
				</option>	
				@foreach ($steps as $step)
					<option value="{{$step->id}}" @if($stepNotCompletedFilter == $step->id) selected @endif >
						{{$step->name_completed}}
					</option>
				@endforeach
			</select>
		</div>
--}}
		

	</div>
	<div class="uk-no-margin-top" uk-grid>
		<div class="uk-width-1-1">

			<a class="uk-badge uk-text-right@s" onclick="$('#parcelFilters').slideToggle();$('#filter-up').toggle();$('#filter-down').toggle();" uk-tooltip="CLICK TO SHOW/HIDE FILTER OPTIONS"  style="background: #CCC; margin-top: 15px;"><span class="a-circle-plus" id="filter-down"></span><span class="a-arrow-small-up" style="display:none;" id="filter-up"></span> <span class="a-parameters-2"></span> </a>
			<div class="uk-badge uk-text-right@s " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totalParcels) }} PARCELS&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_program_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_status_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($hfaStatusFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&hfa_parcels_status_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> HFA: {{strtoupper($hfaStatusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($nextStepFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_next_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> NEXT STEP: {{strtoupper($nextStepFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($stepCompletedFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_step_completed_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> COMPLETED STEP: {{strtoupper($stepCompletedFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($retainageFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&retainage_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> {{strtoupper($retainageFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($advanceFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&advance_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> {{strtoupper($advanceFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($targetAreaFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&target_area_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> <?php echo strtoupper($targetAreaFiltered); ?></a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($dispositionFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&disposition_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> {{strtoupper($dispositionFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
{{--
			@if(isset($stepCompletedFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_step_completed_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> COMPLETED STEP: {{strtoupper($stepCompletedFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

			@if(isset($stepNotCompletedFiltered))
			 <div class="uk-badge uk-text-right@s " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&parcels_step_not_completed_filter=ALL')" class="uk-dark uk-light"><span class="a-circle-cross"></span> NOT COMPLETED STEP: {{strtoupper($stepNotCompletedFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
--}}
			@if($totalParcels > 0)
				@if(Auth::user()->id < 3)
					<div class="uk-badge uk-text-right@s uk-margin-right" style="background: darkred; margin-top: 15px; float:right;">&nbsp;<a href="/dashboard/parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&export=1&export_partially_paid_only=1" target="_blank" class="uk-dark uk-light"  uk-tooltip="Export Matching Parcels On Partially Paid Invoices"><span class="a-lower"></span> EXPORT PARTIALLY PAID ONLY</a>&nbsp;</div> &nbsp;

					<div class="uk-badge uk-text-right@s  uk-margin-right" style="background: black; margin-top: 15px; float:right;">&nbsp;<a href="/dashboard/parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&export=1&export_paid_only=1" target="_blank" class="uk-dark uk-light"  uk-tooltip="Export Matching Parcels On Fully Paid Invoices"><span class="a-lower"></span> EXPORT FULLY PAID ONLY</a>&nbsp;</div> &nbsp;
			
{{--
					<div class="uk-badge uk-text-right@s  uk-margin-right" style="background: black; margin-top: 15px; float:right;">&nbsp;<a href="#" target="_blank" class="uk-dark uk-light" onclick="exportPaidParcels();" uk-tooltip="Export Matching Parcels On Fully Paid Invoices"><span class="a-lower"></span> EXPORT FULLY PAID ONLY</a>&nbsp;</div> &nbsp;--}}
				@endIf

				<div class="uk-badge uk-text-right@s uk-margin-right " style="background: green; margin-top: 15px; float:right;">&nbsp;<a href="/dashboard/parcel_list?parcels_sort_by={{ $parcels_sorted_by_query }}&parcels_asc_desc={{ $parcelsAscDesc }}&export=1" target="_blank" class="uk-dark uk-light" uk-tooltip="Export All Matching Parcels"><span class="a-lower"></span> EXPORT</a>&nbsp;</div> &nbsp;


			@endIf
			<?php /*
			<div class="uk-badge uk-text-right " style="background: #4D4D4D; margin-top: 15px;">COST: </div>
			<div class="uk-badge uk-text-right " style="background: #4D4D4D; margin-top: 15px;">REQ: </div>
			<div class="uk-badge uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PO: </div>
			<div class="uk-badge uk-text-right " style="background: #4D4D4D; margin-top: 15px;">INV: </div>
			<div class="uk-badge uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PAID: </div>
			*/ ?>

		<hr class="dashed-hr">
		<div class="uk-overflow-auto" class="margin:4px;">

		<table class="uk-table uk-table-hover uk-table-striped" style="min-width: 1420px;">
		<thead >
			<tr >
				<th width="50" colspan="2">
				<a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=12&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" >Date <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} date-arrow-{{ $parcels_sorted_by_query }}"></span>
				</th>
				<th width="100"><span class="a-info-circle"></span> <a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=1&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" uk-tooltip="Sorting by parcel id will highlight duplicate parcels by their parcel id." >Parcel Id <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} parcel-arrow-{{ $parcels_sorted_by_query }}"></span></a></th>
				<th width="200"><span class="a-map-marker"></span> <a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=2&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" uk-tooltip="Sorting by address will highlight duplicate parcels by their street address.">Address <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} address-arrow-{{ $parcels_sorted_by_query }}"></span></a></th>
				<th width="90">


					<a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=14&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" >Target Area <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} target-area-arrow-{{ $parcels_sorted_by_query }}"></span></a>
				 </th>
				<th width="70">


					<a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=6&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" >Program <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} program-arrow-{{ $parcels_sorted_by_query }}"></span></a>
				 </th>
				<th width="120"><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=11&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" >Status <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} status-arrow-{{ $parcels_sorted_by_query }}"></span></a></th>
				@if(Auth::user()->entity_type == 'hfa')
				<th width="120"><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','parcel_list?parcels_sort_by=13&parcels_asc_desc={{ $parcelsAscDescOpposite }}');" >HFA Status <span class="{{ $parcelsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $parcelsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} hfa-status-arrow-{{ $parcels_sorted_by_query }}"></span></a></th>
				@endIf
				<th width="110">Next</th>

				<th width="">

					<a  class="uk-link-muted"><span class="a-file-tool" uk-tooltip="Retainage" ></span></a>
					 |
					<span class="a-comment-ellipsis" uk-tooltip="Communications"></span>
					 |
					<span class="a-files-layout" uk-tooltip="Documents"></span>
					 |
					 <a class="uk-link-muted"><span class="a-tag" uk-tooltip="Dispositions"></span><div class="countFlag"></div></a>

					@if(Auth::user()->canDeleteParcels())
					 |
						<span class="a-trash-4"></span>
					@endIf


				</th>

			</tr>
		</thead>
		<tbody id="results-list">
	@endif
	<?php
	if(Request::query('page')){
		$i = (Request::query('page')-1) * 100;
	}

	// we use sessions for these because of the infinite scroll - and only set them if they are not already set


	?>
	@if($totalParcels > 0)
	    @foreach ($parcels as $parcel )

	    	<?php $i = $i + 1; ?>
	    	<tr id="parcel-{{str_replace(' ','',$parcel->id)}}" <?php if((session('previousParcelID') !== str_replace('-','',strtoupper($parcel->parcel_id))) && (session('previousAddress') != strtoupper($parcel->street_address))) { ?> class="hideMe" <?php } else { ?> class="keepMe" <?php } ?>>
	    	<?php if((session('previousParcelID') !== str_replace('-','',strtoupper($parcel->parcel_id)) ) && (session('previousAddress') != strtoupper($parcel->street_address))) {?>
	         <td width="1">
	         <td width="49">

	        <?php } else { ?>
	         <td width="5">
	         	<span class="a-avatar-exclamation red-text"></span>
	         <script>
	         		 $('#parcel-{{str_replace(' ','',session('previousParcelSystemID'))}}').removeClass('hideMe');
	         		 $('#parcel-{{str_replace(' ','',session('previousParcelSystemID'))}}').addClass('keepMe');
	         </script>
	         </td>
	         <td width="45">
	         <?php } ?>

	         	<div class="date">


					<p class="m-d">{{ date('m',strtotime($parcel->created_at)) }}/{{ date('d',strtotime($parcel->created_at)) }}</p><span class="year">{{ date('Y',strtotime($parcel->created_at)) }}</span>
				</div>
			 </td>
	         <td width="">

	         <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}','1',0,0)"><span uk-tooltip="System ID  {{$parcel->id}} @if(isset($parcel->importId->id)), a parcel in import {{$parcel->importId->import_id}} on {{date('n/j/y \a\t g-h a', strtotime($parcel->importId->created_at))}} by {{$parcel->importId->import->imported_by->name}} @endif. ">{{ $parcel->parcel_id }}</span></a><br /><small style="color:lightgray;">ROW# <?php echo $i; ?></small> </td>
	         <?php
	         session(['previousParcelID' => str_replace('-','',strtoupper($parcel->parcel_id))]);
	         session(['previousParcelSystemID' => $parcel->id]);
	         session(['previousAddress' => strtoupper($parcel->street_address)]);
	         ?>
	         <td width=""><a href="{{ $parcel->google_map_link}}" target="_blank">{{ $parcel->street_address }}<br /> {{ $parcel->city }}, {{$parcel->state->state_acronym }} {{ $parcel->zip }}</a></td>
	         <td width="220"> {{ $parcel->targetArea->target_area_name }}
	         </td>
	         <td width="220"> {{ $parcel->program->program_name }}
	         </td>
	         <td width=""> {{ $parcel->landbankPropertyStatus->option_name }}
	         </td>
		     @if(Auth::user()->entity_type == 'hfa')
		     <td width=""> {{ $parcel->hfaPropertyStatus->option_name }}
		     </td>
	         @endIf
	        <td width="">
	        <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}','1',0,0)">
	        	<strong>@if(isset($parcel->guide_next_step) && isset($parcel->guide_next_step)) {{$parcel->guide_next_step->name}} @else N/A @endif</strong>
	        </a>
		 	</td>
		         <td>
		         @php
		         	// DO SOME DOCUMENT DETERMINATION


			        $documents = $parcel->documents()->select('categories','approved','notapproved')->orderBy('created_at', 'desc')->get();
			        $reference = array();
			        $reference['unreviewed'] = 0;
			        $reference['reviewed']  = 0;
			        $reference['approved']  = 0;
			        $reference['notapproved']  = 0;
			        $reference['totalCategories']  = 0;
			        $reference['totalDocuments']  = $documents->count();
			        $reference['flag'] = 'No document categories have been submitted. Nada. Zilch.';
			        if(AUTH::user()->entity_type != "hfa"){
			        	$documentClass = "attention";
			        	$need = "are available"; //plural for lb
			        	$needs = "is available"; // singular for lb
			        } else {
			        	$documentClass = "";
			        	$need = "need";
			        	$needs = "needs";
			        }
			        $documentIcon = "file-o";
			        if($reference['totalDocuments'] > 0){
			        	echo "<!-- We're in -->";
			            foreach($documents as $document){

			                $categories = array(); // store the new associative array cat id, cat name



			                // get all categories
			                if($document->categories){
			                    $document->categories = json_decode($document->categories, true);
			                }else{
			                    $document->categories = array();
			                }

			                // get approved category id in an array
			                if($document->approved){
			                    $document->approved_array = json_decode($document->approved, true);
			                }else{
			                    $document->approved_array = array();
			                }

			                // get notapproved category id in an array
			                if($document->notapproved){
			                    $document->notapproved_array = json_decode($document->notapproved, true);
			                }else{
			                    $document->notapproved_array = array();
			                }
			                $total = count($document->categories);
			                $approved = count($document->approved_array);
			                $notapproved = count($document->notapproved_array);


			                $reference['unreviewed'] += $total - ($approved + $notapproved);
			                $reference['reviewed'] += $approved + $notapproved;
			                $reference['approved'] += $approved;
			                $reference['notapproved'] += $notapproved;
			                $reference['totalCategories'] += $total;
			                $reference['totalDocuments'] ++;
			            }
			            if($reference['unreviewed'] > 0){
			            	//singular reference

			                $reference['flag'] = $reference['unreviewed'].' document category '.$needs.' to be reviewed by the HFA.';
			                if(AUTH::user()->entity_type == "hfa"){
			                	$documentClass = "attention";
			                } else {
			                	$documentClass = "";
			                }
			                $documentIcon = "file-text";


			            }
			            if($reference['unreviewed'] > 1){
			            	//plural reference
			                $reference['flag'] = $reference['unreviewed'].' document categories '.$need.' to be reviewed by the HFA.';
			                if(AUTH::user()->entity_type == "hfa"){
			                	$documentClass = "attention";
			                } else {
			                	$documentClass = "";
			                }
			                $documentIcon = "file-text";
			            }
			            if($reference['reviewed'] == $reference['totalCategories'] && $reference['reviewed'] > 0){
			            	//singular reference
			                $reference['flag'] = 'The '.$reference['totalCategories'].' submitted supporting document category has been reviewed by the HFA.';
			                $documentClass = "";
			                $documentIcon = "file-text-o";
			            }
			            if($reference['reviewed'] == $reference['totalCategories'] && $reference['reviewed'] > 1){
			            	//plural reference
			                $reference['flag'] = 'All '.$reference['totalCategories'].' submitted supporting document categories have been reviewed by the HFA.';
			                $documentIcon = "file-text-o";
			                $documentClass = "";
			            }
			            if($reference['reviewed'] == $reference['notapproved'] && $reference['reviewed'] > 0){
			            	//singular reference
			                $reference['flag'] = 'The '.$reference['totalCategories'].' and only submitted document category has NOT been approved. Ouch.';
			                $documentClass = "attention uk-badge uk-badge-danger uk-dark uk-light";
			                $documentIcon = "exclamation";
			            }
			            if($reference['reviewed'] == $reference['notapproved'] && $reference['reviewed'] > 1){
			            	//plural reference
			                $reference['flag'] = 'None of the '.$reference['totalCategories'].' submitted document categories have been approved. Ouch.';
			                $documentClass = "attention uk-badge uk-badge-danger uk-dark uk-light";
			                $documentIcon = "exclamation";
			            }

			        }

			        /// Process Communications
			        $messages = $parcel->communications;


			        $messageClass = "";
			        $messageIcon = "comment-o";
			        $message = array();
			        $messageStatus = "No messages.";
			        $messageClass = "gray-text";
					$myUnseenMessages = 0;
					$mymessages = 0;

					$messageTotal = $messages->count();

			        if($messageTotal > 0){
			        	$messageStatus = "There is $messageTotal message.";
			        	$messageIcon = "commenting-o";
			        	$messageClass = "";
			        }
			        if($messageTotal > 1){
			        	$messageStatus = "There are $messageTotal messages.";
			        	$messageIcon = "commenting-o";
			        	$messageClass = "";
			        }

			        foreach($messages as $message){
			        	$myUnseenMessages += $message->recipients->where('user_id',AUTH::user()->id)->where('seen',0)->count();
			        	$mymessages += $message->recipients->where('user_id',AUTH::user()->id)->count();
			        }

			        if($mymessages > 0){
			        	$messageStatus = 'You have '.$mymessages.' message out of the '.$messageTotal.' total for this parcel.';
			        	$messageIcon = "commenting";
			        	$messageClass = "green-text";
			        }

			         if($mymessages > 1){
			        	$messageStatus = 'You have '.$mymessages.' messages out of the '.$messageTotal.' total for this parcel.';
			        	$messageIcon = "commenting";
			        	$messageClass = "green-text";
			        }

			        if($myUnseenMessages > 0) {
			        	$messageStatus = 'You have '.$myUnseenMessages.' unread message out of the '.$messageTotal.' total for this parcel.';
			        	$messageIcon = "commenting";
			        	$messageClass = "green-text attention";
			        }
			        if($myUnseenMessages > 1) {
			        	$messageStatus = 'You have '.$myUnseenMessages.' unread messages out of the '.$messageTotal.' total for this parcel.';
			        	$messageIcon = "commenting";
			        	$messageClass = "green-text attention";
			        }
			        // process retainages
			        $retainageIcon = "registered";
			        $retainageClass = "gray-text";
			        $retainageStatus = "No Retainages";
			        $retainageTotal = 0;
			        if($parcel->retainages){
			       		$retainageTotal = $parcel->retainages->count(); // number of retainages
			       	}

			        if($retainageTotal > 0){
			        	$retainageClass = "";
			        	$retainageStatus = "All Retainage Paid";

			        	if($parcel->unpaidRetainages->count() > 0){
			        		$retainageClass = "green-text attention";
			        		$retainageStatus = "Unpaid Retainage";
			        	}
			        }
		          @endphp



		         <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}','1',0,0)" class="uk-link-muted {{$retainageClass}}" uk-tooltip="{{ $retainageStatus }}"><span class="a-file-tool {{$retainageClass}}"></span></a>
		          |
		         <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}?subTab=3','1',0,'communications')" class="uk-link-muted {{$messageClass}}" uk-tooltip="{{ $messageStatus }}"><span class="a-comment-ellipsis {{$messageClass}}"></span></a>
		          |
		         <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}?subTab=2','1',0,'documents')" class="uk-link-muted {{$documentClass}}" uk-tooltip="{{ $reference['flag'] }}"><span class="a-files-layout {{$documentClass}}"></span></a>
		         |
		        @if(count($parcel->dispositions)>0)

		        <?php // determine the flash and color
		        	$flashing = "";
		        	$danger = "";
		        	$pass = "";
		        	if(AUTH::user()->entity_type == "hfa" && !AUTH::user()->isHFAFiscalAgent()){
			        	foreach ($parcel->dispositions as $disposition) {
			        		if($disposition->status_id == 3){
			        			$flashing = "attention"; // flash that it needs our attention as needing approval
			        		} else
			        		if($disposition->status_id == 5){
			        			$danger = "red-text";
			        		} else
			        		if($disposition->status_id == 6){
			        			$pass = "green-text";
			        		} else
			        		if($disposition->status_id == 7 && $pass == ""){
			        			$pass = "blue-text"; // only set this to blue if it hasn't be set already
			        		}
			        	}
			        } else if(AUTH::user()->entity_type == "hfa" && AUTH::user()->isHFAFiscalAgent()){
			        	foreach ($parcel->dispositions as $disposition) {
			        		if($disposition->status_id == 8){
			        			$flashing = "attention";
			        		}
			        	}
			        } else if(AUTH::user()->entity_type != "hfa") {
			        	foreach ($parcel->dispositions as $disposition) {
			        		if($disposition->status_id == 2 || $disposition->status_id == 4 ){
			        			$flashing = "attention"; // flash that it needs our attention as needing approval and or payment
			        		} else
			        		if($disposition->status_id == 5){
			        			$danger = "red-text";
			        		} else
			        		if($disposition->status_id == 6){
			        			$pass = "green-text";
			        		} else
			        		if($disposition->status_id == 4 && $pass == ""){
			        			$pass = "blue-text"; // only set this to blue if it hasn't be set already
			        		}
			        	}

			        }

			        ?>
			        	<div style="display:inline-block"><span class="a-tag {{$flashing}} {{$pass}} {{$danger}}"></span><div class="countFlag">{{ $parcel->dispositions->count() }}</div></div>
			        	<div uk-dropdown="pos: bottom-right; mode: hover;">
		          			<ul class="uk-subnav" style="display: inline-block; position: relative;top: 5px;">
                                <li>
                                	@forEach($parcel->dispositions as $disposition)
							         <li>
							         	<a href="/dispositions/{{ $parcel->id }}/{{$disposition->id}}" target="_blank" class="uk-link-muted" uk-tooltip="">{{ $disposition->id }} @if($disposition->status): {{ $disposition->status->invoice_status_name }} @endif</a>
							         </li>

									@endForEach
                                </li>
                            </ul>
                        </div>
				 @else
					 <a onClick="loadDetailTab('/parcel/','{{ $parcel->id }}','1',0,0)" class="uk-link-muted" uk-tooltip="No Disposition"><span class="a-tag gray-text" ></span><div class="countFlag gray-flag">0</div></a>
				 @endif
		          @if(Auth::user()->canDeleteParcels()) | <a class="uk-link-muted" onclick="deleteParcel('{{$parcel->id}}');" uk-tooltip="Click here to delete this parcel and ALL its associated items."><span class="a-trash-4"></span></a>@endif

		          </td>


	         </tr>



	    @endforeach

    <!-- PARCEL PAGINATION LINKS??? -->
    {{ $parcels->links() }}
    @endIf

    @if(Request::query('page')<2)

		</tbody>
		</table>


		</div>
	</div>
</div>
<?php /* @if($totalParcels == 0 && $statusFiltered == NULL && $hfaStatusFiltered == NULL && Auth::user()->entity_type == "hfa" && $parcelsProgramFilterOperator == "LIKE" && session('selectParcelsFilter') != "Seen")
<script>
UIkit.modal.alert('<h1>Yikes!</h1><p>Wow there are a lot of parcels now! To keep the system running fast, please select at least one filter.</p><p>Don\'t worry, I\'ll only tell you this once (each time you login).');
<?php session(['selectParcelsFilter' => 'Seen']); ?>
</script>
@endIf
*/ ?>
<div id="results-pagination">
<a name="bottom"></a>
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> @if(session('parcels_sort_by')=="parcels.parcel_id" || session('parcels_sort_by')=="parcels.street_address") <a onclick="$('.hideMe').slideUp();" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right"> Hide Non Duplicate Parcels </a> @endIf

</div>

<script>
	$('#parcelFilters').slideToggle();$('#filter-up').toggle();$('#filter-down').toggle();
	// show filters if no filter is applied and they are an hfa

	@if(Auth::user()->id < 3)
	function exportPaidParcels(){
		event.preventDefault();

		$.post('/system_messages/exportPaidParcels', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!='1'){ 
				UIkit.modal.alert(data);
			} else {
				UIkit.modal.alert('You got it! I will send you a message once it is ready. It may take a few minutes!');
			}
		} );
	}
	@endif
</script>



@if(Auth::user()->canDeleteParcels())
<script>
function deleteParcel(parcel_id){
	UIkit.modal.confirm("<p>Are you sure you want to delete this parcel and EVERYTHING associated with it?</p><p> This includes: <ul><li>Supporting Documents</li><li>Communications</li><li>Notes</li><li>Compliances</li><li>Cost,Request,Approved/PO,Invoice Items</li><li>Dispositions</li><li>Recaptures</li><li>Retainages</li><li>Site Visits</li></ul></p><p>If this parcel was a part of an import it will clear itself and any validations from that import. If the parcel was the only parcel on a request (po and invoice) it and its associated approvals will also be deleted.</p> <p><strong>Any transactions in accounting WILL NOT BE DELETED. Those will need to be manually reconciled/deleted.</strong></p>").then(function() {
        $.get('/parcels/delete/'+parcel_id, function(data) {
			if(data['message']!='' && data['error']!=1){
				$('#parcel-'+parcel_id).removeClass('keepMe');
				$('#parcel-'+parcel_id).addClass('hideMe');
				$('#parcel-'+parcel_id).addClass('deletedParcel');

				UIkit.modal.alert(data['message']);
			}else if(data['message']!='' && data['error']==1){
				UIkit.modal.alert(data['message']);

			}else{
				UIkit.modal.alert('Something went wrong. Please contact Brian at Greenwood 360 and let him know that the parcel deletion failed and which parcel on which it failed. brian@greenwood360.com');
			}
		} );

    });
}

</script>
@endIf
@endif
