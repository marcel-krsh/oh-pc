<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css{{ asset_version() }}">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
	/*for toggler*/
	#allita-documents .switch {
		position: relative;
		display: inline-block;
		width: 52px;
		height: 26px;
	}

	#allita-documents .switch input {
		opacity: 0;
		width: 0;
		height: 0;
	}

	#allita-documents .slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #ccc;
		-webkit-transition: .4s;
		transition: .4s;
	}

	#allita-documents .slider:before {
		position: absolute;
		content: "";
		height: 18px;
		width: 18px;
		left: 4px;
		bottom: 4px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}

	#allita-documents input:checked + .slider {
		background-color: #005186;
	}

	#allita-documents input:focus + .slider {
		box-shadow: 0 0 1px #005186;
	}

	#allita-documents input:checked + .slider:before {
		-webkit-transform: translateX(26px);
		-ms-transform: translateX(26px);
		transform: translateX(26px);
	}

	/* Rounded sliders */
	#allita-documents .slider.round {
		border-radius: 34px;
	}

	#allita-documents .slider.round:before {
		border-radius: 50%;
	}

	/* select2 style for filter dropdown*/
	.select2-selection {
		box-sizing: border-box !important;
		border: none !important;
		background-color: aliceblue !important;
		font-size: 12px !important;
		color: black !important;
		padding-left: 10px !important;
		border-radius: 0 !important !important;
		height: 30px !important;
	}

	.select_box_area {
		position: relative;
		display: inline-block;
	}
	.select_box_area p {
		/*margin-bottom: 0px;*/
		min-width: 300px;
		max-width: 300px;
	 /* background: #31599c;
	  padding: 10px 15px;
	  border: 1px solid rgba(255, 255, 255, 0.5);
	  line-height: 24px;
	  padding-right: 30px;
	  cursor: pointer;*/
	  box-sizing: border-box;
	  border: none;
	  background-color: aliceblue;
	  font-size: 12px;
	  color: black;
	  padding-left: 10px;
	  border-radius: 0;
	  height: 30px;
	}
	.select_box_area p em {
		position: absolute;
		right: 15px;
		top: 6px;
		font-size: 20px;
		transition: all 0.3s linear;
		color: #000;
	}
	.select_box_area p em.angle-up {
		transform: rotate(180deg);
	}
	.select_box_area p .option {
		position: relative;
		display: inline-block;
		padding-right: 15px;
	}
	.select_box_area p .option::after {
		content: ",";
		position: absolute;
		right: 5px;
		top: 0;
	}
	.select_box_area p .option:last-of-type {
		padding-right: 0px;
	}
	.select_box_area p .option:last-of-type::after {
		display: none;
	}

	.filter_list_ul {
		padding: 0px;
		background: aliceblue;
		border: 1px solid #999999;
		border-top: none;
		display: none;
		max-height: 300px;
		overflow-y: scroll;
		position: relative;
		z-index: 999999
	}
	.filter_list_ul li {
		list-style: none;
	}
	.filter_list_ul li label {
		display: block;
		width: 100%;
		padding: 10px;
		margin: 0px;
		font-size: 14px;
		cursor: pointer;
	}
	.filter_list_ul li input[type="checkbox"] {
		margin-right: 5px;
	}
	.filter_list_ul li + li {
		border-top: 1px solid #999999;
	}

	.custom-select {
		display: none;
	}

	#documents-tab-pages-and-filters .pagination {
		display: inline-block;
	}
