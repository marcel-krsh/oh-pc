var currentHistory ="";
var historyListTemplate = new String("<div class=\"uk-width-1-1 history-list-item\" data-uk-filter=\"staff-!!staffId!!,attachment-!!attachment!!\" id=\"history-!!historyArrayIndex!!\" ><div class=\"uk-grid\"><div class=\"uk-width-1-4@l uk-width-1-3@m history-type-and-who \"><span data-uk-tooltip=\"{pos:\'top-left\'}\" title=\"!!staffName!!\" class=\"no-print\"><div class=\"user-badge user-badge-history-item user-badge-!!staffBadgeColor!! no-float\">!!staffInitials!!</div></span><span class=\"print-only\">!!staffName!!<br /></span><span class=\" history-item-date-time\">!!dateTimeOfHistory!!</span></div><div class=\"uk-width-3-4@l uk-width-4-6@m history-item-excerpt\">!!historyContent!!</div><div class=\"uk-width-1-6\"></div></div></div>");


function loadHistories(parcelId){
	console.log('Loading in histories for file '+parcelId);
	 $.ajax({
        type: "GET",
        //Url to the json-file
        //url: "/histories/"+parcelId+".json",
        url: "/histories/123456.json",
        dataType: "json",
        success: listHistories
    });
}

function listHistories(histories){
	window.histories = histories;
	window.filterStaffIds = [0];
	histories.forEach(printHistoriesList);	
}

function printHistoriesList(c, i, a){
	// set the template:
	outputTemplate = historyListTemplate;
	staffFilterTemplate = $('#filter-by-staff-template').html();
	outputTemplate = outputTemplate.replace(/!!historyArrayIndex!!/g, i);
	outputTemplate = outputTemplate.replace(/!!historyId!!/g, c.historyId);
	
	/////////// BUILD THE FILTERS ARRAY /////////////////
	if($.inArray(c.staffId, filterStaffIds)< 0) {
		// Preventing more than one to be added //
		var newStaff = staffFilterTemplate;
		newStaff = newStaff.replace(/!!staffName!!/g, c.staffName);
		newStaff = newStaff.replace(/!!staffId!!/g, c.staffId);
		newStaff = newStaff.replace(/!!staffInitials!!/g, c.staffInitials);
		newStaff = newStaff.replace(/!!badgeColor!!/g, c.staffBadgeColor)
		filterStaffIds.push(c.staffId);
		$('#message-filters').append(newStaff);

	}
	outputTemplate = outputTemplate.replace(/!!staffName!!/g, c.staffName);
	outputTemplate = outputTemplate.replace(/!!staffInitials!!/g, c.staffInitials);
	outputTemplate = outputTemplate.replace(/!!staffId!!/g, c.staffId);
	outputTemplate = outputTemplate.replace(/!!staffBadgeColor!!/g, c.staffBadgeColor);
	outputTemplate = outputTemplate.replace(/!!dateTimeOfHistory!!/g, c.dateTimeOfHistory);
	outputTemplate = outputTemplate.replace(/!!historyAcronym!!/g, c.historyAcronym);
	outputTemplate = outputTemplate.replace(/!!historyContent!!/g, c.historyContent);
	
	console.log("Updated the history list with history-id: "+c.historyId+" at array index: "+i);
	$('#history-list').append(outputTemplate);
	
}

function loadNewHistory(){
	//load in the applicant info update template
	 $.ajax({
        type: "GET",
        //Url to the html-file
        url: "/histories/applicantInfo"+window.currentDetailId+".json",
        dataType: "json",
        success: updateApplicantInfoForm
    });
	
}



