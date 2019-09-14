<div class="uk-margin-large-top" uk-grid>
<div id="project-details-selections" class="uk-width-1-1">
		<?php
			$siteInspections = $audit->audit->project_amenity_inspections;
			$buildingInspections = $audit->audit->building_inspections;
			$unitInspections = $audit->audit->unit_inspections;
			$pdtDetails = $details;
			$pdtFindings = $audit->audit->findings;
			$pieceData = [];
			$print = null;
			$report = $audit;
			$selected_audit = $audit;
			?>
		@if(count($siteInspections))
			<?php
			  $bladeData = $siteInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'site',$detailsPage = 1])
		@endif
		@if(count($buildingInspections))
			<?php
			  $bladeData = $buildingInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'building',$detailsPage = 1])
		@endif
		@if(count($unitInspections))
			<?php
				//dd($unitInspections);
			  $bladeData = $unitInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'unit',$detailsPage = 1])
		@endif
</div>
</div>