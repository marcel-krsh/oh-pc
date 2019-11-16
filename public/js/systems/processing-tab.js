function showApplicantQuickCopyButtons(){
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	for(i=1;i<totalApplicants;i++) {
		if($('#applicant-'+i+'-type').val() !== "Resident") {
			$('#applicant-'+i+'-quick-copy').show();
			$('#applicant-'+i+'-quick-copy-text').html($('#applicant-'+i+'-name').val());
			console.log('Showing quick copy button for applicant '+i+'.');
		} else {
			$('#applicant-'+i+'-quick-copy').hide();
			console.log('Hiding applicant '+i+' quick copy button');
		}
		//update labels.
		$('#applicant-'+i+'-type-text').attr('title',$('#applicant-'+i+'-type').val());
	}
	
}

$(document).on("change", '.format-my-number', function(){
	console.log('Formatting number for '+this);
	var formatIt = 	$(this).val();
	formatIt = stripCommas(formatIt);
	formatIt = numberWithCommas(formatIt.toFixed(2));
	$(this).val(formatIt);
	
});

$(document).on("change", 'select.applicantType', function(){
	console.log('Fired Type Change');
	if($(this).val() != "Resident") {
		
    	$(this).parents(".clonedInput").find('.applicantSSN').prop('placeholder', 'Enter SSN');
		console.log('Coborrower or spouse selected. Require SSN');
		$(this).parents(".clonedInput").find('.applicantSSN').prop('disabled', false);
		$(this).parents(".clonedInput").find('.applicantSSN').prop('readonly', false);
	} else if($(this).val() == "Resident") {
		console.log('Resident selected. Do not require SSN');
		$(this).parents(".clonedInput").find('.applicantSSN').prop('disabled', true);
		$(this).parents(".clonedInput").find('.applicantSSN').prop('readonly', true);
    	$(this).parents(".clonedInput").find('.applicantSSN').prop('placeholder', 'SSN Not Required');
    	if(parseInt(window.applicantId) > 0){
    		$(this).parents(".clonedInput").find('.applicantSSN').prop('ondblclick', 'alert(\'Cannot Retrieve SSN for This User Type.\')');
    	}
    	$(this).parents(".clonedInput").find('.applicantSSN').val('');
	} else {
		$(this).parents(".clonedInput").find('.applicantSSN').prop('disabled', true);
    	$(this).parents(".clonedInput").find('.applicantSSN').prop('placeholder', 'Select \'Type\' First');
	}
	
	
});



// Reset modal size on close event.
$('.modalSelector').on({
    'hide.uk.modal': function(){
	$('#modal-size').css('width','600px');
	console.log('Reset default modal width to 600px')
	}
});

// Use for modals that need to be wider than the preset.
function resizeModal (setSize) {
	
	$('#modal-size').css('width', setSize+'%');
	console.log('Resized Modal to '+setSize+'%')
}

function addApplicant(fileId) {
	/// put in ajax post
		console.log('addApplicant() called: Add in the ajax post of content.');
		
		/// check return data for errors
		
		/// use UIkit.modal.alert(''); for any errors.
		
		/// place bottom of script into condition that data is confirmed 
		
		dynamicModalClose();
		loadDetailTab(1,window.currentDetailId,null,1);
		console.log('Refreshing Detail Tab for '+window.currentDetailId+' to show updated applicants');
}

function editApplicant(applicantId){
	
	// set the global for when the modal loads
	window.applicantId = applicantId
	dynamicModalLoad('update-applicant')
	
}

function updateApplicantForm () {
	
	// update the fields in the form to match that of the window.applicantId
	var applicantId = window.applicantId;
	
	$('#first-name').val($('#'+applicantId+'-first-name').val());
	$('#last-name').val($('#'+applicantId+'-last-name').val());
	$('#ssn').val($('#'+applicantId+'-ssn').val());
	
	$('#race').val($('#'+applicantId+'-race').val());
	$('#ethnicity').val($('#'+applicantId+'-ethnicity').val());
	$('#sex').val($('#'+applicantId+'-sex').val());
	$('#type').val($('#'+applicantId+'-type').val());
	if($('#type').val() == "Resident"){
		$('#ssn').prop('placeholder', 'SSN Not Required');
	} else {
		$('#ssn').prop('placeholder', 'Enter SSN');
		
	}
	$('#birth-date').val($('#'+applicantId+'-birth-date').val());
	
}

function updateApplicant () {
	
	// update the fields in the form to match that of the window.applicantId
	var applicantId = window.applicantId;
	
	$('#'+applicantId+'-name').val($('#first-name').val()+ " "+ $('#last-name').val());
	$('#'+applicantId+'-first-name').val($('#first-name').val());
	$('#'+applicantId+'-last-name').val($('#last-name').val());
	$('#'+applicantId+'-ssn').val($('#ssn').val());
	$('#'+applicantId+'-race').val($('#race').val());
	$('#'+applicantId+'-ethnicity').val($('#ethnicity').val());
	$('#'+applicantId+'-sex').val($('#sex').val());
	$('#'+applicantId+'-type').val($('#type').val());
	$('#'+applicantId+'-birth-date').val($('#birth-date').val());
	window.saved = 0;
	//update the quick copy buttons
	showApplicantQuickCopyButtons();
	
	dynamicModalClose();
}


function editAddress(addressId){
	
	// set the global for when the modal loads
	window.addressId = addressId
	dynamicModalLoad('update-address')
	

}

function updateAddressForm () {
	
	// update the fields in the form to match that of the window.applicantId
	var addressId = window.addressId;
	
	$('#street-number').val($('#'+addressId+'-street-number').val());
	$('#street-name').val($('#'+addressId+'-street-name').val());
	$('#street-type').val($('#'+addressId+'-street-type').val());
	$('#address-2').val($('#'+addressId+'-address-2').val());
	$('#city').val($('#'+addressId+'-city').val());
	$('#state').val($('#'+addressId+'-state').val());
	$('#zip').val($('#'+addressId+'-zip').val());
}

function updateAddress () {
	
	// update the fields in the form to match that of the window.applicantId
	var addressId = window.addressId;
	
	if($('#address-2').val().length > 0) {
		$('#'+addressId+'-address').val($('#street-number').val()+ " "+ $('#street-name').val()+ " "+ $('#street-type').val()+", "+$('#address-2').val());
		} else {
		$('#'+addressId+'-address').val($('#street-number').val()+ " "+ $('#street-name').val()+ " "+ $('#street-type').val());
		}
	$('#'+addressId+'-city-state-zip').val($('#city').val()+", "+$('#state').val()+" "+$('#zip').val());
	$('#'+addressId+'-street-number').val($('#street-number').val());
	$('#'+addressId+'-street-name').val($('#street-name').val());
	$('#'+addressId+'-street-type').val($('#street-type').val());
	$('#'+addressId+'-address-2').val($('#address-2').val());
	$('#'+addressId+'-city').val($('#city').val());
	$('#'+addressId+'-state').val($('#state').val());
	$('#'+addressId+'-zip').val($('#zip').val());
	window.saved = 0;
	dynamicModalClose();
}

