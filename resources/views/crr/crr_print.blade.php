   @extends('layouts.simplerAllita')
@section('head')
<title>PRINT: {{ $report->template()->template_name }}: #{{ $report->id }} || {{ $report->project->project_number }} : {{ $report->project->project_name }} || AUDIT: {{ $report->audit->id }}.{{ str_pad($report->version, 3, '0', STR_PAD_LEFT) }}</title>

@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
	<?php // determin background type
$background = "none";
if (1 == $report->crr_approval_type_id) {
  $background = '-draft';
}
if (2 == $report->crr_approval_type_id) {
  $background = '-pending';
}
if (3 == $report->crr_approval_type_id) {
  $background = '-declined';
}
if (4 == $report->crr_approval_type_id) {
  $background = '-revise';
}
?>
	body, div, p {
		font-size: 13pt;
	}
	h1 {
		font-size: 24pt;

	}
	h2 {
		font-size: 20pt;
	}
	h3 {
		font-size: 16pt
	}
	h4,h5 {
		font-size: 14pt;
	}
	.crr-sections {
		width:1142px; min-height: 1502px; margin-left:auto; margin-right:auto; background-image: url('/paginate-2x{{$background}}.gif'); padding: 72px;

	}

	#crr-part {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}
	#crr-sections {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}

#crr-panel .uk-panel-box-white {background-color:#ffffff;}
#crr-panel .uk-panel-box .uk-panel-badge {}
#crr-panel .green {color:#82a53d;}
#crr-panel .blue {color:#005186;}
#crr-panel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#crr-panel table tfoot tr td {border: none;}
#crr-panel textarea {width:100%;}
#crr-panel .note-list-item:last-child { border: none;}
#crr-panel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#crr-panel .property-summary {margin-top:0;}
#main-window { padding-top:0px !important; padding-bottom: 0px !important; max-width: 1142px !important; min-width: 1142px !important; }
body{ background-color: white; }
.crr-blocks { page-break-inside: avoid; }

@page {
   margin: .5in;
   size: portrait;
}

ul.leaders li:before, .leaders > div:before {
	content:"";
}
ul.leaders li {
	border-bottom: 1px dotted black;
    padding-bottom: 9px;
    padding-top: 7px;
}
</style>


<div uk-grid >





            <div id="main-report-view" class="" style=" min-width: auto; padding:0px; background-color: currentColor;">
            	@php
					$j = 0;
				@endphp
            	@forEach($data as $section)





            	<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
            		@if(property_exists($section,'parts'))
            		<?php $pieceCount = 1;?>
	            		@forEach($section->parts as $part)

	            			@forEach($part as $piece)


	            				<div class="crr-part-{{$piece->part_id}} crr-part @if(!$print) crr-part-comment-icons @endIf"> <a name="part-{{$piece->part_id}}"></a>
	            					<?php $pieceData = json_decode($piece->data);?>
	            					@if($pieceData[0]->type =='free-text')

	            						{!!$piece->content!!}
	            					@endIf
	            					{{-- @if($j > 3)
									{{ dd($pieceData[1][1]) }}
									@endif
									@php
										$j++;
									@endphp --}}
									@if($pieceData[0]->type == 'blade')
										<?php

										if (array_key_exists(2, $pieceData)) {
											$bladeData = $pieceData[2];
										} else {
											$bladeData = null;
										}
										?>
										@if($piece->blade == 'crr_parts.crr_inspections')
											@include($piece->blade, [$inspections_type = 'site', $audit_id = $report->audit->id])
										@endif
										<?php
										if (array_key_exists(3, $pieceData)) {
											$bladeData = $pieceData[3];
										} else {
											$bladeData = null;
										}
										?>
										@if($piece->blade == 'crr_parts.crr_inspections')
											@include($piece->blade, [$inspections_type = 'building', $audit_id = $report->audit->id])
										@endif


										<?php
										if (array_key_exists(1, $pieceData)) {
											$bladeData = $pieceData[1];
										} else {
											$bladeData = null;
										}
										?>
										@include($piece->blade, [$inspections_type = 'unit', $audit_id = $report->audit->id])


									@endIf
	            				</div>
	            				<?php $pieceCount++;?>
	            			@endForEach
	            		@endForEach
            		@endIf
            	</div>
            	@endForEach
            	
            </div>
            <div class=" uk-width-1-1" >
				
	            	<div class="uk-background-default uk-padding uk-panel">
			            <p class="uk-h4" align="center">To View All Ohio Violation Codes Please Visit:
			            <a href="{{URL::to('/codes')}}" class="uk-link uk-text-center uk-h4">{{URL::to('/codes')}}</a></p>
			        </div>
			   
			</div>




</div>

@stop