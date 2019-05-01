<template class="uk-hidden" id="form-finding-type-followup-template">
    <div class="form-default-followup uk-margin-top" uk-grid>
        
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            
                <label>DUE IN: 
           
        
            
                <input type="number" min="1" max="31" value="1" class="uk-form-small followup-number " style="height: 20px; width:36%; border-width: 1px; border-style: solid; border-color: rgb(229, 229, 229);"></label>
           
        </div>
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            <select class="uk-select uk-form-small followup-duration">
                <option value="hours">Hours</option>
                <option value="days">Days</option>
                <option value="weeks">Weeks</option>
                <option value="months">Months</option>
            </select>
        </div>
        <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
            <select class="uk-select uk-form-small followup-assignment">
                <option>Select Assignee</option>
                <option value="lead">Lead Auditor</option>
                <option value="pm">Property Manager</option>
                <option value="user">User Creating Finding</option>
            </select>
        </div>
        <div class="uk-width-1-2  uk-margin-small-top uk-margin-small-bottom">
            <input type="text" value="" placeholder="Follow-up Description" class="uk-input uk-form-small followup-description">
        </div>
        
        <div class="uk-width-1-2  uk-margin-top">Actions Required</div>
        <div class="uk-width-1-2  uk-margin-top"><span class="doc-cats" >Document Categories</span></div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label><input class="uk-checkbox followup-reply" type="checkbox" value="1" > Comment</label><br /><br />
            
        </div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label><input class="uk-checkbox followup-photo" type="checkbox" value="1" > Upload Photo</label>
        </div>
        <div class="uk-width-1-6  uk-margin-small-top">
            <label><input class="uk-checkbox followup-doc" type="checkbox" value="1" > Upload a Doc</label>
        </div>
        <div class="uk-width-1-2  uk-margin-small-top doc-cats">
            @if(count($document_categories))
            <div class="uk-width-1-1 uk-width-2-3@m uk-scrollable-box" style="width:100%; height:100px;">
                <ul class="uk-list">
                    @foreach($document_categories as $cat)
                    <li><label><input class="uk-checkbox followup-cat" type="checkbox" name="categories[]" value="{{$cat->id}}"> {{ucwords($cat->document_category_name)}}</label></li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        
        <div class="uk-width-1-1">

            <button class="uk-button uk-button-default uk-button-small" onclick="removeFollowUp(this);return false;">[—] Remove This Follow-up</button>
            <hr class="uk-width-1-1" style="border-style: dashed;">
        </div>
    </div>
