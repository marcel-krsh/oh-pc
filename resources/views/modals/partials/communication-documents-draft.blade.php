<link rel="stylesheet" href="/css/communications-tab.css{{ asset_version() }}">
<div class="uk-width-1-5 " style="padding:18px;"><div style="width:20px;display: inline-block;" onClick="showDocuments"><i class="a-paperclip-2 "></i></div> DOCUMENTS:</div>
<div class="uk-width-4-5" id="documents-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
	<div id="add-documents-button" class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showDocuments()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD DOCUMENTS </div><div id="done-adding-documents-button" class="uk-button uk-button-success uk-button-small" style="padding-top: 2px; display: none;" onClick="showDocuments()"><i class="a-circle-cross"></i> &nbsp;DONE ADDING DOCUMENTS</div>
	<div id='documents-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="" id="update-me" value="" type="checkbox" checked class="uk-checkbox documents-selector"><span class=
		'documents-name'></span>
	</div>
</div>
<div class="uk-width-1-5 documents-list" style="display: none;"></div>
<div class="uk-width-5-5 uk-grid documents-list" id='recipients' style="border-top: 1px #111 dashed; border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:10px 2px 2px 2px; position: relative;top:0px; display: none">
	@if($auditor_access)
	<div class="uk-width-1-2@m uk-width-1-1@s">
		<h4>Select from exising documents</h4>
		<div class="communication-selector  uk-scrollable-box">
			<ul class="uk-list document-menu" id="existing-documents">
				@foreach ($documents as $document)
				<li class="document-list-item uk-margin-small-bottom {{ strtolower($document->document_class) . ' : ' . strtolower($document->document_description) }}">
					@if(get_class($document) == 'App\Models\SyncDocuware')
					<input style="float: left; margin-top: 3px" name="docuware_documents[]" id="list-document-id-docuware-{{ $document->id }}" value="docuware-{{ $document->id }}" type="checkbox"  class="uk-checkbox" onClick="addDocuwareDocument(this.value,'{{ $document->document_class }} {{ $document->document_description }}')">
					<label style="display: block; margin-left: 20px" for="docuware-document-id-{{ $document->id }}">
						{{ucwords(strtolower($document->document_class))}} :
						{{ucwords(strtolower($document->document_description))}}
						<span uk-tooltip title=" Created at: {{ date('m/d/Y', strtotime($document->created_at)) }} {{ $document->created_at->format('h:i a') }} <br>Extension: {{ $document->dw_extension }}">
							<span class="a-info-circle"  style="color: #56b285;"></span>
						</span>
					</label>
					@else
					<input style="float: left; margin-top: 3px" name="local_documents[]" id="list-document-id-local-{{ $document->id }}" value="local-{{ $document->id }}" type="checkbox"  class="uk-checkbox" onClick="addLocalDocument(this.value,'{{ $document->assigned_categories->first()->document_category_name }} : {{ ucwords(strtolower($document->filename)) }}')">
					<label style="display: block; margin-left: 20px" for="local-document-id-{{ $document->id }}">
						{{ $document->assigned_categories->first()->parent->document_category_name }} : {{ ucwords(strtolower($document->assigned_categories->first()->document_category_name)) }}
						<span uk-tooltip title=" Created at: {{ date('m/d/Y', strtotime($document->created_at)) }} {{ $document->created_at->format('h:i a') }} @if($document->comment)<br>Comment: {{ $document->comment }}@endif">
							<span class="a-info-circle"  style="color: #56b285;"></span>
						</span>
					</label>
					@endif
						{{-- <br />
						<ul class="document-category-menu">
							@foreach ($document->categoriesarray as $documentcategory_id => $documentcategory_name)
							<li>
								{{ $documentcategory_name }}
							</li>
							@endforeach
						</ul> --}}
					</li>
					@endforeach
				</ul>
			</div>
			<div class="uk-form-row">
				<input type="text" style="width: 100%" id="document-filter" class="uk-input uk-width-1-1" placeholder="Filter Documents" >
			</div>
		</div>
		@endIf

		@if($auditor_access)
		<div class="uk-width-1-2@m uk-width-1-1@s">
			@else
			<div class="uk-width-1-1">
				@endIf
				<h4 class="uk-text-primary uk-text-uppercase">Upload new documents</h4>
				<div class="communication-selector uk-scrollable-box" >
					<ul class="uk-list document-category-menu">
						<li class="recipient-list-item  ohfa limited partnership"><strong>Select Category</strong></li>
						<hr class="recipient-list-item dashed-hr uk-margin-bottom">
						@foreach ($document_categories as $category)
						<li>
							<input style="float: left; margin-top: 3px" name="category-id-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="radio"  class="uk-radio">
							<label style="display: block; margin-left: 20px; font-size: 13px" for="category-id-{{ $category->id }}">
								{{ $category->parent->document_category_name }} : {{ $category->document_category_name }}
							</label>
						</li>
						@endforeach
					</ul>
				</div>
				<div class="uk-form-row">
					<input class="uk-input uk-width-1-1" id="local-comment" type="text" name="local-comment" placeholder="Enter a brief note about this document" style="width:100%">
				</div>
				<div class="uk-form-row" id="list-item-upload-box">
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
							var bar = document.getElementById('js-progressbar');
							settings    = {
								url: '{{ URL::route("documents.local-upload-draft", $project->id) }}',
								multiple: true,
								allow : '*.(jpg|jpeg|gif|png|pdf|doc|docx|xls|xlsx)',
								beforeSend: function () {
								},
								beforeAll: function (settings) {
									var categoryArray = [];
									$("input:radio[name=category-id-checkbox]:checked").each(function(){
										categoryArray.push($(this).val());
									});
									settings.params.categories = categoryArray;
									settings.params.ohfa_file = 1;
									settings.params.draft_id = "{{ $draft->id }}";
									settings.params.audit_id = "{{ $audit_id }}";
									settings.params.comment = $("input[name=local-comment]").val();
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
								complete: function (response) {
									var data = jQuery.parseJSON(response.response);
									var documentids = data['document_ids'];
									// debugger;
									setTimeout(function () {
										bar.setAttribute('hidden', 'hidden');
									}, 250);
			            //update existing doc list
			            // get document filename and categories
			            var document_info_array = [];
			            $.post('{{ URL::route("documents.documentInfo", $project->id) }}', {
			            	'postvars' : documentids,
			            	'_token' : '{{ csrf_token() }}'
			            }, function(data) {
			            	if(data=='0'){
			            		UIkit.modal.alert("There was a problem getting the documents' information.",{stack: true});
			            	} else {
			            		document_info_array = data;
			            		documentids = documentids + '';
			            		var documentid_array = documentids.split(',');
			            		for (var i = 0; i < documentid_array.length; i++) {
			            			did = documentid_array[i];
			            			docnameActual = document_info_array[did]['categories']['category_name'] + ' : ' + document_info_array[did]['filename'];
			            			docname = "'"+docnameActual+"'";
			            			newinput = '<li>'+
			            			'<input name="local_documents[]" id="list-document-id-local-'+did+'" value="local-'+did+'" type="checkbox" checked  class="uk-checkbox" onClick="addLocalDocument(this.value,'+docname+')">'+
			            			'<label for="local-document-id-'+did+'">'+
			            			'    ' +document_info_array[did]['categories']['category_name']+
			            			' : ' +document_info_array[did]['filename']+
			            			'</label>'+
			            			'<br />'+
			            			'<ul class="document-category-menu">';
			            			for(var j = 0; j < document_info_array[did]['categories'].length; j++){
			            				newinput = newinput +
			            				'    <li>'+
			            				'       '+document_info_array[did]['categories'][j]+
			            				'    </li>';
			            			}
			            			newinput = newinput +
			            			'</ul>'+
			            			'</li>';
			            			$("#existing-documents").append(newinput);
			            			var newid = 'local-'+did;
			            		}
			            		addLocalDocument(newid, docnameActual);
			            	}
			            });
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

			          }
			        };
			        var select = UIkit.upload('.js-upload', settings);
			      });
			    </script>
			  </div>
			  @cannot('access_auditor')
			  <div class="uk-form-row" id="existing-documents"></div>
			  @endIfnot
			</div>
			<script>
      // CLONE RECIPIENTS
      function addDocuwareDocument(formValue,name){
        //alert(formValue+' '+name);
        if($("#list-document-id-"+formValue).is(':checked')){
        	var documentClone = $('#documents-template').clone();
        	documentClone.attr("id", "document-id-"+formValue+"-holder");
        	documentClone.prependTo('#documents-box');
        	$("#document-id-"+formValue+"-holder").slideDown();
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("id","document-id-"+formValue);
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("name","docuware_documents[]");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeDocuwareDocument('"+formValue+"');");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
        	$("#document-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
        } else {
        	$("#document-id-"+formValue+"-holder").slideUp();
        	$("#document-id-"+formValue+"-holder").remove();
        }
      }
      function removeDocuwareDocument(id){
      	$("#document-id-"+id+"-holder").slideUp();
      	$("#document-id-"+id+"-holder").remove();
      	$("#list-document-id-"+id).prop("checked",false)
      }

      function addLocalDocument(formValue,name){
        //alert(formValue+' '+name);
        if($("#list-document-id-"+formValue).is(':checked')){
        	var documentClone = $('#documents-template').clone();
        	documentClone.attr("id", "document-id-"+formValue+"-holder");
        	documentClone.prependTo('#documents-box');
        	$("#document-id-"+formValue+"-holder").slideDown();
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("id","document-id-"+formValue);
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("name","local_documents[]");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeLocalDocument('"+formValue+"');");
        	$("#document-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
        	$("#document-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
        } else {
        	$("#document-id-"+formValue+"-holder").slideUp();
        	$("#document-id-"+formValue+"-holder").remove();
        }
      }

      function removeLocalDocument(id){
      	$("#document-id-"+id+"-holder").slideUp();
      	$("#document-id-"+id+"-holder").remove();
      	$("#list-document-id-"+id).prop("checked",false)
      }
    </script>

    <script type="text/javascript">
	    // filter documents based on class
	    $('#document-filter').on('keyup', function () {
	    	var searchString = $(this).val().toLowerCase();
	    	if(searchString.length > 0){
	    		$('.document-list-item ').hide();
	    		$('.document-list-item[class*="' + searchString + '"]').show();
	    	}else{
	    		$('.document-list-item ').show();
	    	}
	    });
	  </script>


	</div>
