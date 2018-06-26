<script>
resizeModal(95);
reloadUnseenMessages();
</script>
<div class="uk-container">
	<div uk-grid class="uk-grid-collapse open-communication-bottom-rules ">
		<div class="uk-width-1-1">
			<span class="communication-direction-text uk-margin-bottom">Message</span> @if($parcel)&nbsp;&nbsp;&nbsp;Parcel: {{$parcel->parcel_id}}@endif
		</div>
	</div>
	
	<div uk-grid class="uk-grid-collapse open-communication-bottom-rules uk-margin-small-top uk-margin-bottom">
		<div class="uk-width-1-1 uk-width-1-5@s communication-type-and-who ">
			<span class=" communication-item-date-time">
				{{ date('F d, Y h:i', strtotime($message->created_at)) }}
			</span>
		</div>
		<div class="uk-width-1-1 uk-width-1-5@s communication-item-excerpt">
			From: 
			<span uk-tooltip="pos:top-left">
				<div class="user-badge user-badge-communication-item user-badge-{{ $message->owner->badge_color}} no-float">
					{{ $message->initials}}
				</div>
			</span>

			{{$message->owner->name}}
			
		</div>
		<div class="uk-width-1-1 uk-width-3-5@s communication-item-tt-to-from">
			@if(count($message->recipient_details))
			To: 
			@foreach ($message->recipient_details as $recipient)
			{{ $recipient->name }}@if(!$loop->last), @endif
			@endforeach
			@endif
			

			@if(isset($message->documents))
			@if(count($message->documents))
			@foreach($message->documents as $document)
			<a href="{{ URL::route('documents.downloadDocument', [$parcel->id, $document->document->id]) }}" target="_blank" class="uk-button uk-button-default uk-button-small uk-margin-left" uk-tooltip="Download file<br />{{$document->document->filename}}<br />in @foreach($document->categoriesarray as $category){{$category}}@if(!$loop->last), @endif @endforeach">
				<i class="a-paperclip-2"></i> {{$document->document->filename}}
			</a>
			
			@endforeach
			@endif
			
			@endif
			
		</div>
	</div>
	<!-- Start content of communication -->
	<div class="uk-width-1-1"><!--used to be uk-width-9-10, but Linda changed it-->
		<div uk-grid class="uk-grid-collapse">
			<div class="uk-width-1-1 uk-margin-bottom">
				@if($message->subject)<strong>{{$message->subject}}</strong><br /> @endif
				<div>{{$message->message}}</div>
				<hr />
			</div>		
		</div>
	</div>
</div>
@if(count($replies))
<div class="uk-container uk-margin-top" id="communication-list" style="position: relative; height: 222.5px; margin-left:5px;">
	@foreach ($replies as $reply)
	<div class="uk-width-1-1 communication-list-item normal-cursor" style="position: absolute; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
		<div uk-grid class="communication-summary">
			<div class="uk-width-1-1 uk-width-1-5@s communication-type-and-who ">
				<span uk-tooltip="pos:top-left;title:{{ $reply->owner->name}};">
					<div class="user-badge user-badge-communication-item user-badge-{{ $reply->owner->badge_color}} no-float">
						{{ $reply->initials}}
					</div>
				</span>
				<span class=" communication-item-date-time">
					{{ date('F d, Y h:i', strtotime($reply->created_at)) }}
				</span>
			</div>
			<div class="uk-width-1-1 uk-width-3-5@s communication-item-excerpt">
				{{ $reply->message}}
			</div>
			<div class="uk-width-1-1 uk-width-1-5@s">
				@if(count($reply->documents))
				@foreach($reply->documents as $document) 
				<a href="{{ URL::route('documents.downloadDocument', [$parcel->id, $document->document->id]) }}" target="_blank"  uk-tooltip="Download file<br />{{$document->document->filename}}<br />in @foreach($document->categoriesarray as $category){{$category}}@if(!$loop->last), @endif @endforeach">
	                <i uk-icon="download"></i></a>@if(!$loop->last), @endif
				@endforeach
				@endif
			</div>
		</div>
	</div>
	@endforeach
</div>
@endif


