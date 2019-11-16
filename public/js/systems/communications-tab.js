var currentCommunication ="";
var listTemplate = new String("<div class=\"uk-width-1-1 communication-list-item\" data-uk-filter=\"{{communicationDirection}}-{{communicationMethod}},staff-{{staffId}},attachment-{{attachment}}\" id=\"communication-{{communicationArrayIndex}}\" onclick=\"openCommunication({{communicationArrayIndex}});\"><div class=\"uk-grid\"><div class=\"uk-width-2-10 communication-type-and-who \"><!-- Type and Who --><span class=\"{{communicationMethod}}-communication\"><i uk-icon=\"caret-left\" class=\"{{inbound}}\"></i> <i class=\"uk-icon-justify\" uk-icon=\"{{communicationMethodIcon}}\"></i> <i uk-icon=\"caret-right\" class=\"{{outbound}}\"></i></span> <span data-uk-tooltip=\"{pos:\'top-left\'}\" title=\"{{staffName}}\"><div class=\"user-badge user-badge-communication-item user-badge-{{staffBadgeColor}} no-float\">{{staffInitials}}</div></span><span class=\" communication-item-date-time\">{{dateTimeOfCommunication}}</span></div><div class=\"uk-width-2-10 communication-item-tt-to-from\">{{communicationAcronym}}: {{userName}}</div><div class=\"uk-width-5-10 communication-item-excerpt\">{{communicationContent}}</div><div class=\"uk-width-1-10\"><div class=\"uk-align-right communication-item-attachment uk-margin-right show-{{attachment}}\"><a href=\"{{attachmentUrl}}\" target=\"_blank\" class=\"uk-link-muted\"><i uk-icon=\"icon: paperclip; ratio: 1\"></i></a></div></div></div></div>");

function loadSupportInfo(){
	
	 $.ajax({
        type: "GET",
    
        url: "/resources.json",
        dataType: "json",
        success: storeSupportInfo,
        error: function(xhr,error){
	        alert('Sorry, ran into a problem loading in the resources data: Transfer status '+xhr.status+" "+xhr.statusText+" \nError Reported: "+error);
        }
    });
}
function storeSupportInfo(loadedSupportInfo){
	////console.log('Converting support info to database');
	window.loadedSupportInfo = loadedSupportInfo;
	
}

