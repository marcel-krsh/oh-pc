<div class="@if($message->recipients->where('owner_id','<>',$current_user->id)->where('user_id',$current_user->id)->where('seen','<>',null)->count())user_comms_read @endif filter_element_project uk-width-1-1 communication-list-item @if($message->message_recipients) @foreach($message->message_recipients as $mr) staff-{{ $mr->id }} @endforeach @endif @if($message->project)program-{{ $message->project->id }}@endif  @if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0) attachment-true @endif" uk-filter="outbound-phone" id="communication-{{ $message->id }}" data-grid-prepared="true" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1; @if($message->recipients->where('user_id',$current_user->id)->where('seen',null)->count()) font-weight: bold; @endif" onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects'); ">
	<div uk-grid class="communication-summary @if($message->unseen) communication-unread @endif">
		@if($message->owner->id == $current_user->id)
		<div class="uk-width-1-5@m uk-width-1-2@s communication-item-tt-to-from uk-margin-small-bottom" >
			<div class="communication-item-date-time">
				<small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small><br>
				<span>
					{{-- onclick="dynamicModalLoad('communication/0/replies/@if($message->parent_id){{ $message->parent_id }}@else{{ $message->id }}@endif/projects')" --}}
					FROM: Me<hr class="dashed-hr uk-margin-bottom uk-width-1-1">
					@if(count($message->message_recipients))TO:
					<?php $recipients = $message->message_recipients->where('id', '<>', $current_user->id);?>
					@if(count($recipients)>0)
					@foreach($recipients as $recipient)
					@if($recipient->pivot->seen != 1)<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">@endif {{ $recipient->full_name() }}@if($recipient->pivot->seen != 1)</strong>@endif{{ !$loop->last ? ', ': '' }}
					@endforeach
					@else
					Me
					@endif
					@endif
				</span>
			</div>
			@if($message->unseen > 0)
			<div class="uk-label no-text-shadow user-badge-{{ $current_user->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen }} unread messages">{{ $message->unseen }}</div>
			@endif
		</div>
		@else
		<div class="uk-width-1-5@m uk-width-3-6@s communication-item-tt-to-from uk-margin-small-bottom"  >
			<div class="communication-item-date-time">
				<small>{{ date("m/d/y", strtotime($message->created_at)) }} {{ date('h:i a', strtotime($message->created_at)) }}</small>
			</div>
			FROM: {{ $message->owner->full_name() }}<hr class="dashed-hr uk-margin-bottom uk-width-1-1"> @if(count($message->message_recipients))TO:
			@foreach ($message->message_recipients->where('id', '<>', $message->owner_id) as $recipient)
			@if($recipient->id != $current_user->id && $message->owner != $recipient && $recipient->name != '')
			{{-- {{ dd($recipient) }} --}}
			@if($recipient->pivot->seen == null)<strong uk-tooltip title="HAS NOT READ THIS MESSAGE">@endif
				{{ $recipient->full_name() }}{{ !$loop->last ? ', ': '' }}
				@if($recipient->pivot->seen == null)</strong>@endif
				@elseif($recipient->id == $current_user->id)
				Me{{ !$loop->last ? ', ': '' }}
				@endif
				@endforeach
				@endif
				@if($message->unseen > 0)
				<div class="uk-label no-text-shadow user-badge-{{ $current_user->badge_color }}" uk-tooltip="pos:top-left;title:{{ $message->unseen }} unread messages">{{ $message->unseen }}</div>
				@endif
			</div>
			@endif
			<div class="uk-width-1-5@s communication-type-and-who uk-hidden@m uk-text-right " >
				<div class="uk-margin-right">
					@if($message->audit_id && $message->audit && $message->audit->cached_audit)
					<p style="margin-bottom:0">{{ $message->audit_id }} | {{ $message->project->project_number }} : {{ $message->project->project_name }}</p>
					<p class="uk-visible@m" style="margin-top:0" >
						<small>{{ $message->audit->cached_audit->address }},
							{{ $message->audit->cached_audit->city }}, @if($message->audit->cached_audit->state){{ $message->audit->cached_audit->state }} @endif {{ $message->audit->zip }}
						</small>
					</p>
					@endif
				</div>
			</div>
			<div class="uk-width-1-5@m communication-item-parcel uk-visible@m">
				@if($message->audit_id && $message->audit && $message->audit->cached_audit)
				<p style="margin-bottom:0"><a class="uk-link-muted">{{ $message->audit_id }} | {{ $message->project->project_number }} : {{ $message->project->project_name }}</a></p>
				<p class="uk-visible@m" style="margin-top:0" uk-tooltip="pos:left" title="{{ $message->audit->cached_audit->title }}">
					<small>{{ $message->audit->cached_audit->address }},
						{{ $message->audit->cached_audit->city }}, @if($message->audit->cached_audit->state){{ $message->audit->cached_audit->state }} @endif {{ $message->audit->cached_audit->zip }}
					</small>
				</p>
				@endif
			</div>
			<div class="uk-width-3-5@m uk-width-1-1@s communication-item-excerpt "  >
				@if(count($message->local_documents) > 0 || count($message->docuware_documents) > 0)
				<div uk-grid class="uk-grid-collapse">
					<div class="uk-width-5-6@m uk-width-1-1@s communication-item-excerpt" >
						@if($message->subject)<strong>{{ $message->subject }}</strong><hr /> @endif
						{{ $message->message }}
					</div>
					<div class="uk-width-1-6@m uk-width-1-1@s communication-item-excerpt uk-align-center"  >
						<div class="communication-item-attachment uk-margin-large-left">
							<span uk-tooltip="pos:top-left;title:@foreach($message->local_documents as $document) {{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }} <br> @endforeach @foreach($message->docuware_documents as $document) {{ ucwords(strtolower($document->document_class)) }} : {{ ucwords(strtolower($document->document_description)) }} @endforeach">
								<i class="a-paperclip-2"></i>
							</span>
						</div>
					</div>
				</div>
				@else
				@if($message->subject)<strong>{{ $message->subject }}</strong><br />@endif
				{{ $message->message }}
				@endif
			</div>
		</div>
	</div>