<?php
if (!isset($hfa_users_array)) {$hfa_users_array = null;}
$crrStatusSelection  = 'all';
$crrProjectSelection = 'all';
$crrLeadSelection    = 'all';
$crrTypeSelection    = 'all';

?>

<div id="detail-project-reports" uk-grid>
    <div class="uk-width-1-1" id="note-filters">
                <!-- Begin Tools and Filters -->

        <div uk-grid class="uk-margin-top">
            <input type="hidden" id="project-crr-newest" name="project-crr-newest">
            <div class="uk-width-1-1@s uk-width-1-4@m" id="recipient-dropdown" style="vertical-align: top;">
                    <select id="filter-by-owner" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/projects/{{$id}}/reports?crr_report_status_id='+this.value, '6','','','project-',1);">
                        <option value="all">
                            FILTER BY STATUS
                        </option>
                        <option value="all" @if(session($prefix.'crr_report_status_id') == 'all')  @endIf>
                            ALL REPORT STATUSES
                        </option>
                        @if(!is_null($crrApprovalTypes))
                            @foreach ($crrApprovalTypes as $status)
                            <option value="{{$status->id}}" @if(session($prefix.'crr_report_status_id') == $status->id) <?php $crrStatusSelection = $status->name;?> @endIf><a class="uk-dropdown-close">{{$status->name}}</a></option>
                            @endforeach
                        @endIf
                    </select>
            </div>

            @can('access_auditor')
                <div class="uk-width-1-5" style="vertical-align: top;">
                    <select id="filter-by-program" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/projects/{{$id}}/reports?crr_report_lead_id='+this.value, '6','','','project-',1);">
                        <option value="all" selected="">
                            FILTER BY LEAD
                            </option>
                            <option value="all" @if(session($prefix.'crr_report_lead_id') == 'all')  @endIf>
                            ALL LEADS
                        </option>
                            @if(!is_null($hfa_users_array))
	                            @foreach ($hfa_users_array as $user)
	                            <option value="{{$user->id}}">@if(session($prefix.'crr_report_lead_id') == $user->id)<?php $crrLeadSelection = $user->name;?>  @endIf<a  class="uk-dropdown-close">{{$user->name}}</a></option>
	                            @endforeach
	                        @endIf
                        </select>
                </div>
                <div class="uk-width-1-5" style="vertical-align: top;">
                    <select id="filter-by-type" class="uk-select filter-drops uk-width-1-1" onchange="loadTab('/projects/{{$id}}/reports?crr_report_type='+encodeURIComponent(this.value), '6','','','project-',1);">
                        <option value="all" selected="">
                            FILTER BY TYPE
                            </option>
                            <option value="all" @if($prefix.session('crr_report_type') == 'all')  @endIf>
                            ALL TYPES
                        </option>
                            @if(!is_null($crr_types_array))
                                @foreach ($crr_types_array as $type)
                                <option value="{{$type->id}}">@if(session($prefix.'crr_report_type') == $type->id)<?php $crrTypeSelection = $type->template_name;?>  @endIf<a  class="uk-dropdown-close">{{$type->template_name}}</a></option>
                                @endforeach
                            @endIf
                        </select>
                </div>

                <div class="uk-width-1-5" >
                    <input id="project-reports-search" name="reports-search" type="text" value="" class=" uk-input" placeholder="REPORT #">

                </div>

            @endCan

        </div>

    </div>
