        <div>
            <h2 id="post-response" class="uk-margin-top">@if($formRows['tag'][4])<span uk-icon="{{$formRows['tag'][4]}}" class="form-title-icon"></span>@else<img src="/apple-icon-57x57.png" >@endif {{$formRows['tag'][3]}}</h2>
            <hr />
            <form action="{{$formRows['tag'][0]}}" method="{{$formRows['tag'][1]}}" target="_blank">
                {{ csrf_field() }}

                @foreach($formRows['rows'] as $row)
                    @if($row['type']=='text')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['for']; ?>: </label>
                                <input id="{{$row['for']}}" type="text" name="{{$row['name']}}" value="{{$row['value']}}" placeholder="{{$row['placeholder']}}" class="uk-input uk-width-1-1 uk-width-1-2@m"
                                       @if($row['required']=='required') required @endif>
                            </div>
                        </div>
                    @elseif($row['type']=='password')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['for'];?>: </label>
                                <input id="{{$row['for']}}" type="password" name="{{$row['name']}}" value="{{$row['value']}}" placeholder="{{$row['placeholder']}}" class="uk-input uk-width-1-1 uk-width-1-2@m"
                                       @if($row['required']=='required') required @endif>
                            </div>
                        </div>
                    @elseif($row['type']=='select')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['for']; ?>: </label>
                                <select id="{{$row['for']}}" name="{{$row['name']}}"  class="uk-select uk-width-1-1 uk-width-1-2@m" @if($row['required']=='required') required @endif
                                @if($row['multiple']) multiple @endif>
                                    @for($i=0; $i<count($row['value']); $i++)
                                        <option value="{{$row['value'][$i]}}" @if($row['selected'][$i]=="true") selected @endif>{{$row['optionLabel'][$i]}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @elseif($row['type']=='textarea')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['for']; ?>: </label>
                                <textarea id="{{$row['id']}}" name="{{$row['name']}}" placeholder="{{$row['placeholder']}}" ="" class="uk-textarea uk-width-1-1 uk-width-1-2@m" @if($row['required']=='required') required @endif>{{$row['value']}}</textarea>
                            </div>
                        </div>
                    @elseif($row['type']=='checkbox')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['for']; ?>: </label>
                                <input type="checkbox" id="{{$row['id']}}" name="{{$row['name']}}" class="uk-checkbox" value="{{$row['value']}}" @if($row['checked']=="true") checked @endif style="height: 25px;">
                                    {{$row['optionLabel']}}
                              
                            </div>
                        </div>
                    @elseif($row['type']=='radio')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo$row['for']; ?>: </label>
                                @for($i=0; $i<count($row['value']); $i++)
                                    <input style="margin: 2px 7px 0 15px;" type="radio" id="{{$row['id']}}" class="uk-radio" name="{{$row['name']}}" value="{{$row['value'][$i]}}" @if($row['checked'][$i]=="true") checked @endif>
                                    {{$row['optionLabel'][$i]}}
                                @endfor
                            </div>
                        </div>
                    @elseif($row['type']=='file')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['label']}}" class="uk-width-1-1 uk-width-1-2@m"><?php echo $row['label']; ?>: </label>
                                    <input type="file" class="uk-input " id="{{$row['id']}}" name="{{$row['name']}}" accept="{{$row['accept']}}" @if($row['required']) required @endif>
                            </div>
                        </div>
                    @elseif($row['type']=='reset')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <input type="reset" class="uk-input uk-button uk-button-default" style="margin: auto;" name="{{$row['name']}}" value="{{$row['value']}}">
                            </div>
                        </div>
                    @elseif($row['type']=='submit')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <input type="submit" id="submit" class="uk-button uk-button-default" style="margin: auto;" name="{{$row['name']}}" value="{{$row['value']}}">
                            </div>
                        </div>
                    @elseif($row['type']=='button')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <input type="button" class="uk-button uk-button-default" style="margin: auto;" name="{{$row['name']}}" value="{{$row['value']}}">
                            </div>
                        </div>
                    @elseif($row['type']=='image')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <input type="image" name="{{$row['name']}}" src="{{$row['src']}}">
                            </div>
                        </div>
                    @elseif($row['type']=='hidden')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <input type="hidden" name="{{$row['name']}}" value="{{$row['value']}}">
                            </div>
                        </div>
                    @elseif($row['type']=='multipleText')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{$row['for']}}" class="uk-width-1-1 uk-width-1-1@m uk-text-center"><strong>{{$row['for']}} </strong></label>
                                <div class="uk-width-1-1 uk-width-1-1@m">
                                    <div class="uk-grid uk-grid-small">
                                        <div  class="uk-width-1-1 uk-width-1-2@m" style="margin-bottom: 15px;">
                                            <div class="uk-form-controls">
                                                <input type="checkbox" class="uk-checkbox checkMax"  id="{{$row['id']}}" name="{{$row[0]['name']}}" value="{{$row[0]['value']}}" @if($row['checked']==true) checked @endif>
                                                {{$row[0]['placeholder']}}?
                                            </div>
                                        </div>
                                        <div  class="uk-width-1-1 uk-width-1-2@m">
                                            <div class="uk-form-label advance" style="@if($row['checked']==true) display:inline; @else display: none;@endif">{{$row[0]['placeholder']}}</div>
                                            <div class="uk-form-controls">
                                                <input class="uk-input advance" id="{{$row['for']}}" style="width: 90%; margin-bottom: 5px; @if($row['checked']==true) display:inline; @else display: none;@endif" type="text" name="{{$row[1]['name']}}" value="{{$row[1]['value']}}" placeholder="{{$row[1]['placeholder']}}"
                                                       @if($row[1]['required']=='required') required @endif>
                                            </div>
                                        </div>
                                        @for($i=2; $i<$row['cells']; $i++)
                                            <div  class="uk-width-1-1 uk-width-1-2@m">
                                                <div class="uk-form-label">{{$row[$i]['placeholder']}}</div>
                                                <div class="uk-form-controls">
                                                    <input id="{{$row['for']}}" style="width: 90%; margin-bottom: 5px;" type="text" name="{{$row[$i]['name']}}" value="{{$row[$i]['value']}}" class="uk-input" placeholder="{{$row[$i]['placeholder']}}"
                                                           @if($row[$i]['required']=='required') required @endif>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                </div>

                            </div>

                        </div>
                    @elseif($row['type']=='multipleText1')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="parcel_reimbursement_rules" class="uk-width-1-1 uk-width-1-1@m uk-text-center">
                                    <strong>Parcel reimbursement rules </strong>
                                    <span id="add-reimburse-field" uk-icon="plus-square" class="uk-text-primary" title="Add reimbursement field"></span></label>

                                <div class="uk-width-1-1 uk-width-1-1@m row-grid">
                                    <div class="uk-grid uk-grid-small reimburse">
                                        @for($i=0; $i<$row['cells']; $i++)
                                            <div  class="uk-width-1-1 uk-width-1-3@m" id="inputs">
                                                <div class="uk-form-label">{{$row[$i]['placeholder']}}</div>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input @if($i==0) field_trigger @endif" style="width: 100%; margin-bottom: 5px;" type="text" name="{{$row[$i]['name']}}[]" value="{{$row[$i]['value']}}" placeholder="{{$row[$i]['placeholder']}}"
                                                       @if($row[$i]['required']=='required') required @endif>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                </div>

                            </div>

                        </div>
                    @elseif($row['type']=='documentRule')
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{strtolower($row['label'])}}" class="uk-width-1-1 uk-width-1-1@m uk-text-center">
                                    <strong>{{$row['for']}} </strong>
                                </label>
                                    <div class="uk-width-1-1 uk-width-1-1@m">
                                        <div class="uk-grid uk-grid-small">
                                            <div  class="uk-width-1-1 uk-width-1-2@m uk-margin-small-bottom">
                                                <input type="text" name="{{$row['textInput'][0]}}" value="" class="uk-input" placeholder="{{$row['textInput'][2]}}" >
                                            </div>
                                            <div  class="uk-width-1-1 uk-width-1-2@m uk-margin-small-bottom ui grid">
                                                <select name="{{$row['selectInput'][0]}}[]" style="width:90%;" multiple="multiple" class="uk-select ui fluid dropdown">
                                                    @for($i=0; $i<count($row['selectInput']['options']['option_values']); $i++ ))
                                                    <option value="{{$row['selectInput']['options']['option_values'][$i]}}"
                                                    @if(!empty($row['selectInput']['options']['selected']))
                                                    @if($row['selectInput']['options']['selected'][$i]) selected @endif @endif>
                                                        {{$row['selectInput']['options']['option_names'][$i]}}
                                                    </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <input type="hidden" name="id" value="{{$row['hidden']}}">
                                        </div>
                                    </div>
                            </div>
                        </div>
                    @elseif($row['type']=='documentRule13')
                        <div class="uk-form-row">
                            <h2 class="uk-text-center uk-text-extramuted "><strong>Document Rules</strong> </h2>
                        </div>
                        <div class="uk-form-row">
                            <div class="uk-grid">
                                <label for="{{strtolower($row['label'])}}" class="uk-width-1-1 uk-width-1-1@m uk-text-center">
                                    <strong>{{$row['for']}} </strong>
                                </label>
                                    <div class="uk-width-1-1 uk-width-1-1@m">
                                        <div class="uk-grid uk-grid-small">
                                            <div  class="uk-width-1-1 uk-width-1-2@m uk-margin-small-bottom">
                                                <span>&nbsp;</span>
                                            </div>
                                            <div  class="uk-width-1-1 uk-width-1-2@m uk-margin-small-bottom ui grid">
                                                <select name="{{$row['selectInput'][0]}}[]" style="width:90%;" multiple="multiple" class="uk-select ui fluid dropdown">
                                                    @for($i=0; $i<count($row['selectInput']['options']['option_values']); $i++ ))
                                                    <option value="{{$row['selectInput']['options']['option_values'][$i]}}"
                                                            @if(!empty($row['selectInput']['options']['selected']))
                                                            @if($row['selectInput']['options']['selected'][$i]) selected @endif @endif>
                                                        {{$row['selectInput']['options']['option_names'][$i]}}
                                                    </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <input type="hidden" name="id" value="{{$row['hidden']}}">
                                        </div>
                                    </div>
                            </div>
                        </div>
                    @endif
                @endforeach

            
            </form>
        </div>
