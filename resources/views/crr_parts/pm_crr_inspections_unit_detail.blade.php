<?php

$siteVisited = [];
$fileVisited = [];
$nameOutput = [];
$loops = 0;

$currentUnit = 0;
//dd($allUnitInspections->where('is_file_audit',1)->groupBy('unit_id'));
?>

@forEach($inspections as $i)
	<?php $noShow = 0;?>
	@if($currentUnit != $i->unit_id)
		<div id="unit-inspection-{{ $i->unit_id }}"  class="inspection-data-row">

			@if(!in_array($i->unit_id, $nameOutput))
				<?php
$currentUnit = $i->unit_id;
//$thisUnitValues = collect($inspections)->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
$thisUnitValues = $allUnitInspections->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');

//dd($inspections->where('unit_id', $i->unit_id)->where('is_file_audit','1')->count());
$thisUnitFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('cancelled_at', NULL));
//$thisUnitUnresolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
//$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
$thisUnitResolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('auditor_approved_resolution', 1)->where('cancelled_at', NULL));
$thisUnitUnresolvedFileFindings = $thisUnitFileFindings - $thisUnitResolvedFileFindings;
$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('cancelled_at', NULL));
//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
$thisUnitResolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1)->where('cancelled_at', NULL));
$thisUnitUnresolvedSiteFindings = $thisUnitSiteFindings - $thisUnitResolvedSiteFindings;
$isHome = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'HOME')->where('cancelled_at', NULL));
$isOhtf = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'OHTF')->where('cancelled_at', NULL));
$isNhtf = count(collect($allUnitInspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'NHTF')->where('cancelled_at', NULL));

//if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
//	$hasFindings = 1;
//}

?>
				<div  class="unit-name"  >

					<strong>Unit {{ $i->unit_name }} <?php $nameOutput[] = $i->unit_id;?></strong>  <small>IN</small> {{ $i->building->building_name }} :

					@if($i->unit->bedroomCount())
					<br />Bedrooms: <strong>{{ $i->unit->bedroomCount() }}</strong>
					@else
					<br />Bedrooms: <strong>0</strong>
					@endIf
					@if(!is_null($i->unit->building->address))
					<br /><small style="text-transform: uppercase;">{{ $i->unit->building->address->line_1 }} {{ $i->unit->building->address->line_2 }} <br />
						{{ $i->unit->building->address->city }}, {{ $i->unit->building->address->state }} {{ $i->unit->building->address->zip }}</small>
					@endIf



				</div>

			@endIf
			<div class="inspection-icons">
				@forEach($thisUnitValues as $g)
					<?php //dd($thisUnitValues, $g); ?>
				@if($g->is_site_visit == 1 && $canViewSiteInspections)
				@if(!in_array($g->unit_id, $siteVisited))


				<i class="a-mobile uk-text-large uk-margin-small-right" ></i> @if($thisUnitSiteFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-phone @if($thisUnitUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitSiteFindings }} FINDINGS @if($thisUnitUnresolvedSiteFindings > 0) WITH {{ $thisUnitUnresolvedSiteFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitSiteFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-phone no-findings"></i> @else <span class="uk-badge finding-number on-phone ">NA</span> @endIf 
				<?php $siteVisited[] = $g->unit_id;?>
				@else
				<?php $noShow = 1;?>
				@endIf

				@elseIf(!in_array($g->unit_id, $fileVisited) && $canViewFileInspections)
					@if(!in_array($g->unit_id, $siteVisited))
						@if($canViewSiteInspections)<span style="color:#cecece"><i class="a-mobile uk-text-large uk-margin-small-right "  ></i> @if($thisUnitSiteFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-phone" uk-tooltip title="{{ $thisUnitSiteFindings }}">{{ $thisUnitSiteFindings }}</span> @elseif($canViewFindings)<i class="a-circle-minus on-phone"></i> @else <span class="uk-badge finding-number on-phone">NA</span>@endIf</span>
						@endIf
					@endIf
				<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, 'file',null,'0');" @endif @endif></i> @if($thisUnitFileFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-folder @if($thisUnitUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitFileFindings }} FINDINGS @if($thisUnitUnresolvedFileFindings > 0) WITH {{ $thisUnitUnresolvedFileFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitFileFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-folder no-findings"></i> @else <span class="uk-badge finding-number on-folder">NA</span> @endIf
				@if($isHome || $isOhtf || $isNhtf) <i class="a-home-2 home-folder"></i> @endIf
				<?php $fileVisited[] = $g->unit_id; ?>
				
				@else
				<?php $noShow = 1;?>
				@endIf

				@endForEach
				@if(!in_array($g->unit_id, $fileVisited) && $canViewFileInspections)
				<span style="color:#cecece"><i class="a-folder uk-text-large"></i> <i class="a-circle-minus on-folder"></i></span>

				@endIf
				@if($canViewFileInspections)
					<?php

					if($canViewFindings){
						$g_documents = $g->all_documents();

					}else{
						$g_documents = $g->all_documents();
						$g_documents = $g_documents->filter(function ($doc){
							
							if (count($doc->findings) < 1) {
								return $doc;
								
							}
						});

					}
					$unresolvedFlash = 0;

					$toolTip = "";

					if(count($g_documents)){
						forEach($g_documents as $gc){
							foreach ($gc->assigned_categories as $document_category)
							$toolTip .= strtoupper($gc->user->full_name())."'S ";
							$toolTip .= strtoupper($document_category->parent->document_category_name)." : ".strtoupper($document_category->document_category_name);
							if($gc->findings){
								$toolTip .= ' Findings: ';
								forEach($gc->findings as $gcFinding){
									$toolTip .= '#'.$gcFinding->id.' ';
								}
							}
							$toolTip .= ($gc->notapproved == 1) ? " (DECLINED)" : "";
							$toolTip .= ($gc->approved == 1) ? " (APPROVED)" : "";
							$toolTip .= ($gc->approved != 1 && $gc->notapproved != 1) ? "(PENDING)" : "";
							$toolTip .= '<hr />';
						}
						
					}

					?>
					<span onclick="openDocuments('{{ $i->unit_name }}')" class="use-hand-cursor a-file"></span><span class="use-hand-cursor uk-badge finding-number on-file-pd" onclick="openDocuments('{{ $i->unit_name }}')" @if($toolTip != "") uk-tooltip title="{{$toolTip}}" @endIf>
					@if(count($g_documents))
						 {{count($g_documents)}}
					@else
						0
					@endIf
					</span><br />
						<span class="use-hand-cursor" onclick="gotoDocumentUploader('',{{ $g->unit_id }},{{$report->audit->id}},'')"><span class="a-file-plus"></span><small> FILE DOCUMENT</small></span>
						@if($thisUnitUnresolvedFileFindings || $thisUnitUnresolvedSiteFindings )
						<br /><span class="use-hand-cursor" onclick="gotoDocumentUploader('',{{ $g->unit_id }},{{$report->audit->id}},1)"><span class="a-file-plus"></span><small> FINDING RESOLUTION</small></span>
						@endIf
					
				@endIf

			</div>
			<hr class="dashed-hr uk-margin-small-bottom">
		</div>
	@endIf
@endForEach