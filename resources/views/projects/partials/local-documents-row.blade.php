<td style="vertical-align: middle;"><span class="uk-margin-top uk-padding-left" uk-tooltip title="{{ $document->created_at->format('h:i a') }}">{{ date('m/d/Y', strtotime($document->created_at)) }}</span>
</td>
<td class="uk-width-1-2" style="vertical-align: middle;">
	<ul class="uk-list document-category-menu">
		@foreach ($document->assigned_categories as $document_category)
		<div class="uk-padding-remove" style="margin-top: 7px;">
			<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{ $document->user->full_name() }};" title="" aria-expanded="false" class="user-badge user-badge-{{ $document->user->badge_color }}"> {{ $document->user->initials() }}
			</span>
		</div>
		<li class="{{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
			<a id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}" class="">
				<span style="float: left;margin-top:8px;margin-left: 5px"  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }}"></span>
				<span style="float: left;margin-top:8px;margin-left: 5px" id="sent-id-{{ $document->id }}category-id-1-not-received-icon" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }}"></span>
				<span style="display: block; margin-left: 55px;margin-top:2px">{{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }} </span>
			</a>
			<div uk-dropdown="mode: click" id="#sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}">
				<ul class="uk-nav uk-nav-dropdown">

					<li>
						<a onclick="markApproved({{ $document->id }},{{ $document_category->id }});">
							Mark as approved
						</a>
					</li>
					@if($unresolved_findings > 0)
					<li>
						<a onclick="markApproved({{ $document->id }},{{ $document_category->id }},1);">
							Mark as approved and resolve @if($unresolved_findings > 1)findings @else finding @endIf
						</a>
					</li>
					@endIf
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
			@if($document->comment) <div style="display: block; margin-left:35.25px;"><i class="a-comment"></i> "{{ $document->comment }}" </div>@endif
		</li>
		@endforeach
	</ul>
</td>
<td style="padding-left: 10px">
	<div class="document-building-list"><strong>BUILDINGS {{ count($buildings) }}</strong>
		@forEach($buildingCollection as $docBin) <br /> {{$docBin->building_name}} @endForEach</div><div class="document-unit-list"> <strong>UNITS {{ count($units) }}</strong> @forEach($unitCollection as $docUnit) <br /> {{$docUnit->unit_name}} @endForEach</div>
</td>
<td style="padding-left: 10px">
	@if(count($all_ids) > 0)
	<div class="document-findings-content-{{ $document->id }}">
		@include('projects.partials.local-documents-findings')
	</div>
	@else
	NA | NA
	@endIf
</td>
<td>
	<a class="uk-link-muted " uk-tooltip="@foreach($document->assigned_categories as $document_category){{ $document_category->parent->document_category_name }}/{{ $document_category->document_category_name }}@endforeach">
		<span class="a-info-circle"  style="color: #56b285;"></span>
	</a>
	&nbsp;|&nbsp;
	<a class="uk-link-muted " onclick="dynamicModalLoad('edit-local-document/{{ $document->id }}')" uk-tooltip="Edit this file">
		<span class="a-pencil-2" style="color: rgb(0, 193, 247);"></span>
	</a>
	&nbsp;|&nbsp;
	@if($admin_access)
	<a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
		<span class="a-trash-4" style="color: #da328a;"></span>
	</a>
	&nbsp;|&nbsp;
	@endif
	<a href="download-local-document/{{ $document->id }}" target="_blank"  uk-tooltip="Download file." download>
		<span class="a-lower"></span>
	</a>
</td>