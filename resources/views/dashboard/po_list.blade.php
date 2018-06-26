<?php setlocale(LC_MONETARY, 'en_US'); ?>

<?php $totalpos = count($pos); ?>
<?php 
//dd($pos);
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
$data->total_poed
$data->total_approved
$data->total_amount
$data->total_paid
$data->invoice_status_name
    
*/ ?>
<style>
	
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
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','po_list?pos_sort_by={{ $pos_sorted_by_query }}&pos_asc_desc={{ $posAscDesc }}&pos_program_filter='+this.value)">
				<option value="ALL"
					@if ($posProgramFilter == '%%')
					 selected
					@endif
					>
					FILTER BY PROGRAM
					</option>
				@foreach ($programs as $program)
					
					<option value="{{$program->id}}"

					@if ($posProgramFilter == $program->id)
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
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','po_list?pos_sort_by={{ $pos_sorted_by_query }}&pos_asc_desc={{ $posAscDesc }}&pos_status_filter='+this.value)">
				<option value="ALL"
					@if ($posStatusFilter == '%%')
					 selected 
					
					@endif
					>
					FILTER BY STATUS 
					</option>
				@foreach ($statuses as $status)
					
					<option value="{{$status->id}}"

					@if ($posStatusFilter == $status->id)
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
			<div class="uk-badge  uk-text-right " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totalpos) }} Purchase Orders&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by={{ $pos_sorted_by_query }}&pos_asc_desc={{ $posAscDesc }}&pos_program_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge  uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by={{ $pos_sorted_by_query }}&pos_asc_desc={{ $posAscDesc }}&pos_status_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
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
						 <table class="uk-table  uk-table-striped uk-table-small small-table-text" style="min-width: 1420px">
						 	<thead>
						 		<th>
						 			<!-- 12 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=1&pos_asc_desc={{ $posAscDescOpposite }}');" >Date <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-date-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 2 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=2&pos_asc_desc={{ $posAscDescOpposite }}');" >po # <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-invoice-number-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 3 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=3&pos_asc_desc={{ $posAscDescOpposite }}');" >ACCOUNT <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-account-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 4 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=4&pos_asc_desc={{ $posAscDescOpposite }}');" >PROGRAM <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-program-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		
						 		<th>
						 			<!-- 5 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=5&pos_asc_desc={{ $posAscDescOpposite }}');" >ENTITY <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-entity-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 6 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=6&pos_asc_desc={{ $posAscDescOpposite }}');" ># PARCELS <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-number-parcels-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 7 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=7&pos_asc_desc={{ $posAscDescOpposite }}');" >REQUESTED <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-requested-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th >
						 		<th class="uk-text-right">
						 			<!-- 8 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=8&pos_asc_desc={{ $posAscDescOpposite }}');" >APPROVED <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-approved-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		
						 		<th class="uk-text-right">
						 			<!-- 9 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=9&pos_asc_desc={{ $posAscDescOpposite }}');" >INVOICED <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-total-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 9 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=12&pos_asc_desc={{ $posAscDescOpposite }}');" >PAID <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-paid-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 		<th class="uk-text-center">
						 			<!-- 10 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','po_list?pos_sort_by=10&pos_asc_desc={{ $posAscDescOpposite }}');" >PO STATUS <i class="{{ $posAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $posAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-status-arrow-{{ $pos_sorted_by_query }}"></i></small>
						 		</th>
						 			
						 		<th>
						 			<!-- 11 -->
						 			<small>ACTION </small>
						 		</th>
						 	</thead>
						 	<tbody>
<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////// BREAKOUT ITEMS

