<?php 

	$siteVisited = [];
	$fileVisited = [];
	$nameOutput = [];
	$loops = 0;

	$currentUnit = 0;

?>
@forEach($inspections as $i)
	<?php $noShow = 0;?>
	@if($currentUnit != $i->unit_id)
		<div id="unit-inspection-{{ $i->unit_id }}"  class="inspection-data-row">

			@if(!in_array($i->unit_id, $nameOutput))
				<?php
				$currentUnit = $i->unit_id;
				// $thisUnitValues = collect($inspections)->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
				$thisUnitValues = $allUnitInspections->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
				$thisUnitFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file'));
				//$thisUnitUnresolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
				//$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
				//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
				$thisUnitResolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('auditor_approved_resolution', 1));
				$thisUnitUnresolvedFileFindings = $thisUnitFileFindings - $thisUnitResolvedFileFindings;
				$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
				//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
				$thisUnitResolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1));
				$thisUnitUnresolvedSiteFindings = $thisUnitSiteFindings - $thisUnitResolvedSiteFindings;
				$isHome = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'HOME')->where('cancelled_at',NULL));
				$isOhtf = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'OHTF')->where('cancelled_at',NULL));
				$isNhtf = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'NHTF')->where('cancelled_at',NULL));

				//if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
				//	$hasFindings = 1;
				//}

				?>
				<div  class="unit-name"  >
					@if($print !== 1 && !$dpView)<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('unit-{{ $i->unit_id }}-finding');"> @elseIf($dpView && $i->unit->household)<span onclick="dynamicModalLoad('household/{{ $i->unit_id }}')" class="use-hand-cursor" uk-tooltip title="VIEW HOUSEHOLD DETAILS"><i class="a-avatar-info"></i>
						@endIf {{ $i->building->building_name }} : {{ $i->unit_name }}<?php $nameOutput[] = $i->unit_id;?> :
					@if($print !== 1 && !$dpView)</a> @elseIf($dpView && $i->unit->household)</span>@endIf
					@if($dpView)
					@if($i->unit->bedroomCount())
					<br />Bedrooms: <strong>{{ $i->unit->bedroomCount() }}</strong>
					@endIf
					@if(!is_null($i->unit->building->address))
					<br /><small style="text-transform: uppercase;">{{ $i->unit->building->address->line_1 }} {{ $i->unit->building->address->line_2 }} <br />
						{{ $i->unit->building->address->city }}, {{ $i->unit->building->address->state }} {{ $i->unit->building->address->zip }}</small>
					@endIf


					@endIf
				</div>

			@endIf
			<div class="inspection-icons">
				@forEach($thisUnitValues as $g)
				<?php //dd($thisUnitValues, $g); ?>
				@if($g->is_site_visit == 1)
				@if(!in_array($g->unit_id, $siteVisited))


				<i class="a-mobile uk-text-large uk-margin-small-right @if($auditor_access) @if(!$print)use-hand-cursor @endif @endif" @if($auditor_access) @if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif @endif></i> @if($thisUnitSiteFindings > 0) <span class="uk-badge finding-number on-phone @if($thisUnitUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitSiteFindings }} FINDINGS @if($thisUnitUnresolvedSiteFindings > 0) WITH {{ $thisUnitUnresolvedSiteFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitSiteFindings }}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endIf <?php $siteVisited[] = $g->unit_id;?>
				@else
				<?php $noShow = 1;?>
				@endIf

				@elseIf(!in_array($g->unit_id, $fileVisited))
				@if(!in_array($g->unit_id, $siteVisited))
				<span style="color:#cecece"><i class="a-mobile uk-text-large uk-margin-small-right "  ></i> @if($thisUnitSiteFindings > 0) <span class="uk-badge finding-number on-phone" uk-tooltip title="{{ $thisUnitSiteFindings }}">{{ $thisUnitSiteFindings }}</span> @else<i class="a-circle-minus on-phone"></i> @endIf</span>
				@endIf
				<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, 'file',null,'0');" @endif @endif></i> @if($thisUnitFileFindings > 0) <span class="uk-badge finding-number on-folder @if($thisUnitUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitFileFindings }} FINDINGS @if($thisUnitUnresolvedFileFindings > 0) WITH {{ $thisUnitUnresolvedFileFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitFileFindings }}</span> @else<i class="a-circle-checked on-folder no-findings"></i>@endIf
				@if($isHome || $isOhtf || $isNhtf) <i class="a-home-2 home-folder"></i> @endIf
				<?php $fileVisited[] = $g->unit_id;?>

				@else
				<?php $noShow = 1;?>
				@endIf

				@endForEach
				@if(!in_array($g->unit_id, $fileVisited))
				<span style="color:#cecece"><i class="a-folder uk-text-large"></i> <i class="a-circle-minus on-folder"></i></span>

				@endIf

			</div>
			<hr class="dashed-hr uk-margin-small-bottom">
		</div>
	@endIf
@endForEach