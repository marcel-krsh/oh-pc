<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css{{ asset_version() }}">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
	/*for toggler*/
	.document-building-list, .document-unit-list {
		display: inline-block;
		width: 45%;
		vertical-align: text-top;
	}
	.document-building-list {
		margin-right: 10px;
		border-right: 1px solid black;
	}

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
					<th class="white-text" style="padding-left: 10px;" width="5%"> STORED</th>
					<th class="white-text" width="30%"><span class="uk-margin-small-left">CATEGORY | SUBCATEGORY</span></th>
					<th class="white-text" width="30%"><span class="uk-margin-small-left">BUILDINGS | UNITS</span></th>
					<th class="white-text" width="30%">AUDITS | FINDINGS</th>
					<th width="5%" class="white-text">ACTIONS</th>
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
				$unitCollection = !is_null($document->units) ? ($document->units) : collect([]);
				$buildingCollection = !is_null($document->buildings) ? ($document->buildings) : collect([]);
				

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
								<span id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}" class="">
									<span style="float: left;margin-top:8px;margin-left: 5px"  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }}"></span>
									<span style="float: left;margin-top:8px;margin-left: 5px" id="sent-id-{{ $document->id }}category-id-1-not-received-icon" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }}"></span>
									<span style="display: block; margin-left: 55px;margin-top:2px">{{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }} </span>
								</span>
								
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
						<span  >@if(count($all_ids) > 1) Audits: {{ implode(', ',$all_ids) }} @elseIf(count($all_ids)) Audit: {{ implode(', ',$all_ids) }} @endIf</span> |
						<span uk-tooltip="pos: right" title="@if(count($document_findings) > 0){{ implode(', ', $document_findings->pluck('id')->toArray()) }}@endif">
							<span onclick="$('#document-{{ $document->id }}-findings').slideToggle();" class="use-hand-cursor" uk-tooltip title="CLICK TO VIEW FINDING(S)">
								Total Findings: <span class="uk-badge finding-number {{ $unresolved_findings > 0 ? 'attention' : '' }} " uk-tooltip="" title="" aria-expanded="false"> {{ @count($document_findings) }}</span>
							</span>
						</span>

						<div id="document-{{ $document->id }}-findings" style="display: none;">
							{{-- @foreach($document_findings as $fin) --}}
							<hr class="uk-margin-bottom" style="border: 1px solid #bbbbbb" />
							<li>
								@include('non_modal.finding-summary')
							</li>
							{{-- @endforeach --}}
						</div>
						@else
						NA | NA
						@endIf
					</td>
					<td>
						
						
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
	window.projectDocumentsLoaded = 1;

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
		if(window.documentSearch != ''){
			$('#local-documents-search').val(window.documentSearch);
			window.documentSearch = '';
			searchDocuments();
		}
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
		$.post('{{ URL::route("pm-documents.local-search", $project->id) }}', {
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


	function openFindingDetails(findingId) {
		dynamicModalLoad('finding-details/'+findingId);
	}

	

</script>
