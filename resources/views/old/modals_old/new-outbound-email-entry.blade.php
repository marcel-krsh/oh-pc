<div id="dynamic-modal-content">
	<script>
	resizeModal(95);
	</script>


	<form name="newOutboundEmailForm" id="newOutboundEmailForm" method="post">
		@if($parcel)<input type="hidden" name="parcel" value="{{$parcel->id}}">@endif
		<div class="uk-container uk-container-center"> <!-- start form container -->
			<div uk-grid class="uk-grid-small">
				<div class="uk-width-1-1">
                    @if($parcel)
					Message for Parcel: <span id="current-file-id-dynamic-modal">{{$parcel->parcel_id}}</span>
                    @else
                    New Message
                    @endif
				</div>
			</div>

			<div uk-grid>
				<div class="uk-width-1-3@m uk-width-1-1@s">
					<h4>Select recipients</h4>
					<div class="communication-selector">
			            <ul class="uk-list document-menu">
			            	@foreach ($recipients_from_hfa as $recipient_from_hfa)
                            <li>
                                <input name="recipients[]" id="recipient-id-{{ $recipient_from_hfa->id }}" value="{{ $recipient_from_hfa->id }}" type="checkbox" class="uk-checkbox">
                                <label for="recipient-id-{{ $recipient_from_hfa->id }}">
                                    {{ $recipient_from_hfa->name }} (HFA)
                                </label>
                            </li>
                            @endforeach
                            @foreach ($recipients as $recipient)
                            <li>
                                <input name="recipients[]" id="recipient-id-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox">
                                <label for="recipient-id-{{ $recipient->id }}">
                                    {{ $recipient->name }}
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
				</div>
                @if($parcel)
				<div class="uk-width-1-3@m uk-width-1-1@s">
					<h4>Select exising documents</h4>
					<div class="communication-selector">
			            <ul class="uk-list document-menu" id="existing-documents">
                            @foreach ($documents as $document)
                            <li>
                                <input name="documents[]" id="document-id-{{ $document->id }}" value="{{ $document->id }}" type="checkbox"  class="uk-checkbox">
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
				<div class="uk-width-1-3@m uk-width-1-1@s">
					<h4>Upload new documents</h4>
					<div class="communication-selector" style="height: 150px;">
						<ul class="uk-list document-category-menu">
                            @foreach ($document_categories as $category)
	                        <li>
	                            <input name="category-id-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="checkbox"  class="uk-checkbox">
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

                                setTimeout(function () {
                                    bar.setAttribute('hidden', 'hidden');
                                }, 250);

                                // Submit form and make sure it responds back with 1 - otherwise it will output the response to a browser alert box.
                                UIkit.modal.prompt("I uploaded and categorized the document(s) accordingly. Please add your comment for the history record.",'',{stack: true},function(val){
                                    $.post('{{ URL::route("documents.uploadComment", $parcel->id) }}', {
                                            'postvars' : documentids,
                                            'comment' : val,
                                            '_token' : '{{ csrf_token() }}'
                                            }, function(data) {
                                                if(data!='1'){
                                                    UIkit.modal.alert(data,{stack: true});
                                                } else {
                                                    UIkit.modal.alert('Your comment has been saved.',{stack: true});
                                                }
                                    });
                                });

                                //update existing doc list
                                // get document filename and categories
                                var document_info_array = [];
                                $.post('{{ URL::route("documents.documentInfo", $parcel->id) }}', {
                                            'postvars' : documentids,
                                            'categories' : categories,
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

                                                        newinput = '<li>'+
                                                            '<input name="documents[]" id="document-id-'+did+'" value="'+did+'" type="checkbox" checked  class="uk-checkbox">'+
                                                            '<label for="document-id-'+did+'">'+
                                                            '    '+document_info_array[did]['filename']+
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
                @endif
			</div>

            <div uk-grid>
                <div class="uk-width-1-1">
                    <h4>Message subject</h4>
                    <div class="field-box" style="width: initial;">
                        <div uk-grid class="uk-grid-collapse">
                            <div class="uk-width-1-1">
                                <input id="subject" name="subject" value=""></input>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

			<div uk-grid>
				<div class="uk-width-1-1">
                    <h4>Message body</h4>
					<div class="field-box" style="min-height:3em; width: initial;">
						<div uk-grid class="uk-grid-collapse">
                            <div class="uk-width-1-1">
								<textarea id="message-body" rows="11" name="messageBody" value=""></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div uk-grid>
			<div class="uk-width-1-1">
				<div id="applicant-info-update">
					<div uk-grid class="uk-margin">
						<div class="uk-width-1-3 uk-push-1-3">
							<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <span  uk-icon="close" class="uk-margin-left"></span> CANCEL</a>
						</div>
						<div class="uk-width-1-3  uk-push-1-3">
							<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="submitNewCommunication()"> <span uk-icon="mail" class="uk-margin-left"></span> SEND &nbsp;</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
	function submitNewCommunication() {

		var form = $('#newOutboundEmailForm');
        var no_alert = 1;

        var recipients_array = [];
        $("input[name='recipients[]']:checked").each(function (){
            recipients_array.push(parseInt($(this).val()));
        });

        if(recipients_array.length === 0){
            no_alert = 0;
            UIkit.modal.alert('You must select a recipient.',{stack: true});
        }

        if(no_alert){
            $.post('{{ URL::route("communication.create") }}', {
                'inputs' : form.serialize(),
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!='1'){
                    UIkit.modal.alert(data,{stack: true});
                } else {
                    UIkit.modal.alert('Your message has been saved.',{stack: true});
                }
            } );

            @if($parcel)
            var id = {{$parcel->id}};
            loadParcelSubTab('communications',id);
            @else
            loadDashBoardSubTab('dashboard','communications');
            @endif

            dynamicModalClose();
        }

	}
	</script>
</div>