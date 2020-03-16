//disable ajax cache it solves that the Ajax content doesn't load properly when back button is clicked
$.ajaxSetup({ cache: false });

// selector = "#max_hours", data="01:15:15", ref="auditor.availability.lunch"
function autosave(selector, ref) {
	var data = $(selector).val();
	$.post("/autosave", {
        'data' : data,
        'ref' : ref,
        '_token' : $('meta[name="csrf-token"]').attr('content')
    }, function(data) {
        if(data!=1){
            UIkit.modal.alert(data,{stack: true});
        } else {
            UIkit.notification('<span uk-icon="icon: check"></span> Saved', {pos:'top-right', timeout:1000, status:'success'});
        }
    });
}

function loadListTab(listURI,quickLookupURI,overrideSaveCheck,sortBy,refreshOnly) {
	// check if changes need to be saved.
	var continueToLoad = 1;
	// set the variable for infinite scrolling to use
	window.getContentForListId = listURI;

	if (window.saved !== 1  && overrideSaveCheck !== 1) {
		continueToLoad = 0;
		UIkit.modal.confirm("You have unsaved changes, are you sure you want to continue opening this group of items without saving?").then(function() {
    		continueToLoad = 1;
			window.saved = 1;
			loadListTab(listURI);
			return;
		});
	} else {

		if(continueToLoad === 1) {
			console.log('Loading '.listURI)
			//close the offcanvas bar
			UIkit.offcanvas.hide();
			//make the list tab focused
			$('#list-tab').trigger("click");
			if(refreshOnly !== 1) {
			//unload the content of the detail tab
			$('#detail-tab-content').html('');
			//hide the detail tab
			$('#detail-tab').fadeOut();
			}
			//Load the selected list tab content -
			//We are only using get here so we can test that vars are being passed easily. You may want to convert this to a ajax post to your processor.
			$('#list-tab-content').load(listURI, function(response, status, xhr) {
				  if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}

				    UIkit.modal.alert(msg);
				  }
				});
			console.log('Requested List via ajax: '+listURI);

			//take back to top
			$('#smoothscrollLink').trigger("click");
			return;

		}
	}
}
var timeoutID;
function delayedClick(tabNumber) {
		window.tabNumber = tabNumber;
  		timeoutID = window.setTimeout(clickTab, 300);
	}
function clickTab(){
	var tabNumber = window.tabNumber;
	$('#detail-tab-'+tabNumber+'-content').fadeIn();
	// $('.detail-tab-'+tabNumber).fadeIn();

	//make the detail tab focused

	//reset style property of the thead of the list

}

// works for main tabs and also sub tabs using prefix
function loadProjectTab() {
	loadTab('/projects/view/'+window.selectedProjectKey+'/'+window.selectedAuditId, '4', 0, 0, '', 1);
}
function pmLoadProjectTab() {
	pmLoadTab('/pm-projects/view/'+window.selectedProjectKey+'/'+window.selectedAuditId, '4', 0, 0, '', 1);
}