function stripCommas(theNumber){
	
	
	var parsed = parseFloat(theNumber.replace(/,/g,'').replace(/ /g,''));
	console.log('Stripping commas and spaces for: '+theNumber+' to be '+ parsed );
	return parsed;
	
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function calculateEligibility () {
	var mortgagePaymentTotal = 0;
	console.log('Calculating Eligibility');
	// Mortgage Payment Total
	mortgagePaymentTotal = mortgagePaymentTotal + stripCommas($('#principal-plus-interest').val());
	
	console.log(mortgagePaymentTotal);
			// for the purpose of eligibity we include property tax, insurance and hoa fees regardless of their escrow status.
	mortgagePaymentTotal = mortgagePaymentTotal + stripCommas($('#property-tax').val());
	
	console.log(mortgagePaymentTotal);
	mortgagePaymentTotal = mortgagePaymentTotal + stripCommas($('#property-insurance').val());
	
	console.log(mortgagePaymentTotal);
	mortgagePaymentTotal = mortgagePaymentTotal + stripCommas($('#hoa-misc').val());
	
	console.log('Added up PITIA for 1st mortgage (prior to formatting):'+mortgagePaymentTotal);
	$('#1st-pitia').val(numberWithCommas(mortgagePaymentTotal));
	
	// Monthly Income Totals
	
	// Set base vars
	this.applicant1TotalIncome = 0;
	this.applicant2TotalIncome = 0;
	this.applicant3TotalIncome = 0;
	this.applicant4TotalIncome = 0;
	this.applicant5TotalIncome = 0;
	this.applicant6TotalIncome = 0;
	this.applicant7TotalIncome = 0;
	this.applicant8TotalIncome = 0;
	this.coApplicantsTotalIncome = 0;
	this.totalGrossIncome = 0;
	mpa = 0;
	rpa = 0;
	this.mpaMonthlyPayments = 0;
	this.rpaPayments = 0;
	
	var amiBase = parseFloat(parseInt($('#borrower-ami-base').val()) / 12);
	// this is based on https://www.census.gov/quickfacts/table/INC110214/39041,39159,39 data - 
	// this reference must be kept SOMEWHERE for the purpose of audits. 
	// the ami number used for approving each record  needs to be stored with the record.
	
	
	// Calculate Applicant Totals Based on What is on the Processing Tab
	
	
	// Only process the total number of applicants - max is 8.
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	
	
	for(g=1; g<totalApplicants; g++){
		//reset vars
		var applicantPrimaryJobMonthlyIncomeTotal = 0;
		var applicantSecondaryJobMonthlyIncomeTotal = 0;
		var applicantThirdJobMonthlyIncomeTotal = 0;
		var applicantMonthlyIncomeTotal = 0;
		console.log('Updating applicant '+g+' monthly income totals');
		
		
		// total Primary, Second and Third Jobs first
		
		// should be able to just use the window vars for these calculated totals.
		
		//
			for(i=1; i<5; i++){
			applicantPrimaryJobMonthlyIncomeTotal = applicantPrimaryJobMonthlyIncomeTotal + stripCommas($('#applicant-'+g+'-primary-job-paystub-'+i).val());
			applicantSecondaryJobMonthlyIncomeTotal = applicantSecondaryJobMonthlyIncomeTotal + stripCommas($('#applicant-'+g+'-secondary-job-paystub-'+i).val());
			applicantThirdJobMonthlyIncomeTotal = applicantThirdJobMonthlyIncomeTotal + stripCommas($('#applicant-'+g+'-third-job-paystub-'+i).val());
		}
		/*
		applicantPrimaryJobMonthlyIncomeTotal = stripCommas(window.income['applicant-'+g+'-primary-job-total']);
		applicantSecondaryJobMonthlyIncomeTotal = stripCommas(window.income['applicant-'+g+'-secondary-job-total']);
		applicantThirdJobMonthlyIncomeTotal = stripCommas(window.income['applicant-'+g+'-secondary-job-total']);
		
		*/
		
		console.log('Updated Job Totals');
		$('#applicant-'+g+'-monthly-gross-wages-primary-job').val(numberWithCommas(applicantPrimaryJobMonthlyIncomeTotal.toFixed(2)));
		$('#applicant-'+g+'-monthly-gross-wages-secondary-job').val(numberWithCommas(applicantSecondaryJobMonthlyIncomeTotal.toFixed(2)));
		$('#applicant-'+g+'-monthly-gross-wages-third-job').val(numberWithCommas(applicantThirdJobMonthlyIncomeTotal.toFixed(2)));
		
		console.log('Updating Applicant '+g+' Gross Income Total');
		
		applicantMonthlyIncomeTotal = 
			
				stripCommas($('#applicant-'+g+'-self-employment').val())
				+ stripCommas($('#applicant-'+g+'-alimony').val())
				+ stripCommas($('#applicant-'+g+'-unemployment-compensation').val())
				+ stripCommas($('#applicant-'+g+'-social-security-income').val())
				+ stripCommas($('#applicant-'+g+'-social-security-disability').val())
				+ stripCommas($('#applicant-'+g+'-other-government-assistance').val())
				+ stripCommas($('#applicant-'+g+'-retirement').val())
				+ stripCommas($('#applicant-'+g+'-annuity-investment').val())
				+ stripCommas($('#applicant-'+g+'-rent-from-roommate').val())
				+ stripCommas($('#applicant-'+g+'-rental-property-income').val())
				+ stripCommas($('#applicant-'+g+'-contributions-from-family-or-friends').val())
				+ stripCommas($('#applicant-'+g+'-other-income').val())
				+ stripCommas($('#applicant-'+g+'-monthly-gross-wages-primary-job').val())
				+ stripCommas($('#applicant-'+g+'-monthly-gross-wages-secondary-job').val())
				+ stripCommas($('#applicant-'+g+'-monthly-gross-wages-third-job').val());
		//set function global//
		this['applicant'+g+'TotalIncome'] = applicantMonthlyIncomeTotal;
		//set form val//
		$('#applicant-'+g+'-total-income').val(numberWithCommas(applicantMonthlyIncomeTotal.toFixed(2)));
		//aggregate total gross income
		this.totalGrossIncome = this.totalGrossIncome + applicantMonthlyIncomeTotal;
		console.log('Applicant '+g+' new monthly total is '+applicantMonthlyIncomeTotal.toFixed(2)+" And total Gross Income "+this.totalGrossIncome.toFixed(2));
		
		////////////////////////// IMPORTANT - THERE CAN ONLY BE ONE PRIMARY APPLICANT - AKA "BORROWER". 
		///////////////////////// THIS NEEDS TO BE REINFORCED FROM BACK-END. 
		//////////////////////// OTHERWISE CALCULATIONS LIKE THE ONE BELOW WILL RETURN INVALID RESULTS.
		
		if($('#applicant-'+g+'-type').val() != "Borrower - Primary Applicant") {
			//this is not the primary - total up the gross income total for co-applicants
			this.coApplicantsTotalIncome = this.coApplicantsTotalIncome + applicantMonthlyIncomeTotal;
			console.log('Not primary applicant - Adding applicant to Co-Applicant Total : '+this.coApplicantsTotalIncome);
		} else {
			// record that this is the primary applicant.
			console.log('Applicant '+g+' determined to be primary applicant');
			var primaryApplicant = g;
		}
		
	}
	/// done with applicants - apply gross totals to form.
	$('#total-gross-income').val(numberWithCommas(this.totalGrossIncome.toFixed(2)));	
	$('#co-applicant-income').val(numberWithCommas(this.coApplicantsTotalIncome.toFixed(2)))
	
	var ami = parseFloat(this['applicant'+primaryApplicant+'TotalIncome'] * 100) / parseInt(amiBase);
	
	console.log('AMI calculated to be '+ami.toFixed(4)+'%');
	$('#borrower-ami').val(ami.toFixed(4)+'%');
	
	
	
	
	////// calculate the MPI
	
	var mpi =  parseFloat(mortgagePaymentTotal) / parseFloat(this.totalGrossIncome) * 100;
	// upate text and form var.
	$('#mpi').val(mpi.toFixed(4));
	$('#mpi-text').html(mpi.toFixed(4));
	console.log('MPI caluclated to be '+mpi.toFixed(4)+"%");
	
	////// Determine RPA first - RPA takes precedent over MPA
		////// Determine RPA first - RPA takes precedent over MPA
			////// Determine RPA first - RPA takes precedent over MPA
				////// Determine RPA first - RPA takes precedent over MPA
					////// Determine RPA first - RPA takes precedent over MPA
						////// Determine RPA first - RPA takes precedent over MPA
							////// Determine RPA first - RPA takes precedent over MPA
							
	var rpaRequestAmount = stripCommas($('#1st-reinstatement-amount').val());
	if(rpaRequestAmount > 0 && $('#apply-rpa').prop('checked')==true){
		
		console.log('Applicant has need for RPA');
		rpa = 1;
		// hide the x
		$('#rpa-qualify-check-box').hide();
		
		if(rpaRequestAmount > 25000){
			//rpaRequestAmount = 25000;
			// client wants the full requested amount to show - removing this limit, and changing the console.log to a UIkit.modal.alert();
			if(window['warnedRPALimit'+window.currentDetailId] !== 1){
			// only show this warning once per file opening.	
			
				UIkit.modal.alert('Please note, the reinstatement amount exceeds the RPA program\'s allowed limit of $25,000. Please decline RPA enrollment per the program guidelines.');
				window['warnedRPALimit'+window.currentDetailId] = 1;
				console.log('File #'+window.currentDetailId+' warned that reinstatement exceeds RPA limits for this session.');
			}
			/// make the RPA text red to show it is over
			$('#rpa-amount-text').addClass('red-text');
		} else {
			/// reset elements if they adjusted the Re-instatment amount.
			window['warnedRPALimit'+window.currentDetailId] = 0;
			$('#rpa-amount-text').removeClass('red-text');

		}
		
		// Update the text and form variables
		$('#rpa-amount-text').html('$'+numberWithCommas(rpaRequestAmount.toFixed(2))+" <br /><small>X 1 PAYMENT</small>");
		$('#rpa-amount').val(stripCommas(rpaRequestAmount.toFixed(2)));
		
	} else {
		if($('#apply-rpa').prop('checked')==false){
			console.log('RPA Declined');
			// hide the X
			$('#rpa-qualify-check-box').hide();

			// Update the text and form variables
			$('#rpa-amount-text').html('DECLINED');
			$('#rpa-amount-text').removeClass('red-text');	
			
		} else {
			
			console.log('No RPA needed.');
			// hide the checkbox
			$('#apply-rpa').hide();
			// show the X
			$('#rpa-qualify-check-box').show();
			
			// Update the text and form variables
			$('#rpa-amount-text').html('NOT NEEDED');
			$('#rpa-amount-text').removeClass('red-text');	

			
		}
		$('#rpa-amount').val(0);
		rpaRequestAmount = 0;
		
	}
	
	////// MPA = MPA = MPA = MPA
		////// MPA = MPA = MPA = MPA
			////// MPA = MPA = MPA = MPA
				////// MPA = MPA = MPA = MPA
					////// MPA = MPA = MPA = MPA
						////// MPA = MPA = MPA = MPA
							
	if(mpi.toFixed(4)>20.0000 && $('#apply-mpa').prop('checked')==true){
		console.log('Eligible for MPA');
		//MPA Eligible
		// hide the x
		$('#mpa-qualify-check-box').hide();
		// show the checkbox
		$('#apply-mpa').show();
		$('#number-of-mpa-payments').show();
		mpa = 1;
		// determine the actual mortgage payment - do not include non escrow amounts.
		console.log('Determining PITIA payment based on escrow inclusions');
		var mpaPaymentAmountEach =  stripCommas($('#principal-plus-interest').val());
		if($('#property-tax-escrowed').val() == "YES") {
			mpaPaymentAmountEach =  mpaPaymentAmountEach + stripCommas($('#property-tax').val());
		}
		if($('#property-insurance-escrowed').val() == "YES") {
			mpaPaymentAmountEach =  mpaPaymentAmountEach + stripCommas($('#property-insurance').val());
		}
		if($('#hoa-misc-escrowed').val() == "YES") {
			mpaPaymentAmountEach =  mpaPaymentAmountEach + stripCommas($('#hoa-misc').val());
		}
		console.log('Actual mortgage payment is '+mpaPaymentAmountEach.toFixed(2));
		var mpaPaymentsPossible = 25000 / parseInt(mpaPaymentAmountEach);
		console.log('Total possible MPA payments: '+parseInt(mpaPaymentsPossible));
		if(parseInt(mpaPaymentsPossible)< 6) {
			if(window['warnedMPA'+window.currentDetailId] !== 1){
			// can't make 6 total payments - don't block an i record - but add a note.
			UIkit.modal.alert('Please note, the mortgage payment amount exceeds the MPA program\'s allowed limit of $25,000 for the minimum 6 payments. Please decline MPA enrollment per the program guidelines.');
			window['warnedMPA'+window.currentDetailId] = 1;
			// make it so they are only warned once per session.
			}
			$('#mpa-amount-text').addClass('red-text');	

		} else {
			//reset vars
			window['warnedMPA'+window.currentDetailId] = 0;
			$('#mpa-amount-text').removeClass('red-text');
			
		}
		/// we default to 6 payments. Use the number of payments the user specified to calculate the total.
		// Update the text and form variables
		$('#mpa-amount-text').html('$'+numberWithCommas(mpaPaymentAmountEach.toFixed(2))+'<br /><small>x <a>'+$('#number-of-mpa-payments').val()+'</a> payments</small>');
		$('#mpa-payment-amount-each').val(mpaPaymentAmountEach);
		// Setting mandatory escrow reserve to 3000 so it is calculated in total.
		$('#escrow-reserve').val('3,000');
		
		
	} else {
		
		if($('#apply-mpa').prop('checked')==false){
			console.log('Declined MPA');
			// Update the text and form variables
			$('#mpa-amount-text').html('DECLINED');
			// hide the x
			$('#mpa-qualify-check-box').hide();
			$('#number-of-mpa-payments').hide();
			$('#apply-mpa').show();
			// Setting mandatory mpa escrow reserve to 0 so it is not calculated in total.
			$('#escrow-reserve').val('0');
			
			
		} else {
			// hide the check box
			$('#apply-mpa').hide();
			$('#number-of-mpa-payments').hide();
			$('#mpa-qualify-check-box').show();
			console.log('Not eligible for MPA');
			// Update the text and form variables
			$('#mpa-amount-text').html('NOT ELIGIBLE');
			// Setting mandatory mpa escrow reserve to 0 so it is not calculated in total.
			$('#escrow-reserve').val('0');
		}
		$('#mpa-amount').val(0);
		mpaPaymentAmountEach = 0;
	
		
	} 
	// The 3000 at the end is the mandatory MPA
	var totalAid = (mpaPaymentAmountEach * parseInt($('#number-of-mpa-payments').val()) ) + rpaRequestAmount +  stripCommas($('#escrow-reserve').val());
	$('#total-aid-text').html('$'+numberWithCommas(totalAid.toFixed(2)));
	$('#total-aid').val(totalAid.toFixed(2));
	// Corrected maximum amount to 35,000 - was 33,000.
	if(totalAid > 35000) {
		$('#total-aid-text').removeClass('green-text');
		$('#total-aid-text').addClass('red-text');
		
	} else {
		$('#total-aid-text').removeClass('red-text');
		$('#total-aid-text').addClass('green-text');
		
	}
	
	
	
	
	
	
	
	
	
}

function editPITIA(){	
	dynamicModalLoad('pitia');
}

function updatePITIAForm () {
	
	$('#update-principal-plus-interest').val(stripCommas($('#principal-plus-interest').val()));
	$('#update-property-tax').val(stripCommas($('#property-tax').val()));
	$('#update-property-tax-escrowed').val($('#property-tax-escrowed').val());
	$('#update-property-insurance').val(stripCommas($('#property-insurance').val()));
	$('#update-property-insurance-escrowed').val($('#property-insurance-escrowed').val());
	$('#update-hoa-misc').val(stripCommas($('#hoa-misc').val()));
	$('#update-hoa-misc-escrowed').val($('#hoa-misc-escrowed').val());
	
	
}

function updatePITIA () {
	
	// update the fields in the form to match that of the window.applicantId
	var PITIAId = window.PITIAId;
	
	$('#principal-plus-interest').val(stripCommas($('#update-principal-plus-interest').val()));
	$('#property-tax').val(stripCommas($('#update-property-tax').val()));
	$('#property-tax-escrowed').val($('#update-property-tax-escrowed').val());
	$('#property-insurance').val(stripCommas($('#update-property-insurance').val()));
	$('#property-insurance-escrowed').val($('#update-property-insurance-escrowed').val());
	$('#hoa-misc').val(stripCommas($('#update-hoa-misc').val()));
	$('#hoa-misc-escrowed').val($('#update-hoa-misc-escrowed').val());
	

	window.saved = 0;
	calculateEligibility ();
	dynamicModalClose();
}

function getSSN (forApplicantId) {
	var response = "";
    var form_data = {
        applicantId: forApplicantId
        
    };
    $('#ssn').val('Requesting SSN...');
	console.log('Request for SSN Made');
	$.ajax({
        type: "POST", 
        url: "/modals/social-security-number.json", 
        data: form_data,
        success: function(response)
	        {
				console.log('json file responded with :'+response); 
				 var json_obj = response;
				 console.log('Json showSSN: '+json_obj[0].showSSN);
				 console.log('SSN Retreived: '+json_obj[0].ssn);
				 console.log('Error Message: '+json_obj[0].errorMessage);	
				 
				 if(json_obj[0].showSSN === "1") {
					 
					 $('#ssn').val(json_obj[0].ssn);
					 $('#ssn').attr('readonly', false);
				 } else {
					 $('#ssn').val(json_obj[0].errorMessage);
				 }
					 
				 	    },
	    dataType: "json"
	});
	
}

function breakOut(id){
	console.log('Showing Income Breakout for '+id);
	$(id).slideToggle();	
}

function openUrl(givenUrl){
	
	window.open(givenUrl, '_blank', 'location=yes,height=570,width=920,scrollbars=yes,status=yes');
	console.log('opened '+givenUrl);

}

function showTotalApplicants(){
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	for(i=1;i<totalApplicants;i++) {
		
		$('#update-applicant-'+i+'-holder').show();
		$('#update-applicant-'+i+'-divider').show();
		console.log('Showing applicant '+i+' section');
		
	}
	
}

///////// APPLICANT INCOME CALCULATIONS //////////// 
//////// REVISED 9-29-16 ////////
function totalJobIncomeFor(applicant,stub,frequency) {
	var monthlyTotal = 0;
	switch(frequency){
		case 'monthly':
			console.log('applying monthly logic');
			// get first paystub value from the update form elements.
			monthlyTotal = stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').val());
						
			// set monthly total at the window level
			window.jobIncomeTotal = window.jobIncomeTotal + monthlyTotal;
			// hide unused paystub fields
			for (i=2; i<5; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).parent().parent().parent().parent().slideUp();
			}
			// show used paystub fields
			$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').parent().parent().parent().parent().slideDown();
			// show logic note for audit
			$('#update-applicant-'+applicant+'-'+stub+'-job-logic').html('Total gross monthly income for '+stub+' job is based on the most recently provided paystub.');
			
			return monthlyTotal.toFixed(2);
			break;
		////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
						////////////////////////////////////////////////////////////////
							
		case 'bi-monthly':
			console.log('applying bi-monthly logic');
			// get first and second paystubs value from the update form elements.
			monthlyTotal = ((stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').val()) + stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-2').val()))/2)*2;
						
			// set monthly total at the window level
			window.jobIncomeTotal = window.jobIncomeTotal + monthlyTotal;
			// hide unused paystub fields
			for (i=3; i<5; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).parent().parent().parent().parent().slideUp();
			}
			// show used paystub fields
			for (i=1; i<3; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).parent().parent().parent().parent().slideDown();
			}
			// show logic note for audit
			$('#update-applicant-'+applicant+'-'+stub+'-job-logic').html('Total gross monthly income for '+stub+' job is based on the average of the two most recently provided paystubs.');
			
			return monthlyTotal.toFixed(2);
			break;
		////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
						////////////////////////////////////////////////////////////////
			
		case 'bi-weekly':
			console.log('applying bi-weekly logic');
			monthlyTotal = ((stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').val()) + stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-2').val()))/2)*2; 
						
			// set monthly total at the window level
			window.jobIncomeTotal = window.jobIncomeTotal + monthlyTotal;
			// hide unused paystub fields
			for (i=3; i<5; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).parent().parent().parent().parent().slideUp();
			}
			// show used paystub fields
			for (i=1; i<3; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).parent().parent().parent().parent().slideDown();
			}
			// show logic note for audit
			$('#update-applicant-'+applicant+'-'+stub+'-job-logic').html('Total gross monthly income for '+stub+' job is based on the average of the two most recently provided paystubs.');
			
			return monthlyTotal.toFixed(2);
			break;
		////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
						////////////////////////////////////////////////////////////////
		
		case 'weekly':
			console.log('values passed to process are applicant:'+applicant+' stub:'+stub);
			console.log('applying weekly logic using paystub 1:'+$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').val());
			console.log('applying weekly logic using paystub 2:'+$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-2').val());
			console.log('applying weekly logic using paystub 3:'+$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-3').val());
			console.log('applying weekly logic using paystub 4:'+$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-4').val());
			monthlyTotal = ((((stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-1').val()) + stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-2').val()) + stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-3').val()) + stripCommas($('#update-applicant-'+applicant+'-'+stub+'-job-paystub-4').val())))/4)*52)/12;
			console.log('weekly average determined to be '+ monthlyTotal); 
						
			// set monthly total at the window level
			
			window.jobIncomeTotal = window.jobIncomeTotal + monthlyTotal;
			
			// show used paystub fields
			for (i=1; i<5; i++){
				$('#update-applicant-'+applicant+'-'+stub+'-job-paystub-'+i).slideDown();
			}
			// show logic note for audit
			$('#update-applicant-'+applicant+'-'+stub+'-job-logic').html('Total gross monthly income for '+stub+' job is based on the average of the four most recently provided paystubs.');
			
			return monthlyTotal.toFixed(2);
			break;
		////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////
						////////////////////////////////////////////////////////////////
		
		
	}
}

function updateApplicantIncomeForm(){
	console.log('Updating Applicant Income Form with current values.')
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	
	
	for(g=1; g<totalApplicants; g++){
		// set title areas text
		console.log('Updating applicant '+g);
		$('#update-applicant-'+g+'-type-text').html($('#applicant-'+g+'-type').val());
		$('#update-applicant-'+g+'-name').html($('#applicant-'+g+'-name').val());
		
		// total Primary, Second and Third Jobs first
		for(i=1; i<5; i++){
			$('#update-applicant-'+g+'-primary-job-paystub-'+i).val($('#applicant-'+g+'-primary-job-paystub-'+i).val());
			$('#update-applicant-'+g+'-secondary-job-paystub-'+i).val($('#applicant-'+g+'-secondary-job-paystub-'+i).val());
			$('#update-applicant-'+g+'-third-job-paystub-'+i).val($('#applicant-'+g+'-third-job-paystub-'+i).val());
		}
		
		// set job payment frequency
		$('#update-applicant-'+g+'-primary-job-payment-type').val($('#applicant-'+g+'-primary-job-payment-type').val());
		$('#update-applicant-'+g+'-secondary-job-payment-type').val($('#applicant-'+g+'-secondary-job-payment-type').val());
		$('#update-applicant-'+g+'-third-job-payment-type').val($('#applicant-'+g+'-third-job-payment-type').val());
		
		// calculate totals for primary, secondary, and third jobs.
		
		$('#update-applicant-'+g+'-monthly-gross-wages-primary-job').val(totalJobIncomeFor(g,'primary',$('#update-applicant-'+g+'-primary-job-payment-type').val()));
		$('#update-applicant-'+g+'-monthly-gross-wages-secondary-job').val(totalJobIncomeFor(g,'secondary',$('#update-applicant-'+g+'-primary-job-payment-type').val()));
		$('#update-applicant-'+g+'-monthly-gross-wages-third-job').val(totalJobIncomeFor(g,'third',$('#update-applicant-'+g+'-primary-job-payment-type').val()));
		$('#update-applicant-'+g+'-self-employment').val($('#applicant-'+g+'-self-employment').val());
		$('#update-applicant-'+g+'-alimony').val($('#applicant-'+g+'-alimony').val());
		$('#update-applicant-'+g+'-unemployment-compensation').val($('#applicant-'+g+'-unemployment-compensation').val());
		$('#update-applicant-'+g+'-social-security-income').val($('#applicant-'+g+'-social-security-income').val());
		$('#update-applicant-'+g+'-social-security-disability').val($('#applicant-'+g+'-social-security-disability').val());
		$('#update-applicant-'+g+'-other-government-assistance').val($('#applicant-'+g+'-other-government-assistance').val());
		$('#update-applicant-'+g+'-retirement').val($('#applicant-'+g+'-retirement').val());
		$('#update-applicant-'+g+'-annuity-investment').val($('#applicant-'+g+'-annuity-investment').val());
		$('#update-applicant-'+g+'-rent-from-roommate').val($('#applicant-'+g+'-rent-from-roommate').val());
		$('#update-applicant-'+g+'-rental-property-income').val($('#applicant-'+g+'-rental-property-income').val());
		$('#update-applicant-'+g+'-contributions-from-family-or-friends').val($('#applicant-'+g+'-contributions-from-family-or-friends').val());
		$('#update-applicant-'+g+'-other-income').val($('#applicant-'+g+'-other-income').val());
		$('#update-applicant-'+g+'-total-income-text').html($('#applicant-'+g+'-total-income').val());
	}
	
}


function updateApplicantIncomeFields(){
	console.log('Updating Applicant Income Fields with new values.')
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	
	
	for(g=1; g<totalApplicants; g++){
		// set title areas text
		console.log('Updating applicant '+g);
		$('#applicant-'+g+'-type-text').html($('#update-applicant-'+g+'-type').val());
		$('#applicant-'+g+'-name').html($('#update-applicant-'+g+'-name').val());
		
		// total Primary, Second and Third Jobs first
		
		for(i=1; i<5; i++){
			$('#applicant-'+g+'-primary-job-paystub-'+i).val($('#update-applicant-'+g+'-primary-job-paystub-'+i).val());
			$('#applicant-'+g+'-secondary-job-paystub-'+i).val($('#update-applicant-'+g+'-secondary-job-paystub-'+i).val());
			$('#applicant-'+g+'-third-job-paystub-'+i).val($('#update-applicant-'+g+'-third-job-paystub-'+i).val());
		}
		$('#applicant-'+g+'-primary-job-payment-type').val($('#update-applicant-'+g+'-primary-job-payment-type').val());
		$('#applicant-'+g+'-secondary-job-payment-type').val($('#update-applicant-'+g+'-secondary-job-payment-type').val());
		$('#applicant-'+g+'-third-job-payment-type').val($('#update-applicant-'+g+'-third-job-payment-type').val());
		$('#applicant-'+g+'-monthly-gross-wages-primary-job').val($('#update-applicant-'+g+'-monthly-gross-wages-primary-job').val());
		$('#applicant-'+g+'-monthly-gross-wages-secondary-job').val($('#update-applicant-'+g+'-monthly-gross-wages-secondary-job').val());
		$('#applicant-'+g+'-monthly-gross-wages-third-job').val($('#update-applicant-'+g+'-monthly-gross-wages-third-job').val());
		$('#applicant-'+g+'-self-employment').val($('#update-applicant-'+g+'-self-employment').val());
		$('#applicant-'+g+'-alimony').val($('#update-applicant-'+g+'-alimony').val());
		$('#applicant-'+g+'-unemployment-compensation').val($('#update-applicant-'+g+'-unemployment-compensation').val());
		$('#applicant-'+g+'-social-security-income').val($('#update-applicant-'+g+'-social-security-income').val());
		$('#applicant-'+g+'-social-security-disability').val($('#update-applicant-'+g+'-social-security-disability').val());
		$('#applicant-'+g+'-other-government-assistance').val($('#update-applicant-'+g+'-other-government-assistance').val());
		$('#applicant-'+g+'-retirement').val($('#update-applicant-'+g+'-retirement').val());
		$('#applicant-'+g+'-annuity-investment').val($('#update-applicant-'+g+'-annuity-investment').val());
		$('#applicant-'+g+'-rent-from-roommate').val($('#update-applicant-'+g+'-rent-from-roommate').val());
		$('#applicant-'+g+'-rental-property-income').val($('#update-applicant-'+g+'-rental-property-income').val());
		$('#applicant-'+g+'-contributions-from-family-or-friends').val($('#update-applicant-'+g+'-contributions-from-family-or-friends').val());
		$('#applicant-'+g+'-other-income').val($('#update-applicant-'+g+'-other-income').val());
		$('#applicant-'+g+'-total-income-text').html($('#update-applicant-'+g+'-total-income').val());
	}
	console.log('Updating Eligibility Based on New Income Values');
	calculateEligibility ();
	dynamicModalClose();
	
}



function previewUpdateApplicantMonthlyTotal(type){
	console.log('Updating the monthly income preview totals')
	var totalApplicants = parseInt($('#total-number-of-applicants').val())+1;
	
	
	for(g=1; g<totalApplicants; g++){
		var applicantPrimaryJobMonthlyIncomeTotal = 0;
		var applicantSecondaryJobMonthlyIncomeTotal = 0;
		var applicantThirdJobMonthlyIncomeTotal = 0;
		var applicantMonthlyIncomeTotal = 0;
		console.log('Updating applicant '+g+' monthly income totals');
		
		
		// total Primary, Second and Third Jobs first
		/* OLD METHOD 
		for(i=1; i<5; i++){
			applicantPrimaryJobMonthlyIncomeTotal = applicantPrimaryJobMonthlyIncomeTotal + stripCommas($('#update-applicant-'+g+'-primary-job-paystub-'+i).val());
			applicantSecondaryJobMonthlyIncomeTotal = applicantSecondaryJobMonthlyIncomeTotal + stripCommas($('#update-applicant-'+g+'-secondary-job-paystub-'+i).val());
			applicantThirdJobMonthlyIncomeTotal = applicantThirdJobMonthlyIncomeTotal + stripCommas($('#update-applicant-'+g+'-third-job-paystub-'+i).val());
		}
		*/
		console.log('Processing Primary Job Monthly Total for Applicant '+g+', who is paid '+$('#update-applicant-'+g+'-primary-job-payment-type').val());
		applicantPrimaryJobMonthlyIncomeTotal = totalJobIncomeFor(g,'primary',$('#update-applicant-'+g+'-primary-job-payment-type').val());
		applicantSecondaryJobMonthlyIncomeTotal = totalJobIncomeFor(g,'secondary',$('#update-applicant-'+g+'-secondary-job-payment-type').val());
		applicantThirdJobMonthlyIncomeTotal = totalJobIncomeFor(g,'third',$('#update-applicant-'+g+'-primary-job-payment-type').val());
		console.log('Updated Job Totals');
		$('#update-applicant-'+g+'-monthly-gross-wages-primary-job').val(numberWithCommas(applicantPrimaryJobMonthlyIncomeTotal.toFixed(2)));
		$('#update-applicant-'+g+'-monthly-gross-wages-secondary-job').val(numberWithCommas(applicantSecondaryJobMonthlyIncomeTotal.toFixed(2)));
		$('#update-applicant-'+g+'-monthly-gross-wages-third-job').val(numberWithCommas(applicantThirdJobMonthlyIncomeTotal.toFixed(2)));
		
		console.log('Updating Grand Total');
		
		applicantMonthlyIncomeTotal = 
			
				stripCommas($('#update-applicant-'+g+'-self-employment').val())
				+ stripCommas($('#update-applicant-'+g+'-alimony').val())
				+ stripCommas($('#update-applicant-'+g+'-unemployment-compensation').val())
				+ stripCommas($('#update-applicant-'+g+'-social-security-income').val())
				+ stripCommas($('#update-applicant-'+g+'-social-security-disability').val())
				+ stripCommas($('#update-applicant-'+g+'-other-government-assistance').val())
				+ stripCommas($('#update-applicant-'+g+'-retirement').val())
				+ stripCommas($('#update-applicant-'+g+'-annuity-investment').val())
				+ stripCommas($('#update-applicant-'+g+'-rent-from-roommate').val())
				+ stripCommas($('#update-applicant-'+g+'-rental-property-income').val())
				+ stripCommas($('#update-applicant-'+g+'-contributions-from-family-or-friends').val())
				+ stripCommas($('#update-applicant-'+g+'-other-income').val())
				+ stripCommas($('#update-applicant-'+g+'-monthly-gross-wages-primary-job').val())
				+ stripCommas($('#update-applicant-'+g+'-monthly-gross-wages-secondary-job').val())
				+ stripCommas($('#update-applicant-'+g+'-monthly-gross-wages-third-job').val());
				
		$('#update-applicant-'+g+'-total-income-text').html(numberWithCommas(applicantMonthlyIncomeTotal.toFixed(2)));
		console.log('Applicant '+g+' new monthly total is '+applicantMonthlyIncomeTotal.toFixed(2));
	}
	
}



function openAuditorSite(){
	var county = $('#parcel-county').val();
	console.log('County Id '+county+" Selected.");
	switch(county){
		case '1':
		//adams county
		openUrl('http://www.adamscountyauditor.org/Search.aspx');
		console.log('case 1 fired');
		break;
		case '2':
		//allen county
		openUrl('http://allencountyohpropertytax.com/AddressSearch.aspx');
		break;
		case '3':
		//ashland county
		openUrl('http://oh-ashland-auditor.publicaccessnow.com/AddressSearch.aspx');
		break;
		case '4':
		//ashtabula county
		openUrl('http://ashtabulaoh-auditor.ddti.net/Search.aspx');
 		break;
		case '5':
		//athens county
		openUrl('http://www.athenscountyauditor.org/Search.aspx');
 		break;
		case '6':
		//auglaize county
		openUrl('http://www.auglaizeauditor.ddti.net/Search.aspx');
 		break;
		case '7':
		//belmont county
		openUrl('http://oh-belmont-auditor.publicaccessnow.com/OwnerSearch.aspx');
 		break;
		case '8':
		//brown county
		openUrl('http://brownauditor.ddti.net/Search.aspx');
 		break;
		case '9':
		//butler county
		openUrl('http://propertysearch.butlercountyohio.org/PT/search/commonsearch.aspx?mode=owner');
 		break;
		case '10':
		//carroll county
		openUrl('http://carroll.mfcdsoftware.com/re/re-search.php?item=Name');
 		break;
		case '11':
		//champaign county
		openUrl('http://champaignoh.ddti.net/Search.aspx');
 		break;
		case '12':
		//clark county
		openUrl('http://gis.clarkcountyauditor.org/Search.aspx');
 		break;
		case '13':
		//clermont county
		openUrl('http://www.clermontauditor.org/_web/search/commonsearch.aspx?mode=owner');
 		break;
		case '14':
		//clinton county
		openUrl('http://clintonoh.ddti.net/Search.aspx');
 		break;
		case '15':
		//columbiana county
		openUrl('http://oh-columbiana-auditor.publicaccessnow.com/OwnerSearch.aspx');
 		break;
		case '16':
		//coshocton county
		openUrl('http://www.coshcoauditor.org/pt/search/commonsearch.aspx?mode=address');
 		break;
		case '17':
		//crawford county
		openUrl('http://realestate.crawford-co.org/re-search.php');
 		break;
		case '18':
		//cuyahoga county
		openUrl('http://fiscalofficer.cuyahogacounty.us/AuditorApps/real-property/REPI/default.asp');
 		break;
		case '19':
		//darke county
		openUrl('http://www.darkecountyrealestate.org/Search.aspx');
 		break;
		case '20':
		//defiance county
		openUrl('http://defiance.ddti.net/Search.aspx');
		break;
		case '21':
		//delaware county
		openUrl('http://delaware-auditor-ohio.manatron.com/OwnerSearch.aspx');
 		break;
		case '22':
		//erie county
		openUrl('http://www.erie.iviewtaxmaps.com/Search.aspx');
 		break;
		case '23':
		//fairfield county
		openUrl('http://realestate.co.fairfield.oh.us/Search.aspx');
 		break;
		case '24':
		//fayette county
		openUrl('http://fayettepropertymax.governmax.com/propertymax/rover30.asp');
 		break;
		case '25':
		//franklin county
		openUrl('http://property.franklincountyauditor.com/_web/search/commonsearch.aspx?mode=owner');
 		break;
		case '26':
		//fulton county
		openUrl('http://fultonoh-auditor.ddti.net/Search.aspx');
 		break;
		case '27':
		//gallia county
		openUrl('http://galliaauditor.ddti.net/Search.aspx');
 		break;
		case '28':
		//geauga county
		openUrl('http://geaugarealink.co.geauga.oh.us/realink/');
 		break;
		case '29':
		//greene county
		openUrl('http://apps.co.greene.oh.us/auditor/ureca/default.aspx');
 		break;
		case '30':
		//guernsey county
		openUrl('http://www.guernseycountyauditor.org/Search.aspx');
 		break;
		case '31':
		//hamilton county
		openUrl('http://wedge1.hcauditor.org/');
 		break;
		case '32':
		//hancock county
		openUrl('http://regis.co.hancock.oh.us/Search.aspx');
 		break;
		case '33':
		//hardin county
		openUrl('http://realestate.co.hardin.oh.us/re-search.php?item=Name');
 		break;
		case '34':
		//harrison county
		UIkit.modal.alert('Sorry - there isn\'t a website for Harrison County');
		break;
		case '35':
		//henry county
		openUrl('http://www.co.henry.oh.us/re/re-search.php?item=Name');
 		break;
		case '36':
		//highland county
		openUrl('http://highlandcountyauditor.org/Search.aspx');
 		break;
		case '37':
		//hocking county
		openUrl('http://www.realestate.co.hocking.oh.us/re/re-search.php?item=Name');
 		break;
		case '38':
		//holmes county
		openUrl('http://www.holmescountyauditor.org/Search.aspx');
 		break;
		case '39':
		//huron county
		openUrl('http://www.huroncountyauditor.org/Search.aspx');
 		break;
		case '40':
		//jackson county
		openUrl('http://www.jacksoncountyauditor.org/Disclaimer.aspx?Redirect=%2fSearch.aspx&CheckForCookies=Yes');
 		break;
		case '41':
		//jefferson county
		openUrl('http://www.jeffersoncountyoh.com/OnLineServices/RealEstateSearch.aspx');
 		break;
		case '42':
		//knox county
		openUrl('http://www.knoxcountyauditor.org/Search.aspx');
 		break;
		case '43':
		//lake county
		openUrl('http://www.lake.iviewauditor.com/Search.aspx');
 		break;
		case '44':
		//lawerence county
		openUrl('http://www.lawrencecountyauditor.org/Search.aspx');
 		break;
		case '45':
		//licking county
		openUrl('http://www.lcounty.com/TAGCPM.PA.PublicPortal/faces/pages/search.xhtml?confirm=true');
 		break;
		case '46':
		//logan county
		openUrl('http://realestate.co.logan.oh.us/search.aspx');
 		break;
		case '47':
		//lorain county
		openUrl('http://www.loraincountyauditor.com/gis/');
 		break;
		case '48':
		//lucas county
		openUrl('http://icare.co.lucas.oh.us/LucasCare/search/commonsearch.aspx?mode=address');
 		break;
		case '49':
		//madison county
		openUrl('http://madisonoh.ddti.net/Search.aspx');
 		break;
		case '50':
		//mahoning county
		openUrl('http://oh-mahoning-auditor.publicaccessnow.com/AddressSearch.aspx');
 		break;
		case '51':
		//marion county
		openUrl('http://propertysearch.co.marion.oh.us/Search.aspx');
 		break;
		case '52':
		//medina county
		openUrl('http://www.medinacountyauditor.org/propsearch.htm');
 		break;
		case '53':
		//meigs county
		openUrl('http://www.meigscountyauditor.org/Search.aspx');
 		break;
		case '54':
		//mercer county
		openUrl('http://www2.mercercountyohio.org/auditor/ParcelSearch/');
 		break;
		case '55':
		//miami county
		openUrl('http://www.miamicountyauditor.org/Search.aspx');
 		break;
		case '56':
		//monroe county
		openUrl('http://monroecountyauditor.org/Search.aspx');
 		break;
		case '57':
		//montgomery county
		openUrl('http://www.mcrealestate.org/search/commonsearch.aspx?mode=address');
 		break;
		case '58':
		//morgan county
		openUrl('http://www.morgancountyauditor.org/Search.aspx');
 		break;
		case '59':
		//morrow county
		openUrl('http://auditor.co.morrow.oh.us/Search.aspx');
 		break;
		case '60':
		//muskingum county
		openUrl('http://www.muskingumcountyauditor.org/Search.aspx');
 		break;
		case '61':
		//noble county
		UIkit.modal.alert('Sorry - there isn\'t a site for Noble County');
		break;
		case '62':
		//ottawa county
		openUrl('http://www.ottawacountyauditor.org/Search.aspx');
 		break;
		case '63':
		//paulding county
		openUrl('http://www.pauldingcountyauditor.com/Search.aspx');
 		break;
		case '64':
		//perry county
		openUrl('http://www.perrycountyauditor.us/Search.aspx');
 		break;
		case '65':
		//pickaway county
		openUrl('http://pickaway.iviewauditor.com/Search.aspx');
 		break;
		case '66':
		//pike county
		openUrl('http://www.realestate.pike-co.org/re/re-search.php');
 		break;
		case '67':
		//portage county
		openUrl('http://portagecountyauditor.org/Search.aspx');
 		break;
		case '68':
		//preble county
		openUrl('http://www.preblecountyauditor.org/Search.aspx');
 		break;
		case '69':
		//putnam county
		openUrl('http://co.putnam.oh.us/re/re-search.php');
 		break;
		case '70':
		//richland county
		openUrl('http://www.richlandcountyauditor.org/pt/search/commonsearch.aspx?mode=owner');
 		break;
		case '71':
		//ross county
		openUrl('http://auditor.co.ross.oh.us/Search.aspx');
 		break;
		case '72':
		//sandusky county
		openUrl('http://www.sanduskycountyauditor.us/Search.aspx');
 		break;
		case '73':
		//scioto county
		openUrl('http://oh-scioto-auditor.publicaccessnow.com/AddressSearch.aspx');
 		break;
		case '74':
		//seneca county
		openUrl('http://www.senecacountyauditor.org/Search.aspx');
 		break;
		case '75':
		//shelby county
		openUrl('http://cama.shelbycountyauditors.com/cama/');
		break;
		case '76':
		//stark county
		openUrl('http://ddti.starkcountyohio.gov/Search.aspx');
 		break;
		case '77':
		//summit county
		openUrl('http://fiscaloffice.summitoh.net/index.php/property-tax-search');
 		break;
		case '78':
		//trumbull county
		openUrl('http://property.co.trumbull.oh.us/Search.aspx');
 		break;
		case '79':
		//tuscarawas county
		openUrl('http://auditor.co.tuscarawas.oh.us/search.aspx');
 		break;
		case '80':
		//union county
		openUrl('http://unionsearch.ohiorevaluations.com/default.aspx');
 		break;
		case '81':
		//van wert county
		openUrl('http://www.co.vanwert.oh.us/re/re-search.php');
 		break;
		case '82':
		//vinton county
		openUrl('http://vintoncountyauditor.org/Search.aspx');
 		break;
		case '83':
		//warren county
		openUrl('http://www.co.warren.oh.us/property_search/');
 		break;
		case '84':
		//washington county
		openUrl('http://www.washingtoncountyauditor.us/Search.aspx');
 		break;
		case '85':
		//wayne county
		openUrl('http://www.waynecountyauditor.org/Search.aspx');
 		break;
		case '86':
		//williams county
		openUrl('http://www.williamsoh.ddti.net/Search.aspx');
 		break;
		case '87':
		//wood county
		openUrl('http://auditor.co.wood.oh.us/Search.aspx');
 		break;
		case '88':
		//wyandot county
		openUrl('http://realestate.co.wyandot.oh.us/re/re-search.php');
		break;
		
	}
}

function saveTheChanges (){
	
	UIkit.modal.prompt("Please comment on why you made your changes.",'',function(val){
		$('#note').val(val);
		$.post("output_post.php", $("#detail-tab-form").serialize(), function(data) {
        	UIkit.modal.alert(data);
        	window.saved=1;
    });
	});
}


/*!
 * clipboard.js v1.5.12
 * https://zenorocha.github.io/clipboard.js
 *
 * Licensed MIT © Zeno Rocha
 */
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{var e;e="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,e.Clipboard=t()}}(function(){var t,e,n;return function t(e,n,o){function i(a,c){if(!n[a]){if(!e[a]){var s="function"==typeof require&&require;if(!c&&s)return s(a,!0);if(r)return r(a,!0);var l=new Error("Cannot find module '"+a+"'");throw l.code="MODULE_NOT_FOUND",l}var u=n[a]={exports:{}};e[a][0].call(u.exports,function(t){var n=e[a][1][t];return i(n?n:t)},u,u.exports,t,e,n,o)}return n[a].exports}for(var r="function"==typeof require&&require,a=0;a<o.length;a++)i(o[a]);return i}({1:[function(t,e,n){var o=t("matches-selector");e.exports=function(t,e,n){for(var i=n?t:t.parentNode;i&&i!==document;){if(o(i,e))return i;i=i.parentNode}}},{"matches-selector":5}],2:[function(t,e,n){function o(t,e,n,o,r){var a=i.apply(this,arguments);return t.addEventListener(n,a,r),{destroy:function(){t.removeEventListener(n,a,r)}}}function i(t,e,n,o){return function(n){n.delegateTarget=r(n.target,e,!0),n.delegateTarget&&o.call(t,n)}}var r=t("closest");e.exports=o},{closest:1}],3:[function(t,e,n){n.node=function(t){return void 0!==t&&t instanceof HTMLElement&&1===t.nodeType},n.nodeList=function(t){var e=Object.prototype.toString.call(t);return void 0!==t&&("[object NodeList]"===e||"[object HTMLCollection]"===e)&&"length"in t&&(0===t.length||n.node(t[0]))},n.string=function(t){return"string"==typeof t||t instanceof String},n.fn=function(t){var e=Object.prototype.toString.call(t);return"[object Function]"===e}},{}],4:[function(t,e,n){function o(t,e,n){if(!t&&!e&&!n)throw new Error("Missing required arguments");if(!c.string(e))throw new TypeError("Second argument must be a String");if(!c.fn(n))throw new TypeError("Third argument must be a Function");if(c.node(t))return i(t,e,n);if(c.nodeList(t))return r(t,e,n);if(c.string(t))return a(t,e,n);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList")}function i(t,e,n){return t.addEventListener(e,n),{destroy:function(){t.removeEventListener(e,n)}}}function r(t,e,n){return Array.prototype.forEach.call(t,function(t){t.addEventListener(e,n)}),{destroy:function(){Array.prototype.forEach.call(t,function(t){t.removeEventListener(e,n)})}}}function a(t,e,n){return s(document.body,t,e,n)}var c=t("./is"),s=t("delegate");e.exports=o},{"./is":3,delegate:2}],5:[function(t,e,n){function o(t,e){if(r)return r.call(t,e);for(var n=t.parentNode.querySelectorAll(e),o=0;o<n.length;++o)if(n[o]==t)return!0;return!1}var i=Element.prototype,r=i.matchesSelector||i.webkitMatchesSelector||i.mozMatchesSelector||i.msMatchesSelector||i.oMatchesSelector;e.exports=o},{}],6:[function(t,e,n){function o(t){var e;if("INPUT"===t.nodeName||"TEXTAREA"===t.nodeName)t.focus(),t.setSelectionRange(0,t.value.length),e=t.value;else{t.hasAttribute("contenteditable")&&t.focus();var n=window.getSelection(),o=document.createRange();o.selectNodeContents(t),n.removeAllRanges(),n.addRange(o),e=n.toString()}return e}e.exports=o},{}],7:[function(t,e,n){function o(){}o.prototype={on:function(t,e,n){var o=this.e||(this.e={});return(o[t]||(o[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){function o(){i.off(t,o),e.apply(n,arguments)}var i=this;return o._=e,this.on(t,o,n)},emit:function(t){var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),o=0,i=n.length;for(o;i>o;o++)n[o].fn.apply(n[o].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),o=n[t],i=[];if(o&&e)for(var r=0,a=o.length;a>r;r++)o[r].fn!==e&&o[r].fn._!==e&&i.push(o[r]);return i.length?n[t]=i:delete n[t],this}},e.exports=o},{}],8:[function(e,n,o){!function(i,r){if("function"==typeof t&&t.amd)t(["module","select"],r);else if("undefined"!=typeof o)r(n,e("select"));else{var a={exports:{}};r(a,i.select),i.clipboardAction=a.exports}}(this,function(t,e){"use strict";function n(t){return t&&t.__esModule?t:{"default":t}}function o(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}var i=n(e),r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol?"symbol":typeof t},a=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}(),c=function(){function t(e){o(this,t),this.resolveOptions(e),this.initSelection()}return t.prototype.resolveOptions=function t(){var e=arguments.length<=0||void 0===arguments[0]?{}:arguments[0];this.action=e.action,this.emitter=e.emitter,this.target=e.target,this.text=e.text,this.trigger=e.trigger,this.selectedText=""},t.prototype.initSelection=function t(){this.text?this.selectFake():this.target&&this.selectTarget()},t.prototype.selectFake=function t(){var e=this,n="rtl"==document.documentElement.getAttribute("dir");this.removeFake(),this.fakeHandlerCallback=function(){return e.removeFake()},this.fakeHandler=document.body.addEventListener("click",this.fakeHandlerCallback)||!0,this.fakeElem=document.createElement("textarea"),this.fakeElem.style.fontSize="12pt",this.fakeElem.style.border="0",this.fakeElem.style.padding="0",this.fakeElem.style.margin="0",this.fakeElem.style.position="absolute",this.fakeElem.style[n?"right":"left"]="-9999px",this.fakeElem.style.top=(window.pageYOffset||document.documentElement.scrollTop)+"px",this.fakeElem.setAttribute("readonly",""),this.fakeElem.value=this.text,document.body.appendChild(this.fakeElem),this.selectedText=(0,i.default)(this.fakeElem),this.copyText()},t.prototype.removeFake=function t(){this.fakeHandler&&(document.body.removeEventListener("click",this.fakeHandlerCallback),this.fakeHandler=null,this.fakeHandlerCallback=null),this.fakeElem&&(document.body.removeChild(this.fakeElem),this.fakeElem=null)},t.prototype.selectTarget=function t(){this.selectedText=(0,i.default)(this.target),this.copyText()},t.prototype.copyText=function t(){var e=void 0;try{e=document.execCommand(this.action)}catch(n){e=!1}this.handleResult(e)},t.prototype.handleResult=function t(e){e?this.emitter.emit("success",{action:this.action,text:this.selectedText,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)}):this.emitter.emit("error",{action:this.action,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)})},t.prototype.clearSelection=function t(){this.target&&this.target.blur(),window.getSelection().removeAllRanges()},t.prototype.destroy=function t(){this.removeFake()},a(t,[{key:"action",set:function t(){var e=arguments.length<=0||void 0===arguments[0]?"copy":arguments[0];if(this._action=e,"copy"!==this._action&&"cut"!==this._action)throw new Error('Invalid "action" value, use either "copy" or "cut"')},get:function t(){return this._action}},{key:"target",set:function t(e){if(void 0!==e){if(!e||"object"!==("undefined"==typeof e?"undefined":r(e))||1!==e.nodeType)throw new Error('Invalid "target" value, use a valid Element');if("copy"===this.action&&e.hasAttribute("disabled"))throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');if("cut"===this.action&&(e.hasAttribute("readonly")||e.hasAttribute("disabled")))throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');this._target=e}},get:function t(){return this._target}}]),t}();t.exports=c})},{select:6}],9:[function(e,n,o){!function(i,r){if("function"==typeof t&&t.amd)t(["module","./clipboard-action","tiny-emitter","good-listener"],r);else if("undefined"!=typeof o)r(n,e("./clipboard-action"),e("tiny-emitter"),e("good-listener"));else{var a={exports:{}};r(a,i.clipboardAction,i.tinyEmitter,i.goodListener),i.clipboard=a.exports}}(this,function(t,e,n,o){"use strict";function i(t){return t&&t.__esModule?t:{"default":t}}function r(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){if(!t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!e||"object"!=typeof e&&"function"!=typeof e?t:e}function c(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function s(t,e){var n="data-clipboard-"+t;if(e.hasAttribute(n))return e.getAttribute(n)}var l=i(e),u=i(n),f=i(o),d=function(t){function e(n,o){r(this,e);var i=a(this,t.call(this));return i.resolveOptions(o),i.listenClick(n),i}return c(e,t),e.prototype.resolveOptions=function t(){var e=arguments.length<=0||void 0===arguments[0]?{}:arguments[0];this.action="function"==typeof e.action?e.action:this.defaultAction,this.target="function"==typeof e.target?e.target:this.defaultTarget,this.text="function"==typeof e.text?e.text:this.defaultText},e.prototype.listenClick=function t(e){var n=this;this.listener=(0,f.default)(e,"click",function(t){return n.onClick(t)})},e.prototype.onClick=function t(e){var n=e.delegateTarget||e.currentTarget;this.clipboardAction&&(this.clipboardAction=null),this.clipboardAction=new l.default({action:this.action(n),target:this.target(n),text:this.text(n),trigger:n,emitter:this})},e.prototype.defaultAction=function t(e){return s("action",e)},e.prototype.defaultTarget=function t(e){var n=s("target",e);return n?document.querySelector(n):void 0},e.prototype.defaultText=function t(e){return s("text",e)},e.prototype.destroy=function t(){this.listener.destroy(),this.clipboardAction&&(this.clipboardAction.destroy(),this.clipboardAction=null)},e}(u.default);t.exports=d})},{"./clipboard-action":8,"good-listener":4,"tiny-emitter":7}]},{},[9])(9)});

