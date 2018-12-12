<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($documents)}} TOTAL DOCUMENT CATEGORIES 
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
        @foreach($documents as $data)
            <tr>
                <td>
                    @if($data->created_at)
                        {{$data->created_at}}
                    @else
                        NA
                    @endif
                </td>
                <td><small><a onclick="dynamicModalLoad('admin/document_category/create/{{$data->id}}')" class="uk-link-muted"> {{$data->document_category_name}}</a></small></td>
                <td>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateDocument uk-link-muted' href="modals/admin/activate/document/{{$data->id }}" title="Click to activate document category" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateDocument uk-link-muted' href="modals/admin/deactivate/document/{{$data->id }}" title="Click to deactivate document category">
                        <i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>