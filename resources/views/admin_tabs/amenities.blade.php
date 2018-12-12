<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($amenities)}} TOTAL AMENITIES</h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        
        <th>
            <small>AMENITY NAME</small>
        </th>
        </thead>
        <tbody>
            @foreach($amenities as $data)
                    <tr>
                        
                        <td><small>{{$data->amenity_description}}</small></td>
                    </tr>
                @endforeach

        </tbody>
    </table>

</div>