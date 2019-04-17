<?php $findings = $bladeData;?>
@if(!is_null($findings))
	<div uk-grid>
		<div class="uk-width-1-1">
			<h2>Findings: </h2><small><i class="a-folder"></i> : FILE FINDING &nbsp;|  &nbsp;<i class="a-booboo"></i> : NON LIFE THREATENING FINDING  &nbsp;|  &nbsp;<i class="a-skull"></i> : LIFE THREATENING FINDING </small><hr class="dashed-hr">
		</div>
	@forEach($findings as $f)
		<?php //dd($f); ?>
		<div class="uk-width-1-3 crr-blocks" style="border-bottom:1px dotted #3c3c3c; padding-top:12px; padding-bottom: 18px; page-break-inside: avoid;">
			<div style="min-height: 105px;">
			<hr><strong>Finding # {{$f->id}}</strong><hr />
			{{date('m/d/y', strtotime($f->date_of_finding))}} | AID:{{$f->auditor->id}} <br />

			@if(!is_null($f->building_id))
				<strong>{{$f->building->building_name}}</strong> <br />
				@if(!is_null($f->building->address))
			   	{{$f->building->address->line_1}} {{$f->building->address->line_2}}<br />
			   	{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}
			   	@endIf
			
			@elseIf(!is_null($f->unit_id))
				{{$f->unit->building->building_name}} <br />
				@if(!is_null($f->unit->building->address))
			   	{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}}<br />
			   	{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}
			   	@endIf
			   	<br /><strong>Unit {{$f->unit->unit_name}}</strong>
			@endIf

			
			</div>
			<hr class="dashed-hr">
				<h2>@if($f->finding_type->type == 'nlt')
					<i class="a-booboo"></i> 
				@endIf 
				@if($f->finding_type->type == 'lt')
					<i class="a-skull"></i> 
				@endIf 
				@if($f->finding_type->type == 'file')
					<i class="a-folder"></i>  
				@endIf  {{$f->amenity->amenity_description}}</h2>
			   	<strong>{{$f->finding_type->name}}</strong><br>
			   	@if($f->level == 1)
			   		{{$f->finding_type->one_description}}
			   	@endIf
			   	@if($f->level == 2)
			   		{{$f->finding_type->two_description}}
			   	@endIf
			   	@if($f->level == 3)
			   		{{$f->finding_type->three_description}}
			   	@endIf
			   	@if(!is_null($f->comments))
			   	
			   	@forEach($f->comments as $c)
				   	@if(is_null($c->deleted_at))
					   	<hr class="dashed-hr uk-margin-bottom">
					   	<i class="a-comment"></i> : {{$c->comment}}
				   	@endIf
			   	@endForEach
			   	@endIf
			<hr class="dashed-hr uk-margin-bottom">
			<div style="min-height: 80px;">
				<?php $piecePrograms = collect($f->amenity_inspection->unit_programs)->where('audit_id',$report->audit_id); 
						//dd($piecePrograms);
				?>
				@if(count($piecePrograms)>0)
				<span class="uk-margin-bottom"><strong >PROGRAMS:</strong></span><ul > @forEach($piecePrograms as $p) 
					<li>@if(!is_null($p->is_substitute))SUBSTITUTED FOR:@endIf 
					{{$p->program->program_name}}</li>
					@endForEach
				</ul>
				@endIf
			</div>
		

		</div>
	@endForEach
	</div>
@else
<hr class="dashed-hr">
<h3>NO FINDINGS</h3>
@endIf