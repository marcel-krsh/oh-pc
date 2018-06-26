// JavaScript Document
var sentListTemplate = $('#sent-list-template').html();

var documentListTemplate = $('#document-list-template').html();

function selectCategory(categoryId,sentId){
	// fix the lack of closing of the drop list by clicking on the original link.
	var target = "#sent-id-"+sentId+"-category-id-"+ categoryId;
	//$(target).click();
	 
}

function loadDocuments(fileId){
	console.log('Loading in sent documents for file '+fileId);
	 $.ajax({
        type: "GET",
        //Url to the JSON-file
        url: "/documents/"+fileId+".json",
        dataType: "json",
        success: listDocuments
       
    });
}

function loadCategories(fileId){
	console.log('Loading in categories for file '+fileId);
	 $.ajax({
        type: "GET",
        //Url to the JSON-file
        url: "/categories/"+fileId+".json",
        dataType: "json",
        success: listCategories
       
    });
}

function listCategories(categories){
	console.log('Successfully loaded in the category lists. Beginning to process them for output to the DOM.')
	// Process the category items ///
	var cat = 0;
	var outputCategoryListTemplate = "";
	$(window.categoryListTarget).html(' ');
	console.log(categories[0].relatedCategories.length);
	while(categories[0].relatedCategories.length > (cat + 1)){
		console.log('Processing Category List Related Categories id '+ categories[0].relatedCategories[cat]['categoryId']);
		outputCategoryListTemplate += $('#category-list-template').html();
		// output the document list
		outputCategoryListTemplate = outputCategoryListTemplate.replace(/{{listPlace}}/g, window.categoryListPlace);
		outputCategoryListTemplate = outputCategoryListTemplate.replace(/{{categoryId}}/g, categories[0].relatedCategories[cat]['categoryId']);
		outputCategoryListTemplate = outputCategoryListTemplate.replace(/{{categoryName}}/g, categories[0].relatedCategories[cat]['categoryName']);
		console.log('Processesed '+categories[0].relatedCategories[cat]['categoryName']+" at cat number "+cat)
		cat++;
		}
		// add in divider
		outputCategoryListTemplate += '<div class="uk-width-1-1 uk-margin-top"><small>OTHER CATEGORIES THAT ARE PROBABLY NOT NEEDED</small><hr class="uk-margin-bottom"></div>';
	// reset cat for unrelated categories
		cat = 0;
		while(categories[1].unrelatedCategories.length > (cat + 1)){
		console.log('Processing Unrelated Category List Related Categories id '+ categories[1].unrelatedCategories[cat]['categoryId']);
		outputCategoryListTemplate += $('#category-list-template').html();
		// output the document list
		outputCategoryListTemplate = outputCategoryListTemplate.replace(/{{categoryId}}/g, categories[1].unrelatedCategories[cat]['categoryId']);
		outputCategoryListTemplate = outputCategoryListTemplate.replace(/{{categoryName}}/g, categories[1].unrelatedCategories[cat]['categoryName']);
		console.log('Processesed '+categories[1].unrelatedCategories[cat]['categoryName']+" at cat number "+cat)
		cat++;
		}
		
		$(window.categoryListTarget).append(outputCategoryListTemplate);
		if(window.selectTheseCategories.length > 0){
			console.log('Selecting the categories');
			selectCategory(window.selectTheseCategories);
			window.selectTheseCategories = "";
		}		
}

