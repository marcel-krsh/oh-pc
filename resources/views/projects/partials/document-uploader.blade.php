<style type="text/css">
	.main-document-category-name {
		display: inline-block;
    width: 358px;
    vertical-align: top;
	}
				.help-badge {
					    box-sizing: border-box;
					    min-width: 22px;
					    height: 22px;
					    padding: 0 5px;
					    border-radius: 500px;
					    vertical-align: middle;
					    background:  #6aa26a;;
					    color: #fff;
					    font-size: .875rem;
					    display: inline-flex;
					    justify-content: center;
					    align-items: center;
				}
				/*for toggler*/
				#document-upload .switch {
				  position: relative;
				  display: inline-block;
				  width: 52px;
				  height: 26px;
				}

				#document-upload .switch input { 
				  opacity: 0;
				  width: 0;
				  height: 0;
				}

				#document-upload .slider {
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

				#document-upload .slider:before {
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

				#document-upload input:checked + .slider {
				  background-color: #005186;
				}

				#document-upload input:focus + .slider {
				  box-shadow: 0 0 1px #005186;
				}

				#document-upload input:checked + .slider:before {
				  -webkit-transform: translateX(26px);
				  -ms-transform: translateX(26px);
				  transform: translateX(26px);
				}

				/* Rounded sliders */
				#document-upload .slider.round {
				  border-radius: 34px;
				}

				#document-upload .slider.round:before {
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
<div uk-grid>
<div class="uk-width-1-1">
<hr>			
			<h2><i class="a-file-up"></i> DOCUMENT UPLOAD</h2>
			<hr class="dashed-hr uk-margin-bottom uk-width-1-1" />
		</div>
		<div class="uk-width-1-3@m uk-width-1-1 uk-margin-top" >
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1" style="border-right: 1px dotted gray;min-height: 500px;">

					<h2><span class="uk-icon-button uk-contrast" style="font-family: sans-serif; background-color: #005086;line-height: 31px;">1</span> <SMALL>SELECT DOCUMENT CATEGORY </SMALL> <div class="uk-badge uk-text-right " style="    background: #6aa26a;"><a uk-tooltip title="WHAT DOES STEP 1 DO?"  class="uk-dark uk-light use-hand-cursor" style="    position: relative; top: -5px; margin-right: 0.85px; padding-left: 0px;font-size: 15px;" aria-expanded="false" onclick="UIkit.modal.alert('<div class=\'uk-grid\'><div class=\'uk-width-1-1 uk-margin-bottom\'><h1>Step 1 Assigns a Category to the Document:</h1></div><div class=\'uk-width-1-2\'><h3><strong>A Document Category is required for every document uploaded.</strong></h3><h3>How to Select Your Category: <ul><li>Only one document category can be assigned per document, selecting a second category automatically unselects the first</li><li>Find the top level category for your document</li><li>Click on its name to view the subcategories</li><li>From the expanded list click on the radio button to select your document category</li><li>The other two steps are now available.</li></ul></h3></div><div class=\'uk-width-1-2\'><img src=\'/help_graphics/selecting_category.gif\'></div></div>');">? 
						</a></div></h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">

					<div id="upload-category-list" class="uk-width-1-1" style="height: 735px; border:none; border-bottom: 1px dashed gray;  border-radius: 0px;overflow-x: hidden;
    padding-top: 10px;"><div uk-grid>
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
										echo "<li onclick=\"$('.child-of-".$category->parent_id."').slideToggle();\" class='use-hand-cursor'><h3><i class='a-circle-down'></i> <div class='main-document-category-name'>".$category->parent_category_name."</div></h3></li>";
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
		</div>
		<div id="document-upload-form-top" class="uk-width-1-3@m uk-width-1-1 uk-margin-top" >
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1" style="border-right: 1px dotted gray; min-height: 500px;">
					<h2><span class="uk-icon-button uk-contrast" style="font-family: sans-serif; background-color: #005086;line-height: 31px;">2</span> <SMALL>ASSIGN AUDIT, BIN/UNIT, OR FINDINGS </SMALL><div class="uk-badge uk-text-right " style="    background: #6aa26a;">
						<a uk-tooltip="" title="WHAT DOES STEP 2 DO?" onclick="UIkit.modal.alert('<h1>Step 2 Completes One of Two Optional Actions:</h1><h3><strong>1. Uploading Documentation for File Review:</strong> <ul><li>This allows you to assign the document to a specific audit, building, and or unit.</li></ul><strong>2. Resolving a Finding:</strong><ul><li>You can use the drop downs to narrow the list of findings to a specific audit, building, unit and resolved status. When findings are selected, the auidt, building, and unit data associated with the findings is used.</li></ul>Documents for File Review can only be assigned to a single building or unit. Documents for Finding Resolution, however, can be assinged to multiple findings.</h3>');" class="uk-dark uk-light use-hand-cursor" style="    position: relative; top: -5px; margin-right: 0.85px; padding-left: 0px;font-size: 15px;" aria-expanded="false">? 
						</a>
					</div>
				</h2>
					<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
					<div class="document-upload-step-2"><h3 class="uk-align-center gray-text">PLEASE SELECT A DOCUMENT CATEGORY FIRST</h3></div>
					<div class="document-upload-step-2-selection" style="display: none;">
						<select name="audit" id="upload-document-audit" class="uk-select filter-drops uk-width-1-1"><option value="reset">AUDIT (OPTIONAL)</option>
							@forEach($allAudits as $uploadAudit)
								<option value="{{$uploadAudit->audit_id}}">{{date('m/d/Y',strtotime($uploadAudit->inspection_schedule_date))}} AUDIT {{$uploadAudit->audit_id}} | FILE {{$uploadAudit->file_findings_count}} | NLT {{$uploadAudit->nlt_findings_count}} | LT {{$uploadAudit->lt_findings_count}} | {{$uploadAudit->step_status_text}} </option>
								<option disabled>___________________________________________________________________</option>
							@endForEach
						</select>
						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						
						<select name="building_unit" id="upload-document-building-unit"class="uk-select filter-drops uk-width-1-1" value=""><option value="reset">BUILDING / UNIT  (OPTIONAL)</option>
							<option disabled>___________________________________________________________________</option>
								@forEach($allBuildings as $uploadBuilding)
									<option disabled>|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||</option>
									<option value="building-{{$uploadBuilding->id}}">BIN {{$uploadBuilding->building_name}} | @if($allFindings !== null) FINDINGS :: {{$allFindings->where('auditor_approved_resolution',NULL)->where('building_id',$uploadBuilding->id)->count()}} UNRESOLVED @else NO FINDINGS @endIf 
										</option>
										<option disabled>|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||</option>
										@if($uploadBuilding->units != NULL && count($uploadBuilding->units))
											@forEach($uploadBuilding->units->sortBy('unit_name') as $uploadUnit)

											<option id="document-upload-unit-{{$uploadUnit->id}}-select" value="unit-{{$uploadUnit->id}}">UNIT {{$uploadUnit->unit_name}} | @if($allFindings !== null) FINDINGS :: {{$allFindings->where('auditor_approved_resolution','<>',1)->where('unit_id',$uploadUnit->id)->count()}} UNRESOLVED  @else NO FINDINGS @endIf
												</option>
												<option disabled>___________________________________________________________________</option>
											@endForEach

										@endIf
								@endForEach


						</select>
						<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
						<select name="unresolved" id="upload-document-resolution" class="uk-select filter-drops uk-width-1-1" value="">
															<option value="reset">FINDING STATUS (OPTIONAL)</option>
															<option value="on">ONLY UNRESOLVED</option>
															<option value="off">ONLY RESOLVED</option>
															<option value="both">BOTH RESOLVED &amp; UNRESOLVED</option>
														</select>
														<hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
														<a class="uk-button uk-button-success uk-button-small uk-width-1-1 uk-link-mute"  onclick="filterUploaderFindings();" style="">
														<span>VIEW MATCHING FINDINGS</span>
													</a>
													<hr />
						
							<div uk-grid  style="border:none; border-bottom: 1px dashed gray;height:510px; border-radius: 0px;overflow-x: hidden; max-height: 452px; min-height: 452px; padding-top: 10px;">
								<div class="uk-width-1-1 uk-margin-small-bottom">
									<ul id="document-upload-findings-list" class="uk-list document-category-menu"  style="font-size: 13px">
										
											
												<li class="all" ><h3>Attaching findings?</h3><p> You can use the above drop down options for Audit, Building/Unit, and Finding Status to filter narrow your selection, or just click "VIEW MATCHING FINDINGS" to view them all.</p>
												
													
													</a>
													<hr />
												</li>
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
						<div id="document-findings-box">
							<h2 style="display: none">ASSOCIATED FINDINGS</h2>
							<div id='finding-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox finding-selector"><span class='finding-name'></span>
							</div>
						</div>
						<p>Knowingly submitting incorrect, altered or falsified documentation constitutes fraud and will be prosecuted to the fullest extent of the law.</p>
					</div>
				</div>
			</div><!--6-10-->
		</div>

