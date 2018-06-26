<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($expenses)}} TOTAL EXPENSE CATEGORIES <small>

            <a onclick="$('.inactiveExpenses').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveExpenses">SHOW</span>
                <span class="inactiveExpenses" style="display:none;">HIDE</span> DISABLED EXPENSE CATEGORIES
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
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($expenses as $data)
            <tr @if($data->active == 0) class='inactiveExpenses' style="display:none;" @endif>
                <td>
                    @if($data->created_at)
                        {{$data->created_at}}
                    @else
                        NA
                    @endif
                </td>
                <td><small><a onclick="dynamicModalLoad('admin/expense_category/create/{{$data->id}}')" class="uk-link-muted"> {{$data->expense_category_name}}</a></small></td>
                <td>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateExpense uk-link-muted' href="modals/admin/activate/expense/{{$data->id }}" title="Click to activate expense category" >
                       <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateExpense uk-link-muted' href="modals/admin/deactivate/expense/{{$data->id }}" title="Click to deactivate expense category">
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
    $(".activateExpense,.deactivateExpense").on('click', function(e){
        e.preventDefault();
        var el = $(this);
        $.get(el.attr('href'), function(data){
            UIkit.modal.alert('<h2>'+data+'</h2>');
            $('#expense-tab-content').load('/tabs/expense_category');
        });
    });
</script>