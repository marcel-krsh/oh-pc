<div id="modal-project-contact" class="uk-padding-remove uk-margin-bottom">
	<div uk-grid>
		<div class="uk-width-1-1 ">
			<h3  uk-tooltip="title:">{{$project->project_name}}<br /><small>Project {{$project->project_number}} @if($project->currentAudit())| Current Audit {{$project->currentAudit()->id}}@endif</small></h3>
		</div>

		<div id="project-details-stats" class="uk-width-1-1 " style="margin-top:20px;">
			
			<ul class="leaders" style="margin-right:30px;">
				<li><span>Total Buildings</span> <span>{{$project->stats_total_buildings()}}</span></li>
				<li><span class="indented">Total Building Common Areas</span> <span></span></li>
				<li><span class="indented">Total Building Systems</span> <span></span></li>
				<li><span class="indented">Total Building Exteriors</span> <span></span></li>
				<li><span>Total Project Common Areas</span> <span></span></li>
				<li><span>Total Units</span> <span>{{$project->stats_total_units()}}</span></li>
				<li><span class="indented">• Market Rate</span> <span></span></li>
				<li><span class="indented">• Subsidized</span> <span></span></li>
				<li><span>Total Programs</span> <span></span></li>
				@foreach($project->stat_program_units() as $program_units)
				<li><span class="indented">• {{$program_units['name']}}</span> <span>{{$program_units['units']}}</span></li>
				@endforeach
			</ul>
		</div>
		
		<div class="uk-width-1-1 ">
			<div uk-grid>
				<div class="uk-width-1-1 uk-padding-remove">
					Last Audit Completed: {{$project->lastAuditCompleted()}}<br />
					Next Audit Due By: {{$project->nextDueDate()}}<br />
					Current Project Score : N/A
				</div>
				<div class="uk-width-1-2 uk-padding-remove">
					@if($project->owner())
					<h5 class="uk-margin-remove"><strong>OWNER: {{$project->owner()['organization']}}</strong></h5>
					<div class="address">
						<i class="a-avatar"></i> {{$project->owner()['name']}}<br />
						<i class="a-phone-5"></i> {{$project->owner()['phone']}} @if($project->owner()['fax'] != '')<i class="a-fax-2" style="margin-left:10px"></i> {{$project->owner()['fax']}} @endif<br />
						<i class="a-mail-send"></i> {{$project->owner()['email']}}<br />
						@if($project->owner()['address'])<i class="a-mailbox"></i> {{$project->owner()['address']}} @endif
					</div>
					@endif
				</div>

				<div class="uk-width-1-2">
					@if($project->pm())
					<h5 class="uk-margin-remove"><strong>Managed By: {{$project->pm()['organization']}}</strong></h5>
					<div class="address">
						<i class="a-avatar"></i> {{$project->pm()['name']}}<br />
						<i class="a-phone-5"></i> {{$project->pm()['phone']}} @if($project->pm()['fax'] != '')<i class="a-fax-2" style="margin-left:10px"></i> {{$project->pm()['fax']}} @endif<br />
						<i class="a-mail-send"></i> {{$project->pm()['email']}}<br />
						@if($project->pm()['address'])<i class="a-mailbox"></i> {{$project->pm()['address']}} @endif
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>