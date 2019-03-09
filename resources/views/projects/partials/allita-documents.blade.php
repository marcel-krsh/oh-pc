<template class="uk-hidden" id="category-list-template">
	<div class="uk-width-1-1 uk-margin-small-bottom">
		<input name="category-id-x-y" id="category-id-x-y" type="a-checkbox">
		<label for="category-id-x">
			Category Name
		</label>
	</div>
</template>
<template class="uk-hidden" id="sent-document-list-template">
	<tr>
		<td>10/10/10</td>
		<td><ul class="uk-subnav document-category-menu">Categories</ul></td>
		<td><a class="uk-link-muted" onclick="newEmailRequest('2');"><span class="a-checkbox"></span>&nbsp;&nbsp;|&nbsp;&nbsp;</a><a onclick="resetDocTabCategoryListVars();selectCategory('2')" uk-tooltip="Select all categories listed for this document group that was uploaded."><span class="a-higher"></span></a></td>
	</tr>
</template>
<template id="document-list-template" class="uk-hidden">
	<tr class="">
		<td>Upload date</td>
		<td>type</td>
		<td>Staff name</td>
		<td>Categories</td>
		<td><a class="uk-link-muted " onclick="deleteDocument(123)"><span class="a-trash-4"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" target="_blank"><span class="a-lower"></span></a></td>
	</tr>
</template>{{--
<script>
	$('.detail-tab-1-text').html('<span class="a-home-2"></span> PARCEL: {{ $project->parcel_id }} :: Documents ');
	$('#main-option-text').html('Parcel: {{ $project->parcel_id }}');
	$('#main-option-icon').attr('uk-icon','arrow-circle-o-left');
	var subTabType = window.subTabType;
	if(subTabType == 'documents'){
		delete window.subTabType;
		$('#parcel-subtab-1').attr("aria-expaned", "false");
		$('#parcel-subtab-1').removeClass("uk-active");
		$('#parcel-subtab-2').attr("aria-expaned", "true");
		$('#parcel-subtab-2').addClass("uk-active");
	}
</script> --}}
<div class="uk-grid uk-margin-top uk-animation-fade">
	<div class="uk-width-3-5@m uk-width-1-1 ">
		<table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
			<thead>
				<tr class="uk-text-small" style="color:#fff;background-color:#555;">
					<th>CLASS: DESCRIPTION</th>
					<th>TYPE</th>
					<th>STORED</th>
					<th>MODIFIED</th>
					<th width="110">ACTIONS</th>
				</tr>
			</thead>
			<tbody id="sent-document-list">
				@foreach ($documents as $document)
				<?php
				if($document->categories){
             //$listcats = implode(",", json_decode($document->categories, true));
					$listcats = $document->categories->lists('id');
				}else{
					$listcats = '';
				}
				?>
				<tr>
					<td style="vertical-align: middle;">
						<ul class="uk-list document-category-menu">
		    		{{-- See if doc is approved or declined
		    			* allita_document_approval = null (none)
		    			* allita_document_approval == 0 (declined) //think twice
		    			* allita_document_approval == 1 (approved)
		    			--}}
		    			@foreach ($document->assigned_categories as $document_category)
		    			<li class="{{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
		    				<a id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}" class="">
		    					<span id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes" : "check-received-no received-no" }}"></span>
		    					<span id="sent-id-{{ $document->id }}category-id-1-not-received-icon" class="{{ ($document->notapproved == 0) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }}"></span>
		    					{{ ucwords(strtolower($document->filename)) }}
		    				</a>
		    				<div uk-dropdown="toggle: #sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}">
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
		    			@endforeach
		    		</ul>
		    	</td>
		    	<td>{{ $document->filename }}</td>
		    	<td><span uk-tooltip title="{{ date('g:h a', strtotime($document->created_at)) }}">{{ date('m/d/Y', strtotime($document->created_at)) }}</span>
		    	</td>
		    	<td><span uk-tooltip title="{{ date('g:h a', strtotime($document->updated_at)) }}">{{ date('m/d/Y', strtotime($document->updated_at)) }}</span>
		    	</td>
		    	<td>
		    		@can('access_admin')
		    		<a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
		    			<span class="a-trash-4"></span>
		    		</a>
		    		&nbsp;&nbsp;|&nbsp;&nbsp;
		    		@endcan
		    		<?php $url = "/document/{$document->id}"; ?>
		    		<a href="{{ $url }}" target="_blank"  uk-tooltip="Download file.">
		    			<span class="a-lower"></span>
		    		</a>
		    		@if($document->comment)&nbsp;&nbsp;| &nbsp;&nbsp;<a class="uk-link-muted " uk-tooltip="{{ $document->comment }}">
		    			<span class="a-file-info"></span>
		    		</a>
		    		@endif
		    	</td>
		    </tr>
		    @endforeach
		  </tbody>
		</table>
	</div><!--4-10-->
	<div class="uk-width-2-5@m uk-width-1-1">
		<div class="uk-grid-collapse" uk-grid>
			<div class="uk-width-1-1">
				<p class="blue-text">Click on the <span class="a-higher"></span> icon in the document listed to the left to automatically select categories for that document.</p>
				<div uk-grid id="category-list">
					<div class="uk-width-1-1 uk-margin-small-bottom">
						<ul class="uk-list document-category-menu">
							@foreach ($document_categories as $category)
							@if($category->active == 1)
							<li>
								<input name="category-id-checkbox" class="uk-radio" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio">
								<label for="category-id-{{ $category->id }}">
									{{ $category->document_category_name }}
								</label>
							</li>
							@endif
							@endforeach
						</ul>
						<div>
							<small>OTHER CATEGORIES THAT ARE PROBABLY NOT NEEDED</small>
							<hr class="uk-margin-bottom" />
						</div>
						<ul class="uk-list document-category-menu">
							<li>
								<input name="category-id-checkbox" class="uk-checkbox" id="category-id-0" value="0" type="checkbox">
								<label for="category-id-0">
									Category TBD
								</label>
							</li>
						</ul>
					</div>
				</div>
				<div class="uk-align-center uk-margin-top">
						<label for="comment">Comment:</label>
						<textarea name="local-comment" id="local-comment" class="uk-textarea uk-width-1-1" placeholder="Enter a breif note about this document."></textarea>
					</div>
				<div class="uk-align-center uk-margin-top">
					<div id="list-item-upload-step-2" class="noclick">
						<div class="js-upload-noclick uk-placeholder"><center>Please select a category to upload a new document</center></div>
						<div class="js-upload uk-placeholder uk-text-center">
							<span class="a-higher"></span>
							<span class="uk-text-middle"> Please upload your document by dropping it here or</span>
							<div uk-form-custom>
								<input type="file">
								<span class="uk-link">by browsing and selecting it here.</span>
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
								settings    = {
									url: '{{ URL::route("documents.allita-upload", $project->id) }}',
									multiple: true,
									allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',
									beforeSend: function () {
									},
									beforeAll: function (settings) {
										var categoryArray = [];
										$("input:radio[name=category-id-checkbox]:checked").each(function(){
											categoryArray.push($(this).val());
										});
										settings.params.categories = categoryArray;
										settings.params.comment = $("#local-comment").val();
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
										var is_retainage = data['is_retainage'];
										var is_advance = data['is_advance'];
										setTimeout(function () {
											bar.setAttribute('hidden', 'hidden');
										}, 250);
										if(is_retainage == 0 && is_advance == 0){
											UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'').then(function(val){
												$.post('{{ URL::route("documents.uploadComment", $project->id) }}', {
													'postvars' : documentids,
													'comment' : val,
													'_token' : '{{ csrf_token() }}'
												}, function(data) {
													if(data!='1'){
														UIkit.modal.alert(data);
													} else {
														UIkit.modal.alert('Your comment has been saved.');
														loadParcelSubTab('documents',{{ $project->id }});
													}
												});
											});
										}else if(is_retainage == 1){
											dynamicModalLoad('document-retainage-form/{{ $project->id }}/'+documentids);
										} else if(is_advance == 1){
											dynamicModalLoad('document-advance-form/{{ $project->id }}/'+documentids);
										}
										loadParcelSubTab('documents',{{ $project->id }});
									}
								};
								var select = UIkit.upload('.js-upload', settings);
							});
						</script>
					</div>
				</div>
				<p>Knowingly submitting incorrect documentation, request for reimbursements for expenses not incurred or those expenses where payment was received from another source, constitutes fraud and will be prosecuted to the fullest extent of the law.</p>
			</div>
		</div><!--6-10-->
	</div>
