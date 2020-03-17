function toggleCritical() {


	$.get( '/session/filters/audit-hidenoncritical/', function( data ) {
       // reload
		// $('#detail-tab-1').trigger("click");
		// UIkit.switcher('#top-tabs').show(0);
		$(".notcritical").fadeToggle();
		$('.btnToggleCritical').toggle();
    });


}

function sortAuditList(sortBy, sortOrder, inputClass='') {
	// 'audit-sort-by'
    // 'audit-sort-order'

    $('#audits').fadeOut('slow');

    // capture input value if any
    if(inputClass != ''){
    	var filter = '';
	    var filterId = 0;

	    if( $('.'+inputClass).val().length ){
	    	// clear all other session variables
	    	$.get( '/session/filters/filter-search-project-input/');
	    	$.get( '/session/filters/filter-search-pm-input/');
	    	$.get( '/session/filters/filter-search-address-input/');

	    	// set new filter
	    	filter = $('.'+inputClass).val();
	    	$.get( '/session/filters/'+inputClass+'/'+filter, function( data ) {});
	    }

    }

	$.get( '/session/filters/audit-sort-by/'+sortBy, function( data ) {
		$.get( '/session/filters/audit-sort-order/'+sortOrder, function( data ) {
			//?filter="+filter+"&filterId="+filterId
			loadTab("dashboard/audits", "1", 0, 0, '', 1);

      	});
    });
}
function pmSortAuditList(sortBy, sortOrder, inputClass='') {
	// 'audit-sort-by'
    // 'audit-sort-order'

    $('#audits').fadeOut('slow');

    // capture input value if any
    if(inputClass != ''){
    	var filter = '';
	    var filterId = 0;

	    if( $('.'+inputClass).val().length ){
	    	// clear all other session variables
	    	$.get( '/pmsession/filters/filter-search-project-input/');
	    	$.get( '/pmsession/filters/filter-search-pm-input/');
	    	$.get( '/pmsession/filters/filter-search-address-input/');

	    	// set new filter
	    	filter = $('.'+inputClass).val();
	    	$.get( '/pmsession/filters/'+inputClass+'/'+filter, function( data ) {});
	    }

    }

	$.get( '/pmsession/filters/audit-sort-by/'+sortBy, function( data ) {
		$.get( '/pmsession/filters/audit-sort-order/'+sortOrder, function( data ) {
			//?filter="+filter+"&filterId="+filterId
			loadTab("dashboard/pmaudits", "1", 0, 0, '', 1);

      	});
    });
}

function filterAudits(type, value=''){
	$.get( '/session/filters/'+type+'/'+value, function( data ) {
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
			$('#auditstable').html(tempdiv);
		setTimeout(function () {
		    loadTab("dashboard/audits", "1", 0, 0, '', 1);
		}, 1000);

    });
}

function pmFilterAudits(type, value=''){
	$.get( '/pmsession/filters/'+type+'/'+value, function( data ) {
		var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
			$('#auditstable').html(tempdiv);
		setTimeout(function () {
		    loadTab("dashboard/pmaudits", "1", 0, 0, '', 1);
		}, 1000);

    });
}

function filterAuditList(element, searchClass){
	// clear all other filters
	// $('.filter-box').not(element).val('');

	var value = $(element).val().toLowerCase();

	// if(value == '') {value = 0;}

	$.get( '/session/filters/'+searchClass+'/'+value, function( data ) {
       loadTab("dashboard/audits", "1", 0, 0, '', 1);
    });

	// $('tr[id^="audit-r-"]').each(function() {
	// 	var parentElement = this;
	// 	var found = 0; // we may look through multiple fields with the same class

	// 	$(this).find('.'+searchClass).each(function() {
 //    		if($(this).text().toLowerCase().search(value) > -1) {
 //    			if(found == 0){
 //    				found = 1;
 //    				$(parentElement).show();
 //    			}
 //    		}else{
 //    			if(found == 0){
 //    				$(parentElement).hide();
 //    			}
 //    		}
 //    	});
	// });
}

function pmFilterAuditList(element, searchClass){
	// clear all other filters
	// $('.filter-box').not(element).val('');

	var value = $(element).val().toLowerCase();

	// if(value == '') {value = 0;}

	$.get( '/pmsession/filters/'+searchClass+'/'+value, function( data ) {
       loadTab("dashboard/pmaudits", "1", 0, 0, '', 1);
    });

	// $('tr[id^="audit-r-"]').each(function() {
	// 	var parentElement = this;
	// 	var found = 0; // we may look through multiple fields with the same class

	// 	$(this).find('.'+searchClass).each(function() {
 //    		if($(this).text().toLowerCase().search(value) > -1) {
 //    			if(found == 0){
 //    				found = 1;
 //    				$(parentElement).show();
 //    			}
 //    		}else{
 //    			if(found == 0){
 //    				$(parentElement).hide();
 //    			}
 //    		}
 //    	});
	// });
}

function toggleArchivedAudits() {
	$(".archived-icon").toggle();
}

function pmToggleArchivedAudits() {
	$(".archived-icon").toggle();
}

function createAudits(){
	console.log("create audits clicked");
}

function projectDetails(id, target, buildingcount = 10, reload = 0) {
	if ($('#audit-r-'+target+'-buildings').length && reload == 0){

		// close own details
		$('#audit-r-'+target+'-buildings').remove();
		$('tr[id^="audit-r-"]').show();
		$('html, body').animate({
			scrollTop: $('#audit-r-'+target).offset().top - 59
			}, 500, 'linear');
	}else{
		if(reload == 1){
			$('#audit-r-'+target+'-buildings').remove();
		}

		// scroll to row early
    	$('html, body').animate({
			scrollTop: $('#audit-r-'+target).offset().top - 59
			}, 500, 'linear');

		// close all details
		$('tr[id$="-buildings"]').remove();
		$('tr[id^="audit-r-"]').not( 'tr[id="audit-r-'+target+'"]' ).hide();

		// open the expanded div early based on expected number of buildings
		if($('#audit-r-'+target).hasClass('notcritical')){
			var tempdiv = '<tr id="audit-r-'+target+'-buildings" class="notcritical rowinset"><td colspan="10">';
		}else{
			var tempdiv = '<tr id="audit-r-'+target+'-buildings" class="rowinset"><td colspan="10">';
		}

    	if(buildingcount){
    		var tempdivheight = 150 * buildingcount;
    		tempdiv = tempdiv + '<div style="height:'+tempdivheight+'px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
    	}
    	tempdiv = tempdiv + '</td></tr>';
    	$('#audit-r-'+target).after(tempdiv);

		// fetch and display new details
		var url = 'dashboard/audits/'+id+'/buildings';
	    $.get(url, {
        	'context' : 'audits',
            'target' : target
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the buildings' information.");
                } else {

					$('#audit-r-'+target+'-buildings').html(data);
            	}
	    });
	}
}

