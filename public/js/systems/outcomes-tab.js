function swapOutcome(programId){
		//hide all
		$('#'+programId+'-in-progress').slideUp();
		$('#'+programId+'-withdrawn').slideUp();
		$('#'+programId+'-declined').slideUp();
		$('#'+programId+'-closeout').slideUp();
		// show the selected one
		
		$('#'+programId+'-'+$('#program-'+programId+'-switcher').val()).slideDown();
		
	}
	
	function sendTRecord(){
	UIkit.modal.confirm("Are you sure you want to save the changes and send a T record?").then(function() {
		UIkit.modal.prompt("Please comment on why you are sending a T record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have saved the changes and added a T record for processing.');
        	// reload the tab following submission
		});
	});
	}
	function saveOutcomes(){
	UIkit.modal.confirm("Are you sure you want to only save the changes for all outcomes?").then(function() {
		UIkit.modal.prompt("Please comment on the changes you made.",'',function(val){
					
        	UIkit.modal.alert('Success! I have saved the changes.');
        	// reload the tab following submission
		});
	});
	}
	function markConfidential(){
	UIkit.modal.confirm("Are you sure you want to mark this file as confidential?").then(function() {
		UIkit.modal.prompt("Please comment on why you are marking this record confidential.",'',function(val){
					
        	UIkit.modal.alert('Success! I have marked this file as confidential and moved it to the confidential queue.');
        	// reload the tab following submission
        	$('#list-tab-icon').click();
        	$('#detail-subtabs-content').html('');
				//take back to top
			$('#smoothscrollLink').trigger("click");
			$('#detail-tab').hide();
		});
	});
}
	function printDeclineLetter(){
		openWindow('decline-letter');
	}
	
	function printWithdrawnLetter(){
		openWindow('withdrawn-letter');
	}