</div>
	<script type="text/javascript">
		function markApproved(id,catid){
			UIkit.modal.confirm("Are you sure you want to approve this file?").then(function() {
				$.post('{{ URL::route("documents.approve", $project->id) }}', {
					'id' : id,
					'catid' : catid,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data!='1'){ console.log("processing");
					UIkit.modal.alert(data);
				} else {
				}
				loadParcelSubTab('documents',{{ $project->id }});
			}
			);
			});
		}

		function markUnreviewed(id,catid){
			UIkit.modal.confirm("Are you sure you want to clear the review on this file?").then(function() {
				$.post('{{ URL::route("documents.clearReview", $project->id) }}', {
					'id' : id,
					'catid' : catid,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data!='1'){ console.log("processing");
					UIkit.modal.alert(data);
				} else {
				}
				loadParcelSubTab('documents',{{ $project->id }});
			}
			);
			});
		}

		function markNotApproved(id,catid){
			UIkit.modal.confirm("Are you sure you want to decline this file?").then(function() {
				$.post('{{ URL::route("documents.notapprove", $project->id) }}', {
					'id' : id,
					'catid' : catid,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data!='1'){
						UIkit.modal.alert(data);
					} else {
						UIkit.modal.alert('The document is not approved.');
					}
					loadParcelSubTab('documents',{{ $project->id }});
				});
			});
		}

		function deleteFile(id){
			UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
				$.post('{{ URL::route("documents.deleteDocument", $project->id) }}', {
					'id' : id,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data!='1'){
						UIkit.modal.alert(data);
					} else {
					}
					loadParcelSubTab('documents', {{ $project->id }} );
				});
			});
		}
	</script>