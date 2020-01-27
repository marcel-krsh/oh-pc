{{--
 AUDIT: XXXXX

BIN: XX-XXXXXX : UNIT: XXXXXXXXX
Addresss Dr, City, ZIP345
FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
FN: XXXX : OH.NLT.187 Level 1:
Description only - no comment
what I can't illustrate here is the icon to precede each finding with the open circle, circle-x, or circle check on its resolution

then you can pull up the full details - including the resolve button if it is not resolved

so they can mark it resolved

to keep things consistent- it would be good to make the finding count flash (attention) if it is attached to an unresolved finding.

 --}}



@php
$findingHeader = "";
$f = [];
// $audits_ids = ($document->audits->pluck('id')->toArray());
// $document_finding_audit_ids = $document_findings->pluck('audit_id')->toArray();
// $all_ids = array_merge($audits_ids, $document_finding_audit_ids, [$document->audit_id]);
// $document_audits = $audits->whereIn('id', $all_ids);
// $site_findings = $document_findings->where('building_id', null)->where('unit_id', null);
// $building_findings = $document_findings->where('building_id', '<>', null)->where('unit_id', null);
// $unit_findings = $document_findings->where('building_id', null)->where('unit_id', '<>', null);
@endphp

@if(count($site_findings))
	@php
	$f = $site_findings->first();
	@endphp
	{{--  AUDIT: XXXXX --}} {{-- BIN: XX-XXXXXX  --}}
	<div class="uk-width-1-1">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> SITE: {{ $f->project->project_name }}</span></strong>
	</div>
	{{-- Addresss Dr, City, ZIP345 --}}
	@if($f->project->address)
		<div class="uk-width-1-1">
			<small style="text-transform: uppercase;">{{$f->project->address->line_1}} {{$f->project->address->line_2}} |
			{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}
			</small>
		</div>
	@endif
	{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
	FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
	<small>
	@foreach($site_findings as $fin)
	F|N #{{ $f->id }}:
	<span>OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:{{ $f->finding_type->name }}
	</span>
	@endforeach
	</small>
@endif



@if(count($building_findings))
<hr class="dashed-hr uk-margin-bottom">
	{{-- Check if there are any unit specific findings for this building --}}
	@php
	$f = $building_findings->first();
	$b_unit_findings = $unit_findings->where('unit.building_id', $f->building_id);
	if(count($b_unit_findings)) {
		$unit_findings = $unit_findings->where('unit.building_id', '<>', $f->building_id);
	}
	@endphp
	{{--  AUDIT: XXXXX --}} {{-- BIN: XX-XXXXXX  --}}
	<div class="uk-width-1-1">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> BIN: {{ $f->building->building_name }}</span></strong>
	</div>
	{{-- Addresss Dr, City, ZIP345 --}}
	@if(!is_null($f->building->address))
		<div class="uk-width-1-1">
			<small style="text-transform: uppercase;">{{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }} |
				{{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }}
			</small>
		</div>
	@endif
	{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
	FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
	<small>
	@foreach($building_findings as $fin)
	F|N #{{ $f->id }}:
	<span>OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:{{ $f->finding_type->name }}
	</span>
	@endforeach
	</small>

	@if(count($b_unit_findings)) {
		@php
			$f = $b_unit_findings->first();
		@endphp
		{{--  AUDIT: XXXXX --}} {{-- UNIT: XX-XXXXXX  --}}
		<div class="uk-width-1-1">
			<strong><span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
		FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
		<small>
		@foreach($b_unit_findings as $fin)
		F|N #{{ $fin->id }}:
		<span>OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
		@endforeach
		</small>
	@endif
@endif



@if(count($unit_findings))
<hr class="dashed-hr uk-margin-bottom">
	@php
		$f = $unit_findings->first();
	@endphp
		{{--  AUDIT: XXXXX --}} {{-- UNIT: XX-XXXXXX  --}}
		<div class="uk-width-1-1">
			<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		{{-- Addresss Dr, City, ZIP345 --}}
		@if(!is_null($f->unit->building->address))
			<div class="uk-width-1-1">
				<small style="text-transform: uppercase;">{{ $f->unit->building->address->line_1 }} {{ $f->unit->building->address->line_2 }} |
					{{ $f->unit->building->address->city }}, {{ $f->unit->building->address->state }} {{ $f->unit->building->address->zip }}
				</small>
			</div>
		@endif
		{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
		FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
		<small>
		@foreach($unit_findings as $fin)
		<div>
		F|N #{{ $fin->id }}:
		<span>OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
		</div>
		@endforeach
		</small>
@endif



{{-- OLD ONE --}}

@php
$findingHeader = "";
$print = 0;
$oneColumn = 1;

return 12;
@endphp
@if(!is_null($f->building_id))

@if ($findingHeader !== $f->building->building_name)

@php $findingHeader = $f->building->building_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{ $f->unit_id }}-finding building-{{ $f->unit->building_id }}-finding @endif @if($f->building_id > 0) building-{{ $f->building_id }}-finding @endif @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{ $f->id }} @endif @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group" style="/*border: 1px dotted #9e9e9e; */!important;">
	<h6 class="uk-margin-remove">BUILDING FINDINGS FOR BIN: {{ $f->building->building_name }}</h6>@else <small>BUILDING FINDINGS FOR BIN: {{ $f->building->building_name }}</small>
	@if(!is_null($f->building->address))
	<small style="text-transform: uppercase;">{{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }} |
		{{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }}
	</small>
	@endif
</div>
@endif

@elseif(!is_null($f->unit_id))

@if ($findingHeader !== $f->unit->unit_name)
@php $findingHeader = $f->unit->unit_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{ $f->unit_id }}-finding building-{{ $f->unit->building_id }}-finding @endif @if($f->building_id > 0) building-{{ $f->building_id }}-finding @endif @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{ $f->id }} @endif @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group">
	<h6 class="uk-margin-remove">UNIT FINDINGS FOR UNIT: {{ $f->unit->unit_name }}</h6>
	@if(!is_null($f->unit->building_id))IN BIN: {{ $f->unit->building->building_name }} <br />		@endif
	@if($f->unit->building && !is_null($f->unit->building->address))
	<small style="text-transform: uppercase;">{{ $f->unit->building->address->line_1 }} {{ $f->unit->building->address->line_2 }} |
		{{ $f->unit->building->address->city }}, {{ $f->unit->building->address->state }} {{ $f->unit->building->address->zip }}
	</small>
	@endif