<hr class="uk-width-1-1 uk-margin-bottom">
</div>

<script type="text/javascript">
	
	$('.document-category-selection').on('click', function(){
		$('.document-upload-step-2').slideUp();
		$('.document-upload-step-2-selection').slideDown();
		
	});



    function filterUploaderFindings(){
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		var unresolved = 'off';
		var id = 'document-upload-form-top';
		var yourElement = document.getElementById(id);
		var y = yourElement.getBoundingClientRect().top + window.pageYOffset;

		window.scrollTo({top: y - 63});
		
		$('#document-upload-findings-list').html(tempdiv);
		
		unresolved =  $("#upload-document-resolution").val();
		
		
		//alert(unresolved);
		$.post('{{ URL::route("documents.upload-finding-filter", $project->id) }}', {
			'document-upload-unresolved' : unresolved,
			
			'document-upload-audit' : $("#upload-document-audit").val(),
			'document-upload-building-unit' : $("#upload-document-building-unit").val(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {

			if(data==0){
				UIkit.modal.alert('I was not able to complete the search.'+data);
			} else {
				//$('#usertop').trigger("click");
				$('#document-upload-findings-list').html(data);

				

			}
		} );
	}

	function addFinding(formValue,name){
    //alert(formValue+' '+name);
    if($("#list-finding-id-"+formValue).is(':checked')){
    	var recipientClone = $('#finding-template').clone();
    	recipientClone.attr("id", "finding-id-"+formValue+"-holder");
    	recipientClone.prependTo('#document-findings-box');

    	$("#finding-id-"+formValue+"-holder").slideDown();
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("id","finding-id-"+formValue);
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("name","findings[]");
    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeFinding("+formValue+");");

    	$("#finding-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
    	$("#finding-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
    } else {
    	$("#finding-id-"+formValue+"-holder").slideUp();
    	$("#finding-id-"+formValue+"-holder").remove();
    }
    updateMessage();
  }

  function removeFinding(id){
  	$("#finding-id-"+id+"-holder").slideUp();
  	$("#finding-id-"+id+"-holder").remove();
  	$("#list-finding-id-"+id).prop("checked",false)
    updateMessage();
  }


	
</script>