new Clipboard('.copy-to-clip');

// JavaScript Document Consolidating misc scripts.
/*
    jQuery Masked Input Plugin
    Copyright (c) 2007 - 2015 Josh Bush (digitalbush.com)
    Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license)
    Version: 1.4.1
*/
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){var b,c=navigator.userAgent,d=/iphone/i.test(c),e=/chrome/i.test(c),f=/android/i.test(c);a.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},autoclear:!0,dataName:"rawMaskFn",placeholder:"_"},a.fn.extend({caret:function(a,b){var c;if(0!==this.length&&!this.is(":hidden"))return"number"==typeof a?(b="number"==typeof b?b:a,this.each(function(){this.setSelectionRange?this.setSelectionRange(a,b):this.createTextRange&&(c=this.createTextRange(),c.collapse(!0),c.moveEnd("character",b),c.moveStart("character",a),c.select())})):(this[0].setSelectionRange?(a=this[0].selectionStart,b=this[0].selectionEnd):document.selection&&document.selection.createRange&&(c=document.selection.createRange(),a=0-c.duplicate().moveStart("character",-1e5),b=a+c.text.length),{begin:a,end:b})},unmask:function(){return this.trigger("unmask")},mask:function(c,g){var h,i,j,k,l,m,n,o;if(!c&&this.length>0){h=a(this[0]);var p=h.data(a.mask.dataName);return p?p():void 0}return g=a.extend({autoclear:a.mask.autoclear,placeholder:a.mask.placeholder,completed:null},g),i=a.mask.definitions,j=[],k=n=c.length,l=null,a.each(c.split(""),function(a,b){"?"==b?(n--,k=a):i[b]?(j.push(new RegExp(i[b])),null===l&&(l=j.length-1),k>a&&(m=j.length-1)):j.push(null)}),this.trigger("unmask").each(function(){function h(){if(g.completed){for(var a=l;m>=a;a++)if(j[a]&&C[a]===p(a))return;g.completed.call(B)}}function p(a){return g.placeholder.charAt(a<g.placeholder.length?a:0)}function q(a){for(;++a<n&&!j[a];);return a}function r(a){for(;--a>=0&&!j[a];);return a}function s(a,b){var c,d;if(!(0>a)){for(c=a,d=q(b);n>c;c++)if(j[c]){if(!(n>d&&j[c].test(C[d])))break;C[c]=C[d],C[d]=p(d),d=q(d)}z(),B.caret(Math.max(l,a))}}function t(a){var b,c,d,e;for(b=a,c=p(a);n>b;b++)if(j[b]){if(d=q(b),e=C[b],C[b]=c,!(n>d&&j[d].test(e)))break;c=e}}function u(){var a=B.val(),b=B.caret();if(o&&o.length&&o.length>a.length){for(A(!0);b.begin>0&&!j[b.begin-1];)b.begin--;if(0===b.begin)for(;b.begin<l&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}else{for(A(!0);b.begin<n&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}h()}function v(){A(),B.val()!=E&&B.change()}function w(a){if(!B.prop("readonly")){var b,c,e,f=a.which||a.keyCode;o=B.val(),8===f||46===f||d&&127===f?(b=B.caret(),c=b.begin,e=b.end,e-c===0&&(c=46!==f?r(c):e=q(c-1),e=46===f?q(e):e),y(c,e),s(c,e-1),a.preventDefault()):13===f?v.call(this,a):27===f&&(B.val(E),B.caret(0,A()),a.preventDefault())}}function x(b){if(!B.prop("readonly")){var c,d,e,g=b.which||b.keyCode,i=B.caret();if(!(b.ctrlKey||b.altKey||b.metaKey||32>g)&&g&&13!==g){if(i.end-i.begin!==0&&(y(i.begin,i.end),s(i.begin,i.end-1)),c=q(i.begin-1),n>c&&(d=String.fromCharCode(g),j[c].test(d))){if(t(c),C[c]=d,z(),e=q(c),f){var k=function(){a.proxy(a.fn.caret,B,e)()};setTimeout(k,0)}else B.caret(e);i.begin<=m&&h()}b.preventDefault()}}}function y(a,b){var c;for(c=a;b>c&&n>c;c++)j[c]&&(C[c]=p(c))}function z(){B.val(C.join(""))}function A(a){var b,c,d,e=B.val(),f=-1;for(b=0,d=0;n>b;b++)if(j[b]){for(C[b]=p(b);d++<e.length;)if(c=e.charAt(d-1),j[b].test(c)){C[b]=c,f=b;break}if(d>e.length){y(b+1,n);break}}else C[b]===e.charAt(d)&&d++,k>b&&(f=b);return a?z():k>f+1?g.autoclear||C.join("")===D?(B.val()&&B.val(""),y(0,n)):z():(z(),B.val(B.val().substring(0,f+1))),k?b:l}var B=a(this),C=a.map(c.split(""),function(a,b){return"?"!=a?i[a]?p(b):a:void 0}),D=C.join(""),E=B.val();B.data(a.mask.dataName,function(){return a.map(C,function(a,b){return j[b]&&a!=p(b)?a:null}).join("")}),B.one("unmask",function(){B.off(".mask").removeData(a.mask.dataName)}).on("focus.mask",function(){if(!B.prop("readonly")){clearTimeout(b);var a;E=B.val(),a=A(),b=setTimeout(function(){B.get(0)===document.activeElement&&(z(),a==c.replace("?","").length?B.caret(0,a):B.caret(a))},10)}}).on("blur.mask",v).on("keydown.mask",w).on("keypress.mask",x).on("input.mask paste.mask",function(){B.prop("readonly")||setTimeout(function(){var a=A(!0);B.caret(a),h()},0)}),e&&f&&B.off("input.mask").on("input.mask",u),A()})}})});