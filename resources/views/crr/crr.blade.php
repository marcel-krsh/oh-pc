@extends('layouts.simplerAllita')
@section('head')
<?php

session(['projectDetailsOutput' => 0]);

?>
<title>{{ $report->template()->template_name }}: #{{ $report->id }} || {{ $report->project->project_number }} : {{ $report->project->project_name }} || AUDIT: {{ $report->audit->id }}.{{ str_pad($report->version, 3, '0', STR_PAD_LEFT) }}</title>
<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/audits.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/findings.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/communications.js{{ asset_version() }}"></script>

<script type="text/javascript" src="/js/jquery.mask.js"></script>
<script>
	function showOnlyFindingsFor(className){
		if(className == 'finding-group'){
			$('.show-all-findings-button').slideUp();
		}else{
			$('.show-all-findings-button').slideDown();
		}

		$('.finding-group').hide();
		$('.'+className).fadeIn();
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
		$('#main-report-view').width('90%');
		$('#main-report-view').css({'min-width':'980px','width':'90%'});
		$('.crr-sections').css({'min-width':'996px','padding':'72px','width':'720px'});
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
@if($auditor_access)
<script type="text/javascript">

	function updateStatus(report_id, action, receipents = []) {
		// debugger;
		$.get('/dashboard/reports', {
			'id' : report_id,
			'action' : action,
			'receipents' : receipents,
			'check' : 1
		}, function(data2) {
			UIkit.modal.alert('Your message has been saved.',{stack: true});
			window.location.href ='/report/{{ $report->id }}';
		});
	}

	function reportAction(reportId,action,project_id = null){
		// debugger;
		window.crrActionReportId = reportId;
  	//Here goes the notification code
  	if(isNaN(action)) {
  		UIkit.modal.alert('Opening report '+ action);
  		window.location.href ='/report/{{ $report->id }}?'+action;
  		return;
  	}
  	// return;

  	if(action == 6) {
  		dynamicModalLoad('report-ready/' + reportId + '/' + project_id);
  	} else if(action == 2) {
  		// debugger;
  		dynamicModalLoad('report-send-to-manager/' + reportId + '/' + project_id);
  	} else if(action == 3) {
  		dynamicModalLoad('report-decline/' + reportId + '/' + project_id);
  	} else if(action == 4) {
  		dynamicModalLoad('report-approve-with-changes/' + reportId + '/' + project_id);
  	} else if(action == 5) {
  		dynamicModalLoad('report-approve/' + reportId + '/' + project_id);
  	} else if(action == 9) {
  		dynamicModalLoad('report-resolved/' + reportId + '/' + project_id);
  	} else if(action != 8){
  		UIkit.modal.alert('Updating status... please wait a moment.');
  		$.get('/dashboard/reports', {
  			'id' : reportId,
  			'action' : action
  		}, function(data2) {
  			window.location.href ='/report/{{ $report->id }}';

  		});
    	//loadTab('/dashboard/reports?id='+reportId+'&action='+action, '3','','','',1);
    }else if(action == 8){
    	UIkit.modal.confirm('Refreshing the dynamic data will set the report back to Draft status - are you sure you want to do this?').then(function(){
    		$.get('/dashboard/reports', {
    			'id' : window.crrActionReportId,
    			'action' : 8
    		}, function(data2) {

    		});
    	},function(){
      //nope
    });
    }
    $('#crr-report-action-'+reportId).val(0);
    // $('#crr-report-row-'+reportId).slideUp(); //commented by Div on 20190922 - While modal is open, this row is hinding, any reason?
  }

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
			//documentsLocal('{{ 0 }}');
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
@endif


@stop
@section('content')

@if($auditor_access)
@include('templates.modal-findings-items')
@endif

@if($auditor_access || $report->crr_approval_type_id > 5)
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
	<?php // determin background type
$background = "";
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
		width:90%;min-width:720px; min-height: 1502px; margin-left:auto; margin-right:auto; border:1px black solid; background-image: url('/paginate-2x{{ $background }}.gif'); padding: 72px;

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
	@if($auditor_access)
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
	ul.leaders, .leaders {
		background-color: white;
	}
	@endif
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
	#report-actions-footer{
		position: fixed;
		top:0px;
		right:0px;
	}
</style>

