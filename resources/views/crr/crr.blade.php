@extends('layouts.simplerAllita')
@section('head')
<title>{{ $report->template()->template_name }}: #{{ $report->id }} || {{ $report->project->project_number }} : {{ $report->project->project_name }} || AUDIT: {{ $report->audit->id }}.{{ str_pad($report->version, 3, '0', STR_PAD_LEFT) }}</title>
<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/audits.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/findings.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/communications.js{{ asset_version() }}"></script>

<script>
	function showOnlyFindingsFor(className){
		if(className == 'finding-group'){
			$('.show-all-findings-button').slideUp();
		}else{
			$('.show-all-findings-button').slideDown();
		}
		
		$('.finding-group').hide();
		$('.'+className).fadeIn();
		scrollToAnchor('findings-list');
	}

	function showOnlyInspectionsFor(className){
		
		$('.finding-group').hide();
		$('.'+className).fadeIn();
	}

	function scrollToAnchor(aid){
	    var aTag = $("a[name='"+ aid +"']");
	    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
	}
	function showComments(partId){
		$('#section-thumbnails').css({'min-width':'400px','width':'400px','padding':'0px'});
		$('#main-report-view').width('926px');
		$('#main-report-view').css({'min-width':'926px','width':'926px'});
		$('.crr-sections').css({'min-width':'926px','padding':'36px','width':'926px'});
		$('.crr-part').css({'min-width':'854px','width':'854px'});
		$('.crr-comment-edit').hide();
		$('.crr-part-'+partId).addClass('crr-part-commenting');
		if($('.crr-part').hasClass('crr-part-comment-icons')){
			window.crrparticons = true;
			$('.crr-part').removeClass('crr-part-comment-icons');
		}
		Promise.resolve().then(function(){$('.crr-thumbs').fadeOut();},function(){})
		.then(function(){$('#close-comments').slideDown();},function(){})
		.then(function(){$('#comment-list').html('');},function(){})
	  //LOAD IN COMMENTS
	  .then(function(){$('#comment-list').html('<div style="margin-left:164px;" uk-spinner></div><p align="center"><small>LOADING COMMENTS</small></p>');})
	  .then(function(){$.get('/report/{{ $report->id }}/comments/'+partId, function(data) {
	  	if(data==1){
	  		$('#comment-list').html('No Comments');
	  	} else {
        //UIkit.modal.alert('updated'+data1);
        $('#comment-list').html(data);
      }
    });
	})
	  .then(function(){$('#comment-list').fadeIn();});
	}

	function closeComments(){
		$('#comment-list').fadeOut();
		$('#comment-list').html('');
		$('#close-comments').slideUp();
		$('.crr-thumbs').fadeIn();
		$('#section-thumbnails').css({'max-width':'130px','min-width':'90px','width':'113px','padding-top':'30px','padding-right':'5px','padding-left':'5px'});
		$('#main-report-view').width('1248px');
		$('#main-report-view').css({'min-width':'1248px','width':'1248px'});
		$('.crr-sections').css({'min-width':'996px','padding':'72px','width':'1142px'});
		$('.crr-part').css({'min-width':'996px','width':'996px'});
		$('.crr-comment-edit').show();
		$('.crr-part').removeClass('crr-part-commenting');
		if($(window.crrparticons)){
			window.crrparticons = true;
			$('.crr-part').addClass('crr-part-comment-icons');
		}
	}

	function resizeModal (setSize) {
		$('#modal-size').css('width', setSize+'%');
		console.log('Resized Modal to '+setSize+'%')
	}