function listDocuments(docs){
	console.log('Successfully loaded in the document lists. Beginning to process them for output to the DOM.')
	window.documents = docs;
	window.outputSentListTemplate = $('#sent-list-template').html();
	window.outputDocumentListTemplate = $('#document-list-template').html();

	// Blank out section for refreshes
	$('#document-category-filters').html(" ");
	$('#document-applicant-filters').html(" ");
	// Process the uploaded items ///
	var doc = 0;
	var filterCategoryIds = ['0'];
	var filterApplicantIds = ['0'];
	for(;docs[0].documentList[doc];doc++){
		c = docs[0].documentList[doc];
		console.log('Processing document id '+c.documentId);
		/// reset the template
		outputDocumentListTemplate = $('#document-list-template').html();
		// output the document list
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{documentId}}/g, c.documentId);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{dateTimeOfUpload}}/g, c.dateTimeOfUpload);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{staffId}}/g, c.staffId);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{staffName}}/g, c.staffName);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{staffBadgeColor}}/g, c.staffBadgeColor);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{docuemtnFileId}}/g, c.documentFileId);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{documentType}}/g, c.documentType);
		var theCategoryList = "";
		var theCategoryClasses ="";
		var theCategoryListComma = 0;
		var theCategoryFilters = "";
		var theApplicantFilters = "";
		for(var cat in c.attachmentCategory){
			if(theCategoryListComma == 0){
				theCategoryListComma = 1;
			} else {
				// add in comma after each - this way we do not end with a trailing comma.
				theCategoryList += ", ";
			}
			theCategoryList += "<span class=\"applicant-id-"+c.attachmentCategory[cat].categoryApplicantId+" category-id-"+c.attachmentCategory[cat].categoryId+"\">"+c.attachmentCategory[cat].categoryApplicantName+": "+c.attachmentCategory[cat].categoryName+"</span>";
			theCategoryClasses += " category-id-"+c.attachmentCategory[cat].categoryId;
			/////////// BUILD THE FILTERS ARRAY /////////////////
			if($.inArray(c.attachmentCategory[cat].categoryId, filterCategoryIds)< 0) {
				// Preventing more than one to be added //
				theCategoryFilters += "<button class=\"uk-button uk-button-default uk-button-small uk-margin-small-right category-id-"+c.attachmentCategory[cat].categoryId+"\" data-uk-toggle=\"{target:'.category-id-"+c.attachmentCategory[cat].categoryId+"',cls:'category-highlight'}\">"+c.attachmentCategory[cat].categoryName+"</button>";
				filterCategoryIds.push(c.attachmentCategory[cat].categoryId);
			}
			if($.inArray(c.attachmentCategory[cat].categoryApplicantId, filterApplicantIds)< 0) {
				// Preventing more than one to be added //
				theApplicantFilters += "<button class=\"uk-button uk-button-default uk-button-small uk-margin-small-right applicant-id-"+c.attachmentCategory[cat].categoryApplicantId+"\" data-uk-toggle=\"{target:'.applicant-id-"+c.attachmentCategory[cat].categoryApplicantId+"',cls:'applicant-highlight'}\">"+c.attachmentCategory[cat].categoryApplicantName+"</button>";
				filterApplicantIds.push(c.attachmentCategory[cat].categoryApplicantId);
			}
		}
		
		$('#document-category-filters').append(theCategoryFilters);
		$('#document-applicant-filters').append(theApplicantFilters);
		
		
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{categories}}/g, theCategoryList);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{catClasses}}/g, theCategoryClasses);
		console.log('Category list for index ' + i + " should be "+theCategoryList);
		
		$('#document-list').append(outputDocumentListTemplate);
		
	}
	
	/// Process the items sent and to be sent that will need to be uploaded.
	doc = 0;
	for(;docs[1].documentsToUpload[doc];doc++){
		c = docs[1].documentsToUpload[doc];
		console.log('Processing requested document id '+c.method);
		/// reset the template
		var outputDocumentListTemplate = $('#sent-document-list-template').html();
		// output the document list
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{method}}/g, c.method);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{dateSent}}/g, c.dateSent);
		var theCategoryIds = "";
		var theCategoryList = "";
		var theCategoryClasses ="";
		var theCategoryFilters = "";
		var theApplicantFilters = "";
		for(var cat in c.attachmentCategory){
			theCategoryIds += c.attachmentCategory[cat].categoryId+",";
			theCategoryList += "<li data-uk-dropdown=\"{mode:'click'}\"><a id=\"sent-id-"+c.sentId+"-category-id-"+ c.attachmentCategory[cat].categoryId +"\" class=\"\"><i id=\"sent-id-"+c.sentId+"-category-id-"+ c.attachmentCategory[cat].categoryId +"-recieved-icon\" class=\"uk-icon-check-circle-o received-"+c.attachmentCategory[cat].received +"\"></i> <i id=\"sent-id-"+c.sentId+"-category-id-"+ c.attachmentCategory[cat].categoryId +"\-not-received-icon\" class=\"uk-icon-circle-o received-"+c.attachmentCategory[cat].received +"\"></i> " + c.attachmentCategory[cat].categoryApplicantName +": " + c.attachmentCategory[cat].categoryName + "</a><div class=\"uk-dropdown uk-dropdown-small\"><ul class=\"uk-nav uk-nav-dropdown\">";
			// determine sub list
			if(c.attachmentCategory[cat].received == "no"){
					theCategoryList += "<li><a onclick=\"resetDocTabCategoryListVars();selectCategory('" + c.attachmentCategory[cat].categoryId + "','"+c.sentId+"');\">Select this category on right</a></li><li><a onClick=\"markReceived('"+ c.attachmentCategory[cat].categoryId + "','"+ c.sentId + "');\">Mark as received</a><li></ul></div></li>";		
			}else{
				theCategoryList += "<li><a onclick=\"\selectCategory('"+ c.attachmentCategory[cat].categoryId + "');\">Select this category</a></li><li><a onClick=\"newEmailRequest('" + c.attachmentCategory[cat].categoryId + "');\">Request another document for this category</a><li><a onClick=\"unmarkReceived('"+ c.attachmentCategory[cat].categoryId + "','"+ c.sentId + "');\">Unmark as received</a><li></ul></div></li>";	
				
			}
			
			
		}
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{categories}}/g, theCategoryList);
		outputDocumentListTemplate = outputDocumentListTemplate.replace(/{{categoryIds}}/g, theCategoryIds);
		$('#sent-document-list').append(outputDocumentListTemplate);
		
	}

	
	
}

	
	
	
function deleteDocument(documentId) {
	UIkit.modal.confirm("Are you sure you want to delete this document from the file?").then(function() {
		UIkit.modal.prompt("Please comment on why you are deleting the file.",'',function(val){
					
        	UIkit.modal.alert('Success! I have removed the file. I will now refresh the tab to update list.');
        	// reload the tab following submission
        	loadSubTab('documents', window.currentDetailId);
        	dynamicModalClose();
		});
	});
	
}
	
