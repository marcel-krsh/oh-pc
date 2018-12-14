        <div>
            <h2 id="post-response" class="uk-margin-top">@if(!$amenity)<span uk-icon="plus-circle" class="form-title-icon"></span> Create Amenity @else<span class="uk-icon-edit" ></span> Update Amenity @endif</h2>
            <hr />
            <form @if($amenity) action="/admin/amenity/store/{{$amenity->id}}" @else action="/admin/amenity/store" @endif method="post" target="_blank">
                {{ csrf_field() }}
                
                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="amenity_description" class="uk-width-1-1 uk-width-1-3@m">Name: </label>
                        <input id="amenity_description" type="text" name="amenity_description" value="@if($amenity){{$amenity->amenity_description}}@endif" placeholder="Enter the amenity name" class="uk-input uk-width-1-1 uk-width-2-3@m" required>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                    	<div class="uk-width-1-1 uk-width-1-3@m">
                    		<label>Options: </label>
                    	</div>
                    	<div class="uk-width-1-1 uk-width-2-3@m">
                    		<div uk-grid>
                    			<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="project" type="checkbox" value="1" @if($amenity) @if($amenity->project) checked @endif @endif> Project</label>
		                		</div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="building" type="checkbox" value="1"  @if($amenity) @if($amenity->building) checked @endif @endif> Building</label>
		                		</div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-checkbox" name="unit" type="checkbox" value="1"  @if($amenity) @if($amenity->unit) checked @endif @endif> Unit</label>
		                		</div>
		                		<div class="uk-width-1-4">
			                		<label><input class="uk-checkbox" name="inspectable" type="checkbox" value="1"  @if($amenity) @if($amenity->inspectable) checked @endif @endif> Inspectable</label>
			                	</div>
		                	</div>
	                	</div>
                	</div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="policy" class="uk-width-1-1 uk-width-1-3@m">Policy: </label>
                        <input id="policy" type="text" name="policy" value="@if($amenity){{$amenity->policy}}@endif" placeholder="Enter the amenity policy" class="uk-input uk-width-1-1 uk-width-2-3@m">
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <label for="time" class="uk-width-1-1 uk-width-1-3@m">Time to complete (minutes): </label>
                        <input type="number" name="time" min="1" max="180" value="@if($amenity){{$amenity->time_to_complete}}@endif" class="uk-form-small" style="height: 20px;">
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                    	<div class="uk-width-1-1 uk-width-1-3@m">
                    		<label>Icon: </label>
                    	</div>
                    	<div class="uk-width-1-1 uk-width-2-3@m">
                    		<div uk-grid>
                    			<div class="uk-width-1-4">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-buildings" @if($amenity) @if($amenity->icon == 'a-buildings' || $amenity->icon == null) checked @endif @endif> <i class="a-buildings" style="font-size: 40px;"></i></label>
		                		</div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-buildings-2" @if($amenity) @if($amenity->icon == 'a-buildings-2') checked @endif @endif> <i class="a-buildings-2" style="font-size: 40px;"></i></label>
		                		</div>
		                		<div class="uk-width-1-4">
		                			<label><input class="uk-radio" name="icon" type="radio" value="a-pool" @if($amenity) @if($amenity->icon == 'a-pool') checked @endif @endif> <i class="a-pool" style="font-size: 40px;"></i></label>
		                		</div>
		                	</div>
	                	</div>
                	</div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <input type="submit" id="submit" class="uk-button uk-button-default" style="margin: auto;" name="submit" value="Update Amenity">
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

            $.ajax({
                url: action, 
                method: 'POST',
                data: {
                    'inputs': getFormData(form),
                    '_token' : '{{ csrf_token() }}'
                },
                success: function(response){
                    form.remove();
                    $('h2#post-response').hide().html("<span class='uk-text-success'><span uk-icon='check'></span> "+response+"</span>").fadeIn();
                    console.log(action);
                    $('#amenities-tab').trigger('click');

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