function loadTab(route, tabNumber, doTheClick=0, loadTitle=0, prefix='', forceReload=0, audit=0) {
	// check if tab already exist, if not create it
	// debugger;
	if($('#'+prefix+'detail-tab-'+tabNumber+'-content').length == 0){
		var newTabContent = '<li><div id="'+prefix+'detail-tab-'+tabNumber+'-content"></div></li>';
        $( newTabContent ).appendTo( $('#tabs') );
	}
	if($('#'+prefix+'detail-tab-'+tabNumber).length == 0){

		var newTabTitle = '<li id="'+prefix+'detail-tab-'+tabNumber+'" onclick="if($(\'#detail-tab-'+tabNumber+'\').hasClass(\'uk-active\')){loadProjectTab();}" class="detail-tab-'+tabNumber+'" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" >';
		newTabTitle = newTabTitle + '<a href=""></a>';
		newTabTitle = newTabTitle + '</li>';
        $( newTabTitle ).appendTo( $('#'+prefix+'top-tabs') );
	}

	// check if tab has already been loaded
	// if content is already there, just switch tab, do not reload
	if($('#'+prefix+'detail-tab-'+tabNumber+'-content').length && $('#'+prefix+'detail-tab-'+tabNumber+'-content').html().length == 0 || forceReload == 1){
		// display spinner
		var tempdiv = '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% auto;"></div></div>';
		$('#'+prefix+'detail-tab-'+tabNumber+'-content').html(tempdiv);

		// get the tab title and statuses
		if(loadTitle){
			$('#'+prefix+'detail-tab-'+tabNumber+' a').load(route+'/title', function(response, status, xhr) {
				if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}

				    UIkit.modal.alert(msg);
				}
			});
		}

		//load the selected detail tab content
		$('#'+prefix+'detail-tab-'+tabNumber+'-content').load(route, function(response, status, xhr) {
			console.log('loadTab() Loading into tab '+prefix+'detail-tab-'+tabNumber+'-content');
			if (status == "error") {
			  	if(xhr.status == "401") {
			  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
			  	} else if( xhr.status == "500"){
			  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
			  	} else {
			  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
			  	}

			    UIkit.modal.alert(msg);
			}

			// if tab is opened by a link, trigger click to switch tab
			if(doTheClick == 1){
				$("#"+prefix+"top-tabs").find($('#'+prefix+'detail-tab-'+tabNumber)).trigger("click");
			}

			//take back to top
		 	$('#smoothscrollLink').trigger("click");
		});
	}else{
		if(doTheClick == 1){
			$("#"+prefix+"top-tabs").find($('#'+prefix+'detail-tab-'+tabNumber)).trigger("click");
		}
		//take back to top
		$('#smoothscrollLink').trigger("click");
	}



}

function pmLoadTab(route, tabNumber, doTheClick=0, loadTitle=0, prefix='', forceReload=0, audit=0) {
	// check if tab already exist, if not create it
	// debugger;
	if($('#'+prefix+'detail-tab-'+tabNumber+'-content').length == 0){
		var newTabContent = '<li><div id="'+prefix+'detail-tab-'+tabNumber+'-content"></div></li>';
        $( newTabContent ).appendTo( $('#tabs') );
	}
	if($('#'+prefix+'detail-tab-'+tabNumber).length == 0){

		var newTabTitle = '<li id="'+prefix+'detail-tab-'+tabNumber+'" onclick="if($(\'#detail-tab-'+tabNumber+'\').hasClass(\'uk-active\')){pmLoadProjectTab();}" class="detail-tab-'+tabNumber+'" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" >';
		newTabTitle = newTabTitle + '<a href=""></a>';
		newTabTitle = newTabTitle + '</li>';
        $( newTabTitle ).appendTo( $('#'+prefix+'top-tabs') );
	}

	// check if tab has already been loaded
	// if content is already there, just switch tab, do not reload
	if($('#'+prefix+'detail-tab-'+tabNumber+'-content').length && $('#'+prefix+'detail-tab-'+tabNumber+'-content').html().length == 0 || forceReload == 1){
		// display spinner
		var tempdiv = '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% auto;"></div></div>';
		$('#'+prefix+'detail-tab-'+tabNumber+'-content').html(tempdiv);

		// get the tab title and statuses
		if(loadTitle){
			$('#'+prefix+'detail-tab-'+tabNumber+' a').load(route+'/title', function(response, status, xhr) {
				if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}

				    UIkit.modal.alert(msg);
				}
			});
		}

		//load the selected detail tab content
		$('#'+prefix+'detail-tab-'+tabNumber+'-content').load(route, function(response, status, xhr) {
			console.log('loadTab() Loading into tab '+prefix+'detail-tab-'+tabNumber+'-content');
			if (status == "error") {
			  	if(xhr.status == "401") {
			  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
			  	} else if( xhr.status == "500"){
			  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
			  	} else {
			  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
			  	}

			    UIkit.modal.alert(msg);
			}

			// if tab is opened by a link, trigger click to switch tab
			if(doTheClick == 1){
				$("#"+prefix+"top-tabs").find($('#'+prefix+'detail-tab-'+tabNumber)).trigger("click");
			}

			//take back to top
		 	$('#smoothscrollLink').trigger("click");
		});
	}else{
		if(doTheClick == 1){
			$("#"+prefix+"top-tabs").find($('#'+prefix+'detail-tab-'+tabNumber)).trigger("click");
		}
		//take back to top
		$('#smoothscrollLink').trigger("click");
	}



}

// function loadDetailTab(typeId,detailId,tabNumber,overrideSaveCheck,subTabType) {
// 	// check if changes need to be saved.

