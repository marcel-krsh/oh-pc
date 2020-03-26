<div id="detail-project-notes" uk-grid>
    <script>
        // disable infinite scroll:
        window.getContentForListId = 0;
    </script>
    <div class="uk-width-1-1" id="note-filters">
	    <!-- Begin Tools and Filters -->

        <div uk-grid class="uk-margin-top">

	        <div class=" uk-width-1-1@s  uk-width-1-5@m">

	            <input id="notes-search" name="notes-search" type="text" value="{{ Session::get('notes-search') }}" class="uk-width-1-1 uk-input" placeholder="Search Within Notes (press enter)">
	        </div>

            <div class="uk-width-1-1@s uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
	            <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterByOwner();">
	                <option value="all" selected="">
	                    FILTER BY AUTHOR
	                </option>
	                @foreach ($owners_array as $owner)
	                <option value="staff-{{$owner['id']}}"><a class="uk-dropdown-close">{{$owner['name']}}</a></option>
	                @endforeach
	            </select>

	        </div>

	        <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align:top">
	            <a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-note-entry/{{$project->id}}')">
	                <span class="a-file-text"></span>
	                <span>NEW NOTE</span>
	            </a>
	        </div>
        </div>
	    <div class="uk-container uk-margin-top-large " id="note-list" uk-grid style="position: relative; margin-top:30px;">
	        <!-- note list is loaded in place using loadCommuincations() -> printnotesList(); using the json input from notes/file-id.json where file-id is the file-id of the current file. -->
	        @foreach ($notes as $note)
	        <div class="filter_element uk-margin-top uk-width-1-1 note-list-item staff-{{ $note->owner->id}}" id="note-{{ $note->id}}" data-grid-prepared="true" style="position: relative; box-sizing: border-box; top: 0px; left: 0px; opacity: 1;">
	            <div uk-grid>
	                <div class="uk-width-1-6 uk-width-1-3@m note-type-and-who ">
	                    <span uk-tooltip="pos:top-left;title:{{ strtoupper($note->owner->full_name())}}" class="no-print">
	                        <div class="user-badge user-badge-note-item user-badge-{{ $note->owner->badge_color}} no-float">{{ $note->initials}}</div>
	                    </span>
	                    <span class="print-only">{{ $note->owner->full_name()}}<br></span>
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
	</div>

    <script>
    //loadNotes(window.currentDetailId);
		window.project_detail_tab_4 = 1;
    // process search
    $(document).ready(function() {
        $('#notes-search').keydown(function (e) {
          if (e.keyCode == 13) {
            $.post('{{ URL::route("notes.search", $project->id) }}', {
                'notes-search' : $("#notes-search").val(),
                '_token' : '{{ csrf_token() }}'
                }, function(data) {
                    if(data!=1){
                        UIkit.modal.alert(data);
                    } else {
                        $('#project-detail-tab-4').trigger("click");
                    }
            } );
            e.preventDefault();
            return false;
          }
        });
    });

    function filterByOwner(){
	    var myGrid = UIkit.grid($('#note-list'), {
	        controls: '#note-filters',
	        animation: false
	    });
	    var textinput = $("#filter-by-owner").val();

	    // filter grid items
	    //myGrid.filter(textinput);
	    filterElement(textinput, '.filter_element');
	}

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