function markApproved(categoryId,sentId){
	UIkit.modal.confirm("Are you sure you want to mark this document category as approved?").then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	// put in ajax request here?
        	
        	// reload the documents section of the tab
			loadDocuments(currentDetailId);
        	
        	});
	});
	
}

function unmarkApproved(categoryId,sentId){
	UIkit.modal.confirm("Are you sure you want to unmark this document category as approved?").then(function() {
		UIkit.modal.prompt("Please add your comment for the history record.",'',function(val){
					
        	// put in ajax request here?
        	// reload the documents section of the tab
			loadDocuments(currentDetailId);        	        	
        	});
	});
	
}
	
function	selectCategory(cats,sentId){
	console.log("Selecting the following category id(s):"+ cats)
	var categoriesToSelect = cats.split(",");
	var c = 0;
	while(categoriesToSelect.length > c){
		console.log('Selecting Category Id #category-id-'+categoriesToSelect[c]);
		//$('#category-id-'+categoriesToSelect[c]+'-'+window.categoryListPlace).attr('checked',true);
		$('#category-id-'+categoriesToSelect[c]).attr('checked',true);
		c++
	}
	UIkit.modal.alert('I made the selection for you on the right.');
	
}

function resetDocTabCategoryListVars (){
	$("input:checkbox[name=category-id-checkbox]:checked").removeAttr('checked');
	// window.categoryListTarget = "#category-list"
	// window.categoryListPlace = "doc-tab";

}


