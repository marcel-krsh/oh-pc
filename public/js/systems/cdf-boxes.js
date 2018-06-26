function checkSendVia(){
			if($('#send-via').val() == 'Email-other'){
				$('#email-other-field').slideDown();
			} else {
				$('#email-other-field').slideUp();
			}	
		}
		
function importCDFRecord(cdfDocumentId){
	UIkit.modal.confirm('Are you sure that you want to import all the records for this CDF document?<br/><br/><strong>THIS CANNOT BE UNDONE!</strong>').then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have imported the CDF record(s). I will now refresh the CDF Inbox.');
        	// reload the tab following submission
        	loadListTab(11,null,null,null,1);
		});
	});
}

function sendCDF(cdfDocumentId){
	UIkit.modal.confirm('Are you sure that you want to send all the records for this CDF document?<br/><br/><strong>THIS CANNOT BE UNDONE!</strong>').then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have sent the CDF record(s). I will now refresh the CDF Outbox.');
        	// reload the tab following submission
        	loadListTab(12,null,null,null,1);
		});
	});
}

function cdfFileUploadModalUpdate(cdfDocumentId){
	window.cdfDocumentIdToUpdate = cdfDocumentId;
	dynamicModalLoad('updated-cdf-file-upload');
	
}

function cdfFileUploadModal(){
	
	dynamicModalLoad('cdf-file-upload');
	
}

function makeActiveCDFVersion(cdfDocumentId,versionNumber){
	UIkit.modal.confirm('Are you sure that you want to set Version '+versionNumber+' to be the active version for this CDF document?').then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	UIkit.modal.alert('Success! I made Version '+ versionNumber +' the current version.');
        	// reload the tab following submission
        	loadListTab(11,null,null,null,1);
		});
	});
}

function cdfDateAgingFilter() {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(window.tabId,null,null,null,1,$('#date-aging-from').val(),$('#date-aging-through').val());	
}
function cdfDateRangeFilter() {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(12,null,null,null,1,$('#date-range-from').val(),$('#date-range-through').val());	
}

function cdfDateImportedFilter() {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(tabId,null,null,null,1,$('#date-imported-from').val(),$('#date-imported-through').val());	
}

function cdfDateExpireFilter(tabId) {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(window.tabId,null,null,null,1,null,null,null,$('#date-expire-from').val(),$('#date-expire-through').val());	
}


