<div uk-grid="" class="uk-margin-top uk-grid" id="document-filters" data-uk-button-radio="">
	<div class=" uk-width-1-1@s uk-width-1-1@m">
		<div uk-grid="">
			<hr class="dashed-hr uk-width-1-1">
			<div class="uk-width-1-1@s uk-width-1-5@m uk-padding-remove-left" id="audits-dropdown" style="vertical-align: top;">
				<select id="document-filter-by-audit" class="uk-select filter-drops uk-width-1-1" onchange="getSelectedFilters();" style="font-size:13px; padding:0px">
					<option value="" selected="">
						FILTER BY AUDIT
					</option><option disabled>___________________________________________________________________</option>
					@foreach($allAudits as $audit)
					<option value="audit-{{ $audit->id }}" {{ $filter['filter_audit_id'] == $audit->id ? 'selected=selected': '' }}>{{date('m/d/Y',strtotime($audit->inspection_schedule_date))}} AUDIT {{$audit->audit_id}} | FILE {{$audit->file_findings_count}} | NLT {{$audit->file_findings_count}} | LT {{$audit->file_findings_count}} | {{$audit->step_status_text}}</option><option disabled>___________________________________________________________________</option>
					@endforeach
				</select>
			</div>
			<div class="uk-width-1-1@s uk-width-1-5@m" id="units-dropdown" style="vertical-align: top;">
				<select id="document-filter-by-unit" class="uk-select filter-drops uk-width-1-1" onchange="getSelectedFilters();" style="font-size:13px; padding:0px">
					<option value="" selected="">
						FILTER BY UNIT
					</option><option disabled>___________________________________________________________________</option>
					@foreach($allUnits as $unit)
					<option value="unit-{{ $unit->id }}">UNIT {{ $unit->unit_name}} in {{$unit->building->building_name}}</option><option disabled>___________________________________________________________________</option>
					@endforeach
				</select>
			</div>
			<div class="uk-width-1-1@s uk-width-1-5@m" id="findings-dropdown" style="vertical-align: top;">
				<select id="document-filter-by-finding" class="uk-select filter-drops uk-width-1-1" onchange="getSelectedFilters();" style="font-size:13px; padding:0px">
					<option value="" selected="">
						FILTER BY FINDING
					</option><option disabled>___________________________________________________________________</option>
					<option value="finding-resolved">RESOLVED FINDINGS</option><option disabled>___________________________________________________________________</option>
					<option value="finding-unresolved">UNRESOLVED FINDINGS</option><option disabled>___________________________________________________________________</option>
					@foreach($findings as $finding)
					<option value="finding-{{ $finding->id }}" {{ $filter['filter_finding_id'] == $finding->id ? 'selected=selected': '' }}>@if($finding->auditor_approved_resolution)RESOLVED @else UNRESOLVED @endIf  | {{strtoupper($finding->finding_type->type)}} | @if($finding->building_id) BUILDING {{$finding->building->building_name}} @elseIf($finding->unit_id) UNIT {{$finding->unit->unit_name}}  @elseIf($finding->site) SITE @endIf | AUDIT # {{$finding->audit_id}} | FINDING # {{$finding->id}}<br /> {{$finding->amenity->amenity_description}}</option><option disabled>___________________________________________________________________</option>
					@endforeach
				</select>
			</div>
			<div class="uk-width-1-1@s uk-width-1-5@m" id="categories-dropdown" style="vertical-align: top;">
				<select id="document-filter-by-category" class="uk-select filter-drops uk-width-1-1" onchange="getSelectedFilters();" style="font-size:13px; padding:0px">
					<option value="" selected="">
						FILTER BY CATEGORY
					</option><option disabled>___________________________________________________________________</option>
					@foreach($categories as $category)
					<option value="category-{{ $category->id }}" {{ $filter['filter_category_id'] == $category->id ? 'selected=selected': '' }}>{{ $category->document_category_name }} : {{ $category->parent->document_category_name }}</option>
					<option disabled>___________________________________________________________________</option>
					@endforeach
				</select>
			</div>
			<div class=" uk-width-1-1@s uk-width-1-5@m ">
				<div class="uk-button uk-width-1-1 uk-button-success green-button active" onclick="$('.documents-upload-panel').slideToggle('slow');" style="max-height: 40px; height: 40px; overflow: hidden;" > <span class="documents-upload-panel"><i class="a-file-up" ></i> UPLOAD DOCUMENTS</span><span class="documents-upload-panel" style="display: none;"><i class="a-circle-cross" ></i> CANCEL UPLOAD</span></div>
			</div>
		</div>
	</div>
