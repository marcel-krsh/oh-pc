

function completionCheck() {
	UIkit.modal('#modal-findings-completion-check', {center: true, bgclose: false, keyboard:false,  stack:true}).show();
}

function getCurrentFilter() {
	var activeFilterButton = $('.button-filter.uk-active').attr('uk-filter-control');
    var regexFilter = "data-finding='(.*)'"; //[data-finding='sd']

    var activeFilter = activeFilterButton.match(regexFilter);

    if(activeFilter !== null){
    	return activeFilter[1];
    }else{
    	return '';
    }
}

function searchFilterTerm(valThis) {
	// combine with active filter
	var currentActiveFilter = $('.button-filter.uk-active');
	var filterFindings= $('.js-filter-findings');

    var currentFilter = getCurrentFilter();

	var sortableElementParent = $('.js-filter-findings');

  	// if currentFilter is set, only search through the visible items
  	if(currentFilter.length){
  		var sortableElements = sortableElementParent.children("[data-finding='"+currentFilter+"']");
  	}else{
  		var sortableElements = sortableElementParent.children();
  	}
  	sortableElements.each(function(){
     	var text = this.getAttribute('data-title-finding').toLowerCase();
        (text.indexOf(valThis) >= 0) ? $(this).show() : $(this).hide();
    });
}

$('#finding-description').keyup(function(){
	debugger;
   var valThis = $(this).val().toLowerCase();
   searchFilterTerm(valThis);
});

function newFinding(id){

	// scroll to row early
    $('html, body').animate({
		scrollTop: $('#filter-checkbox-list-item-'+id).offset().top - 59
	}, 500, 'linear');

	if ($('#filter-checkbox-list-item-'+id).attr('expanded')){
		$('#filter-checkbox-list-item-'+id).removeAttr('expanded');
		$('.filter-checkbox-new-item-'+id).slideUp("slow", function() {

			$(this).remove();

			// reapply search
			var valThis = $('#finding-description').val();
   			searchFilterTerm(valThis);
		 });
	}else{

		var newFindingsFormTemplate = $('#modal-findings-new-form-template').html();
		var newFindingsTemplate = $('#modal-findings-new-form-template').html();

		var newfinding = newFindingsFormTemplate.replace(/tplFindingId/g, id);

		$('#filter-checkbox-list-item-'+id).append('<div style="display:none" class="uk-width-1-1 uk-padding-remove filter-checkbox-new-item-'+id+'">'+newfinding+'</div>');

		$('.filter-checkbox-new-item-'+id).slideDown("slow");
		$('#filter-checkbox-list-item-'+id).attr( "expanded", true );

		$('.findings-new-add-comment-textarea textarea').bind('input propertychange', function() {
		    console.log("trying to save to db... "+$(this).val());
		    $.post('/autosave', {
		        '_token' : '{{ csrf_token() }}'
		        }, function(data) {
		        	console.log("saved textarea returned "+data);
		    });
		});

	}

}

function refreshLocationFindingStreamFetch(type,auditid,buildingid,unitid,amenityid,toplevel,location = '') {
	// debugger;
	tempdiv = '<div style="height:200px;text-align:center;width: 44%;padding-left: 53%;"><div uk-spinner style="margin: 10% 0;"></div></div>';
	$('#modal-findings-items-container').html(tempdiv);
	$('#modal-findings-items-container').load('/modals/updatestream/'+type+'/'+auditid+'/'+buildingid+'/'+unitid+'/'+amenityid+'/'+toplevel+'/1'+'/'+location);
}

function refreshFindingStream(type,auditid,buildingid,unitid,amenityid,toplevel) {
	if(window.findingModalRightLocation) {
		$('#finding-modal-audit-stream-location-sticky').trigger('click');
	} else {
		tempdiv = '<div style="height:200px;text-align:center;width: 44%;padding-left: 53%;"><div uk-spinner style="margin: 10% 0;"></div></div>';
		$('#modal-findings-items-container').html(tempdiv);
		$('#modal-findings-items-container').load('/modals/updatestream/'+type+'/'+auditid+'/'+buildingid+'/'+unitid+'/'+amenityid+'/'+toplevel+'/1');
	}
}

