<div id="reports_tab">
    <div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">
        
                <div uk-grid class="uk-grid-collapse uk-visible@m">
                	@if(Auth::user()->isOhfa())
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="all">
                        ALL
                    </a>
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="draft">
                        DRAFT
                    </a>
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="approved-with-changes">
                        APPROVED WITH CHANGES
                    </a>
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="declined-with-changes">
                        DECLINED WITH CHANGES
                    </a>	
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="approved-ready-to-send">
                        APPROVED
                    </a>
                    <a class="uk-button uk-button-default filter_link uk-margin-right" data-filter="sent">
                        SENT
                    </a>
                    @else
                    @endIf
                    
                </div>
                <div class="uk-width-1-1@s uk-hidden@m">
                    @if(Auth::user()->isOhfa())
                    <a class="uk-button uk-button-default filter_link " data-filter="all">
                        ALL
                    </a>
                    <a class="uk-button uk-button-default filter_link " data-filter="drafts">
                        DRAFTS
                    </a>
                    <a class="uk-button uk-button-default filter_link " data-filter="approved-with-changes">
                        APPROVED WITH CHANGES
                    </a>
                    <a class="uk-button uk-button-default filter_link " data-filter="declined-with-changes">
                        DECLINED WITH CHANGES
                    </a>	
                    <a class="uk-button uk-button-default filter_link " data-filter="approved-ready-to-send">
                        APPROVED READY TO SEND
                    </a>
                    <a class="uk-button uk-button-default filter_link " data-filter="sent">
                        SENT
                    </a>
                    @else
                    @endIf
                </div>
                
                @if(Auth::user()->isOhfa())
                <div class="uk-width-1-1@s uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
                    <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="filterByProject();">
                        <option value="all" selected="">
                            FILTER BY PROJECT 
                        </option>
                        @if(!is_nulll($projects_array))
	                        @foreach ($projects_array as $owner)
	                        <option value="staff-{{$owner['id']}}"><a class="uk-dropdown-close">{{$project['name']}}</a></option>    
	                        @endforeach
	                    @endIf
                    </select>
                    
                </div>
                <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align: top;">
                    <select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="filterByLead();">
                        <option value="all" selected="">
                            FILTER BY LEAD 
                            </option>
                            @if(!is_nulll($hfa_users_array))
	                            @foreach ($hfa_users_array as $user)
	                            <option value="user-{{$user->id}}"><a  class="uk-dropdown-close">{{$user->person->first_name}} {{$user->person->last_name}}</a></option>    
	                            @endforeach 
	                        @endIf      
                        </select>
                </div>
                @endif
                <div class="uk-width-1-1@s uk-width-1-5@m" style="vertical-align:top">
                    <a class="uk-button uk-button-success green-button uk-width-1-1" onclick="dynamicModalLoad('new-report/')">
                        <span class="a-file-plus"></span> 
                        <span>NEW REPORT</span>
                    </a>    
                </div> 
    </div>
    @if(count((array)$reports))
    <div uk-grid class="uk-margin-top uk-visible@m">
        <div class="uk-width-1-1">
            <div uk-grid>
                <div class=" uk-width-1-5@m uk-width-1-1@s">
                    <div class="uk-margin-small-left"><small><strong>PROJECT</strong></small></div>
                </div>
                <div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
                    <div class="uk-margin-right"><small><strong>DATE</strong></small></div>
                </div>
                <div class="uk-width-1-5@m uk-width-1-1@s">
                    <div class="uk-margin-small-left"><small><strong>AUDIT</strong></small></div>
                </div>
                <div class="uk-width-2-5@m uk-width-1-1@s">
                    <div class="uk-margin-small-left"><small><strong>SUMMARY</strong></small></div>
                </div>
                <div class="uk-width-1-5@m uk-width-1-1@s uk-text-right">
                    <div class="uk-margin-right"><small><strong>ACTION</strong></small></div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div uk-grid class="uk-margin-top uk-visible@m">
        <div class="uk-width-1-1">
            <div uk-grid>
                <div class=" uk-width-1-1">
                	SORRY - NO REPORTS - PLEASE TRY APPLYING A FILTER
                </div>
            </div>
        </div>
    </div>

    @endif
</div>
<?php // keep this script at the bottom of page to ensure the tabs behave appropriately ?>
<script>
	window.reportsLoaded = 1;
</script>
<?php // end script keep ?>