	<div class="uk-grid uk-margin-top uk-animation-fade">
		<div class="uk-width-5-5@m uk-width-1-1 uk-scrollable-box" style="height:400px;">
			<table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
				<thead >
					<tr class="uk-text-small" style="color:#fff;background-color:#555;">
						<th>CLASS: DESCRIPTION</th>
						<th>TYPE</th>
						<th>STORED</th>
						<th>MODIFIED</th>

						<th width="110">ACTIONS</th>
					</tr>
				</thead>
				<tbody id="sent-document-list">
					@php $documentReset = $documents; @endphp
					@foreach ($documents as $document)
					<tr class="{{strtolower(str_replace(' ','-',$document->document_class))}} {{str_replace(' ','-',strtolower($document->document_description))}}">
						<td>
							{{ucwords(strtolower($document->document_class))}} :
							{{ucwords(strtolower($document->document_description))}}
						</td>
						<td>
							<!-- FROM -->
							{{$document->dw_extension}}
						</td>
						<td><span uk-tooltip title="{{ date('g:h a', strtotime($document->dw_stored_date_time)) }}">{{ date('m/d/Y', strtotime($document->dw_stored_date_time)) }}</span></td>
						<td><span uk-tooltip title="{{ date('g:h a', strtotime($document->dw_mod_date_time)) }}">{{ date('m/d/Y', strtotime($document->dw_mod_date_time)) }}
						</td>
						<td>
							@if($admin_access)
							<a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
								<span class="a-trash-4"></span>
							</a>
							&nbsp;&nbsp;|&nbsp;&nbsp;
							@endIf
							@if($document->dw_extension == '.dwtiff' || $document->dw_extension == '.tif' || $document->dw_extension == '.DWTIFF' || $document->dw_extension == '.tiff' || $document->dw_extension == '.TIF' ||$document->dw_extension == '.TIFF' )
							<?php $url = "http://docuware/DocuWare/Platform/WebClient/NTLM/1/Integration?fc={$document->cabinet_id}&did={$document->docuware_doc_id}&p=V";
							?>
							@else
							<?php $url = "/document/{$document->docuware_doc_id}"; ?>
							@endif
							<a href="{{$url}}" target="_blank"  uk-tooltip="Download file.">
								<span class="a-lower"></span>
							</a>
							@if($document->notes)&nbsp;&nbsp;| &nbsp;&nbsp;<a class="uk-link-muted " uk-tooltip="{{ $document->notes }}">
								<span class="a-file-info"></span>
							</a>
							@endif
						</td>
					</tr>
					@endforeach


				</tbody>
			</table>

		</div><!--4-10-->

		{{-- <div class="uk-width-2-5@m uk-width-1-1">
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-1-1">

					<div uk-grid id="category-list">
						<div class="uk-width-1-1 uk-margin-small-bottom">
							<ul class="uk-list document-category-menu uk-scrollable-box">
								@php $currentParent = ''; @endphp
								@foreach ($document_categories as $category)
								@if($currentParent != $category->parent_id)
								<li class="uk-margin-top-large"><strong>{{ucwords(strtolower($category->parent->document_category_name))}}</strong><br /><hr class="dashed-hr" /></li>
								@php $currentParent = $category->parent_id; @endphp
								@endIf
								<li>
									<input name="category-id-checkbox" class="uk-radio" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio">
									<label for="category-id-{{ $category->id }}">
										{{ ucwords(strtolower($category->document_category_name)) }}
									</label>
								</li>
								@endforeach
							</ul>
						</div>

					</div>
					<div class="uk-align-center uk-margin-top">
						<label for="notes">NOTES:</label>
						<textarea class="uk-textarea uk-width-1-1" placeholder="Enter a brief note about this document."></textarea>
					</div>
					<div class="uk-align-center uk-margin-top">
						<div id="list-item-upload-step-2">

							<div class="js-upload uk-placeholder uk-text-center">
								<span class="a-higher"></span>
								<span class="uk-text-middle"> Please upload your document by dropping it here or</span>
								<div uk-form-custom>
									<input type="file" multiple>
									<span class="uk-link">by browsing and selecting it here.</span>
								</div>
							</div>

							<progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

							<script>
								$(function(){
									var bar = document.getElementById('js-progressbar');

									settings    = {

										url: '{{ URL::route("documents.upload", $project->id) }}',
										multiple: true,
										allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',

										headers : {
											'enctype' : 'multipart/form-data'
										},

										beforeSend: function () {
	                      // console.log('beforeSend', arguments);
	                    },
	                    beforeAll: function (settings) {
	                      // console.log('beforeAll', arguments);
	                      var categoryArray = [];
	                      $("input:checkbox[name=category-id-checkbox]:checked").each(function(){
	                      	categoryArray.push($(this).val());
	                      });
	                      settings.params.categories = categoryArray;
	                      settings.params._token = '{{ csrf_token() }}';
	                      if(categoryArray.length > 0){
	                      	console.log('Categories selected: '+categoryArray);


	                      }else{
	                      	UIkit.modal.alert('You must select at least a category.');
	                      	return false;
	                      }
	                    },
	                    load: function () {
	                      // console.log('load', arguments);
	                    },
	                    error: function () {
	                      // console.log('error', arguments);
	                    },
	                    complete: function () {
	                      // console.log('complete', arguments);
	                    },

	                    loadStart: function (e) {
	                      // console.log('loadStart', arguments);

	                      bar.removeAttribute('hidden');
	                      bar.max = e.total;
	                      bar.value = e.loaded;
	                    },

	                    progress: function (e) {
	                      // console.log('progress', arguments);

	                      bar.max = e.total;
	                      bar.value = e.loaded;
	                    },

	                    loadEnd: function (e) {
	                      // console.log('loadEnd', arguments);

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

	                      // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
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
	                      				loadProjectSubTab('documents',{{$project->id}});
	                      			}
	                      		});
	                      	});
	                      }else if(is_retainage == 1){
	                      	dynamicModalLoad('document-retainage-form/{{$project->id}}/'+documentids);

	                      }else if(is_advance == 1){
	                      	dynamicModalLoad('document-advance-form/{{$project->id}}/'+documentids);
	                      }

	                      loadProjectSubTab('documents',{{$project->id}});
	                    }

	                  };

	                  var select = UIkit.upload('.js-upload', settings);

	                });
	              </script>


	        </div>

	      </div>
	      <p>Knowingly submitting incorrect documentation constitutes fraud and will be prosecuted to the fullest extent of the law.</p>
	    </div>
	  </div> --}}<!--6-10-->

	</div>