function pmProjectDetails(id, target, buildingcount = 10, reload = 0) {
	if ($('#audit-r-'+target+'-buildings').length && reload == 0){

		// close own details
		$('#audit-r-'+target+'-buildings').remove();
		$('tr[id^="audit-r-"]').show();
		$('html, body').animate({
			scrollTop: $('#audit-r-'+target).offset().top - 59
			}, 500, 'linear');
	}else{
		if(reload == 1){
			$('#audit-r-'+target+'-buildings').remove();
		}

		// scroll to row early
    	$('html, body').animate({
			scrollTop: $('#audit-r-'+target).offset().top - 59
			}, 500, 'linear');

		// close all details
		$('tr[id$="-buildings"]').remove();
		$('tr[id^="audit-r-"]').not( 'tr[id="audit-r-'+target+'"]' ).hide();

		// open the expanded div early based on expected number of buildings
		if($('#audit-r-'+target).hasClass('notcritical')){
			var tempdiv = '<tr id="audit-r-'+target+'-buildings" class="notcritical rowinset"><td colspan="10">';
		}else{
			var tempdiv = '<tr id="audit-r-'+target+'-buildings" class="rowinset"><td colspan="10">';
		}

    	if(buildingcount){
    		var tempdivheight = 150 * buildingcount;
    		tempdiv = tempdiv + '<div style="height:'+tempdivheight+'px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
    	}
    	tempdiv = tempdiv + '</td></tr>';
    	$('#audit-r-'+target).after(tempdiv);

		// fetch and display new details
		var url = 'dashboard/pmaudits/'+id+'/buildings';
	    $.get(url, {
        	'context' : 'audits',
            'target' : target
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the buildings' information.");
                } else {

					$('#audit-r-'+target+'-buildings').html(data);
            	}
	    });
	}
}

function buildingDetails(id, auditid, target, targetaudit, detailcount=10, context='audits') {
	// context to reuse function on multiple tabs
	if(context == 'project-details'){
		var scrollToTarget = '#building-'+context+'-r-'+target;
	}else{
		var scrollToTarget = '#audit-r-'+targetaudit;
	}

	// scroll to row early
    $('html, body').animate({
		scrollTop: $(scrollToTarget).offset().top - 59
	}, 500, 'linear');

	if ($('#building-'+context+'-r-'+target+'-details').length){

		if ($('#building-'+context+'-r-'+target).attr('expanded')){
			$('#building-'+context+'-r-'+target).removeAttr('expanded');
		}

		// close own details
		$('#building-'+context+'-r-'+target+'-details').slideUp( "slow", function() {
			$(this).remove();
			});
		// unblur other building rows
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-r-'+target+'"]' ).slideDown();
		if(context != 'project-details'){
			$('.rowinset-top').slideDown();
			$('.rowinset-bottom').slideDown();
		}
		$('div[id^="building-'+context+'-r-"]').removeClass('blur');
	}else{

    	if ($('#building-'+context+'-r-'+target).attr('expanded')){
			 $('#building-'+context+'-r-'+target).removeAttr('expanded');
			// close own details

			$('#inspection-'+context+'-main-'+target+'-container').fadeOut("slow");
			$('#inspection-'+context+'-menus-'+target+'-container').fadeOut("slow");
			$('#inspection-'+context+'-tools-'+target+'-container').fadeOut("slow", function() {
			    $('#inspection-'+context+'-tools-switch-'+target).fadeIn( "slow", function() {
				    // Animation complete
				  });
			    // $('div[id^="building-r-"]').not( 'div[id="building-r-'+target+'"]' ).slideDown();
				// unblur other building inspection rows
				// $('div[id^="building-r-"]').removeClass('blur');
			 });
		}

		// close all details
		$('div[id$="-details"]').remove();
		// unblur other building rows
		$('div[id^="building-'+context+'-r-"]').removeClass('blur');

		// blur all other building rows
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).addClass('blur');
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).slideUp();
		if(context != 'project-details'){
			$('.rowinset-top').slideUp();
			$('.rowinset-bottom').slideUp();
		}

    	// open the expanded div early based on expected number of buildings
    	var tempdiv = '<div id="building-'+context+'-r-'+target+'-details" class="rowinset indent nobox">';
    	if(detailcount){
    		var tempdivheight = 150 * detailcount;
    		tempdiv = tempdiv + '<div style="height:'+tempdivheight+'px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
    	}
    	tempdiv = tempdiv + '</div>';
    	$('#building-'+context+'-r-'+target).after(tempdiv);

		// fetch and display new details
		var url = 'dashboard/audits/'+auditid+'/building/'+id+'/details';
	    $.get(url, {
            'target' : target,
            'targetaudit' : targetaudit,
            'context' : context
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the building details' information.");
                } else {
                	console.log('loading details in new divs '+'#building-'+context+'-r-'+target);
                	$('#building-'+context+'-r-'+target).attr( "expanded", true );
					$('#building-'+context+'-r-'+target+'-details').html(data);
            	}
	    });
	}
}

function loadInspectionMenu(data, id, context='audits', level='', auditid='') {
	var inspectionLeftTemplate = $('#inspection-left-template').html();
	var inspectionMenuItemTemplate = $('#inspection-menu-item-template').html();

	var unitOrBuildingOrProjectId = null;
	var unitOrBuildingOrProject = null;
// console.log(data);
	if(data.detail.unit_id != null){
		unitOrBuildingOrProjectId = data.detail.unit_id;
		unitOrBuildingOrProject = 'unit';
	} else if(data.detail.building_id != null){
		unitOrBuildingOrProjectId = data.detail.building_id;
		unitOrBuildingOrProject = 'building';
	} else if(data.detail.project_id != null){
		unitOrBuildingOrProjectId = data.detail.project_id;
		unitOrBuildingOrProject = 'project';
	}

	var addAmenityButton = '<button class="uk-button tool-add-area uk-link" onclick="addAmenity(\'tplId\', \'tplBuildingOrUnit\',0,0,\'auditid\');"><i class="a-circle-plus"></i> AREA</button>';

	addAmenityButton = addAmenityButton.replace(/tplId/g, unitOrBuildingOrProjectId);
	addAmenityButton = addAmenityButton.replace(/tplBuildingOrUnit/g, unitOrBuildingOrProject);
	addAmenityButton = addAmenityButton.replace(/auditid/g, auditid);

	var menus = addAmenityButton;
	var newmenu = '';
	data.menu.forEach(function(menuitem) {
		newmenu = inspectionMenuItemTemplate;
		newmenu =  newmenu.replace(/menuName/g, menuitem.name);
		newmenu = newmenu.replace(/menuAction/g, menuitem.action);
		newmenu = newmenu.replace(/menuTarget/g, id);
		newmenu = newmenu.replace(/menuAudit/g, menuitem.audit_id);
		newmenu = newmenu.replace(/menuBuilding/g, menuitem.building_id);
		newmenu = newmenu.replace(/menuUnit/g, menuitem.unit_id);
		newmenu = newmenu.replace(/menuLevel/g, level);
		newmenu = newmenu.replace(/menuIcon/g, menuitem.icon);
		newmenu = newmenu.replace(/menuStatus/g, menuitem.status);
		menus = menus + newmenu.replace(/menuStyle/g, menuitem.style);
	});


	$('#inspection-'+context+'-'+level+'menus-'+id).html(inspectionLeftTemplate);
	$('#inspection-'+context+'-'+level+'menus-'+id+' .inspection-menu').html(menus);
	$('#inspection-'+context+'-'+level+'menus-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	  });
}

function loadInspectionTools(data, id, context='audits', level = '') {
	var inspectionToolsTemplate = $('#inspection-tools-template').html();
	var unitOrBuildingOrProjectId = null;
	var unitOrBuildingOrProject = null;

	if(data.detail.unit_id != null){
		unitOrBuildingOrProjectId = data.detail.unit_id;
		unitOrBuildingOrProject = 'unit';
	} else if(data.detail.building_id != null){
		unitOrBuildingOrProjectId = data.detail.building_id;
		unitOrBuildingOrProject = 'building';
	} else if(data.detail.project_id != null){
		unitOrBuildingOrProjectId = data.detail.project_id;
		unitOrBuildingOrProject = 'project';
	}

	// inspectionToolsTemplate = inspectionToolsTemplate.replace(/tplId/g, unitOrBuildingOrProjectId);
	// inspectionToolsTemplate = inspectionToolsTemplate.replace(/tplBuildingOrUnit/g, unitOrBuildingOrProject);

	$('#inspection-'+context+'-'+level+'tools-'+id).html(inspectionToolsTemplate);
	$('#inspection-'+context+'-'+level+'tools-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	    loadInspectionComments(data.comments, id, context, level);
	});

}