// 	var continueToLoad = 1;
// 	if (window.saved !== 1 && overrideSaveCheck !== 1) {
// 		continueToLoad = 0;
// 		UIkit.modal.confirm("You have unsaved changes, are you sure you want to continue loading this item without saving?").then(function() {
//     		continueToLoad = 1;
// 			window.saved = 1;
// 			loadDetailTab(typeId,detailId,tabNumber,overrideSaveCheck,refreshOnly);
// 			return;
// 		});
// 	} else {
// 		if(continueToLoad === 1) {
// 			//take back to top
// 			$('#smoothscrollLink').trigger("click");
// 			//hide the detail tab
// 			$('#detail-tab-'+tabNumber+'-content').html('').fadeOut();
// 			$('.detail-tab-'+tabNumber).fadeOut();
// 			//unload the content of the detail tab
// 			$('#detail-tab-'+tabNumber+'-content').html('');
// 			$('#detail-tab-'+tabNumber).trigger("click");
// 			//load the selected detail tab content
// 			$('#detail-tab-'+tabNumber+'-content').load(''+typeId+detailId, function(response, status, xhr) {
// 				  if (status == "error") {
// 				  	if(xhr.status == "401") {
// 				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
// 				  	} else if( xhr.status == "500"){
// 				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
// 				  	} else {
// 				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
// 				  	}

// 				    UIkit.modal.alert(msg);
// 				  }
// 				});
// 			console.log('Requested '+typeId+detailId+' and loaded into #detail-tab-'+tabNumber+'-content');
// 			//show the detail tab
// 			delayedClick(tabNumber);
// 			$('#list-tab').css("position", "");
// 			$('#list-tab').css("top", "");
// 			$('#list-tab').css("width", "");
// 			$('#list-tab').removeClass('uk-active');

// 			// store the subtab number to have it open when the page loads
// 			window.subTabType = subTabType;

// 			// STORE THE CURRENT DETAIL ID IN THE WINDOW PORTION OF THE DOM TO ACCESS IN OTHER SCRIPTS
// 			window.currentDetailId = detailId;
// 			return;
// 		}
// 	}
// }

function enableField(fieldId){
	$(fieldId).prop('disabled', false);
	$(fieldId).addClass('editing');
	$(fieldId).focus();
	console.log('Enabled field '+fieldId);
}
function disableField(fieldId) {
	$(fieldId).prop('disabled', true);
	$(fieldId).removeClass('editing');
	console.log('Disabled field '+fieldId);
}

// function loadSearchResult(detailTypeId,detailItemId) {
// 	if (window.saved !== 1) {
// 		continueToLoad = 0;
// 		UIkit.modal.confirm("You have unsaved changes, are you sure you want to open this item without saving?").then(function() {
//     		continueToLoad = 1;
// 			window.saved = 1;

// 			loadListTab(16,encodeURI($('#quick-lookup-box').val()),1);
// 			loadDetailTab(detailTypeId,detailItemId,1);
// 			return;

// 		});
// 	}else {

// 		loadListTab(16,encodeURI($('#quick-lookup-box').val()));
// 		loadDetailTab(detailTypeId,detailItemId);
// 	}
// 	$('#quick-lookup-box').val('');
// 	return;
// }

function logout() {
	//UIkit.offcanvas.hide();
	var continueToLoad = 1;
	if (window.saved !== 1) {

		continueToLoad = 0;
		UIkit.modal.confirm("Leaving so soon!\n\nLooks like you have unsaved changes, are you sure you want to logout without saving?").then(function(){
    		continueToLoad = 1;
			event.preventDefault();
            document.getElementById('logout-form').submit();
            console.log('logout form submitted');
			window.saved = 1;
			return;
		},function(){
			console.log('logout cancelled');
		});
	} else {
		if(continueToLoad === 1) {
			console.log('initiating logout confirmation');
  			UIkit.modal.confirm("We were just starting to have fun!\n\nAre you sure you want to logout?").then(
  				function(){
				//event.preventDefault();
	            document.getElementById('logout-form').submit();
	            console.log('logout form submitted');
				},function(){
					console.log('logout cancelled');
				}
			);

			return;
		}
	}
}
function dateExpireFilter() {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(1,null,null,null,1,null,null,null,$('#date-expire-from').val(),$('#date-expire-through').val());
}
function dateAgingFilter() {
	UIkit.modal('#dynamic-modal').hide();
	$('#parentHTML').removeClass('uk-modal-page');
	loadListTab(1,null,null,null,1,$('#date-aging-from').val(),$('#date-aging-through').val());
}

