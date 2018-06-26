<?php setlocale(LC_MONETARY, 'en_US'); ?>

<?php $totaldispositions = count($dispositions); ?>
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
<div class="uk-grid">
@if(Auth::user()->entity_type == "hfa")
	<div class="uk-width-1-1 uk-width-1-4@m uk-margin-top ">
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by={{ $dispositions_sorted_by_query }}&dispositions_asc_desc={{ $dispositionsAscDesc }}&dispositions_program_filter='+this.value)">
			<option value="ALL"
				@if ($dispositionsProgramFilter == '%%')
				 selected
				@endif
				>
				FILTER BY PROGRAM
				</option>
			@foreach ($programs as $program)
				<option value="{{$program->id}}"
				@if ($dispositionsProgramFilter == $program->id)
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
	<div class="uk-width-1-1 uk-width-1-4@m uk-margin-top ">
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by={{ $dispositions_sorted_by_query }}&dispositions_asc_desc={{ $dispositionsAscDesc }}&dispositions_status_filter='+this.value)">
			<option value="ALL"
				@if ($dispositionsStatusFilter == '%%')
				 selected 
				@endif
				>
				FILTER BY STATUS 
				</option>
			@foreach ($statuses as $status)
				<option value="{{$status->id}}"
				@if ($dispositionsStatusFilter == $status->id)
				<?php $statusFiltered = $status->invoice_status_name; ?>
				 selected
				@endif	
				>
					{{$status->invoice_status_name}}
				</option>        
			@endforeach
		</select>
	</div>
	<div class="uk-width-1-1">
		<hr class="dashed-hr">
			<div class="uk-badge uk-badge-notification uk-text-right " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totaldispositions) }} Dispositions&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge uk-badge-notification uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by={{ $dispositions_sorted_by_query }}&dispositions_asc_desc={{ $dispositionsAscDesc }}&dispositions_program_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge uk-badge-notification uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by={{ $dispositions_sorted_by_query }}&dispositions_asc_desc={{ $dispositionsAscDesc }}&dispositions_status_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif

		<hr class="dashed-hr">
		<div class="uk-overflow-container">
			 <table class="uk-table  uk-table-striped uk-table-condensed small-table-text" style="min-width: 1420px">
			 	<thead>
			 		<th>
			 			<!-- 12 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=1&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >DATE <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-date-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		<th>
			 			<!-- 2 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=2&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >DISPOSITION # <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-invoice-number-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		<th>
			 			<!-- 3 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=3&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >ACCOUNT <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-account-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		<th>
			 			<!-- 4 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=4&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >PROGRAM <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-program-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		
			 		<th>
			 			<!-- 5 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=5&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >ENTITY <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-entity-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		<th>
			 			<!-- 6 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=6&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >PARCEL <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-number-parcels-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 					 		
			 		<th class="uk-text-right">
			 			<!-- 9 -->
			 			<small>INVOICED <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-total-arrow-{{ $dispositions_sorted_by_query }}"></i></small>
			 		</th>
			 		<th class="uk-text-right">
			 			<!-- 9 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=12&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >PAID <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-paid-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 		<th class="uk-text-center">
			 			<!-- 10 -->
			 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','disposition_list?dispositions_sort_by=10&dispositions_asc_desc={{ $dispositionsAscDescOpposite }}');" >STATUS <i class="{{ $dispositionsAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $dispositionsAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} requests-pos-status-arrow-{{ $dispositions_sorted_by_query }}"></i></a></small>
			 		</th>
			 			
			 		<th>
			 			<!-- 11 -->
			 			<small>ACTION </small>
			 		</th>
			 	</thead>
			 	<tbody>
<?php /////////////////////////////////////////////////////////////////////////////////////////////////////////// BREAKOUT ITEMS

?>
			 		
			 		@foreach($dispositions as $data)
			 		
			 		
			 		<tr id="disposition_{{$data->id}}">
			 		
			 			<td>@if($data->date)
			 				<div class="date">
								<p class="m-d">{{ date('m',strtotime($data->date)) }}/{{ date('d',strtotime($data->date)) }}</p><span class="year">{{ date('Y',strtotime($data->date)) }}</span>
							</div>
			 			@else
			 			NA
			 			@endif</td>
			 			<td class="uk-text-left">
			 				<small>
			 					{{$data->id}}
			 				</small>
			 			</td>
			 			<td class="uk-text-left"><small> {{$data->account_id}}</small></td>

			 			<td class="uk-text-left"><small>{{$data->program_name}}</small></td>
			 			
			 			<td class="uk-text-left"><small>{{$data->entity_name}}</small></td>
			 			<td class="uk-text-left"><small ><a onClick="loadDetailTab('/parcel/','{{$data->parcel_id}}','1',0,0)" class="uk-link-muted" uk-tooltip="OPEN parcel #{{$data->pid}}"> {{$data->pid}}</a></small></td>
			 			<td class="uk-text-right"><small ><a onclick="" class="uk-link-muted" uk-tooltip=""> </a></small></td>
			 			<td class="uk-text-right"><small ><a onclick="" class="uk-link-muted" uk-tooltip=""> </a></small></td>
			 			<td class="uk-text-center"><small>{{$data->status_name}}</small></td>
			 			<td>
			 				<a class="a-file-copy-2" onclick="window.open('/dispositions/{{$data->parcel_id}}', '_blank')" uk-tooltip="View Disposition"></a>
			 			</td>
			 			
			 		</tr>
			 		
			 		@endforeach
			 		
			 	</tbody>
			 </table>
</div>

<script>
function displayParcelsFromDisposition(id) {
	if ($('.disposition_'+id+'_p').length){
		$('.disposition_'+id+'_p').remove();
	}else{
		var parcels_array = [];
	    $.post('/purchase_orders/'+id+'/parcels', {
            '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data=='0'){ 
                    UIkit.modal.alert("There was a problem getting the parcels' information.");
                } else {
                    parcels_array = data; 
                    var newinput_start = '<tr class="disposition_'+id+'_p">'+
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
                    	var target = $('#disposition_'+id);
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

					        newinput = newinput+'<td width="">'+parcels_array[i]['landbank_property_status']['option_name']+'</td>'+
					        @if(Auth::user()->entity_type == "hfa")
					        '<td width="">'+parcels_array[i]['hfa_property_status']['option_name']+'</td>'+
					        @endif
					        ' </tr>';
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