function loadInspectionMain(data, id, context='audits', level = '') {
	var inspectionMainTemplate = $('#inspection-areas-template').html();
	var inspectionAreaTemplate = $('#inspection-area-template').html();

	var areas = '';
	var newarea = '';

	if(data.length == 0){
		console.log("no amenity found");
		areas = "No amenity inspection area found.";
	}else{
		data.forEach(function(area) {
			newarea = inspectionAreaTemplate;
			newarea = newarea.replace(/areaContext/g, context);
			newarea = newarea.replace(/areaRowId/g, area.id);
			newarea = newarea.replace(/areaName/g, area.name); // missing
			//debugger;
			if(area.status == 'fileaudit' || area.status == ' fileaudit') {
				newarea = newarea.replace(/fileHiddenStatus/g, 'uk-hidden');
				newarea = newarea.replace(/fileShowStatus uk-hidden/g, 'show');
			}
			newarea = newarea.replace(/areaStatus/g, area.status);  // missing
			newarea = newarea.replace(/areaAuditorId/g, area.auditor_id);  // missing
			newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);  // missing
			newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);  // missing
			newarea = newarea.replace(/areaCompletedIcon/g, area.completed_icon);
			newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);  // missing
			newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
			newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
			newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
			newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
			newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
			newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);
			newarea = newarea.replace(/areaFILEStatus/g, area.finding_file_status);

			newarea = newarea.replace(/areaDataAudit/g, area.audit_id);
			newarea = newarea.replace(/areaDataBuilding/g, area.building_id);
			newarea = newarea.replace(/areaDataArea/g, area.unit_id);
			newarea = newarea.replace(/areaDataAmenity/g, area.id);

			newarea = newarea.replace(/areaAuditorColor/g, area.auditor_color);
			areas = areas + newarea.replace(/areaDataHasFindings/g, area.has_findings);


		});

	}

	inspectionMainTemplate = inspectionMainTemplate.replace(/areaContext/g, context);
	$('#inspection-'+context+'-'+level+'main-'+id).html(inspectionMainTemplate);
	$('#inspection-'+context+'-'+level+'main-'+id+' .inspection-areas').html(areas);
	$('#inspection-'+context+'-'+level+'main-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	  });

}

function formatCommentType(item, type) {
	var inspectionCommentPhotosTemplate = '<div class="photo-gallery" uk-slider><div class="uk-position-relative uk-visible-toggle uk-light"><ul class="uk-slider-items uk-child-width-1-1">tplPhotos</ul></div><ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul></div>';
	var inspectionCommentPhotoTemplate = '<li class="findings-item-photo-tplPhotoId use-hand-cursor" onclick="openFindingPhoto(tplFindingId,tplItemId,tplPhotoId);"><img src="tplUrl" alt=""><div class="uk-position-bottom-center uk-panel photo-caption use-hand-cursor"><i class="a-comment-text"></i> tplComments</div></li>';

	var inspectionCommentFileTemplate = '<div class="finding-file-container">tplFileContent</div>';

	var itemcontent = '';

	switch(type) {
	    case 'photo':
	        var itemtype = 'PIC';
	        var images = '';
	        var newimage = '';

	        JSON.parse(item.photos_json).forEach(function(pic){
	        	newimage = inspectionCommentPhotoTemplate;
	        	newimage = newimage.replace(/tplUrl/g, pic.url);
	        	newimage = newimage.replace(/tplComments/g, pic.commentscount);
	        	newimage = newimage.replace(/tplFindingId/g, item.finding_id);
	        	newimage = newimage.replace(/tplItemId/g, item.id);
	        	newimage = newimage.replace(/tplPhotoId/g, pic.id);

	        	images = images + newimage;
	        });
	        itemcontent = inspectionCommentPhotosTemplate.replace(/tplPhotos/g, images);
	        break;
	    case 'file':
	        var itemtype = 'DOC';
	        var categoryTemplate = "<div class='finding-file-category'><i class='tplCatIcon'></i> tplCatName</div>";
	        var categories = '';
	        var newcategory = '';
	        var file = '';

	        var doc = JSON.parse(item.document_json);

	        doc.categories.forEach(function(cat) {
	        	newcategory = categoryTemplate;
	        	switch(cat.status) {
	        		case 'checked':
	        			newcategory = newcategory.replace(/tplCatIcon/g, 'a-circle-checked');
	        		break;
	        		case 'notchecked':
	        			newcategory = newcategory.replace(/tplCatIcon/g, 'a-circle-cross');
	        		break;
	        		case '':
	        			newcategory = newcategory.replace(/tplCatIcon/g, 'a-circle');
	        		break;
	        	}
	        	newcategory = newcategory.replace(/tplCatName/g, cat.name);
	        	categories = categories + newcategory;
	        });

	        file = categories+"<div class='finding-file use-hand-cursor' onclick='openFindingFile();'><i class='a-down-arrow-circle'></i> "+doc.file.name+"<br />"+doc.file.size+" MB "+doc.file.type+"</div>";

	        itemcontent = inspectionCommentFileTemplate.replace(/tplFileContent/g, file);
	        break;
	}

	return itemcontent;
}

