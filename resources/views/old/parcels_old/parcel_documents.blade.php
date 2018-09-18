
<template class="uk-hidden" id="category-list-template">
    <div class="uk-width-1-1 uk-margin-small-bottom">
        <input name="category-id-x-y" id="category-id-x-y" type="checkbox">
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
</template>

<script>
    $('.detail-tab-1-text').html('<span class="a-home-2"></span> PARCEL: {{$parcel->parcel_id}} :: Documents ');
    $('#main-option-text').html('Parcel: {{$parcel->parcel_id}}');
    $('#main-option-icon').attr('uk-icon','arrow-circle-o-left');

    var subTabType = window.subTabType;
    if(subTabType == 'documents'){
        delete window.subTabType;
        
        $('#parcel-subtab-1').attr("aria-expaned", "false");
        $('#parcel-subtab-1').removeClass("uk-active");
        $('#parcel-subtab-2').attr("aria-expaned", "true");
        $('#parcel-subtab-2').addClass("uk-active");
    }
</script>
    <div class="uk-grid uk-margin-top uk-animation-fade">
        <div class="uk-width-3-5@m uk-width-1-1 ">

         <table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
          <thead>
              <tr class="uk-text-small" style="color:#fff;background-color:#555;">
                <th>UPLOADED</th>
                <th>CATEGORIES</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="sent-document-list">
            @foreach ($documents as $document)
            <?php
            if($document->categories){
             $listcats = implode(",", json_decode($document->categories, true));
         }else{
            $listcats = '';
        }
        ?>
        <tr>
            <td>{{ date('F d, Y', strtotime($document->created_at)) }}</td>
            <td>
                <ul class="uk-list document-category-menu">
                    @foreach ($document->categoriesarray as $documentcategory_id => $documentcategory_name)
                    <li>
                        <a id="sent-id-{{ $document->id }}-category-id-{{ $documentcategory_id }}" class="">
                            <span id="sent-id-{{ $document->id }}-category-id-1-recieved-icon" class="a-checkbox-checked {{ in_array($documentcategory_id, $document->approved_array) ? "received-yes" : "check-received-no received-no" }}"></span> 
                            <span id="sent-id-{{ $document->id }}category-id-1-not-received-icon" class="{{ in_array($documentcategory_id, $document->notapproved_array) ? "a-checkbox-minus" : "a-checkbox" }} {{ in_array($documentcategory_id, $document->approved_array) ? " minus-received-yes received-yes" : "received-no" }}"></span> 

                            {{ $documentcategory_name }}

                        </a>
                        @if(Auth::user()->entity_type == "hfa")
                        <div uk-dropdown="toggle: #sent-id-{{ $document->id }}-category-id-{{ $documentcategory_id }}">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li>
                                    <a onclick="resetDocTabCategoryListVars();selectCategory('{{ $documentcategory_id }}');">
                                        Select this category on right
                                    </a>
                                </li>
                                <li>
                                    <a onclick="markApproved({{ $document->id }},{{ $documentcategory_id }});">
                                        Mark as approved
                                    </a>
                                </li>
                                <li>
                                    <a onclick="markNotApproved({{ $document->id }},{{ $documentcategory_id }});">
                                        Mark as declined
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </td>
            <td>
                <a class="uk-link-muted " uk-tooltip="{{ $document->filename }} @if($document->comment)<br />{{ $document->comment }}@endif @if(count($document->retainages)) <br />Retainages:@foreach($document->retainages as $document_retainage) <br />@if($document_retainage->cost_item){{$document_retainage->cost_item->description}}@endif @endforeach @endif @if(count($document->advances)) <br />Retainages:@foreach($document->advances as $document_advance) <br />{{$document_advance->description}} @endforeach @endif">
                    <span class="a-info-circle"></span>
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a class="uk-link-muted " onclick="dynamicModalLoad('edit-document/{{$document->id}}')" uk-tooltip="Edit this file">
                    <span class="a-pencil-2"></span>
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
                    <span class="a-trash-4"></span>
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="{{ URL::route('documents.downloadDocument', [$parcel->id, $document->id]) }}" target="_blank"  uk-tooltip="Download file {{ $document->filename }}">
                    <span class="a-lower"></span>
                </a>
                <!-- &nbsp;&nbsp;|&nbsp;&nbsp;
                <a class="uk-link-muted" onclick="newEmailRequest('');" uk-tooltip="Email request">
                    <i class="uk-icon-share-square-o "></i>
                </a> -->
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a onclick="resetDocTabCategoryListVars();selectCategory('{{ $listcats }}')" uk-tooltip="Select all categories listed for this document group that was sent via EMAIL.">
                    <span class="a-higher"></span>
                </a>
            </td>
        </tr>
        @endforeach
        @if ($pending_categories_list != '')
        <tr>

            <td>PENDING</td>
            <td>
                @if(count($pending_categories)>0)
                <ul class="uk-subnav document-category-menu">
                    @foreach ($pending_categories as $pending_category)

                    <li data-uk-dropdown=""  class="">
                        <a id="sent-id-1-category-id-4" class="">
                            <span id="sent-id-1-category-id-4-recieved-icon" class="a-checkbox-checked received-no"></span> <span id="sent-id-1-category-id-4-not-received-icon" class=" received-no a-checkbox-minus"></span> 
                            {{ $document_categories_key[$pending_category] }}
                        </a>
                        <div class="uk-dropdown  uk-dropdown-bottom" style="top: 22px; left: 0px;">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li>
                                    <a onclick="resetDocTabCategoryListVars();selectCategory('{{ $pending_category }}');">
                                        Select this category on right
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endforeach

                </ul>
                @else
                @endIf
            </td>
            <td>
             <span class="a-info-circle" class=" uk-text-extramuted"></span>
             &nbsp;&nbsp;|&nbsp;&nbsp;
             <span class="a-trash-4" class=" uk-text-extramuted"></span>
             &nbsp;&nbsp;|&nbsp;&nbsp;
             <span class="a-lower" class=" uk-text-extramuted"></span>
             <!-- &nbsp;&nbsp;|&nbsp;&nbsp;
             <a class="uk-link-muted" onclick="newEmailRequest('{{ $pending_categories_list }}');">
                <i class="uk-icon-share-square-o "></i> -->
                &nbsp;&nbsp;|&nbsp;&nbsp;
            </a>
            <a onclick="resetDocTabCategoryListVars();selectCategory('{{ $pending_categories_list }}')" uk-tooltip="Select all categories listed for this document group that was sent via PENDING."><span class="a-higher"></span></a></td>
        </tr>
        @endif

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
                        <li>
                            <input name="category-id-checkbox" class="uk-checkbox" id="category-id-{{ $category->id }}" value="{{ $category->id }}" type="checkbox">
                            <label for="category-id-{{ $category->id }}">
                                {{ $category->document_category_name }}
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
                                if(categoryArray.length > 0){
                                    console.log('Categories selected: '+categoryArray);
                                    
                                    if($.inArray('47', categoryArray) > -1 && categoryArray.length > 1){
                                        UIkit.modal.alert('You must only select one category when uploading an Advance Payment Document.');
                                        return false;
                                    }
                                    if($.inArray('9', categoryArray) > -1 && categoryArray.length > 1){
                                        UIkit.modal.alert('You must only select one category when uploading an Retainage Payment Document.'+categoryArray.length+' '+categoryArray);
                                        return false;
                                    }
                                }else{
                                    UIkit.modal.alert('You must select at least one category.');
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
                                        $.post('{{ URL::route("documents.uploadComment", $parcel->id) }}', {
                                            'postvars' : documentids,
                                            'comment' : val,
                                            '_token' : '{{ csrf_token() }}'
                                        }, function(data) {
                                            if(data!='1'){ 
                                                UIkit.modal.alert(data);
                                            } else {
                                                UIkit.modal.alert('Your comment has been saved.');
                                                loadParcelSubTab('documents',{{$parcel->id}});
                                            }
                                        });
                                    });
                                }else if(is_retainage == 1){
                                    dynamicModalLoad('document-retainage-form/{{$parcel->id}}/'+documentids);
                                    
                                }else if(is_advance == 1){
                                    dynamicModalLoad('document-advance-form/{{$parcel->id}}/'+documentids);
                                }
                                
                                loadParcelSubTab('documents',{{$parcel->id}});
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
<script type="text/javascript">
function markApproved(id,catid){
    UIkit.modal.confirm("Are you sure you want to approve this file?").then(function() {
        $.post('{{ URL::route("documents.approve", $parcel->id) }}', {
            'id' : id,
            'catid' : catid,
            '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data!='1'){ console.log("processing");
                    UIkit.modal.alert(data);
                } else {
                    // UIkit.modal.alert('The document is approved.');                                                                           
                }
                loadParcelSubTab('documents',{{$parcel->id}});
            }
        );
    });
}

function markNotApproved(id,catid){
    UIkit.modal.confirm("Are you sure you want to decline this file?").then(function() {
        $.post('{{ URL::route("documents.notapprove", $parcel->id) }}', {
            'id' : id,
            'catid' : catid,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!='1'){ 
                UIkit.modal.alert(data);
            } else {
                UIkit.modal.alert('The document is not approved.');                                                                           
            }
            loadParcelSubTab('documents',{{$parcel->id}});
        });
    });
}

function deleteFile(id){
    UIkit.modal.confirm("Are you sure you want to delete this file? This is permanent.").then(function() {
        $.post('{{ URL::route("documents.deleteDocument", $parcel->id) }}', {
            'id' : id,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data!='1'){ 
                UIkit.modal.alert(data);
            } else {
                //UIkit.modal.alert('The document has been deleted.');                                                                           
            }
            loadParcelSubTab('documents', {{$parcel->id}} );
        });
    });
}


</script>