?>
						 		
						 		@foreach($pos as $data)
						 		
						 		
						 		<tr id="po_{{$data->po_id}}" style="cursor: pointer;">
						 		
						 			<td onclick="displayParcelsFromPO({{$data->po_id}})">@if($data->date)
						 				<div class="date">
											<p class="m-d">{{ date('m',strtotime($data->date)) }}/{{ date('d',strtotime($data->date)) }}</p><span class="year">{{ date('Y',strtotime($data->date)) }}</span>
										</div>
						 			@else
						 			NA
						 			@endif</td>
						 			<td class="uk-text-center" onclick="displayParcelsFromPO({{$data->po_id}})">
						 				<small>
						 					{{$data->po_id}}
						 				</small>
						 			</td>
						 			<td class="uk-text-center" onclick="displayParcelsFromPO({{$data->po_id}})"><small> {{$data->account_id}}</small></td>

						 			<td onclick="displayParcelsFromPO({{$data->po_id}})"><small>{{$data->program_name}}</small></td>
						 			
						 			<td onclick="displayParcelsFromPO({{$data->po_id}})"><small>{{$data->entity_name}}</small></td>
						 			<td onclick="displayParcelsFromPO({{$data->po_id}})"><small>{{$data->total_parcels}}</small>
						 			
						 			</td>
						 			<td class="uk-text-right" onclick="displayParcelsFromPO({{$data->po_id}})"><small ><a onclick="" class="uk-link-muted" uk-tooltip="OPEN po #{{$data->req_id}}"> {{money_format('%(8n', $data->total_requested)}}</a></small></td>
						 			<td class="uk-text-right" onclick="displayParcelsFromPO({{$data->po_id}})"><small ><a onclick="" class="uk-link-muted" uk-tooltip="PO #{{$data->po_id}}">{{money_format('%(8n', $data->total_approved)}}</a></small></td>

						 			<td class="uk-text-right" onclick="displayParcelsFromPO({{$data->po_id}})"><small ><a onclick="" class="uk-link-muted" uk-tooltip="@if(!is_null($data->invoice_id)) INVOICE #{{$data->invoice_id}} @else NA @endif">{{money_format('%(8n', $data->total_invoiced)}}</a></small></td>

						 			<td class="uk-text-right" onclick="displayParcelsFromPO({{$data->po_id}})"><small ><a onclick="" class="uk-link-muted" uk-tooltip="@if(!is_null($data->invoice_id)) INVOICE #{{$data->invoice_id}} @else NA @endif">{{money_format('%(8n', $data->total_paid)}}</a></small></td>



						 			
						 			<td class="uk-text-center" onclick="displayParcelsFromPO({{$data->po_id}})"><small>{{$data->invoice_status_name}}</small></td>
						 			<td>
						 				<a class="a-file-copy-2" onclick="window.open('/po/{{$data->po_id}}', '_blank')" uk-tooltip="View PO"></a>
						 				<!-- <a class="uk-icon-edit " onclick="dynamicModalLoad('/invoice/edit/{{$data->po_id}}');" uk-tooltip="Edit"></a>
						 				<a class="uk-icon-file-o " onclick="dynamicModalLoad('/invoice/post/{{$data->po_id}}');" uk-tooltip="INVOICE"></a> 
						 				<a class="uk-icon-times-circle " onclick="dynamicModalLoad('/invoice/cancel/{{$data->po_id}}');" uk-tooltip="Cancel"></a> 
						 				<a class="uk-icon-trash " onclick="dynamicModalLoad('/invoice/delete/{{$data->po_id}}');" uk-tooltip="Delete"></a> -->
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
function displayParcelsFromPO(id) {
	if ($('.po_'+id+'_p').length){
		$('.po_'+id+'_p').remove();
	}else{
		var parcels_array = [];
	    $.post('/purchase_orders/'+id+'/parcels', {
            '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data=='0'){ 
                    UIkit.modal.alert("There was a problem getting the parcels' information.");
                } else {
                    parcels_array = data; 
                    var newinput_start = '<tr class="po_'+id+'_p">'+
					    	'<td></td>'+
					    	'<td colspan="11">'+
					    	'<table class="uk-table ">'+
					    	'<thead>'+
					    	'<tr>'+
					    	'<th width="50">'+
					    	'<small>Date</small></th>'+
					    	'<th width="135"><small>Parcel Id</small></th>'+
					    	'<th width="360"><small>Address</small></th>'+
					    	'<th width="220"><small>Program</small></th>'+
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
                    	var target = $('#po_'+id);
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