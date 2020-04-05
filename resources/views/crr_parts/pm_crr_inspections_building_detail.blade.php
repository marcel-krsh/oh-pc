@forEach($inspections as $i)

	<?php
	$currentBuilding = $i->building_id;
	$findingCount = 'findings' . $currentBuilding;
	if($canViewSiteInspections){
		$thisBuildingValues = collect($inspections)->where('building_id', $i->building_id)->sortByDesc('is_site_visit');

	}else{
		$thisBuildingValues = collect([]);
	}
	if ($dpView) {
		$thisBuildingUnfinishedInspections = collect($inspections)->where('building_id', $i->building_id)->where('complete', 0)->sortByDesc('is_site_visit');
		// $building_auditors = $type->auditors($audit->audit_id);
		$building_auditors = $selected_audit->audit->amenity_inspections->where('building_id', '=', $i->building_id)->where('auditor_id', '<>', null);
		//dd($inspections,$building_auditors);
		if (count($building_auditors)) {
			$b_units = $building_auditors->pluck('building')->first();
			$unit_ids = $b_units->units->pluck('id');
			$unit_auditors = $selected_audit->audit->amenity_inspections->whereIn('unit_id', $unit_ids)->where('auditor_id', '<>', null);
			$combined_auditors = $building_auditors->merge($unit_auditors);
			$building_auditors = $combined_auditors->pluck('user')->unique();
		}
	}
	if($canViewFindings){
		$findingCount = collect($findings);

	}else{
		$findingCount = collect([]);
	}
	$findingCount = $findingCount->filter(function ($item) use ($currentBuilding) {
		if ($item->building && $item->building->id == $currentBuilding) {
			return $item;
		}
		if ($item->unit && $item->unit->building_id == $currentBuilding) {
			return $item;
		}
	});
	$thisBuildingsUnresolvedFindings = $findingCount->filter(function ($item) use ($currentBuilding) {
		if ($item->building && $item->building->id == $currentBuilding && $item->auditor_approved_resolution !== 1) {
			return $item;
		}
	});
	$hasFindings = 0;
	// dd($currentBuilding,$findingCount);
	if($canViewFindings){
		$thisBuildingSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file'));
		$thisBuildingResolvedSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1));
		$thisBuildingUnresolvedSiteFindings = $thisBuildingSiteFindings - $thisBuildingResolvedSiteFindings;
		$thisBuildingFileFindings = count($findingCount->where('finding_type.type', '==', 'file'));
		$thisBuildingResolvedFileFindings = count($findingCount->where('finding_type.type', '==', 'file')->where('auditor_approved_resolution', 1));
		$thisBuildingUnresolvedFileFindings = $thisBuildingFileFindings - $thisBuildingResolvedFileFindings;

	} else {
		$thisBuildingSiteFindings = 0;
		$thisBuildingResolvedSiteFindings = 0;
		$thisBuildingUnresolvedSiteFindings = 0;
		$thisBuildingFileFindings = 0;
		$thisBuildingResolvedFileFindings = 0;
		$thisBuildingUnresolvedFileFindings = 0;
	}


	if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
		$hasFindings = 1;
	}

	?>
	<div  class="inspection-data-row">
		<div  class="unit-name"  >
			
				<span  >
					
					{{ $i->building_name }}
					
			</span>
			
			@if($hasFindings && property_exists($i, 'latest_resolution') && $i->latest_resolution)
			<br /> <i class="a-checkbox-checked" uk-tooltip title ="ALL ITEMS CORRECTED"></i> {{ date('m/d/Y',strtotime($i->latest_resolution)) }}
			@elseIf(($hasFindings && property_exists($i,'latest_resolution')) || ($dpView && !count($thisBuildingUnfinishedInspections) && $hasFindings && $i->latest_resolution == null))

			@if($canViewFindings)
			<br /> <span class="attention" style="color:red; display: inline-block;margin-top: 5px;"><i class="a-multiply"></i> UNCORRECTED </span>
			@else
			 <span class="" style="display: inline-block;margin-top: 5px;"><i class="a-circle"></i> INSPECTION PENDING </span>
			@endIf
			@if($dpView)
			<br/> <small><i class="a-circle-checked"></i> INSPECTION COMPLETE</small>
			@endIf
			@elseif($dpView && count($thisBuildingUnfinishedInspections) && ($canViewFindings) || count($selected_audit->audit->findings) && $canViewFindings && count($thisBuildingUnfinishedInspections))
			<br /> <small><i class="a-circle"></i> INSPECTION IN PROGRESS</small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && !$hasFindings && $canViewFindings)
			<br /><small><i class="a-circle-checked"></i> INSPECTION COMPLETE </small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && $hasFindings && $canViewFindings)
			<br /><small ><i class="a-circle"></i> INSPECTION INCOMPLETE </small>
			@else
			<br /><small ><i class="a-circle"></i> INSPECTION PENDING</small>
			@endIf

			
		</div>
		
		<div style="float: right;">
			@if($canViewSiteInspections)
			<i class="a-mobile uk-text-large uk-margin-small-right" ></i> @if($thisBuildingSiteFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-phone @if($thisBuildingUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingSiteFindings }} @if($thisBuildingSiteFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedSiteFindings > 0) WITH {{ $thisBuildingUnresolvedSiteFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingSiteFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-phone no-findings"></i> @else <span class="uk-badge finding-number on-phone" >NA</span> @endif
			@endIf
			@if($canViewFileInspections)
			<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" ></i> @if($thisBuildingFileFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-folder @if($thisBuildingUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingFileFindings }} @if($thisBuildingFileFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedFileFindings > 0) WITH {{ $thisBuildingUnresolvedFileFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingFileFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-folder no-findings"></i> @else <span class="uk-badge finding-number on-folder">NA</span>@endIf
			@endIf

			@if($canViewFileInspections)
					<?php

					if($canViewFindings){
						$i_documents = $i->all_documents();

					}else{
						$i_documents = $i->all_documents();
						//dd($i->all_documents());
						$i_documents = $i_documents->filter(function ($doc){
							
							if (count($doc->findings) < 1) {
								return $doc;
								
							}
						});

					}
					$unresolvedFlash = 0;

					$toolTip = "";

					if(count($i_documents)){
						forEach($i_documents as $ic){
							foreach ($ic->assigned_categories as $document_category)
							$toolTip .= strtoupper($ic->user->full_name())."'S ";
							$toolTip .= strtoupper($document_category->parent->document_category_name)." : ".strtoupper($document_category->document_category_name);
							if($ic->findings){
								$toolTip .= ' Findings: ';
								forEach($ic->findings as $icFinding){
									$toolTip .= '#'.$icFinding->id.' ';
								}
							}
							$toolTip .= ($ic->notapproved == 1) ? " (DECLINED)" : "";
							$toolTip .= ($ic->approved == 1) ? " (APPROVED)" : "";
							$toolTip .= ($ic->approved != 1 && $ic->notapproved != 1) ? "(PENDING)" : "";
							$toolTip .= '<hr />';
						}
						
					}

					?>
					<span onclick="openDocuments('{{ $i->building_name }}')" class="use-hand-cursor a-file"></span><span class="use-hand-cursor uk-badge finding-number on-file-pd" onclick="openDocuments('{{ $i->building_name }}')" @if($toolTip != "") uk-tooltip title="{{$toolTip}}" @endIf>
					@if(count($i_documents))
						 {{count($i_documents)}}
					@else
						0
					@endIf
					</span><br />
						<span class="use-hand-cursor" onclick="gotoDocumentUploader({{ $i->building_id }},'',{{$report->audit->id}},'')"><span class="a-file-plus"></span><small> FILE DOCUMENT</small></span>
						@if(count($thisBuildingsUnresolvedFindings) > 0)
						<br /><span class="use-hand-cursor" onclick="gotoDocumentUploader({{ $i->building_id }},'',{{$report->audit->id}},1)"><span class="a-file-plus"></span><small> FINDING RESOLUTION</small></span>
						@endIf
					
				@endIf

		</div>
		<hr class="dashed-hr uk-margin-small-bottom">
	</div>

	@endForEach