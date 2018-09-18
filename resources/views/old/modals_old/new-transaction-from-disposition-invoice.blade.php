<div id="dynamic-modal-content">
	<script>
	$('#modal-size').css('width', '60%');
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>New Payment for Disposition Invoice #{{$invoice->id}}</H3>
				<form id='transactionForm' class="uk-form-horizontal" role="form">
					<div class="uk-form-row">
                        <label class="uk-form-label" for="amount">Amount</label>
                        <div class="uk-form-controls">
						    <input type="text" id="amount" name="amount" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value="{{$invoice->balance()}}"/>
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="created_at">Date Entered</label>
                        <div class="uk-form-controls">
						    <input type="text" id="date_entered" name="date_entered" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" style="width:80px;"  value="{{date('Y-m-d',time())}}" data-id="dateformat"/>
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="updated_at">Date Cleared</label>
                        <div class="uk-form-controls">
						    <input type="text" id="date_cleared" name="date_cleared" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" style="width:80px;" value="{{date('Y-m-d',time())}}" data-id="dateformat"/>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="transaction_note">Transaction Note</label>
                        <div class="uk-form-controls">
                            
							<textarea id="transaction_note" name="transaction_note" class="uk-textarea  uk-width-1-1"></textarea>
							
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="status_id">Status</label>
                        <div class="uk-form-controls">
                            <div>
    							<span></span>
							    <select name="status_id" id="status_id" class="uk-select">
	                                <option>Select a status</option>
	                            	@foreach($status_array as $status)
	                                <option value="{{$status['id']}}" @if($status['name'] == "Cleared") Selected @endIf>{{$status['name']}}</option>
									@endforeach
	                            </select>
	                        </div>
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
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <span uk-icon="times-circle" class="uk-margin-left"></span> CANCEL</a>
					</div>
					<div class="uk-width-1-3 uk-push-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="savetransaction()"> <i class="uk-margin-left"></i> SAVE &nbsp;</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">

	function savetransaction() {
		var form = $('#transactionForm');
		var no_alert = 1;

		if($('#date_entered').val() == ''){
			no_alert = 0;
		    alert('You must enter an entered date.');
		}
		if($('#amount').val() == ''){
			no_alert = 0;
		    alert('You must enter an amount.');
		}

		if(no_alert){
			$.post('/transaction/create', {
				'inputs' : form.serialize(),
				'disposition' : 1,
				'invoice_id' : {{ $invoice->id }},
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!='1'){ 
					UIkit.modal.alert(data);
				} else {
					UIkit.modal.alert('The transaction has been saved.');
					if(typeof window.accountingTabAddTransaction !== 'undefined'){
						$('#dash-subtab-6').trigger('click');
						console.log('clicking accounting tab to refresh');
					}else{
						location.reload();
						console.log('reloading page to refresh');
					}	
				}
			} );
			//location.reload();
			dynamicModalClose();
		}
		
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