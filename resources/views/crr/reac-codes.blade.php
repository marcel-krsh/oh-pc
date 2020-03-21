   @extends('layouts.simplerAllita')
@section('head')
<title>Ohio UPCS Violation Code Reference.</title>


@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
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
		width:1142px; min-height: 1502px; margin-left:auto; margin-right:auto; padding: 72px;

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
            	
            	<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
            		<h1>Ohio UPCS Violation Code Reference</h1>
            		<hr class="uk-width-1-1">
            		<p>These codes can be referenced directly to your Compliance Audit Report (CAR). You can enter the reference number of your violation code in the box below, or click on the violation number in your online version of the CAR to view its description directly.</p>
            		<hr class="uk-width-1-1">
            		<div class="uk-margin">
				        <div class="uk-inline">
				            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: search"></span>
				            <input class="uk-input" type="number" placeholder="CODE NUMBER" onChange="filterTo($(this).val());" style="width: 300px;">
				        </div>
				    </div>
            		<br /><div id="clear-filter" class="uk-badge uk-text-right@s badge-filter" style="display: none;">
						<a onclick="clearFilter();" class="uk-dark uk-light" ><i class="a-circle-cross"></i> <span>CLEAR CODE FILTER</span></a>
					</div><hr class="dashed-hr uk-margin-bottom uk-width-1-1">
            		<table class="uk-table uk-striped">
            			<thead>
            				<tr>
            				<th style="width: 130px">
            					REF#
            				</th>
            				<th >
            					TYPE
            				</th>
            				<th>
            					DESCRIPTION
            				</th>
            				
            				<th width="160">
            					APPLIES TO
            				</th>
            				@if($root_access)
	            				<!-- <th >
	            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 30 DAYS" >30 Days</span>
	            				</th>
	            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 60 DAYS" >60 Days</span>
	            				<th>
	            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 90 DAYS" >90 Days</span>
	            				</th>
	            				<th>
	            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 90 DAYS" >120 Days</span>
	            				</th>
	            				<th>
	            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 365 DAYS" >365 Days</span>
	            				</th>
	            				<th>
	            					<span uk-tooltip title="VIOLATIONS FOR JANUARY 1, {{date('Y',strtotime('last year'))}} - {{date('M, d Y')}} COMPARED TO THIS YEAR TO DATE" >Y/YTD</span>
	            				</th> -->
            				@endIf
            				</tr>
            			</thead>
            			<tbody>
	            		@forEach($codes as $code)

	            				<tr class="code-{{$code->id}}-rows rows">
		            				<td @if(0 !== $code->one || 0 !== $code->two || 0 !== $code->three ) style="border-bottom: none;" @endIf >
		            					<a name="code-{{$code->id}}"></a>OH.{{strtoupper($code->type)}}.{{$code->id}}
		            				</td>
		            				<td>
		            					<span style="font-size: 27px">
			            					@if($code->type == 'nlt')
			            					<i uk-tooltip title="NON-LIFE THREATENING" class="a-booboo"></i>
			            					@elseIf($code->type == 'lt')
			            					<i uk-tooltip title="LIFE THREATENING" class="a-skull"></i>
			            					@elseIf($code->type == 'file')
			            					<i uk-tooltip title="FILE - DOCUMENTATION" class="a-folder"></i>
			            					@endIf
		            					</span>
		            				</td>
		            				<td>
		            					<strong><h3>{{$code->name}}</h3></strong>
		            				</td>
		            				
		            				<td @if($code->one || $code->two || $code->three) style="border-top: 1px solid white" rowspan="2" @endIf >
		            					<small><ul>
		            					@if($code->site) <li>SITE</li> @endIf
		            					@if($code->common_area) <li>COMMON AREAS</li> @endIf
		            					@if($code->building_exterior) <li>BUILDING EXTERIORS</li> @endIf
		            					@if($code->building_system) <li>BUILDING SYSTEMS</li> @endIf
		            					@if($code->unit) <li>UNITS</li> @endIf
		            					@if($code->file) <li>DOCUMENTATION</li> @endIf
		            					</ul>
		            					</small>
		            				</td>
		            				@if($root_access)
			            				<!-- <td>
			            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 30 DAYS" >30 Days</span>
			            				</td>
			            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 60 DAYS" >60 Days</span>
			            				<td>
			            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 90 DAYS" >90 Days</span>
			            				</td>
			            				<td>
			            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 90 DAYS" >120 Days</span>
			            				</td>
			            				<td>
			            					<span uk-tooltip title="VIOLATIONS FOR THE LAST 365 DAYS" >365 Days</span>
			            				</td>
			            				<td>
			            					<span uk-tooltip title="VIOLATIONS FOR JANUARY 1, {{date('Y',strtotime('last year'))}} - {{date('M, d Y')}} COMPARED TO THIS YEAR TO DATE" >Y/YTD</span>
			            				</td> -->
		            				@endIf
            				</tr>
            				@if(0 !== $code->one || 0 !== $code->two || 0 !== $code->three )
            				<tr class="code-{{$code->id}}-rows rows">
            					<td></td>
            					<td></td>
            					<td @if($root_access) colspan="9" @else colspan="" @endIf>
            						@if($code->one)<a name="code-{{$code->id}}-level-1"></a><strong>LEVEL 1</strong>: {{$code->one_description}} @if(0 !== $code->two || 0 !== $code->three)<hr> @endIf @endIf
            						@if($code->two)<a name="code-{{$code->id}}-level-2"></a><strong>LEVEL 2</strong>: {{$code->two_description}} @if(0 !== $code->three)<hr> @endIf @endIf
            						@if($code->three)<a name="code-{{$code->id}}-level-3"></a><strong>LEVEL 3</strong>: {{$code->three_description}} @endIf
            					</td>
            					
            					<td colspan=""></td>
            				</tr>
            				@endIf
	            				
	            		@endForEach
	            	 	</tbody>
            		</table>
            	</div>
            	
            </div>




</div>
<script>
	function filterTo(codeId){
		$('.rows').hide();
		$('.code-'+codeId+'-rows').fadeIn();
		$('#clear-filter').slideDown();
	}
	function clearFilter(){
		$('.rows').show();
		$('#clear-filter').slideUp();
	}
	@if($codeId)
	$(function(){
		filterTo({{$codeId}});
	});
	@endIf
</script>

@stop