<script>
    $ = jQuery;
    $(document).ready(function(){
        let row_el = $(".doc-rule:last").html();
        let row_data = $(".counter").data('counter');
        console.log(row_data);
        $("#new-rule").on("click", function(){
            $(".row-repeat").find(".doc-rule:last").prepend(row_el);
//            $.get('/row/counter/document',function(res));

        });

        $("form").on('submit',function(e){
            e.preventDefault();
            let form= $(this);
            let action = $(this).attr('action');
            $.ajax(action, {
                type: 'POST',
                data: form.serialize(),
                success: function(response){
                    form.remove();
                    $('h2#post-response').hide().html("<span class='uk-text-success'><span uk-icon='check'></span> "+response+"</span>").fadeIn();
                    console.log(action);
                    switch (action){
                        case "/admin/program/store":
                            $('#programs-tab').trigger('click');
                            break;
                        case "/admin/document_category/store":
                            $('#document-tab').trigger('click');
                            break;
                        case "/admin/boilerplate/store":
                            $('#boilerplate-tab').trigger('click');
                            break;
                        case "/admin/findingtype/store":
                            $('#findingtype-tab').trigger('click');
                            break;
                        case "/admin/hud_area/store":
                            $('#hud-tab').trigger('click');
                            break;
                    }

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

//        $("input.advance").hide();
//        if($("input.advance").prop('checked',true)){
//            $(this).closest(".uk-grid").find("input.advance").show();
//        }
        $(".checkMax").on('click', function(){
            $(this).closest(".uk-grid").find("input.advance").toggle(this.checked);
        });

        let reimburse_row = $(".reimburse:last").html();
        $("#add-reimburse-field").on('click', function(){
            $(".reimburse:last").prepend(reimburse_row);
        })























    });
</script>