function showSupportInfo(){
	
	
		
	supportInfo = TAFFY(window.loadedSupportInfo)
	supportInfo().callback(function(){
				
		window.supportInfoTableFront = '<table class="uk-table uk-table-hover uk-table-striped" style="color:black;font-weight:700"><thead><tr><th>Resource Name</th><th>Phone 1</th><th>Phone 2</th><th style="border-right: 1px solid #f3f3f3;">Website</th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Received Aid from HHF 2010 Funding" ><i uk-icon="life-saver" style="color:#39643e;">&nbsp;</i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Received Aid from HHF 2016 Funding" ><i uk-icon="life-saver" style="color:#284686;"></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Received Foreclosure Prevention" ><i uk-icon="umbrella"></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Mortgage" ><i uk-icon="newspaper-o" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Unemployment" ><i uk-icon="user-times" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Veteran" ><i uk-icon="shield" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Senior" ><i uk-icon="blind" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Medical Needs" ><i uk-icon="medkit" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Sheriff Sale" ><i uk-icon="star-half-empty" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="FHA" ><i uk-icon="building-o" ></i></span></th><th width="10" style="border-right: 1px solid #f3f3f3;"><span title="Foreclosure" ><i uk-icon="legal"></i></span></th></tr></thead><tbody class="uk-margin-top" data-uk-grid-margin>';
		
		window.supportRowTemplate = '<tr class="uk-width-1-4 uk-margin-large-bottom"><td>{{agencyName}}</td><td>{{agencyPhone}} </td><td>{{agencyAltPhone}}</td><td style="border-right:1px solid #fff;"><a href="{{agencyWebsite}}" target="_blank">{{agencyWebsite}}</a></td><td style="border-right:1px solid #fff;     background-color: rgba(0,0,0,.04);"><i title="Received Aid from HHF 2010 Funding" uk-icon="check" class=" gray-text check-received-aid-from-hhf-2010-funding display-{{hhf2010}}"></i></td><td style="border-right:1px solid #fff;"><i title="Received Aid from HHF 2016 Funding" uk-icon="check" class=" gray-text check-received-aid-from-hhf-2016-funding display-{{hhf2016}}"></i></td><td style="border-right:1px solid #fff;    background-color: rgba(0,0,0,.04);"><i title="Received Foreclosure Prevention" uk-icon="check" class=" gray-text check-received-foreclosure-prevention display-{{foreclosurePrevention}}"></i></td><td style="border-right:1px solid #fff;"><i title="Mortgage" uk-icon="check" class="gray-text check-mortgage display-{{mortgage}}"></i></td><td style="border-right:1px solid #fff;    background-color: rgba(0,0,0,.04);"><i title="Unemployment" uk-icon="check" class="gray-text check-unemployment display-{{unemployment}}"></i></td><td style="border-right:1px solid #fff;"><i title="Veteran" uk-icon="check" class="gray-text check-veteran display-{{veteran}}"></i></td><td style="border-right:1px solid #fff;    background-color: rgba(0,0,0,.04);"><i title="Senior" uk-icon="check" class="gray-text check-senior display-{{senior}}"></i></td><td style="border-right:1px solid #fff;"><i title="Medical Needs" uk-icon="check" class="gray-text check-medical-needs display-{{medicalNeeds}}"></i></td><td style="border-right:1px solid #fff;    background-color: rgba(0,0,0,.04);"><i  title="Sheriff Sale" uk-icon="check" class=" gray-text check-sheriff-sale display-{{sheriffSale}}"></i></td><td style="border-right:1px solid #fff;"><i  title="FHA" uk-icon="check" class="gray-text check-fha display-{{fha}}"></i></td><td style="border-right:1px solid #fff;    background-color: rgba(0,0,0,.04);"><i  title="Foreclosure" uk-icon="check" class="gray-text check-foreclosure display-{{foreclosure}}"></i></td></tr>';
		
		
		window.supportInfoTableBack = '</tbody></table>';
		
		var selectedCounty = $('#new-communication-parcel-county').val();
		////console.log('Selected county is '+selectedCounty);
		
		this.filter({County: selectedCounty}).each(function(record,recordnumber) {
			
			var supportInfoTemplate = window.supportRowTemplate; 
			supportInfoTemplate = supportInfoTemplate.replace(/{{agencyName}}/g, record['ResourceName']);
			if(record['ResourcePhone1'] != undefined){
				record['ResourcePhone1'] = record['ResourcePhone1'].replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
			}
			if(record['ResourcePhone2'] != undefined){
				record['ResourcePhone2'] = record['ResourcePhone1'].replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
			}
			supportInfoTemplate = supportInfoTemplate.replace(/{{agencyPhone}}/g, record['ResourcePhone1']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{agencyAltPhone}}/g, record['ResourcePhone2']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{agencyWebsite}}/g, record['ResourceWebsite']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{agencyLocation}}/g, record['ResourceLocation']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{hhf2010}}/g, record['ReceivedAidFromHhf2010']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{hhf2016}}/g, record['ReceivedAidFromHhf2016']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{foreclosurePrevention}}/g, record['ReceivedForcelosurePrevention']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{mortgage}}/g, record['Mortgage']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{unemployment}}/g, record['Unemployment']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{veteran}}/g, record['Veteran']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{senior}}/g, record['Senior']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{medicalNeeds}}/g, record['MedicalNeeds']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{sheriffSale}}/g, record['SheriffSale']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{fha}}/g, record['FHA']);
			supportInfoTemplate = supportInfoTemplate.replace(/{{foreclosure}}/g, record['Foreclosure']);
			//supportInfoTemplate = supportInfoTemplate.replace(/undefined/g, '');
			window.supportInfoRows += supportInfoTemplate
			});
		// output the list:
		
	var output = window.supportInfoTableFront+supportInfoRows+window.supportInfoTableBack;
	output = output.replace(/undefined/g, '');
	//clear previous output:
	
	$('#support-info-list').html(output);
	window.supportInfoRows = '';
	//apply check marks
	 checkCondition('#received-aid-from-hhf-2010-funding');
     checkCondition('#received-aid-from-hhf-2016-funding');
     checkCondition('#received-foreclosure-prevention');
     checkCondition('#mortgage');
     checkCondition('#unemployment');
     checkCondition('#veteran');
     checkCondition('#senior');
     checkCondition('#medical-needs');
     checkCondition('#sheriff-sale');
     checkCondition('#fha');
     checkCondition('#foreclosure');
	});
	
}