</style>
<div uk-grid id="document-filters" data-uk-button-radio="">
	<div class=" uk-width-1-1@s uk-width-1-1@m">
		<hr>
		<div uk-grid class="uk-grid-collapse">
			<div class="uk-width-1-1" uk-grid>
				<input type="text" value="{{ session()->has('local-documents-search-term') ? $searchTerm : '' }}" id="local-documents-search" class="filter-box uk-width-1-5" placeholder="SEARCH DOCUMENTS">
				<div class="uk-width-1-5">
					<label class="switch">
						<input type="checkbox" onchange="searchDocuments(this);" id="documents-unreviewed-checkbox" @if($unreviewed) checked="true" @endIf >
						<span class="slider round"></span>
					</label> <small>SHOW NEEDS REVIEWED </small>
				</div>
				<div class="uk-width-1-5">
					<label class="switch">
						<input type="checkbox" onchange="searchDocuments(this);" id="documents-reviewed-checkbox" @if($reviewed) checked="true" @endIf>
						<span class="slider round"></span>
					</label> <small>SHOW REVIEWED</small>
				</div>
				<div class="uk-width-1-5">
					<label class="switch">
						<input type="checkbox" onchange="searchDocuments(this);" id="documents-findings-uncorrected-checkbox" @if($unresolved) checked="true" @endIf >
						<span class="slider round"></span>
					</label> <small>WITH UNRESOLVED FINDINGS </small>
				</div>
				<div class="uk-width-1-5">
					<label class="switch">
						<input type="checkbox" onchange="searchDocuments(this);" id="documents-findings-corrected-checkbox" @if($resolved) checked="true" @endIf>
						<span class="slider round"></span>
					</label> <small>ALL  FINDINGS RESOLVED</small>
				</div>
				@if($searchTerm != null)
				<div class="uk-width-1-1 uk-margin-remove-top">
					<div  class="uk-badge uk-text-right badge-filter">
						<a onclick="$('#local-documents-search').val(''); searchDocuments();" class="uk-dark uk-light use-hand-cursor" style="position: relative;top: -3px; margin-right: 9px;"><i class="a-circle-cross"></i> <span> {{ strtoupper($searchTerm) }}</span></a>
					</div>
					<div class="uk-badge uk-text-right badge-filter" style="    background: #6aa26a;">
						<a uk-tooltip title="EXPLAIN RESULTS" onclick="UIkit.modal.alert('<h1>What Did My Search... Search?</h1><h3>The Query Searches: <ul><li>Document Categories</li><li>Finding Types</li><li>Building Names</li><li>Unit Names</li><li>Amenity Names</li><li>First and Last Names of Uploaders</li><li>Audit Numbers</li><li>Finding Number</li></h3>');" class="uk-dark uk-light use-hand-cursor" style="position: relative;top: -2px;margin-right: 1.5px;padding-left: 0px;">?
						</a>
					</div>
				</div>
				@endif
			</div>
			<hr class="uk-width-1-1 uk-margin-top">
			<div class="uk-width-1-1 uk-margin-top" id="documents-tab-pages-and-filters" >
				{{ $documents->links() }} <div class="uk-badge uk-align-right uk-label uk-margin-top uk-margin-right" style="line-height: 19px">{{ $documents->total() }}  DOCUMENTS </div>
			</div>
		</div>
	</div>
</div>

<div id="document-uploader" class="documents-upload-panel" style="display: none;" uk-grid>
</div>