// reload messages
function reloadUnseenMessages(){
	$.get( "/communications/unseen", function(data) {
		if(data){
			var unseen_output = '';
			unseen_output = unseen_output + '<a class="uk-light" href="" onclick="return false;">| ALERTS <span class="uk-badge uk-badge-notification uk-badge-warning" style="vertical-align:top;line-height: initial;padding: 0;height: 18px;">'+data['count']+'</span></a>';
	        unseen_output = unseen_output + '<div uk-dropdown>';
	        unseen_output = unseen_output + '<ul class="uk-nav uk-nav-dropdown">';
	        unseen_output = unseen_output + '<li class="uk-nav-header">Messages</li>';

	        $.each(data['messages'], function(index, message){
	        	unseen_output = unseen_output + '<li>';
				if(message['parent_id']){
					unseen_output = unseen_output + '<a href="#" onclick="dynamicModalLoad(\'communication/0/replies/'+message['parent_id']+'\')">';
				}else{
					unseen_output = unseen_output + '<a href="#" onclick="dynamicModalLoad(\'communication/0/replies/'+message['communication_id']+'\')">';
				}
				unseen_output = unseen_output + '<i class="a-envelope-4"></i>"'+message['summary']+'" from '+message['owner_name'];
				if(message['parcel_id'] != null){
					unseen_output = unseen_output + 'for parcel '+message['parcel_id'];
				}
				unseen_output = unseen_output + '</a></li>';
	        });

	        unseen_output = unseen_output + '</ul>';
	        unseen_output = unseen_output + '</div>';

	        $( "#unseen_communications" ).html(unseen_output);
		}
	});
}

// DYNAMIC MODAL FUNCTION //
function dynamicModalLoad(modalSource,fullscreen,warnAboutSave,fixedHeight=0,inmodallevel) {
	if(inmodallevel > 0){
		// copy modal divs and rename ids if it doesn't alreay exist
		if($('#dynamic-modal-'+inmodallevel).length){
			var newmodal = $('#dynamic-modal-'+inmodallevel);
			var newmodalsize = $('#modal-size-'+inmodallevel);
			var newmodalcontent = $('#dynamic-modal-content-'+inmodallevel);
		}else{
			var newmodal = $('#dynamic-modal').clone().prop('id', 'dynamic-modal-'+inmodallevel);
			$('#dynamic-modal').after(newmodal);
			var newmodalsize = $(newmodal).find('#modal-size').prop('id', 'modal-size-'+inmodallevel);
			var newmodalcontent = $(newmodal).find('#dynamic-modal-content').prop('id', 'dynamic-modal-content-'+inmodallevel);
		}
	}else{
		var newmodal = $('#dynamic-modal');
		var newmodalsize = $('#modal-size');
		var newmodalcontent = $('#dynamic-modal-content');
	}

	$(newmodalsize).removeAttr('style');

	// UIkit.offcanvas.hide();
	var continueToLoad = 1;
	if (window.saved !== 1 && warnAboutSave === 1) {
		var goober = UIkit.modal.alert("You have changes you haven't saved yet. You might want to cancel this window and save your changes first.");
	}
	if(continueToLoad === 1) {
		$(newmodalcontent).html('<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>');
		$(newmodalcontent).load('/modals/'+modalSource, function(response, status, xhr) {
				  if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}

				    UIkit.modal.alert(msg);
				  }
				});
		console.log('loaded /modals/'+modalSource+' into #dynamic-modal-content');
		if(fullscreen === 1) {
			// add class to modal so it opens full screen.
			$(newmodalsize).addClass('uk-modal-dialog-blank');
			$(newmodal).addClass('fullscreen');
			//$(newmodalcontent).addClass('uk-height-viewport');
			$(newmodalsize).removeClass('modal-fixed-height');

		} else {

			$(newmodal).removeClass('fullscreen');

			if(fixedHeight === 1){
				$(newmodalsize).addClass('modal-fixed-height');
				$(newmodalsize).removeClass('uk-modal-dialog-blank');
			}else{
				// remove the class in case it is still there.
				$(newmodalsize).removeClass('uk-modal-dialog-blank');
				// $(newmodalcontent).removeClass('uk-height-viewport');
			}
		}

		UIkit.modal(newmodal, {center: true, bgclose: false, keyboard:false,  stack:true}).show();
	}
}


