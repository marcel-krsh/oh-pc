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
			loadTab("dashboard/audits", "1");

      	});	
    });
}

function filterAuditList(element, searchClass){
	// clear all other filters
	$('.filter-box').not(element).val('');

	var value = $(element).val().toLowerCase();
	
	$('tr[id^="audit-r-"]').each(function() {
		var parentElement = this;
		var found = 0; // we may look through multiple fields with the same class

		$(this).find('.'+searchClass).each(function() {
    		if($(this).text().toLowerCase().search(value) > -1) {
    			if(found == 0){
    				found = 1;
    				$(parentElement).show();
    			}
    		}else{
    			if(found == 0){
    				$(parentElement).hide();
    			}
    		}
    	});
	});
}

function toggleArchivedAudits() {
	$(".archived-icon").toggle();
}

function createAudits(){
	console.log("create audits clicked");
}

function projectDetails(id, target, buildingcount = 10) {
	if ($('#audit-r-'+target+'-buildings').length){

		// close own details
		$('#audit-r-'+target+'-buildings').remove();
	}else{
		// scroll to row early
    	$('html, body').animate({
			scrollTop: $('#audit-r-'+target).offset().top - 59
			}, 500, 'linear');

		// close all details
		$('tr[id$="-buildings"]').remove();

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

function loadInspectionMenu(data, id, context='audits', level='') {
	var inspectionLeftTemplate = $('#inspection-left-template').html();
	var inspectionMenuItemTemplate = $('#inspection-menu-item-template').html();

	var menus = '';
	var newmenu = '';
	data.forEach(function(menuitem) {
		newmenu = inspectionMenuItemTemplate;
		newmenu =  newmenu.replace(/menuName/g, menuitem.name);
		newmenu = newmenu.replace(/menuAction/g, menuitem.action);
		newmenu = newmenu.replace(/menuTarget/g, id);
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




	// $('#inspection-menus-'+id).html(inspectionLeftTemplate);
	// $('#inspection-menus-'+id).fadeIn( "slow", function() {
	//     // Animation complete
	//   });
}

function loadInspectionTools(data, id, context='audits', level = '') {
	var inspectionToolsTemplate = $('#inspection-tools-template').html();

	$('#inspection-'+context+'-'+level+'tools-'+id).html(inspectionToolsTemplate);
	$('#inspection-'+context+'-'+level+'tools-'+id+'-container').fadeIn( "slow", function() {
		    // Animation complete
		  });
	
}

function loadInspectionMain(data, id, context='audits', level = '') {
	var inspectionMainTemplate = $('#inspection-areas-template').html();
	var inspectionAreaTemplate = $('#inspection-area-template').html();

	var areas = '';
	var newarea = '';

	data.forEach(function(area) {
		newarea = inspectionAreaTemplate;
		newarea = newarea.replace(/areaContext/g, context);
		newarea = newarea.replace(/areaRowId/g, area.id);
		newarea = newarea.replace(/areaName/g, area.name);
		newarea = newarea.replace(/areaStatus/g, area.status);
		newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);
		newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);

		newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);
		newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
		newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
		newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
		newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
		newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
		newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);

		areas = areas + newarea.replace(/areaAuditorColor/g, area.auditor_color);
	});
	$('#inspection-'+context+'-'+level+'main-'+id).html(inspectionMainTemplate);
	$('#inspection-'+context+'-'+level+'main-'+id+' .inspection-areas').html(areas);
	$('#inspection-'+context+'-'+level+'main-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	  });

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

		if(comment.replies){
			comment.replies.forEach(function(reply){
				newreply = inspectionCommentReplyTemplate;
				newreply = newreply.replace(/tplCommentTypeIcon/g, reply.type_icon);
				newreply = newreply.replace(/tplCommentType/g, reply.type_text);
				newreply = newreply.replace(/tplCommentAuditId/g, reply.audit_id);
				newreply = newreply.replace(/tplCommentCreatedAt/g, reply.created_at);
				newreply = newreply.replace(/tplCommentUserName/g, reply.user_name);
				newreply = newreply.replace(/tplCommentContent/g, reply.content);

				replies = replies + newreply;
			});
		}

		if(comment.actions_json){
			JSON.parse(comment.actions_json).forEach(function(action){
				newaction = '<div class="uk-width-1-4"><button class="uk-button uk-link inspec-tools-tab-finding-button"><i class="tplActionIcon"></i> tplActionText</button></div>';
				newaction = newaction.replace(/tplActionIcon/g, action.icon);
				newaction = newaction.replace(/tplActionText/g, action.name);

				actions = actions + newaction;
			});

			newcomment = newcomment.replace(/tplCommentActions/g, actions);
		}

		newcomment = newcomment.replace(/tplCommentReplies/g, newreply);

		comments = comments + newcomment.replace(/tplCommentContent/g, comment.content);
	});
	
	$('.inspec-tools-tab-finding').replaceWith(comments);
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
					loadInspectionMenu(data.menu, target, context);
					loadInspectionMain(data.amenities, target, context);
					loadInspectionTools(data, target, context);
					loadInspectionComments(data.comments, target, context);
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
					loadInspectionMenu(data.menu, target, context, 'detail-');
					loadInspectionMain(data.amenities, target, context, 'detail-');
					loadInspectionTools(data, target, context, 'detail-');
					loadInspectionComments(data.comments, target, context, 'detail-');
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

