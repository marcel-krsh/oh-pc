@extends('layouts.allita-mobile')
@section('header')
MY AUDITS ({{count($audits)}})
@endsection
@section('content')
	
	@forEach($audits as $a)
		@if($a->cached_audit)
		<div class="uk-width-1-1 ">
			<h3><i class="a-info-circle" id="info-audit-{{$a->id}}-toggle" onclick="$('#info-audit-{{$a->id}}').slideToggle();infoToggle(this)"></i> <span onclick="$('#info-audit-{{$a->id}}-toggle').trigger('click');">{{$a->project->project_name}}</span>
			<small class="small-h3">AUDIT:&nbsp;{{$a->id}}&nbsp;|&nbsp;<i class="{{$a->cached_audit->step_status_icon}}"></i>&nbsp;{!!str_replace(' ','&nbsp;',$a->cached_audit->step_status_text)!!}</small></h3>
		</div>
		<div id="info-audit-{{$a->id}}" class="uk-width-1-1 uk-margin-small-top" style="display: none;">
			<h3>

				<ul class="audit-info-mobile"><li><h3 class="uk-margin-remove findings-details-mobile">@if(count($a->findings)>0)<i class="a-info-circle" id="audit-{{$a->id}}-findings-toggle" onclick="$('#audit-{{$a->id}}-findings').slideToggle();infoToggle(this);"></i>  @endIf <span onclick="$('#audit-{{$a->id}}-findings-toggle').trigger('click');"><i class="a-booboo"></i> {{count($a->nlts)}}  &nbsp; &nbsp; <i class="a-skull"></i> {{count($a->lts)}}</span></h3>
					<div id="audit-{{$a->id}}-findings" class=" findings-details-mobile" style="display: none; margin-top: 12px; margin-bottom: 12px;">
						<?php // lets make these go in a nice order:
							
							
						?>
						<i class="a-info-circle" id="audit-{{$a->id}}-site-findings-toggle" onclick="$('#audit-{{$a->id}}-site-findings').slideToggle();infoToggle(this);"></i> <span class="finding-breakdown" onclick="$('#audit-{{$a->id}}-site-findings-toggle').trigger('click')">SITE:</span><span class="finding-breakdown-stat" onclick="$('#audit-{{$a->id}}-site-findings-toggle').trigger('click')"><i class="a-booboo"></i> {{count($a->site_nlts)}}</span><span class="finding-breakdown-stat" onclick="$('#audit-{{$a->id}}-site-findings-toggle').trigger('click')"><i class="a-skull"></i> {{count($a->site_lts)}}</span>
						@if(count($a->findings->where('building_id',null)->where('unit_id',null)))
							<div id="audit-{{$a->id}}-site-findings" style="display: none; margin-top: 21px; margin-bottom: 24px">
								<?php 	$findings = $a->findings->where('cancelled_at',null)->where('building_id',null)->where('unit_id',null)->sortBy('date_of_finding');
										$cancelledFindings = $a->findings->where('cancelled_at','<>',null)->where('building_id',null)->where('unit_id',null)->sortBy('date_of_finding');
								?>
								@forEach($findings as $f)
									<i class="{{$f->icon()}}"></i> <span onClick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)"><span class="dimmer">F|N</span>{{$f->id}} OH-{{strtoupper($f->finding_type->type)}}-{{$f->finding_type_id}} Level {{$f->level}}</span><br />@if(count($f->comments)) {{$f->comments->first()->comment}} @else {{$f->finding_type->name}} @endIf
									<hr />
								@endForEach
								<div class="close-long-box " onclick="$('#audit-{{$a->id}}-site-findings-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE SITE FINDINGS</div>
							</div>
						@endIf
						<hr class="dashed-hr uk-margin-bottom">
						<i class="a-info-circle" id="audit-{{$a->id}}-building-findings-toggle" onclick="$('#audit-{{$a->id}}-building-findings').slideToggle();infoToggle(this);"></i> <span class="finding-breakdown" onclick="$('#audit-{{$a->id}}-building-findings-toggle').trigger('click')">BUILDING:</span><span class="finding-breakdown-stat" onclick="$('#audit-{{$a->id}}-building-findings-toggle').trigger('click')"><i class="a-booboo"></i> {{count($a->building_nlts)}}</span><span class="finding-breakdown-stat" onclick="$('#audit-{{$a->id}}-building-findings-toggle').trigger('click')"><i class="a-skull"></i> {{count($a->building_lts)}}</span>
						@if(count($a->findings->where('building_id','<>',null)))
							<div id="audit-{{$a->id}}-building-findings" style="display: none; margin-top: 21px; margin-bottom: 24px">
								<?php 	$findings = $a->findings->where('cancelled_at',null)->where('building_id','<>',null)->sortBy('building.building_name');
										$cancelledFindings = $a->findings->where('cancelled_at','<>',null)->where('building_id',null)->where('unit_id',null)->sortBy('building.building_name');
								?>
								@forEach($findings as $f)
									<i class="{{$f->icon()}}"></i> <span onClick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)"><span class="dimmer">F|N</span>{{$f->id}} <span class="dimmer">BIN</span>{{$f->building->building_name}} <br />OH-{{strtoupper($f->finding_type->type)}}-{{$f->finding_type_id}} Level {{$f->level}}</span><br />@if(count($f->comments)) {{$f->comments->first()->comment}} @else {{$f->finding_type->name}} @endIf
									<hr />
								@endForEach
								<div class="close-long-box " onclick="$('#audit-{{$a->id}}-building-findings-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE BUILDING FINDINGS</div>
							</div>
						@endIf
						<hr class="dashed-hr uk-margin-bottom">
						<i class="a-info-circle" id="audit-{{$a->id}}-unit-findings-toggle" onclick="$('#audit-{{$a->id}}-unit-findings').slideToggle();infoToggle(this);"></i> <span class="finding-breakdown" onclick="$('#audit-{{$a->id}}-unit-findings-toggle').trigger('click')">UNIT:</span><span class="finding-breakdown-stat"onclick="$('#audit-{{$a->id}}-unit-findings-toggle').trigger('click')"><i class="a-booboo"></i> {{count($a->unit_nlts)}}</span><span class="finding-breakdown-stat"onclick="$('#audit-{{$a->id}}-unit-findings-toggle').trigger('click')"> <i class="a-skull"></i> {{count($a->unit_lts)}}</span>
						@if(count($a->findings->where('unit_id','<>',null)))
							<div id="audit-{{$a->id}}-unit-findings" style="display: none; margin-top: 21px; margin-bottom: 24px">
								<?php 	$findings = $a->findings->where('cancelled_at',null)->where('unit_id','<>',null)->sortBy('unit.unit_name');
										$cancelledFindings = $a->findings->where('cancelled_at','<>',null)->where('unit_id',null)->where('unit_id',null)->sortBy('unit.unit_name');
								?>
								@forEach($findings as $f)
									<i class="{{$f->icon()}}"></i> <span onClick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)"><span class="dimmer">F|N</span>{{$f->id}} <span class="dimmer">BIN</span>{{$f->unit->building->building_name}} #{{$f->unit->unit_name}} <br/>OH-{{strtoupper($f->finding_type->type)}}-{{$f->finding_type_id}} Level {{$f->level}}</span><br />@if(count($f->comments)) {{$f->comments->first()->comment}} @else {{$f->finding_type->name}} @endIf
									<hr />
								@endForEach
								<div class="close-long-box " onclick="$('#audit-{{$a->id}}-unit-findings-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE UNIT FINDINGS</div>
							</div>
						@endIf
						<hr class="dashed-hr ">
						<div class="close-long-box " onclick="$('#audit-{{$a->id}}-findings-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE FINDINGS</div>
					</div>

				</li>
					@if($a->lead)<li><i id="audit-{{$a->id}}-lead-info-toggle" class="a-info-circle" onclick="$('#audit-{{$a->id}}-lead-info').slideToggle();infoToggle(this)"></i> <span onclick="$('#audit-{{$a->id}}-lead-info-toggle').trigger('click');">Lead : {{$a->lead->name}}</span>
						
						<div id="audit-{{$a->id}}-lead-info" style="display: none; margin-bottom: 24px; margin-top:21px;"> 
							<a class="uk-button" href="mailto:{{$a->lead->email}}"><i class="a-envelope-4"></i></a> 
						&nbsp; &nbsp; @if($a->lead && $a->lead->person && $a->lead->person->allita_phone)<a class="uk-button" href="sms://+1{{$a->lead->person->allita_phone->area_code}}{{$a->lead->person->allita_phone->phone_number}}"><i class="a-comment"></i></a> 
						&nbsp; &nbsp; 
					<a class="uk-button"  href="tel://+1{{$a->lead->person->allita_phone->area_code}}{{$a->lead->person->allita_phone->phone_number}}"><i class="a-phone-talk"></i></a>@endIf</li>@endIf
					@if(count($a->auditors)>0)
					<li><i id="audit-{{$a->id}}-auditors-toggle" class="a-info-circle" onclick="$('#audit-{{$a->id}}-auditors').slideToggle();infoToggle(this)"></i> <span onclick="$('#audit-{{$a->id}}-auditors-toggle').trigger('click');">{{count($a->auditors)}} @if(count($a->auditors)>1 || count($a->auditors) < 1)Auditors @else Auditor @endIf Assigned </span>
						<div id="audit-{{$a->id}}-auditors" style="display: none; margin-top: 5px; margin-bottom: 12px;">
							@forEach($a->auditors as $auditor)

								<hr class="dashed-hr uk-margin-bottom">
								<i class="a-info-circle" id="audit-{{$a->id}}-auditor-{{$auditor->id}}-info-toggle" onclick="$('#audit-{{$a->id}}-auditor-{{$auditor->id}}-info').slideToggle();infoToggle(this)"></i> <span onclick="$('#audit-{{$a->id}}-auditor-{{$auditor->id}}-info-toggle').trigger('click')">{{$auditor->user->name}} </span>
							
								<div id="audit-{{$a->id}}-auditor-{{$auditor->id}}-info" style="display: none; margin-bottom: 24px; margin-top:21px;"> 
									<a class="uk-button" href="mailto:{{$auditor->email}}"><i class="a-envelope-4"></i></a> 
								&nbsp; &nbsp; @if($auditor->user->person && $auditor->user->person->allita_phone)<a class="uk-button" href="sms://+1{{$auditor->user->person->allita_phone->area_code}}{{$auditor->user->person->allita_phone->phone_number}}"><i class="a-comment"></i></a> 
								&nbsp; &nbsp; 
									<a class="uk-button"  href="tel://+1{{$auditor->user->person->allita_phone->area_code}}{{$auditor->user->person->allita_phone->phone_number}}"><i class="a-phone-talk"></i></a>@endIf
								</div>
								
							@endForEach
							<div class="close-long-box " onclick="$('#audit-{{$a->id}}-auditors-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE AUDITORS LIST</div>
						</div>
					</li>
					@endIf
					<li>Buildings : {{count($a->building_inspections->where('complete','1'))}}/{{$a->cached_audit->total_buildings}} AUDITED</li>
					<li>Unit Inspections : {{count($a->unit_inspections)}}</li>
					<li>Programs : {{count($a->project->programs)}}</li>
					<li>Address : <div style="display: inline-table;"><a href="http://maps.apple.com/?daddr={{$a->project->address->line_1}}@if(null !== $a->project->address->line_2 && $a->project->address->line_2 !== '') {{$a->project->address->line_2}}@endIf @if(null !== $a->project->address->city) {{$a->project->address->city}} @endIf {{$a->project->address->state}} {{$a->project->address->zip}}&dirflg=d&t=h"> {{$a->project->address->line_1}}@if(null !== $a->project->address->line_2 && $a->project->address->line_2 !== '')<br />{{$a->project->address->line_2}}@endIf @if(null !== $a->project->address->city)<br />{{$a->project->address->city}},@endIf {{$a->project->address->state}} {{$a->project->address->zip}}</a></div></li>
				</ul>
			</h3>
			<div class="close-long-box " onclick="$('#info-audit-{{$a->id}}-toggle').trigger('click');"><i class="a-circle-up"></i> CLOSE AUDIT DETAILS</div>
		</div>
		<hr class="dashed-hr uk-width-1-1">
		@endIf
	@endForEach
	<script type="text/javascript">
		function infoToggle(target){
			if($(target).hasClass('a-info-circle')){
				$(target).removeClass('a-info-circle');
				$(target).addClass('a-circle-up');
			}else{
				$(target).removeClass('a-circle-up');
				$(target).addClass('a-info-circle');
				
			}
		}
	</script>

@endsection