</div>
<hr class="dashed-hr">
<input type="hidden" id="project-reports-current-page" value="{{$reports->currentPage()}}">
<div uk-grid class="uk-margin-top ">

        <div class="uk-width-1-1">
            <div class="uk-align-right uk-label  uk-margin-top uk-margin-right">{{$reports->total()}} @if($reports->total() == 1) REPORT @else REPORTS @endif</div>
            @can('access_auditor')
            <div id="crr-filter-mine" class="uk-button uk-text-right@s uk-margin-right" style="background-color:#1B9A56">
            <a class="uk-contrast" onclick="dynamicModalLoad('new-report?project_id={{$id}}')">
                    <span class="a-file-plus"></span>
                    <span>NEW REPORT</span>
                </a>
            </div>
            @endCan
            @if(session($prefix.'crrp_search') && session($prefix.'crrp_search') !== 'all')
                <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                    <a onClick="loadTab('/projects/{{$id}}/reports?search=all', '6','','','project-',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>REPORT #:{{ session($prefix.'crrp_search') }}</span></a>
                </div>
            @endIf

            @if(session($prefix.'crr_report_status_id') && session($prefix.'crr_report_status_id') !== 'all')
                <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                    <a onClick="loadTab('/projects/{{$id}}/reports?crr_report_status_id=all', '6','','','project-',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ strtoupper($crrStatusSelection) }}</span></a>
                </div>
            @endIf

            @if(session($prefix.'crr_report_lead_id') && session($prefix.'crr_report_lead_id') !== 'all')
                <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                    <a onClick="loadTab('/projects/{{$id}}/reports?crr_report_lead_id=all', '6','','','project-',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>@if($crrLeadSelection == Auth::user()->full_name()) Mine @else {{ $crrLeadSelection }} @endIf</span></a>
                </div>
            @endIf

            @if(session($prefix.'crr_report_type') && session($prefix.'crr_report_type') !== 'all')
                <div id="crr-filter-mine" class="uk-badge uk-text-right@s badge-filter">
                    <a onClick="loadTab('/projects/{{$id}}/reports?crr_report_type=all', '6','','','project-',1);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ $crrTypeSelection }}</span></a>
                </div>
            @endIf
            <div style="display:inline-block" id="report-refreshing"></div>
        </div>
<hr class="dashed-hr uk-width-1-1 uk-margin-bottom uk-grid-margin uk-first-column">

