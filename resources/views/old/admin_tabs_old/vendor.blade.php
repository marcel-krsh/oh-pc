<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($vendors)}} TOTAL VENDORS <small>

            <a onclick="$('.inactiveVendor').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveVendor">SHOW</span>
                <span class="inactiveVendor" style="display:none;">HIDE</span> DISABLED VENDORS
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
            <small>EMAIL</small>
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
            <small>NOTES</small>
        </th>
        <th style="min-width: 60px;">
            <small>ACTIONS</small>
        </th>
        </thead>
        <tbody>
        @foreach($vendors as $data)
            <tr @if($data->active == 0) class='inactiveVendor' style="display:none;" @endif>
                <td><small>{{$data->created_at ?? 'NA'}}</small></td>
                <td><small><a onclick="dynamicModalLoad('expense-categories-vendor-details/{{$data->id}}/')" class="uk-link-muted">{{$data->vendor_name ?? 'NA'}}</a></small></td>
                <td><small>{{$data->vendor_email ?? 'NA'}}</small></td>
                <td><small>{{$data->vendor_city ?? 'NA'}}</small></td>
                <td><small>{{$data->state->state_name ?? 'NA'}}</small></td>
                <td><small>{{$data->vendor_zip ?? 'NA'}}</small></td>
                <td><small>{{$data->vendor_notes}}</small></td>
                <td>
                    <a title="Click to view vendor's stats" class="uk-hidden" onclick="">
                        <i class="a-info-circle" >
                        </i>
                    </a>
                    <a title="Click to edit vendor"  onclick="dynamicModalLoad('admin/vendor/create/{{$data->id}}')">
                        <i class="a-pencil-2">
                        </i>
                    </a>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateVendor uk-link-muted' href="modals/admin/activate/vendor/{{$data->id }}" title="Click to activate vendor" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateVendor uk-link-muted' href="modals/admin/deactivate/vendor/{{$data->id }}" title="Click to deactivate vendor">
                        <i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>
<script>
    $(".activateVendor,.deactivateVendor").on('click', function(e){
        e.preventDefault();
        e.target.blur();
        var el = $(this);
        console.log(el.attr('href'));
        $.get(el.attr('href'), function(data){
            console.log(data);
            UIkit.modal.alert('<h2>'+data+'</h2>');
            $('#vendors-tab').click();
        });
    });
</script>