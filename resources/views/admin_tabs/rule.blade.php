<div class="uk-overflow-container uk-margin-top">
    <h4 class="uk-text-left">{{count($rules)}} TOTAL PROGRAM RULES <small>

            <a onclick="$('.inactiveRule').toggle();" class="uk-button uk-button-default uk-button-small uk-align-right">
                <span class="inactiveRule">SHOW</span>
                <span class="inactiveRule" style="display:none;">HIDE</span> DISABLED PROGRAM RULES
            </a>
        </small>
    </h4>
    <hr>
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text">
        <thead>
        <th>
            <small>NAME</small>
        </th>
        <th>
            <small>ACQUISITION</small>
        </th>
        <th>
            <small>PRE DEMOLITION</small>
        </th>
        <th>
            <small>DEMOLITION</small>
        </th>
        <th>
            <small>GREENING</small>
        </th>
        <th>
            <small>MAINTENANCE</small>
        </th>
        <th>
            <small>ADMINISTRATION</small>
        </th>
        <th>
            <small>OTHERS</small>
        </th>
        <th>
            <small>NIP LOAN</small>
        </th>
        <th>
            <small>REIMB. RULES</small>
        </th>
        <th>
            <small>ACTION</small>
        </th>
        </thead>
        <tbody>
        @foreach($rules as $data)
            <tr @if($data->active == 0) class='inactiveRule' style="display:none;" @endif>
                <td><small><a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted"> {{$data->rules_name}}</a></small></td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{ $data->acquisition_advance === 1 ? "Yes" : "No" }}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->acquisition_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->acquisition_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->acquisition_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{ $data->pre_demo_advance === 1 ? "Yes" : "No" }}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->pre_demo_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->pre_demo_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->pre_demo_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->demolition_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->demolition_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->demolition_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->demolition_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->greening_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->greening_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->greening_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->greening_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->maintenance_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->maintenance_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->maintenance_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->maintenance_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->administration_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->administration_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->administration_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->administration_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->other_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->other_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->other_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->other_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Advance: </strong>{{$data->nip_loan_payoff_advance === 1 ? "Yes" : "No"}}</p>
                            <p><strong>Maximum Advance: </strong>{{$data->nip_loan_payoff_max_advance}}</p>
                            <p><strong>Maximum: </strong>{{$data->nip_loan_payoff_max}}</p>
                            <p><strong>Minimum: </strong>{{$data->nip_loan_payoff_min}}</p>
                        </a>
                    </small>
                </td>
                <td><small>
                        <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted">
                            <p><strong>Parcel Reimbursement Rules</strong></p>
                            <p><strong>Maximum reimbursement: </strong>@if(count($data->reimbursementRules)) {{$data->reimbursementRules[0]->maximum_reimbursement}}@endif</p>
                            <p><strong>Maximum units: </strong>@if(count($data->reimbursementRules)){{$data->reimbursementRules[0]->maximum_units}}@endif</p>
                            <p><strong>Minimum units: </strong>@if(count($data->reimbursementRules)){{$data->reimbursementRules[0]->minimum_units}}@endif</p>
                        </a>
                    </small>
                </td>
                <td>
                    <a onclick="dynamicModalLoad('admin/rule/create/{{$data->id}}')" class="uk-link-muted"><i class="a-pencil-2 uk-margin-right"></i></a>
                    <a @if($data->active == 1) style="display:none;" @endif class='activateRule uk-link-muted' href="modals/admin/activate/rule/{{$data->id }}" title="Click to activate rule" >
                        <i class="uk-text-danger a-locked-2" onmouseout="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseover="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i></a>
                    <a @if($data->active == 0) style="display:none;" @endif class='deactivateRule uk-link-muted' href="modals/admin/deactivate/rule/{{$data->id }}" title="Click to deactivate rule">
                        <i class="a-unlocked" onmouseover="$(this).removeClass('a-unlocked');$(this).addClass('a-locked-2');" onmouseout="$(this).removeClass('a-locked-2');$(this).addClass('a-unlocked');">
                        </i>
                    </a>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>
</div>
<script>
    $(".activateRule,.deactivateRule").on('click', function(e){
        e.preventDefault();
        var el = $(this);
        $.get(el.attr('href'), function(data){
            UIkit.modal.alert('<h2>'+data+'</h2>');
            $('#rules-tab-content').load('/tabs/rule');
        });
    });
</script>