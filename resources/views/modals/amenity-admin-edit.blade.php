        <script>
            function hideHud(hud){
                console.log(hud);
                switch(hud){
                    case 'all':
                            $('.Hud-Site').show();
                            $('.Hud-Building-System').show();
                            $('.Hud-Building-Exterior').show();
                            $('.Hud-Common-Area').show();
                            $('.Hud-Unit').show();
                            $('.Hud-File').show();
                            $('.Hud-Unselected').show();
                            $('.Hud-Selected').show();
                            break;
                            console.log('all');
                    case 'Selected':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').show();
                            break;
                            console.log('Selected');
                    case 'Unselected':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Unselected').show();
                            break;
                            console.log('all');
                    case 'Site':
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Site').show();
                             console.log('project');
                            break;
                    case 'Building System':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Building-System').show();
                             console.log('bs');
                            break;
                    case 'Building Exterior':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Building-Exterior').show();
                             console.log('be');
                            break;
                    case 'Common Area':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Common-Area').show();
                             console.log('ca');
                            break;

                    case 'Unit':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-File').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-Unit').show();
                             console.log('unit');
                            break;
                    case 'File':
                            $('.Hud-Site').hide();
                            $('.Hud-Building-System').hide();
                            $('.Hud-Building-Exterior').hide();
                            $('.Hud-Common-Area').hide();
                            $('.Hud-Unit').hide();
                            $('.Hud-Unselected').hide();
                            $('.Hud-Selected').hide();
                            $('.Hud-File').show();
                             console.log('unit');
                            break;
                    default:
                        console.log('called but nothing happened to '.amenity);
                }
            }
        </script>
        <div>
            <h2 id="post-response" class="uk-margin-top">@if(is_null($amenity))<span uk-icon="plus-circle" class="form-title-icon"></span> Create Amenity @else<span class="uk-icon-edit" ></span> Update Amenity @endif</h2>
            <hr />
            <form @if($amenity) action="/admin/amenity/store/{{$amenity->id}}" @else action="/admin/amenity/store" @endif method="post" target="_blank">
                {{ csrf_field() }}
                
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="amenity_description" class="uk-width-1-1 uk-width-1-3@m">Name: </label>
                        <input id="amenity_description" type="text" name="amenity_description" value="@if($amenity){{$amenity->amenity_description}}@endif" placeholder="Enter the amenity name" class="uk-input uk-width-1-1 uk-width-2-3@m" required>
                    </div>
                </div>
                <hr class="dashed-hr uk-margin-bottom" />
                <div class="uk-form-row">
                    <div class="uk-grid">
                    	<div class="uk-width-1-1 uk-width-1-3@m">
                    		<label>Allita Inspection Type Options: </label>
                    	</div>
                    	<div class="uk-width-1-1 uk-width-2-3@m">
                    		<div uk-grid>
                    			<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="project" type="checkbox" value="1" @if($amenity) @if($amenity->project) checked @endif @endif> Project</label>
		                		</div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="building_system" type="checkbox" value="1"  @if($amenity) @if($amenity->building_system) checked @endif @endif> Building System</label>
		                		</div>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="building_exterior" type="checkbox" value="1"  @if($amenity) @if($amenity->building_exterior) checked @endif @endif> Building Exterior</label>
                                </div>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="common_area" type="checkbox" value="1"  @if($amenity) @if($amenity->common_area) checked @endif @endif> Common Area</label>
                                </div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="unit" type="checkbox" value="1"  @if($amenity) @if($amenity->unit) checked @endif @endif> Unit</label>
		                		</div>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="file" type="checkbox" value="1"  @if($amenity) @if($amenity->file) checked @endif @endif> File</label>
                                </div>
		                		<div class="uk-width-1-4">
			                		<label><input class="uk-checkbox" name="inspectable" type="checkbox" value="1"  @if($amenity) @if($amenity->inspectable) checked @endif @endif> Inspectable</label>
			                	</div>
		                	</div>
	                	</div>
                	</div>
                </div>
                <hr class="dashed-hr uk-margin-bottom" />
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <div class="uk-width-1-1 uk-width-1-3@m">
                            <label uk-tooltip title="Auditors can 'Apply Defaults' in the project detail tab to save time adding basic amenities to projects, buildings, and units.">Use as a Default Amenity for: </label>
                        </div>
                        <div class="uk-width-1-1 uk-width-2-3@m">
                            <div uk-grid>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="project_default" type="checkbox" value="1" @if($amenity) @if($amenity->project_default) checked @endif @endif> Projects</label>
                                </div>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="building_default" type="checkbox" value="1"  @if($amenity) @if($amenity->building_default) checked @endif @endif> Buildings</label>
                                </div>
                                <div class="uk-width-1-4">
                                    <label><input class="uk-checkbox" name="unit_default" type="checkbox" value="1"  @if($amenity) @if($amenity->unit_default) checked @endif @endif> Units</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="dashed-hr uk-margin-bottom" />
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="policy" class="uk-width-1-1 uk-width-1-3@m">Policy: </label>
                        <input id="policy" type="text" name="policy" value="@if($amenity){{$amenity->policy}}@endif" placeholder="Enter the amenity policy" class="uk-input uk-width-1-1 uk-width-2-3@m">
                    </div>
                </div>
                <hr class="dashed-hr uk-margin-bottom" />
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="time" class="uk-width-1-1 uk-width-1-3@m">Time to complete (minutes): </label>
                        <input type="number" name="time" min="1" max="180" value="@if($amenity){{$amenity->time_to_complete}}@endif" class="uk-form-small" style="height: 20px;">
                    </div>
                </div>
                <hr class="dashed-hr uk-margin-bottom" />
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <div class="uk-width-1-1 uk-width-1-3@m">
                            <label>IS A GROUP FOR THESE HUD AREAS</label>
                        </div>
                        

                        @if(count($huds))
                        <div class="uk-width-1-1 uk-width-2-3@m ">
                            <small>
                                <select onchange="hideHud(this.value)" class="uk-select filter-drops " style="height: 30px;
    padding: 1px;
    margin-top: 5px;">
                                    <option value="all">SELECT FROM ALL HUD AREAS</option>
                                    @if($amenity)
                                    <option value="Selected">SELECTED ONLY</option>
                                    <option value="Unselected">ONLY THOSE NOT SELECTED</option>
                                    @endif
                                    <option value="Site">SITE ONLY</option>
                                    <option value="Building System">BUILDING SYSTEM ONLY</option>
                                    <option value="Building Exterior">BUILDING EXTERIOR ONLY</option>
                                    <option value="Common Area">COMMON AREA ONLY</option>
                                    <option value="Unit">UNIT ONLY</option>
                                    <option value="File">FILE ONLY</option>
                                </select>

                            </small>
                            <ul class="uk-list uk-scrollable-box">
                                @foreach($huds as $hud)
                                <li class="@if($hud->site) Hud-Site @endIf @if($hud->building_exterior) Hud-Building-Exterior @endif @if($hud->building_system) Hud-Building-System @endif  @if($hud->common_area) Hud-Common-Area @endIf  @if($hud->unit) Hud-Unit @endIf @if($amenity) @if($amenity->huds) @if(in_array($hud->id, $amenity->huds->pluck('hud_inspectable_area_id')->toArray())) Hud-Selected @else Hud-Unselected @endif @endif @endif"><label><input class="uk-checkbox" type="checkbox" name="huds[]" value="{{$hud->id}}" @if($amenity) @if($amenity->huds) @if(in_array($hud->id, $amenity->huds->pluck('hud_inspectable_area_id')->toArray())) checked @endif @endif @endif> {{$hud->name}}<br />
                                    <span class="gray-text uk-margin-large-left">
                                    @if($hud->site)• Site @endIf 
                                    @if($hud->building_exterior)• Building Exterior @endif
                                    @if($hud->building_system)• Building System @endif 
                                    @if($hud->common_area)• Common Area @endIf 
                                    @if($hud->unit)• Unit @endIf</span></label></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                    </div>
                </div>
                <div class="uk-form-row">
                    <div class="uk-grid">
                    	<div class="uk-width-1-1 uk-width-1-3@m">
                    		<label>Icon Used When at Project Level: </label>
                    	</div>
                    	<div class="uk-width-1-1 uk-width-2-3@m uk-scrollable-box">
                    		<div uk-grid>
                    			<div class="uk-width-1-6">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-buildings" @if($amenity) @if($amenity->icon == 'a-buildings' || $amenity->icon == null) checked @endif @endif> <i class="a-buildings" style="font-size: 40px;"></i></label>
		                		</div>
		                		<div class="uk-width-1-6">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-buildings-2" @if($amenity) @if($amenity->icon == 'a-buildings-2') checked @endif @endif> <i class="a-buildings-2" style="font-size: 40px;"></i></label>
		                		</div>
		                		<div class="uk-width-1-6">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-pool" @if($amenity) @if($amenity->icon == 'a-pool') checked @endif @endif> <i class="a-pool" style="font-size: 40px;"></i></label>
		                		</div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-lightbulb-2" @if($amenity) @if($amenity->icon == 'a-lightbulb-2') checked @endif @endif> <i class="a-lightbulb-2" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-tools" @if($amenity) @if($amenity->icon == 'a-tools') checked @endif @endif> <i class="a-tools" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-washer" @if($amenity) @if($amenity->icon == 'a-washer') checked @endif @endif> <i class="a-washer" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-fridge" @if($amenity) @if($amenity->icon == 'a-fridge') checked @endif @endif> <i class="a-fridge" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-teapot" @if($amenity) @if($amenity->icon == 'a-teapot') checked @endif @endif> <i class="a-teapot" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-tv" @if($amenity) @if($amenity->icon == 'a-tv') checked @endif @endif> <i class="a-tv" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-toaster" @if($amenity) @if($amenity->icon == 'a-toaster') checked @endif @endif> <i class="a-toaster" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-coffee-machine" @if($amenity) @if($amenity->icon == 'a-coffee-machine') checked @endif @endif> <i class="a-coffee-machine" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-bus" @if($amenity) @if($amenity->icon == 'a-bus') checked @endif @endif> <i class="a-bus" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-bus" @if($amenity) @if($amenity->icon == 'a-bus') checked @endif @endif> <i class="a-bus" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-books" @if($amenity) @if($amenity->icon == 'a-books') checked @endif @endif> <i class="a-books" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-rulers" @if($amenity) @if($amenity->icon == 'a-rulers') checked @endif @endif> <i class="a-rulers" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-weights" @if($amenity) @if($amenity->icon == 'a-weights') checked @endif @endif> <i class="a-weights" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-bicycle" @if($amenity) @if($amenity->icon == 'a-bicycle') checked @endif @endif> <i class="a-bicycle" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-ambulance" @if($amenity) @if($amenity->icon == 'a-ambulance') checked @endif @endif> <i class="a-ambulance" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-syringe" @if($amenity) @if($amenity->icon == 'a-syringe') checked @endif @endif> <i class="a-syringe" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-scale" @if($amenity) @if($amenity->icon == 'a-scale') checked @endif @endif> <i class="a-scale" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-oven" @if($amenity) @if($amenity->icon == 'a-oven') checked @endif @endif> <i class="a-oven" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-scale" @if($amenity) @if($amenity->icon == 'a-scale') checked @endif @endif> <i class="a-scale" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-stovetop" @if($amenity) @if($amenity->icon == 'a-stovetop') checked @endif @endif> <i class="a-stovetop" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-microwave" @if($amenity) @if($amenity->icon == 'a-microwave') checked @endif @endif> <i class="a-microwave" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-truck-2" @if($amenity) @if($amenity->icon == 'a-truck-2') checked @endif @endif> <i class="a-truck-2" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-package" @if($amenity) @if($amenity->icon == 'a-package') checked @endif @endif> <i class="a-package" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-man-4" @if($amenity) @if($amenity->icon == 'a-man-4') checked @endif @endif> <i class="a-man-4" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-basketball" @if($amenity) @if($amenity->icon == 'a-basketball') checked @endif @endif> <i class="a-basketball" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-piano" @if($amenity) @if($amenity->icon == 'a-piano') checked @endif @endif> <i class="a-piano" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-swimmer" @if($amenity) @if($amenity->icon == 'a-swimmer') checked @endif @endif> <i class="a-swimmer" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-bottle" @if($amenity) @if($amenity->icon == 'a-bottle') checked @endif @endif> <i class="a-bottle" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-pingpong" @if($amenity) @if($amenity->icon == 'a-pingpong') checked @endif @endif> <i class="a-pingpong" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-nurse" @if($amenity) @if($amenity->icon == 'a-nurse') checked @endif @endif> <i class="a-nurse" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-stetoscope" @if($amenity) @if($amenity->icon == 'a-stetoscope') checked @endif @endif> <i class="a-stetoscope" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-water" @if($amenity) @if($amenity->icon == 'a-water') checked @endif @endif> <i class="a-water" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-night" @if($amenity) @if($amenity->icon == 'a-night') checked @endif @endif> <i class="a-night" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-sun" @if($amenity) @if($amenity->icon == 'a-sun') checked @endif @endif> <i class="a-sun" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-sailboat" @if($amenity) @if($amenity->icon == 'a-sailboat') checked @endif @endif> <i class="a-sailboat" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-recycle" @if($amenity) @if($amenity->icon == 'a-recycle') checked @endif @endif> <i class="a-recycle" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-beer" @if($amenity) @if($amenity->icon == 'a-beer') checked @endif @endif> <i class="a-beer" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-trash-recycle" @if($amenity) @if($amenity->icon == 'a-trash-recycle') checked @endif @endif> <i class="a-trash-recycle" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-poolchair" @if($amenity) @if($amenity->icon == 'a-poolchair') checked @endif @endif> <i class="a-poolchair" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-electric-car" @if($amenity) @if($amenity->icon == 'a-electric-car') checked @endif @endif> <i class="a-electric-car" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-tramway" @if($amenity) @if($amenity->icon == 'a-tramway') checked @endif @endif> <i class="a-tramway" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-factory-leaf" @if($amenity) @if($amenity->icon == 'a-factory-leaf') checked @endif @endif> <i class="a-factory-leaf" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-tree" @if($amenity) @if($amenity->icon == 'a-tree') checked @endif @endif> <i class="a-tree" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-lightning" @if($amenity) @if($amenity->icon == 'a-lightning') checked @endif @endif> <i class="a-lightning" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-windturbine" @if($amenity) @if($amenity->icon == 'a-windturbine') checked @endif @endif> <i class="a-windturbine" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-spade" @if($amenity) @if($amenity->icon == 'a-spade') checked @endif @endif> <i class="a-spade" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-floorplan" @if($amenity) @if($amenity->icon == 'a-floorplan') checked @endif @endif> <i class="a-floorplan" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-electrical-warning" @if($amenity) @if($amenity->icon == 'a-electrical-warning') checked @endif @endif> <i class="a-electrical-warning" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-saw" @if($amenity) @if($amenity->icon == 'a-saw') checked @endif @endif> <i class="a-saw" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-fence" @if($amenity) @if($amenity->icon == 'a-fence') checked @endif @endif> <i class="a-fence" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-phone-3" @if($amenity) @if($amenity->icon == 'a-phone-3') checked @endif @endif> <i class="a-phone-3" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-gear-2" @if($amenity) @if($amenity->icon == 'a-gear-2') checked @endif @endif> <i class="a-gear-2" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-tools-2" @if($amenity) @if($amenity->icon == 'a-tools-2') checked @endif @endif> <i class="a-tools-2" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-faces-left" @if($amenity) @if($amenity->icon == 'a-faces-left') checked @endif @endif> <i class="a-faces-left" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-chess-tower" @if($amenity) @if($amenity->icon == 'a-chess-tower') checked @endif @endif> <i class="a-chess-tower" style="font-size: 40px;"></i></label>
                                </div>
                                <div class="uk-width-1-6">
                                    <label><input class="uk-radio" name="icon" type="radio" value="a-circle-keyhole" @if($amenity) @if($amenity->icon == 'a-circle-keyhole') checked @endif @endif> <i class="a-circle-keyhole" style="font-size: 40px;"></i></label>
                                </div>
		                	</div>
	                	</div>
                	</div>
                </div>
                <hr class="uk-margin-bottom">
                <div class="uk-form-row">
                    <div class="uk-grid uk-text-right">
                        <div class="uk-width-1-3"></div>
                        <input type="submit" id="submit" class="uk-button uk-button-success  uk-width-2-3" style="margin: auto;" name="submit" value="@if(is_null($amenity))CREATE @else Update @endif AMENITY">
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
            var huds = {items: []};

            $.each($('input[name^="huds"]'), function(index, element) {
                if($(element).is(':checked')){
                    huds.items.push({
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
                    'huds': JSON.stringify(huds),
                    '_token' : '{{ csrf_token() }}'
                },
                success: function(response){
                    // form.remove();
                    // $('h2#post-response').hide().html("<span class='uk-text-success'><span uk-icon='check'></span> "+response+"</span>").fadeIn();
                    // console.log(action);
                    $('#amenities-tab').trigger('click');
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


