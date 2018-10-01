<div id="project" class="uk-no-margin-top">
	<div id="project-top" uk-grid>
		<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
			<nav class="tab-sub-nav" uk-navbar="" style="border:none">
				<div class="uk-navbar-left" style="overflow-x: auto; overflow-y: hidden; width:100%;">
					<ul id="project-top-tabs" class="uk-navbar-nav" uk-switcher="connect: #project-main">
						<li id="project-detail-tab-1" class="project-detail-tab-1" onclick="loadTab('{{ route('project.details', '19200114') }}', '1', 0, 0, 'project-');" style="margin-left: 10px;" aria-expanded="true"><a><i class="a-clipboard"></i> <span>Details</span></a></li>
						<li id="project-detail-tab-2" class="project-detail-tab-2 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '2', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-envelope-incoming"></i> <span>Communications</span></a></li>		
						<li id="project-detail-tab-3" class="project-detail-tab-3 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '3', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-file-clock"></i> <span>Documents</span></a></li>
						<li id="project-detail-tab-4" class="project-detail-tab-4 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '4', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-file-text"></i> <span>Notes</span></a></li>
						<li id="project-detail-tab-5" class="project-detail-tab-5 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '5', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-comment-text"></i> <span>Comments</span></a></li>
						<li id="project-detail-tab-6" class="project-detail-tab-6 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '6', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-picture"></i> <span>Photos</span></a></li>
						<li id="project-detail-tab-7" class="project-detail-tab-7 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '7', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-mobile-info"></i> <span>Findings</span></a></li>
						<li id="project-detail-tab-8" class="project-detail-tab-8 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '8', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-bell-ring"></i> <span>Follow-ups</span></a></li>
						<li id="project-detail-tab-9" class="project-detail-tab-9 uk-margin-small-left" onclick="loadTab('{{ route('project.details', '19200114') }}', '9', 0, 0, 'project-');" aria-expanded="false"><a><i class="a-file-chart-3"></i> <span>Reports</span></a></li>		
					</ul>
					
				</div>
			</nav>
		</div>
	</div>
	<ul id="project-main" class="uk-switcher">
		<li id="project-details" class="project-tab">
			<div id="project-detail-tab-1-content">
			</div>
		</li>
		<li id="project-coms" class="project-tab">
			<div id="project-detail-tab-2-content">
			</div>
		</li>
		<li id="project-documents" class="project-tab">
			<div id="project-detail-tab-3-content">
			</div>
		</li>
		<li id="project-notes" class="project-tab">
			<div id="project-detail-tab-4-content">
			</div>
		</li>
		<li id="project-comments" class="project-tab">
			<div id="project-detail-tab-5-content">
			</div>
		</li>
		<li id="project-photos" class="project-tab">
			<div id="project-detail-tab-6-content">
			</div>
		</li>
		<li id="project-findings" class="project-tab">
			<div id="project-detail-tab-7-content">
			</div>
		</li>
		<li id="project-followups" class="project-tab">
			<div id="project-detail-tab-8-content">
			</div>
		</li>
		<li id="project-reports" class="project-tab">
			<div id="project-detail-tab-9-content">
			</div>
		</li>
	</ul>
</div>

<script>
$( document ).ready(function() {
@if($tab !== null)
$('#{{$tab}}').trigger("click");
@else
$('#project-detail-tab-1').trigger("click");
@endif
});
</script>
