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
                        <label for="type" class="uk-width-1-1 uk-width-1-3@m">Amenities: </label>
                        @if(count($amenities))
                        <div class="uk-width-1-1 uk-width-2-3@m uk-scrollable-box">
                            <ul class="uk-list">
                                @foreach($amenities as $amenity)
                                <li><label><input class="uk-checkbox" type="checkbox" name="amenities[]" value="{{$amenity->id}}" @if($hud) @if($hud->amenities) @if(in_array($amenity->id, $hud->amenities->pluck('amenity_id')->toArray())) checked @endif @endif @endif> {{$amenity->amenity_description}}</label></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-grid">
                        <input type="submit" id="submit" class="uk-button uk-button-default" style="margin: auto;" name="submit" value="Create HUD Area">
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

            $.each($('input[name^="amenities"]'), function(index, element) {
                if($(element).is(':checked')){
                    amenities.items.push({
                        id: $(element).val()
                    });
                }
            });

            $.ajax({
                url: action, 
                method: 'POST',
                data: {
                    'inputs': getFormData(form),
                    'amenities': JSON.stringify(amenities),
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


