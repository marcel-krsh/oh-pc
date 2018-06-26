<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($entities)}} TOTAL ENTITIES <small>

            <a onclick="$('.inactiveEntity').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveEntity">SHOW</span>
                <span class="inactiveEntity" style="display:none;">HIDE</span> DISABLED ENTITIES
            </a>
        </small>
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>ENTITY NAME</small>
        </th>
        <th>
            <small>OWNER</small>
        </th>
        <th>
            <small>ADDRESS</small>
        </th>
        <th>
            <small>CITY</small>
        </th>
        <th>
            <small>STATE</small>
        </th>
        <th>
            <small>ZIP</small>
        </th>
        <th>
            <small>PHONE</small>
        </th>
        <th>
            <small>EMAIL</small>
        </th>
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
            @foreach($entities as $data)
                    <tr @if($data->active == 0) class='inactiveEntity' style="display:none;" @endif>
                        
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->entity_name}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> @if($data->user->active == 0)<span class="red-text attention" uk-tooltip="This owner cannot login because they are not active."> [ @endIf {{$data->user->name}} @if($data->user->active == 0) ] </span> @endIf </a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->address1." ".$data->address2}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->city}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->state->state_name}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->zip}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->phone}}</a></small></td>
                        <td><small><a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"> {{$data->email_address}}</a></small></td>
                        <td>
                            
                            
                                <a onclick="dynamicModalLoad('admin/entity/create/{{$data->id}}')" class="uk-link-muted"><i class="uk-icon-edit uk-margin-right"></i></a>
                                <a @if($data->active == 1) style="display:none;" @endif class='activateEntity uk-link-muted' href="modals/admin/activate/entity/{{$data->id }}" title="Click to activate entity" >
                                     <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                                    </i>
                                </a>
                                <a @if($data->active == 0) style="display:none;" @endif class='deactivateEntity uk-link-muted' href="modals/admin/deactivate/entity/{{$data->id }}" title="Click to deactivate entity"  data-active="1"><i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                            </a>
                        </td>
                    </tr>
                @endforeach

        </tbody>
    </table>

</div>
<script>
    $(".activateEntity,.deactivateEntity").on('click', function(e){
        e.preventDefault();
        var el = $(this);
        
        if(el.data('active') == 1){
            UIkit.modal.confirm("<h2>Are you sure!?</h2> <p>Deactivating an entity will deactivate all of its associated programs, accounts, and users as well.</p>").then(function() {
                $.get(el.attr('href'), function(data){
                        UIkit.modal.alert(''+data+'');
                        $('#entities-tab').trigger('click');
                });
            });
        } else {
            $.get(el.attr('href'), function(data){
                        UIkit.modal.alert(''+data+'');
                        $('#entities-tab').trigger('click');
                });
        }
    });


</script>