<div class="uk-container uk-grid-collapse uk-margin-top" id="communication-list" uk-grid style="position: relative; height: 222.5px; border-bottom:0px;">
	<button class="uk-button uk-button-success uk-width-1-3@m uk-width-1-1@s toggle-form" onclick="this.style.visibility = 'hidden';" uk-toggle="target: #newOutboundEmailForm">Write a reply</button>

	<form name="newOutboundEmailForm" id="newOutboundEmailForm" method="post" class="uk-margin-top toggle-form uk-width-1-1" hidden>
		@if($parcel)<input type="hidden" name="parcel" value="{{$parcel->id}}">@endif
		<input type="hidden" name="communication" value="{{$message->id}}">
		<div class="uk-container uk-container-center"> <!-- start form container -->
			
			<div uk-grid class="uk-grid-collapse">
				<div class="uk-width-1-1">
					<h4>Reply message body</h4>
					<div class="field-box" style="min-height:3em; width: initial;">
						<div uk-grid class="uk-grid-collapse">
							<div class="uk-width-1-1">
								<textarea id="message-body" rows="11" name="messageBody" value=""></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			@if($parcel)
			<div uk-grid class="uk-grid-collapse uk-grid-small uk-margin-top">
				<div class="uk-width-1-2@m uk-width-1-1@s">
					<h4>Select exising documents</h4>
					<div class="communication-selector">
			            <ul class="uk-list document-menu" id="existing-documents">
	                        @foreach ($documents as $document)
	                        <li>
	                            <input name="documents[]" id="document-id-{{ $document->id }}" value="{{ $document->id }}" type="checkbox" class="uk-checkbox">
	                            <label for="document-id-{{ $document->id }}">
	                                {{ $document->filename }}
	                            </label>
	                            <br />
	                            <ul class="document-category-menu">
		                            @foreach ($document->categoriesarray as $documentcategory_id => $documentcategory_name)
		                            <li>
										{{ $documentcategory_name }}
		                            </li>
		                            @endforeach
		                        </ul>
	                        </li>
	                        @endforeach
	                    </ul>
	                </div>
				</div>
				<div class="uk-width-1-2@m uk-width-1-1@s">
					<h4>Upload new documents</h4>
					<div class="communication-selector" style="height: 150px;">
						<ul class="uk-list document-category-menu">
	                        @foreach ($document_categories as $category)
	                        <li>
	                            <input class="uk-checkbox" name="category-id-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="checkbox">
	                            <label for="category-id-{{ $category->id }}">
	                                {{ $category->document_category_name }}
	                            </label>
	                        </li>
	                        @endforeach
	                    </ul>
					</div>
					<div class="uk-form-row" id="list-item-upload-box">

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

		                            url: '{{ URL::route("documents.upload", $parcel->id) }}',
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
	                                    categories = categoryArray;
	                                    if(categoryArray.length > 0){
	                                        console.log('Categories selected: '+categoryArray);
	                                    }else{
	                                        UIkit.modal.alert('You must select at least one category.',{
	                                                stack: true});
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

	                                 //    setTimeout(function () {
		                                //     bar.setAttribute('hidden', 'hidden');
		                                // }, 250);

	                                    // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
	                                    UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{
	                                                stack: true}).then(function(val){
	                                        $.post('{{ URL::route("documents.uploadComment", $parcel->id) }}', {
	                                                'postvars' : documentids,
	                                                'comment' : val,
	                                                '_token' : '{{ csrf_token() }}'
	                                                }, function(data) {
	                                                    if(data!='1'){ 
	                                                        UIkit.modal.alert(data,{
	                                                stack: true});
	                                                    } else {
	                                                        UIkit.modal.alert('Your comment has been saved.',{
	                                                stack: true});          
	                                                    }
	                                        });
	                                    });

	                                    console.log("docids: "+documentids);
	                                    //update existing doc list
	                                    // get document filename and categories
	                                    var document_info_array = [];
	                                    $.post('{{ URL::route("documents.documentInfo", $parcel->id) }}', {
	                                                'postvars' : documentids,
	                                                'categories' : categories,
	                                                '_token' : '{{ csrf_token() }}'
	                                                }, function(data) {
	                                                    if(data=='0'){ 
	                                                        UIkit.modal.alert("There was a problem getting the documents' information.",{
	                                                stack: true});
	                                                    } else {

	                                                        document_info_array = data; 
	                                                        documentids = documentids + '';
	                                                        var documentid_array = documentids.split(',');
					                                        for (var i = 0; i < documentid_array.length; i++) {
					                                        	did = documentid_array[i];

					                                        	newinput = '<li>'+
									                                '<input name="documents[]" id="document-id-'+did+'" value="'+did+'" type="checkbox" checked>'+
									                                '<label for="document-id-'+did+'">'+
									                                '    '+document_info_array[did]['filename']+
									                                '</label>'+
									                                '<br />'+
									                                '<ul class="document-category-menu">';
									                            for(var j = 0; j < document_info_array[did]['categories'].length; j++){
									                            	newinput = newinput + 
									                            	'    <li>'+
																	'		'+document_info_array[did]['categories'][j]+
											                        '    </li>';
									                            }
											                        

											                    newinput = newinput +   
											                        '</ul>'+
									                            	'</li>';
															  	$("#existing-documents").append(newinput); 
															} 
	                                                    }
	                                        });
		                            }

		                        };

		                        var select = UIkit.upload('.js-upload', settings);
		                        
		                    });
		                    </script>
	            	</div>
		        </div> 
			</div>
			@endif
		</div> 
		<hr>
		<div uk-grid>
			<div class="uk-width-1-1">
				<div id="applicant-info-update">
					<div uk-grid class="uk-margin">
						<div class="uk-width-1-3 uk-push-1-3">
							<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <i uk-icon="close" class="uk-margin-left"></i> CANCEL</a>
						</div>
						<div class="uk-width-1-3 uk-push-1-3">
							<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="submitNewCommunication()"> <i uk-icon="mail" class="uk-margin-left"></i> SEND &nbsp;</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div id="dialog-comment-modal" style="display:none;">
		<p>I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.</p>
	    <input name="comment" type="text" value=""/>
	    <input type="submit" class="submit" value="Add comment" />
	</div>
	<script type="text/javascript">
	function submitNewCommunication() {
		var form = $('#newOutboundEmailForm');

		$.post('{{ URL::route("communication.create") }}', {
			'inputs' : form.serialize(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!='1'){ 
				UIkit.modal.alert(data);
			} else {
				UIkit.modal.alert('Your message has been saved.');
			}
		} );


		@if($parcel)
        var id = {{$parcel->id}};
        if (typeof loadParcelSubTab === "function"){
        	loadParcelSubTab('communications',id);
        }
        @else
        $('#dash-subtab-10').trigger('click'); 
        loadDashBoardSubTab('dashboard','communications');
        @endif

		dynamicModalClose();

		
	}	
	</script>
</div>
