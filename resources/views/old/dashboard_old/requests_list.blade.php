<?php setlocale(LC_MONETARY, 'en_US'); ?>

<?php $totalRequests = count($requests); ?>
<?php 
//dd($requests);
/*

$data->invoice_id
$data->account_id
$data->date
$data->po_id
$data->req_id 
$data->program_id 
$data->entity_id 
$data->program_name 
$data->entity_name 
$data->total_parcels
$data->total_requested
$data->total_approved
$data->total_amount
$data->total_paid
$data->invoice_status_name
    
*/ ?>
<style>
	.date {
		width: 40px;
		background: #fff;
	text-align: center;
	font-family: 'Helvetica', sans-serif;
	position: relative;
	}
	.year {
		display: block;
		background-color: lightgray;
		color: white;
		font-size: 12px;
	}
	.m-d {
		font-size: 14px;
		line-height: 14px;
    padding-top: 7px;
    margin-top: 0px;
    padding-bottom: 7px;
    margin-bottom: 0px;

	}
	.filter-drops{
					    -webkit-appearance: none;
					    -moz-appearance: none;
					    margin: 0;
					    border: none;
					    overflow: visible;
					    font: inherit;
					    color: #3a3a3a;
					    text-transform: none;
					    display: inline-block;
					    box-sizing: border-box;
					    padding: 0 12px;
					    background-color: #f5f5f5;
					    vertical-align: middle;
					    line-height: 28px;
					    min-height: 30px;
					    font-size: 1rem;
					    text-decoration: none;
					    text-align: center;
					    border: 1px solid rgba(0, 0, 0, 0.06);
					    border-radius: 4px;
					    text-shadow: 0 1px 0 #ffffff;
					    background: url(/images/select_icon.png) no-repeat;
					    background-position: 5px 7px;
					    text-indent: 13.01px;
					    background-size: 18px;
					    background-color: #f5f5f5;
				}
				select::-ms-expand {
				    display: none;
				}

</style>
<div uk-grid class="uk-child-width-1-1 uk-child-width-1-5@s">
@if(Auth::user()->entity_type == "hfa")
	<div class="uk-margin-top ">
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','request_list?requests_sort_by={{ $requests_sorted_by_query }}&requests_asc_desc={{ $requestsAscDesc }}&requests_program_filter='+this.value)">
				<option value="ALL" 
					@if ($requestsProgramFilter == '%%')
					selected  
					@endif
					>
					FILTER BY PROGRAM 
					</option>
				@foreach ($programs as $program)
					
					<option value="{{$program->id}}"

					@if ($requestsProgramFilter == $program->id)
					 selected 
					<?php $programFiltered = $program->program_name; ?>
					@endif	
					>
						{{$program->program_name}}

					</option>        
				@endforeach
		</select>
	</div>
@endif
	<div class="uk-margin-top ">
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','request_list?requests_sort_by={{ $requests_sorted_by_query }}&requests_asc_desc={{ $requestsAscDesc }}&requests_status_filter='+this.value)">
				<option value="ALL" 
					@if ($requestsStatusFilter == '%%')
					selected  
					
					@endif
					>
					FILTER BY STATUS
					</option>
				@foreach ($statuses as $status)
					
					<option value="{{$status->id}}"

					@if ($requestsStatusFilter == $status->id)
					<?php $statusFiltered = $status->invoice_status_name; ?>
					 selected 
					@endif
					>	
						{{$status->invoice_status_name}}

					</option>         
				@endforeach
		</select>
		
	</div>
</div>
<div uk-grid>
	<div class="uk-width-1-1"> 
		<hr class="dashed-hr">
			<div class="uk-badge  uk-text-right " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totalRequests) }} Requests&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by={{ $requests_sorted_by_query }}&requests_asc_desc={{ $requestsAscDesc }}&requests_program_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by={{ $requests_sorted_by_query }}&requests_asc_desc={{ $requestsAscDesc }}&requests_status_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			
			<?php /*
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">COST: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">REQ: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PO: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">INV: </div>
			<div class="uk-badge  uk-text-right " style="background: #4D4D4D; margin-top: 15px;">PAID: </div>
			*/ ?>

		<hr class="dashed-hr">
