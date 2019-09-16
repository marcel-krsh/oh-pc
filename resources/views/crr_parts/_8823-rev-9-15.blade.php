<?php
$buildings = $bladeData[1];
$findings = $bladeData[0];
//dd($buildings,$findings);

?>
<style>
	._8823-field-text {
		font-size:12pt;
		color:black;
		font-family: arial,_sans;
		/*background-color: #add8e68a;*/
		min-height: 19px;
		min-width: 19px;
		color: darkblue;
	}
	._8823-field-1 {
		position: absolute;
	    top: 117px;
	    left: 1040px;
	}
	._8823-field-2 {
		position: absolute;
	    top: 154px;
	    left: 554px;
	}
	._8823-field-3 {
		position: absolute;
	    top: 174px;
	    left: 93px;
	}
	._8823-field-4 {
		position: absolute;
	    top: 218px;
	    left: 93px;
	}
	._8823-field-5 {
		    position: absolute;
		    top: 262px;
		    left: 95px;
	}
	._8823-field-6 {
		position: absolute;
	    top: 283px;
	    left: 408px;
	}
	._8823-field-7 {
		position: absolute;
	    top: 306px;
	    left: 554px;
	}
	._8823-field-8 {
		position: absolute;
	    top: 323px;
	    left: 93px;
	}
	._8823-field-9 {
		position: absolute;
	    top: 370px;
	    left: 93px;
	}
	._8823-field-10 {
		position: absolute;
	    top: 412px;
	    left: 93px;
	}
	._8823-field-11 {
		position: absolute;
	    top: 455px;
	    left: 93px;
	}
	._8823-field-12 {
		position: absolute;
	    top: 456px;
	    left: 417px;
	}
	._8823-field-13 {
		position: absolute;
	    top: 456px;
	    left: 498px;
	}
	._8823-field-14 {
		    position: absolute;
		    top: 478px;
		    text-align: right;
		    left: 979px;
		    width: 89px;
	}
	._8823-field-15 {
		position: absolute;
	    top: 500px;
	    text-align: right;
	    left: 979px;
	    width: 89px;
	}
	._8823-field-16 {
		position: absolute;
	    top: 521px;
	    text-align: right;
	    left: 979px;
	    width: 89px;
	}
	._8823-field-17 {
		position: absolute;
	    top: 542px;
	    text-align: right;
	    left: 979px;
	    width: 89px;
	}
	._8823-field-18 {
		position: absolute;
	    top: 564px;
	    text-align: right;
	    left: 979px;
	    width: 89px;
	}
	._8823-field-19 {
		position: absolute;
	    top: 586px;
	    text-align: right;
	    left: 979px;
	    width: 89px;
	}
	._8823-field-20 {
		position: absolute;
	    top: 608px;
	    text-align: right;
	    left: 913px;
	    width: 155px;
	}
	._8823-field-21 {
		position: absolute;
	    top: 630px;
	    text-align: right;
	    left: 913px;
	    width: 155px;
	}
	._8823-field-22 {
		position: absolute;
	    top: 652px;
	    left: 1013px;
	}
	._8823-field-23 {
		position: absolute;
	    top: 717px;
	    left: 919px;
	}
	._8823-field-24 {
		position:absolute;
		top:717px;
		left:1013px;
	}
	._8823-field-25 {
		position: absolute;
	    top: 739px;
	    left: 919px;
	}
	._8823-field-26 {
		position:absolute;
		top:739px;
		left:1013px;
	}
	._8823-field-27 {
		position: absolute;
	    top: 760px;
	    left: 919px;
	}
	._8823-field-28 {
		position:absolute;
		top:760px;
		left:1013px;
	}
	._8823-field-29 {
		position: absolute;
	    top: 782px;
	    left: 919px;
	}
	._8823-field-30 {
		position:absolute;
		top:782px;
		left:1013px;
	}
	._8823-field-31 {
		position: absolute;
	    top: 804px;
	    left: 919px;
	}
	._8823-field-32 {
		position:absolute;
		top:804px;
		left:1013px;
	}
	._8823-field-33 {
		position: absolute;
	    top: 826px;
	    left: 919px;
	}
	._8823-field-34 {
		position:absolute;
		top:826px;
		left:1013px;
	}
	._8823-field-35 {
		position: absolute;
	    top: 848px;
	    left: 919px;
	}
	._8823-field-36 {
		position:absolute;
		top:848px;
		left:1013px;
	}
	._8823-field-37 {
		position: absolute;
	    top: 869px;
	    left: 919px;
	}
	._8823-field-38 {
		position:absolute;
		top:869px;
		left:1013px;
	}
	._8823-field-39 {
		position: absolute;
	    top: 891px;
	    left: 919px;
	}
	._8823-field-40 {
		position:absolute;
		top:891px;
		left:1013px;
	}
	._8823-field-41 {
		position: absolute;
	    top: 913px;
	    left: 919px;
	}
	._8823-field-42 {
		position:absolute;
		top:913px;
		left:1013px;
	}
	._8823-field-43 {
		position: absolute;
	    top: 934px;
	    left: 919px;
	}
	._8823-field-44 {
		position:absolute;
		top:934px;
		left:1013px;
	}
	._8823-field-45 {
		position: absolute;
	    top: 956px;
	    left: 919px;
	}
	._8823-field-46 {
		position:absolute;
		top:956px;
		left:1013px;
	}
	._8823-field-47 {
		position: absolute;
	    top: 978px;
	    left: 919px;
	}
	._8823-field-48 {
		position:absolute;
		top:978px;
		left:1013px;
	}
	._8823-field-49 {
		position: absolute;
	    top: 999px;
	    left: 919px;
	}
	._8823-field-50 {
		position:absolute;
		top:999px;
		left:1013px;
	}
	._8823-field-51 {
		position: absolute;
	    top: 1021px;
	    left: 919px;
	}
	._8823-field-52 {
		position:absolute;
		top:1021px;
		left:1013px;
	}
	._8823-field-53 {
		position: absolute;
	    top: 1042px;
	    left: 919px;
	}
	._8823-field-54 {
		position: absolute;
	    top: 1063px;
	    left: 919px;
	}
	._8823-field-55 {
		position: absolute;
	    top: 1063px;
	    left: 1013px;
	}
	._8823-field-56 {
		position: absolute;
	    top: 1084px;
	    left: 1013px;
	}
	._8823-field-57 {
		position: absolute;
	    top: 1107px;
	    left: 310px;
	}
	._8823-field-58 {
		position: absolute;
	    top: 1107px;
	    left: 417px;
	}
	._8823-field-59 {
		position: absolute;
	    top: 1107px;
	    left: 578px;
	}
	._8823-field-60 {
		position: absolute;
	    top: 1107px;
	    left: 739px;
	}
	._8823-field-61 {
		position: absolute;
	    top: 1128px;
	    left: 443px;
	}
	._8823-field-62 {
	    position: absolute;
	    top: 1168px;
	    left: 121px;
	}
	._8823-field-63 {
		position: absolute;
	    top: 1212px;
	    left: 121px;
	}
	._8823-field-64 {
		 position: absolute;
	    top: 1256px;
	    left: 121px;
	}
	._8823-field-65 {
		position: absolute;
	    top: 1171px;
	    left: 616px;
	}
	._8823-field-66 {
		position: absolute;
	    top: 1171px;
	    left: 913px;
	}
	._8823-field-67 {
		position: absolute;
	    top: 1171px;
	    left: 994px;
	}
	._8823-field-68 {
		position: absolute;
	    top: 1212px;
	    left: 616px;
	}
	._8823-field-69 {
	    position: absolute;
	    top: 1256px;
	    left: 617px;
	}
	._8823-field-70 {
		position: absolute;
	    top: 1256px;
	    left: 912px;
	}
	._8823-field-71 {
		position: absolute;
	    top: 1311px;
	    left: 93px;
	    width: 390PX;
	    height: 50px;
	}
	._8823-field-72 {
		position: absolute;
	    top: 1320px;
	    left: 510px;
	}
	._8823-field-73 {
		position: absolute;
	    top: 1320px;
	    left: 972px;
	}
	._8823-field-74 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-75 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-76 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-77 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-78 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-79 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-field-80 {
		position:absolute;
		top:480px;
		left:93px;
	}
	._8823-rev-9-15 {
		max-width: 500% !important;
	    width: 1139px;
	    height: 1429px;
	    position: relative;
	    top: -72px;
	    left: -72px;
	    opacity: .6;
	}
	@media print {
   		._8823-rev-9-15 {
			max-width: 500% !important;
		    width: 1139px;
		    height: 1429px;
		    position: relative;
		    top: -.1in;
		    left: -.80in;
		    opacity: 1;
		}
		.crr-sections {
			page-break-after: always;
		}
		._8823-field-text {
			color:black;
		}
		._8823-field-1 {
			position: absolute;
		    top: 179px;
		    left: 1036px;
		}
		._8823-field-2 {
			position: absolute;
		    top: 216px; /*154*/
		    left: 550px;
		}
		._8823-field-3 {
			position: absolute;
		    top: 236px; /*174*/
		    left: 89px;
		}
		._8823-field-4 {
			position: absolute;
		    top: 280px;
		    left: 89px;
		}
		._8823-field-5 {
			    position: absolute;
			    top: 324px;
			    left: 91px;
			    
		}
		._8823-field-6 {
			position: absolute;
		    top: 345px;
		    left: 404px;
		}
		._8823-field-7 {
			position: absolute;
		    top: 368px;
		    left: 550px;
		}
		._8823-field-8 {
			position: absolute;
		    top: 385px;
		    left: 89px;
		}
		._8823-field-9 {
			position: absolute;
		    top: 432px;
		    left: 89px;
		}
		._8823-field-10 {
			position: absolute;
		    top: 475px;
		    left: 89px;
		}
		._8823-field-11 {
			position: absolute;
		    top: 517px;
		    left: 89px;/*?*/
		}
		._8823-field-12 {
			position: absolute;
		    top: 518px;
		    left: 413px;
		}
		._8823-field-13 {
			position: absolute;
		    top: 518px;
		    left: 494px;
		}
		._8823-field-14 {
		    position: absolute;
		    top: 540px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		/*///*/
		._8823-field-15 {
			position: absolute;
		    	top: 562px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		._8823-field-16 {
			position: absolute;
		    	top: 583px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		._8823-field-17 {
			position: absolute;
		    	top: 604px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		._8823-field-18 {
			position: absolute;
		    	top: 626px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		._8823-field-19 {
			position: absolute;
		    	top: 648px;
		    text-align: right;
		    left: 975px;
		    width: 89px;
		}
		._8823-field-20 {
			position: absolute;
		    	top: 670px;
		    text-align: right;
		    left: 909px;
		    width: 155px;
		}
		._8823-field-21 {
			position: absolute;
		    	top: 692px;
		    text-align: right;
		    left: 909px;
		    width: 155px;
		}
		._8823-field-22 {
			position: absolute;
		    	top: 714px;
		    left: 1009px;
		}
	}
</style>
		<h2>Please See The {{count($buildings)}} 8823s Attached</h2>
		<hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
		<p>Buildings in this batch:</p>
		<div class="uk-column-1-4">
			<ul>
				@forEach($buildings as $building)
				<li class="uk-margin-bottom"><a href="#_8823-{{$building->id}}">BIN: {{$building->building_name}}</a>

					<br />@if($building->address)
							{{$building->address->line_1}}<br />
							@if($building->address->line_2) {{$building->address->line_2}}<br />@endIf
							@if($building->address->city) {{$building->address->city}},@endIf
							@if($building->address->state) {{$building->address->state}} @endIf
							@if($building->address->zip) {{$building->address->zip}}@endIf


						@else
							No Address is Available for this Building.
						@endIf

				</li>
				@endForEach
			</ul>
		</div>
	
	</div>
</div>
@forEach($buildings as $building)
<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" >
	<a name="_8823-{{$building->id}}"></a>
	
		<img src="/images/8823/f8823_Rev_9-15.png" class="_8823-rev-9-15">
		<div class="_8823-field-text _8823-field-1">X</div>
		<div class="_8823-field-text _8823-field-2">X</div>
		<div class="_8823-field-text _8823-field-3">{{$building->building_name}}</div>
		<div class="_8823-field-text _8823-field-4">{{$building->address->line_1}}</div>
		<div class="_8823-field-text _8823-field-5">{{$building->address->city}}</div>
		<div class="_8823-field-text _8823-field-6">{{$building->building_name}}</div>
		<div class="_8823-field-text _8823-field-7">X</div>
		<div class="_8823-field-text _8823-field-8">Owner's Name</div>
		<div class="_8823-field-text _8823-field-9">Owner's Street</div>
		<div class="_8823-field-text _8823-field-10">Owner's City</div>
		<div class="_8823-field-text _8823-field-11">Owner EIN</div>
		<div class="_8823-field-text _8823-field-12">X</div>
		<div class="_8823-field-text _8823-field-13">X</div>
		<div class="_8823-field-text _8823-field-14">100,000,000</div>
		<div class="_8823-field-text _8823-field-15">10,00</div>
		<div class="_8823-field-text _8823-field-16">0</div>
		<div class="_8823-field-text _8823-field-17">100,000</div>
		<div class="_8823-field-text _8823-field-18">100,000</div>
		<div class="_8823-field-text _8823-field-19">100,000</div>
		<div class="_8823-field-text _8823-field-20">MMDDYYYY</div>
		<div class="_8823-field-text _8823-field-21">MMDDYYYY</div>
		<div class="_8823-field-text _8823-field-22">X</div>
		<div class="_8823-field-text _8823-field-23">X</div>
		<div class="_8823-field-text _8823-field-24">X</div>
		<div class="_8823-field-text _8823-field-25">X</div>
		<div class="_8823-field-text _8823-field-26">X</div>
		<div class="_8823-field-text _8823-field-27">X</div>
		<div class="_8823-field-text _8823-field-28">X</div>
		<div class="_8823-field-text _8823-field-29">X</div>
		<div class="_8823-field-text _8823-field-30">X</div>
		<div class="_8823-field-text _8823-field-31">X</div>
		<div class="_8823-field-text _8823-field-32">X</div>
		<div class="_8823-field-text _8823-field-33">X</div>
		<div class="_8823-field-text _8823-field-34">X</div>
		<div class="_8823-field-text _8823-field-35">X</div>
		<div class="_8823-field-text _8823-field-36">X</div>
		<div class="_8823-field-text _8823-field-37">X</div>
		<div class="_8823-field-text _8823-field-38">X</div>
		<div class="_8823-field-text _8823-field-39">X</div>
		<div class="_8823-field-text _8823-field-40">X</div>
		<div class="_8823-field-text _8823-field-41">X</div>
		<div class="_8823-field-text _8823-field-42">X</div>
		<div class="_8823-field-text _8823-field-43">X</div>
		<div class="_8823-field-text _8823-field-44">X</div>
		<div class="_8823-field-text _8823-field-45">X</div>
		<div class="_8823-field-text _8823-field-46">X</div>
		<div class="_8823-field-text _8823-field-47">X</div>
		<div class="_8823-field-text _8823-field-48">X</div>
		<div class="_8823-field-text _8823-field-49">X</div>
		<div class="_8823-field-text _8823-field-50">X</div>
		<div class="_8823-field-text _8823-field-51">X</div>
		<div class="_8823-field-text _8823-field-52">X</div>
		<div class="_8823-field-text _8823-field-53">X</div>
		<div class="_8823-field-text _8823-field-54">X</div>
		<div class="_8823-field-text _8823-field-55">X</div>
		<div class="_8823-field-text _8823-field-56">X</div>
		<div class="_8823-field-text _8823-field-57">X</div>
		<div class="_8823-field-text _8823-field-58">X</div>
		<div class="_8823-field-text _8823-field-59">X</div>
		<div class="_8823-field-text _8823-field-60">X</div>
		<div class="_8823-field-text _8823-field-61">MMDDYYYY</div>
		<div class="_8823-field-text _8823-field-62">New Owner's Name</div>
		<div class="_8823-field-text _8823-field-63">New Owner's Street Address</div>
		<div class="_8823-field-text _8823-field-64">New Owner's City, State Zip</div>
		<div class="_8823-field-text _8823-field-65">New Owner's EIN</div>
		<div class="_8823-field-text _8823-field-66">X</div>
		<div class="_8823-field-text _8823-field-67">X</div>
		<div class="_8823-field-text _8823-field-68">Contact Person Name</div>
		<div class="_8823-field-text _8823-field-69">(000) 000-0000</div>
		<div class="_8823-field-text _8823-field-70">EXT</div>
		<div class="_8823-field-text _8823-field-71">Signature</div>
		<div class="_8823-field-text _8823-field-72">Name???</div>
		<div class="_8823-field-text _8823-field-73">MMDDYY</div>

	
</div>
@endForEach

