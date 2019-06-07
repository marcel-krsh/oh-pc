<?php $findings = $bladeData;
session(['selected_findings' => $findings]);
?>
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
	<div id="cancelled-finding-{{$f->id}}" class="uk-width-1-3 crr-blocks" style="border-bottom:1px dotted #3c3c3c; @if($columnCount < 3) border-right:1px dotted #3c3c3c; @endIf padding-top:12px; padding-bottom: 18px; page-break-inside: avoid;">
		<?php
				// using column count to put in center lines rather than rely on css which breaks.
		$columnCount++;
		if($columnCount > 3){
			$columnCount = 1;
		}
		?>
		<div style="min-height: 105px;">
			<div class="inspec-tools-tab-finding-top-actions" style="z-index:10">
				@can('access_auditor') @if(!$print)
				<a onclick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)" class="uk-mute-link">
					<i class="a-pencil"></i>@endIf
					@endCan
					<strong class="cancelled-{{$f->id}}">Finding # {{$f->id}}</strong>@can('access_auditor') @if(!$print)
				</a> @endIf @endCan
				@if(!$print)
				<span class="use-hand-cursor" style="float: right;" aria-expanded="false"><i class="a-circle-plus  "></i> ADD RESPONSE</span>
				<div uk-drop="mode: click; pos: bottom-right" style="min-width: 315px; background-color: #ffffff;  ">
					<div class="uk-card uk-card-body uk-card-default uk-card-small">
						<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
							@can('access_auditor')
							<div class="icon-circle use-hand-cursor" onclick="addChildItem({{ $f->id }}, 'followup')"><i class="a-bell-plus"></i></div>
							<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $f->id }}, 'comment')"><i class="a-comment-plus"></i></div>
							@endCan
							<div class="icon-circle use-hand-cursor" onclick="dynamicModalLoad('new-outbound-email-entry/{{$report->project_id}}/{{$report->audit_id}}/{{$report->id}}/{{$f->id}}/{{ $f->id }}')" ><i class="a-envelope-4"></i>
							</div>
							@can('access_auditor')
							<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $f->id }}, 'document')"><i class="a-file-plus"></i></div>
							@endCan
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

		@can('access_auditor')
		@if(!$print)
		<div class="inspec-tools-tab-finding-actions  uk-margin-small-top ">

			@if($f->cancelled_at)
			<button class="uk-button uk-link uk-width-1-2" style="width: 45%;" onclick="restoreFinding({{ $f->id }})"><i class="a-trash-3"></i> RESTORE</button>
			@else
			<button class="uk-button uk-link uk-width-1-2" style="width: 45%;" onclick="cancelFinding({{ $f->id }})"><i class="a-trash-3"></i> CANCEL</button>
			@endif

			<span id="inspec-tools-finding-resolve-{{ $f->id }}" >
				@can('access_auditor')
				@if(!$f->cancelled_at)
				@if($f->auditor_approved_resolution != 1)
				<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" style="width: 45%;" onclick="resolveFinding({{ $f->id }})"><span class="a-circle"></span> RESOLVE</button>
				@else
				<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" style="width: 45%;" uk-tooltip="pos:top-left;title:RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }};" onclick="resolveFinding({{ $f->id }})"><span class="a-circle-checked"></span> RESOLVED</button>
				@endif
				@endif
				@else
				@if($f->auditor_approved_resolution == 1)
				<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" style="width: 45%;" uk-tooltip="pos:top-left;title:RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }};"><span class="a-circle-checked"></span> RESOLVED</button>
				@endif
				@endcan
			</span>
		</div>
		@endif
		@endcan





		<h2>@if($f->finding_type->type == 'nlt')
			<i class="a-booboo"></i>
			@endIf
			@if($f->finding_type->type == 'lt')
			<i class="a-skull"></i>
			@endIf
			@if($f->finding_type->type == 'file')
			<i class="a-folder"></i>
			@endIf  {{$f->amenity->amenity_description}}
		</h2>
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
		{!! $loop->first ? '<hr>': '' !!}
		@if(is_null($c->deleted_at) && !(isset($c->hide_on_reports) && $c->hide_on_reports == 1))
		@can('access_auditor')@if(!$print)<i class="a-pencil use-hand-cursor" onclick="addChildItem({{ $c->id }}, 'comment-edit', 'comment')"></i>@endif @endcan<i class="a-comment"></i> : {{$c->comment}}
		{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : ''!!}
		@endIf
		@endForEach
		@endIf

		{{-- Photos --}}
		@if(property_exists($f,'photos') && !is_null($f->photos))
		@forEach($f->photos as $p)
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
		@endIf
		@endForEach
		@endIf

		<hr class="dashed-hr uk-margin-bottom">
		@if($f->amenity_inspection)
		<?php $piecePrograms = collect($f->amenity_inspection->unit_programs)->where('audit_id',$report->audit_id); ?>
		@if(count($piecePrograms)>0)
		<div style="min-height: 80px;">
			<span class="uk-margin-bottom"><strong >PROGRAMS:</strong></span>
			<ul > @forEach($piecePrograms as $p)
				<li>@if(!is_null($p->is_substitute))SUBSTITUTED FOR:@endIf
					{{$p->program->program_name}}
				</li>
				@endForEach
			</ul>
		</div>
		@endIf
		@endIf

		@if(!$print)
		{{-- Communications section --}}
		{{-- 38:40
		* Envolope icon
		* Datetime
		* Person
		* Subject,
		* Body
		* Attachment
		*
		--}}
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
		{!! $loop->first ? '<hr>': '' !!}
		<strong class="a-envelope-4"></strong> : {{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }} <br>
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
					@if(count($message->message_recipients))@foreach ($message->message_recipients as $recipient)@if($recipient->id != $current_user->id && $message->owner->id != $recipient->id && $recipient->name != ''){{ $recipient->full_name() }}{{ !$loop->last ? ', ': '' }}@elseif($recipient->id == $current_user->id) Me{{ !$loop->last ? ', ': '' }} @endif @endforeach @endif
				</label>
			</li>
			<li>
				<strong>
					<label style="display: block;" for="message-sub-{{ $message->id }}">
						{{ $message->subject }}
					</label>
				</strong>
			</li>
			<li>
				<label style="display: block;" for="message-msg-{{ $message->id }}">
					{{ $message->message }}
				</label>
			</li>
			@if(count($message->local_documents) > 0)
			<li class="uk-margin-small-top">
				{{-- <strong class="uk-text-small" style="float: left; margin-top: 2px;">DOC:&nbsp;</strong> --}}
				{{-- <label style="display: block; margin-left: 28px;" for="message-doc-{{ $message->id }}"> --}}
					@foreach($message->local_documents as $document)
					<span class="finding-documents-{{ $document->id }}">
						@php
						$document_category = $document->assigned_categories->first();
						@endphp
						<li class="doc-{{ $document->id }} {{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
							<a id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}-{{ $f->id }}" class="">
								<span  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }} doc-span-{{ $document->id }}"></span>
								<span style="float: left;" id="sent-id-{{ $document->id }}category-id-1-not-received-icon-{{ $f->id }}" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }} doc-span-check-{{ $document->id }}"></span>
								<span style="display: block; margin-left: 30px"></span>
							</a>
							@can('access_auditor')
							<div uk-dropdown="mode: click" id="#sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}">
								<ul class="uk-nav uk-nav-dropdown">
									<li>
										<a onclick="markApproved({{ $document->id }},{{ $document_category->id }});">
											Mark as approved
										</a>
									</li>
									<li>
										<a onclick="markNotApproved({{ $document->id }},{{ $document_category->id }});">
											Mark as declined
										</a>
									</li>
									<li>
										<a onclick="markUnreviewed({{ $document->id }},{{ $document_category->id }});">
											Clear review status
										</a>
									</li>
								</ul>
							</div>
							@endCan
							<label style="display: block; margin-left: 15px;" for="documents-{{ $document->id }}">
								<a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file:<br />{{ ucwords(strtolower($document->filename)) }} <br> {{ $document->comment }}">
									<i class="a-paperclip-2"></i> {{-- {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} --}}{{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }}
								</a>
								<br>
							</label>
						</li>
					</span>
					{{-- <a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file:<br />{{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}">
						<i class="a-paperclip-2"></i> {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}
					</a> --}}
					@endforeach
				{{-- </label> --}}
			</li>
			@endif
		</span>
		{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : ''!!}
		@endforeach
		@endIf

		{{-- Documents --}}
		@if(count($documents))
		@foreach($documents as $document)
		{!! $loop->first ? '<span class="uk-margin-top"><hr></span>':'' !!}
		<span class="finding-documents-{{ $document->id }}">
			@php
			$document_category = $document->assigned_categories->first();
			@endphp
			<li class="doc-{{ $document->id }} {{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
				<a id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}-{{ $f->id }}" class="">
					<span  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }} doc-span-{{ $document->id }}"></span>
					<span style="float: left;" id="sent-id-{{ $document->id }}category-id-1-not-received-icon-{{ $f->id }}" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }} doc-span-check-{{ $document->id }}"></span>
					<span style="display: block; margin-left: 30px"></span>
				</a>
				@can('access_auditor')
				<div uk-dropdown="mode: click" id="#sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}">
					<ul class="uk-nav uk-nav-dropdown">
						<li>
							<a onclick="markApproved({{ $document->id }},{{ $document_category->id }});">
								Mark as approved
							</a>
						</li>
						<li>
							<a onclick="markNotApproved({{ $document->id }},{{ $document_category->id }});">
								Mark as declined
							</a>
						</li>
						<li>
							<a onclick="markUnreviewed({{ $document->id }},{{ $document_category->id }});">
								Clear review status
							</a>
						</li>
					</ul>
				</div>
				@endCan
				<label style="display: block; margin-left: 15px;" for="documents-{{ $document->id }}">
					<a href="{{ URL::route('document.local-download', $document->id) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-text-left uk-margin-small-bottom" uk-tooltip title="Download file:<br />{{ ucwords(strtolower($document->filename)) }} <br> {{ $document->comment }}">
						<i class="a-paperclip-2"></i> {{-- {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} --}}{{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }}
					</a>
					<br>
				</label>
			</li>
		</span>
		@endforeach
		@endIf

		@endif



	</div>
	@endForEach
</div>

<script>
	@can('access_auditor')

	function resolveFinding(findingid){
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data != 0){
				$('#inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" uk-tooltip="pos:top-left;title:RESOLVED ON '+data.toUpperCase()+';" onclick="resolveFinding('+findingid+')"><span class="a-circle-checked">&nbsp; </span>RESOLVED</button>');
			}else{
				$('#inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button uk-link uk-margin-small-left uk-width-1-2" onclick="resolveFinding('+findingid+')"><span class="a-circle">&nbsp; </span>RESOLVE</button>');
			}
		});
	}

	function cancelFinding(findingid){
		UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>Cancel Finding #'+findingid+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to cancel this finding? All its comments/photos/documents/followups will remain and the cancelled finding will be displayed at the bottom of the list.</h3><h3>NOTE: Cancelled findings will not be displayed on a report. If you have cancelled a finding that was being displayed on a report, you will need to refresh that reports content for the change to be reflected.</h3></div>', {stack:1}).then(function() {

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
	@endCan
</script>

@else
<hr class="dashed-hr">
<h3>NO FINDINGS</h3>
@endIf
