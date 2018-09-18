<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($targets)}} TOTAL TARGET AREAS <small>

            <a onclick="$('.inactiveTarget').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveTarget">SHOW</span>
                <span class="inactiveTarget" style="display:none;">HIDE</span> DISABLED TARGET AREAS
            </a>
        </small>
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        <th>
            <small>COUNTY</small>
        </th>
        <th>
            <small>NAME</small>
        </th>
        
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($targets as $data)
            <tr @if($data->active == 0) class='inactiveTarget' style="display:none;" @endif>
                <td><small><a onclick="dynamicModalLoad('admin/target_area/create/{{$data->id}}')" class="uk-link-muted"> {{$data->county->county_name}}</a></small></td>
                <td><small><a onclick="dynamicModalLoad('admin/target_area/create/{{$data->id}}')" class="uk-link-muted"> {{$data->target_area_name}}</a></small></td>
                
                <td>
                    <a onclick="dynamicModalLoad('admin/target_area/create/{{$data->id}}')" class="uk-link-muted"><i  class="a-pencil-2 uk-margin-right"></i></a>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateTarget uk-link-muted' href="modals/admin/activate/target/{{$data->id }}" title="Click to activate target area" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateTarget uk-link-muted' href="modals/admin/deactivate/target/{{$data->id }}" title="Click to deactivate target area">
                        <i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    $(".activateTarget,.deactivateTarget").on('click', function(e){
        e.preventDefault();
        var el = $(this);
        $.get(el.attr('href'), function(data){
            UIkit.modal.alert('<h2>'+data+'</h2>');
            $('#target-tab-content').load('/tabs/target_area');
        });
    });
</script>