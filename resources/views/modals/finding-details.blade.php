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
	<strong>
		<span> F|N #{{ $f->id }}: AUDIT: {{ $f->audit_id }}, <small style="text-transform: uppercase;">@if($f->project->address){{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }} | {{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}@endif </small>

		</span>
	</strong>
	<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
	<div class="alert alert-danger uk-text-danger" style="display:none"></div>
	<div class="uk-width-1-1 uk-margin-remove">
		<h2 style="page-break-inside: avoid; break-inside: avoid;">
			<i class="{{ $f->icon() }}"></i> {{ $f->amenity->amenity_description }}  {{ $f->amenity_index ?? '' }}
			@if($auditor_access)
			{{-- @if($f->auditor_approved_resolution)
			<span id="finding-resolve-button" >
				<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-right;title:RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }};"><span class="a-circle-checked"> </span> RESOLVED
				</button>
			</span>
			@else
			<span id="finding-resolve-button" >
				<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFindingAS({{ $f->id }})"><span class="a-circle"></span> RESOLVE
				</button>
			</span>
			@endif --}}
			<div uk-grid class="uk-text-small">
				<div class="uk-width-1-5">
					@if($f->auditor_approved_resolution == 1)
					<span id="inspec-tools-finding-resolve-{{ $f->id }}">
						<button class="uk-button uk-link uk-margin-small-left uk-width-1-1" uk-tooltip="pos:top-right;title:DATE" onclick="resolveFinding({{ $f->id }})"><i class="a-circle-cross"></i>&nbsp; DATE
						</button>
					</span>
					@else
					<span id="inspec-tools-finding-resolve-{{ $f->id }}">
						<button class="uk-button uk-link uk-margin-small-left uk-width-1-1"> RESOLVED AT:</button>
					</span>
					@endif
				</div>
				<div class="uk-width-1-3">
					<input id="resolved-date-finding-{{$f->id}}" class="uk-input " readonly type="text" placeholder="DATE" onchange="resolveFinding({{ $f->id }},$(this).val());"  >
					@push('flatPickers')
					$('#resolved-date-finding-{{$f->id}}').flatpickr('{dateFormat: "m-d-Y"}');
					@if(null !== $f->auditor_last_approved_resolution_at)
					$('#resolved-date-finding-{{$f->id}}').val('{{date('m-d-Y',strtotime($f->auditor_last_approved_resolution_at))}}');
					@endIf
					@endpush
				</div>
				<span id="resolved-text-{{ $f->id }}" class="uk-text-danger attention" style="font-size: 15px"></span>
			</div>


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
<script type="text/javascript">
	@stack('flatPickers')
	function resolveFinding(findingid, dateResolved){
		var resolveFindingId = findingid;
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}',
			'date' : dateResolved
		}, function(data) {
			if(data != 0){
				console.log('Resolution saved for finding '+resolveFindingId);
				$('#inspec-tools-finding-resolve-'+resolveFindingId).html('<button class="uk-button uk-link uk-margin-small-left uk-width-1-1" onclick="resolveFinding(\''+resolveFindingId+'\');"><i class="a-circle-cross"></i>&nbsp; DATE</button>');
				$('#resolved-date-finding-'+resolveFindingId).val(data);
				//<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" onclick="resolveFinding(\''+resolveFindingId+'\',\'null\')"><span class="a-circle-cross">&nbsp;</span>CLEAR</button>
			}else{
				console.log('Resolution cleared for finding '+resolveFindingId);
				$('#inspec-tools-finding-resolve-'+resolveFindingId).html('<button class="uk-button uk-link uk-margin-small-left uk-width-1-1"> RESOLVED AT:</button>');
				$('#resolved-date-finding-'+resolveFindingId).val('');
			}
			$('#resolved-text-'+resolveFindingId).html('<p>Don\'t Forget! You will need to refresh the document\'s tab for these changes to appear on the document.</p>');
		});
	}
	function cancelFinding(findingid){
		UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>Cancel Finding #'+findingid+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to cancel this finding? All its comments/photos/documents/followups will remain and the cancelled finding will be displayed at the bottom of the list.</h3><h3>NOTE: Cancelled findings will not be displayed on a report. If you have cancelled a finding that was being displayed on a report, you will need to refresh that reports content for the change to be reflected.</h3></div>', {stack:2}).then(function() {
			$.post('/findings/'+findingid+'/cancel', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data==0){
					UIkit.modal.alert(data,{stack: true});
				} else {
					UIkit.notification('<span uk-icon="icon: check"></span> Finding Canceled', {pos:'top-right', timeout:1000, status:'success'});
					$('#finding-modal-audit-stream-refresh').trigger('click');
					$('#cancelled-finding-'+findingid).css("text-decoration","line-through");
				}
			} );


		}, function () {
			console.log('Rejected.')
		});
	}

</script>