<div uk-grid >
	<div id="section-thumbnails" class="uk-panel-scrollable" style="background-color:lightgray; padding-top:30px; min-height: 100vh; max-width:130px;">
		<a href="/report/{{ $report->id }}?print=1" target="_blank" class=" uk-link-mute" uk-tooltip title="PRINT REPORT"> <i class="a-print" style="font-weight: bolder;"></i></a> @if($auditor_access)| <a uk-tooltip title="REFRESH REPORT CONTENT" onClick="UIkit.modal.confirm('<h1>Refresh report {{ $report->id }}?</h1><h3>Refreshing the dynamic content of the report will create a new version and move it to the status of draft.</h3>').then(function() {window.location.href ='/report/{{ $report->id }}/generate';}, function () {console.log('Rejected.')});" class="uk-link-mute refresh-content-button" > <i class="a-rotate-left-3" style="font-weight: bolder;"></i></a> @endif | @if($oneColumn) <a uk-tooltip title="VIEW FINDINGS IN THREE COLUMNS" href="/report/{{ $report->id }}?three_column=1" target="_blank" class=" uk-link-mute"> <i class="a-grid" style="font-weight: bolder;"></i></a> @else <a uk-tooltip title="VIEW FINDINGS IN ONE COLUMN" href="/report/{{ $report->id }}?one_column=1" target="_blank" class=" uk-link-mute"> <i class="a-list" style="font-weight: bolder;"></i></a> @endif @if($auditor_access) | <a uk-tooltip title="CHANGE REPORT DATES" onclick="changeReportDates()" class=" uk-link-mute"> <i class="a-calendar-8" style="font-weight: bolder;"></i></a> @endif
		@if($auditor_access)
		<div id="close-comments" style="display: none" onclick="closeComments();" class="uk-link"><i class="a-circle-cross uk-contrast"></i> CLOSE COMMENTS<hr class="hr-dashed uk-margin-small-bottom"></div>
		<div id="comment-list" style="display: none;"></div>
		@endif
		<hr class="dashed-hr uk-margin-bottom">
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
		<hr class="dashed-hr uk-margin-bottom">

		<a href="/report/{{ $report->id }}?print=1" target="_blank" class=" uk-link-mute" uk-tooltip title="PRINT REPORT"> <i class="a-print" style="font-weight: bolder;"></i></a> @if($auditor_access)| <a uk-tooltip title="REFRESH REPORT CONTENT" onClick="UIkit.modal.confirm('<h1>Refresh report {{ $report->id }}?</h1><h3>Refreshing the dynamic content of the report will create a new version and move it to the status of draft.</h3>').then(function() {window.location.href ='/report/{{ $report->id }}/generate';}, function () {console.log('Rejected.')});" class="uk-link-mute refresh-content-button" > <i class="a-rotate-left-3" style="font-weight: bolder;"></i></a> @endif | @if($oneColumn) <a uk-tooltip title="VIEW FINDINGS IN THREE COLUMNS" href="/report/{{ $report->id }}?three_column=1" target="_blank" class=" uk-link-mute"> <i class="a-grid" style="font-weight: bolder;"></i></a> @else <a uk-tooltip title="VIEW FINDINGS IN ONE COLUMN" href="/report/{{ $report->id }}?one_column=1" target="_blank" class=" uk-link-mute"> <i class="a-list" style="font-weight: bolder;"></i></a> @endif @if($auditor_access) | <a uk-tooltip title="CHANGE REPORT DATES" onclick="changeReportDates()" class=" uk-link-mute"> <i class="a-calendar-8" style="font-weight: bolder;"></i></a> @endif
		@if($auditor_access)
		<div id="close-comments" style="display: none" onclick="closeComments();" class="uk-link"><i class="a-circle-cross uk-contrast"></i> CLOSE COMMENTS<hr class="hr-dashed uk-margin-small-bottom"></div>
		<div id="comment-list" style="display: none;"></div>
		@endif
	</div>
	<div id="main-report-view" class=" uk-panel-scrollable" style=" min-height: 100vh; width: 90%; padding:0px; background-color: currentColor;">
		@php
		$j = 0;
		@endphp
		@foreach($data as $section)
		<a name="{{ str_replace(' ','',$section->crr_section_id) }}" ></a>
		<hr class="dashed-hr" style="margin-bottom: 60px; margin-top: 0px; padding:0px; border-color: #3a3a3a;">
		<small style="position: relative;top: -55px; left:15px; color:lightblue">VERSION: {{ $version }} @if($auditor_access) | <a onClick="UIkit.modal.confirm('<h1>Refresh report {{ $report->id }}?</h1><h3>Refreshing the dynamic content of the report will create a new version and move it to the status of draft.</h3>').then(function() {window.location.href ='/report/{{ $report->id }}/generate';}, function () {console.log('Rejected.')});" class="uk-link-mute refresh-content-button" style="color:lightblue">REFRESH REPORT CONTENT</a>@endif | <a href="/report/{{ $report->id }}?print=1" target="_blank" class="uk-contrast uk-link-mute"> <i class="a-print"></i> PRINT</a></small>
		<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
			@if(property_exists($section,'parts'))
			<?php $pieceCount = 1;?>
			{{-- {{ dd($section) }} --}}
			@foreach($section->parts as $part)
			@foreach($part as $piece)
			<?php
