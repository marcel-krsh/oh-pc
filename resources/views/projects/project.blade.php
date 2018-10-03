<div id="project" class="uk-no-margin-top">
	<div id="project-top" uk-grid>
		<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
			<nav class="tab-sub-nav" uk-navbar="" style="border:none">
				<div class="uk-navbar-left" style="overflow-x: auto; overflow-y: hidden; width:100%;">
					<ul id="project-top-tabs" class="uk-navbar-nav" uk-switcher="connect: #project-main">
						@foreach($projectTabs as $projectTab)
						<li id="project-detail-tab-{{$loop->iteration}}" class="project-detail-tab-{{$loop->iteration}} uk-margin-small-left {{$projectTab['status']}}" onclick="loadTab('{{ route($projectTab['action'], $projectId) }}', '{{$loop->iteration}}', 0, 0, 'project-');" aria-expanded="false"><a><i class="{{$projectTab['icon']}}"></i> <span>{{$projectTab['title']}}</span></a></li>
						@endforeach		
					</ul>
				</div>
			</nav>
		</div>
	</div>
	<ul id="project-main" class="uk-switcher">
		@foreach($projectTabs as $projectTab)
		<li class="project-tab">
			<div id="project-detail-tab-{{$loop->iteration}}-content">
			</div>
		</li>
		@endforeach	
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
