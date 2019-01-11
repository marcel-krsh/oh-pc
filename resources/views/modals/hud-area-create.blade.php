        <script>
            function hideAmenity(amenity){
                console.log(amenity);
                switch(amenity){
                    case 'all':
                            $('.Amenity-Project').show();
                            $('.Amenity-Building-System').show();
                            $('.Amenity-Building-Exterior').show();
                            $('.Amenity-Common-Area').show();
                            $('.Amenity-Unit').show();
                            break;
                            console.log('all');
                    case 'Selected':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Common-Area').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Selected').show();
                            break;
                            console.log('Selected');
                    case 'Unselected':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Common-Area').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Unselected').show();
                            break;
                            console.log('all');
                    case 'Project':
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Common-Area').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Project').show();
                             console.log('project');
                            break;
                    case 'Building System':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Common-Area').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Building-System').show();
                             console.log('bs');
                            break;
                    case 'Building Exterior':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Common-Area').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Building-Exterior').show();
                             console.log('be');
                            break;
                    case 'Common Area':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Unit').hide();

                            $('.Amenity-Common-Area').show();
                             console.log('ca');
                            break;

                    case 'Unit':
                            $('.Amenity-Project').hide();
                            $('.Amenity-Building-System').hide();
                            $('.Amenity-Building-Exterior').hide();
                            $('.Amenity-Common-Area').hide();

                            $('.Amenity-Unit').show();
                             console.log('unit');
                            break;
                    default:
                        console.log('called but nothing happened to '.amenity);
                }
                
                

            }

            function hideFinding(finding){
                console.log(finding);
                switch(finding){
                    case 'all':
                            $('.Finding-Site').show();
                            $('.Finding-Building-System').show();
                            $('.Finding-Building-Exterior').show();
                            $('.Finding-Common-Area').show();
                            $('.Finding-Unit').show();
                            break;
                            console.log('all');
                    case 'Selected':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-System').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Common-Area').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Selected').show();
                            break;
                            console.log('Selected');
                    case 'Unselected':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-System').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Common-Area').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Unselected').show();
                            break;
                            console.log('all');
                    case 'Site':
                            $('.Finding-Building-System').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Common-Area').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Site').show();
                             console.log('project');
                            break;
                    case 'Building System':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Common-Area').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Building-System').show();
                             console.log('bs');
                            break;
                    case 'Building Exterior':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-System').hide();
                            $('.Finding-Common-Area').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Building-Exterior').show();
                             console.log('be');
                            break;
                    case 'Common Area':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-System').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Unit').hide();

                            $('.Finding-Common-Area').show();
                             console.log('ca');
                            break;

                    case 'Unit':
                            $('.Finding-Site').hide();
                            $('.Finding-Building-System').hide();
                            $('.Finding-Building-Exterior').hide();
                            $('.Finding-Common-Area').hide();

                            $('.Finding-Unit').show();
                             console.log('unit');
                            break;
                    default:
                        $('.Finding-Site').hide();
                        $('.Finding-Building-System').hide();
                        $('.Finding-Building-Exterior').hide();
                        $('.Finding-Common-Area').hide();
                        $('.Finding-Unit').hide();
                        console.log('called but nothing happened to '.finding);
                }
                
                

            }
        </script>
        <div>
            <h2 id="post-response" class="uk-margin-top">@if(!$hud)<span uk-icon="plus-circle" class="form-title-icon"></span> Create HUD Inspectable Area @else<span class="uk-icon-edit" ></span> Update HUD Inspectable Area @endif</h2>
            <hr />
            <form @if($hud) action="/admin/hud_area/store/{{$hud->id}}" @else action="/admin/hud_area/store" @endif method="post" target="_blank">
                {{ csrf_field() }}
                
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="name" class="uk-width-1-1 uk-width-1-3@m">Name: </label>
                        <input id="name" type="text" name="name" value="@if($hud){{$hud->name}}@endif" placeholder="Enter the HUD area name" class="uk-input uk-width-1-1 uk-width-2-3@m" required>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="type" class="uk-width-1-1 uk-width-1-3@m">Associated Amenities: <br>
                            
                            
                        </label>
                        @if(count($amenities))
                        <div class="uk-width-1-1 uk-width-2-3@m ">
                            <small>
                                <select onchange="hideAmenity(this.value)" class="uk-select filter-drops " style="height: 30px;
    padding: 1px;
    margin-top: 5px;">
                                    <option value="all">SELECT FROM ALL AMENITIES</option>
                                    <option value="Selected">SELECTED ONLY</option>
                                    <option value="Unselected">ONLY THOSE NOT SELECTED</option>
                                    <option value="Project">PROJECT ONLY</option>
                                    <option value="Building System">BUILDING SYSTEM ONLY</option>
                                    <option value="Building Exterior">BUILDING EXTERIOR ONLY</option>
                                    <option value="Common Area">COMMON AREA ONLY</option>
                                    <option value="Unit">UNIT ONLY</option>
                                </select>

                            </small>
                            <ul class="uk-list uk-scrollable-box">
                                @foreach($amenities as $amenity)
                                <li class="@if($amenity->project) Amenity-Project @endIf @if($amenity->building_exterior) Amenity-Building-Exterior @endif @if($amenity->building_system) Amenity-Building-System @endif  @if($amenity->common_area) Amenity-Common-Area @endIf  @if($amenity->unit) Amenity-Unit @endIf @if($hud) @if($hud->amenities) @if(in_array($amenity->id, $hud->amenities->pluck('amenity_id')->toArray())) Amenity-Selected @else Amenity-Unselected @endif @endif @endif"><label><input class="uk-checkbox" type="checkbox" name="amenities[]" value="{{$amenity->id}}" @if($hud) @if($hud->amenities) @if(in_array($amenity->id, $hud->amenities->pluck('amenity_id')->toArray())) checked @endif @endif @endif> {{$amenity->amenity_description}}</label><br />
                                    <span class="gray-text uk-margin-large-left">
                                    @if($amenity->project)• Project @endIf 
                                    @if($amenity->building_exterior)• Building Exterior @endif
                                    @if($amenity->building_system)• Building System @endif 
                                    @if($amenity->common_area)• Common Area @endIf 
                                    @if($amenity->unit)• Unit @endIf

                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                    <label for="type" class="uk-width-1-1 uk-width-1-3@m">Possible Findings: <br />
                        
                    </label>
                    @if(count($findingTypes))
                    <div class="uk-width-1-1 uk-width-2-3@m ">
                        <small>
                                <select onchange="hideFinding(this.value)" class="uk-select filter-drops " style="height: 30px;
    padding: 1px;
    margin-top: 5px;">
                                    <option value="all">SELECT FROM ALL FINDINGS</option>
                                    <option value="Selected">SELECTED ONLY</option>
                                    <option value="Unselected">ONLY THOSE NOT SELECTED</option>
                                    <option value="Site">SITE ONLY</option>
                                    <option value="Building System">BUILDING SYSTEM ONLY</option>
                                    <option value="Building Exterior">BUILDING EXTERIOR ONLY</option>
                                    <option value="Common Area">COMMON AREA ONLY</option>
                                    <option value="Unit">UNIT ONLY</option>
                                </select>

                            </small>
                        <ul class="uk-list uk-scrollable-box">
                            @foreach($findingTypes as $findingType)
                                <li class="@if($findingType->site) Finding-Site @endIf @if($findingType->building_exterior) Finding-Building-Exterior @endif @if($findingType->building_system) Finding-Building-System @endif  @if($findingType->common_area) Finding-Common-Area @endIf  @if($findingType->unit) Finding-Unit @endIf @if($hud) @if($hud->finding_types) @if(in_array($findingType->id, $hud->finding_types->pluck('amenity_id')->toArray())) Amenity-Selected @else Amenity-Unselected @endif @endif @endif"><label><input class="uk-checkbox" type="checkbox" name="hudFindingTypes[]" value="{{$findingType->id}}" @if($hud) @if($hud->finding_types) @if(in_array($findingType->id, $hud->finding_types->pluck('finding_type_id')->toArray())) checked @endif @endif @endif>

                                    {{$findingType->name}}<br />
                                    <span class="gray-text uk-margin-large-left">
                                    @if($findingType->site)• Site @endIf 
                                    @if($findingType->building_exterior)• Building Exterior @endif
                                    @if($findingType->building_system)• Building System @endif 
                                    @if($findingType->common_area)• Common Area @endIf 
                                    @if($findingType->unit)• Unit @endIf
                                    </span>
                                </label></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <input type="submit" id="submit" class="uk-button uk-button-default uk-align-right" style="margin: auto;" name="submit" value="@if($hud)UPDATE @else CREATE @endIf HUD AREA">
                    </div>
                </div>
            </form>
        </div>
