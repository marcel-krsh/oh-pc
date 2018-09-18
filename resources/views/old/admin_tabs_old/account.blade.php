<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($accounts)}} TOTAL ACCOUNTS <small>

            <a onclick="$('.inactiveAccount').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveAccount">SHOW</span>
                <span class="inactiveAccount" style="display:none;">HIDE</span> DISABLED ACCOUNTS
            </a>
        </small>
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        <th>
            <small>DATE</small>
        </th>
        <th>
            <small>NAME</small>
        </th>
        <th>
            <small>PROGRAM</small>
        </th>
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($accounts as $data)
            <tr @if($data->active == 0) class='inactiveAccount' style="display:none;" @endif>
                <td>
                    @if($data->created_at)
                        {{$data->created_at}}
                    @else
                        NA
                    @endif
                </td>
                <td><small><a onclick="dynamicModalLoad('admin/account/create/{{$data->id}}')" class="uk-link-muted"> {{$data->account_name}}</a></small></td>
                <td><small><a onclick="dynamicModalLoad('admin/account/create/{{$data->id}}')" class="uk-link-muted"> {{$data->program->program_name}}</a></small></td>
                <td>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateAccount uk-link-muted' href="modals/admin/activate/account/{{$data->id }}" title="Click to activate account" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateAccount uk-link-muted' href="modals/admin/deactivate/account/{{$data->id }}" title="Click to deactivate account">
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
    $(".activateAccount,.deactivateAccount").on('click', function(e){
        e.preventDefault();
        var el = $(this);
        $.get(el.attr('href'), function(data){
            UIkit.modal.alert('<h2>'+data+'</h2>');
            $('#accounts-tab-content').load('/tabs/account');
        });
    });
</script>