function loadCommunications(fileId){
	////console.log('Loading in communications for file '+fileId);
	 $.ajax({
        type: "GET",
        //Url to the json-file
        url: "/communications/"+fileId+".json",
        dataType: "json",
        success: listCommunications
    });
}

function listCommunications(comms){
	window.communications = comms;
	window.filterStaffIds = [0];
	comms.forEach(printCommunicationsList);	
}

function printCommunicationsList(c, i, a){
	// set the template:
	outputTemplate = listTemplate;
	staffFilterTemplate = $('#filter-by-staff-template').html();
	outputTemplate = outputTemplate.replace(/{{communicationArrayIndex}}/g, i);
	outputTemplate = outputTemplate.replace(/{{communicationId}}/g, c.communicationId);
	
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
	if(c.communicationMethod == "email"){
		outputTemplate = outputTemplate.replace(/{{communicationMethodIcon}}/g, 'envelope');
	}else{
		outputTemplate = outputTemplate.replace(/{{communicationMethodIcon}}/g, c.communicationMethod+'-square');
	}
	outputTemplate = outputTemplate.replace(/{{communicationMethod}}/g, c.communicationMethod);
	outputTemplate = outputTemplate.replace(/{{communicationDirection}}/g, c.communicationDirection);
	if(c.communicationDirection == "inbound"){
		outputTemplate = outputTemplate.replace(/{{outbound}}/g, "uk-dark");
		outputTemplate = outputTemplate.replace(/{{inbound}}/g, "");
	}else{
		outputTemplate = outputTemplate.replace(/{{inbound}}/g, "uk-dark");
		outputTemplate = outputTemplate.replace(/{{outbound}}/g, "");
	}
	
	outputTemplate = outputTemplate.replace(/{{staffName}}/g, c.staffName);
	outputTemplate = outputTemplate.replace(/{{staffInitials}}/g, c.staffInitials);
	outputTemplate = outputTemplate.replace(/{{staffId}}/g, c.staffId);
	outputTemplate = outputTemplate.replace(/{{staffBadgeColor}}/g, c.staffBadgeColor);
	outputTemplate = outputTemplate.replace(/{{dateTimeOfCommunication}}/g, c.dateTimeOfCommunication);
	outputTemplate = outputTemplate.replace(/{{communicationAcronym}}/g, c.communicationAcronym);
	outputTemplate = outputTemplate.replace(/{{userName}}/g, c.userName);
	outputTemplate = outputTemplate.replace(/{{attachment}}/g, c.attachment);
	outputTemplate = outputTemplate.replace(/{{attachmentUrl}}/g, c.attachmentUrl);
	outputTemplate = outputTemplate.replace(/{{communicationContent}}/g, c.communicationContent.substring(0,405)+"...");
	
	////console.log("Updated the communication list with communication-id: "+c.communicationId+" at array index: "+i);
	$('#communication-list').append(outputTemplate);
	
}

function loadNewCommunication(){
	//load in the applicant info update template
	 $.ajax({
        type: "GET",
        //Url to the html-file
        url: "/communications/applicantInfo"+window.currentDetailId+".json",
        dataType: "json",
        success: updateApplicantInfoForm
    });
	
}

