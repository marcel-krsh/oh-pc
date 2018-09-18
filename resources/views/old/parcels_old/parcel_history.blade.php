<div id="detail-subtabs-content">
    <script>
        // disable infinite scroll:
        window.getContentForListId = 0;
    </script>

    <!-- Begin history --> 
    <style>
        .black-button{
            background-color:black;
            
        }
        .user-badge-black {
            background-color: #000000 !important;
        }
        .user-badge-activity-item {
            width: 20px;
            height: 20px;
            font-size: 11px;
            line-height: 21px;
            display:inline-block;
        }
        .activity-list-item {
            padding-top: 15px;
            padding-bottom: 15px;
            border: 1px solid #eaeaea;
            padding-left: 10px;
            padding-right: 10px;
            border-bottom: none;
            -webkit-transition: all 350ms ease-in-out;
            -moz-transition: all 350ms ease-in-out;
            -ms-transition: all 350ms ease-in-out;
            -o-transition: all 350ms ease-in-out;
            transition: all 350ms ease-in-out;
        }
    </style>

<!-- begin output templates -->
<template id="filter-by-staff-template" class="uk-hidden">
    <a class="uk-button uk-button-default no-text-shadow user-badge-!!badgeColor!! uk-dark uk-light" uk-tooltip="pos:top-left;title:!!staffName!!" data-uk-filter="staff-!!staffId!!">
        !!staffInitials!!
    </a>
</template>
<!-- END TEMPLATTES -->

<!-- Begin Tools and Filters --> 
    <div class="uk-container uk-margin-top no-print">
        <div uk-grid class="uk-grid-collapse uk-margin-top ">
            <div class="uk-width-1-1 uk-margin-remove"></div>
            <div class="uk-width-1-1 uk-margin-small-top uk-button-group" id="message-filters" data-uk-button-radio="">
                <a class="uk-button uk-button-default filter_link" data-filter="all">
                    ALL
                </a>
                <div class="uk-button uk-button-default uk-width-1-4 ">
                    <input id="activities-search" name="activities-search" type="text" value="{{ Session::get('activities-search') }}" class="uk-width-1-1" placeholder="Search Within activities (press enter)">      
                </div>

                <!-- display current info - read only -->
                @foreach ($owners_array as $owner)
                
                <a class="uk-button uk-button-default no-text-shadow user-badge-{{$owner['color']}} uk-dark uk-light filter_link" uk-tooltip="pos:top-left;title:{{$owner['name']}}" data-filter="staff-{{$owner['id']}}">
                    {{$owner['initials']}}
                </a>
                @endforeach
            </div>
            <!-- End Tool and Filters -->
        </div>
    </div>

    <div class="uk-container uk-margin-top uk-grid-collapse" id="activity-list" uk-grid style="position: relative; ">
        @foreach ($activities as $activity)
        <div class="filter_element uk-width-1-1 activity-list-item staff-{{ $activity->user_id}}" id="activity-{{ $activity->id}}" data-grid-prepared="true" style="position: relative; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
            <div uk-grid>
                <div class="uk-width-1-6 uk-width-1-3@m activity-type-and-who ">
                    <span uk-tooltip="pos:top-left;title:{{ $activity->name}}" class="no-print">
                        <div class="user-badge user-badge-activity-item user-badge-{{ $activity->badge_color}}">{{ $activity->initials}}</div>
                    </span>
                    <span class="print-only">{{ $activity->name}}<br></span>
                    <span class=" activity-item-date-time">{{ date('F d, Y', strtotime($activity->date)) }}  <br class="print-only">{{ date('h:i', strtotime($activity->date)) }}</span>
                </div>
                <div class="uk-width-5-6 uk-width-2-3@m activity-item-excerpt">
                    {{ $activity->description}}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <script>
    //loadActivities(window.currentDetailId);

    // process search
    $(document).ready(function() {
        $('#activities-search').keydown(function (e) {
          if (e.keyCode == 13) {
            $.post('{{ URL::route("activities.search", $parcel->id) }}', {
                'activities-search' : $("#activities-search").val(),
                '_token' : '{{ csrf_token() }}'
                }, function(data) {
                    if(data!='1'){ 
                        UIkit.modal.alert(data);
                    } else {
                        loadParcelSubTab('history',{{$parcel->id}});                                                                           
                    }
            } );
            e.preventDefault();
            return false; 
          }
        });

        var $filteredElements = $('.filter_element');
           $('.filter_link').click(function (e) {
            e.preventDefault();
            // get the category from the attribute
            var filterVal = $(this).data('filter');
            filterElement(filterVal, '.filter_element');

            // reset dropdowns
            $('#filter-by-owner').prop('selectedIndex',0);
            @if(Auth::user()->isFromEntity(1))
            $('#filter-by-program').prop('selectedIndex',0);
            @endif
           });
    });

    function filterElement(filterVal, filter_element){
        if (filterVal === 'all') {
         $(filter_element).show();
        }
        else {
         // hide all then filter the ones to show
         $(filter_element).hide().filter('.' + filterVal).show();
        }
        UIkit.update(event = 'update');
    }

    </script>


</div>

