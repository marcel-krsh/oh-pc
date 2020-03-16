<div id="project" class="uk-no-margin-top">
	<div id="project-top" uk-grid>
		<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
			<nav class="tab-sub-nav" uk-navbar="" style="border:none">
				<div class="uk-navbar-left" style="overflow-x: auto; overflow-y: hidden; width:100%;">
					<ul id="project-top-tabs" class="uk-navbar-nav" uk-switcher="connect: #project-main">
						@foreach($projectTabs as $projectTab)
						<li id="project-detail-tab-{{$loop->iteration}}" class="project-detail-tab-{{$loop->iteration}} uk-margin-small-left {{$projectTab['status']}}" onclick="if($('#project-detail-tab-{{$loop->iteration}}').hasClass('uk-active') || window.project_detail_tab_{{$loop->iteration}} != '1'){loadTab('{{ route($projectTab['action'], ['id' => $projectId, 'audit_id' => $audit_id, 'project' => $projectId, 'audit' => $audit_id, 'project_id' => $projectId]) }}', '{{$loop->iteration}}', 0, 0, 'project-',1);window.tab_{{$loop->iteration}}=1;}" aria-expanded="false"><a><i class="{{$projectTab['icon']}}"></i> <span>{{$projectTab['title']}}</span></a></li>
						@endforeach
						<li><a><i class="a-clipboard"></i> {{$project->project_name}} Project Details</a></li>
					</ul>
				</div>
			</nav>
		</div>
	</div>
	<ul id="project-main" class="uk-switcher">
		@foreach($projectTabs as $projectTab)
		<li class="project-tab">
			<div id="project-detail-tab-{{$loop->iteration}}-content"></div>
		</li>
		@endforeach
		<li>
			<div id="project-detail-tab-999-content" style="margin:100px; padding:10px;">
				<h2>This feature is currently unavailable.</h2>
				<p>Please watch for its release in the near future.</p>
			</div>
		</li>
	</ul>
</div>

<div id="footer-actions-project" hidden>
		<a href="#top" id="smoothscrollLink" uk-scroll="{offset: 90}" class="uk-button uk-button-default"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
</div>

<script>
$( document ).ready(function() {
	window.currentProjectOpen = {{$projectId}};
	// place tab's buttons on main footer
	$('#footer-actions-tpl').html($('#footer-actions-project').html());
	@if(session()->has('audit-message'))
		@if(session('audit-message') == 1)

		@endif
	@endif

	@if($tab !== null)
	$('#{{$tab}}').trigger("click");
	@else
	$('#project-detail-tab-1').trigger("click");
	@endif
});
</script>