function updateApplicantInfoForm(applicantInfo){
			updateTemplate = $('#applicant-info-update-form-template').html();
	       // Update File Id Reference
	       $('#current-file-id-dynamic-modal').html(window.currentDetailId);
	       // Update Form Elements with Current Applicant Info
	       
	       // set c = to the selected communication held in memory.
		var c = applicantInfo[0];
		////console.log('Updating the update applicant information form to show the current applicant information from /communications/appplicantInfo'+window.currentDetailId+'.json');
		updateTemplate = updateTemplate.replace(/{{userName}}/g, c.applicantFirstName+" "+ c.applicantLastName);		
		updateTemplate = updateTemplate.replace(/{{bestTimeToCall}}/g, c.bestTimeToCall);
		updateTemplate = updateTemplate.replace(/{{applicantPhone}}/g, c.applicantPhone);
		updateTemplate = updateTemplate.replace(/{{applicantAltPhone}}/g, c.applicantAltPhone);
		updateTemplate = updateTemplate.replace(/{{applicantEmail}}/g, c.applicantEmail);
		updateTemplate = updateTemplate.replace(/{{parcelStreetNumber}}/g, c.parcelStreetNumber);
		updateTemplate = updateTemplate.replace(/{{parcelStreetName}}/g, c.parcelStreetName);
		updateTemplate = updateTemplate.replace(/{{parcelStreetType}}/g, c.parcelStreetType);
		updateTemplate = updateTemplate.replace(/{{parcelAddress2}}/g, c.parcelAddress2);
		updateTemplate = updateTemplate.replace(/{{parcelCity}}/g, c.parcelCity);
		updateTemplate = updateTemplate.replace(/{{parcelState}}/g, c.parcelState);
		updateTemplate = updateTemplate.replace(/{{parcelZip}}/g, c.parcelZip);
		updateTemplate = updateTemplate.replace(/{{parcelCounty}}/g, c.parcelCounty);
		updateTemplate = updateTemplate.replace(/{{mailingStreetNumber}}/g, c.mailingStreetNumber);
		updateTemplate = updateTemplate.replace(/{{mailingStreetName}}/g, c.mailingStreetName);
		updateTemplate = updateTemplate.replace(/{{mailingStreetType}}/g, c.mailingStreetType);
		updateTemplate = updateTemplate.replace(/{{mailingAddress2}}/g, c.mailingAddress2);
		updateTemplate = updateTemplate.replace(/{{mailingCity}}/g, c.mailingCity);
		updateTemplate = updateTemplate.replace(/{{mailingState}}/g, c.mailingState);
		updateTemplate = updateTemplate.replace(/{{mailingZip}}/g, c.mailingZip);
		updateTemplate = updateTemplate.replace(/{{mailingCounty}}/g, c.mailingCounty);
		updateTemplate = updateTemplate.replace(/{{agencyName}}/g, c.agencyName);
		updateTemplate = updateTemplate.replace(/{{agencyPhone}}/g, c.agencyPhone);
		updateTemplate = updateTemplate.replace(/{{agencyAddress}}/g, c.agencyAddress);
		updateTemplate = updateTemplate.replace(/{{agencyAddress2}}/g, c.agencyAddress2);
		updateTemplate = updateTemplate.replace(/{{agencyCity}}/g, c.agencyCity);
		updateTemplate = updateTemplate.replace(/{{agencyState}}/g, c.agencyState);
		updateTemplate = updateTemplate.replace(/{{agencyZip}}/g, c.agencyZip);
		updateTemplate = updateTemplate.replace(/{{communicationFileId}}/g, c.communicationFileId);
		// condition fields
		updateTemplate = updateTemplate.replace(/{{conditionSenior}}/g, c.conditionSenior);
		updateTemplate = updateTemplate.replace(/{{conditionReceivedAidFromHHF2010Funding}}/g, c.conditionReceivedAidFromHHF2010Funding);
		updateTemplate = updateTemplate.replace(/{{conditionReceivedAidFromHHF2016Funding}}/g, c.conditionReceivedAidFromHHF2016Funding);
		updateTemplate = updateTemplate.replace(/{{conditionReceivedForeclosurePrevention}}/g, c.conditionReceivedForeclosurePrevention);
		updateTemplate = updateTemplate.replace(/{{conditionMortgage}}/g, c.conditionMortgage);
		updateTemplate = updateTemplate.replace(/{{conditionUnemployment}}/g, c.conditionUnemployment);
		updateTemplate = updateTemplate.replace(/{{conditionVeteran}}/g, c.conditionVeteran);
		updateTemplate = updateTemplate.replace(/{{conditionMedicalNeeds}}/g, c.conditionMedicalNeeds);
		updateTemplate = updateTemplate.replace(/{{conditionSheriffSale}}/g, c.conditionSheriffSale);
		updateTemplate = updateTemplate.replace(/{{conditionFHA}}/g, c.conditionFHA);
		updateTemplate = updateTemplate.replace(/{{conditionForeclosure}}/g, c.conditionForeclosure);
		updateTemplate = updateTemplate.replace(/{{attachementCategory}}/g, c.attachementCategory);
	       
	       
	       $('#applicant-info-update').html(updateTemplate);
	       // show the support info for the selected county
	        showSupportInfo();
	       // highlight check marks
	       
	      
	       		       
		       
	       
	     // update other optional form fields on the page to current contact info:
	     $('#applicant-numbers').html('<option value="'+c.applicantPhone+'" selected>Primary Phone: '+c.applicantPhone+'</option><option value="'+c.applicantAltPhone+'">Alternate Phone: '+c.applicantAltPhone+'</option>');
	     $('#applicant-numbers-text').html('Primary Phone: '+c.applicantPhone);
	     $('#applicant-notifiers').html('<option value="'+c.applicantPhone+'" selected>Primary Phone: '+c.applicantPhone+'</option><option value="'+c.applicantAltPhone+'">Alternate Phone: '+c.applicantAltPhone+'</option><option value="'+c.applicantEmail+'">Email Address: '+c.applicantEmail+'</option>');
	     $('#applicant-notify-via-text').html('Primary Phone: '+c.applicantPhone);
	     
	     
	     
	       
	       
	        
}
function openCommunicationold(communicationId){
	////console.log('Requested to Open '+communicationId);
	if(("#communication-"+communicationId) != window.currentCommunication || window.openedCommunication != 1){
		closeOpenCommunication();
		// so we can restore the selected message back to the list item
		window.openedCommunication = 1;
		window.currentCommunication = "#communication-"+communicationId;
		window.restoreLastCommunicationItem = $(window.currentCommunication).html();
		
		// set c = to the selected communication held in memory.
		var c = communications[communicationId];
		// set selectedCommunicationContent to the communication content template.
		var selectedCommunicationContent = $('#communication-open-template').html();
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationDirection}}/g, c.communicationDirection);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationMethod}}/g, c.communicationMethod);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationFileId}}/g, c.communicationFileId);
		if(c.communicationMethod == "email"){
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationMethodIcon}}/g, 'envelope');
		}else{
			selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationMethodIcon}}/g, c.communicationMethod+'-square');
		}
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationMethod}}/g, c.communicationMethod);
		if(c.communicationDirection == "inbound"){
			selectedCommunicationContent = selectedCommunicationContent.replace(/{{outbound}}/g, "uk-dark");
			selectedCommunicationContent = selectedCommunicationContent.replace(/{{inbound}}/g, "");
		}else{
			selectedCommunicationContent = selectedCommunicationContent.replace(/{{inbound}}/g, "uk-dark");
			selectedCommunicationContent = selectedCommunicationContent.replace(/{{outbound}}/g, "");
		}
		
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{staffId}}/g, c.staffId);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{staffName}}/g, c.staffName);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{staffInitials}}/g, c.staffInitials);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{staffBadgeColor}}/g, c.staffBadgeColor);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{dateTimeOfCommunication}}/g, c.dateTimeOfCommunication);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationAcronym}}/g, c.communicationAcronym);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{userName}}/g, c.userName);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{attachment}}/g, c.attachment);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{attachmentUrl}}/g, c.attachmentUrl);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{userContactThrough}}/g, c.userContactThrough);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{userContactThroughInfo}}/g, c.userContactThroughInfo);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationContent}}/g, c.communicationContent);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{bestTimeToCall}}/g, c.bestTimeToCall);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{applicantPhone}}/g, c.applicantPhone);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{applicantAltPhone}}/g, c.applicantAltPhone);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{applicantEmail}}/g, c.applicantEmail);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelStreetNumber}}/g, c.parcelStreetNumber);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelStreetName}}/g, c.parcelStreetName);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelStreetType}}/g, c.parcelStreetType);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelAddress2}}/g, c.parcelAddress2);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelCity}}/g, c.parcelCity);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelState}}/g, c.parcelState);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelZip}}/g, c.parcelZip);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{parcelCounty}}/g, c.parcelCounty);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingStreetNumber}}/g, c.mailingStreetNumber);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingStreetName}}/g, c.mailingStreetName);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingStreetType}}/g, c.mailingStreetType);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingAddress2}}/g, c.mailingAddress2);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingCity}}/g, c.mailingCity);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingState}}/g, c.mailingState);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingZip}}/g, c.mailingZip);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{mailingCounty}}/g, c.mailingCounty);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyName}}/g, c.agencyName);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyPhone}}/g, c.agencyPhone);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyAddress}}/g, c.agencyAddress);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyAddress2}}/g, c.agencyAddress2);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyCity}}/g, c.agencyCity);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyState}}/g, c.agencyState);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{agencyZip}}/g, c.agencyZip);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{communicationFileId}}/g, c.communicationFileId);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionSenior}}/g, c.conditionSenior);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionReceivedAidFromHHF2010Funding}}/g, c.conditionReceivedAidFromHHF2010Funding);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionReceivedAidFromHHF2016Funding}}/g, c.conditionReceivedAidFromHHF2016Funding);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionReceivedForeclosurePrevention}}/g, c.conditionReceivedForeclosurePrevention);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionMortgage}}/g, c.conditionMortgage);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionUnemployment}}/g, c.conditionUnemployment);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionVeteran}}/g, c.conditionVeteran);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionMedicalNeeds}}/g, c.conditionMedicalNeeds);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionSheriffSale}}/g, c.conditionSheriffSale);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionFHA}}/g, c.conditionFHA);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{conditionForeclosure}}/g, c.conditionForeclosure);
		selectedCommunicationContent = selectedCommunicationContent.replace(/{{attachementCategory}}/g, c.attachementCategory);


		
		$(window.currentCommunication).html(selectedCommunicationContent);
		$(window.currentCommunication).addClass('normal-cursor');
		$(window.currentCommunication).addClass('communication-open');
		////console.log('Loaded in the requested communication to '+window.currentCommunication);
		} else {
			////console.log('Ignoring request - communication is already open, or was just closed.');
			if(resetOpenCommunicationId == 1){
				window.resetOpenCommunicationId = 0;
				window.currentCommunication = 0;
				window.openedCommunication = 0;
			}
		}
		
}
function historicAlert(){
	
	UIkit.modal.alert('Sorry, these fields are not editable. Instead, they reflect the information that was in the file at the time of this communication. <br /><br />If you need to update this information, it needs to be done by opening a new communication. Be sure to select the preset message for \'Applicant updated their contact information\'.');

}

