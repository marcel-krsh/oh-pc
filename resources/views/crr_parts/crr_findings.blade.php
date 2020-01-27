<?php $findings = $bladeData;
session(['selected_findings' => $findings]);
?>
@if(!is_null($findings))

@php
		// count them up...
$fileCount = 0;
$nltCount = 0;
$ltCount = 0;
$group_building = 0;
$group_unit = 0;
forEach($findings as $fc){

	switch ($fc->finding_type->type) {
		case 'file':
		$fileCount++;
		break;
		case 'nlt':
		$nltCount++;
		break;
		case 'lt':
		$ltCount++;
		break;
		default:
					# code...
		break;
	}
}


$columnCount = 1;
$findings = collect($findings)->sortByDesc('site');
$current_audit = $report->audit_id;
$site_findings = $findings->where('unit_id', null)->where('building_id', null);//->where('site', 1)
$building_findings = $findings->where('building_id', '<>', null);
$unit_findings = $findings->where('unit_id', '<>', null);

$site_findings->map(function ($s) use($current_audit) {
	$existing_records = App\Models\AmenityInspection::where('audit_id', $current_audit)->where('amenity_id', $s->amenity_id)->pluck('id')->toArray();
	$index = 0;
	$index = array_search($s->amenity_inspection_id, $existing_records);
	if($index == 0 && count($existing_records) <= 1) {
		$index = '';
	} else {
		$index = $index + 1;
	}
	if($index == 0) {
		$index = '';
	}
	$s->amenity_index = $index;
	return $s;
});
$unit_findings->map(function ($o) use($current_audit) {
	$o->u_building_key = $o->unit->building_key;
	$existing_records = App\Models\AmenityInspection::where('audit_id', $current_audit)->where('unit_id', $o->unit_id)->where('amenity_id', $o->amenity_id)->pluck('id')->toArray();
	$index = 0;
	$index = array_search($o->amenity_inspection_id, $existing_records);
	if($index == 0 && count($existing_records) <= 1) {
		$index = '';
	} else {
		$index = $index + 1;
	}
	if($index == 0) {
		$index = '';
	}
	$o->amenity_index = $index;
	return $o;
});
$building_findings->map(function ($b) use($current_audit) {
	$b->u_building_key = $b->building->building_key;
	$existing_records = App\Models\AmenityInspection::where('audit_id', $current_audit)->where('building_id', $b->building_id)->where('amenity_id', $b->amenity_id)->pluck('id')->toArray();
	$index = 0;
	$index = array_search($b->amenity_inspection_id, $existing_records);
	if($index == 0 && count($existing_records) <= 1) {
		$index = '';
	} else {
		$index = $index + 1;
	}
	if($index == 0) {
		$index = '';
	}
	$b->amenity_index = $index;
	return $b;
});

$grouped_bf = $building_findings->sortBy('building.building_name')->groupBy('u_building_key')->toArray();
$grouped_uf = $unit_findings->sortBy('unit.unit_name')->groupBy('u_building_key')->toArray();
foreach($grouped_bf as $bk => $bf) {
	if(array_key_exists($bk, $grouped_uf)) {
		$grouped_bf[$bk]['uf'] = $grouped_uf[$bk];
		unset($grouped_uf[$bk]);
	}
}
//dd($unit_findings->where('id', 2479));

@endphp

<div uk-grid>
	<div class="uk-width-1-1">
		<a name="findings-list"></a><h2 id="findings-list">Findings &amp; Notes: </h2> <small>
			@if($fileCount > 0)
			<i class="a-folder"></i> : {{$fileCount}} FILE
			@if($fileCount != 1)
			FINDINGS
			@else
			FINDING
			@endIf
			&nbsp;|  &nbsp;
			@endIf
			@if($nltCount > 0)
			<i class="a-booboo"></i> : {{$nltCount}}  NON LIFE THREATENING
			@if($nltCount != 1)
			FINDINGS
			@else
			FINDING
			@endIf
			&nbsp;|  &nbsp;
			@endIf
			@if($ltCount > 0)
			<i class="a-skull"></i> : {{$ltCount}} LIFE THREATENING
			@if($ltCount != 1)
			FINDINGS
			@else
			FINDING
			@endIf
		@endIf <br /><div class="uk-badge uk-text-right@s badge-filter show-all-findings-button" style="display: none;">
                <a onclick="showOnlyFindingsFor('finding-group');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>REMOVE FINDINGS FILTER</span></a>

            	</div>

        </small><hr class="dashed-hr">
	</div>
	{{-- Here was the foreach findings loop --}}