function loadInspectionComments(data, id, context='audits', level = '') {
	var inspectionCommentTemplate = $('#inspection-comment-template').html();
	var inspectionCommentReplyTemplate = $('#inspection-comment-reply-template').html();

	var comments = '';
	var newcomment = '';
	var newreply = '';
	var replies = '';
	var actions = '';
	var newaction = '';

	data.forEach(function(comment) {
		newcomment = inspectionCommentTemplate;
		newcomment = newcomment.replace(/tplCommentTypeIcon/g, comment.type_icon);
		newcomment = newcomment.replace(/tplCommentType/g, comment.type_text);
		newcomment = newcomment.replace(/tplCommentAuditId/g, comment.audit_id);
		newcomment = newcomment.replace(/tplCommentStatus/g, comment.status);
		newcomment = newcomment.replace(/tplCommentCreatedAt/g, comment.created_at);
		newcomment = newcomment.replace(/tplCommentUserName/g, comment.user_name);
		if(comment.type !== undefined && comment.type.length){
			if(comment.type == 'finding'){
				newcomment = newcomment.replace(/tplCommentResolve/g, '<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">&nbsp; </span>RESOLVE</button>');
			}else{
				newcomment = newcomment.replace(/tplCommentResolve/g, '');
			}

			newcomment = newcomment.replace(/comment-type/g, 'comment-type-'+comment.type);
		}else{
			newcomment = newcomment.replace(/tplCommentResolve/g, '');
		}

		if(comment.replies !== undefined && comment.replies.length){
			replies = '';
			comment.replies.forEach(function(reply){
				newreply = inspectionCommentReplyTemplate;
				newreply = newreply.replace(/tplCommentTypeIcon/g, reply.type_icon);
				newreply = newreply.replace(/tplCommentType/g, reply.type_text);
				newreply = newreply.replace(/tplCommentAuditId/g, reply.audit_id);
				newreply = newreply.replace(/tplCommentCreatedAt/g, reply.created_at);
				newreply = newreply.replace(/tplCommentUserName/g, reply.user_name);

				if(reply.type == 'file' || reply.type == 'photo'){
					var content = formatCommentType(reply, reply.type);
					newreply = newreply.replace(/tplCommentContent/g, content);
				}else{
					newreply = newreply.replace(/tplCommentContent/g, reply.content);
				}

				newreply = newreply.replace(/tplCommentResolve/g, '');

				replies = replies + newreply;
			});
			newcomment = newcomment.replace(/tplCommentReplies/g, replies);
		}else{
			newcomment = newcomment.replace(/tplCommentReplies/g, '');
		}

		if(comment.actions_json !== undefined && comment.actions_json.length){
			actions = '';
			JSON.parse(comment.actions_json).forEach(function(action){
				newaction = '<div class="uk-width-1-4"><button class="uk-button uk-link inspec-tools-tab-finding-button"><i class="tplActionIcon"></i> tplActionText</button></div>';
				newaction = newaction.replace(/tplActionIcon/g, action.icon);
				newaction = newaction.replace(/tplActionText/g, action.name);

				actions = actions + newaction;
			});

			newcomment = newcomment.replace(/tplCommentActions/g, actions);
		}else{
			newcomment = newcomment.replace(/tplCommentActions/g, '');
		}

		if(comment.type == 'file' || comment.type == 'photo'){
			var content = formatCommentType(comment, comment.type);
			comments = comments + newcomment.replace(/tplCommentContent/g, content);
		}else{
			comments = comments + newcomment.replace(/tplCommentContent/g, comment.content);
		}

	});

	$(".inspec-tools-tab-findings-container").html(comments);
	//$('.inspec-tools-tab-finding').replaceWith(comments);
//	$('#inspection-'+context+'-tools-'+id).html(inspectionCommentTemplate);

	// $('#inspection-'+context+'-'+level+'comments-'+id+' .inspection-comments').html(comments);
	// $('#inspection-'+context+'-'+level+'comments-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	 // });

}

function inspectionDetailsFromBuilding(buildingid, auditid, target, targetaudit, rowid, context='audits'){
	// context to reuse function on multiple tabs
	if(context == 'project-details'){
		var scrollToTarget = '#building-'+context+'-r-'+target;
	}else{
		var scrollToTarget = '#audit-r-'+targetaudit;
	}

	// scroll to row early
	$('html, body').animate({
		scrollTop: $(scrollToTarget).offset().top - 59
	}, 500, 'linear');

	// close building details
	if ($('#building-'+context+'-r-'+target+'-details').length){

		if ($('#building-'+context+'-r-'+target).attr('expanded')){
			$('#building-'+context+'-r-'+target).removeAttr('expanded');
		}

		// close own details
		$('#building-'+context+'-r-'+target+'-details').slideUp( "slow", function() {
			$(this).remove();
			});
		// unblur other building rows
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).slideDown();
		if(context != 'project-details'){
			$('.rowinset-top').slideDown();
			$('.rowinset-bottom').slideDown();
		}
		$('div[id^="building-'+context+'-r-"]').removeClass('blur');
	}

	if ($('#building-'+context+'-r-'+target).attr('expanded')){
		 $('#building-'+context+'-r-'+target).removeAttr('expanded');
		// close own details
		$('#inspection-'+context+'-tools-'+target+'-container').fadeOut("slow", function() {
			$('#inspection-'+context+'-main-'+target+'-container').slideUp("slow");
			$('#inspection-'+context+'-menus-'+target+'-container').slideUp("slow");
		    $('#inspection-'+context+'-tools-switch-'+target).fadeIn( "slow", function() {
			    // Animation complete
			    // delete content
			    //$('#inspection-'+context+'-main-'+target+'-container .inspection-areas').html('');
			  });
		    $('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).slideDown();
			// unblur other building inspection rows
			$('div[id^="building-'+context+'-r-"]').removeClass('blur');
		 });


	}else{

		// blur all other building rows
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).addClass('blur');
		$('div[id^="building-'+context+'-r-"]').not( 'div[id="building-'+context+'-r-'+target+'"]' ).slideUp();

    	// open the expanded div early based on expected number of buildings
    	var tempdiv = '<div>';
    	tempdiv = tempdiv + '<div style="height:1000px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
    	tempdiv = tempdiv + '</div>';
    	$('#inspection-'+context+'-main-'+target).html(tempdiv);
    	$('#inspection-'+context+'-main-'+target+'-container').slideDown();

    	$('#inspection-'+context+'-tools-switch-'+target).fadeOut("slow");

		// fetch and display new details
		var url = 'dashboard/audits/'+auditid+'/building/'+buildingid+'/inspection';
	    $.get(url, {
            'target' : target,
            'rowid' : rowid,
            'targetaudit' : targetaudit
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the building details' information.");
                } else {
					// $('#building-detail-r-'+target+'-inspect').html(data);
					$('#building-'+context+'-r-'+target).attr( "expanded", true );
					loadInspectionMenu(data, target, context,'',auditid);
					loadInspectionMain(data.amenities, target, context,'',auditid);
					loadInspectionTools(data, target, context,'',auditid);
					//
					// //loadInspectionComments(data.comments, target, context);




				}
	    });
	}
}


function inspectionDetails(id, buildingid, auditid, target, targetaudit, rowid, context='audits') {
	// context to reuse function on multiple tabs
	if(context == 'project-details'){
		var scrollToTarget = '#building-'+context+'-r-'+target;
	}else{
		var scrollToTarget = '#audit-r-'+targetaudit;
	}
	// scroll to row early
    $('html, body').animate({
		scrollTop: $(scrollToTarget).offset().top - 59
	}, 500, 'linear');

	if ($('#building-'+context+'-detail-r-'+target).attr('expanded')){

		$('#building-'+context+'-detail-r-'+target).removeAttr('expanded');
		// close own details
		$('#inspection-'+context+'-detail-tools-'+target+'-container').fadeOut("slow", function() {
			$('#inspection-'+context+'-detail-main-'+target+'-container').slideUp("slow");
			$('#inspection-'+context+'-detail-menus-'+target+'-container').slideUp("slow");
		    $('#inspection-'+context+'-detail-tools-switch-'+target).fadeIn( "slow", function() {
			    // Animation complete
			    // delete content
			    //$('#inspection-'+context+'-detail-main-'+target+'-container .inspection-areas').html('');
			  });
		    $('div[id^="building-'+context+'-detail-r-"]').not( 'div[id="building-'+context+'-detail-r-'+target+'"]' ).slideDown();
			// unblur other building inspection rows
			$('div[id^="building-'+context+'-detail-r-"]').removeClass('blur');
		 });


	}else{
		// blur all other building detail rows
		$('div[id^="building-'+context+'-detail-r-"]').not( 'div[id="building-'+context+'-detail-r-'+target+'"]' ).addClass('blur');
		$('div[id^="building-'+context+'-detail-r-"]').not( 'div[id="building-'+context+'-detail-r-'+target+'"]' ).slideUp();

    	// open the expanded div early based on expected number of buildings
    	var tempdiv = '<div>';
    	tempdiv = tempdiv + '<div style="height:1000px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
    	tempdiv = tempdiv + '</div>';
    	$('#inspection-'+context+'-detail-main-'+target).html(tempdiv);
    	$('#inspection-'+context+'-detail-main-'+target+'-container').slideDown();

    	$('#inspection-'+context+'-detail-tools-switch-'+target).fadeOut("slow");

		// fetch and display new details
		var url = 'dashboard/audits/'+auditid+'/building/'+buildingid+'/details/'+id+'/inspection';
	    $.get(url, {
            'target' : target,
            'rowid' : rowid,
            'targetaudit' : targetaudit
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the building details' information.");
                } else {
					// $('#building-detail-r-'+target+'-inspect').html(data);

					$('#building-'+context+'-detail-r-'+target).attr( "expanded", true );
					loadInspectionMenu(data, target, context, 'detail-', auditid);
					loadInspectionMain(data.amenities, target, context, 'detail-', auditid);
					loadInspectionTools(data, target, context, 'detail-', auditid); // includes the comments
					//loadInspectionComments(data.comments, target, context, 'detail-');
				}
	    });
	}

}