function clickingOnFindingFilter(element, selected = 0, filter = '') {
	//console.log('clicking on a filter');
	// debugger;
	if(filter != '') {
		if(filter == 'mine') {
			window.findingModalRightMine = true;
			window.findingModalRightEveryone = false;
		} else if(filter == 'everyone') {
			window.findingModalRightMine = false;
			window.findingModalRightEveryone = true;
		}
		if(filter == 'current') {
			window.findingModalRightCurrent = true;
			window.findingModalRightAll = false;
		} else if(filter == 'all') {
			window.findingModalRightCurrent = false;
			window.findingModalRightAll = true;
		}
	}
	if($(element).find('span').is(':visible')){

		// switch order
		var currentOrdering = 'sort-asc';
	  	if($(this).find('span a').hasClass('sort-desc')){
			currentOrdering = 'sort-desc';
	  	}

	  	// switch the span data and the visual
	  	if(currentOrdering == "sort-asc"){
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children();
		  	//console.log(sortableElements.length);
		  	sortableElements.sort(function(a,b){
				var an = a.getAttribute('data-ordering-finding'),
					bn = b.getAttribute('data-ordering-finding');

				if(an > bn) {
					return 1;
				}
				if(an < bn) {
					return -1;
				}
				return 0;
			});
			 sortableElements.detach().appendTo(sortableElementParent);
	  		$(element).find('span a').removeClass('sort-asc').addClass('sort-desc');
	  	}else{
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children();
		  	//console.log(sortableElements.length);
		  	sortableElements.sort(function(a,b){
				var an = a.getAttribute('data-ordering-finding'),
					bn = b.getAttribute('data-ordering-finding');

				if(an < bn) {
					return 1;
				}
				if(an > bn) {
					return -1;
				}
				return 0;
			});
		    sortableElements.detach().appendTo(sortableElementParent);
	  		$(element).find('span a').removeClass('sort-desc').addClass('sort-asc');
	  	}
	}else{

		// close all spans in the group
		if($(element).hasClass('auditgroup')){
			$('.auditgroup').find('span').hide();
		}else if($(element).hasClass('findinggroup')){
			$('.findinggroup').find('span').hide();
		}

		// show selected one
		$(element).find('span').toggle();
	}
}

$('.filter-button-set').find('button.button-filter').click(function() {

  $('#finding-description').val('');
  // check if selected span is already visible, if so switch the order
  if($(this).closest('div').find('span').is(':visible')){
  	// what is the current ordering
  	var currentOrdering = 'sort-asc';
  	if($(this).closest('div').find('span a').hasClass('sort-desc')){
		currentOrdering = 'sort-desc';
  	}

  	// swtich the span data and the visual
  	if(currentOrdering == "sort-asc"){
  		var sortableElementParent = $('.js-filter-findings');
	  	var sortableElements = sortableElementParent.children();
	  	sortableElements.sort(function(a,b){
			var an = a.getAttribute('data-title-finding'),
				bn = b.getAttribute('data-title-finding');

			if(an > bn) {
				return 1;
			}
			if(an < bn) {
				return -1;
			}
			return 0;
		});
		sortableElements.detach().appendTo(sortableElementParent);

  		$(this).closest('div.uk-grid').find('span a').removeClass('sort-asc').addClass('sort-desc');
  	}else{
  		var sortableElementParent = $('.js-filter-findings');
	  	var sortableElements = sortableElementParent.children();
	  	sortableElements.sort(function(a,b){
			var an = a.getAttribute('data-title-finding'),
				bn = b.getAttribute('data-title-finding');

			if(an < bn) {
				return 1;
			}
			if(an > bn) {
				return -1;
			}
			return 0;
		});
		sortableElements.detach().appendTo(sortableElementParent);

  		$(this).closest('div.uk-grid').find('span a').removeClass('sort-desc').addClass('sort-asc');
  	}
  }else{
  	// hide all orderSpans
	$('.filter-button-set').find('span.order-span').hide();

	// show selected one
	var orderSpan = $(this).closest('div').find('span');
	orderSpan.toggle();
  }

  // is there a search term?
  var searchTerm = $('#finding-description').val();
  if(searchTerm.length > 0){
  	searchFilterTerm(searchTerm);
  }

});

function useBoilerplate(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}

