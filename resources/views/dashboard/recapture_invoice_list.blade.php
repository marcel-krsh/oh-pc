<?php setlocale(LC_MONETARY, 'en_US'); ?>

<?php $totalInvoices = count($recapture_invoices); ?>

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
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by={{ $invoices_sorted_by_query }}&invoices_asc_desc={{ $invoicesAscDesc }}&invoices_program_filter='+this.value)">
					<option value="ALL"
					@if ($invoicesProgramFilter == '%%')
					 selected
					@endif
					>
					FILTER BY PROGRAM 
					</option>
				@foreach ($programs as $program)
					
					<option value="{{$program->id}}"

					@if ($invoicesProgramFilter == $program->id)
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
		<select class="uk-select filter-drops uk-width-1-1" onchange="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by={{ $invoices_sorted_by_query }}&invoices_asc_desc={{ $invoicesAscDesc }}&invoices_status_filter='+this.value)">
					<option value="ALL"
					@if ($invoicesStatusFilter == '%%')
					 selected
					
					@endif
					>
					FILTER BY STATUS
					</option>
				@foreach ($statuses as $status)
					@if($status->id != 1 || ($status->id == 1 && Auth::user()->entity_type == "hfa"))
					<option value="{{$status->id}}"

					@if ($invoicesStatusFilter == $status->id)
					<?php $statusFiltered = $status->invoice_status_name; ?>
					 selected
					@endif	
					>
						{{$status->invoice_status_name}}
					@endif
					</option>       
				@endforeach
			</select>
		
	</div>
	<div class="uk-width-1-1">
		<hr class="dashed-hr">
			<div class="uk-badge uk-badge-notification uk-text-right " style="background: #005186; margin-top: 15px;">&nbsp;{{ number_format($totalInvoices) }} recapture Invoices&nbsp;</div> &nbsp;
			@if(isset($programFiltered))
			 <div class="uk-badge uk-badge-notification uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by={{ $invoices_sorted_by_query }}&invoices_asc_desc={{ $invoicesAscDesc }}&invoices_program_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> FOR {{strtoupper($programFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			@if(isset($statusFiltered))
			 <div class="uk-badge uk-badge-notification uk-text-right " style="background: #7f7f7f; margin-top: 15px;">&nbsp;<a onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by={{ $invoices_sorted_by_query }}&invoices_asc_desc={{ $invoicesAscDesc }}&invoices_status_filter=ALL')" class="uk-dark uk-light"><i class="a-circle-cross"></i> {{strtoupper($statusFiltered)}}</a>&nbsp;</div> &nbsp;
			@endif
			

		<hr class="dashed-hr">