<?php /*
<div class="uk-width-3-5@m uk-width-1-1">
    <div class="uk-grid">
    <select class="uk-width-1-2 uk-select filter-drops " style="style="height: 30px; padding: 1px; margin-top: 5px;" onchange="filterClasses(this.value)">
        <option value="all">ALL CLASSES</option>
        @php $currentParent = ''; @endphp
        @php $documentClasses = $documentReset; @endphp
        @foreach($documentClasses as $category)
            @if($currentParent != $category->document_class)
                <option value="{{strtolower(str_replace(' ','-',$category->document_class))}}">{{ucwords(strtolower($category->document_class))}}</option>
               @php $currentParent = $category->document_class; @endphp
            @endif
        @endforeach
    </select>
    <select class="uk-width-1-2 uk-select filter-drops " style="style="height: 30px; padding: 1px; margin-top: 5px;" onchange="filterDescriptions(this.value)">
        <option  value="all" >ALL DESCRIPTIONS</option>
        @php $currentParent = ''; @endphp
        @php $documentDescriptions = $documentReset; @endphp
        @foreach($documentDescriptions as $category)
            @if($currentParent != $category->document_description)
                <option value="{{strtolower(str_replace(' ','-',$category->document_description))}}">{{ucwords(strtolower($category->document_description))}}</option>
               @php $currentParent = $category->document_description; @endphp
            @endif
        @endforeach
    </select>
</div>
</div>
*/ ?>
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
                    // UIkit.modal.alert('The document is approved.');
                  }
                  loadProjectSubTab('documents',{{$project->id}});
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
				loadProjectSubTab('documents',{{$project->id}});
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
                //UIkit.modal.alert('The document has been deleted.');
              }
              loadProjectSubTab('documents', {{$project->id}} );
            });
		});
	}


</script>