<div class="uk-overflow-auto">
						 <table class="uk-table uk-table-striped  uk-table-small small-table-text" style="min-width: 1420px">
						 	<thead>
						 		<th>
						 			<!-- 12 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=1&requests_asc_desc={{ $requestsAscDescOpposite }}');" >Date <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-date-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 2 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=2&requests_asc_desc={{ $requestsAscDescOpposite }}');" >REQUEST # <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-invoice-number-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 3 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=3&requests_asc_desc={{ $requestsAscDescOpposite }}');" >ACCOUNT <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-account-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 4 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=4&requests_asc_desc={{ $requestsAscDescOpposite }}');" >PROGRAM <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-program-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		
						 		<th>
						 			<!-- 5 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=5&requests_asc_desc={{ $requestsAscDescOpposite }}');" >ENTITY <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-entity-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 6 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=6&requests_asc_desc={{ $requestsAscDescOpposite }}');" ># PARCELS <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-number-parcels-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 7 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=7&requests_asc_desc={{ $requestsAscDescOpposite }}');" >REQUESTED <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-requested-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th >
						 		<th class="uk-text-right">
						 			<!-- 8 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=8&requests_asc_desc={{ $requestsAscDescOpposite }}');" >APPROVED <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-approved-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		
						 		<th class="uk-text-right">
						 			<!-- 9 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=9&requests_asc_desc={{ $requestsAscDescOpposite }}');" >INVOICED <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-total-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 9 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=12&requests_asc_desc={{ $requestsAscDescOpposite }}');" >PAID <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-paid-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-center">
						 			<!-- 10 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','request_list?requests_sort_by=10&requests_asc_desc={{ $requestsAscDescOpposite }}');" >REQ STATUS <i class="{{ $requestsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $requestsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-status-arrow-{{ $requests_sorted_by_query }}"></i></small>
						 		</th>
						 			
						 		<th>
						 			<!-- 11 -->
						 			<small>ACTION </small>
						 		</th>
						 	</thead>
						 	<tbody>
<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////// BREAKOUT ITEMS

?>
						 		
						 		@foreach($requests as $data)
						 		
						 		
						 		<tr id="r_{{$data->req_id}}" style="cursor: pointer;">
						 		
						 			<td onclick="displayParcelsFromRequest({{$data->req_id}})">
						 			@if($data->date)
						 				<div class="date">
											<p class="m-d">{{ date('m',strtotime($data->date)) }}/{{ date('d',strtotime($data->date)) }}</p><span class="year">{{ date('Y',strtotime($data->date)) }}</span>
										</div>
						 			@else
						 			NA
						 			@endif
						 				
						 				</td>
						 			<td class="uk-text-center" onclick="displayParcelsFromRequest({{$data->req_id}})">
						 				<small>
						 					<a onclick="" class="uk-link-muted display-block" uk-tooltip="OPEN REQUEST #{{$data->req_id}}">{{$data->req_id}}</a>
						 				</small>
						 			</td>
						 			<td class="uk-text-center" onclick="displayParcelsFromRequest({{$data->req_id}})"><small>{{$data->account_id}}</small></td>

						 			<td onclick="displayParcelsFromRequest({{$data->req_id}})"><small>{{$data->program_name}}</small></td>
						 			
						 			<td onclick="displayParcelsFromRequest({{$data->req_id}})"><small>{{$data->entity_name}}</small></td>
						 			<td onclick="displayParcelsFromRequest({{$data->req_id}})"><small>{{$data->total_parcels}}</small>
						 			
						 			</td>
						 			<td class="uk-text-right" onclick="displayParcelsFromRequest({{$data->req_id}})"><small >{{money_format('%(8n', $data->total_requested)}}</small></td>
						 			<td class="uk-text-right" onclick="displayParcelsFromRequest({{$data->req_id}})"><small >{{money_format('%(8n', $data->total_approved)}}</small></td>

						 			<td class="uk-text-right" onclick="displayParcelsFromRequest({{$data->req_id}})"><small >{{money_format('%(8n', $data->total_invoiced)}}</small></td>

						 			<td class="uk-text-right" onclick="displayParcelsFromRequest({{$data->req_id}})"><small >{{money_format('%(8n', $data->total_paid)}}</small></td>



						 			
						 			<td class="uk-text-center" onclick="displayParcelsFromRequest({{$data->req_id}})"><small>{{$data->invoice_status_name}}</small></td>
						 			<td>
						 				<a class="a-file-copy-2" onclick="window.open('/requests/{{$data->req_id}}', '_blank')" uk-tooltip="View Request"></a>
						 				
						 				<!-- <a class="uk-icon-edit " onclick="dynamicModalLoad('/invoice/edit/{{$data->req_id}}');" uk-tooltip="Edit"></a>
						 				<a class="uk-icon-check-square-o " onclick="dynamicModalLoad('/po/post/{{$data->req_id}}');" uk-tooltip="APPROVE"></a> 
						 				<a class="uk-icon-times-circle " onclick="dynamicModalLoad('/invoice/cancel/{{$data->invoice_id}}');" uk-tooltip="Cancel"></a> 
						 				<a class="uk-icon-trash " onclick="dynamicModalLoad('/invoice/delete/{{$data->invoice_id}}');" uk-tooltip="Delete"></a> -->
						 			</td>
						 			
						 		</tr>
						 		
						 		@endforeach
						 		
						 	</tbody>
						 </table>
</div>

<div id="list-tab-bottom-bar" class="uk-flex-middle"  style="height:50px;">
<a  href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default uk-button-small uk-align-right uk-margin-top uk-margin-right" style="margin-right:302px !important"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a> 

</div>