function addArea() {
	console.log('adding inspectable area');
}

function applyFilter(filter, value) {
	// hide tab
	$("#detail-tab-1-content").children().fadeOut();
	// set session value
	$.get( '/session/filters/'+filter+'/'+value, function( data ) {
       // reload
		$('#detail-tab-1').trigger("click");
		// UIkit.switcher('#top-tabs').show(0);
    });
}

function switchInspectionMenu(action, context, level='', audit_id, building_id, unit_id){
	//console.log("Switching menu "+action+level);
	console.log(action);
	if(action == 'site_audit'){
		$('.fileaudit').fadeOut('slow',function(){});
		$('.siteaudit').fadeIn('slow',function(){});
	}else if(action == 'file_audit'){
		$('.siteaudit').fadeOut('slow',function(){});
		$('.fileaudit').fadeIn('slow',function(){});
	}else if(action == 'complete'){
		console.log(audit_id+'-'+building_id+'-'+unit_id);
		markAmenityComplete(audit_id, building_id, unit_id, 0, '');

	}

	// $('#inspection-'+context+'-'+level+'main-'+id).html("Switching menu "+action+level);
	// $('#inspection-'+context+'-'+level+'main-'+id+' .inspection-areas').html('areas');
	// $('#inspection-'+context+'-'+level+'main-'+id+'-container').fadeIn( "slow", function() {
	//     // Animation complete
	//   });
}

var quicklookupbox = new autoComplete({
	selector: '#filter-by-project',
    minChars: 3,
    cache: 1,
    delay: 150,
	offsetLeft: 0,
	offsetTop: 1,
	menuClass: '',

    source: function(term, suggest){
    	console.log('filtering by name... '+term);
    	$.get( "/autocomplete/auditproject", {
			'search' : term
		},
		function(data) {
			var output = eval(data);
			term = term.toLowerCase();
            var suggestions = [];
            for (i=0;i<output.length;i++)
            	suggestions.push(output[i]);
	        suggest(suggestions);
		},
		'json' );
    },
    renderItem: function (item, search){
	    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
	    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

	    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
	    output = output + 'Parcel ID: '+item[3]+'<br />';
	    output = output + item[0]+'<br />';
	    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
		output = output + '<span class="hideImport'+item[6]+'">';
	    output = output + '</div>';

	    return output;
	},
    onSelect: function(e, term, item){
    	e.preventDefault();
    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
    	$('#quick-lookup-box').val('');
    }
});
var quicklookupbox = new autoComplete({
	selector: '#filter-by-name',
    minChars: 3,
    cache: 1,
    delay: 150,
	offsetLeft: 0,
	offsetTop: 1,
	menuClass: '',

    source: function(term, suggest){
    	console.log('filtering by name... '+term);
    	$.get( "/autocomplete/auditname", {
			'search' : term
		},
		function(data) {
			var output = eval(data);
			term = term.toLowerCase();
            var suggestions = [];
            for (i=0;i<output.length;i++)
            	suggestions.push(output[i]);
	        suggest(suggestions);
		},
		'json' );
    },
    renderItem: function (item, search){
	    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
	    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

	    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
	    output = output + 'Parcel ID: '+item[3]+'<br />';
	    output = output + item[0]+'<br />';
	    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
		output = output + '<span class="hideImport'+item[6]+'">';
	    output = output + '</div>';

	    return output;
	},
    onSelect: function(e, term, item){
    	e.preventDefault();
    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
    	$('#quick-lookup-box').val('');
    }
});
var quicklookupbox = new autoComplete({
	selector: '#filter-by-address',
    minChars: 3,
    cache: 1,
    delay: 150,
	offsetLeft: 0,
	offsetTop: 1,
	menuClass: '',

    source: function(term, suggest){
    	console.log('filtering by name... '+term);
    	$.get( "/autocomplete/auditaddress", {
			'search' : term
		},
		function(data) {
			var output = eval(data);
			term = term.toLowerCase();
            var suggestions = [];
            for (i=0;i<output.length;i++)
            	suggestions.push(output[i]);
	        suggest(suggestions);
		},
		'json' );
    },
    renderItem: function (item, search){
	    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
	    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

	    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
	    output = output + 'Parcel ID: '+item[3]+'<br />';
	    output = output + item[0]+'<br />';
	    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
		output = output + '<span class="hideImport'+item[6]+'">';
	    output = output + '</div>';

	    return output;
	},
    onSelect: function(e, term, item){
    	e.preventDefault();
    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
    	$('#quick-lookup-box').val('');
    }
});



var startIndex;
var endIndex;

// reorder(".buildings > .sortable", '.building');
function reorder(classname, childclassname) {
	// console.log("classname "+classname+" childclassname "+ childclassname);
	var currentIndex;
	$(classname+" > div").each(function () {
		if(this.id != ''){
			//console.log(this.id);
			currentIndex = $(childclassname).index(this) + 1;
			$("#"+this.id).find(".rowindex").each(function () {
				$(this).html(currentIndex);
			});
		}
	});
}
//onclick="openFindings(this, 6659, 23057, 1005319, 'file');"
function openFindings(element, auditid, buildingid, unitid='', type='all', amenity='', toplevel=0){
	dynamicModalLoad('findings/'+type+'/audit/'+auditid+'/building/'+buildingid+'/unit/'+unitid+'/amenity/'+amenity+'/'+toplevel,1,0,1);
}

// function reorderBuildings(auditId, projectId, buildingId, amenityId, amenityInspectionId, endIndex) {
// 	var url = 'dashboard/audits/'+auditId+'/buildings/reorder';
// 	$.get(url, {
//         'building' : buildingId,
//         'amenity' : amenityId,
//         'amenity_inspection' : amenityInspectionId,
//         'project' : projectId,
//         'index' : endIndex
//         }, function(data) {
//             if(data=='0'){
//                 UIkit.modal.alert("There was a problem reordering the buildings.");
//             } else {
// 				console.log("reordering completed");
// 			}
//     });
// }

