<?php $findings = $bladeData;?>
@if(!is_null($findings))
	
	<?php 
		// count them up...
		$fileCount = 0;
		$nltCount = 0;
		$ltCount = 0;
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

	?>

	<div uk-grid>
		<div class="uk-width-1-1">
			<h2>Findings: </h2> <small>
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
					@endIf</small><hr class="dashed-hr">
		</div>
		<?php $columnCount = 1; ?>
	@forEach($findings as $f)
		<div class="uk-width-1-3 crr-blocks" style="border-bottom:1px dotted #3c3c3c; @if($columnCount < 3) border-right:1px dotted #3c3c3c; @endIf padding-top:12px; padding-bottom: 18px; page-break-inside: avoid;">
			<?php
				// using column count to put in center lines rather than rely on css which breaks.
				$columnCount++;
				if($columnCount > 3){
					$columnCount = 1;
				}
			?>
			<div style="min-height: 105px;">
			
			

				<div class="inspec-tools-tab-finding-top-actions" style="z-index:10">
					@can('access_auditor') @if(!$print)<a onclick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)" class="uk-mute-link">
				<i class="a-pencil"></i>@endIf @endCan <strong>Finding # {{$f->id}}</strong>@can('access_auditor') @if(!$print)</a> @endIf @endCan
									@if(!$print)
										<span class="use-hand-cursor" style="float: right;" aria-expanded="false"><i class="a-circle-plus  "></i> ADD RESPONSE</span>
										<div uk-drop="mode: click; pos: bottom-right" style="min-width: 315px; background-color: #ffffff;  ">
											<div class="uk-card uk-card-body uk-card-default uk-card-small">
												<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
													@can('access_auditor')
													<div class="icon-circle use-hand-cursor" onclick="addChildItem({{ $f->id }}, 'followup')"><i class="a-bell-plus"></i></div>
													<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $f->id }}, 'comment')"><i class="a-comment-plus"></i></div>
													@endCan
													<div class="icon-circle use-hand-cursor" onclick="dynamicModalLoad('new-outbound-email-entry/{{$report->project_id}}/{{$report->audit_id}}/{{$report->id}}/{{$f->id}}')" ><i class="a-envelope-4"></i>
													</div>
													<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $f->id }}, 'document')"><i class="a-file-plus"></i></div>
													@if(env('APP_ENV') == 'local')
													<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $f->id }}, 'photo')"><i class="a-picture"></i></div>
													@endIf
												</div>
											</div>
										</div>
										@endIf
									</div>

				<hr />
			
			
			

			@if(!is_null($f->building_id))
				<strong>{{$f->building->building_name}}</strong> <br />
				@if(!is_null($f->building->address))
			   	{{$f->building->address->line_1}} {{$f->building->address->line_2}}<br />
			   	{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}<br /><br />
			   	@endIf

			@elseIf(!is_null($f->unit_id))
				{{$f->unit->building->building_name}} <br />
				@if(!is_null($f->unit->building->address))
			   	{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}}<br />
			   	{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}
			   	@endIf
			   	<br /><strong>Unit {{$f->unit->unit_name}}</strong>
			@else
				<strong>Site Finding</strong><br />
				{{$f->project->address->line_1}} {{$f->project->address->line_2}}<br />
				{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}<br /><br />
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
			   	<strong> {{$f->finding_type->name}}</strong><br>
			   	@if($f->level == 1)
			   		{{$f->finding_type->one_description}}
			   	@endIf
			   	@if($f->level == 2)
			   		{{$f->finding_type->two_description}}
			   	@endIf
			   	@if($f->level == 3)

			   		{{$f->finding_type->three_description}}
			   	@endIf
			   	@if((is_null($f->level) || $f->level == 0) && $f->finding_type->type !== 'file')
			   	<span style="color:red" class="attention">!!LEVEL NOT SET!!</span> 
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
			@if($f->amenity_inspection)
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
			@endIf
			
  		</a>

		</div>
	@endForEach
	</div>
@else
<hr class="dashed-hr">
<h3>NO FINDINGS</h3>
@endIf