function updateCondition(id){
	
	var thisId = "#"+$(id).attr('id');
	var thisClass = ""+$(id).attr('id');
	////console.log('Evaluating condition of '+thisId+'. It appears to be '+$(thisId +' input').val());
	if($(thisId +' input').val() == 'false'){
		////console.log('Updating condition '+thisId+' to be true');
		
		$(thisId +' input').val('true');
		$(thisId +' i:first-of-type').removeClass('check-box-false');
		$(thisId +' i:first-of-type').addClass('check-box-true');
		$(thisId +' i:last-of-type').removeClass('open-box-false');
		$(thisId +' i:last-of-type').addClass('open-box-true');
		$('.check-'+thisClass).addClass('green-text');
		$('.check-'+thisClass).removeClass('gray-text');
		
		
		
		
		
	} else {
		////console.log('Updating condition '+thisId+' to be false');

		$(thisId +' input').val('false');
		
		$(thisId +' i:first-of-type').addClass('check-box-false');
		$(thisId +' i:first-of-type').removeClass('check-box-true');
		$(thisId +' i:last-of-type').addClass('open-box-false');
		$(thisId +' i:last-of-type').removeClass('open-box-true');
		$('.check-'+thisClass).removeClass('green-text');
		$('.check-'+thisClass).addClass('gray-text');
		
		
	}
}

