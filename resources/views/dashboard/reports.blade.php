<?php 
    if(!isset($projects_array)){ $projects_array = null;}
    if(!isset($hfa_users_array)){ $hfa_users_array = null;} 
    $crrStatusSelection = 'all';
    $crrProjectSelection = 'all';
    $crrLeadSelection = 'all';
    ?>

<div id="reports_tab">
    <div uk-grid class="uk-margin-top" id="message-filters" data-uk-button-radio="">

        
                <div uk-grid class="uk-width-1-4">
                	@can('access_auditor')

                    <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_status_id='+this.value, '3','','','',1);">
                        <option value="all">
                            FILTER BY STATUS 
                        </option>
                        <option value="all" @if(session('crr_report_status_id') == 'all')  @endIf>
                            ALL REPORT STATUSES
                        </option>
                        @if(!is_null($crrApprovalTypes))
                            @foreach ($crrApprovalTypes as $status)
                            <option value="{{$status->id}}" @if(session('crr_report_status_id') == $status->id) <?php $crrStatusSelection = $status->name; ?> @endIf><a class="uk-dropdown-close">{{$status->name}}</a></option>    
                            @endforeach
                        @endIf
                    </select>

                    
                    
                    @endCan
                    
                </div>
                
                
                @can('access_auditor')
                <div class="uk-width-1-4" id="recipient-dropdown" style="vertical-align: top;">
                    <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_project_id='+this.value, '3','','','',1);">
                        <option value="all" selected="">
                            FILTER BY PROJECT 
                        </option>
                        <option value="all" @if(session('crr_report_project_id') == 'all')  @endIf>
                            ALL PROJECTS
                        </option>
                        @if(!is_null($projects_array))
                            <!-- crr_report_project_id -->
	                        @foreach ($projects_array as $project)
	                        <option value="{{$project->id}}" @if(session('crr_report_project_id') == $project->id) <?php $crrProjectSelection = $project->project_number.' : '.$project->project_name; ?>  @endIf><a class="uk-dropdown-close">{{$project->project_number}} : {{$project->project_name}}</a></option>    
	                        @endforeach
	                    @endIf
                    </select>
                    
                </div>
                <div class="uk-width-1-4" style="vertical-align: top;">
                    <select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/dashboard/reports?crr_report_lead_id='+this.value, '3','','','',1);">
                        <option value="all" selected="">
                            FILTER BY LEAD 
                            </option>
                            <option value="all" @if(session('crr_report_lead_id') == 'all')  @endIf>
                            ALL LEADS
                        </option>
                            @if(!is_null($hfa_users_array))
	                            @foreach ($hfa_users_array as $user)
	                            <option value="{{$user->id}}">@if(session('crr_report_lead_id') == $user->id)<?php $crrLeadSelection = $user->person->first_name.' '.$user->person->last_name; ?>  @endIf<a  class="uk-dropdown-close">{{$user->person->first_name}} {{$user->person->last_name}}</a></option>    
	                            @endforeach 
	                        @endIf      
                        </select>
                </div>
                
                <div class="uk-width-1-4" >
                    <input id="reports-search" name="reports-search" type="text" value="" class=" uk-input" placeholder="Search report content (press enter)">
                        
                </div>
                
                @endCan
    </div>
    <hr class="dashed-hr">
    <div uk-grid class="uk-margin-top ">

        <div class="uk-width-1-1">
            <div class="uk-align-right uk-label  uk-margin-top uk-margin-right">{{$reports->total()}} @if($reports->total() == 1) REPORT @else REPORTS @endif</div>
            <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter" style="background-color:#d8eefa"><a class=" " onclick="dynamicModalLoad('new-report/')">
                        <span class="a-file-plus"></span> 
                        <span>NEW REPORT</span>
                    </a>
                </div>
            @if(session('crr_search') && session('crr_search') !== '%%clear-search%%')

            <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                <a onClick="loadTab('/dashboard/reports?search=%%clear-search%%', '3','','','',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>SEARCH " {{ session('crr_search') }} "</span></a>
                
            </div>
            @endIf
            @if(session('crr_report_status_id') && session('crr_report_status_id') !== 'all')

            <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                <a onClick="loadTab('/dashboard/reports?crr_report_status_id=all', '3','','','',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ strtoupper($crrStatusSelection) }}</span></a>
                
            </div>
            @endIf
            @if(session('crr_report_project_id') && session('crr_report_project_id') !== 'all')

            <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                <a onClick="loadTab('/dashboard/reports?crr_report_project_id=all', '3','','','',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ $crrProjectSelection }}</span></a>
                
            </div>
            @endIf

            @if(session('crr_report_lead_id') && session('crr_report_lead_id') !== 'all')

            <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                <a onClick="loadTab('/dashboard/reports?crr_report_lead_id=all', '3','','','',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ $crrLeadSelection }}</span></a>
                
            </div>
            @endIf
        </div>
        <hr class="dashed-hr uk-width-1-1 uk-margin-bottom">
    @if(count($reports))
        <style type="text/css">
            .calendar-button {
                    position: relative;
                    top: 4px;
                    font-size: 16px;
            }
        </style>
        <div class="uk-width-1-1">
            <table class="uk-table uk-table-striped">
                <thead>

                    
                    <th ><strong>REPORT</strong></th>
                    <th ><strong>PROJECT</strong></th>
                
                    <th  width="100px"><strong>AUDIT</strong></th>
                
                    <th ><strong>LEAD</strong></th>
                
                    <th ><strong>TYPE</strong></th>

                    <th width="120px"><strong>STATUS</strong></th>
                
                    <th  width="80px"><strong>ACTION</strong></th>

                    <th width="120px"><strong>CREATED</strong></th>

                    <th width="120px"><strong>LAST EDITED</strong></th>
                
                    <th width="120px"><strong>DUE DATE</strong></th>
                    @can('access_auditor')
                        <th width="40px" uk-tooltip title="Report History"><i class="a-person-clock"></i></th>
                    @endCan
               
            </thead>
            @forEach($reports as $report)
                <tr>
                    
                    <td><a href="/report/{{$report->id}}" target="report-{{$report->id}}" class="uk-mute"><i class="a-file-chart-3"></i> #{{$report->id}}</a></td>
                    <td><a onclick="loadTab('/projects/{{$report->project->project_key}}', '4', 1, 1,'',1);" class="uk-mute"> {{$report->project->project_number}} : {{$report->project->project_name}}</a></td>
                    <td>{{$report->audit_id}}</td>
                    <td>{{$report->lead->person->first_name}} {{$report->lead->person->last_name}}</td>
                    <td>{{$report->template()->template_name}}</td>
                    <td>{{$report->crr_approval_type->name}}</td>
                    <td>
                       <?php
                            //ACTION OPTIONS BASED ON STATUS AND USER ROLE
                        ?>
                        @can('access_auditor')
                            @if($report->crr_approval_type_id == 1)
                                <a class="uk-muted uk-button uk-button-success uk-button-small" onClick="loadTab('/dashboard/reports?action=2', '3','','','',1);">SEND TO MANAGER</a>
                            @elseIf($report->crr_approval_type_id == 2)
                                @can('access_manager')
                                <select onchange="loadTab('/dashboard/reports?action='+this.value, '3','','','',1);">
                                    <option >APPROVE?</option>
                                    <option value="3">DECLINE</option>
                                    <option value="4">APPROVE WITH CHANGES</option>
                                    <option value="5">APPROVED</option>
                                </select>
                                @else
                                    <a class="uk-muted" onClick="loadTab('/dashboard/reports?action=2', '3','','','',1);">RESEND TO MANAGER</a>
                                @endCan
                            @endIf

                        @endCan

                    </td>
                    <td>{{ date('M d, Y',strtotime($report->created_at)) }}</td>
                    <td>{{ ucfirst($report->updated_at->diffForHumans()) }}</td>
                    <td>@if(!is_null($report->response_due_date))  @if(strtotime($report->response_due_date) < time()) <span class="attention" style="color:darkred"> <i class="a-warning"></i> @endIf {{date('M d, Y',strtotime($report->response_due_date)) }} @if(strtotime($report->response_due_date) < time()) </span> @endIf<a class=" flatpickr selectday{{$report->id}} flatpickr-input "><input type="text" placeholder="Edit Due Date.." data-input="" style="display:none" ><i class="a-pencil " ></i></a>@else <a class="uk-button uk-button-small uk-button-success flatpickr selectday{{$report->id}} flatpickr-input"><input type="text" placeholder="Select Due Date.." data-input="" style="display:none" ><i class="a-calendar-pencil calendar-button " ></i></a> @endIf
                         <script>
                            flatpickr(".selectday{{$report->id}}", {
                                weekNumbers: true,
                                defaultDate:"today",
                                altFormat: "F j, Y",
                                dateFormat: "Ymd",
                                "locale": {
                                    "firstDayOfWeek": 1 // start week on Monday
                                    }
                            });
                            $('.flatpickr.selectday{{$report->id}}').change(function(){
                                console.log('New Due Date for report {{$report->id}} of '+this.value);
                                loadTab('/dashboard/reports?report_id={{$report->id}}&due='+encodeURIComponent(this.value), '3','','','',1);
                            });
                    </script>
                    </td>
                    @can('access_auditor')
                    <td><i @if($report->report_history) class="a-person-clock uk-link"  uk-toggle="target: #report-{{$report->id}}-history;" @else class="a-clock-not" @endIf></i></td>
                    @endCan
                </tr>
                @can('access_auditor')
                @if($report->report_history)

                <tr id="report-{{$report->id}}-history" hidden>
                    <td  bgcolor="#3c3c3c"></td>
                    
                    
                        <td colspan="10">    
                   
                        <table class="uk-table uk-table-striped">
                            
                            <thead>
                                <th width="80px">DATE</th>
                                <th width="120px">USER</th>
                                <th>NOTE</th>
                            </thead>
                               
                                
                            @forEach($report->report_history as $h)
                                <tr>
                                    <td>{{$h['date']}}</td>
                                    <td>{{$h['user_name']}} @can('access_admin')<i class="a-info-circle uk-link"  onClick="openUserPreferences({{$h['user_id']}});"></i>@endCan</td>
                                    <td>{{$h['note']}}</td>
                                </tr>
                            @endForEach
                            @can('access_admin')
                                <tr >
                                    <td height="40px" valign="middle" colspan="3" bgcolor="#8a8998"><span class="uk-contrast"> ADMINS: Information presented was current at time of recording the record. Click the <i class="a-info-circle"></i> icon to view a user's current information.</span></td>
                                </tr>
                            @endCan
                        </table>
                    </td>

                </tr>
                @endIf
                @endCan
            @endForEach
        </table>
        {{$reports->links()}}
        <script>

    $(document).ready(function(){
   // your on click function here
       $('.page-link').click(function(){
               $('#detail-tab-3-content').load($(this).attr('href'));
               window.current_finding_type_page = $('#detail-tab-3-content').load($(this).attr('href'));
               return false;
           });
        });
    
        function searchReports(){
            $.get('/dashboard/reports?search=' + encodeURIComponent($("#reports-search").val()), function(data) {
                    
                        $('#detail-tab-3-content').load('/dashboard/reports');
                        
                    
            } );
        }

        // process search
        $(document).ready(function() {
            $('#reports-search').keydown(function (e) {
              if (e.keyCode == 13) {
                searchReports();
                e.preventDefault();
                return false; 
              }
            });
        });

        @if(count($messages))
            @forEach($messages as $message)
                UIkit.notification({
                    message: '{{$message}}',
                    status: 'success',
                    pos: 'top-right',
                    timeout: 3000
                });
            @endForEach
        @endif

        </script>
        </div>
  
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
</div>
<?php // keep this script at the bottom of page to ensure the tabs behave appropriately ?>
<script>
	window.reportsLoaded = 1;
</script>
<?php // end script keep ?>