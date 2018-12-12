<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($huds)}} TOTAL HUD INSPECTABLE AREAS</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>HUD AREA NAME</small>
        </th>
        </thead>
        <tbody>
            @foreach($huds as $data)
                    <tr>
                        
                        <td><small>{{$data->name}}</small></td>
                    </tr>
                @endforeach

        </tbody>
    </table>

</div>