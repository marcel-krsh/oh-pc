<div id="dynamic-modal-content">

	<form name="newOutboundEmailForm" id="newOutboundEmailForm" method="post">
		@if(!is_null($project))<input type="hidden" name="project" value="{{$project->id}}">@endif
		<div class="uk-container uk-container-center"> <!-- start form container -->
			<div uk-grid class="uk-grid-small ">
				<div class="uk-width-1-1 uk-padding-small">
                    @if($project)
					<h2><span  class="uk-icon-button uk-margin-right uk-margin-left uk-disabled uk-button-succes" uk-icon="mail"></span>Message for Project: <span id="current-file-id-dynamic-modal">{{$project->project_number}}</span></h2>
                    @else
                    <h3>New Message</h3>
                    @endif
				</div>
			</div>
            <hr class="uk-width-1-1 dashed-hr uk-margin-bottom">
			<div uk-grid class="uk-grid-collapse">
                <div class="uk-width-1-6 " style="padding:18px;"><div style="width:25px; display: inline-block;"><i uk-icon="user"></i></div> &nbsp;FROM:</div>
                <div class="uk-width-5-6 " style="border-bottom:1px #111 dashed; padding:18px; padding-left:27px;">{{Auth::user()->full_name()}}</div>
                <div class="uk-width-1-6 " style="padding:18px;"><div style="width:25px;display: inline-block;"><i uk-icon="users" class=""></i></div> &nbsp;TO: </div>
               
                <div class="uk-width-5-6 "  id="recipients-box" style="border-bottom:1px #111 dashed;padding:18px; padding-left:25px;">
                    <div class="uk-button uk-button-small" style="padding-top: 2px;" onClick="showRecipients()"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD RECIPIENT</div>
                    <div id='recipient-template' class="uk-button uk-button-small uk-margin-small-right uk-margin-small-bottom uk-margin-small-top" style="padding-top: 2px; display:none;"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;<input name="recipients[]" id="update-me" value="" type="checkbox" checked class="uk-checkbox recipient-selector"><span class=
                        'recipient-name'></span></div>

                </div> <div class="uk-width-1-6 recipient-list" style="display: none;"></div>
                <div class="uk-width-5-6 recipient-list" id='recipients' style="border-left: 1px #111 dashed; border-right: 1px #111 dashed; border-bottom: 1px #111 dashed; padding:18px; padding-left:25px; position: relative;top:0px; display: none">
                    <!-- RECIPIENT LISTING -->
                    
                            <div class="communication-selector uk-scrollable-box">
                                <ul class="uk-list document-menu">
                                    @if(count($recipients_from_hfa) > 0)
                                        <li class="ohfa "><strong>OHFA STAFF</strong></li>
                                        <hr class="dashed-hr uk-margin-bottom">
                                        @foreach ($recipients_from_hfa as $recipient_from_hfa)
                                        <li class="ohfa {{strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient_from_hfa->organization_name)))))}} {{ strtolower($recipient_from_hfa->first_name) }} {{ strtolower($recipient_from_hfa->last_name) }}">
                                            <input name="recipients[]" id="list-recipient-id-{{ $recipient_from_hfa->id }}" value="{{ $recipient_from_hfa->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipient(this.value,'{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}')">
                                            <label for="recipient-id-{{ $recipient_from_hfa->id }}">
                                                {{ ucwords($recipient_from_hfa->first_name) }} {{ ucwords($recipient_from_hfa->last_name) }}
                                            </label>
                                        </li>
                                        @endforeach
                                    @endif
                                    @php $currentOrg = ''; @endphp
                                    @foreach ($recipients as $recipient)
                                        @if($currentOrg != $recipient->organization_name)
                                        <li class="@if(count($recipients_from_hfa) > 0 || $currentOrg != '') uk-margin-large-top @endif {{strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name)))))}}"><strong>{{$recipient->organization_name}}</strong></li>
                                        <hr class="dashed-hr uk-margin-bottom">
                                        @php $currentOrg = $recipient->organization_name; @endphp
                                        @endIf

                                    <li class="{{strtolower(str_replace('&','',str_replace('.','',str_replace(',','',str_replace('/','',$recipient->organization_name)))))}} {{ strtolower($recipient->first_name) }} {{ strtolower($recipient->last_name) }}">
                                        <input name="" id="list-recipient-id-{{ $recipient->id }}" value="{{ $recipient->id }}" type="checkbox" class="uk-checkbox" onClick="addRecipient(this.value,'{{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}')">
                                        <label for="recipient-id-{{ $recipient->id }}">
                                            {{ ucwords($recipient->first_name) }} {{ ucwords($recipient->last_name) }}
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="uk-form-row">
                                <input type="text" class="uk-input uk-width-1-1" placeholder="Filter Recipients">
                            </div>

                            <script>
                                // CLONE RECIPIENTS
                                function addRecipient(formValue,name){
                                    //alert(formValue+' '+name);
                                    if($("#list-recipient-id-"+formValue).is(':checked')){
                                        var recipientClone = $('#recipient-template').clone();
                                        recipientClone.attr("id", "recipient-id-"+formValue+"-holder");
                                        recipientClone.prependTo('#recipients-box');

                                        $("#recipient-id-"+formValue+"-holder").slideDown();
                                        $("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("id","recipient-id-"+formValue);
                                        $("#recipient-id-"+formValue+"-holder input[type=checkbox]").attr("onClick","removeRecipient("+formValue+");");
                                        
                                        $("#recipient-id-"+formValue+"-holder input[type=checkbox]").val(formValue);
                                        $("#recipient-id-"+formValue+"-holder span").html('&nbsp; '+name+' ');
                                    } else {
                                        $("#recipient-id-"+formValue+"-holder").slideUp();
                                        $("#recipient-id-"+formValue+"-holder").remove();
                                    }

                                    
                                }
                                function removeRecipient(id){
                                    $("#recipient-id-"+id+"-holder").slideUp();
                                    $("#recipient-id-"+id+"-holder").remove();
                                    $("#list-recipient-id-"+id).prop("checked",false)
                                }
                            </script>
                    <!-- END RECIPIENT LISTING -->
                </div>
                @if($project)
                <div class="uk-width-1-6 " style="padding:18px;"><div style="width:25px;display: inline-block;" onClick="showDocuments"><i class="a-paperclip-2 "></i></div> &nbsp;DOCUMENTS</div>
                <div class="uk-width-5-6 "  id="attachments-box" style="border-bottom:1px #111 dashed; padding:18px; padding-left:25px;">
                    <div class="uk-button uk-button-small" style="padding-top: 2px;"><i uk-icon="icon: plus-circle; ratio: .7"></i> &nbsp;ADD DOCUMENT</div><div class="uk-button uk-button-small" style="padding-top: 2px; display:none"><i uk-icon="icon: cross-circle; ratio: .7"></i> &nbsp;Class:Description [Date]</div>
                </div>
                @endIf

                <div class="uk-width-1-6 " style="padding:18px;padding-top:27px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;SUBJECT:</div>
                <div class="uk-width-5-6"  style="padding:18px; border-bottom:1px #111 dashed;">
                    <fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
                        <div uk-grid class="uk-grid-collapse">
                            <div class="uk-width-1-1">
                                <input type="text" name="subject" class="uk-width-1-1 uk-input uk-form-large uk-form-blank" placeholder="Recipients will see your subject in their notifications.">
                            </div>
                        </div>
                    </fieldset>
                </div>
                

                 <div class="uk-width-1-6 " style="padding:18px; padding-top:40px;"><div style="width:25px;display: inline-block;">&nbsp;</div> &nbsp;MESSAGE:</div> 
                 <div class="uk-width-5-6 " style="padding:18px;">
                    <fieldset class="uk-fieldset" style="min-height:3em; width: initial;">
                        <div uk-grid class="uk-grid-collapse">
                            <div class="uk-width-1-1">
                                <textarea id="message-body" style="min-height: 100px;padding-left: 10px; border:none;" rows="11" class="uk-width-1-1 uk-form-large uk-input uk-form-blank uk-resize-vertical" name="messageBody" value="" placeholder="Recipients will have to log-in to view your message."></textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-margin-top">
                <div class="uk-width-1-3">&nbsp;</div>
                <div class="uk-width-1-3"><a class="uk-width-5-6 uk-button uk-align-right " onclick="dynamicModalClose();"><i class="a-circle-cross"></i> CANCEL</a></div>
                <div class="uk-width-1-3"><a class="uk-width-5-6 uk-align-right uk-button uk-button-success" onclick="submitNewCommunication()"><i class="a-paper-plane"></i> SEND</a></div>
            </div>
        </div>
    </form>
</div>


                    

                        @if($project)
        				<div class="uk-width-1-1@m uk-width-1-1@s">
        					<h4>Select exising documents</h4>
        					<div class="communication-selector  uk-scrollable-box">
        			            <ul class="uk-list document-menu" id="existing-documents">
                                    @foreach ($documents as $document)
                                    <li class="uk-margin-large-bottom">
                                        <input name="documents[]" id="document-id-{{ $document->id }}" value="{{ $document->id }}" type="checkbox"  class="uk-checkbox">
                                        <label for="document-id-{{ $document->id }}">
                                            {{ $document->document_class }} {{ $document->document_description }}
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
                            <div class="uk-form-row">
                                <input type="text" class="uk-input uk-width-1-1" placeholder="Filter Documents">
                            </div>
        				</div>

        				<div class="uk-width-1-1@m uk-width-1-1@s">
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
                            <div class="uk-form-row">
                                <input class="uk-input uk-width-1-1" type="text" name="notes" placeholder="Enter a brief note about this document">
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
                                            $.post('{{ URL::route("documents.uploadComment", $project->id) }}', {
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
                                        $.post('{{ URL::route("documents.documentInfo", $project->id) }}', {
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
                    <div class="uk-width-2-3">
                    <h4>Message body</h4>
                    
               
                
            </div></div></div></div>
            
				
		
	
		
		
			
		

	<script type="text/javascript">
        function showRecipients() {
            $('.recipient-list').slideToggle();

        }
        function showDocuments() {

        }
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
                if(data!=1){ 
                    UIkit.modal.alert(data,{stack: true});
                } else {
                    UIkit.modal.alert('Your message has been saved.',{stack: true});
                }
            } );

            @if($project)
            var id = {{$project->id}};
            //loadParcelSubTab('communications',id);
            @else
            //loadDashBoardSubTab('dashboard','communications');
            @endif
            
            dynamicModalClose();
        }
		
	}	
	</script>
</div>