</div>
@endif

@else
@if ($findingHeader !== $f->project->project_name)
@php $findingHeader = $f->project->project_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{ $f->unit_id }}-finding building-{{ $f->unit->building_id }}-finding @endif @if($f->building_id > 0) building-{{ $f->building_id }}-finding @endif @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{ $f->id }} @endif @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group">
	<h6 class="uk-margin-remove">SITE FINDINGS FOR: {{ $f->project->project_name }}</h6>
	@if($f->project->address)
	<small style="text-transform: uppercase;"> {{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }} |
		{{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}
	</small>
	@endif
</div>
@endif

@endif

<div id="cancelled-finding-{{ $f->id }}" class="@if($print || $oneColumn) uk-width-1-1 @else uk-width-1-3 @endif crr-blocks @if($f->unit_id > 0) unit-{{ $f->unit_id }}-finding building-{{ $f->unit->building_id }}-finding @endif @if($f->building_id > 0) building-{{ $f->building_id }}-finding @endif @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{ $f->id }} @endif @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group" style=" @if(!$print && !$oneColumn) @if($columnCount < 3 && count($findings) > $columnCount && count($findings) > $findingsRun) border-right:1px dotted #3c3c3c; @endif @elseif($oneColumn && !$print) !!;margin-top:0px; margin-bottom:0px; @endif @if(!$print) padding-top:5px; padding-bottom: 10px; @else margin-top:5px !important;  @endif page-break-inside: avoid; break-inside: avoid;">
	<div style="break-inside:avoid" @if($print || $oneColumn) uk-grid @endif>
		<div class="uk-width-4-5">
		@if(!$print) </div> @endif
		<hr @if($oneColumn && !$print) class="uk-width-1-1 uk-margin-small-top" @endif />
		@if($auditor_access)
		@if(!$print)
		@else
		@if($f->auditor_approved_resolution == 1)
		<p>RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }}</p>
		@else
		<strong><p class="attention" style="color:red">UNCORRECTED</p></strong>
		@endif

		@endif
		@else
		@if($f->auditor_approved_resolution == 1)
		<!-- LINE 127 -->
		<p>RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }}</p>
		@else
		<strong><p class="attention" style="color:red">UNCORRECTED</p></strong>
		@endif
		@endif
		@if(!$print && !$oneColumn)
	</div>
	@elseif($print)