function checkCondition(id){
	
	var thisId = "#"+$(id).attr('id');
	var thisClass = ""+$(id).attr('id');
	////console.log('Evaluating condition of '+thisId+'. It appears to be '+$(thisId +' input').val());
	if($(thisId +' input').val() == 'true'){
		////console.log('Updating condition '+thisId+' to be true');
		
		$(thisId +' input').val('true');
		$(thisId +' i:first-of-type').removeClass('check-box-false');
		$(thisId +' i:first-of-type').addClass('check-box-true');
		$(thisId +' i:last-of-type').removeClass('open-box-false');
		$(thisId +' i:last-of-type').addClass('open-box-true');
		$('.check-'+thisClass).addClass('green-text');
		$('.check-'+thisClass).removeClass('gray-text');
		
		
		
		
		
	} else {
		////console.log('Updating condition '+thisId+' to be false');

		$(thisId +' input').val('false');
		
		$(thisId +' i:first-of-type').addClass('check-box-false');
		$(thisId +' i:first-of-type').removeClass('check-box-true');
		$(thisId +' i:last-of-type').addClass('open-box-false');
		$(thisId +' i:last-of-type').removeClass('open-box-true');
		$('.check-'+thisClass).removeClass('green-text');
		$('.check-'+thisClass).addClass('gray-text');
		
		
	}
}





