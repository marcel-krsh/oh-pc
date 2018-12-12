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
                
            </tr>
        @endforeach
        </tbody>
    </table>
</div>