<div id="dynamic-modal-content">
	<script>
	$('#modal-size').css('width', '60%');
	function programFormUpdate(program){
		if(program != ''){
			$('#credit-types').slideDown();
			$('#credit-type').val('0');
			$('#credit-type-span').html('Select Credit Situation');
			$('.initially-hidden').slideUp()
		}else{
			$('#credit-types').slideUp();
			$('.initially-hidden').slideUp(); 
			$('#credit-type').val('0');
			$('#credit-type-span').html('Select Credit Situation');
		}
	}
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>Overpayment Adjustment:: Reimbursement to HFA from Landbank</H3>
				<form id='transactionForm' class="uk-form-horizontal" role="form">
					<div class="uk-form-row">
                        <label class="uk-form-label" for="status_id">Landbank's Program</label>
                        <div class="uk-form-controls">
                            <div>
							    <select name="program_id" id="program" onChange="programFormUpdate(this.value)" class="uk-select">
	                                <option value="">Select a Program</option>
	                            	@foreach($programs as $program)
	                                <option value="{{$program->id}}" >{{$program->program_name}}</option>
									@endforeach
	                            </select>
	                        </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                    	function loadCreditOptions(optionType){
                    		var program = $('#program').val();
                    		$('#credit-options').load('/transactions/landbank-credit/options-'+optionType+'?program='+program);
                    		$('.initially-hidden').slideDown();
                    	}
                    </script>
                    <div class="uk-form-row uk-margin-top" id="credit-types" style="display:none;">
                        <label class="uk-form-label" for="credit_type">Credit Type</label>
                        <div class="uk-form-controls">
                            <div>
    							<span id="credit-type-span"></span>
							    <select name="credit-type" id="credit-type" onChange="if(this.value > 0) {$('#credit-options').fadeOut();$('#credit-options').html('');loadCreditOptions(this.value);$('#credit-options').fadeIn();}" class="uk-select">
							    	<option value="0">Select Credit Situation</option>
	                                <option value="1">Over Payment of Recapture Invoice from Landbank</option>
	                                <!-- <option value="2">Over Payment of Disposition Invoice Overpayment from Landbank</option>
	                                <option value="3">Credit a Overcompensated Parcel</option>
	                                <option value="4">Account Credit from a Non-specific Funds Return from Landbank</option> -->
	                            	
	                            </select>
	                        </div>
                        </div>
                    </div>
                    <div class="uk-form-row uk-margin-top initially-hidden" id="credit-options">
                    </div>

					<div class="uk-form-row initially-hidden" style="display: none;">
                        <label class="uk-form-label" for="amount">Amount</label>
                        <div class="uk-form-controls">
						    <input type="text" id="amount" name="amount" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value=""/>
                        </div>
                    </div>
					<div class="uk-form-row initially-hidden" style="display: none">
                        <label class="uk-form-label" for="created_at">Date Entered</label>
                        <div class="uk-form-controls">
						    <input type="text" id="date_entered" name="date_entered" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" style="width:80px;" value="" data-id="dateformat"/>
                        </div>
                    </div>
					<div class="uk-form-row initially-hidden" style="display: none">
                        <label class="uk-form-label" for="updated_at">Date Cleared</label>
                        <div class="uk-form-controls">
						    <input type="text" id="date_cleared" name="date_cleared" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" style="width:80px;" value="" data-id="dateformat"/>
                        </div>
                    </div>
                    <div class="uk-form-row initially-hidden" style="display: none">
                        <label class="uk-form-label" for="transaction_note">Transaction Note</label>
                        <div class="uk-form-controls">
                            
							<textarea id="transaction_note" name="transaction_note" class="uk-textarea uk-width-1-1"></textarea>
							
                        </div>
                    </div>
					<div class="uk-form-row initially-hidden" style="display: none">
                        <label class="uk-form-label" for="status_id">Status</label>
                        <div class="uk-form-controls">
                            <div>
    							<span></span>
							    <select name="status_id" id="status_id" class="uk-select">
	                                <option>Select a status</option>
	                            	@foreach($status_array as $status)
	                                <option value="{{$status['id']}}" >{{$status['name']}}</option>
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
					<div class="uk-width-1-3 uk-push-1-3  initially-hidden" style="display: none">
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
		    alert('Please enter an entered date.');
		}
		if($('#amount').val() == ''){
			no_alert = 0;
		    alert('Please enter an amount.');
		}
		if($('#program').val() == ''){
			no_alert = 0;
		    alert('Please select a program.');
		}
		if($('#status_id').val() == ''){
			no_alert = 0;
		    alert('Please select a status.');
		}

		if(no_alert){
			$.post('/transaction/create', {
				'inputs' : form.serialize(),
				'disposition' : 0,
				'funding_award' : 0,
				'funding_reduction' : 0,
				'balance_credit' : 0,
				'balance_debit' : 0,
				'landbank_credit' : 1,
				'invoice_id' : 0,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!='1'){ 
					UIkit.modal.alert(data);
				} else {
					UIkit.modal.alert('The transaction has been saved.');
					$('#dash-subtab-6').trigger('click');
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