// collect comments for this part
if ($auditor_access) {
	$comments = collect($report->comments)->where('part_id', $piece->part_id);
	if ($comments) {
		$totalComments = count($comments);
	}
} else {
	$comments = [];
	$totalComments = 0;
}
?>
			@if($auditor_access)
			<div class="crr-comment-edit"><a class="uk-contrast" onClick="showComments({{ $piece->part_id }});" >#{{ $pieceCount }}<hr class="dashed-hr uk-margin-bottom"><i class="a-comment"></i> @if($comments) {{ $totalComments }} @else 0 @endif</a>
				<hr class="dashed-hr uk-margin-bottom"><a class="uk-contrast"><i class="a-pencil" style="font-size: 19px;"></i></a>
			</div>
			@endif

			<div class="crr-part-{{ $piece->part_id }} crr-part @if(!$print) crr-part-comment-icons @endIf"> <a name="part-{{ $piece->part_id }}"></a>
				<?php
$pieceData = json_decode($piece->data);
// set this so we only output details once from the blade.
;?>
				@if($pieceData[0]->type =='free-text')
				{!! $piece->content !!}
				@endif

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
				@if($piece->blade == 'crr_parts.crr_inspections')
				@include($piece->blade, [$inspections_type = 'site', $audit_id = $report->audit->id])
				@endif
				<?php
if (array_key_exists(3, $pieceData)) {
	$bladeData = $pieceData[3];
} else {
	$bladeData = null;
}
?>
				@if($piece->blade == 'crr_parts.crr_inspections')
				@include($piece->blade, [$inspections_type = 'building', $audit_id = $report->audit->id])
				@endif
				<?php
if (array_key_exists(1, $pieceData)) {
	$bladeData = $pieceData[1];
} else {
	$bladeData = null;
}
?>
				@include($piece->blade, [$inspections_type = 'unit', $audit_id = $report->audit->id])

				@endif
			</div>

			<?php $pieceCount++;?>
			@endforeach
			@endforeach
			@endif
		</div>
		@endforeach

		<?php /* Send Fax with Print Report PDF - Start */?>
		<div id="fax-modal" uk-modal>
			<div class="uk-modal-dialog uk-modal-body">
				<h2 class="uk-modal-title" style="font-size: 20px;font-weight: 600;"><i class="a-fax-2"></i> FAX Number</h2>
				<p><input id="faxnumber" name="faxnumber" type="text" style="width: 100%;" class="uk-input fieldDisable" placeholder="111-333-5555"></p>
				<p class="uk-text-right">
					<button class="uk-button uk-button-default uk-modal-close sendfaxbtnClose fieldDisable" type="button">Cancel</button>
					<button class="uk-button uk-button-primary sendfaxbtn fieldDisable" type="button">Send</button>
				</p>
			</div>
		</div>
		<?php /* Send Fax with Print Report PDF - End */?>
	</div>
	<?php
session(['projectDetailsOutput' => 0]);
?>

	<script type="text/javascript">
		$("#faxnumber").mask("999-999-9999");
		$(".sendfaxbtnClose").click(function(){
			$('#faxnumber').val("");
		});
		$(".sendfaxbtn").click(function(){
			var faxnumber = $('#faxnumber').val();
			var _token= '{!! csrf_token() !!}';
			var report='{{ $report->id }}';
			$('.sendfaxbtn').html("<div uk-spinner></div> Please Wait");
			$('.fieldDisable').prop('disabled', true);
			$.ajax({
				type:'POST',
				url:"{{ URL('/report/sendfax') }}",
				data:{faxnumber:faxnumber,_token:_token,report:report},
				dataType:'json',
				success:function(data){
					$('.fieldDisable').prop('disabled', false);
					$('.sendfaxbtn').html("Send");
					$(".sendfaxbtnClose").trigger('click');
					if(data.status){
						UIkit.modal.dialog('<center style="color:green">'+data.message+'</center>');
						/* UIkit.notification({
							message: data.message,
							status: 'success',
							pos: 'top-center',
							timeout: 30000
						}); */
					}else{
						UIkit.notification({
							message: data.message,
							status: 'danger',
							pos: 'top-center',
							timeout: 30000
						});
					}

				}
			});
		});
	</script>