</div>

<div class="uk-width-4-5 uk-padding-remove" style="page-break-inside: avoid; break-inside: avoid;">
	@elseif($oneColumn)
	<div class="uk-width-4-5 uk-padding-remove" style="page-break-inside: avoid; break-inside: avoid; margin-top: 5px;">
		@endif

		@if($print)<h6 style="page-break-inside: avoid; break-inside: avoid;"> @else
			<h6 style="page-break-inside: avoid; break-inside: avoid;">
				@endif
				@if($f->finding_type->type == 'nlt')
				<i class="a-booboo"></i>
				@endif
				@if($f->finding_type->type == 'lt')
				<i class="a-skull"></i>
				@endif
				@if($f->finding_type->type == 'file')
				<i class="a-folder"></i>
				@endif
				{{ $f->amenity->amenity_description }}  {{ $f->amenity_index ?? '' }}
				@if($print) | @else
			</h6>
			@endif
			@if(null !== $f->date_of_finding)
			<small>{{ date('l F jS, Y',strtotime($f->date_of_finding)) }}</small><br />
			@endif
			@if(!$print || (count($f->comments)==0))
			<strong style="page-break-inside: avoid; break-inside: avoid; ">VIOLATION CODE: OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:</strong><br/><div style="margin-left:24px; margin-top:7px;"> {{ $f->finding_type->name }}:</div>
			@if($f->level == '1')
			@if(null == $f->finding_type->one_description)
			@if($auditor_access)
			<div style="margin-left:24px; margin-top:7px;">
				<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
			</div>
			@endif
			@else
			<div style="margin-left:24px; margin-top:7px;">
				<span style="page-break-inside: avoid; break-inside: avoid;">{{ $f->finding_type->one_description }}</span>
			</div>
			@endif
			@endif
			@if($f->level == '2')
			@if(null == $f->finding_type->two_description)
			@if($auditor_access)
			<div style="margin-left:24px; margin-top:7px;">
				<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
			</div>
			@endif
			@else
			<div style="margin-left:24px; margin-top:7px;">
				<span style="page-break-inside: avoid; break-inside: avoid;">{{ $f->finding_type->two_description }}</span>
			</div>
			@endif
			@endif
			@if($f->level == '3')
			@if(null == $f->finding_type->three_description)
			@if($auditor_access)
			<div style="margin-left:24px; margin-top:7px;">
				<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
			</div>
			@endif
			@else
			<div style="margin-left:24px; margin-top:7px;">
				<span style="page-break-inside: avoid; break-inside: avoid;">{{ $f->finding_type->three_description }}</span>
			</div>
			@endif
			@endif
			@if( (is_null($f->level) || $f->level == 0) && $f->finding_type->type !== 'file')
			<div style="margin-left:24px; margin-top:7px;">
				<span style="color:red" class="attention">!!LEVEL NOT SET!!</span>
			</div>
			@endif
			@else
			VIOLATION CODE: OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} </h6>
			@endif
			@if(!is_null($f->comments))
			@foreach($f->comments as $c)
			{!! $loop->first ? '<hr>': '' !!}

			@if(is_null($c->deleted_at))
			@if($c->hide_on_reports != 1)
			@if(!$print)<span style="page-break-inside: avoid; break-inside: avoid; color: black">@else <span style="page-break-inside: avoid; break-inside: avoid; color: black">  @endif <i class="a-person-chat-2" ></i></span> <div style="display:inline-table;margin-left: 2px;color:black;line-height: 23px;">{!!  nl2br($c->comment) !!}</div>
			{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : '' !!}
			@endif
			@endif
			@endforeach
			@endif

			{{-- Photos --}}
			@if(property_exists($f,'photos') && !is_null($f->photos))
			@foreach($f->photos as $p)
			@if(!$p->deleted)
			<hr class="dashed-hr uk-margin-bottom">
			<div class="photo-gallery uk-slider uk-slider-container" uk-slider="">
				<div class="uk-position-relative uk-visible-toggle uk-light">
					<ul class="uk-slider-items uk-child-width-1-1" style="transform: translateX(0px);">
						<li class="findings-item-photo-4 use-hand-cursor uk-active">
							<img src="{{ url($p->file_path) }}" alt="">
						</li>
					</ul>
				</div>
				<ul class="uk-slider-nav uk-dotnav uk-flex-center uk-hidden">
					<li uk-slider-item="0" class="uk-hidden uk-active"><a href="#"></a></li>
				</ul>
			</div>
			@endif
			@endforeach
			@endif

			<hr class="dashed-hr uk-margin-bottom">
			@if($f->amenity_inspection)
			<?php $piecePrograms = collect($f->amenity_inspection->unit_programs)->where('audit_id', $f->audit_id);?>
			@if(count($piecePrograms)>0 && $auditor_access)
			<div style="min-height: 80px;">
				<span class="uk-margin-bottom"><strong >PROGRAMS:</strong></span>
				<ul > @foreach($piecePrograms as $p)
					<li>@if(!is_null($p->is_substitute))SUBSTITUTED FOR:@endif
						{{ $p->program->program_name }}
					</li>
					@endforeach
				</ul>
			</div>
			@endif
			@endif

			@if($print)
		</div>
	</div>
	@endif

	@if($oneColumn  && !$print)
