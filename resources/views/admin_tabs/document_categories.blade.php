<div class="uk-overflow-container uk-margin-top">
    
    <div class="uk-grid">
        <h4 class="uk-text-left uk-width-4-5">{{count($documents)}} TOTAL DOCUMENT CATEGORIES</h4>
        <span class="uk-width-1-5"><a onclick="dynamicModalLoad('admin/document_category/create')" class="uk-button uk-button-success uk-button-small uk-align-right" style="padding-top:1px;"><i class="a-circle-plus" style="    position: relative;
    top: 1px; margin-right:3px"></i> CREATE A CATEGORY</a></span>
    </div>
 
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
       
        <th>
            <small>CATEGORY NAME</small>
        </th>
         <th>
            <small>DATE</small>
        </th>
        </thead>
        <tbody>
        @foreach($documents as $data)
            <tr>
                
                <td><small><a onclick="dynamicModalLoad('admin/document_category/create/{{$data->id}}')" class="uk-link-muted"> {{$data->document_category_name}}</a></small></td>
                <td>
                    @if($data->created_at)
                        {{date('m/d/Y g:i a',strtotime($data->updated_at))}}
                    @else
                        NA
                    @endif
                </td>
                
            </tr>
        @endforeach
        </tbody>
    </table>
</div>