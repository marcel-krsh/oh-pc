function deleteCDFRecord(RecordId){
	
	UIkit.modal.confirm("Are you sure you want to delete this record?").then(function() {
		UIkit.modal.prompt("Please comment on why you are deleting this record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have deleted this record from processing. I will now refresh the tab to update its list.');
        	// reload the tab following submission
        	loadSubTab('cdfs',window.currentDetailId);
		});
	});
}

function requestQRecord(){
	UIkit.modal.confirm("Are you sure you want to send a Q record?").then(function() {
		UIkit.modal.prompt("Please comment on why you are requesting a Q record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have added a Q record for processing. I will now refresh the tab to update its list.');
        	// reload the tab following submission
        	loadSubTab('cdfs',window.currentDetailId);
		});
	});
}