function reorderUnits(auditId, buildingId, unitId, endIndex) {
	var url = 'dashboard/audits/'+auditId+'/building/'+buildingId+'/units/reorder';
	$.get(url, {
        'unit' : unitId,
        'index' : endIndex
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem reordering the units.");
            } else {
				console.log("reordering completed");
			}
    });
}

function reorderAmenities(auditId, buildingId, unitId, amenityId, endIndex) {
	// console.log(auditId+" "+buildingId+" "+unitId+" "+amenityId+" "+endIndex);
	var url = 'dashboard/audits/'+auditId+'/amenities/reorder';
	$.get(url, {
        'amenity_id' : amenityId,
        'building_id' : buildingId,
        'unit_id' : unitId,
        'index' : endIndex
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem reordering the amenities.");
            } else {
				console.log("reordering completed");
			}
    });
}

$(function () {
	// $(document).on('start', '.sortablebuildings', function (item) {
	// 	//console.log("almost moving....");
	// 	var listItem = document.getElementById( item.detail[1].id );
	// 	//console.log(item.detail[1].id);
	// 	if($('#'+item.detail[1].id).hasClass('building-detail')){
	// 		startIndex = $( ".building-detail" ).index( listItem );
	// 	}else if($('#'+item.detail[1].id).hasClass('building')){
	// 		startIndex = $( ".building" ).index( listItem );
	// 	}else if($('#'+item.detail[1].id).hasClass('inspection-area')){
	// 		startIndex = $( ".inspection-area" ).index( listItem );
	// 	}
	// 	//console.log( item.detail[1].id + " started at index: " + startIndex );
	// });
	// $(document).on('moved', '.sortablebuildings', function (item) {
	// 	//console.log("moving....");
	// 	var listItem = document.getElementById( item.detail[1].id );
	// 	var auditId = $(listItem).data('audit');
	// 	var buildingId = $(listItem).data('building');
	// 	var amenityId = $(listItem).data('amenity');
	// 	var amenityInspectionId = $(listItem).data('amenityinspection');
	// 	var projectId = $(listItem).data('project');

	// 	if($('#'+item.detail[1].id).hasClass('building-detail')){
	// 		console.log("moving building detail ....");
	// 		var unitId = $(listItem).data('area');
	// 		endIndex = $( ".building-detail" ).index( listItem );
	// 		console.log( item.detail[1].id + " ended at index: " + endIndex );
	// 		UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
	// 		reorder(".building-details > .sortable", '.building-detail');

	// 		reorderUnits(auditId, buildingId, unitId, endIndex);

	// 	}else if($('#'+item.detail[1].id).hasClass('building')){
	// 		console.log("moving building ....");
	// 		endIndex = $( ".building" ).index( listItem );
	// 		//console.log( item.detail[1].id + " ended at index: " + endIndex );
	// 		//UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
	// 		reorder(".buildings > .sortable", '.building');
	// 		console.log("reordered");
	// 		reorderBuildings(auditId, projectId, buildingId, amenityId, amenityInspectionId, endIndex);

	// 		console.log("endIndex "+endIndex+' '+item.detail[1].id);
	// 		// update journey icons
	// 		var length = $('.building').length;
	// 		$('.building').each(function(index, element) {
	// 			$(element).find( ".journey-start" ).addClass('journey');
	// 			$(element).find( ".journey-start" ).removeClass('journey-start');
	// 			$(element).find( ".journey-end" ).addClass('journey');
	// 			$(element).find( ".journey-end" ).removeClass('journey-end');
	// 			$(element).find( ".a-home-marker" ).addClass('a-marker-basic');
	// 			$(element).find( ".a-home-marker" ).removeClass('a-home-marker');

	// 		    if (index == 0) {
	// 		        $(element).find( ".journey" ).addClass('journey-start');
	// 				$(element).find( ".journey" ).removeClass('journey');
	// 				$(element).find( ".a-marker-basic" ).addClass('a-home-marker');
	// 				$(element).find( ".a-marker-basic" ).removeClass('a-marker-basic');
	// 	        }
	// 			if (index == (length - 2)) {
	// 		        $(element).find( ".journey" ).addClass('journey-end');
	// 				$(element).find( ".journey" ).removeClass('journey');
	// 				$(element).find( ".a-marker-basic" ).addClass('a-home-marker');
	// 				$(element).find( ".a-marker-basic" ).removeClass('a-marker-basic');
	// 		    }
	// 		});
	// 		console.log("done ....");
	// 	}else if($('#'+item.detail[1].id).hasClass('inspection-area')){
	// 		console.log("moving inspection area ....");
	// 		var unitId = $(listItem).data('unit');
	// 		var amenityId = $(listItem).data('amenity');
	// 		endIndex = $( ".inspection-area" ).index( listItem );
	// 		// console.log( item.detail[1].id + " ended at index: " + endIndex );
	// 		// UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
	// 		// reorder(".inspection-main-list > .sortable", '.inspection-area');

	// 		reorderAmenities(auditId, buildingId, unitId, amenityId, endIndex);
	// 	}
	// });
});

function loadProjectDetailsBuildings(id, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	$('#project-details-buildings-container').html(tempdiv);

	var url = 'dashboard/audits/'+id+'/buildings';
    $.get(url, {
        'context' : 'project-details',
         'target' : target
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the buildings' information.");
            } else {
            	var newdiv = '<div uk-grid><div id="auditstable" class="uk-width-1-1 uk-overflow-auto"><table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="min-width: 1440px;"><tr>';
            	newdiv = newdiv + data;
            	newdiv = newdiv + '</tr></table></div></div>'
				$('#project-details-buildings-container').html(newdiv);
        	}
    });
}

// get unit data using pagination
$(document).on('click', '.site .pagination a', function(event){
 	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
	var projectId = $(this).parents('#site').data('project-id');
  	var auditId = $(this).parents('#site').data('audit-id');

  	fetch_site_data(projectId, 'selections', auditId, page);
});

function fetch_site_data(id, type, audit, page) {
	var tempdiv = '<div style="height:300px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#site').html(tempdiv);

	var url = '/projects/'+id+'/details/'+type+'/'+audit+'/site?page=' + page;
    $.get(url, {
        }, function(data) {
        	if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#site').html(data);
        	}
    });
}


// get unit data using pagination
$(document).on('click', '.unit .pagination a', function(event){
 	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
	var projectId = $(this).parents('#unit').data('project-id');
  	var auditId = $(this).parents('#unit').data('audit-id');

  	fetch_unit_data(projectId, 'selections', auditId, page);
});

function fetch_unit_data(id, type, audit, page) {
	var tempdiv = '<div style="height:300px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#unit').html(tempdiv);

	var url = '/projects/'+id+'/details/'+type+'/'+audit+'/unit?page=' + page;
    $.get(url, {
        }, function(data) {
        	if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#unit').html(data);
        	}
    });
}

// get building data using pagination
$(document).on('click', '.building .pagination a', function(event){
  	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
  	var projectId = $(this).parents('#building').data('project-id');
  	var auditId = $(this).parents('#building').data('audit-id');
  	
  	fetch_building_data(projectId, 'selections', auditId, page);
});

function fetch_building_data(id, type, audit, page) {
	
	var tempdiv = '<div style="height:175px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#building').html(tempdiv);

	var url = '/projects/'+id+'/details/'+type+'/'+audit+'/building?page=' + page;
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#building').html(data);
            	AfterBuildingUpLoad();
            }
    });
}


