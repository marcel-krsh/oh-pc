<div class="uk-width-1-1">
<hr>			
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

									<option value="building-{{$uploadBuilding->id}}">BIN {{$uploadBuilding->building_name}} | @if($allFindings !== null) FINDINGS :: {{$allFindings->where('auditor_approved_resolution',NULL)->where('building_id',$uploadBuilding->id)->count()}} UNRESOLVED @else NO FINDINGS @endIf 
										</option>
										<option disabled>___________________________________________________________________</option>
										@if($uploadBuilding->units != NULL && count($uploadBuilding->units))
											@forEach($uploadBuilding->units->sortBy('unit_name') as $uploadUnit)

											<option value="unit-{{$uploadUnit->id}}"> --- UNIT {{$uploadUnit->unit_name}} | @if($allFindings !== null) FINDINGS :: {{$allFindings->where('auditor_approved_resolution',NULL)->where('unit_id',$uploadUnit->id)->count()}} UNRESOLVED  @else NO FINDINGS @endIf
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
										@if($allFindings != null) 
										@forEach($allFindings as $uploadFinding)
										<li class="all upload-finding audit-{{$uploadFinding->audit_id}} @if($uploadFinding->building_id != NULL) building-{{$uploadFinding->building_id}} @endIf @if($uploadFinding->unit_id != NULL) building-{{$uploadFinding->unit->building_id}} unit-{{$uploadFinding->unit_id}} @endIf finding-{{$uploadFinding->id}}   @if($uploadFinding->auditor_approved_resolution) finding-resolved @else finding-unresolved @endIf">
											<input style="float: left; margin-top: 3px" name="finding-id-checkbox" class="uk-checkbox document-category-selection" id="upload-finding-id-{{ $uploadFinding->id }}" value="{{ $uploadFinding->id }}" type="checkbox">
											<label style="display: block; margin-left: 30px" for="category-id-{{ $category->id }}">
												@if($uploadFinding->auditor_approved_resolution)<span>RESOLVED @else <span class=" attention" style="color:red"><strong>UNRESOLVED</strong> @endIf </span> | {{strtoupper($uploadFinding->finding_type->type)}} | @if($uploadFinding->building_id) BUILDING {{$uploadFinding->building->building_name}} @elseIf($uploadFinding->unit_id) UNIT {{$uploadFinding->unit->unit_name}}  @elseIf($uploadFinding->site) SITE @endIf | AUDIT # {{$uploadFinding->audit_id}} | FINDING # {{$uploadFinding->id}}<br /> {{$uploadFinding->amenity->amenity_description}}: {{$uploadFinding->level_description()}}  @if($uploadFinding->comments != NULL && count($uploadFinding->comments))<br />Auditor Comment: "{{$uploadFinding->comments->first()->comment}}"@endIf
												<hr />
											</label>
										</li>
										@endforeach
										@else
										<li><h2 class="gray-text">NO FINDINGS ON THIS PROJECT</h2></li>
										@endIf
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