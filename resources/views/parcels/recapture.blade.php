<?php setlocale(LC_MONETARY, 'en_US'); ?>

<div class="uk-grid-collapse" uk-grid>
	<div class="uk-width-1-1 uk-margin-top  uk-margin-bottom">
		<div class="uk-panel">
			@if(count($parcel->recaptures))
			<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed small-table-text" >
			 	<thead>
			 		<th>
			 			<small>DATE CREATED</small>
			 		</th>
			 		<th>
			 			<small>CATEGORY</small>
			 		</th>
			 		<th>
			 			<small>DESCRIPTION</small>
			 		</th>
			 		<th>
			 			<small>AMOUNT</small>
			 		</th>
			 		<th>
			 			<small>ACTIONS</small>
			 		</th>
			 	</thead>
			 	<tbody>
			 		@foreach($parcel->recaptures as $recapture)
					<tr>
						<td>
							@if($recapture->created_at !== null && $recapture->created_at != "-0001-11-30 00:00:00" && $recapture->created_at != "0000-00-00 00:00:00")
							<div class="date">
								<p class="m-d">{{ date('m',strtotime($recapture->created_at)) }}/{{ date('d',strtotime($recapture->created_at)) }}</p><span class="year">{{ date('Y',strtotime($recapture->created_at)) }}</span>
							</div>
							@else
							N/A
							@endif
						</td>
						<td>{{$recapture->expenseCategory->expense_category_name}}</td>
				        <td>{{$recapture->description}}</td>
						<td>{{money_format('%n', $recapture->amount)}}</td>
						<td>
							@if($recapture->invoice->status_id == 1)
							<a class="a-pencil-2" onclick="dynamicModalLoad('recapture/{{$recapture->id}}/edit');" uk-tooltip="Edit recapture"></a>
							@endif
			 				<a class="a-file-copy-2" onclick="window.open('recapture_invoice/{{$recapture->recapture_invoice_id}}', '_blank')" uk-tooltip="View Invoice"></a> 
						
						</td>
					</tr>
					@endforeach
			 	</tbody>
			</table>
			@else
			This parcel doesn't have any recapture at this time.
			@endif
		</div>
		<hr />
	</div>

</div>
