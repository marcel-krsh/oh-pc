@forEach($reports as $report)
<tr id="crr-project-report-row-{{$report->id}}">

                    <td><a href="/report/{{$report->id}}" target="report-{{$report->id}}" class="uk-mute"><i class="a-file-chart-3"></i> #{{$report->id}}</a></td>
                    <td>@if($auditor_access)<a onclick="loadTab('/projects/{{$report->project->project_key}}', '4', 1, 1,'',1);" class="uk-mute"> @endIf {{$report->project->project_number}} : {{$report->project->project_name}}@if($auditor_access)</a>@endIf</td>
                    <td>{{$report->audit_id}}</td>
                    @if($auditor_access)<td>@if($report->lead->person){{$report->lead->person->first_name}} {{$report->lead->person->last_name}}@else NA @endif</td>@endIf
                    <td>{{$report->template()->template_name}}</td>
                    <td>{{$report->crr_approval_type->name}}</td>

                    <td>{{ date('M d, Y',strtotime($report->created_at)) }}</td>
                    <td>{{ ucfirst($report->updated_at->diffForHumans()) }}</td>
                    <td>@if(!is_null($report->response_due_date))
                            @if(strtotime($report->response_due_date) < time() && $report->crr_approval_type_id !== 9)
                                <span class="attention" style="color:darkred"> <i class="a-warning"></i>
                            @endIf
                            {{date('M d, Y',strtotime($report->response_due_date)) }}

                            @if(strtotime($report->response_due_date) < time() && $report->crr_approval_type_id !== 9)
                               </span>
                            @endIf
                        @endIf

                        @if($auditor_access)
                            @if(!is_null($report->response_due_date))
                                 <a class=" flatpickr selectday{{$report->id}} flatpickr-input "><input type="text" placeholder="Edit Due Date.." data-input="" style="display:none" ><i class="a-pencil " ></i></a>
                            @else
                                <a class="uk-button uk-button-small uk-button-success flatpickr selectday{{$report->id}} flatpickr-input"><input type="text" placeholder="Select Due Date.." data-input="" style="display:none" ><i class="a-calendar-pencil calendar-button " ></i></a>
                            @endIf

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
                                $.get('/dashboard/reports',{'report_id':{{$report->id}},'due':encodeURIComponent(this.value)});
                                $('#crr-project-report-row-{{$report->id}}').slideUp();
                                if($('#project-reports-current-page').val() !== '1') {
                                	UIkit.modal.confirm('<h1>Due Date Updated</h1><p>Your new due date was updated, would you like to go to the first page of the results?</p>').then(function(){
                                			$('#project-reports-current-page').val(1);
                                			$('#project-detail-tab-6').trigger("click");

                                		}, function() {
                                			$('#project-detail-tab-6').trigger("click");
                                		});
                                } else {
                                	$('#project-detail-tab-6').trigger("click");
                                }
                            });
                            function deleteThisReport{{$report->id}}(){
                                UIkit.modal.confirm('Are you sure you want to delete the {{$report->template()->template_name}} report #{{$report->id}}?').then(function() {
                                    $.get('/tabs/report/delete/{{$report->id}}');
                                    $('#crr-report-row-{{$report->id}}').slideUp();
                                    UIkit.notification({
                                        message: '{{$report->template()->template_name}} Report #{{$report->id}} has been deleted.',
                                        status: 'primary',
                                        pos: 'top-right',
                                        timeout: 1500
                                    });
                                },
                                    function () {
                                        console.log('Delete Cancelled.')
                                });
                            }


                            </script>
                        @endIf
                    </td>
                    @if($auditor_access)
                    <td><i @if($report->report_history) class="a-person-clock uk-link"  uk-toggle="target: #project-report-{{$report->id}}-history;" @else class="a-clock-not" @endIf></i></td>
                    @endIf
                    @if($auditor_access)<td>
                       <?php
//ACTION OPTIONS BASED ON STATUS AND USER ROLE
?>
                        @if($auditor_access)
                                    @if($report->crr_approval_type_id !== 8)
                                    <select id="crr-report-action-{{$report->id}}" onchange="reportAction({{$report->id}},this.value, {{ $report->project->id }});" style="width: 184px;">
                                        <option value="0">ACTION</option>
                                        <option value="1">DRAFT</option>
                                        @if($report->requires_approval)
                                        <option value="2">SEND TO MANAGER REVIEW</option>
                                        @endIf
                                        @if($manager_access)
                                            @if($report->requires_approval)
                                            <option value="3">DECLINE</option>
                                            <option value="4">APPROVE WITH CHANGES</option>
                                            <option value="5">APPROVE</option>
                                            @endIf
                                        @endIf
                                        @if(($report->requires_approval == 1 && $report->crr_approval_type_id > 3) || $report->requires_approval == 0 || Auth::user()->can('access_manager'))
                                        <option value="6">SEND TO PROPERTY CONTACT</option>
                                        <option value="7">PROPERTY VIEWED IN PERSON</option>
                                        <option value="9">ALL ITEMS RESOLVED</option>
                                        @endIf
                                        {{-- @if(!$report->audit->is_archived() || Auth::user()->can('access_manager'))
                                        <option value="8">REFRESH DYNAMIC DATA</option>
                                        @endIf --}}
                                        {{-- Commented above code to have refresh reports only from rports page and link in reports -Div 20190610 --}}

                                    </select>
                                    @else
                                    <div style="margin-left: auto; margin-righ:auto;" uk-spinner></div>
                                    @endIf
                        @endIf

                    </td>
                    @endIf
                     @if($admin_access)
                    <td><i class="a-trash use-hand-cursor" onclick="deleteThisReport{{$report->id}}();"></i></td>
                    @endIf
                </tr>
                @if($auditor_access)
                @if($report->report_history)

                <tr id="project-report-{{$report->id}}-history" hidden>
                    <td  ></td>


                        <td colspan="10">

                        <table class="uk-table uk-table-striped">

                            <thead>
                                <th width="80px"> DATE</th>
                                <th width="120px">USER</th>
                                <th>NOTE</th>
                            </thead>

                            <?php
$history = collect($report->report_history);
?>
                            @forEach($history as $h)
                                <tr>
                                    <td> {{$h['date']}}</td>
                                    <td>{{$h['user_name']}} @if($admin_access)<i class="a-info-circle uk-link"  onClick="openUserPreferencesView({{$h['user_id']}});"></i>@endIf</td>
                                    <td>{{$h['note']}}</td>
                                </tr>
                            @endForEach

                        </table>
                        @if($admin_access)

                                    <div class="uk-width-1-1 uk-margin-top uk-margin-bottom"><small> ADMINS: Information presented was current at time of recording the record. Click the <i class="a-info-circle"></i> icon to view a user's current information.</small></div>
                            @endIf
                    </td>

                </tr>
                @endIf
                @endIf
@endForEach