<script>
function displayParcelsFromRequest(id) {
	if ($('.r_'+id+'_p').length){
		$('.r_'+id+'_p').remove();
	}else{
		var parcels_array = [];
	    $.get('/requests/'+id+'/parcels', {
            '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data=='0'){ 
                    UIkit.modal.alert("There was a problem getting the parcels' information.");
                } else {
                    parcels_array = data; 
                    var newinput_start = '<tr class="r_'+id+'_p">'+
					    	'<td></td>'+
					    	'<td colspan="11">'+
					    	'<table class="uk-table">'+
					    	'<thead>'+
					    	'<tr>'+
					    	'<th width="50">'+
					    	'<small>Date</small></th>'+
					    	'<th width="135"><small>Parcel Id</small></th>'+
					    	'<th width="360"><small>Address</small></th>'+
					    	'<th width="160"><small>Program</small></th>'+
					    	'<th width="100"><small>Cost</small></th>'+
					    	'<th width="100"><small>Requested</small></th>'+
					    	'<th width="100"><small>Approved</small></th>'+
					    	'<th width="100"><small>Invoiced</small></th>'+
					    	'<th width="60"><small>Landbank Status</small></th>'+
					    	@if(Auth::user()->entity_type == "hfa")
					    	'<th width="60"><small>HFA Status</small></th>'+
					    	@endif
					    	'</tr>'+
					    	'</thead>'+
					    	'<tbody style="font-size: 80%;">';

					var newinput = '';
                    for (var i = 0; i < parcels_array.length; i++) {
                    	var target = $('#r_'+id);
					    newinput = newinput+'<tr>'+
					        '<td width="">'+	
					        ' 	<div class="date">'+	
							'		<p class="m-d">'+parcels_array[i]['created_at_m']+'/'+parcels_array[i]['created_at_d']+'</p><span class="year">'+parcels_array[i]['created_at_Y']+'</span>'+
							'	</div>'+
							' </td>'+
					        ' <td width=""><a onClick="loadDetailTab(\'/parcel/\',\''+parcels_array[i]['id']+'\',\'1\',0,0)">'+parcels_array[i]['parcel_id']+'</a></td>'+
					        ' <td width=""><a href="'+parcels_array[i]['google_map_link']+'" target="_blank">'+parcels_array[i]['street_address']+', '+parcels_array[i]['city']+', '+parcels_array[i]['state_acronym']+' '+parcels_array[i]['zip']+'</a></td>'+
					        '<td width="">'+parcels_array[i]['program']['program_name']+''+
					        ' </td>';

					        if(parcels_array[i]['reimbursement_request_id'] != 0){
					        	newinput = newinput+'<td>'+parcels_array[i]['cost_total_formatted']+'</td>'+
					        	' <td>'+parcels_array[i]['requested_total_formatted']+'<br/><small><a onclick="window.open(\'/requests/'+parcels_array[i]['reimbursement_request_id']+'\', \'_blank\')">RQ# '+parcels_array[i]['reimbursement_request_id']+'</a></small></td>';
					        }else{
					        	newinput = newinput+'<td>'+parcels_array[i]['cost_total_formatted']+'</td>'+
					        ' <td>'+parcels_array[i]['requested_total_formatted']+'</td>';
					        }

					        if(parcels_array[i]['purchase_order_id'] != 0){
					        	newinput = newinput+'<td>'+parcels_array[i]['approved_total_formatted']+'<br/><small><a onclick="window.open(\'/po/'+parcels_array[i]['purchase_order_id']+'\', \'_blank\')">PO# '+parcels_array[i]['purchase_order_id']+'</a></small></td>';
					        }else{
					        	newinput = newinput+'<td>'+parcels_array[i]['approved_total_formatted']+'</td>';
					        }

					        if(parcels_array[i]['reimbursement_invoice_id'] != 0){
					        newinput = newinput+' <td>'+parcels_array[i]['invoiced_total_formatted']+'<br/><small><a onclick="window.open(\'/invoices/'+parcels_array[i]['reimbursement_invoice_id']+'\', \'_blank\')">IN# '+parcels_array[i]['reimbursement_invoice_id']+'</a></small></td>';
					        }else{
					        	newinput = newinput+' <td>'+parcels_array[i]['invoiced_total_formatted']+'</td>';
					        }

					        if(parcels_array[i]['landbank_property_status'] !== null){
					        	newinput = newinput+'<td width="">'+parcels_array[i]['landbank_property_status']['option_name']+'</td>';
					        	if(parcels_array[i]['hfa_property_status'] !== null){
					        		@if(Auth::user()->entity_type == "hfa")
							        newinput = newinput+'<td width="">'+parcels_array[i]['hfa_property_status']['option_name']+'</td>';
							        @endif
					        	}
						        
						        newinput = newinput+' </tr>';
					        }else{
					        	newinput = newinput+'<td width=""></td>'+
						        @if(Auth::user()->entity_type == "hfa")
						        '<td width=""></td>'+
						        @endif
						        ' </tr>';
					        }

					} 
					var newinput_end = '</tbody></table>'+
					    	'</td>'+
					    	'</tr>';
					target.closest('tr').after(newinput_start+newinput+newinput_end);
            	}
	    });
	}
}
</script>