</script>
@can('access_auditor')
<script type="text/javascript">
	function markApproved(id,catid){
		UIkit.modal.confirm("Are you sure you want to approve this file?").then(function() {
			$.post('{{ URL::route("documents.local-approve", 0) }}', {
				'id' : id,
				'catid' : catid,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data != 1 ) {
					console.log("processing");
					UIkit.modal.alert(data);
				} else {
					dynamicModalClose();
				}
			//documentsLocal('{{0}}');
			let els = $('.doc-'+id);
			let spanels = $('.doc-span-'+id);
			let spancheck = $('.doc-span-check-'+id);
			for (i = 0; i < els.length; i++) {
				els[i].className = '';
				els[i].className = 'approved-category doc-'+id;
			}
			for (i = 0; i < spanels.length; i++) {
				spanels[i].className = '';
				spanels[i].className = 'a-checkbox-checked received-yes uk-float-left doc-span-'+id;
			}
			for (i = 0; i < spancheck.length; i++) {
				spancheck[i].className = '';
				spancheck[i].className = 'a-checkbox  minus-received-yes received-yes doc-span-check-'+id;
			}
		}
		);
		});
	}

	function markUnreviewed(id,catid){
		UIkit.modal.confirm("Are you sure you want to clear the review on this file?").then(function() {
			$.post('{{ URL::route("documents.local-clearReview", 0) }}', {
				'id' : id,
				'catid' : catid,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data != 1){
					console.log("processing");
					UIkit.modal.alert(data);
				} else {
					dynamicModalClose();
				}
				let els = $('.doc-'+id);
				let spanels = $('.doc-span-'+id);
				let spancheck = $('.doc-span-check-'+id);
				for (i = 0; i < els.length; i++) {
					els[i].className = 'doc-'+id;
				}
				for (i = 0; i < spanels.length; i++) {
					spanels[i].className = '';
					spanels[i].className = 'a-checkbox-checked check-received-no received-no doc-span-'+id;
				}
				for (i = 0; i < spancheck.length; i++) {
					spancheck[i].className = '';
					spancheck[i].className = 'a-checkbox received-no doc-span-check-'+id;
				}
			}
			);
		});
	}

	function markNotApproved(id,catid){
		UIkit.modal.confirm("Are you sure you want to decline this file?").then(function() {
			$.post('{{ URL::route("documents.local-notapprove", 0) }}', {
				'id' : id,
				'catid' : catid,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data != 1){
					UIkit.modal.alert(data);
				} else {
					dynamicModalClose();
				}
				let els = $('.doc-'+id);
				let spanels = $('.doc-span-'+id);
				let spancheck = $('.doc-span-check-'+id);
				for (i = 0; i < els.length; i++) {
					els[i].className = '';
					els[i].className = 'declined-category s doc-'+id;
				}
				for (i = 0; i < spanels.length; i++) {
					spanels[i].className = '';
					spanels[i].className = 'a-checkbox-checked check-received-no received-no doc-span-'+id;
				}
				for (i = 0; i < spancheck.length; i++) {
					spancheck[i].className = '';
					spancheck[i].className = 'a-circle-cross alert received-no doc-span-check-'+id;
				}
			});
		});
	}

	function deleteFile(id){
		UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
			$.post('{{ URL::route("documents.local-deleteDocument", 0) }}', {
				'id' : id,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!= 1){
					UIkit.modal.alert(data);
				} else {
				}

			});
		});
	}
</script>
@endCan
@stop
@section('content')

@can('access_auditor')
@include('templates.modal-findings-items')
@endCan