<div class="uk-grid uk-margin-top uk-animation-fade" id="local-documents">
	<div class="uk-width-1-1@m uk-width-1-1" id="local-documents-filter">
		<table class="uk-table uk-table-condensed gray-link-table" id="">
			<thead>
				<tr class="uk-text-small" style="background-color:#555;">
					<th class="white-text" style="padding-left: 10px;" width="100"> STORED</th>
					<th class="white-text" width="700"><span class="uk-margin-small-left">CATEGORY | SUBCATEGORY</span></th>
					<th class="white-text" width="200"><span class="uk-margin-small-left">BUILDINGS | UNITS</span></th>
					<th class="white-text" width="350">AUDITS | FINDINGS</th>
					<th width="200" class="white-text">ACTIONS</th>
				</tr>
			</thead>
			<tbody id="sent-document-list" style="font-size: 13px">
				@foreach ($documents as $document)
				@php
				$document_findings = $document->all_findings();
				$buildings = !is_null($document->building_ids) ? ($document->building_ids) : [];
				$units = !is_null($document->unit_ids) ? ($document->unit_ids) : [];
				// if(!is_array($units))
				// dd($document);
				$audits_ids = ($document->audits->pluck('id')->toArray());
				$document_finding_audit_ids = $document_findings->pluck('audit_id')->toArray();
				$all_ids = array_merge($audits_ids, $document_finding_audit_ids, [$document->audit_id]);
				$all_ids = collect($all_ids)->unique()->filter()->toArray();
				// $document_audits = $audits->whereIn('id', $all_ids);

				$site_findings = $document_findings->where('building_id', null)->where('unit_id', null);
				$building_findings = $document_findings->where('building_id', '<>', null)->where('unit_id', null);
				$unit_findings = $document_findings->where('building_id', null)->where('unit_id', '<>', null);

				$resolved_findings = count(collect($document_findings)->where('auditor_approved_resolution', 1));
				$unresolved_findings = count($document_findings) - $resolved_findings;

				// dd(count($site_findings));
				// $x = $unit_findings->where('unit.building_id', 24961);
				// $thisUnitFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file'));
				// $thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
				// dd($document_audits);
				@endphp
				<tr class="all @foreach ($document->audits as $audit)audit-{{ $audit->id }} @endforeach


					@foreach($document_findings as $finding)finding-{{ $finding->id }}



					@endforeach @foreach($document->assigned_categories as $category)category-{{ $category->id }} @endforeach


					@foreach($units as $unit)finding-{{ $unit }} @endforeach


					@foreach($buildings as $building)finding-{{ $building }} @endforeach">
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
						BUILDINGS {{ count($buildings) }} | UNITS {{ count($units) }}
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
				</tr>
				@endforeach
			</tbody>
		</table>
		<div id="documents-tab-pages-and-filters-2">
			{{ $documents->links() }}
		</div>
	</div>

</div>