</div>
@endif

{{--
@php
$communications = App\Models\Communication::whereJsonContains('finding_ids', "$f->id")
->with('owner')
->with('recipients', 'docuware_documents', 'local_documents')
->orderBy('created_at', 'desc')
->get();
$documents = App\Models\Document::whereJsonContains('finding_ids', "$f->id")
->with('assigned_categories')
->orderBy('created_at', 'desc')
->get();
@endphp

@if(count($communications))
@foreach($communications as $message)
@if($print || $oneColumn)
<hr class="uk-width-1-1">
<div class="uk-width-1-5">
	@endif
	@php
	$recipient_yes = 0;
	$message_seen = 0;
	$is_receipient = $message->message_recipients->where('id', $current_user->id);
	if(count($is_receipient)){
		$recipient_yes = 1;
		if($is_receipient->first()->pivot->seen)
			$message_seen = 1;
	}
	@endphp
	{!! $loop->first && !$print && !$oneColumn ? '<hr class="uk-width-1-1 uk-margin-bottom">': '' !!}
	<strong class="a-envelope-4 remove-action-{{ $message->id }} {{ ($recipient_yes && !$message_seen) ? 'attention ok-actionable' : '' }}" @if($print || $oneColumn) style="margin-top: 4px !important" @endif></strong> : {{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }} @if($print || $oneColumn)</div><div class="uk-width-4-5"> @else <br> @endif
		<span>
			<li>
				<strong class="uk-text-small" style="float: left; margin-top: 2px;">From:&nbsp;</strong>
				<label style="display: block; margin-left: 28px;" for="message-{{ $message->id }}">
					@if($message->owner->id == $current_user->id)Me @else {{ $message->owner->full_name() }} @endif
				</label>
			</li>
			<li>
				<strong class="uk-text-small" style="float: left; margin-top: 2px;">To:&nbsp;</strong>
				<label style="display: block; margin-left: 28px;" for="message-{{ $message->id }}">
					@if(count($message->message_recipients))@foreach($message->message_recipients as $recipient) @if($recipient->id != $current_user->id && $message->owner->id != $recipient->id && $recipient->name != ''){{ $recipient->full_name() }}{{ !$loop->last ? ', ': '' }} @elseif($recipient->id == $current_user->id) Me{{ !$loop->last ? ', ': '' }} @endif @endforeach @endif
				</label>
			</li>
			<li>
				<strong>
					<label style="display: block;" for="message-sub-{{ $message->id }}">
						{{ $message->subject }}
					</label>
				</strong>
			</li>
			@if($recipient_yes && !$message_seen)
			<li class="ok-actionable attention use-hand-cursor hide-message-{{ $message->id }}" onclick="messageRead({{ $message->id }})">Click to open</li>
			@endif
				<span class="{{ ($recipient_yes && !$message_seen) ? 'uk-hidden' : '' }} show-message-{{ $message->id }}">
					<li>
						<label style="display: block;" for="message-msg-{{ $message->id }}">
							{{ $message->message }}
						</label>
					</li>
				</span>
			</span>
		@if($print || $oneColumn)</div> @endif
		{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : '' !!}
		@endforeach
		@endif --}}

