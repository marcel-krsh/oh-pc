
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
    $('#main-option-text').html('Project: {{$project->project_number}}');
    $('#main-option-icon').attr('uk-icon','arrow-circle-o-left');

    // var subTabType = window.subTabType;
    // if(subTabType == 'documents'){
    //     delete window.subTabType;
        
    //     $('#parcel-subtab-1').attr("aria-expaned", "false");
    //     $('#parcel-subtab-1').removeClass("uk-active");
    //     $('#parcel-subtab-2').attr("aria-expaned", "true");
    //     $('#parcel-subtab-2').addClass("uk-active");
    // }
</script>
    <div class="uk-grid uk-margin-top uk-animation-fade">
        <div class="uk-width-3-5@m uk-width-1-1 ">

         <table class="uk-table uk-table-striped uk-table-condensed uk-table-hover gray-link-table" id="">
          <thead>
              <tr class="uk-text-small" style="color:#fff;background-color:#555;">
                <th>CLASS</th>
                <th>DESCRIPTION</th><th>STORED</th>
                <th>MODIFIED</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody id="sent-document-list">
            @foreach ($documents as $document)
        <tr>
            
            <td>
                {{ucwords(strtolower($document->document_class))}}
            </td>
            <td>
                {{ucwords(strtolower($document->document_description))}}
            </td>
            <td>{{ date('F d, Y g:h a', strtotime($document->dw_stored_date_time)) }}</td>
            <td>{{ date('F d, Y g:h a', strtotime($document->dw_mod_date_time)) }}</td>
            <td>
                @if($document->notes)<a class="uk-link-muted " uk-tooltip="{{ $document->notes }}">
                    <span class="a-file-info"></span>
                </a>
                &nbsp;&nbsp;| &nbsp;&nbsp; @endif
                <a class="uk-link-muted " onclick="deleteFile({{ $document->id }});" uk-tooltip="Delete this file">
                    <span class="a-trash-4"></span>
                </a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="{{ URL::route('documents.downloadDocument', [$project->id, $document->id]) }}" target="_blank"  uk-tooltip="Download file.">
                    <span class="a-lower"></span>
                </a>
                
            </td>
        </tr>
        @endforeach
        

    </tbody>
</table>

</div><!--4-10-->

<div class="uk-width-2-5@m uk-width-1-1">
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
    </div><!--6-10-->

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