@if(count($reports))
    <style type="text/css">
        .calendar-button {
                position: relative;
                top: 4px;
                font-size: 16px;
        }
        .sort-neutral1 {
            display: inline-block;
            width: 99%;
            height: 18px;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
    <div class="uk-width-1-1 uk-grid-margin uk-first-column">
            <table class="uk-table " id="crr-project-report-list">
                <thead>

                    @php
                    $class = '';
                    $report_asc_desc   = session('report_asc_desc');
                    $report_order_by   = session('report_order_by');

                    if($report_asc_desc == 'asc')
                        $class = 'sort-asc';
                    else
                        $class = 'sort-desc';

                    @endphp
                    <th ><strong>REPORT
                            <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="id" class="<?= ($report_order_by == 'id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                            </span>
                        </strong>
                    </th>
                    <th ><strong> PROJECT</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="project_id" class="<?= ($report_order_by == 'project_id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>

                    <th  width="100px">
                        <strong>AUDIT</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="audit_id" class="<?= ($report_order_by == 'audit_id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>

                    @can('access_auditor') <th ><strong>LEAD</strong>
                    <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                            <a href="JavaScript:void(0);" data-sorting="lead_id" class="<?= ($report_order_by == 'lead_id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                    </span>
                    </th>

                    @endCan

                    <th ><strong>TYPE</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="from_template_id" class="<?= ($report_order_by == 'from_template_id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>

                    <th width="120px"><strong>STATUS</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="crr_approval_type_id" class="<?= ($report_order_by == 'crr_approval_type_id')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>



                    <th width="120px"><strong>CREATED</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="created_at" class="<?= ($report_order_by == 'created_at')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>

                    <th width="120px"><strong>LAST EDITED</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="updated_at" class="<?= ($report_order_by == 'updated_at')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>

                    <th width="120px"><strong>DUE DATE</strong>
                        <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" data-sorting="response_due_date" class="<?= ($report_order_by == 'response_due_date')? $class : 'sort-neutral' ?> sortingcolumn uk-margin-small-top"></a>
                        </span>
                    </th>
                    @can('access_auditor')
                        <th width="40px" uk-tooltip title="Report History"><i class="a-person-clock"></i>
                            <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                    <a href="JavaScript:void(0);" class="sort-neutral1 sortingcolumn uk-margin-small-top"></a>
                            </span>
                        </th>
                        <th  width="80px"><strong>ACTION</strong>
                            <span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" aria-expanded="false">
                                <a href="JavaScript:void(0);" class="sort-neutral1 sortingcolumn uk-margin-small-top"></a>
                            </span>
                        </th>
                    @endCan
                    @can('access_admin')
                    <td><i class="a-trash"></i></td>
                    @endCan

            </thead>

            @include('projects.partials.reports-row')

        </table>
        {{$reports->links()}}
        <script>
            var project_id = {{$id}};

        $(document).ready(function(){
            $('.sortingcolumn').click(function(){
                $("#project-detail-tab-6-content").html('');
                $.get('/projects/'+project_id+'/reports?order_by=' + encodeURIComponent($(this).data('sorting')), function(data) {
                        $("#project-detail-tab-6-content").html(data);
                } );
            }),

            $('.page-link').click(function(){
               $('#project-detail-tab-6-content').load($(this).attr('href'));
               window.current_project_reports_page = $('#project-detail-tab-6-content').load($(this).attr('href'));
               return false;
           });

            // on doc ready we allow updates to start:
            $('#project-crr-newest').val('{{$newest}}');

            $('#project-report-checking').val('0');



            });

            function searchReports(project_id){
                $.get('/projects/'+project_id+'/reports?order_by=' + encodeURIComponent($("#project-reports-search").val()), function(data) {
                            // $('#project-detail-tab-6').load('/projects/'+project_id+'/reports');
                            $('#project-detail-tab-6').trigger("click");
                } );
            }

            // process search
            $(document).ready(function() {
                $('#project-reports-search').keydown(function (e) {
                if (e.keyCode == 13) {
                    searchReports(project_id);
                    e.preventDefault();
                    return false;
                }
                });
            });
            @can('access_auditor')

            function updateStatus(report_id, action) {
                    $.get('/project/'+window.currentProjectOpen+'/reports', {
                                                'id' : report_id,
                                                'action' : action,
                                                'check' : 1
                                                }, function(data2) {

                                            });
                        UIkit.modal.alert('Your message has been saved.',{stack: true});
            }

            function reportAction(reportId,action,project_id = null){
                window.crrActionReportId = reportId;
                //Here goes the notification code
                if(action == 6) {
                    dynamicModalLoad('report-ready/' + reportId + '/' + project_id);
                } else if(action == 2) {
                    dynamicModalLoad('report-send-to-manager/' + reportId + '/' + project_id);
                } else if(action == 3) {
                    dynamicModalLoad('report-decline/' + reportId + '/' + project_id);
                } else if(action == 4) {
                    dynamicModalLoad('report-approve-with-changes/' + reportId + '/' + project_id);
                } else if(action == 5) {
                    dynamicModalLoad('report-approve/' + reportId + '/' + project_id);
                } else if(action == 9) {
                    dynamicModalLoad('report-resolved/' + reportId + '/' + project_id);
                } else if(action != 8){
                    $.get('/projects/'+project_id+'/reports', {
                                                'id' : reportId,
                                                'action' : action
                                                }, function(data2) {

                                            });
                    //loadTab('/dashboard/reports?id='+reportId+'&action='+action, '3','','','',1);
                }else if(action == 8){
                    UIkit.modal.confirm('Refreshing the dynamic data will set the report back to Draft status - are you sure you want to do this?').then(function(){
                        $.get('/projects/'+project_id+'/reports', {
                                                'id' : window.crrActionReportId,
                                                'action' : 8
                                                }, function(data2) {

                                            });
                    },function(){
                        //nope
                    });
                }
                $('#crr-report-action-'+reportId).val(0);
                $('#crr-report-row-'+reportId).slideUp();

            }
            @endCan
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