<script type="text/javascript">

	$(document).ready(function(){
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		$('#documents-tab-pages-and-filters .page-link').click(function(){
			$('#local-documents').html(tempdiv);
			$('#allita-documents').load($(this).attr('href'));
			window.currentDocumentsPage = $(this).attr('href');
			return false;
		});

		$('#documents-tab-pages-and-filters-2 .page-link').click(function(){
			$('#local-documents').html(tempdiv);
			$('#allita-documents').load($(this).attr('href'));
			return false;
		});
		$('#local-documents-search').keydown(function (e) {
			if (e.keyCode == 13) {
				searchDocuments();
				e.preventDefault();
				return false;
			}
		});

		window.documentIds = "{{ $documents->pluck('id')->toJson() }}";
	});

	function searchDocuments(){
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		var unresolved = 'off';
		var resolved = 'off';
		var reviewed = 'off';
		var unreviewed = 'off';
		$('#local-documents').html(tempdiv);
		if($("#documents-unreviewed-checkbox").prop("checked") == true){
			unreviewed = "on";
		}
		if($("#documents-reviewed-checkbox").prop("checked") == true){
			reviewed = "on";
		}
		if($("#documents-findings-uncorrected-checkbox").prop("checked") == true){
			unresolved = "on";
		}
		if($("#documents-findings-corrected-checkbox").prop("checked") == true){
			resolved = "on";
		}
		$.post('{{ URL::route("documents.local-search", $project->id) }}', {
			'local-unresolved' : unresolved,
			'local-resolved' : resolved,
			'local-reviewed' : reviewed,
			'local-unreviewed' : unreviewed,
			'local-search' : $("#local-documents-search").val(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data==0){
				UIkit.modal.alert('I was not able to complete the search.');
			} else {
				//$('#usertop').trigger("click");
				$('#allita-documents').html(data);

			}
		} );
	}

    // process search
    $(document).ready(function() {
    	$('#users-search').keydown(function (e) {
    		if (e.keyCode == 13) {
    			searchUsers();
    			e.preventDefault();
    			return false;
    		}
    	});
    });



    function markApproved(id,catid, resolveFindings = 0){
    	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
    	if(resolveFindings == 0){
    		UIkit.modal.confirm("Are you sure you want to approve this file?").then(function() {
    			$.post('{{ URL::route("documents.local-approve", $project->id) }}', {
    				'id' : id,
    				'catid' : catid,
    				'resolve' : resolveFindings,
    				'_token' : '{{ csrf_token() }}'
    			}, function(data) {
    				if(data != 1 ) {
    					console.log("processing");
    					UIkit.modal.alert(data);
    				} else {
    					dynamicModalClose();
    				}
    				if(window.currentDocumentsPage) {
    					$('#local-documents').html(tempdiv);
    					$('#allita-documents').load(window.currentDocumentsPage);
    				} else {
    					documentsLocal('{{ $project->id }}');
    				}
    			}
    			);
    		});
    	}else{
    		UIkit.modal.confirm("<h1>Are You Sure?</h1><p>This will approve this file and resolve all unresolved findings attached to it with the date you select below.</p> <div uk-grid class=\"uk-text-small uk-grid\"><div class=\"uk-width-1-5 uk-first-column\"><span id=\"inspec-tools-finding-resolve-8552\"><button class=\"uk-button uk-link uk-margin-small-left uk-width-1-1\" uk-tooltip=\"pos:top-right;title:DATE\" ><i class=\"a-circle-cross\"></i>&nbsp; DATE</button></span></div><div class=\"uk-width-1-3\"><input id=\"document-"+id+"-resolved-date\" class=\"uk-input flatpickr-input active\" readonly=\"readonly\" type=\"text\" placeholder=\"DATE\" ></div><span id=\"resolved-text-8552\" class=\"uk-text-danger attention\" style=\"font-size: 15px\"></span></div>").then(function() {
    			$.post('{{ URL::route("documents.local-approve", $project->id) }}', {
    				'id' : id,
    				'catid' : catid,
    				'resolve' : $("#document-"+id+"-resolved-date").val(),
    				'_token' : '{{ csrf_token() }}'
    			}, function(data) {
    				if(data != 1 ) {
    					console.log("processing");
    					UIkit.modal.alert(data);
    				} else {
    					dynamicModalClose();
    				}
    				if(window.currentDocumentsPage) {
    					$('#local-documents').html(tempdiv);
    					$('#allita-documents').load(window.currentDocumentsPage);
    				} else {
    					documentsLocal('{{ $project->id }}');
    				}
    			}
    			);
    		});
    	}
    }

    function markUnreviewed(id,catid){
    	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
    	UIkit.modal.confirm("Are you sure you want to clear the review on this file?").then(function() {
    		$.post('{{ URL::route("documents.local-clearReview", $project->id) }}', {
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
    			if(window.currentDocumentsPage) {
    				$('#local-documents').html(tempdiv);
    				$('#allita-documents').load(window.currentDocumentsPage);
    			} else {
    				documentsLocal('{{ $project->id }}');
    			}
    		}
    		);
    	});
    }

    function markNotApproved(id,catid){
    	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
    	UIkit.modal.confirm("Are you sure you want to decline this file?").then(function() {
    		$.post('{{ URL::route("documents.local-notapprove", $project->id) }}', {
    			'id' : id,
    			'catid' : catid,
    			'_token' : '{{ csrf_token() }}'
    		}, function(data) {
    			if(data != 1){
    				UIkit.modal.alert(data);
    			} else {
    				dynamicModalClose();
    			}
    			if(window.currentDocumentsPage) {
    				$('#local-documents').html(tempdiv);
    				$('#allita-documents').load(window.currentDocumentsPage);
    			} else {
    				documentsLocal('{{ $project->id }}');
    			}
    		});
    	});
    }

    function deleteFile(id){
    	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
    	UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
    		$.post('{{ URL::route("documents.local-deleteDocument", $project->id) }}', {
    			'id' : id,
    			'_token' : '{{ csrf_token() }}'
    		}, function(data) {
    			if(data!= 1){
    				UIkit.modal.alert(data);
    			} else {
    			}
    			if(window.currentDocumentsPage) {
    				$('#local-documents').html(tempdiv);
    				$('#allita-documents').load(window.currentDocumentsPage);
    			} else {
    				documentsLocal('{{ $project->id }}');
    			}
    		});
    	});
    }

    function filterByAudit(){
    	console.log('ada');
    	$(".all").hide();
    	var myGrid = UIkit.grid($('#local-documents'), {
    		controls: '#local-documents-filters',
    		animation: false
    	});
    	var textinput = $("#document-filter-by-audit :selected").val();
    	$("."+textinput).show();
    }

    function filterElement(filterVal, filter_element){
    	if (filterVal === 'all') {
    		$(filter_element).show();
    	} else {
    		$(filter_element).hide().filter('.' + filterVal).show();
    	}
    	UIkit.update(event = 'update');
    }

	// function filterByAudit() {
	// 	filters = getSelectedFilters();
	// 	console.log(filters);
	// }

	function getSelectedFilters() {
		auditId = $('#document-filter-by-audit :selected').val();
		findingId = $('#document-filter-by-finding :selected').val();
		categoryId = $('#document-filter-by-category :selected').val();
		unitId = $('#document-filter-by-unit :selected').val();

		documentName = $('#documents-name-search').val();
		// debugger;
		$(".all").hide();

		// return;
		cssFilter = "";
		filter = '?';
		if(categoryId != "") {
			//filter = filter + '&filter_category_id='+categoryId;

			//cssFilter = cssFilter+' .' +categoryId;
			$("."+categoryId).show();
		}
		if(auditId != "") {
			filter = filter + '&filter_audit_id='+auditId;

			//hide other audits
			$('li[class*=audit-]').hide();

			//cssFilter = '.' +auditId;
			$("."+auditId).show();

		}
		if(unitId != "") {
			filter = filter + '&filter_unit_id='+unitId;
			$('li[class*=unit-]').hide();
			//if(cssFilter.length) { cssFilter = cssFilter+" , "; }
			//cssFilter = cssFilter+' .' +unitId;

			if(findingId != "" && findingId == 'finding-resolved'){
				$("."+unitId).show();
				$(".finding-unresolved").hide();
			} else if(findingId != "" && findingId == 'finding-unresolved'){
				$("."+unitId).show();
				$(".finding-resolved").hide();
			} else if(findingId != "") {
				$("."+findingId+", ."+unitId).show();
			} else {
				$("."+unitId).show();
			}
			console.log('showing unit '+unitId);
		}
		if(findingId != "" && unitId == "") {
			filter = filter + '&filter_finding_id='+findingId;
			//if(auditId.length) { cssFilter = cssFilter+" , "; }
			//cssFilter = cssFilter+' .' +findingId;
			$('li[class*=finding-]').hide();
			$("."+findingId).show();
		}

		// if(cssFilter.length) {
		// 	$(cssFilter).show();
		// 	console.log('Showing '+cssFilter);
		// }
		if(auditId == "" && findingId == "" && categoryId == "" && unitId== "") {
			$(".all").show();
			console.log('showing all');

		}

		// documentsLocal("{{ $project->id }}", "{{ $audit_id }}", filter);
		// return filter = [auditId, findingId, categoryId, documentName];
	}


	function openFindingDetails(findingId, documentId = 0) {
		dynamicModalLoad('finding-details/'+findingId+'/'+documentId);
	}

	function updateContent(updateClassContent, url, documentId) {
		var newContent = $('.'+updateClassContent);
		$(newContent).load('/'+url, function(response) {
			if (response == "error") {
				var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				UIkit.modal(msg, {center: true, bgclose: false, keyboard:false,  stack:true}).show();
			} else {
				$(newContent).html(response);
				if(documentId > 0)
				$('#document-'+documentId+'-findings').slideToggle();
			}
		});
	}

	@if(Auth::user()->auditor_access())
	function resolveFindingAS(findingid){
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data != 0){
				UIkit.notification({
					message: 'Marked finding as resolved',
					status: 'success',
					pos: 'top-right',
					timeout: 30000
				});
				$('#finding-resolve-button').html('<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON '+data.toUpperCase()+';" onclick="resolveFindingAS('+findingid+')"><span class="a-circle-checked">&nbsp; </span>RESOLVED</button>');
			}else{
				$("#finding-resolve-button").html('<span class="a-circle-checked"> </span> RESOLVED');
				UIkit.modal.dialog('<center style="color:green">Marked finding as resolved</center>');
			}
		});
	}
	@endif


</script>
