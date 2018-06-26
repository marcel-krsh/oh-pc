function ignoreRecord(recordId){
	if($('#ignore-cdf-record-'+recordId).val() == "false"){		
		$('#record-'+recordId).addClass('ignore');
		$('#ignore-cdf-record-'+recordId).val("true");
			
			
	}else{
		$('#record-'+recordId).removeClass('ignore');
		$('#ignore-cdf-record-'+recordId).val("false");
			
		}
		
	
}
function openFileOnly(source,id){
	window.open('/external-window/'+source+'-'+id+'.html',"File "+id);
}

function getBatch(timeStamp){
	UIkit.modal.confirm('Are you sure that you want to save all the records for this Batch Report?<br/><br/><strong>THIS CANNOT BE UNDONE!</strong>').then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	UIkit.modal.alert('Success! I have saved the Batch report. I will now take you to the previous batch reports page where you can download it.');
        	// reload the tab following submission
        	loadListTab(101,null,null,null,1);
		});
	});
}