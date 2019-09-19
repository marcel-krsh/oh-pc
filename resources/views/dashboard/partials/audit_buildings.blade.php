
		<td colspan="10">

			<!-- <div class="rowinset-top">PROJECT LEVEL INSPECTION AREAS AND BUILDINGS <span class="uk-link" style="color:#ffffff;" onclick="$('#audit-r-{{ $target }}-buildings').remove();$('tr[id^=\'audit-r-\']').show();"><i class="a-circle-cross"></i></span></div> -->
			<div class="buildings uk-overflow-auto" style="">
				<div class="" >
					@foreach($buildings as $key => $building)
					@if($building->building || $building->amenity_inspection_id)
					@php
							$auditor_exists = true;
							// $building_auditors = $type->auditors($audit->audit_id);
					if(!is_null($building->building_id)) {
						$building_auditors = $amenities->where('building_id', '=', $building->building_id)->where('auditor_id', '<>', null);
						if(count($building_auditors)) {
							$b_units = $building_auditors->pluck('building')->first();
							$unit_ids = $b_units->units->pluck('id');
							$unit_auditors = $amenities->whereIn('unit_id', $unit_ids)->where('auditor_id', '<>', null);
							$combined_auditors = $building_auditors->merge($unit_auditors);
							$building_auditors = $combined_auditors->pluck('user')->unique();
						}
					} else {
						$check_auditor = $amenities->where('id', $building->amenity_inspection_id)->first();
						if($check_auditor) {
							$auditor_exists = is_null($check_auditor->auditor_id) ? false : true;
						} else {
							$auditor_exists = false;
						}
						$building_auditors = $amenities->where('auditor_id', '<>', null)->where('building_id', null)->where('amenity_id', $building->amenity_id)->pluck('user')->unique();
						//dd($building_auditors);
					}
					$b_findings_total = $building->findingstotal();
					if(!$building->building_id) {
						$b_amenity = $building->amenity();

					} else {

						$b_amenity = null;
					}
					$b_amenity_findings = $building->amenities_and_findings();
					@endphp
					<div id="building-{{ $context }}-r-{{ $key }}" class="uk-margin-remove building @if($building->building) building-{{ $building->status }} {{ $building->status }} @endif @if($building->status != 'critical') notcritical @endif uk-grid-match" style=" @if(session('audit-hidenoncritical') == 1 && $building->status != 'critical') display:none; @endif " data-audit="{{ $building->audit_id }}" data-project="{{ $building->project_id }}" data-building="{{ $building->building_id }}" data-amenity="{{ $building->amenity_id }}" data-amenityinspection="{{ $building->amenity_inspection_id }}" uk-grid>

						@include('dashboard.partials.building_row', [$building = $building,$building_auditors = $building_auditors ,$b_findings_total = $b_findings_total, $b_amenity = $b_amenity , $b_amenity_findings = $b_amenity_findings])









					@else
					<div style="display: none;"><hr class="dashed-hr uk-width-1-1"> <h3>!!! It appears the ordering data has extra records.</h3><a class="uk-button uk-button-small" onclick="$('#building-cache-{{ $building->id }}').slideToggle();"> View record data:</a><div class="uk-width-1-1" id="building-cache-{{ $building->id }}" style="display: none;"><small><pre>{{ print_r($building) }}</pre></small></div><p>Please contact Holly at hswisher@ohiohome.org .</p>
					<hr class="dashed-hr uk-width-1-1">

				</div>
				@endIf
				@endforeach
			</div>
		</div>
		<div class="rowinset-bottom" style="    padding: 10px 0;">
			<span class="uk-link" onclick="addAmenity('{{ $audit }}', 'project');">+ ADD INSPECTABLE AREA TO PROJECT</span>
		</div>
		<script>

		</script>
	</td>