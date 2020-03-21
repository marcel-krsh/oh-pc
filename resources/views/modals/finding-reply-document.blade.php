	<div class="uk-modal-header">
		<div class="uk-modal-title uk-remove-margin"> <i class=" a-circle-plus"></i> FINDING #{{$from->id}} <small>ADD DOCUMENTS</small></div>

	</div>
	<form id="add-document-finding-form" method="post">
		<input type="hidden" name="id" value="{{$from->id}}">
		<input type="hidden" name="fromtype" value="{{$fromtype}}">
		<input type="hidden" name="type" value="document">
		<div class="form-default-followup uk-margin-top" uk-grid>

			@if(!is_null($project))
			@if($all_findings > 0)
			<div class="uk-width-1-2">
			@else
			<div class="uk-width-1-1">
			@endif
				<h4 class="uk-text-primary uk-text-uppercase">Upload new documents</h4>
				@if($requested_categories != '')
				<p>Requested Categories: {{$requested_categories}}</p>
				@endif
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
				<br>
				<div class="uk-form-row">
					<input style="width: 100%" class="uk-input uk-width-1-1" id="local-comment" type="text" name="local-comment" placeholder="Enter a brief note about this document">
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
							url: '{{ URL::route("documents.local-upload", $project->id) }}',
							allow : '*.(jpg|gif|png|pdf|doc|docx|xls|xlsx)',
							beforeSend: function () {
							},
		          beforeAll: function (settings) {
								var categoryArray = [];
								$("input:radio[name=category-id-checkbox]:checked").each(function(){
									categoryArray.push($(this).val());
								});
								var findingsArray = [];
								$("input[name='findings[]']:checked").each(function (){
					    		findingsArray.push(parseInt($(this).val()));
					    	});
								settings.params.categories = categoryArray;
								settings.params.ohfa_file = 1;
								settings.params.comment = $("input[name=local-comment]").val();
								settings.params._token = '{{ csrf_token() }}';
								settings.params.findings = findingsArray;
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
		            			'<input name="local_documents[]" id="list-document-id-local-'+did+'" value="'+did+'" type="checkbox" checked  class="uk-checkbox" onClick="addLocalDocument(this.value,'+docname+')">'+
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
		          }
		        };
		        var select = UIkit.upload('.js-upload', settings);
		      });
		    </script>
			  </div>

			  <div class="uk-form-row">
			  	<ul id="existing-documents"></ul>
			  </div>
			</div>
			@endif
			@if($all_findings > 0)
			<div class="uk-width-1-2">
			@include('modals.partials.document-findings')
			</div>
			@endif

			<div class="uk-width-1-1">


				<hr class="dashed-hr uk-margin-bottom">
				<div class="uk-margin-bottom" uk-grid>
		    	<!-- <div class="uk-width-1-2">
		    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
		    	</div> -->

		    	<div class="uk-width-1-2">
		    		<a class="uk-button uk-button-primary uk-width-1-1" onclick="dynamicModalClose(2);"><i class="a-circle-cross"></i> CANCEL</a>
		    	</div>
		    	<div class="uk-width-1-2">
		    		<button class="uk-button uk-button-success uk-width-1-1" onclick="saveFindingDocument(event);"><i class="a-file-pen"></i> SAVE DOCUMENTS</button>
		    	</div>
		    </div>
		  </div>
		</div>
	</form>
	<script type="text/javascript">

		$( document ).ready(function() {
			$('.documents-list').slideToggle();
			$('#add-documents-button').toggle();
			$('#done-adding-documents-button').toggle();
		});

		function saveFindingDocument(e){
			e.preventDefault();
			var form = $('#add-document-finding-form');

			var no_alert = 1;
			var selected_documents_array = [];
			$("input[name='local_documents[]']:checked").each(function (){
				selected_documents_array.push(parseInt($(this).val()));
			});
			if(selected_documents_array.length === 0){
				no_alert = 0;
				UIkit.modal.alert('You must select a document.',{stack: true});
			}

			if(no_alert==1){
				$.post("/findings/reply", {
					'inputs' : form.serialize(),
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data!=1){
						UIkit.modal.alert(data,{stack: true});
					} else {
						dynamicModalClose(2);
						UIkit.notification('<span uk-icon="icon: check"></span> Document(s) Saved', {pos:'top-right', timeout:1000, status:'success'});
						$('#finding-modal-audit-stream-refresh').trigger('click');

					}
				} );
			}

		}

		function showDocuments() {
			$('.documents-list').slideToggle();
			$('#add-documents-button').toggle();
			$('#done-adding-documents-button').toggle();
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

	</script>
