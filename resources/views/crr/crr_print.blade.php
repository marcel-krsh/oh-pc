@extends('layouts.simplerAllita')
@section('head')
<title>{{$report->template()->template_name}}: {{date('y',strtotime($report->audit->scheduled_at))}}-{{$report->audit->id}}.{{str_pad($report->version, 3, '0', STR_PAD_LEFT)}}</title> 


@stop
@section('content')
<!-- <script src="/js/components/upload.js"></script>
<script src="/js/components/form-select.js"></script>
<script src="/js/components/datepicker.js"></script>
<script src="/js/components/tooltip.js"></script> -->
<style>
	<?php // determin background type
            		$background = "";
            		if($report->crr_approval_type_id == 1){
            			$background = '-draft';
            		}
            		if($report->crr_approval_type_id == 2){
            			$background = '-pending';
            		}
            		if($report->crr_approval_type_id == 3){
            			$background = '-declined';
            		}
            		if($report->crr_approval_type_id == 4){
            			$background = '-revise';
            		}
            		?>
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
   margin: 0cm;
   size: portrait;
}
</style>


<div uk-grid >
    

   
       
            
            <div id="main-report-view" class="" style=" min-width: auto; padding:0px; background-color: currentColor;">
            	@forEach($data as $section)
            	
            	
            	
            	
            	
            	<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
            		@if(property_exists($section,'parts'))
            		<?php $pieceCount = 1; ?>
	            		@forEach($section->parts as $part)
	            		
	            			@forEach($part as $piece)
	            				
	            				
	            				<div class="crr-part-{{$piece->part_id}} crr-part @if(!$print) crr-part-comment-icons @endIf"> <a name="part-{{$piece->part_id}}"></a>
	            					<?php $pieceData = json_decode($piece->data);?>
	            					@if($pieceData[0]->type =='free-text')
	            						{!!$piece->content!!}
	            					@endIf
	            					@if($pieceData[0]->type == 'blade')
	            						<?php 
	            							if(array_key_exists(1,$pieceData)){
	            								$bladeData = $pieceData[1];
	            							}else{
	            								$bladeData = null;
	            							}
	            						?>
	            						@include($piece->blade)
	            					@endIf
	            				</div>
	            				<?php $pieceCount ++; ?>
	            			@endForEach
	            		@endForEach
            		@endIf
            	</div>
            	@endForEach
            </div>



	
</div>

@stop