<script>
    $ = jQuery;

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
            var amenities = {items: []};
            var findingTypes = {items: []};

            $.each($('input[name^="hudFindingTypes"]'), function(index, element) {
                if($(element).is(':checked')){
                    findingTypes.items.push({
                        id: $(element).val()
                    });
                    //alert('Finding Type Added');
                }
            });

            $.each($('input[name^="amenities"]'), function(index, element) {
                if($(element).is(':checked')){
                    amenities.items.push({
                        id: $(element).val()
                    });
                    //alert('Amenity Added');
                }
            });


            $.ajax({
                url: action, 
                method: 'POST',
                data: {
                    'inputs': getFormData(form),
                    'amenities': JSON.stringify(amenities),
                    'findingTypes': JSON.stringify(findingTypes),
                    '_token' : '{{ csrf_token() }}'
                },
                success: function(response){
                    form.remove();
                    $('h2#post-response').hide().html("<span class='uk-text-success'><span uk-icon='check'></span> "+response+"</span><br /><br /><a onclick=\"dynamicModalLoad('admin/hud_area/create')\" class=\"uk-button uk-button-default uk-width-2-5@m\">CREATE ANOTHER HUD AREA</a>").fadeIn();
                    console.log(action);
                    $('#hud-tab').trigger('click');

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