</template>

        <div>
            <h2 id="post-response" class="uk-margin-top">@if(!$finding_type)<span uk-icon="plus-circle" class="form-title-icon"></span> Create Finding Type @else<span class="uk-icon-edit" ></span> Update Finding Type @endif</h2>
            <hr />
            <form @if($finding_type) action="/admin/findingtype/store/{{$finding_type->id}}" @else action="/admin/findingtype/store" @endif method="post" target="_blank">
                {{ csrf_field() }}
                
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="name" class="uk-width-1-3 uk-width-1-3@m">Name: </label>
                        <input id="name" type="text" name="name" value="@if($finding_type){{$finding_type->name}}@endif" placeholder="Enter the finding type name" class="uk-input uk-width-2-3 uk-width-2-3@m" required>
                    </div>
                </div>
                <hr style="border-style: dashed;"/>
                <div class="uk-form-row uk-margin-top-large">
                    <div class="uk-grid">
                        <label for="type" class="uk-width-1-3 uk-width-1-3@m">HUD Type: </label>
                        <div class="uk-width-2-3">
                            <label class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="site" value="1" @if($finding_type) @if($finding_type->site) checked @endif @else checked @endif> Site</label> 
                            <label  class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="building_exterior" value="1" @if($finding_type) @if($finding_type->building_exterior) checked @endif @endif> Building Exterior</label>
                            <label class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="building_system" value="1" @if($finding_type) @if($finding_type->building_system) checked @endif @endif> Building System</label>
                            <label class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="common_area" value="1" @if($finding_type) @if($finding_type->common_area) checked @endif @endif> Common Area</label>
                            <label class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="unit" value="1" @if($finding_type) @if($finding_type->unit) checked @endif @endif> Unit</label>
                            <label class="uk-margin-right"><input class="uk-checkbox" type="checkbox" name="file" value="1" @if($finding_type) @if($finding_type->file) checked @endif @endif> File</label>
                        </div>
                    </div>
                </div>

                <hr style="border-style: dashed;"/>
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="type" class="uk-width-1-3 uk-width-1-3@m">Allita Type: </label>
                        <div class="uk-width-2-3">
                            <label class="uk-margin-right"><input class="uk-radio" type="radio" name="type" value="nlt" @if($finding_type) @if($finding_type->type == 'nlt') checked @endif @else checked @endif> NLT</label> 
                            <label  class="uk-margin-right"><input class="uk-radio" type="radio" name="type" value="lt" @if($finding_type) @if($finding_type->type == 'lt') checked @endif @endif> LT</label>
                            <label class="uk-margin-right"><input class="uk-radio" type="radio" name="type" value="file" @if($finding_type) @if($finding_type->type == 'file') checked @endif @endif> FILE</label>
                        </div>
                    </div>
                </div>

                

                

                <hr style="border-style: dashed;"/>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="nominal" class="uk-width-1-1 uk-width-1-6@m ">Nominal Weight %: </label>
                        <input id="nominal_item_weight" type="text" name="nominal_item_weight" value="@if($finding_type){{$finding_type->nominal_item_weight}}@endif" placeholder="Enter the nominal item weight %" class="uk-input uk-width-1-1 uk-width-1-6@m">
                    
                        <label for="criticality" class="uk-width-1-1 uk-width-1-6@m uk-text-right">Criticality: </label>
                        <select id="form-stacked-select" name="criticality" class="uk-select uk-width-1-1 uk-width-1-6@m">
                            <option value="1" @if($finding_type) @if($finding_type->criticality == 1) selected @endif @endif>1</option>
                            <option value="2" @if($finding_type) @if($finding_type->criticality == 2) selected @endif @endif>2</option>
                            <option value="3" @if($finding_type) @if($finding_type->criticality == 3) selected @endif @endif>3</option>
                            <option value="4" @if($finding_type) @if($finding_type->criticality == 4) selected @endif @endif>4</option>
                            <option value="5" @if($finding_type) @if($finding_type->criticality == 5) selected @endif @endif>5</option>
                        </select>
                    </div>
                </div>
                <hr style="border-style: dashed;"/>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        
                        <label class="uk-width-1-6 uk-margin-small-top"><input class="uk-checkbox uk-margin-small-right" type="checkbox" name="one" value="1" @if($finding_type) @if($finding_type->one) checked @endif @endif> Level One</label> <input class="uk-width-5-6 uk-input" type="text" name="one_description" placeholder="Enter Level 1 Description" @if($finding_type) @if($finding_type->one_description)  value="{{$finding_type->one_description}}" @endif @endif>
                        <hr class="uk-width-1-1 dashed-hr">
                        <label class="uk-width-1-6 uk-margin-top"><input class="uk-checkbox uk-margin-small-right" type="checkbox" name="two" value="1" @if($finding_type) @if($finding_type->two) checked @endif @endif> Level Two</label> <input class="uk-width-5-6 uk-input uk-margin-top" type="text" name="two_description" placeholder="Enter Level 2 Description" @if($finding_type) @if($finding_type->two_description) value="{{$finding_type->two_description}}" @endif @endif>
                        <hr class="uk-width-1-1 dashed-hr">
                        <label class="uk-width-1-6  uk-margin-top"><input class="uk-checkbox uk-margin-small-right" type="checkbox" name="three" value="1" @if($finding_type) @if($finding_type->three) checked @endif @endif> Level Three</label> <input class="uk-width-5-6 uk-input uk-margin-top" type="text" name="three_description" placeholder="Enter Level 3 Description" @if($finding_type) @if($finding_type->three_description) value="{{$finding_type->three_description}}" @endif @endif>
                    </div>
                    
                </div>

                <hr class="uk-margin-top-large " style="border-style: dashed;" />

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <div class="uk-width-1-2">
                            <label for="type" class="uk-width-1-1">Use These Boilerplates as Defaults: </label>
                            @if(count($boilerplates))
                            <div class="uk-width-1-1 uk-scrollable-box">
                                <ul class="uk-list">
                                    @foreach($boilerplates as $boilerplate)
                                    <li><label><input class="uk-checkbox" type="checkbox" name="boilerplates[]" value="{{$boilerplate->id}}" @if($finding_type) @if($finding_type->boilerplates) @if(in_array($boilerplate->id, $finding_type->boilerplates->pluck('boilerplate_id')->toArray())) checked @endif @endif @endif> @if($boilerplate->global)GLOBAL: @else {{$boilerplate->user->name}}'s: @endif {{$boilerplate->name}}</label></li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="uk-width-1-2">
                            <label for="type" class="uk-width-1-1">Apply to These HUD Inspectable Areas: </label>
                            @if(count($huds))
                            <div class="uk-width-1-1 uk-scrollable-box">
                                <ul class="uk-list">
                                    @foreach($huds as $hud)
                                    
                                    <li><label><input class="uk-checkbox" type="checkbox" name="huds[]" value="{{$hud->id}}" @if($finding_type) @if($finding_type->huds()) @if(in_array($hud->id, $finding_type->huds()->pluck('id')->toArray())) checked @endif @endif @endif> {{$hud->name}}</label></li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                

                <div class="uk-form-row">
                    <div class="uk-grid"><hr class="uk-width-1-1 uk-margin-bottom" style="border-style: dashed;"/>
                        <label class="uk-width-2-3">Follow-ups:</label>
                        <div class="uk-width-1-3 uk-text-right  uk-margin-small-top">
                            <button class="uk-button uk-button-default uk-button-small" onclick="addDefaultFollowup(this);return false;">[+] Add a Follow-up</button>
                        </div>
                        <hr class="uk-width-1-1 uk-margin-top" style="border-style: dashed;"/>
                        <div class="uk-width-1-1 form-default-followups">
                            @if($finding_type)
                            @if($finding_type->default_follow_ups)
                            @foreach($finding_type->default_follow_ups as $default_followup)
                            <div class="form-default-followup" uk-grid>
                                <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
                                    <input type="number" min="1" max="31" value="{{$default_followup->quantity}}" class="uk-form-small followup-number" style="height: 20px;">
                                </div>
                                <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
                                    <select class="uk-select uk-form-small followup-duration">
                                        <option value="hours" @if($default_followup->duration == 'hours') selected @endif>Hours</option>
                                        <option value="days" @if($default_followup->duration == 'days') selected @endif>Days</option>
                                        <option value="weeks" @if($default_followup->duration == 'weeks') selected @endif>Weeks</option>
                                        <option value="months" @if($default_followup->duration == 'months') selected @endif>Months</option>
                                    </select>
                                </div>
                                <div class="uk-width-1-6 uk-margin-small-top uk-margin-small-bottom">
                                    <select class="uk-select uk-form-small followup-assignment">
                                        <option value="lead" @if($default_followup->assignment == 'lead') selected @endif>Lead Auditor</option>
                                        <option value="pm" @if($default_followup->assignment == 'pm') selected @endif>Property Manager</option>
                                        <option value="user" @if($default_followup->assignment == 'user') selected @endif>User Creating Finding</option>
                                    </select>
                                </div>
                                <div class="uk-width-1-2  uk-margin-small-top uk-margin-small-bottom">
                                    <input type="text" value="{{$default_followup->description}}" placeholder="Description" class="uk-input uk-form-small followup-description">
                                </div>
                                <div class="uk-width-1-6  uk-margin-small-top">
                                    <label><input class="uk-checkbox followup-reply" type="checkbox" value="1" @if($default_followup->reply) checked @endif> Comment</label><br /><br />
                                    <button class="uk-button uk-button-default uk-button-small" onclick="removeFollowUp(this);return false;"><span uk-icon="minus-circle" class="form-title-icon uk-icon"></span> Remove</button>
                                </div>
                                <div class="uk-width-1-6  uk-margin-small-top">
                                    <label><input class="uk-checkbox followup-photo" type="checkbox" value="1" @if($default_followup->photo) checked @endif> Upload Photo</label>
                                </div>
                                <div class="uk-width-1-6  uk-margin-small-top">
                                    <label><input class="uk-checkbox followup-doc" type="checkbox" value="1" @if($default_followup->doc) checked @endif> Upload a Doc</label>
                                </div>
                                <div class="uk-width-1-2  uk-margin-small-top">
                                    @if(count($document_categories))
                                    <div class="uk-width-1-1 uk-width-2-3@m uk-scrollable-box" style="width:100%; height:100px;">
                                        <ul class="uk-list">
                                            @foreach($document_categories as $cat)
                                            <li><label><input class="uk-checkbox followup-cat" type="checkbox" name="categories[]" value="{{$cat->id}}" @if($default_followup->doc_categories) @if(in_array($cat->id, json_decode($default_followup->doc_categories, true))) checked @endif @endif> {{$cat->document_category_name}}</label></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <input type="submit" id="submit" class="uk-button uk-button-default" style="margin: auto;" name="submit" value="@if($finding_type)UPDATE @else CREATE @endif FINDING TYPE">
                    </div>
                </div>
            </form>
        </div>