</div>

<div class="documents-upload-panel" style="display: none;" uk-grid>
		<div class="uk-width-1-1">
			
			<h2><i class="a-file-up"></i> DOCUMENT UPLOAD</h2>
			<hr class="dashed-hr uk-margin-bottom uk-width-1-1" />
		</div>
		<div class="uk-width-1-3@m uk-width-1-1 uk-margin-top" >
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1" style="border-right: 1px dotted gray;min-height: 500px;">

					<h2><span class="uk-icon-button uk-contrast" style="font-family: sans-serif; background-color: #005086;line-height: 31px;">1</span> <SMALL>SELECT DOCUMENT CATEGORY</SMALL></h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">

					<div uk-grid id="category-list" style="border:none; border-bottom: 1px dashed gray;height: 426px; border-radius: 0px;">
						<div class="uk-width-1-1 uk-margin-small-bottom">
							<ul class="uk-list document-category-menu"  style="font-size: 13px">
								<?php $currentGroup = ""; $opened = 0; ?>
								@foreach ($document_categories as $category)
								<?php 
								if($currentGroup !== $category->parent_category_name){
									if($opened == 1){
										echo "<li><hr /></li>";
									} else {
										$opened = 1;
									}
									echo "<li onclick=\"$('.child-of-".$category->parent_id."').slideToggle();\" class='use-hand-cursor'><h3><i class='a-circle-down'></i> ".$category->parent_category_name.":</h3></li>";
									$currentGroup = $category->parent_category_name;
								}
								?>
								<li class="child-of-{{$category->parent_id}}" style="display: none;">
									<input style="float: left; margin-top: 3px" name="category-id-checkbox" class="uk-radio document-category-selection" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio">
									<label style="display: block; margin-left: 30px" for="category-id-{{ $category->id }}">
										{{ $category->document_category_name }}
									</label>
								</li>
								@endforeach
							</ul>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="uk-width-1-3@m uk-width-1-1 uk-margin-top" >
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1" style="border-right: 1px dotted gray; min-height: 500px;">
					<h2><span class="uk-icon-button uk-contrast" style="font-family: sans-serif; background-color: #005086;line-height: 31px;">2</span> <SMALL>ASSIGN AUDIT, BIN, AND/OR UNIT</SMALL></h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
					<div class="document-upload-step-2"><h3 class="uk-align-center gray-text">PLEASE SELECT A DOCUMENT CATEGORY FIRST</h3></div>
					<div class="document-upload-step-2-selection" style="display: none;">
						<select class="uk-select filter-drops uk-width-1-1"><option>AUDIT (OPTIONAL)</option>
							@forEach($allAudits as $uploadAudit)
								<option>{{date('m/d/Y',strtotime($uploadAudit->inspection_schedule_date))}} AUDIT {{$uploadAudit->audit_id}} | FILE {{$uploadAudit->file_findings_count}} | NLT {{$uploadAudit->file_findings_count}} | LT {{$uploadAudit->file_findings_count}} | {{$uploadAudit->step_status_text}} </option>
								<option disabled>___________________________________________________________________</option>
							@endForEach
						</select>
						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						
						<select name="building_unit" class="uk-select filter-drops uk-width-1-1" value=""><option>BUILDING / UNIT  (OPTIONAL)</option>
							<option disabled>___________________________________________________________________</option>
								@forEach($allBuildings as $uploadBuilding)

									<option value="building-{{$uploadBuilding->id}}">BIN {{$uploadBuilding->building_name}} | FINDINGS :: {{$allFindings->where('auditor_approved_resolution',NULL)->where('building_id',$uploadBuilding->id)->count()}} UNRESOLVED 
										</option>
										<option disabled>___________________________________________________________________</option>
										@if($uploadBuilding->units != NULL && count($uploadBuilding->units))
											@forEach($uploadBuilding->units->sortBy('unit_name') as $uploadUnit)

											<option value="unit-{{$uploadUnit->id}}"> --- UNIT {{$uploadUnit->unit_name}} | FINDINGS :: {{$allFindings->where('auditor_approved_resolution',NULL)->where('unit_id',$uploadUnit->id)->count()}} UNRESOLVED    
												</option>
												<option disabled>___________________________________________________________________</option>
											@endForEach

										@endIf
								@endForEach

						</select>
						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						

							<div uk-grid  style="border:none; border-bottom: 1px dashed gray;height: 284px; border-radius: 0px;overflow-x: hidden;
    padding-top: 10px;">
								<div class="uk-width-1-1 uk-margin-small-bottom">
									<ul class="uk-list document-category-menu"  style="font-size: 13px">
										@forEach($allFindings as $uploadFinding)
										<li class="all upload-finding audit-{{$uploadFinding->audit_id}} @if($uploadFinding->building_id != NULL) building-{{$uploadFinding->building_id}} @endIf @if($uploadFinding->unit_id != NULL) building-{{$uploadFinding->unit->building_id}} unit-{{$uploadFinding->unit_id}} @endIf finding-{{$uploadFinding->id}}   @if($uploadFinding->auditor_approved_resolution) finding-resolved @else finding-unresolved @endIf">
											<input style="float: left; margin-top: 3px" name="finding-id-checkbox" class="uk-checkbox document-category-selection" id="upload-finding-id-{{ $uploadFinding->id }}" value="{{ $uploadFinding->id }}" type="checkbox">
											<label style="display: block; margin-left: 30px" for="category-id-{{ $category->id }}">
												@if($uploadFinding->auditor_approved_resolution)<span>RESOLVED @else <span class=" attention" style="color:red"><strong>UNRESOLVED</strong> @endIf </span> | {{strtoupper($uploadFinding->finding_type->type)}} | @if($uploadFinding->building_id) BUILDING {{$uploadFinding->building->building_name}} @elseIf($uploadFinding->unit_id) UNIT {{$uploadFinding->unit->unit_name}}  @elseIf($uploadFinding->site) SITE @endIf | AUDIT # {{$uploadFinding->audit_id}} | FINDING # {{$uploadFinding->id}}<br /> {{$uploadFinding->amenity->amenity_description}}: {{$uploadFinding->level_description()}}  @if($uploadFinding->comments != NULL && count($uploadFinding->comments))<br />Auditor Comment: "{{$uploadFinding->comments->first()->comment}}"@endIf
												<hr />
											</label>
										</li>
										@endforeach
									</ul>
									
								</div>
							</div>
							
						
					</div>
				</div>
			</div>
		</div>
		<div class="uk-width-1-3@m uk-width-1-1 uk-margin-top" style="min-height: 500px;">
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1">
					<h2><span class="uk-icon-button uk-contrast" style="font-family: sans-serif; background-color: #005086;line-height: 31px;">3</span> <SMALL>COMMENT & SELECT DOCUMENTS</SMALL></h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
					<div class="document-upload-step-2"><h3 class="uk-align-center gray-text">PLEASE SELECT A DOCUMENT CATEGORY FIRST</h3></div>
					<div class="document-upload-step-2-selection" style="display: none;">
						<div class="uk-align-center uk-margin-top">
							<textarea name="local-comment" id="local-comment" class="uk-textarea uk-width-1-1" placeholder="Enter a brief note about this document."></textarea>
						</div>
						<div class="uk-align-center uk-margin-top">
							<div id="list-item-upload-step-2" class="noclick">
								{{-- <div class="js-upload-noclick uk-placeholder"><center>Please select a category to upload a new document</center></div> --}}
								<div class="js-upload uk-placeholder uk-text-center">
									<span class="a-higher"></span>
									<span class="uk-text-middle"> Please upload your document by dropping it here or</span>
									<div uk-form-custom>
										<input type="file" multiple>
										<span class="uk-link uk-text-primary">by browsing and selecting it here.</span>
									</div>
								</div>
								<progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>
								<script>
									$(function(){
										var uploaddiv = document.getElementById('list-item-upload-step-2');
										$('.uk-radio').change(function() {
											var categoryArray = [];
											$("input:radio[name=category-id-checkbox]:checked").each(function(){
												categoryArray.push($(this).val());
											});
											var localComment = $("#local-comment").val();
											if(categoryArray.length > 0){
												uploaddiv.classList.remove('noclick');
											}else{
												uploaddiv.classList.add('noclick');
											}
										});
										var bar = document.getElementById('js-progressbar');
										// debugger;
										settings    = {
											url: '{{ URL::route("documents.local-upload", $project->id) }}',
											multiple: true,
											allow : '*.(jpg|jpeg|gif|png|pdf|doc|docx|xls|xlsx)',
											beforeSend: function () {
											},
											beforeAll: function (settings) {
												// debugger;
												var categoryArray = [];
												$("input:radio[name=category-id-checkbox]:checked").each(function(){
													categoryArray.push($(this).val());
												});
												settings.params.categories = categoryArray;
												settings.params.comment = $("#local-comment").val();
												settings.params.audit_id = "{{ $audit_id }}";
												settings.params._token = '{{ csrf_token() }}';
												if(categoryArray.length > 0){
													console.log('Categories selected: '+categoryArray);
												} else{
													UIkit.modal.alert('You must select at least one category.');
													return false;
												}
											},
											load: function () {
											},
											error: function () {
											},
											complete: function () {
											},
											loadStart: function (e) {
												bar.removeAttribute('hidden');
												bar.max = e.total;
												bar.value = e.loaded;
											},
											progress: function (e) {
												bar.max = e.total;
												bar.value = e.loaded;
											},
											loadEnd: function (e) {
												bar.max = e.total;
												bar.value = e.loaded;
											},
											completeAll: function (response) {
												var data = jQuery.parseJSON(response.response);
												var documentids = data['document_ids'];
												setTimeout(function () {
													bar.setAttribute('hidden', 'hidden');
												}, 250);
												documentsLocal('{{ $project->id }}', '{{ $audit_id }}');
											}
										};
										var select = UIkit.upload('.js-upload', settings);
									});
								</script>
							</div>
						</div>
						<p>Knowingly submitting incorrect documentation, request for reimbursements for expenses not incurred or those expenses where payment was received from another source, constitutes fraud and will be prosecuted to the fullest extent of the law.</p>
					</div>
				</div>
			</div><!--6-10-->
		</div>

