<div class="uk-margin-large-top" uk-grid>
<div id="project-details-selections" class="uk-width-1-1">
		<?php
			$siteInspections = $audit->audit->project_amenity_inspections()->paginate(12);
			$buildingInspections = $audit->audit->building_inspections()->paginate(12);
			$allBuildingInspections = $audit->audit->building_inspections;
			$unitInspections = $audit->audit->unit_inspections()->groupBy('unit_id')->paginate(12);
			$allUnitInspections = $audit->audit->unit_inspections;
			
			$pdtDetails = $details;
			$pdtFindings = $audit->audit->findings;
			$pieceData = [];
			$print = null;
			$report = $audit;
			$selected_audit = $audit;

			$projectDetails = null;
			$findings = null;
			$dpView = null;
			if (array_key_exists(4, $pieceData)) {
				$projectDetails = $pieceData[4];
			}
			if (array_key_exists(5, $pieceData)) {
				$findings = $pieceData[5];
			}
			if (isset($pdtDetails)) {
				$projectDetails = $pdtDetails;
				$findings = $pdtFindings;
			}
			
		?>
		@include('crr_parts.crr_inspections_header') 
		
		@if(count($siteInspections))
			<?php
			  $inspections = $siteInspections;
			?>
			<div id="site" data-project-id="@if(isset($projectDetails)){{ $projectDetails->project_id }}@endIf" data-audit-id="@if(isset($selected_audit)){{ $selected_audit->audit_id }}@endIf"> 
				@include('crr_parts.pm_crr_inspections_site', [$inspections_type = 'site',$detailsPage = 1])
			</div>
		@endif
		@if(count($buildingInspections))
			<?php
			  $inspections = $buildingInspections;
			?>
			
			<div id="building" data-project-id="@if(isset($projectDetails)){{ $projectDetails->project_id }}@endIf" data-audit-id="@if(isset($selected_audit)){{ $selected_audit->audit_id }}@endIf"> 
				@include('crr_parts.pm_crr_inspections_building', [$inspections_type = 'building',$detailsPage = 1])
			</div>
					
		@endif
		@if(count($unitInspections))
			<?php
				//dd($unitInspections);
			  $inspections = $unitInspections;
			?>
			<div id="unit" data-project-id="@if(isset($projectDetails)){{ $projectDetails->project_id }}@endIf" data-audit-id="@if(isset($selected_audit)){{ $selected_audit->audit_id }}@endIf"> 
				@include('crr_parts.pm_crr_inspections_unit', [$inspections_type = 'unit',$detailsPage = 1])
			</div>
		@endif
		<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
</div>
</div>