/// pm specific
// get unit data using pagination
$(document).on('click', '.pm-site .pagination a', function(event){
 	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
	var projectId = $(this).parents('#site').data('project-id');
  	var auditId = $(this).parents('#site').data('audit-id');

  	pm_fetch_site_data(projectId, 'selections', auditId, page);
});

function pm_fetch_site_data(id, type, audit, page) {
	var tempdiv = '<div style="height:300px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#site').html(tempdiv);

	var url = '/pm-projects/'+id+'/details/'+type+'/'+audit+'/site?page=' + page;
    $.get(url, {
        }, function(data) {
        	if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#site').html(data);
        	}
    });
}


// get unit data using pagination
$(document).on('click', '.pm-unit .pagination a', function(event){
 	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
	var projectId = $(this).parents('#unit').data('project-id');
  	var auditId = $(this).parents('#unit').data('audit-id');

  	pm_fetch_unit_data(projectId, 'selections', auditId, page);
});

function pm_fetch_unit_data(id, type, audit, page) {
	var tempdiv = '<div style="height:300px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#unit').html(tempdiv);

	var url = '/pm-projects/'+id+'/details/'+type+'/'+audit+'/unit?page=' + page;
    $.get(url, {
        }, function(data) {
        	if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#unit').html(data);
        	}
    });
}

// get building data using pagination
$(document).on('click', '.pm-building .pagination a', function(event){
  	event.preventDefault(); 
	var page = $(this).attr('href').split('page=')[1];
  	var projectId = $(this).parents('#building').data('project-id');
  	var auditId = $(this).parents('#building').data('audit-id');
  	
  	pm_fetch_building_data(projectId, 'selections', auditId, page);
});

function pm_fetch_building_data(id, type, audit, page) {
	
	var tempdiv = '<div style="height:175px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	// $('#building').html(tempdiv);

	var url = '/pm-projects/'+id+'/details/'+type+'/'+audit+'/building?page=' + page;
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
            	$('#building').html(data);
            	AfterBuildingUpLoad();
            }
    });
}

function pmLoadProjectDetailsBuildings(id, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	$('#project-details-buildings-container').html(tempdiv);

	var url = 'pm-dashboard/audits/'+id+'/buildings';
    $.get(url, {
        'context' : 'project-details',
         'target' : target
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the buildings' information.");
            } else {
            	var newdiv = '<div uk-grid><div id="auditstable" class="uk-width-1-1 uk-overflow-auto"><table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="min-width: 1440px;"><tr>';
            	newdiv = newdiv + data;
            	newdiv = newdiv + '</tr></table></div></div>'
				$('#project-details-buildings-container').html(newdiv);
        	}
    });
}

function pmGetUnCorrectedBuilding(e){
	var id = $('#building_dropdown').val();
	
  	id = id.map(Number);
  	var is_uncorrected = e.checked;
  	if((Array.isArray(id) && id.length) && is_uncorrected == false){
  		// get building wise data
  		pm_fetch_buidling_detail('building', id);
  		pm_fetch_buidling_detail('unit', id);
  		
  	}else if(((Array.isArray(id) && id.length) && is_uncorrected == true) || (!(Array.isArray(id) && id.length) && is_uncorrected == true)){
  		pm_fetch_buidling_detail('building', id, is_uncorrected);
  		pm_fetch_buidling_detail('unit', id, is_uncorrected);
  	}else{
  		var projectId = $('#building').data('project-id');
  		var auditId = $('#building').data('audit-id');
  		// fetch_building_data(projectId, 'selections', auditId, 1);
  		// fetch_unit_data(projectId, 'selections', auditId, 1);
  		pm_fetch_buidling_detail('all', id);
  		// get 1 first page data if first option selected FILTER BY BUILDING
  		// fetch_buidling_detail('all', id);
  		// console.log('all clear');
	}
  	// if(!(Array.isArray(id) && id.length) && e.checked == 'false'){
  	// 	fetch_building_data(projectId, 'selections', auditId, 1);
  	// 	fetch_unit_data(projectId, 'selections', auditId, 1);
  	// }else{
  	// 	fetch_buidling_detail('building', id);
  	// }

  	// 	// get building wise data
  	// 	fetch_buidling_detail('building', id, true);
  	// 	// fetch_buidling_detail(projectId, 'unit', auditId, id);
  		
  	// }else if((Array.isArray(id) && id.length) && e.checked){
  	// }else{
  		// get 1 first page data if first option selected FILTER BY BUILDING
	
	// }
	console.log(is_uncorrected, id);
}
// get single building data record by filter
// e: click emlemet object
function pmGetSingleBuilding(e){
	var id = $('#building_dropdown').val();
  	id = id.map(Number);

  	var is_uncorrected;
  	if($('#uncorrected_checkbox:checkbox:checked').length > 0){
  		is_uncorrected = true;
  	}else{
  		is_uncorrected = false;
  	}
  	if((Array.isArray(id) && id.length) && is_uncorrected == false){
  		// get building wise data
  		pm_fetch_buidling_detail('building', id);
  		pm_fetch_buidling_detail('unit', id);
  		
  	}else if(((Array.isArray(id) && id.length) && is_uncorrected == true) || (!(Array.isArray(id) && id.length) && is_uncorrected == true)){
  		pm_fetch_buidling_detail('building', id, is_uncorrected);
  		pm_fetch_buidling_detail('unit', id, is_uncorrected);
  	}else{
		var projectId = $('#building').data('project-id');
  		var auditId = $('#building').data('audit-id');
  		// fetch_building_data(projectId, 'selections', auditId, 1);
  		// fetch_unit_data(projectId, 'selections', auditId, 1);
  		// get 1 first page data if first option selected FILTER BY BUILDING
  		pm_fetch_buidling_detail('all', id);
  		console.log('all clear');
	}
	// console.log(is_uncorrected, id, $('#uncorrected_checkbox').val(), $('#uncorrected_checkbox:checkbox:checked').length);
	console.log((Array.isArray(id) && id.length),is_uncorrected)
}

function pm_fetch_buidling_detail(type, id = [], is_uncorrected = null){
	var projectId = $('#building').data('project-id');
  	var auditId = $('#building').data('audit-id');
	var url = '/pm-projects/'+projectId+'/building-details/'+type+'/'+auditId;
	
    $.post(
    	url,
    	{ 
    		'_token' : $('#token').val(),
    		type_id: id,
    		is_uncorrected: is_uncorrected
		},
    	function(data) {
	    	if(data=='0'){
	            UIkit.modal.alert("There was a problem getting the project information.");
	        } else {
	        	if(type == 'building'){
	        		$('#building').html(data);
	        		AfterBuildingUpLoad();
	        	}else if(type == 'unit'){
	        		$('#unit').html(data);
	        	}else if(type == 'all'){
	        		// get 1 first page data if first option selected FILTER BY BUILDING
			  		pm_fetch_building_data(projectId, 'selections', auditId, 1);
			  		pm_fetch_unit_data(projectId, 'selections', auditId, 1);
	        	}
	        }
    	}
    );
}