@if(Auth::user()->can('access_auditor') || $report->crr_approval_type_id > 5)
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
	<?php // determin background type
	$background = "none";
	if (1 == $report->crr_approval_type_id) {
		$background = '-draft';
	}
	if (2 == $report->crr_approval_type_id) {
		$background = '-pending';
	}
	if (3 == $report->crr_approval_type_id) {
		$background = '-declined';
	}
	if (4 == $report->crr_approval_type_id) {
		$background = '-revise';
	}
	?>
	.crr-sections {
		width:1142px; min-height: 1502px; margin-left:auto; margin-right:auto; border:1px black solid; background-image: url('/paginate-2x{{ $background }}.gif'); padding: 72px;

	}
	.crr-comment-edit {
		float: right;
		left: 132px;
		position: relative;
		width: 50px;
		max-height: 74px;
		top:0px;
	}
	.crr-part {
		position: relative;
		float: left;
		width: 996px;

	}
	@can('access_auditor')
	.crr-part-comment-icons {
		top:-74px;
	}
	.crr-part-commenting {
		background-color: rgba(255, 255, 224, .5);
		-webkit-transition: background-color .5s ease-out;
		-moz-transition: background-color .5s ease-out;
		-o-transition: background-color .5s ease-out;
		transition: background-color .5s ease-out;
	}
	#close-comments {
		background-color: black;
		height: 67px;
		padding: 8px;
		padding-left: 15px;
		width: 377px;
		color:lightyellow;
		position: fixed;

	}
	#comment-list {
		margin-top:110px;
		padding-left: 15px;
		padding-right: 15px;
	}
	@endCan
	#section-thumbnails {
		-webkit-transition: width 1s ease-out;
		-moz-transition: width 1s ease-out;
		-o-transition: width 1s ease-out;
		transition: width 1s ease-out;

	}
	#crr-part {
		-webkit-transition: width 1s ease-out;
		-moz-transition: width 1s ease-out;
		-o-transition: width 1s ease-out;
		transition: width 1s ease-out;

	}
	#crr-sections {
		-webkit-transition: width 1s ease-out;
		-moz-transition: width 1s ease-out;
		-o-transition: width 1s ease-out;
		transition: width 1s ease-out;

	}
	.crr-level-1 {
		background-color: #222;
	}
	.crr-level-2 {
		background-color: #555;
	}
	.crr-level-3 {
		background-color: #999;
	}
	#crr-panel .uk-panel-box-white {background-color:#ffffff;}
	#crr-panel .uk-panel-box .uk-panel-badge {}
	#crr-panel .green {color:#82a53d;}
	#crr-panel .blue {color:#005186;}
	#crr-panel .uk-panel + .uk-panel-divider {
		margin-top: 50px !important;
	}
	#crr-panel table tfoot tr td {border: none;}
	#crr-panel textarea {width:100%;}
	#crr-panel .note-list-item:last-child { border: none;}
	#crr-panel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
	#crr-panel .property-summary {margin-top:0;}
	#main-window { padding-top:0px !important; padding-bottom: 0px !important; max-width: 1362px !important; min-width: 1362px !important; }
</style>