<hr class="uk-width-1-1 uk-margin-bottom">
</div>

<div class="uk-grid uk-margin-top uk-animation-fade" id="local-documents">
	<div class="uk-width-1-1@m uk-width-1-1" id="local-documents-filter">
		<table class="uk-table uk-table-condensed gray-link-table" id="">
			<thead>
				<tr class="uk-text-small" style="background-color:#555;">
					<th class="white-text" style="padding-left: 10px;" width="100"> STORED</th>
					<th class="white-text" width="700"><span class="uk-margin-small-left">CATEGORY | FILE</span></th>
					<th class="white-text" width="200"><span class="uk-margin-small-left">BUILDING | UNIT</span></th>					
					<th class="white-text" width="350">AUDITS | FINDINGS</th>
					<th width="200" class="white-text">ACTIONS</th>
				</tr>
			</thead>
			<tbody id="sent-document-list" style="font-size: 13px">
				@foreach ($documents as $document)
				@php
				$document_findings = $findings->whereIn('id', $document->finding_ids);
				// dd($document_findings);
				$audits_ids = ($document->audits->pluck('id')->toArray());
				$document_finding_audit_ids = $document_findings->pluck('audit_id')->toArray();
				$all_ids = array_merge($audits_ids, $document_finding_audit_ids, [$document->audit_id]);
				$document_audits = $allAudits->whereIn('id', $all_ids);
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
				<tr class="all @foreach ($document_audits as $audit)audit-{{ $audit->id }} @endforeach @if($document->has_findings) @foreach($document_findings as $finding)finding-{{ $finding->id }} @endforeach @endif @foreach($document->assigned_categories as $category)category-{{ $category->id }} @endforeach @if($document->unit_id != NULL) unit-{{$document->unit_id}} @endIf">
						<td style="vertical-align: middle;"><span class="uk-margin-top uk-padding-left" uk-tooltip title="{{ $document->created_at->format('h:i a') }}">{{ date('m/d/Y', strtotime($document->created_at)) }}</span>
		    			</td>
						<td class="uk-width-1-2" style="vertical-align: middle;">
							<ul class="uk-list document-category-menu">
								@foreach ($document->assigned_categories as $document_category)
		    				{{-- @if(!is_null($document->ohfa_file_path))
		    				<li>
		    					<a>{{ $document_category->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}</a>
		    				</li>
		    				@else --}}
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
		    								<a onclick="resetDocTabCategoryListVars();selectCategory('{{ $document_category->id }}');">
		    									Select this category on right
		    								</a>
		    							</li>
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
		    				</li>
		    				{{-- @endif --}}
		    				@endforeach
		    			</ul>
		    		</td>
		    		
		    			<td style="padding-left: 10px">
		    				@if($document->unit_id != NULL)
		    					<?php $thisUnit = $units->where('id',$document->unit_id)->first(); ?>
		    						{{$thisUnit->building->building_name}} | {{$thisUnit->unit_name}}
		    					@else
		    					NA | NA
		    				@endIf
		    			</td>
		    			<td style="padding-left: 10px">
		    				<?php $unitAudits = $document_audits->pluck('id')->toArray(); ?>
		    				@if(count($unitAudits))
			    				<span  >@if(count($unitAudits) > 1) Audits: {{ implode(', ',$unitAudits) }} @elseIf(count($unitAudits)) Audit:{{ implode(', ',$unitAudits) }} @endIf</span> | 
			    				<span uk-tooltip="pos: right" title="@if($document->has_findings){{ implode(', ', $document_findings->pluck('id')->toArray()) }}@endif">
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
		    				@if($document->comment)&nbsp;|&nbsp;<a class="uk-link-muted " uk-tooltip="{{ $document->comment }}">
		    					<span class="a-file-info" style="color: #00538a;"></span>
		    				</a>
		    				@endif
		    			</td>
		    		</span>
		    	</span>
		    </tr>
		    @endforeach
		  </tbody>
		</table>
	</div>
</div>
	


<script type="text/javascript">
	$('.document-category-selection').on('click', function(){
		$('.document-upload-step-2').slideUp();
		$('.document-upload-step-2-selection').slideDown();
		
	});
	function markApproved(id,catid){
		UIkit.modal.confirm("Are you sure you want to approve this file?").then(function() {
			$.post('{{ URL::route("documents.local-approve", $project->id) }}', {
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
				documentsLocal('{{ $project->id }}');
			}
			);
		});
	}

	function markUnreviewed(id,catid){
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
				documentsLocal('{{ $project->id }}');
			}
			);
		});
	}

	function markNotApproved(id,catid){
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
				documentsLocal('{{ $project->id }}');
			});
		});
	}

	function deleteFile(id){
		UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
			$.post('{{ URL::route("documents.local-deleteDocument", $project->id) }}', {
				'id' : id,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!= 1){
					UIkit.modal.alert(data);
				} else {
				}
				documentsLocal('{{ $project->id }}');
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


	function openFindingDetails(findingId) {
		dynamicModalLoad('finding-details/'+findingId);
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
