// JavaScript Document

function paymentNotificationType(type){
	// reset the classes //
	$('#send-ftp').removeClass('payment-notification-selected');
	$('#send-email').removeClass('payment-notification-selected');
	$('#do-not-send').removeClass('payment-notification-selected');
	$('#servicer-email').slideUp();
	$('#send-b-record-via').val(type);
	switch (type) {
		case 'ftp' :
		console.log('Send B Record VIA FTP');
		$('#send-ftp').addClass('payment-notification-selected');
		
		break;
		
		case 'email' :
		console.log('Send B Record VIA Email');
		$('#send-email').addClass('payment-notification-selected');
		$('#servicer-email').slideDown();;
		break;
		
		case 'none' :
		console.log('Do not send B Record');
		$('#do-not-send').addClass('payment-notification-selected');
		break;
	}
}


function updateBankServicer() {
	
	$('#bank-servicer-name').html('Updating...');

	console.log('lien position bankServicer id is :'+$('#lien-position').val());
	var id = $('#lien-position').val();
	var bank_data = {
		bankServicerId: id
	};
	$.ajax({
	    type: "POST", 
	    url: "/bank-servicer/bankServicer.json", 
	    data: bank_data,
	    success: function(response)
	        {
				console.log('BankServicer json file responded with :'+response[0]); 
				 var json_obj = response;
				 console.log('Json bankServicer: '+json_obj[0].bankServicerName);
				 console.log('bankServicer Preferred Method: '+json_obj[0].preferredMethod);
				 console.log('bankServicer Acronym: '+json_obj[0].acronym);	
				 console.log('bankServicer Email: '+json_obj[0].bankEmail);
				 console.log('bankServicer Account Number for File: '+json_obj[0].fileAccountNumber);
				 $('#bank-servicer-name').html(json_obj[0].bankServicerName+' ('+json_obj[0].acronym+') ');
				 $('#servicer-email-address').val(json_obj[0].bankEmail);
				 $('#send-b-record-via').val(json_obj[0].preferredMethod);
				// set payment notification type.
				 paymentNotificationType(json_obj[0].preferredMethod);
				 // update payment details based on the program selection and the most recent CDF file ID 
				 updateProgramSelection();
				 
			},
		dataType: "json"
	});
	
	  
	  
};

function updateProgramSelection() {
	

	console.log('Program selection set to :'+$('#program').val()+' - loading the selected payment info from current CDF Record for File ID:'+window.currentFileId );
	var id = $('#program').val();
	var bankId = $('#lien-position').val();
	var fileId = window.currentFileId;

	var program_data = {
		programId: id,
		bankId: bankId,
		fileId: fileId
	};
	
	// get the appropriate payment amount for this program
	// using directory structure to identify the file
	$.ajax({
	    type: "POST", 
	    url: "/cdf/"+window.currentFileId+"/"+$('#program').val()+".json", 
	    data: program_data,
	    success: function(response)
	        {
				 console.log('CDF json file responded with :'+response); 
				 var json_obj = response;
				 console.log('Setting glogals to check payment changes against');
				 console.log('Json amount: '+json_obj[0].amount);
				 console.log('Json redcordType: '+json_obj[0].recordType);
				 console.log('Json creditDebit: '+json_obj[0].creditDebit);
				 window.originalCreditDebit = json_obj[0].creditDebit;
				 $('#payment-amount').val(json_obj[0].amount);
				 window.originalPaymentAmount = json_obj[0].amount;
				 $('#payment-record-type-text').html('<a onclick="dynamicModalLoad(\''+json_obj[0].recordId+'\');" class="white-link">'+json_obj[0].recordType+'</a>');
				 window.originalRecordType = json_obj[0].recordType;
				 $('#payment-due-date').val(json_obj[0].paymentDueDate);
				 $('#payment-cdf-record-id').val(json_obj[0].recordId);
				 // set the current record to open onClick for the recrod type descriptor
				 window.currentCDFRecordId = json_obj[0].recordId;
				 
				 // disable escrow option if the program selected is NOT MPA
				 if($('#program').val() != "mpa"){
					 $('#escrow-reserve').prop("disabled",true);
					 // IF MPA HAS ZERO RESERVE FUNDS LEFT - CHECK THE BOX BY DEFUALT - OTHERWISE DO NOT INCLUDE THIS LINE
					 $('#escrow-reserve').prop("checked",false);
				 }else{
					 $('#escrow-reserve').prop("disabled",false);
					 $('#escrow-reserve').prop("checked",true);
				 }
		
				
				 // using js because of the issue with uikit with jquery updates to selects not showing on the dom.
				 var sel = document.getElementById('payment-type');
				 var opts = sel.options;
				 for (var opt, j = 0; opt = opts[j]; j++){
					 if(opt.value == json_obj[0].creditDebit) {
						 sel.selectedIndex = j;
						 console.log('selected payment type index '+j);
					 }
					 
				 }
				 // uikit advanced select does not recognize js changes to the select value. So we have to manually set the visual value.
				 if(json_obj[0].creditDebit == '-'){
					 $('#payment-type-text').html('&ndash; &nbsp; Credit (i.e. H/O payback) &nbsp;');
				 } else {
					 $('#payment-type-text').html('+ &nbsp; Debit (i.e. payments to bank/servicer) &nbsp; ');
				 }
				 if(json_obj[0].recordType == "[e]" || json_obj[0].recordType == "[p]") {
		
						// P and E records represent changes in payment amounts. 
						// This can mean pending payments need to be adjusted by the amount that is either a shortage, 
						// or the difference of the new payment amount vs the old payment amount.
						// in either case we need to show the optional add payment and adjust future payments button.
						$('#add-and-adjust').slideDown();
						$('#just-add-payment-modifier').show();
						updateAddAndAdjust();
						
						
					} else {
						$('#add-and-adjust').slideUp();
						$('#just-add-payment-modifier').hide();
					}
					
					
					
				 	
				 	window.originalRecordId = json_obj[0].recordId;			 
			},
		dataType: "json"
	});
	
	  
	  
};

