<script>
	resizeModal(65);
</script>
<style type="text/css">
	li{
		list-style-type:none;
	}
	.photo-gallery {
		width: 30%;
	}
</style>
@php
$f = $finding;
@endphp
<div class="modal-report-dates">
	<h2 class="uk-text-uppercase uk-text-emphasis">{{ $finding_type }} {{ $f->project->project_name }}</h2>
	<strong><span> F|N #{{ $f->id }}: AUDIT: {{ $f->audit_id }}, <small style="text-transform: uppercase;">@if($f->project->address){{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }} | {{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}@endif
	</small></span>
</strong>
<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
<div class="alert alert-danger uk-text-danger" style="display:none"></div>
<div class="uk-width-1-1 uk-margin-remove">
	<h2 style="page-break-inside: avoid; break-inside: avoid;">
		<i class="{{ $f->icon() }}"></i> {{ $f->amenity->amenity_description }}  {{ $f->amenity_index ?? '' }}
		@if($auditor_access)
		<span>
			@if($f->auditor_approved_resolution)
			<span id="finding-resolve-button" >
				<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-right;title:RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }};"><span class="a-circle-checked"> </span> RESOLVED
				</button>
			</span>
			@else
			<span id="finding-resolve-button" >
				<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFindingAS({{ $f->id }})"><span class="a-circle"></span> RESOLVE
				</button>
			</span>
			@endif
		</span>
		@endif
	</h2>
	@if(!is_null($f->date_of_finding))
	<small>{{ date('l F jS, Y',strtotime($f->date_of_finding)) }}</small><br />
	@endif
	<strong style="page-break-inside: avoid; break-inside: avoid; ">VIOLATION CODE: <a href="/codes?code=3" target="code_reference">OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:{{ $f->finding_type->name }}</a>:
	</strong>

	@if($f->level == '1')
	@if(null == $f->finding_type->one_description)
	@if($auditor_access)
	<div>
		<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
	</div>
	@endif
	@else
	<div>
		<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->one_description}}</span>
	</div>
	@endif
	@endif
	@if($f->level == '2')
	@if(null == $f->finding_type->two_description)
	@if($auditor_access)
	<div>
		<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
	</div>
	@endif
	@else
	<div>
		<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->two_description}}</span>
	</div>
	@endif
	@endif
	@if($f->level == '3')
	@if(null == $f->finding_type->three_description)
	@if($auditor_access)
	<div>
		<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
	</div>
	@endif
	@else
	<div>
		<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->three_description}}</span>
	</div>
	@endif
	@endif

	@if( (is_null($f->level) || $f->level == 0) && $f->finding_type->type !== 'file')
	<div style="margin-left:24px; margin-top:7px;">
		<span style="color:red" class="attention">!!LEVEL NOT SET!!</span>
	</div>
	@endif

	@if(!is_null($f->comments))
	@foreach($f->comments as $c)
	{!! $loop->first ? '<hr>': '' !!}
	@if(is_null($c->deleted_at))
	@if($c->hide_on_reports != 1)
	<span style="page-break-inside: avoid; break-inside: avoid; color: black"><i class="a-person-chat-2" ></i></span> <div style="display:inline-table;margin-left: 2px;color:black;line-height: 23px;">{!!  nl2br($c->comment) !!}</div>
	{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : ''!!}
	@endif
	@endif
	@endforeach
	@endif

	{{-- Photos --}}
	@foreach($photos as $p)
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

	<hr class="dashed-hr uk-margin-bottom">
	@if($f->amenity_inspection)
	<?php $piecePrograms = collect($f->amenity_inspection->unit_programs)->where('audit_id', $f->audit_id);?>
	@if(count($piecePrograms)>0 && $auditor_access)
	<div style="min-height: 80px;">
		<span class="uk-margin-bottom"><strong >PROGRAMS:</strong></span>
		<ul > @foreach($piecePrograms as $p)
			<li>@if(!is_null($p->is_substitute))SUBSTITUTED FOR:@endif
				{{$p->program->program_name}}
			</li>
			@endforeach
		</ul>
	</div>
	@endif
	@endif

	@if(count($communications) > 0)
	@foreach($communications as $message)
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

	{!! $loop->first ? '<hr class="uk-width-1-1 uk-margin-bottom">': '' !!}
	<strong class="a-envelope-4 remove-action-{{ $message->id }} {{ ($recipient_yes && !$message_seen) ? 'attention ok-actionable' : '' }}"></strong> : {{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }} <br>
	<span {{-- style="margin-left: 20px" --}}>
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
			@if(count($message->local_documents) > 0)
			<li class="uk-margin-top">
				@foreach($message->local_documents as $document)
				<span class="finding-documents-{{ $document->id }}">
					@php
					$document_category = $document->assigned_categories->first();
					@endphp
					<li class="doc-{{ $document->id }} {{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
						<label style="display: block;" for="documents-{{ $document->id }}">
							<a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file:<br />{{ ucwords(strtolower($document->filename)) }} <br> {{ $document->comment }}">
								<i class="a-paperclip-2"></i> {{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }}
							</a>
							<br>
						</label>
					</li>
				</span>
				@endforeach
			</li>
		</span>
		@endif
	</span>
	{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : ''!!}
	@endforeach
	@endif
</div>

</div>