<div uk-grid >
	<div id="section-thumbnails" class="uk-panel-scrollable" style="background-color:lightgray; padding-top:30px; min-height: 100vh; max-width:130px;">
		@forEach($report->sections as $section)
		<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-thumbs" style="width:85px; magin-left:auto; margin-right:auto; padding:15px; min-height: 110px;">
			<?php $thumbNavPartCount = 1;?>
			@foreach($section->parts as $part)
			<a href="#part-{{ $part->id }}" class="uk-link-mute" onmouseover="$('.crr-part-{{ $part->id }}').addClass('crr-part-commenting');" onmouseout="$('.crr-part-{{ $part->id }}').removeClass('crr-part-commenting');"><small>{{ $part->title }}</small></a><hr class="dashed-hr uk-margin-bottom">
			<?php $thumbNavPartCount++;?>
			@endForeach
		</div>
		<div align="center" class="uk-align-center uk-margin-large-bottom use-handcursor crr-thumbs" style="max-width: 85px;"><a href="#{{ str_replace(' ','',$section->id) }}" class="uk-link-mute">{{ strtoupper($section->title) }}</a>
		</div>
		@endForEach

		@can('access_auditor')
		<div id="close-comments" style="display: none" onclick="closeComments();" class="uk-link"><i class="a-circle-cross uk-contrast"></i> CLOSE COMMENTS<hr class="hr-dashed uk-margin-small-bottom"></div>
		<div id="comment-list" style="display: none;"></div>
		@endCan
	</div>
	<div id="main-report-view" class=" uk-panel-scrollable" style=" min-height: 100vh; min-width: 1248px; padding:0px; background-color: currentColor;">
		@php
			$j = 0;
		@endphp

		@forEach($data as $section)

		<a name="{{ str_replace(' ','',$section->crr_section_id) }}" ></a>
		<hr class="dashed-hr" style="margin-bottom: 60px; margin-top: 0px; padding:0px; border-color: #3a3a3a;">
		<small style="position: relative;top: -55px; left:15px; color:lightblue">VERSION: {{ $report->version }}  @can('access_auditor') | <a onClick="UIkit.modal.confirm('<h1>Refresh report {{ $report->id }}?</h1><h3>Refreshing the dynamic content of the report will create a new version and move it to the status of draft.</h3>').then(function() {window.location.href ='/report/{{ $report->id }}/generate';}, function () {console.log('Rejected.')});" class="uk-link-mute" style="color:lightblue">REFRESH REPORT CONTENT</a>@endCan | <a href="/report/{{ $report->id }}?print=1" target="_blank" class="uk-contrast uk-link-mute"> <i class="a-print"></i> PRINT</a></small>

		<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
			@if(property_exists($section,'parts'))
			<?php $pieceCount = 1;?>
			@forEach($section->parts as $part)

			@forEach($part as $piece)

			<?php
				// collect comments for this part
			if (Auth::user()->can('access_auditor')) {
				$comments = collect($report->comments)->where('part_id', $piece->part_id);

				if ($comments) {
					$totalComments = count($comments);
				}
			} else {
				$comments      = [];
				$totalComments = 0;
			}
			?>
			@can('access_auditor')<div class="crr-comment-edit"><a class="uk-contrast" onClick="showComments({{ $piece->part_id }});" >#{{ $pieceCount }}<hr class="dashed-hr uk-margin-bottom"><i class="a-comment"></i> @if($comments) {{ $totalComments }} @else 0 @endIf</a>
				<hr class="dashed-hr uk-margin-bottom"><a class="uk-contrast"><i class="a-pencil" style="font-size: 19px;"></i></a>

			</div>@endCan
			<div class="crr-part-{{ $piece->part_id }} crr-part @if(!$print) crr-part-comment-icons @endIf"> <a name="part-{{ $piece->part_id }}"></a>
				<?php $pieceData = json_decode($piece->data);?>

				@if($pieceData[0]->type =='free-text')

				{!! $piece->content !!}

				@endIf

				{{-- @if($j > 3)
				{{ dd($pieceData[1][1]) }}
				@endif
				@php
					$j++;
				@endphp --}}
				@if($pieceData[0]->type == 'blade')
				<?php
				if (array_key_exists(2, $pieceData)) {
					$bladeData = $pieceData[2];
				} else {
					$bladeData = null;
				}
				?>
				@if($piece->blade != 'crr_parts.crr_findings')
				@include($piece->blade, [$inspections_type = 'site'])
				@endif
				<?php
				if (array_key_exists(3, $pieceData)) {
					$bladeData = $pieceData[3];
				} else {
					$bladeData = null;
				}
				?>
				@if($piece->blade != 'crr_parts.crr_findings')
				@include($piece->blade, [$inspections_type = 'building'])
				@endif


				<?php
				if (array_key_exists(1, $pieceData)) {
					$bladeData = $pieceData[1];
				} else {
					$bladeData = null;
				}
				?>
				@include($piece->blade, [$inspections_type = 'unit'])


				@endIf


			</div>
			<?php $pieceCount++;?>
			@endForEach
			@endForEach
			@endIf
		</div>
		@endForEach
	</div>




</div>
@can('access_auditor')<div id="comments" class="uk-panel-scrollable" style="display: none;">@endCan

</div>
@else
<h1>Sorry!</h1>
<h2>The report you are trying to view has not been released for your review.</h2>
@endIf
@stop