function dynamicModalClose(inmodallevel) {
	if(inmodallevel > 0){
		UIkit.modal('#dynamic-modal-'+inmodallevel).hide();
	}else{
		UIkit.modal('#dynamic-modal').hide();
	}

	//REMOVE CLASS FROM HTML TAG THAT BLOCKS THE MENU (workaround)
	$('#parentHTML').removeClass('uk-modal-page');

}

function listItemFileUploadModalUpdate(fileId) {
	window.listItemFileUploadFileId = fileId;
	$('#dynamic-modal-content').load('/modals/list-item-upload.html?fileId='+fileId, function(response, status, xhr) {
				  if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}

				    UIkit.modal.alert(msg);
				  }
				});
	UIkit.modal('#dynamic-modal').show();
}

function isACheckBoxChecked(className,idToShow)
{
    var checkboxes=document.getElementsByClassName(className);
    var okay=false;
    for(var i=0,l=checkboxes.length;i<l;i++)
    {
        if(checkboxes[i].checked)
        {
            okay=true;
            break;
        }
    }
    if(okay){$(idToShow).slideDown();}
    else {$(idToShow).slideUp();}
}

function checkTextArea(idOfTextArea,idToShow) {
	//call this function as an onChange on text areas that you want to trigger another section to be visible once they type into them.
	console.log('Text Area Length Check: '+$(idOfTextArea).val().length);
	if($(idOfTextArea).val().length > 2) {
		$(idToShow).slideDown();
	} else {
		$(idToShow).slideUp();
	}

}


// When the user makes any change to any page, set this to 0 to signify changes need to be saved - it needs to be set to window.saved global -->

window.saved = 1;

// warn user that narrowing up their window could be bad -->

/*$.event.add(window, 'resize', resizeFrame);*/
window.warnedResize = 0;
function resizeFrame() {

	/*if($( window ).width() < 1480) {
		window.warnedResize++;
		if(window.warnedResize === 1) {

			UIkit.modal.alert('Uh oh - your window is getting a little tight for me to look my best for you. If things start to look weird, try making my window bigger.');
		} else if(window.warnedResize > 100) {
			window.warnedResize = 0;
		}
	}*/

}


// INFINITE SCROLLING
window.gettingHtml = 0;
$(document).ready(function() {
	var win = $(window);

	// Each time the user scrolls
	win.scroll(function() {
		if($('#next-page').attr('href') != undefined){
			// Make sure we should be checking for more content
			if(window.getContentForListId > 0 && window.gettingHtml < 1) {
			// End of the document reached?
				if ($(document).height() - win.height() === win.scrollTop() ) {
					$('#loading').show();
					window.gettingHtml = 1;
					$.ajax({
						// update url with actual url to get paginated content //
						url: $('#next-page').attr('href'),
						dataType: 'html',
						success: function(html) {
							//recheck in case we changed tab
							$('#results-list').append(html);
							$('#loading').hide();
							window.gettingHtml = 0;
						}
					});
					console.log('Infinite Scroll loaded in content from '+$('#next-page').attr('href'));
				}
			} else {
				// console.log('Did not load infinite scroll: conditions window.gettingHtml ='+window.gettingHtml);
			}
		}

	});
	$('#dynamic-modal').on({

    'show.uk.modal': function(){
		// if you need to run any scripts when the modal opens - put them here - note this happens on all modal opens.
        console.log("Dynamic Modal Opened.");
    },

    'hide.uk.modal': function(){
        // console.log("Dynamic Modal Closed and Content Removed.");
		// $('#dynamic-modal-content').html('<!--LOADING CONENT --><div style="height:500px;>&nbsp;</div>"');
    }
});
});

// Require save if changes were made
$(document).on("change", '.change-watch', function(){
	if(window.saved === 1) {
		console.log('Change detected - turning on save notice');
		window.saved = 0;
	}
});

// Open an address field parts to edit it.

function addressField(addressFieldId,detailsReferenceId) {
	// concept is that we pass the detailsReferenceId to the load request for the address field modal view - so it pulls the relevant address info into the fields.

	// setting a global so address update form knows which address field to update
	window.addressToUpdate = addressFieldId;




}

// Initial load items
resizeFrame();
window.getContentForListId = 0;