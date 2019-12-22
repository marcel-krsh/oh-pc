
<div class="uk-grid uk-margin-top uk-animation-fade">
	<div class="uk-width-3-5@m uk-width-1-1 ">
		<table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
			<thead>
				<tr class="uk-text-small" style="color:#fff;background-color:#555;">
					<th>CATEGORY: FILE</th>
					<th>STORED</th>
					<th>MODIFIED</th>
					<th width="110">ACTIONS</th>
				</tr>
			</thead>
			<tbody id="sent-document-list" style="font-size: 13px">
				@foreach ($documents as $document)
				<tr>
					<span uk-grid>
						<td class="uk-width-1-2" style="vertical-align: middle;">
							<ul class="uk-list document-category-menu">
			    			@foreach ($document->assigned_categories as $document_category)
			    			{{-- @if(!is_null($document->ohfa_file_path))
			    				<li>
			    					<a>{{ $document_category->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}</a>
			    				</li>
			    			@else --}}
			    			<li class="{{ ($document->notapproved == 1) ? "declined-category s" : "" }} {{ ($document->approved == 1) ? "approved-category" : "" }}">
			    				<a id="sent-id-{{ $document->id }}-category-id-{{ $document_category->id }}" class="">
			    					<span  id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ ($document->approved == 1) ? "received-yes uk-float-left" : "check-received-no received-no" }}"></span>
			    					<span style="float: left;" id="sent-id-{{ $document->id }}category-id-1-not-received-icon" class="{{ ($document->notapproved == 1) ? "a-circle-cross alert" : "a-checkbox" }} {{ ($document->approved == 1) ? " minus-received-yes received-yes" : "received-no" }}"></span>
			    					<span style="display: block; margin-left: 30px">{{ $document_category->parent->document_category_name }} : {{ $document_category->document_category_name }} </span>
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
			    	<span class="uk-width-2-2">
			    	<td><span uk-tooltip title="{{ $document->created_at->format('h:i a') }}">{{ date('m/d/Y', strtotime($document->created_at)) }}</span>
			    	</td>
			    	<td><span uk-tooltip title="{{ date('h:i a', strtotime($document->updated_at)) }}">{{ date('m/d/Y', strtotime($document->updated_at)) }}</span>
			    	</td>
			    	<td>
			    		<a class="uk-link-muted " uk-tooltip="@foreach($document->assigned_categories as $document_category){{ $document_category->parent->document_category_name }}/{{ $document_category->document_category_name }}@endforeach">
	              <span class="a-info-circle"  style="color: #56b285;"></span>
	            </a>
	            &nbsp;|&nbsp;
	            <a class="uk-link-muted " onclick="dynamicModalLoad('edit-local-document/{{$document->id}}')" uk-tooltip="Edit this file">
	             		<span class="a-pencil-2" style="color: rgb(0, 193, 247);"></span>
	            </a>
	            &nbsp;|&nbsp;
			    	 @can('access_admin')
			    		<a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
			    			<span class="a-trash-4" style="color: #da328a;"></span>
			    		</a>
			    		&nbsp;|&nbsp;
			    	 @endcan
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
	</div><!--4-10-->
	<div class="uk-width-2-5@m uk-width-1-1">
		<div class="uk-grid-collapse" uk-grid>
			<div class="uk-width-1-1">
				<p class="blue-text">Click on the <span class="a-higher"></span> icon in the document listed to the left to automatically select categories for that document.</p>
				<div uk-grid id="category-list">
					<div class="uk-width-1-1 uk-margin-small-bottom">
						<ul class="uk-list document-category-menu"  style="font-size: 13px">
							@foreach ($document_categories as $category)
							<li>
								<input style="float: left; margin-top: 3px" name="category-id-checkbox" class="uk-radio" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio">
								<label style="display: block; margin-left: 30px" for="category-id-{{ $category->id }}">
									{{ $category->parent->document_category_name }} : {{ $category->document_category_name }}
								</label>
							</li>
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
									allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',
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
										documentsLocal('{{$project->id}}');
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
				documentsLocal('{{$project->id}}');
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
				documentsLocal('{{$project->id}}');
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
					documentsLocal('{{$project->id}}');
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
					documentsLocal('{{$project->id}}');
				});
			});
		}
	</script>