{{-- Site findings --}}
	@if($site_findings)
	@php
	$findings = $site_findings;
	@endphp
	@include('crr_parts.crr_findings_groups', ['site_finding' => 1])
	@endif

{{-- Building findings --}}
	@if($grouped_bf)
		@foreach($grouped_bf as $bf)
		@php
			if(array_key_exists('uf', $bf)) {
				$ufs = $bf['uf'];
				unset($bf['uf']);
			} else {
				$ufs = null;
			}
			$findings = $bf;
			$group_building = 1;
			$group_unit = 0;
		@endphp
		{{-- <strong>{{ $bf[0]->building->building_name }}</strong> <br /> --}}
		@include('crr_parts.crr_findings_groups', ['group_building', $group_building])
		@php
		if(!is_null($ufs)) {
			$findings = $ufs;
			$group_building = 0;
			$group_unit = 1;
		}
		@endphp
		@if(!is_null($ufs))
			@include('crr_parts.crr_findings_groups', ['group_unit', $group_unit])
		@endif
		@endforeach
	@endif

{{-- Unit findings --}}
@if($grouped_uf)
	@foreach($grouped_uf as $uf)
	@php
		$findings = $uf;
		$compiledFlatPikers = "";
	@endphp
		@include('crr_parts.crr_findings_groups')
	@endforeach
@endif


</div>

<script>

	@if($auditor_access)
	// Flatpickers in use for findings
	 @stack('flatPickers')
	// End Flatpickers
	function resolveFinding(findingid, dateResolved){
		var resolveFindingId = findingid;
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}',
			'date' : dateResolved
		}, function(data) {
			if(data != 0){
				console.log('Resolution saved for finding '+resolveFindingId);
				$('#inspec-tools-finding-resolve-'+resolveFindingId).html('<button class="uk-button uk-link uk-margin-small-left uk-width-1-1" onclick="resolveFinding(\''+resolveFindingId+'\');"><i class="a-circle-cross"></i>&nbsp; DATE</button>');
				$('#resolved-date-finding-'+resolveFindingId).val(data);
				//<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" onclick="resolveFinding(\''+resolveFindingId+'\',\'null\')"><span class="a-circle-cross">&nbsp;</span>CLEAR</button>
			}else{
				console.log('Resolution cleared for finding '+resolveFindingId);
				$('#inspec-tools-finding-resolve-'+resolveFindingId).html('<span style="position: relative; top: 9px;">RESOLVED AT:</span>');
				$('#resolved-date-finding-'+resolveFindingId).val('');
			}
			if(window.resolveDateChangeAlert !== 1){
				UIkit.modal.alert('<h1>Don\'t Forget!</h1><p>You will need to refresh the report\'s content for these changes to appear on the report.</p><p>Just in case you forget - I am making the refresh icon pulse to remind you.');
				$('.refresh-content-button').addClass('attention');
				$('.refresh-content-button').css('color','red');
				window.resolveDateChangeAlert = 1;
			}
		});
	}
	function cancelFinding(findingid){
		UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>Cancel Finding #'+findingid+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to cancel this finding? All its comments/photos/documents/followups will remain and the cancelled finding will be displayed at the bottom of the list.</h3><h3>NOTE: Cancelled findings will not be displayed on a report. If you have cancelled a finding that was being displayed on a report, you will need to refresh that reports content for the change to be reflected.</h3></div>', {stack:1}).then(function() {

			$.post('/findings/'+findingid+'/cancel', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data==0){
					UIkit.modal.alert(data,{stack: true});
				} else {
					UIkit.notification('<span uk-icon="icon: check"></span> Finding Canceled', {pos:'top-right', timeout:1000, status:'success'});
					$('#finding-modal-audit-stream-refresh').trigger('click');
					$('#cancelled-finding-'+findingid).css("text-decoration","line-through");
				}
			} );


		}, function () {
			console.log('Rejected.')
		});
	}
	@endif
	// flatpickr(".flatpickr", {
	// 							altFormat: "F j, Y ",
	// 							dateFormat: "F j, Y",
	// 							enableTime: true,
	// 							"locale": {
	// 					        "firstDayOfWeek": 1 // start week on Monday
	// 					      }
	// 					    });
</script>

@else
<hr class="dashed-hr">
<h3>NO FINDINGS</h3>
@endif
