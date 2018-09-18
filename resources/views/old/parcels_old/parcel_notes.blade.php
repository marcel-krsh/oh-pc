<div id="detail-subtabs-content">
    <script>
        // disable infinite scroll:
        window.getContentForListId = 0;
    </script>

    <!-- Begin Tools and Filters --> 
    <div class="uk-container uk-margin-top no-print">
        <div uk-grid class="uk-grid-collapse uk-margin-top ">
            <div class="uk-width-1-1 uk-margin-remove"></div>
            <div class="uk-width-1-1 uk-margin-small-top uk-button-group" id="message-filters" data-uk-button-radio="">
                <a class="uk-button uk-button-default filter_link" data-filter="all">
                    ALL
                </a>
                <div class="uk-button uk-button-default uk-width-1-4 ">
                    <input id="notes-search" name="notes-search" type="text" value="{{ Session::get('notes-search') }}" class="uk-width-1-1" placeholder="Search Within Notes (press enter)">      
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
    <!-- start comm list -->

    <div class="uk-container uk-margin-top uk-grid-collapse" id="note-list" uk-grid style="position: relative; ">
        <!-- note list is loaded in place using loadCommuincations() -> printnotesList(); using the json input from notes/file-id.json where file-id is the file-id of the current file. -->
        @foreach ($notes as $note)
        <div class="filter_element uk-width-1-1 note-list-item staff-{{ $note->owner->id}} attachment-{{$attachment}}" id="note-{{ $note->id}}" data-grid-prepared="true" style="position: relative; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
            <div uk-grid>
                <div class="uk-width-1-6 uk-width-1-3@m note-type-and-who ">
                    <span uk-tooltip="pos:top-left;title:{{ $note->owner->name}}" class="no-print">
                        <div class="user-badge user-badge-note-item user-badge-{{ $note->owner->badge_color}} no-float">{{ $note->initials}}</div>
                    </span>
                    <span class="print-only">{{ $note->owner->name}}<br></span>
                    <span class=" note-item-date-time">{{ date('F d, Y', strtotime($note->created_at)) }}  <br class="print-only">{{ date('h:i', strtotime($note->created_at)) }}</span>
                </div>
                <div class="uk-width-2-3 uk-width-1-2@m note-item-excerpt">
                     {{ $note->note}}
                </div>
                <div class="uk-width-1-6">
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div id="detail-tab-bottom-bar" class="uk-vertical-align no-print">
        <table class="action-bar">
            <tbody>
                <tr>
                    <td width="3.5%"></td>
                    <td width="18.6%"></td>
                    <td width="18.6%"></td>
                    <td width="18.6%">
                        <a class="uk-button uk-button-primary blue-button uk-width-1-1  uk-padding-remove" onclick="dynamicModalLoad('new-note-entry/{{$parcel->id}}')">
                            <span uk-icon="copy"></span> <span class="uk-text-small">NEW NOTE</span>
                        </a>
                    </td>
                    <td width="18.6%">
                        <a class="uk-button uk-button-primary blue-button uk-width-1-1 uk-padding-remove" onclick="openWindow('print-notes')">
                            <span uk-icon="print"></span> <span class="uk-text-small">PRINT NOTES</span>
                        </a>            
                    </td>
                    <td width="3.5%"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- end comm list -->
    <script>
    loadNotes(window.currentDetailId);

    // process search
    $(document).ready(function() {
        $('#notes-search').keydown(function (e) {
          if (e.keyCode == 13) {
            $.post('{{ URL::route("notes.search", $parcel->id) }}', {
                'notes-search' : $("#notes-search").val(),
                '_token' : '{{ csrf_token() }}'
                }, function(data) {
                    if(data!='1'){ 
                        UIkit.modal.alert(data);
                    } else {
                        loadParcelSubTab('notes',{{$parcel->id}});                                                                           
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

