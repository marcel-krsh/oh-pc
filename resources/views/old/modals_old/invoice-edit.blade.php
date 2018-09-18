<div id="dynamic-modal-content">
	<script>
	resizeModal(60);
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>EDIT INVOICE {{$invoice->id}}</H3>
				<form id='invoiceForm' class="uk-form-horizontal" role="form">
					<input type="hidden" name="invoice" value="{{$invoice->id}}">
					<div class="uk-form-row">
                        <label class="uk-form-label" for="created_at">Created Date</label>
                        <div class="uk-form-controls">
                            @if($invoice->created_at === null)
						    <input type="text" id="created_at" name="created_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;" value=""/>
							@else
							<input type="text" id="created_at" name="created_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value="{{$invoice->created_at->format('Y-m-d')}}"/>
							@endif
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="updated_at">Updated Date</label>
                        <div class="uk-form-controls">
                            @if($invoice->updated_at === null)
						    <input type="text" id="updated_at" name="updated_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value=""/>
							@else
							<input type="text" id="updated_at" name="updated_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value="{{$invoice->updated_at->format('Y-m-d')}}"/>
							@endif
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="status_id">Status</label>
                        <div class="uk-form-controls">
                            <div class="uk-button uk-button-default uk-form-select" data-uk-form-select>
    							<span></span>
							    <select name="status_id" id="status_id" class="uk-select">
	                                <option>Select a status</option>
	                            	@foreach($statuses as $status)
	                                <option value="{{$status->id}}" @if($invoice->status_id == $status->id) selected @endif >{{$status->invoice_status_name}}</option>
									@endforeach
	                            </select>
	                        </div>
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="active">Active</label>
                        <div class="uk-form-controls">
                            <input type="checkbox" class="uk-checkbox" name="active" id="active" @if($invoice->active) checked @endif value="1">
                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div> 
	<hr>
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div id="applicant-info-update">
				<div class="uk-grid uk-margin">
					<div class="uk-width-1-3 uk-push-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <i uk-icon="times-circle" class="uk-margin-left"></i> CANCEL</a>
					</div>
					<div class="uk-width-1-3 uk-push-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="editInvoice({{$invoice->id}})"> <i class="uk-margin-left"></i> SAVE &nbsp;</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	function editInvoice(id) {
		var form = $('#invoiceForm');

		$.post('/modals/invoice/edit/{{ $invoice->id }}', {
			'inputs' : form.serialize(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!='1'){ 
				UIkit.modal.alert(data);
			} else {
				UIkit.modal.alert('The invoice has been saved.');
			}
		} );

		loadDashBoardSubTab('dashboard','invoice_list');
		dynamicModalClose();

		
	}	
	</script>
	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");

		var configs = {
		    dateformat: {
		        dateFormat: "m/d/Y",
		    }
		}
	</script>
</div>