function closeOpenCommunication(){
	if(window.openedCommunication == 1) {
			//restore the previous message item first.
			$(window.currentCommunication).html(window.restoreLastCommunicationItem);
			$(window.currentCommunication).removeClass('normal-cursor');
			$(window.currentCommunication).removeClass('communication-open');
			////console.log('Restoring previous communication list item');
			window.resetOpenCommunicationId = 1;
		} else {
			////console.log('openedCommunication != 1 - no comm is open to close');
		}
}

function updateLoginMethodWarning(method,id){
	UIkit.modal.confirm('<h2>ARE YOU SURE?</h2>Please note that changing their '+method+' can affect their ability to login to the system. <br /><br />Please double check that the person requesting the change is absolutely and positively the applicant for this file.').then(function() { enableField('#'+id);});
}

function splitCamelCase(string){
	return string
    // insert a dash before all caps
    .replace(/([A-Z])/g, '-$1')
    .replace(/(\d{4})/, "-$1")
    // lowercase it
    .replace(/([A-Z])/g, function(str){ return str.toLowerCase(); })
    
    
}

function camelize(str) {
	str = str.replace(/-/g," ");
  return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function(letter, index) {
    return index == 0 ? letter.toLowerCase() : letter.toUpperCase();
  }).replace(/\s+/g, '');
}

function openWindow(source){
	window.open('/external-window/'+source+'-'+window.currentDetailId+'.html',"File "+window.currentDetailId+": "+source);
}

function newEmailRequest(categories){
	
	////console.log('Opening up new email request for documents');
	dynamicModalLoad('new-outbound-email-entry-documents');
	setTimeout(console.log(''), 1500);
	window.selectTheseCategories = categories;

	
}