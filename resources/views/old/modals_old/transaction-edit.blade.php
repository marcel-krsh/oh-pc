<div id="dynamic-modal-content">
	<script>
	$('#modal-size').css('width', '60%');
	</script>

		
	<div class="uk-container uk-container-center"> <!-- start form container -->
		<div class="uk-grid">
			<div class="uk-width-1-1@m uk-width-1-1@s">
				<H3>Edit Transaction {{$transaction->id}}</H3>
				<form id='transactionForm' class=" uk-form-horizontal" role="form">
					<input type="hidden" name="transaction" value="{{$transaction->id}}">
					<div class="uk-form-row">
                        <label class="uk-form-label" for="created_at">Date Entered</label>
                        <div class="uk-form-controls">
                            @if($transaction->date_entered === null || $transaction->date_entered == "0000-00-00")
						    <input type="text" id="created_at" name="created_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value=""/>
							@else
							<input type="text" id="date_cleared" name="created_at" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value="{{date("Y-m-d",strtotime($transaction->date_entered))}}"/>
							@endif
                        </div>
                    </div>
					<div class="uk-form-row">
                        <label class="uk-form-label" for="updated_at">Cleared Date</label>
                        <div class="uk-form-controls">
                            @if($transaction->date_cleared === null || $transaction->date_cleared == "0000-00-00")
						    <input type="text" id="date_cleared" name="date_cleared" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value=""/>
							@else
							<input type="text" id="date_cleared" name="date_cleared" class="uk-input uk-form-small uk-form-width-small flatpickr flatpickr-input active" data-id="dateformat" style="width:80px;"  value="{{date("Y-m-d",strtotime($transaction->date_cleared))}}"/>
							@endif
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="amount">Amount</label>
                        <div class="uk-form-controls">
						    <input type="text" id="amount" name="amount" class="uk-input uk-form-small uk-form-width-small" style="width:80px;" value="{{$transaction->amount}}"/>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="transaction_note">Transaction Note</label>
                        <div class="uk-form-controls">
                            
							<textarea id="transaction_note" name="transaction_note" class="uk-textarea uk-width-1-1">{{$transaction->transaction_note}}</textarea>
							
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
	                                <option value="{{$status['id']}}" @if($transaction->status_id == $status['id']) selected @endif >{{$status['name']}}</option>
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
					<div class="uk-width-1-3">
						<a class="uk-button  blue-button uk-button-danger uk-width-1-1" onclick="deleteTransaction();"> DELETE</a>
					</div>
					<div class="uk-width-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="dynamicModalClose();"> <span uk-icon="times-circle" class=" uk-margin-left"></span> CANCEL</a>
					</div>
					<div class="uk-width-1-3">
						<a class="uk-button uk-button-primary blue-button uk-width-1-1" onclick="edittransaction({{$transaction->id}})"> <i class="uk-margin-left"></i> SAVE &nbsp;</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">

	function deleteTransaction(){
		UIkit.modal.confirm("Are you sure you want to delete this transaction?").then(function() {
	        $.post('{{ URL::route("transaction.delete", [$transaction->id]) }}', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data['message']!='' && data['error']!=1){
					dynamicModalClose();
					@if($reload)
					location.reload();
					@else
					loadDashBoardSubTab('dashboard','accounting');
					@endif
				}else if(data['message']!='' && data['error']==1){
					UIkit.modal.alert(data['message']);
				}else{
					UIkit.modal.alert('Something went wrong.');
				}
			} );
			
	    });
	}

	function edittransaction(id) {
		var form = $('#transactionForm');

		$.post('/modals/transaction/edit/{{ $transaction->id }}', {
			'inputs' : form.serialize(),
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data!='1'){ 
				UIkit.modal.alert(data);
			} else {
				UIkit.modal.alert('The transaction has been saved.');
			}
		} );

		@if($reload)
		location.reload();
		@else
		loadDashBoardSubTab('dashboard','accounting');
		@endif
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