function appendBoilerplate(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function insertTag(elem){
	var input = $("#finding-comment");
	var currentFinding = $('.findings-new-add-comment').data("finding-id");
    var cursorPos = input.prop('selectionStart');
    var v = input.val();
    var textBefore = v.substring(0,  cursorPos);
    var textAfter  = v.substring(cursorPos, v.length);

    $('.findings-new-add-comment-textarea textarea').val(textBefore + ' || ' + $(elem).data("tag")+ ' || ' + textAfter);
	$(input).focus();
	input[0].selectionStart = input[0].selectionEnd = input.val().length;
}
function saveBoilerplace(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function saveBoilerplaceAndNewFinding(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}

function expandFindingItems(element, type=null, typeid=null) {

	// is element a grandchild of a finding? if so add witin child

	// var parentitemid = '';
	var parentFindingContainer = $(element).closest("[data-finding-id], .inspec-tools-tab-finding");
	var findingId = parentFindingContainer.data('finding-id');
	//console.log("finding id: "+findingId);


	var parentItemContainer = $(element).closest("[data-parent-id], .inspec-tools-tab-finding-item");
	var parentitemid = parentItemContainer.data('parent-id');
	//console.log("item id: "+parentitemid);


	if (parentFindingContainer.attr('expanded') || parentItemContainer.attr('expanded')){
		if(parentitemid !== undefined){
			parentItemContainer.removeAttr('expanded');
			$('#inspec-tools-tab-finding-item-replies-'+parentitemid).slideUp("slow", function() {
				$(this).remove();
			 });
		}else{
			parentFindingContainer.removeAttr('expanded');
			$('#inspec-tools-tab-finding-items-'+findingId).slideUp("slow", function() {
				$(this).remove();

				// also remove corresponding sticky header
				$('#inspec-tools-tab-finding-sticky-'+findingId).show();
			 });
		}

	}else{

		if(parentitemid !== undefined){
			if($('#inspec-tools-tab-finding-item-replies-'+parentitemid).length == 0){
				var tempdiv = '<div id="inspec-tools-tab-finding-item-replies-'+parentitemid+'" class="uk-width-1-1 uk-margin-remove uk-padding-remove ">';
				tempdiv = tempdiv + '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
				tempdiv = tempdiv + '</div>';
				parentItemContainer.after(tempdiv);
			}
		}else{
			if($('#inspec-tools-tab-finding-items-'+findingId).length == 0){
				var tempdiv = '<div id="inspec-tools-tab-finding-items-'+findingId+'" class="uk-width-1-1 uk-margin-remove uk-padding-remove ">';
				tempdiv = tempdiv + '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
				tempdiv = tempdiv + '</div>';
				parentFindingContainer.append(tempdiv);
			}
		}

		// fetch and display new details
		if(type && typeid){
			var url = 'findings/'+findingId+'/items/'+type+'/'+typeid;
		}else{
			var url = 'findings/'+findingId+'/items';
		}

	    $.get(url, {
            }, function(data) {
                if(data=='0'){
                    UIkit.modal.alert("There was a problem getting the finding's replies.");
                } else {
					var findingsItemsTemplate = $('#inspec-tools-tab-finding-items-template').html();
					var findingsItemRepliesTemplate = $('#inspec-tools-tab-finding-item-replies-template').html();
					var findingsItemTemplate = $('#inspec-tools-tab-finding-item-template').html();
					var findingsItemStatTemplate = '<i class="tplStatIcon"></i> <span id="inspec-tools-tab-finding-stat-tplStatType">tplStatCount</span><br />';
					var findingsPhotoGalleryTemplate = $('#photo-gallery-template').html();
					var findingsPhotoGalleryItemTemplate = $('#photo-gallery-item-template').html();
					var findingsFileTemplate = $('#file-template').html();

					var items = '';
					var newitem = '';
					data.items.forEach(function(item) {
						newitem = findingsItemTemplate;
						newitem = newitem.replace(/tplItemId/g, item.id);
						newitem = newitem.replace(/tplFindingId/g, item.findingid);
						newitem = newitem.replace(/tplStatus/g, item.status);
						newitem = newitem.replace(/tplAuditId/g, item.audit);
						newitem = newitem.replace(/tplFindingId/g, item.findingid);
						newitem = newitem.replace(/tplIcon/g, item.icon);
						newitem = newitem.replace(/tplDate/g, item.date);
						newitem = newitem.replace(/tplRef/g, item.ref);
						newitem = newitem.replace(/tplTopActions/g, item.actions);


						var itemtype = item.type;
						var itemauditorname = item.auditor.name;
						var itemcontent = item.comment;
						var itemstickycontent = item.comment;
						switch(item.type) {
						    case 'followup':
						    	if(item.assigned.name != ''){
						        	itemauditorname = item.auditor.name+'<br />Assigned To: '+item.assigned.name;
						    	}
						    	itemcontent = '';
						    	if(item.duedate){
						    		itemcontent = itemcontent + 'Due Date: '+item.duedate+'<br />';
						    	}
						    	if(item.requested_action){
						    		itemcontent = itemcontent + 'Requested Action: '+item.requested_action+'<br />';
						    	}
						    	itemcontent = itemcontent + item.description;


						        itemtype = 'FLWUP';
						        break;
						    case 'comment':
						        itemtype = 'CMNT';
						        break;
						    case 'photo':
						        itemtype = 'PIC';
						        var images = '';
						        var newimage = '';
						        item.photos.forEach(function(pic) {
						        	newimage = findingsPhotoGalleryItemTemplate;
						        	newimage = newimage.replace(/tplUrl/g, pic.url);
						        	if(pic.commentscount == 0){
						        		newimage = newimage.replace(/tplComments/g, '');
						        	}else{
						        		newimage = newimage.replace(/tplComments/g, '<div class="uk-position-bottom-center uk-panel photo-caption use-hand-cursor"><i class="a-comment-text"></i> '+pic.commentscount+'</div>');
						        	}
						        	newimage = newimage.replace(/tplFindingId/g, item.findingid);
						        	newimage = newimage.replace(/tplItemId/g, item.id);
						        	newimage = newimage.replace(/tplPhotoId/g, pic.id);

						        	images = images + newimage;
						        });
						        itemcontent = findingsPhotoGalleryTemplate.replace(/tplPhotos/g, images);
						        itemstickycontent = '';
						        break;
						    case 'file':
						        itemtype = 'DOC';
						        var categoryTemplate = "<div class='finding-file-category'>tplCatName</div>";
						        var categories = '';
						        var newcategory = '';
						        var file = '';
						        item.categories.forEach(function(cat) {
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

						        file = categories+"<br /><a href=\"download-local-document/"+item.file.id+"\" target=\"_blank\"  uk-tooltip=\"Download file.\" download class='finding-file use-hand-cursor'><i class='a-down-arrow-circle'></i> "+item.file.name+"<br />"+item.file.size+" "+item.file.type+"</a><br /><br />"+item.file.comment;

						        itemcontent = findingsFileTemplate.replace(/tplFileContent/g, file);
						        break;
						}
						newitem = newitem.replace(/tplType/g, itemtype);
						newitem = newitem.replace(/tplName/g, itemauditorname);
						newitem = newitem.replace(/tplContent/g, itemcontent);
						newitem = newitem.replace(/tplStickyContent/g, itemstickycontent);

						var newstat = '';
						var stats = '';
						var statcount = 0;
						item.stats.forEach(function(stat) {
							newstat = findingsItemStatTemplate;
							newstat = newstat.replace(/tplStatIcon/g, stat.icon);
							newstat = newstat.replace(/tplStatType/g, stat.type);
							newstat = newstat.replace(/tplStatCount/g, stat.count);

							statcount = statcount + stat.count;

							stats = stats + newstat;
						});
						if(statcount > 0){
							stats = stats + '<i class="a-menu" onclick="expandFindingItems(this,\''+item.type+'\',\''+item.id+'\');"></i>';
						}

						newitem = newitem.replace(/tplStats/g, stats);

						if(parentitemid !== undefined){
							newitem = newitem.replace(/tplIsReply/g, 'reply');
						}else{
							newitem = newitem.replace(/tplIsReply/g, 'notreply');
						}

						items = items + newitem.replace(/tplParentItemId/g, item.parentitemid);
					});

					if(parentitemid !== undefined){
						console.log("opening replies "+'#inspec-tools-tab-finding-item-replies-'+parentitemid);

						$('#inspec-tools-tab-finding-item-replies-'+parentitemid).html(findingsItemRepliesTemplate);
						$('#inspec-tools-tab-finding-item-replies-'+parentitemid).find('.inspec-tools-tab-finding-item-replies-list').html(items);

						//remove sticky div
						$('#inspec-tools-tab-finding-item-replies-'+parentitemid).find('.inspec-tools-tab-finding-reply-sticky').remove();

						$('#inspec-tools-tab-finding-item-replies-'+parentitemid).find('.inspec-tools-tab-finding-item-replies').slideDown("slow");
						parentItemContainer.attr( "expanded", true );
					}else{
						console.log("opening finding items");
						$('#inspec-tools-tab-finding-items-'+findingId).html(findingsItemsTemplate);
						$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items-list').html(items);

						$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items').slideDown("slow");
						parentFindingContainer.attr( "expanded", true );
					}

				}
	    });

	}


}

function addChildItem(id, type, fromtype='finding', level=2) {
	dynamicModalLoad('addreply/'+id+'/'+fromtype+'/'+type+'/'+level, 0, 0, 0, level);
}

function openFindingPhoto(findingid, itemid, id) {
	dynamicModalLoad('findings/'+findingid+'/items/'+itemid+'/photos/'+id, 0, 0, 0, 2);
}

function openFindingFile() {
	console.log("open file");
}