</div>
@if($auditor_access)
@if($auditor_access)
@if($report->crr_approval_type_id !== 8)
<div id="report-actions-footer">
	<select class="uk-form uk-select" id="crr-report-action-{{ $report->id }}" onchange="reportAction({{ $report->id }},this.value, {{ $report->project->id }});" style="width: 184px;">
		<optgroup label="REPORT ACTIONS">
			<option value="0">ACTION</option>
			@if(!($report->crr_approval_type_id >= 5))
			<option value="1">DRAFT</option>
			@if($report->requires_approval)
			<option value="2">SEND TO MANAGER REVIEW</option>
			@endif
			@if($manager_access)
			@if($report->requires_approval)
			<option value="3">DECLINE</option>
			<option value="4">APPROVE WITH CHANGES</option>
			<option value="5">APPROVE</option>
			@endif
			@endif
			@endif
			@if( ($report->requires_approval == 1 && $report->crr_approval_type_id > 3) || $report->requires_approval == 0 || $manager_access)
			<option value="6">SEND TO PROPERTY CONTACT</option>
			<option value="7">PROPERTY VIEWED IN PERSON</option>
			<option class="uk-nav-divider" style="border-bottom: solid 5px red" value="9">ALL ITEMS RESOLVED</option>
		</optgroup>
		<optgroup label="REPORT VERSIONS">
			@for($i=0 ; $i<$versions_count ; $i++)
			<option value="version={{ $i + 1 }}">Version - {{ $i + 1 }}</option>
			@endfor
		</optgroup>
		@endif
	</select>
</div>
@else
<div style="margin-left: auto; margin-righ:auto;" uk-spinner></div>
@endif
@endif
<div id="comments" class="uk-panel-scrollable" style="display: none;">
	@endif

</div>
@else
<h1>Sorry!</h1>
<h2>The report you are trying to view has not been released for your review.</h2>
@endif
@stop

<script>
	function dynamicModalLoadLocal(modalSource) {
		var newmodalcontent = $('#dynamic-modal-content-communications');
		var mcom = document.getElementById("dynamic-modal-content-2");
	  if(mcom){
	     $(mcom).html('');
	  }
		$(newmodalcontent).html("");
		$(newmodalcontent).html('<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>');
		$(newmodalcontent).load('/modals/'+modalSource, function(response, status, xhr) {
			if (status == "error") {
				if(xhr.status == "401") {
					var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				} else if( xhr.status == "500"){
					var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				} else {
					var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				}
				UIkit.modal.alert(msg);
			}
		});
		var modal = UIkit.modal('#dynamic-modal-communications', {
			escClose: false,
			bgClose: false
		});
		modal.show();
	}

	function messageRead(messageId) {
		$.post('{{ url('mark-message-read') }}/'+messageId, {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!=1){
				UIkit.modal.alert(data,{stack: true});
			} else {
  				// var element = document.getElementById("show-message-"+messageId);
  				$(".show-message-"+messageId).removeClass("uk-hidden");
					// var element = document.getElementById("hide-message-"+messageId);
					$(".hide-message-"+messageId).addClass("uk-hidden");
					$(".remove-action-"+messageId).removeClass("attention");
					$(".remove-action-"+messageId).removeClass("ok-actionable");
					UIkit.notification({
						message: 'Message has been marked as read.',
						status: 'success',
						pos: 'top-right',
						timeout: 1500
					});
				}
			} );
	}

	function changeReportDates() {
		dynamicModalLoad('report-dates/'+{{ $report->id }});
	}

	function submitUserInfoForm() {
		jQuery.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		});
		$("#dates_save_button").prop("disabled", true);
		$("#dates_save_button").html('<span uk-spinner"></span>  Processing');
		var form = $('#reportDatesForm');
		var data = { };
		$.each($('form').serializeArray(), function() {
			data[this.name] = this.value;
		});
		jQuery.ajax({
			url: "{{ URL::route("project.reports.dates", $report->id) }}",
			method: 'post',
			data: {
				letter_date: data['letter_date'],
				review_date: data['review_date'],
				response_due_date: data['response_due_date'],
				'_token' : '{{ csrf_token() }}'
			},
			success: function(data){
				$('.alert-danger' ).empty();
				if(data == 1) {
					// UIkit.modal.alert('User has been saved.',{stack: true});
					$("#dates_save_button").html('<span uk-spinner"></span>  Updating Report...');
					window.location.href ='/report/{{ $report->id }}';
					// dynamicModalClose();
				}
				jQuery.each(data.errors, function(key, value){
					$("#dates_save_button").prop("disabled", false);
					$("#dates_save_button").html('Save');
					jQuery('.alert-danger').show();
					jQuery('.alert-danger').append('<p>'+value+'</p>');
				});
			}
		});
	}
</script>