<script>
    $ = jQuery;

    function addDefaultFollowup(element){
        var followupTemplate = $('#form-finding-type-followup-template').html();
        $('.form-default-followups').append(followupTemplate);
    }

    function removeFollowUp(element){
        $(element).closest('.form-default-followup').remove();
    }

    function getFormData($form){
        var unindexed_array = $form.serializeArray();
        var indexed_array = {};

        $.map(unindexed_array, function(n, i){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }


    $(document).ready(function(){

        $("form").on('submit',function(e){
            e.preventDefault();
            let form= $(this);
            let action = $(this).attr('action'); 
            var followups = {items: []};
            var boilerplates = {items: []};
            var huds = {items: []};

            $.each($('.form-default-followup'), function(index, followup) {
                var number = $(followup).find('.followup-number').val();
                var duration = $(followup).find('.followup-duration').val();
                var assignment = $(followup).find('.followup-assignment').val();
                var description = $(followup).find('.followup-description').val();
                if($(followup).find('.followup-reply').is(':checked')){
                    var reply = 1;
                }else{
                    var reply = 0;
                }
                if($(followup).find('.followup-photo').is(':checked')){
                    var photo = 1;
                }else{
                    var photo = 0;
                }
                if($(followup).find('.followup-doc').is(':checked')){
                    var doc = 1;
                }else{
                    var doc = 0;
                }
                
                var cats = $(followup).find('.followup-cat:checked').map( function(){
                                return $(this).val();
                            }).get();
                followups.items.push(
                        {
                            number: number,
                            duration: duration,
                            assignment: assignment,
                            description: description,
                            reply: reply,
                            photo: photo,
                            doc: doc,
                            cats: cats
                        }
                    );
            });

            $.each($('input[name^="boilerplates"]'), function(index, element) {
                if($(element).is(':checked')){
                    boilerplates.items.push({
                        id: $(element).val()
                    });
                }
            });

            $.each($('input[name^="huds"]'), function(index, element) {
                if($(element).is(':checked')){
                    huds.items.push({
                        id: $(element).val()
                    });
                }
            });

            $.ajax({
                url: action, 
                method: 'POST',
                data: {
                    'inputs': getFormData(form),
                    'boilerplates': JSON.stringify(boilerplates),
                    'huds': JSON.stringify(huds),
                    'followups' : JSON.stringify(followups),
                    '_token' : '{{ csrf_token() }}'
                },
                success: function(response){
                    // form.remove();
                    // $('h2#post-response').hide().html("<span class='uk-text-success'><span uk-icon='check'></span> "+response+"</span><br /><br /><a onclick=\"dynamicModalLoad('admin/finding_type/create')\" class=\"uk-button uk-button-default uk-width-2-5@m\">CREATE ANOTHER FINDING TYPE</a>").fadeIn();
                    // console.log(action);
                    if(window.current_finding_type_page != 'undefined'){
                        console.log(window.current_finding_type_page);
                        //$('#findingtype-tab-content').load(window.current_finding_type_page);
                    }else{
                        $('#findingtype-tab').trigger('click');
                    }
                    
                    UIkit.modal.alert(response);

                },
                error: function(resp){
                    //form.remove();
                    try {
                    var errorMsg = "<span style='display:none;'>&nbsp;</span>";
                    $.each(JSON.parse(resp.responseText),function(key,value) {
                        errorMsg += "<p class='uk-text-danger' style='font-size:14px;'><span uk-icon='exclamation-circle'></span> "+ value+"</p>";
                    });
                    //$('h2#post-response').hide().html(errorMsg).fadeIn();
                    UIkit.modal.alert('UH OH! Some of the fields are\'t quite right:<hr />'+errorMsg,{stack: true});
                    console.log(errorMsg);
                    }catch(e) {
                        UIkit.modal.alert('OOPS! - The server said something bad happened... to be exact it said:<br /><hr />'+e+'<hr />My friends at <a href="mailto:admin@greenwood360.com">Greenwood 360</a> can probably figure that one out.',{stack: true});
                    }
                }
            });

        });

    });
</script>