function switchInspectionMenu(action, context, level='', id){
	console.log("Switching menu "+action+level);
	$('#inspection-'+context+'-'+level+'main-'+id).html("Switching menu "+action+level);
	$('#inspection-'+context+'-'+level+'main-'+id+' .inspection-areas').html('areas');
	$('#inspection-'+context+'-'+level+'main-'+id+'-container').fadeIn( "slow", function() {
	    // Animation complete
	  });
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

function reorder(classname, childclassname) {
	var currentIndex;
	$(classname+" > div").each(function () { 
		// console.log(this.id);
		currentIndex = $(childclassname).index(this) + 1;
		$("#"+this.id).find(".rowindex").each(function () { 
			$(this).html(currentIndex);
		});
	});
}

function openFindings(element, auditid, buildingid, unitid='', type){
	dynamicModalLoad('findings/'+type+'/audit/'+auditid+'/building/'+buildingid+'/unit/'+unitid,1,0,1);
}

function reorderBuildings(auditId, buildingId, endIndex) {
	var url = 'dashboard/audits/'+auditId+'/buildings/reorder';
	$.get(url, {
        'building' : buildingId,
        'index' : endIndex
        }, function(data) {
            if(data=='0'){ 
                UIkit.modal.alert("There was a problem reordering the buildings.");
            } else {
				console.log("reordering completed");
			}
    });
}

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

$(function () {
	$(document).on('start', '.sortablebuildings', function (item) {
		//console.log("almost moving....");
		var listItem = document.getElementById( item.detail[1].id );
		//console.log(item.detail[1].id);
		if($('#'+item.detail[1].id).hasClass('building-detail')){
			startIndex = $( ".building-detail" ).index( listItem );
		}else if($('#'+item.detail[1].id).hasClass('building')){
			startIndex = $( ".building" ).index( listItem );
		}else if($('#'+item.detail[1].id).hasClass('inspection-area')){
			startIndex = $( ".inspection-area" ).index( listItem );
		} 				
		//console.log( item.detail[1].id + " started at index: " + startIndex );
	});
	$(document).on('moved', '.sortablebuildings', function (item) {
		//console.log("moving....");
		var listItem = document.getElementById( item.detail[1].id );
		var auditId = $(listItem).data('audit');
		var buildingId = $(listItem).data('building');

		if($('#'+item.detail[1].id).hasClass('building-detail')){
			var unitId = $(listItem).data('area');
			endIndex = $( ".building-detail" ).index( listItem );
			console.log( item.detail[1].id + " ended at index: " + endIndex );
			UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
			reorder(".building-details > .sortable", '.building-detail');

			reorderUnits(auditId, buildingId, unitId, endIndex);

		}else if($('#'+item.detail[1].id).hasClass('building')){
			endIndex = $( ".building" ).index( listItem );
			//console.log( item.detail[1].id + " ended at index: " + endIndex );
			//UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
			reorder(".buildings > .sortable", '.building');

			reorderBuildings(auditId, buildingId, endIndex);
			
			//console.log("endIndex "+endIndex+' '+item.detail[1].id);
			// update journey icons
			var length = $('.building').length;
			$('.building').each(function(index, element) {
				$(element).find( ".journey-start" ).addClass('journey');
				$(element).find( ".journey-start" ).removeClass('journey-start');
				$(element).find( ".journey-end" ).addClass('journey');
				$(element).find( ".journey-end" ).removeClass('journey-end');
				$(element).find( ".a-home-marker" ).addClass('a-marker-basic');
				$(element).find( ".a-home-marker" ).removeClass('a-home-marker');

			    if (index == 0) {
			        $(element).find( ".journey" ).addClass('journey-start');
					$(element).find( ".journey" ).removeClass('journey');
					$(element).find( ".a-marker-basic" ).addClass('a-home-marker');
					$(element).find( ".a-marker-basic" ).removeClass('a-marker-basic');
		        }
				if (index == (length - 2)) {
			        $(element).find( ".journey" ).addClass('journey-end');
					$(element).find( ".journey" ).removeClass('journey');
					$(element).find( ".a-marker-basic" ).addClass('a-home-marker');
					$(element).find( ".a-marker-basic" ).removeClass('a-marker-basic');
			    }
			});
		}else if($('#'+item.detail[1].id).hasClass('inspection-area')){
			endIndex = $( ".inspection-area" ).index( listItem );
			console.log( item.detail[1].id + " ended at index: " + endIndex );
			UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
			// reorder(".inspection-main-list > .sortable", '.inspection-area');
		}
	});
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

function projectDetailsInfo(id, type, target) {
	var tempdiv = '<div style="height:100px;text-align:center;"><div uk-spinner style="margin: 20px 0;"></div></div>';
	$('#project-details-info-container').html(tempdiv);

	// remove active buttons
	$('#project-details-buttons').find('.uk-button').removeClass('active');
	$(target).addClass('active');

	var url = '/projects/'+id+'/details/'+type;
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



