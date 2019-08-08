	<style type="text/css">
		pre {
			font-size: 12pt;
			font-family: sans-serif;
			background-color: none;
			border: none;
		}
	</style>
	@php 
		$findingHeader = "";
		
	@endphp
@forEach($findings as $f)
		
		@if(!is_null($f->building_id))
			
			@if ($findingHeader !== $f->building->building_name)
				

				@php $findingHeader = $f->building->building_name; $columnCount = 1; $findingsRun = 1; @endphp
				<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast" style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; @if($oneColumn) margin-bottom: 0px !important; @endIf">
					@if(!$print)<h3 class="uk-margin-remove">BUILDING FINDINGS FOR BIN: {{$f->building->building_name}}</h3>@else <small>BUILDING FINDINGS FOR BIN: {{$f->building->building_name}}</small> @endIf
					@if(!is_null($f->building->address))
						<small style="text-transform: uppercase;">{{$f->building->address->line_1}} {{$f->building->address->line_2}} | 
						{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}</small>
					@endIf
					
				</div> 
			@endif
			

		@elseIf(!is_null($f->unit_id))
			@if ($findingHeader !== $f->unit->unit_name)
				@php $findingHeader = $f->unit->unit_name; $columnCount = 1; $findingsRun = 1; @endphp
				<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast"  style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; @if($oneColumn) margin-bottom: 0px !important; @endIf">
					@if(!$print)
						<h3 class="uk-margin-remove">UNIT FINDINGS FOR UNIT: {{$f->unit->unit_name}}</h3> 
							@if(!is_null($f->unit->building_id))IN BIN: {{$f->unit->building->building_name}} <br /> 
							@endIf 
					@else 	<small>UNIT FINDINGS FOR UNIT: {{$f->unit->unit_name}} 
						@if(!is_null($f->unit->building_id))IN BIN: {{$f->unit->building->building_name}} 
						@endIf
							</small>
					@endIf 
						@if(!is_null($f->unit->building->address))
							<small style="text-transform: uppercase;">{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}} | 
							{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}</small>
						@endIf
					
					
				</div> 
			@endif
			
		@else
			@if ($findingHeader !== $f->project->project_name)
				@php $findingHeader = $f->project->project_name; $columnCount = 1; $findingsRun = 1; @endphp
				<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast"  style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; @if($oneColumn) margin-bottom: 0px !important; @endIf">
					<h3 class="uk-margin-remove">SITE FINDINGS FOR: {{$f->project->project_name}}</h3>
					@if($f->project->address)
						<small style="text-transform: uppercase;"> {{$f->project->address->line_1}} {{$f->project->address->line_2}} | 
						{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}} </small>
					@endIf
					@if($print)<hr class="dashed-hr uk-margin-bottom"> @endif
				</div> 
			@endif
			
		@endIf
		
	<div id="cancelled-finding-{{$f->id}}" class="@if($print || $oneColumn) uk-width-1-1 @else uk-width-1-3 @endIf crr-blocks @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group" style=" @if(!$print && !$oneColumn) @if($columnCount < 3 && count($findings) > $columnCount && count($findings) > $findingsRun) border-right:1px dotted #3c3c3c; @endIf @elseIf($oneColumn) border: 1px solid; margin-top:0px; margin-bottom:0px; @endIf @if(!$print) padding-top:12px; padding-bottom: 18px; @else margin-top:11px !important;  @endIf page-break-inside: avoid; break-inside: avoid;">

		<div style="break-inside:avoid" @if($print || $oneColumn) uk-grid @endIf>
			<div class="inspec-tools-tab-finding-top-actions @if($print || $oneColumn) uk-width-1-5 @endIf" style="z-index:10; break-inside: avoid; page-break-inside: avoid;">
				@can('access_auditor') @if(!$print)
				<a onclick="dynamicModalLoad('edit/finding/{{$f->id}}',0,0,0,2)" class="uk-mute-link">
					<i class="a-pencil"></i>@endIf
					@endCan
					<strong class="cancelled-{{$f->id}}">F|N #{{$f->id}}</strong>@can('access_auditor') @if(!$print)
				</a> @endIf @endCan
				@if(!$print)
				@if($oneColumn)
					</div>
					<div class="uk-width-4-5">
				@endIf
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
			@if(!$print) </div> @endIf
			 <hr @if($oneColumn) class="uk-width-1-1 uk-margin-small-top" @endIf />
			@if(!is_null($f->building_id))
			{{-- <strong>{{$f->building->building_name}}</strong> <br /> --}}
			

			@elseIf(!is_null($f->unit_id))
			{{-- {{$f->unit->building->building_name}} <br /> --}}
			@if(!is_null($f->unit->building->address))
			{{-- {{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}}<br />
			{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}} --}}
			@endIf
			{{-- <br /><strong>Unit {{$f->unit->unit_name}}</strong> --}}
			@else
			{{-- <strong>Site Finding</strong><br />
			@if($f->project->address)
				{{$f->project->address->line_1}} {{$f->project->address->line_2}}<br />
				{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}<br /><br />
			@endIf --}}

			@endIf

			@can('access_auditor')
			@if(!$print)
				<!-- LINE 77 -->
				<div class="inspec-tools-tab-finding-actions  @if($oneColumn) uk-width-1-5 @endIf uk-margin-small-top " @if(!$oneColumn) uk-grid @endIf>

					
					
					@if(!$f->cancelled_at)
						<div id="inspec-tools-finding-resolve-{{ $f->id }}" class="@if($oneColumn)  @else uk-width-1-2 uk-margin-remove @endIf">
							
								@if($f->auditor_approved_resolution == 1)

								<button class="uk-button uk-link uk-margin-small-left " style="width: 100%;"  onclick="resolveFinding({{ $f->id }})"><span class="a-circle-cross"></span> DATE</button>
							@else
								<span style="position: relative; top: 9px;">RESOLVED AT:</span>
							@endif
						</div>
						<div  class="@if($oneColumn) uk-margin-top @else uk-width-1-2 uk-margin-remove @endIf">
							<input id="resolved-date-finding-{{$f->id}}" class="uk-input " style="width:100%;" readonly type="text" placeholder="DATE" onchange="resolveFinding({{ $f->id }},$(this).val());"  >
							
							@push('flatPickers')
								$('#resolved-date-finding-{{$f->id}}').flatpickr('{dateFormat: "m-d-Y"}');
								@if(null !== $f->auditor_last_approved_resolution_at)
									$('#resolved-date-finding-{{$f->id}}').val('{{date('m-d-Y',strtotime($f->auditor_last_approved_resolution_at))}}'); 
								@endIf
							@endpush
							
							

						  {{-- @push('flatPickers')
    
						  		///////////////////////////////////////////////////////////////////////////////////////////////////
						  		flatpickr flatpickr-input

								flatpickr("#resolved-date-finding-{{$f->id}}", {
									
									altFormat: "F j, Y G:i K",
									dateFormat: "F j, Y G:i K",
									enableTime: true,
									"locale": {
							        "firstDayOfWeek": 1 // start week on Monday
							      },
							      onClose: function(selectedDates, dateStr, instance){
							      	//var setDefaultTo = instance.parseDate(dateStr)
							      	//alert(setDefaultTo);
							      	//resolveFinding({{ $f->id }},dateStr);
							      	
							      	alert(selectedDates+' '+dateStr+' '+instance);
							      	//loadTab('dashboard/calls?order_by=date&dates='+encodeURIComponent(dateStr),'1','','','',1);
							      }
							    });

							    
						  @endpush --}}

						</div>
						
					@endif
					@if($oneColumn)
								<hr class="uk-margin-top dashed-hr uk-margin-bottom">
								<div class="uk-width-1-1 uk-margin-top">
					@else
								<hr class="uk-margin-top dashed-hr uk-margin-bottom uk-width-1-1">
					@endIf

					@if($f->cancelled_at)
						<button class="uk-button uk-link uk-width-1-1 uk-margin-bottom"  onclick="restoreFinding({{ $f->id }})"><i class="a-trash-3"></i> RESTORE FINDING</button>
					@else
						<button class="uk-button uk-link uk-width-1-1 uk-margin-bottom"  onclick="cancelFinding({{ $f->id }})"><i class="a-trash-3"></i> CANCEL FINDING</button>
					@endif
					@if($oneColumn)
								</div>
					@endIf

				</div>
			
			@else
				

					@if($f->auditor_approved_resolution == 1)
						<p>RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }}</p>
					@else
						<strong><p class="attention" style="color:red">UNCORRECTED</p></strong>
					@endif

			<!-- LINE 123 -->
			@endif
		
		@else
			@if($f->auditor_approved_resolution == 1)
			<!-- LINE 127 -->
				<p>RESOLVED ON {{ strtoupper(formatDate($f->auditor_last_approved_resolution_at)) }}</p>
			@else
						<strong><p class="attention" style="color:red">UNCORRECTED</p></strong>
			@endIf
		@endcan
		@if(!$print && !$oneColumn) 
			</div> 
		@elseif($print) 
			</div> <div class="uk-width-4-5" style="page-break-inside: avoid; break-inside: avoid;"> 
		@elseif($oneColumn) 
			<div class="uk-width-4-5" style="page-break-inside: avoid; break-inside: avoid; margin-top: 11px;"> 
		@endIf

		
			

		
		<!-- LINE 131 -->
		@if($print)<h5 style="page-break-inside: avoid; break-inside: avoid;"> @else
			<h2 style="page-break-inside: avoid; break-inside: avoid;">
		@endIf
			@if($f->finding_type->type == 'nlt')
			<i class="a-booboo"></i>
			@endIf
			@if($f->finding_type->type == 'lt')
			<i class="a-skull"></i>
			@endIf
			@if($f->finding_type->type == 'file')
			<i class="a-folder"></i>
			@endIf
			{{$f->amenity->amenity_description}}  {{ $f->amenity_index ?? '' }}
		 @if($print) | @else </h2> @endIf
		@if(!$print)
			<strong style="page-break-inside: avoid; break-inside: avoid;">VIOLATION CODE: <a href="/codes?code={{$f->finding_type_id}}" target="code_reference">OH.{{strtoupper($f->finding_type->type)}}.{{$f->finding_type_id}} @if($f->level) LEVEL {{$f->level}} @endIf</a>:<br /> {{$f->finding_type->name}}</strong><br>
			@if($f->level == '1')
				@if(null == $f->finding_type->one_description)
					@can('access_auditor')
						<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
					@endCan
				@else
				<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->one_description}}</span>
				@endIf
			@endIf
			@if($f->level == '2')
			@if(null == $f->finding_type->one_description)
					@can('access_auditor')
						<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
					@endCan
				@else
					<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->two_description}}</span>
				@endIf
			@endIf
			@if($f->level == '3')
				@if(null == $f->finding_type->one_description)
					@can('access_auditor')
						<span style="color:red" class="attention">UNDEFINED LEVEL SELECTED - PLEASE SELECT A DEFINED LEVEL</span>
					@endCan
				@else
					<span style="page-break-inside: avoid; break-inside: avoid;">{{$f->finding_type->three_description}}</span>
				@endIf
			@endIf
			@if((is_null($f->level) || $f->level == 0) && $f->finding_type->type !== 'file')
			<span style="color:red" class="attention">!!LEVEL NOT SET!!</span>
			@endIf
		@else
			VIOLATION CODE: <a href="/codes?code={{$f->finding_type_id}}" target="code_reference">OH.{{strtoupper($f->finding_type->type)}}.{{$f->finding_type_id}}</a> </h5>
		@endIf
		@if(!is_null($f->comments))
		@forEach($f->comments as $c)
		{!! $loop->first ? '<hr>': '' !!}

		@if(is_null($c->deleted_at))
		@if($c->hide_on_reports != 1)
		@can('access_auditor')@if(!$print)<span style="page-break-inside: avoid; break-inside: avoid;"><i class="a-pencil use-hand-cursor" onclick="addChildItem({{ $c->id }}, 'comment-edit', 'comment')"></i>@endif @endcan<i class="a-comment"></i> : {{nl2br($c->comment)}}</span>
		{!! !$loop->last ?  '<hr class="dashed-hr uk-margin-bottom">' : ''!!}
		@endIf
		@endif
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
		@if(count($piecePrograms)>0 && Auth::user()->can('access_auditor'))
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

		@if($print)
				</div>
			</div>
		@endIf

		@if($oneColumn)
			</div>
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

		{{-- Communications --}}
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
								<span  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }} doc-span-{{ $document->id }}">
								</span>
								<span style="float: left;" id="sent-id-{{ $document->id }}category-id-1-not-received-icon-{{ $f->id }}" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }} doc-span-check-{{ $document->id }}">
								</span>
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
		{{-- End communications --}}

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
		{{-- End documents --}}
		@endif 

		@if($oneColumn)
			</div>
		@endIf
		
		@if($print )<hr class="dashed-hr uk-width-1-1" style="margin-top: 0px !important;"> @endIf
	</div>
		<?php
				// using column count to put in center lines rather than rely on css which breaks.
		
			$columnCount++;
			$findingsRun++;
		
		if($columnCount > 3){
			$columnCount = 1;
			if(!$print && !$oneColumn){
			?>
				<hr class="dashed-hr uk-margin-bottom uk-width-1-1 @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group" >
			<?php  
			}
			
		}
		?>
		

	@endForEach