function AfterBuildingUpLoad(){
	// console.log('test');
	$('#building_dropdown').select2({
		placeholder: "FILTER BY BUILDING"
	});
}
// get single building data record by filter
// e: click emlemet object
function getSingleBuilding(e){
	var id = $('#building_dropdown').val();
  	id = id.map(Number);

  	var is_uncorrected;
  	if($('#uncorrected_checkbox:checkbox:checked').length > 0){
  		is_uncorrected = true;
  	}else{
  		is_uncorrected = false;
  	}
  	if((Array.isArray(id) && id.length) && is_uncorrected == false){
  		// get building wise data
  		fetch_buidling_detail('building', id);
  		fetch_buidling_detail('unit', id);
  		
  	}else if(((Array.isArray(id) && id.length) && is_uncorrected == true) || (!(Array.isArray(id) && id.length) && is_uncorrected == true)){
  		fetch_buidling_detail('building', id, is_uncorrected);
  		fetch_buidling_detail('unit', id, is_uncorrected);
  	}else{
		var projectId = $('#building').data('project-id');
  		var auditId = $('#building').data('audit-id');
  		// fetch_building_data(projectId, 'selections', auditId, 1);
  		// fetch_unit_data(projectId, 'selections', auditId, 1);
  		// get 1 first page data if first option selected FILTER BY BUILDING
  		fetch_buidling_detail('all', id);
  		console.log('all clear');
	}
	// console.log(is_uncorrected, id, $('#uncorrected_checkbox').val(), $('#uncorrected_checkbox:checkbox:checked').length);
	console.log((Array.isArray(id) && id.length),is_uncorrected)
}

function getUnCorrectedBuilding(e){
	var id = $('#building_dropdown').val();
	
  	id = id.map(Number);
  	var is_uncorrected = e.checked;
  	if((Array.isArray(id) && id.length) && is_uncorrected == false){
  		// get building wise data
  		fetch_buidling_detail('building', id);
  		fetch_buidling_detail('unit', id);
  		
  	}else if(((Array.isArray(id) && id.length) && is_uncorrected == true) || (!(Array.isArray(id) && id.length) && is_uncorrected == true)){
  		fetch_buidling_detail('building', id, is_uncorrected);
  		fetch_buidling_detail('unit', id, is_uncorrected);
  	}else{
  		var projectId = $('#building').data('project-id');
  		var auditId = $('#building').data('audit-id');
  		// fetch_building_data(projectId, 'selections', auditId, 1);
  		// fetch_unit_data(projectId, 'selections', auditId, 1);
  		fetch_buidling_detail('all', id);
  		// get 1 first page data if first option selected FILTER BY BUILDING
  		// fetch_buidling_detail('all', id);
  		// console.log('all clear');
	}
  	// if(!(Array.isArray(id) && id.length) && e.checked == 'false'){
  	// 	fetch_building_data(projectId, 'selections', auditId, 1);
  	// 	fetch_unit_data(projectId, 'selections', auditId, 1);
  	// }else{
  	// 	fetch_buidling_detail('building', id);
  	// }

  	// 	// get building wise data
  	// 	fetch_buidling_detail('building', id, true);
  	// 	// fetch_buidling_detail(projectId, 'unit', auditId, id);
  		
  	// }else if((Array.isArray(id) && id.length) && e.checked){
  	// }else{
  		// get 1 first page data if first option selected FILTER BY BUILDING
	
	// }
	console.log(is_uncorrected, id);
}

function fetch_buidling_detail(type, id = [], is_uncorrected = null){
	var projectId = $('#building').data('project-id');
  	var auditId = $('#building').data('audit-id');
	var url = '/projects/'+projectId+'/building-details/'+type+'/'+auditId;
	
    $.post(
    	url,
    	{ 
    		'_token' : $('#token').val(),
    		type_id: id,
    		is_uncorrected: is_uncorrected
		},
    	function(data) {
	    	if(data=='0'){
	            UIkit.modal.alert("There was a problem getting the project information.");
	        } else {
	        	if(type == 'building'){
	        		$('#building').html(data);
	        		AfterBuildingUpLoad();
	        	}else if(type == 'unit'){
	        		$('#unit').html(data);
	        	}else if(type == 'all'){
	        		// get 1 first page data if first option selected FILTER BY BUILDING
			  		fetch_building_data(projectId, 'selections', auditId, 1);
			  		fetch_unit_data(projectId, 'selections', auditId, 1);
	        	}
	        }
    	}
    );
}

function projectDetailsInfo(id, type, audit, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#project-details-info-container').html(tempdiv);

	// remove active buttons
	$('#project-details-buttons').find('.uk-button').removeClass('active');
	$(target).addClass('active');
	var url = '/projects/'+id+'/details/'+type+'/'+audit;
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {

				$('#project-details-info-container').html(data);
				AfterBuildingUpLoad();
        	}
    });
}

function pmProjectDetailsInfo(id, type, audit, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#project-details-info-container').html(tempdiv);

	// remove active buttons
	$('#project-details-buttons').find('.uk-button').removeClass('active');
	$(target).addClass('active');
	var url = '/pm-projects/'+id+'/details/'+type+'/'+audit;
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {

				$('#project-details-info-container').html(data);
        	}
    });
}

function assignmentDay(id, dateid, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('.project-details-info-assignment-schedule').html(tempdiv);

	// remove active buttons
	$('#project-details-assignment-buttons').find('.uk-button').removeClass('active');
	$(target).addClass('active');

	var url = '/projects/'+id+'/details/assignment/date/'+dateid;
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the assignment information.");
            } else {

				$('.project-details-info-assignment-schedule').html(data);
        	}
    });
}

function fillSpacers() {
	var TotalRows = 60;
	var i = 0;
	var spacers = '';
	for (i = 0; i < TotalRows; i++) {
	    spacers = spacers+"<div></div>";
	}
	$('.day-spacer').html(spacers);
}

// either building id or unit id is given
function addAmenity(id, type, stack=0, findingmodal=0, audit=0) {
	console.log("adding an amenity for "+type+" "+id);
	dynamicModalLoad('amenities/add/'+type+'/'+id+'/'+findingmodal+'?audit='+audit, 0, 0, 0,stack);
}

//documents loading
function documentsDocuware(project_id) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#docuware-documents').html(tempdiv);
	$('#project-documents-button-2').removeClass('uk-button-success green-button');
	$('#project-documents-button-1').addClass('uk-button-success green-button active');
	$('#allita-documents').empty();
	var url = '/projects/'+project_id+'/docuware-documents';
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
				$('#docuware-documents').html(data);
        	}
    });
}

function documentsLocal(project_id, audit_id = null, filter = null) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#allita-documents').html(tempdiv);
	$('#project-documents-button-1').removeClass('uk-button-success green-button');
	$('#project-documents-button-2').addClass('uk-button-success green-button active');
	$('#docuware-documents').empty();
	var url = '/projects/'+project_id+'/local-documents/';
	if(audit_id != null) {
		url = url+ audit_id;
	}
	if(filter != null) {
		url = url+ filter;
	}
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
				$('#allita-documents').html(data);
        	}
    });
}

function pmDocumentsLocal(project_id, audit_id = null, filter = null) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#allita-documents').html(tempdiv);
	$('#project-documents-button-1').removeClass('uk-button-success green-button');
	$('#project-documents-button-2').addClass('uk-button-success green-button active');
	$('#docuware-documents').empty();
	var url = '/pm-projects/'+project_id+'/local-documents/';
	if(audit_id != null) {
		url = url+ audit_id;
	}
	if(filter != null) {
		url = url+ filter;
	}
    $.get(url, {
        }, function(data) {
            if(data=='0'){
                UIkit.modal.alert("There was a problem getting the project information.");
            } else {
				$('#allita-documents').html(data);
        	}
    });
}