<div class="uk-overflow-container">
						 <table class="uk-table uk-table-striped uk-table-condensed small-table-text" style="min-width: 1420px">
						 	<thead>
						 		<th>
						 			<!-- 12 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=1&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >Date <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-date-arrow-{{ $invoices_sorted_by_query }}"></i>
						 			</small>
						 		</th>
						 		<th>
						 			<!-- 2 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=2&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >INVOICE # <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-invoice-number-arrow-{{ $invoices_sorted_by_query }}"></i>
						 			</small>
						 		</th>
						 		<th>
						 			<!-- 3 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=3&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >ACCOUNT <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-account-arrow-{{ $invoices_sorted_by_query }}"></i>
						 			</small>
						 		</th>
						 		<th>
						 			<!-- 4 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=4&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >PROGRAM <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-program-arrow-{{ $invoices_sorted_by_query }}"></i></small>
						 		</th>
						 		
						 		<th>
						 			<!-- 5 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=5&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >ENTITY <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-entity-arrow-{{ $invoices_sorted_by_query }}"></i></small>
						 		</th>
						 		<th>
						 			<!-- 6 -->
						 			<small># RECAPTURES</small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 9 -->
						 			<small>INVOICED</small>
						 		</th>
						 		<th class="uk-text-right">
						 			<!-- 10 -->
						 			<small>PAID</small>
						 		</th>
						 		<th class="uk-text-center">
						 			<!-- 10 -->
						 			<small><a class="uk-link-muted" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list?invoices_sort_by=11&invoices_asc_desc={{ $invoicesAscDescOpposite }}');" >INVOICE STATUS <i class="{{ $invoicesAscDesc == 'asc' ? 'a-arrow-small-up' : ''}}{{ $invoicesAscDesc == 'desc' ? 'a-arrow-small-down' : ''}} invoices-status-arrow-{{ $invoices_sorted_by_query }}"></i></small>
						 		</th>
						 			
						 		<th>
						 			<!-- 11 -->
						 			<small>ACTION </small>
						 		</th>
						 	</thead>
						 	<tbody>

						 		
						 		@foreach($recapture_invoices as $data)
						 		
						 		
						 		<tr id="i_{{$data->id}}" style="cursor: pointer;">
						 		
						 			<td onclick="displayRecaptureFromInvoice({{$data->id}})">@if($data->created_at)
						 				<div class="date">
											<p class="m-d">{{ date('m',strtotime($data->created_at)) }}/{{ date('d',strtotime($data->created_at)) }}</p><span class="year">{{ date('Y',strtotime($data->created_at)) }}</span>
										</div>
						 			@else
						 			N/A
						 			@endif</td>
						 			<td class="uk-text-center" onclick="displayRecaptureFromInvoice({{$data->id}})">
						 				<small>
						 					{{$data->id}}
						 				</small>
						 			</td>
						 			<td class="uk-text-center" onclick="displayRecaptureFromInvoice({{$data->id}})"><small>{{$data->account_id}}</small></td>

						 			<td onclick="displayRecaptureFromInvoice({{$data->id}})"><small>{{$data->program->program_name}}</small></td>
						 			
						 			<td onclick="displayRecaptureFromInvoice({{$data->id}})"><small>{{$data->entity->entity_name}}</small></td>
						 			<td onclick="displayRecaptureFromInvoice({{$data->id}})"><small>@if($data->RecaptureItem){{$data->RecaptureItem->count()}}@endif</small>
						 			
						 			</td>
						 			<td class="uk-text-right" onclick="displayRecaptureFromInvoice({{$data->id}})"><small >{{money_format('%(8n', $data->totalAmount())}}</small></td>

						 			<td class="uk-text-right" onclick="displayRecaptureFromInvoice({{$data->id}})"><small >{{money_format('%(8n', $data->total_paid)}}</small></td>

						 			
						 			<td class="uk-text-center" onclick="displayRecaptureFromInvoice({{$data->id}})"><small>{{$data->status->invoice_status_name}}</small></td>
						 			<td>
						 				<a class="a-file-copy-2" onclick="window.open('/recapture_invoice/{{$data->id}}', '_blank')" uk-tooltip="View recapture Invoice"></a>
						 				<!-- @if(Auth::user()->entity_type == "hfa")
						 				<a class="uk-icon-edit " onclick="dynamicModalLoad('invoice/edit/{{$data->id}}');" uk-tooltip="Edit"></a>
						 				@endif
						 				<a class="uk-icon-money " onclick="dynamicModalLoad('/invoice/pay/{{$data->id}}');" uk-tooltip="PAY"></a> 
						 				<a class="uk-icon-times-circle " onclick="dynamicModalLoad('/invoice/cancel/{{$data->id}}');" uk-tooltip="Cancel"></a> 
						 				<a class="uk-icon-trash " onclick="dynamicModalLoad('/invoice/delete/{{$data->id}}');" uk-tooltip="Delete"></a> -->
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
function displayRecaptureFromInvoice(id) {
	if ($('.i_'+id+'_d').length){
		$('.i_'+id+'_d').remove();
	}else{
		var recaptures_array = [];
	    $.post('/recapture_invoices/'+id+'/recaptures', {
            '_token' : '{{ csrf_token() }}'
            }, function(data) {
                if(data=='0'){ 
                    UIkit.modal.alert("There was a problem getting the recaptures' information.");
                } else {
                    recaptures_array = data['recaptures']; 
                    var newinput_start = '<tr class="i_'+data['invoice_id']+'_d">'+
					    	'<td></td>'+
					    	'<td colspan="11">'+
					    	'<table class="uk-table ">'+
					    	'<thead>'+
					    	'<tr>'+
					    	'<th width="50">'+
					    	'<small>Date</small></th>'+
					    	'<th width="135"><small>Recapture Id</small></th>'+
					    	'<th width="130"><small>Parcel Id</small></th>'+
					    	'<th width="200"><small>Description</small></th>'+
					    	'<th width="150"><small>Category</small></th>'+
					    	'<th width="220"><small>Program</small></th>'+
					    	'<th width="100"><small>Invoiced</small></th>'+
					    	'</tr>'+
					    	'</thead>'+
					    	'<tbody style="font-size: 80%;">';

					var newinput = '';
                    for (var i = 0; i < recaptures_array.length; i++) {
                    	var target = $('#i_'+id);
					    newinput = newinput+'<tr>'+
					        '<td width="">';

					    if(recaptures_array[i]['created_at_Y'] != "-0001" && recaptures_array[i]['created_at'] !== null){
					    	newinput = newinput+' 	<div class="date">'+	
							'		<p class="m-d">'+recaptures_array[i]['created_at_m']+'/'+recaptures_array[i]['created_at_d']+'</p><span class="year">'+recaptures_array[i]['created_at_Y']+'</span>'+
							'	</div>';
					    }else{
					    	newinput = newinput+"N/A";
					    }

					    
						newinput = newinput+	' </td>'+
					        ' <td width=""><a onclick="window.open(\'/recaptures/'+recaptures_array[i]['parcel_id']+'/'+recaptures_array[i]['id']+'\', \'_blank\')" class="uk-link-muted" uk-tooltip="OPEN RECAPTURE">'+recaptures_array[i]['id']+'</a></td>';

					    newinput = newinput+    ' <td width=""><a onClick="window.open(\'/viewparcel/'+recaptures_array[i]['parcel_id']+'/recaptures-tab\', \'_blank\')" class="uk-link-muted" uk-tooltip="OPEN parcel #'+recaptures_array[i]['parcel']['parcel_id']+'"> '+recaptures_array[i]['parcel']['parcel_id']+'</a></td>';

					    newinput = newinput+ ' </td>'+
					        ' <td width="">'+recaptures_array[i]['description']+'</td>';

					    newinput = newinput+ ' </td>'+
					        ' <td width="">'+recaptures_array[i]['expense_category_name']+'</td>';

					    newinput = newinput+ '<td width="">'+recaptures_array[i]['program']['program_name']+''+
					        ' </td>';

					        if(recaptures_array[i]['recapture_invoice_id'] != 0){
					        newinput = newinput+' <td>'+recaptures_array[i]['total_formatted']+'</td>';
					        }else{
					        	newinput = newinput+' <td>'+recaptures_array[i]['total_formatted']+'</td>';
					        }

					        newinput = newinput+' </tr>';
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