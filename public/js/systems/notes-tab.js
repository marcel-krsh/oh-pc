var currentNote ="";
var noteListTemplate = new String("<div class=\"uk-width-1-1 note-list-item\" data-uk-filter=\"staff-{{staffId}},attachment-{{attachment}}\" id=\"note-{{noteArrayIndex}}\" ><div class=\"uk-grid\"><div class=\"uk-width-1-4@l uk-width-1-3@m note-type-and-who \"><span data-uk-tooltip=\"{pos:\'top-left\'}\" title=\"{{staffName}}\" class=\"no-print\"><div class=\"user-badge user-badge-note-item user-badge-{{staffBadgeColor}} no-float\">{{staffInitials}}</div></span><span class=\"print-only\">{{staffName}}<br /></span><span class=\" note-item-date-time\">{{dateTimeOfNote}}</span></div><div class=\"uk-width-3-4@l uk-width-2-3@m note-item-excerpt\">{{noteContent}}</div><div class=\"uk-width-1-6\"></div></div></div>");



function loadNotes(parcelid){
	console.log('Loading in notes for parcel '+parcelid);
	 $.ajax({
        type: "GET",
        //Url to the json-file
        url: "/notes/parcel/"+parcelid+".json",
        dataType: "json",
        success: listNotes
    });
}

function listNotes(notes){
	window.notes = notes;
	window.filterStaffIds = [0];
	notes.forEach(printNotesList);	
}

function printNotesList(c, i, a){
	// set the template:
	outputTemplate = noteListTemplate;
	staffFilterTemplate = $('#filter-by-staff-template').html();
	outputTemplate = outputTemplate.replace(/{{noteArrayIndex}}/g, i);
	outputTemplate = outputTemplate.replace(/{{noteId}}/g, c.noteId);
	
	/////////// BUILD THE FILTERS ARRAY /////////////////
	if($.inArray(c.staffId, filterStaffIds)< 0) {
		// Preventing more than one to be added //
		var newStaff = staffFilterTemplate;
		newStaff = newStaff.replace(/{{staffName}}/g, c.staffName);
		newStaff = newStaff.replace(/{{staffId}}/g, c.staffId);
		newStaff = newStaff.replace(/{{staffInitials}}/g, c.staffInitials);
		newStaff = newStaff.replace(/{{badgeColor}}/g, c.staffBadgeColor)
		filterStaffIds.push(c.staffId);
		$('#message-filters').append(newStaff);

	}
	outputTemplate = outputTemplate.replace(/{{staffName}}/g, c.staffName);
	outputTemplate = outputTemplate.replace(/{{staffInitials}}/g, c.staffInitials);
	outputTemplate = outputTemplate.replace(/{{staffId}}/g, c.staffId);
	outputTemplate = outputTemplate.replace(/{{staffBadgeColor}}/g, c.staffBadgeColor);
	outputTemplate = outputTemplate.replace(/{{dateTimeOfNote}}/g, c.dateTimeOfNote);
	outputTemplate = outputTemplate.replace(/{{noteAcronym}}/g, c.noteAcronym);
	outputTemplate = outputTemplate.replace(/{{noteContent}}/g, c.noteContent);
	
	console.log("Updated the note list with note-id: "+c.noteId+" at array index: "+i);
	$('#note-list').append(outputTemplate);
	
}

function loadNewNote(){
	//load in the applicant info update template
	 $.ajax({
        type: "GET",
        //Url to the html-file
        url: "/notes/applicantInfo"+window.currentDetailId+".json",
        dataType: "json",
        success: updateApplicantInfoForm
    });
	
}


function submitNewNote() {
	
	// code to submit note -
	// by nature this note is it's history note - so no need to ask them for a comment.
	UIkit.modal.alert('I saved your note. I will refresh the notes screen now.');
	loadSubTab('notes',window.currentDetailId);
	dynamicModalClose();
	
}


