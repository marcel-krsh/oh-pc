@if(null == $projectDetails)
	@if(session('projectDetailsOutput') == 0)
	<div id="project-details-stats" class="uk-width-1-1 uk-grid-margin uk-first-column" style="margin-top:20px;">
		<div uk-grid="" class="uk-grid">
			<div class="uk-width-1-1">
				<h2>Project Details: </h2>
			</div>
			<div class="uk-width-2-3 uk-first-column">
				<ul class="leaders" style="margin-right:30px;">
					<li><span>Total Buildings</span> <span>{{ $projectDetails->total_building }}</span></li>
					<li><span>Total Units</span> <span>{{ $projectDetails->total_units }}</span></li>
					<li><span class="indented">• Market Rate Units</span> <span>{{ $projectDetails->market_rate }}</span></li>
					<li><span class="indented">• Program Units</span> <span>{{ $projectDetails->subsidized }}</span></li>
					<?php $pdPrograms = json_decode($projectDetails->programs);?>
					<li><span>Programs</span> <span>{{ count($pdPrograms) }}</span></li>
					<?php $pdpLoop = 1;?>
					@forEach($pdPrograms as $pdp)
					<li><span class="indented">• [{{ $pdpLoop }}] {{ $pdp->name }}</span> <span>{{ $pdp->units }}</span></li>
					<?php $programReference[$pdp->program_id] = $pdpLoop;
					$pdpLoop++;?>
					@endForEach
				</ul>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>OWNER: </strong></h5>
				<div class="address" style="margin-bottom:20px;">
					<i class="a-bank" style="font-weight: bolder;"></i> @if($projectDetails->owner_name != '') {{ $projectDetails->owner_name }} @else NA @endIf<br>
					<i class="a-avatar"></i> POC: @if($projectDetails->owner_poc != ''){{ $projectDetails->owner_poc }}@else NA @endIf<br>
					<i class="a-phone-5"></i>  @if($projectDetails->owner_phone != ''){{ $projectDetails->owner_phone }}@else NA @endIf<br>
					<i class="a-fax-2"></i>  @if($projectDetails->owner_fax != ''){{ $projectDetails->owner_fax }}@else NA @endIf<br>
					<i class="a-mail-send"></i> @if($projectDetails->owner_email != '')<a class="uk-link-mute" href="mailto:{{ $projectDetails->owner_email }}">{{ $projectDetails->owner_email }}</a>@else NA @endIf<br>
				</div>
				<h5 class="uk-margin-remove"><strong>Managed By: </strong></h5>
				<div class="address">
					<i class="a-bank" style="font-weight: bolder;"></i> @if($projectDetails->manager_name != '') {{ $projectDetails->manager_name }} @else NA @endIf<br>
					<i class="a-avatar"></i> POC: @if($projectDetails->manager_poc != ''){{ $projectDetails->manager_poc }}@else NA @endIf<br>
					<i class="a-phone-5"></i>  @if($projectDetails->manager_phone != ''){{ $projectDetails->manager_phone }}@else NA @endIf<br>
					<i class="a-fax-2"></i>  @if($projectDetails->manager_fax != ''){{ $projectDetails->manager_fax }}@else NA @endIf<br>
					<i class="a-mail-send"></i> @if($projectDetails->manager_email != '')<a class="uk-link-mute" href="mailto:{{ $projectDetails->manager_email }}">{{ $projectDetails->manager_email }}</a>@else NA @endIf<br>
				</div>
			</div>
		</div>
	</div>
	<hr class="dashed-hr uk-margin-bottom">
	<?php session(['projectDetailsOutput' => 1])?>
	@endIf
@endIf
