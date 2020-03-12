<?php
if (isset($detailsPage)) {
	$dpView = 1;
}
?>
@if(!is_null($inspections))

	<div uk-grid class="uk-margin-bottom site">
		
		<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
			<?php
			// $inspections = collect($inspections);
			?>
			<div id="containerIntro" style="display: flex;">
				<h2>{{ number_format($inspections->total(), 0) }} @if(count($inspections) > 1 || count($inspections) < 1) Site Amenities @else Site Amenity @endIf @if($dpView) Selected: @else Audited: @endIf </h2>
				<div class="uk-width-1-2" style="padding-left: 10px;"> 
			    	{{ $inspections->links() }}
			    </div>
			</div>
				<small><i class="a-mobile"></i> : PHYSICAL INSPECTION </small>
			<hr class="dashed-hr uk-margin-bottom">

			<div class="uk-column-1-3 uk-column-divider">
				@forEach($inspections as $i)
				<?php
				$currentSite = $i->amenity_id;
				$thisAmenityValues = collect($inspections)->where('amenity_id', $i->amenity_id);
				$thisAmenityFindings = count(collect($findings)->where('amenity_id', $i->amenity_id));
				$thisAmenityUnresolvedFindings = count(collect($findings)->where('amenity_id', $i->amenity_id)->where('finding_type.auditor_last_approved_at', '=', null));
				?>
				<div class="inspection-data-row">
					<div  class="unit-name" >
						@if($print !== 1)<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('site-{{ $i->amenity->amenity_type_key }}-finding');">
							@endIf <strong><i class="{{ $i->amenity->icon }}"></i></strong> {{ $i->amenity->amenity_description }}
						@if($print !== 1)</a>@endIf
					</div>
				{{-- <div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, null, null, {{ $i->amenity_id }},'0');" @endif  @endcan></i>
				</div> --}}
				<div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, null, null, {{ $i->amenity_id }},'0');" @endif  @endif></i> @if($thisAmenityFindings > 0) <span class="uk-badge finding-number on-phone @if($thisAmenityUnresolvedFindings > 0) attention @endIf" uk-tooltip title="{{ $thisAmenityFindings }} @if($thisAmenityFindings > 1) FINDINGS @else FINDING @endIf @if($thisAmenityUnresolvedFindings > 0) WITH {{ $thisAmenityUnresolvedFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisAmenityFindings }}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endif


				</div>
				<hr class="dashed-hr uk-margin-small-bottom">
			</div>
			@endForEach
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-large-bottom">

@else
<hr class="dashed-hr">
<h3>NO {{ strtoupper($inspections_type) }} INSPECTIONS COMPLETED YET</h3>
<hr class="dashed-hr uk-margin-large-bottom">
@endIf