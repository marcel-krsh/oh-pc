<div id="dynamic-modal-content">
	<script>
	$('#modal-size').css('width', '60%');
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>New Recapture</H3>
				<p>You are requesting a recapture for the following item:</p>
				<form id='transactionForm' class="uk-form-horizontal" role="form">
					<div class="uk-form-row">
                        <label class="uk-form-label" for="category">Category</label>
                        <div class="uk-form-controls">
						    <input type="text" id="category" name="category" class="uk-input uk-form-small uk-form-width-small" style="width:160px;" value="{{$cost_item->expenseCategory->expense_category_name}}" disabled />
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="breakout_amount">Breakout Amount</label>
                        <div class="uk-form-controls">
						    <input type="text" id="breakout_amount" name="breakout_amount" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value="{{money_format('%n', $cost_item->amount)}}" disabled/>
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="amount">Amount</label>
                        <div class="uk-form-controls">
						    <input type="text" id="amount" name="amount" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value="{{money_format('%n', $cost_item->amount)}}"/>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="description">Description</label>
                        <div class="uk-form-controls">
                            
							<textarea id="description" name="description" class="uk-textarea  uk-width-1-1"></textarea>
							
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

		if($('#amount').val() == ''){
			no_alert = 0;
		    alert('Please enter an amount.');
		}

		if(no_alert){
			$.post('/recapture/create/{{$cost_item->id}}', {
				'inputs' : form.serialize(),
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data!='1'){ 
					UIkit.modal.alert(data);
				} else {
					UIkit.modal.alert('The recapture has been saved.');
					$('#recaptures-tab').trigger('click');
				}
			} );
			//location.reload();
			dynamicModalClose();
		}
		
	}	
	</script>
</div>