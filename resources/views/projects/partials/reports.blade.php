<div id="detail-project-reports" uk-grid>
    <div class="uk-width-1-1" id="note-filters">
                <!-- Begin Tools and Filters --> 
        
        <div uk-grid class="uk-margin-top">                        

            <div class="uk-width-1-1@s uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
                    <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_status_id='+this.value, '3','','','',1);">
                        <option value="all">
                            FILTER BY STATUS
                        </option>
                        <option value="all" @if(session('crr_report_status_id') == 'all')  @endIf>
                            ALL REPORT STATUSES
                        </option>
                        @if(!is_null($crrApprovalTypes))
                            @foreach ($crrApprovalTypes as $status)
                            <option value="{{$status->id}}" @if(session('crr_report_status_id') == $status->id) <?php $crrStatusSelection = $status->name;?> @endIf><a class="uk-dropdown-close">{{$status->name}}</a></option>
                            @endforeach
                        @endIf
                    </select>      
            </div>
            
            @can('access_auditor')
                <div class="uk-width-1-5" style="vertical-align: top;">
                    <select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_lead_id='+this.value, '3','','','',1);">
                        <option value="all" selected="">
                            FILTER BY LEAD
                            </option>
                            <option value="all" @if(session('crr_report_lead_id') == 'all')  @endIf>
                            ALL LEADS
                        </option>
                            @if(!is_null($hfa_users_array))
	                            @foreach ($hfa_users_array as $user)
	                            <option value="{{$user->id}}">@if(session('crr_report_lead_id') == $user->id)<?php $crrLeadSelection = $user->person->first_name . ' ' . $user->person->last_name;?>  @endIf<a  class="uk-dropdown-close">{{$user->person->first_name}} {{$user->person->last_name}}</a></option>
	                            @endforeach
	                        @endIf
                        </select>
                </div>
                <div class="uk-width-1-5" style="vertical-align: top;">
                    <select id="filter-by-type" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_type='+encodeURIComponent(this.value), '3','','','',1);">
                        <option value="all" selected="">
                            FILTER BY TYPE
                            </option>
                            <option value="all" @if(session('crr_report_type') == 'all')  @endIf>
                            ALL TYPES
                        </option>
                            @if(!is_null($crr_types_array))
                                @foreach ($crr_types_array as $type)
                                <option value="{{$type->id}}">@if(session('crr_report_type') == $type->id)<?php $crrTypeSelection = $type->template_name;?>  @endIf<a  class="uk-dropdown-close">{{$type->template_name}}</a></option>
                                @endforeach
                            @endIf
                        </select>
                </div>

                <div class="uk-width-1-5" >
                    <input id="reports-search" name="reports-search" type="text" value="" class=" uk-input" placeholder="REPORT #">

                </div>

            @endCan

        </div>

    </div>        
</div>
<hr class="dashed-hr">

<div uk-grid class="uk-margin-top ">

        <div class="uk-width-1-1">
            <div class="uk-align-right uk-label  uk-margin-top uk-margin-right">{{$reports->total()}} @if($reports->total() == 1) REPORT @else REPORTS @endif</div>                        
        </div>
<hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-grid-margin uk-first-column">

@if(count($reports))
    <style type="text/css">
        .calendar-button {
                position: relative;
                top: 4px;
                font-size: 16px;
        }
    </style>
    <div class="uk-width-1-1 uk-grid-margin uk-first-column">
            <table class="uk-table " id="crr-report-list">
                <thead>


                    <th ><strong>REPORT</strong></th>
                    <th ><strong>PROJECT</strong></th>

                    <th  width="100px"><strong>AUDIT</strong></th>

                    @can('access_auditor') <th ><strong>LEAD</strong></th>

                    @endCan

                    <th ><strong>TYPE</strong></th>

                    <th width="120px"><strong>STATUS</strong></th>

                    @can('access_auditor')<th  width="80px"><strong>ACTION</strong></th>@endCan

                    <th width="120px"><strong>CREATED</strong></th>

                    <th width="120px"><strong>LAST EDITED</strong></th>

                    <th width="120px"><strong>DUE DATE</strong></th>
                    @can('access_auditor')
                        <th width="40px" uk-tooltip title="Report History"><i class="a-person-clock"></i></th>
                    @endCan

            </thead>

            @include('projects.partials.reports-row')

        </table>
@else
    <div class="uk-width-1-1">
        <div uk-grid>
            <div class="uk-width-1-3"></div>
            <div class=" uk-width-1-3 uk-first-row">
                <article class="uk-comment">
                    <header class="uk-comment-header uk-grid-medium uk-flex-middle" uk-grid>
                        <div class="uk-width-auto">
                            <h1><i class="a-file-fail"></i></h1>
                        </div>
                        <div class="uk-width-expand">
                            <h4 class="uk-comment-title uk-margin-remove"><a class="uk-link-reset" href="#">SORRY!</a></h4>
                            <ul class="uk-comment-meta uk-subnav uk-subnav-divider uk-margin-remove-top">
                                <li><a href="#">No Reports Found</a></li>
                            </ul>
                        </div>
                    </header>
                    <div class="uk-comment-body">

                    </div>
                </article>

            </div>
        </div>
    </div>
@endif
</div>