function updateAddAndAdjust(){
	//updates the add and adjust button based on the current values for the amount and payment type.
	$('#add-and-adjust-credit-debit').html($('#payment-type').val());
	$('#add-and-adjust-amount').html($('#payment-amount').val());
	
}

function checkPaymentChanges(){
	var paymentChanged = 0;
	// see if the user has modified the payment details from the record's specified amounts etc.
	if($('#payment-type').val() != window.originalCreditDebit){
		console.log('User has modified payment type ('+$('#payment-type').val()+') to be different than the record: '+window.originalCreditDebit);
		$('#payment-record-type-text').html('<i class="uk-icon-user"></i>');
		$('#payment-source-id').val('user');
		$('#add-and-adjust').slideUp();
		$('#just-add-payment-modifier').hide();
		paymentChanged = 1;
		
	}
	if($('#payment-amount').val() != window.originalPaymentAmount){
		console.log('User has modified payment amount($'+$('#payment-amount').val()+') to be different than the record: '+window.originalPaymentAmount);
		$('#payment-record-type-text').html('<i class="uk-icon-user"></i>');
		$('#payment-source-id').val('user');
		$('#add-and-adjust').slideUp();
		$('#just-add-payment-modifier').hide();
		paymentChanged = 1;
	}
	if(paymentChanged == 0) {
		//rerun program selection
		updateProgramSelection();
		
	}
	
	/// we allow changes to method and account number.
	
}

function addPayments(){
	// submit payments and refresh the tab.
	UIkit.modal.prompt("Please comment on this/these payments for the history record.",'',function(val){
		$('#note').val(val);
		
        	UIkit.modal.alert('Dev Note : Remember serialize will not recognize disabled fields - gotta post manually.');
	});
}

function addAndAdjustPayments(){
	console.log('setting adjustFuturePayments value to 1');
	$('#adjust-future-payments').val(1);
	// submit payments and refresh the tab.
	UIkit.modal.prompt("Please comment on this/these payments for the history record.",'',function(val){
			// remember you have to specifically create the object when fields are disabled.
		
        	UIkit.modal.alert('Success! I have posted the payments for processing.');
        	loadSubTab('funding',window.currentFileId);
	});
}


function resendBRecord(bRecordId){
	// send request to resend record via ajax - respond success or failure via alert modal:
	
	UIkit.modal.prompt("Please comment on why you are resending this B record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have queued the b record for processing. You may want to contact the CDF manager if you need it processed outside the normal schedule.');
        	// reload the tab following submission
        	loadSubTab('funding',window.currentFileId);
	});
}


function deletePayment(bRecordId){
	
	UIkit.modal.confirm("Are you sure you want to delete this payment?").then(function() {
		UIkit.modal.prompt("Please comment on why you are deleting this payment/B record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have removed the payment from processing. I will now refresh the tab to update budgets.');
        	// reload the tab following submission
        	loadSubTab('funding',window.currentFileId);
		});
	});
}

function loadCDFRecord(){
	
	var cdfId = 'cdf-record-'+ document.getElementById('cdf-records-drop-list').value;
	dynamicModalLoad(cdfId,null,null);
}

function updatePayment() {
	UIkit.modal.confirm("Are you sure you want to change this payment?").then(function() {
		UIkit.modal.prompt("Please comment on why you are changing this payment/B record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have updated the payment for processing. I will now refresh the tab to update budgets.');
        	// reload the tab following submission
        	loadSubTab('funding',window.currentFileId);
        	dynamicModalClose();
		});
	});
	
}

function freeReservedFunds() {
	UIkit.modal.confirm("Are you sure you want to release the reserved funds back into the general fund pool?").then(function() {
		UIkit.modal.prompt("Please comment on why you are releasing the funds.",'',function(val){
					
        	UIkit.modal.alert('Success! I have released the reserved funds back into the general fund pool. Please note, the escrow reserved funds will not be released back into the general pool until the file is closed. I will now refresh the tab to update budgets.');
        	// reload the tab following submission
        	loadSubTab('funding',window.currentFileId);
        	dynamicModalClose();
		});
	});
	
}