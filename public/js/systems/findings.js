$(".inspec-tools-tab-findings-container").on( 'scroll', function(){

    var offset = $(".inspec-tools-tab-findings-container").scrollTop(); 

    var currentFinding ="";
    var currentFindingId ="";
    var position = 0;
    var findingId= "";
    var currentItem ="";
    var currentItemId ="";
    var positionItem = 0;
    var itemId= "";
    var tmpPosition = -offset;
    var tmpPositionItem = - offset - 48;

    if ($(".inspec-tools-tab-findings-container").scrollTop() > 40) {
    	// console.log('scrolltop > 40');

    	$.each($(".inspec-tools-tab-finding-sticky"), function(index, item) {

	    	currentFinding = $(item).closest("[data-finding-id], .inspec-tools-tab-finding");
		    currentFindingId = currentFinding.data('finding-id');
		    position = $(currentFinding).offset().top - $(currentFinding).offsetParent().offset().top;

	        if(position < 0){
	        	if(position >= tmpPosition) {
	        		tmpPosition = position;
	        		findingId = currentFindingId;
	        	}
	        }
	    });

    	$.each($(".inspec-tools-tab-finding-reply-sticky"), function(index, item) {

	    	currentItem = $(item).closest("[data-parent-id], .inspec-tools-tab-finding-item");
		    currentItemId = currentItem.data('parent-id');
		    positionItem = $(currentItem).offset().top - $(currentItem).offsetParent().offset().top;
			// console.log("currentItemId "+currentItemId+" | positionItem "+positionItem+" | tmpPositionItem "+tmpPositionItem);
	        if(positionItem < 40){
	        	if(positionItem >= tmpPositionItem) {
	        		tmpPositionItem = positionItem;
	        		itemId = currentItemId;
	        	}
	        }
	    });

	    // console.log("Finding id: "+findingId+" | Item id: "+itemId);

    	// console.log("finding: "+findingId);
    	$('div[id^="inspec-tools-tab-finding-sticky-"]').not( 'div[id="inspec-tools-tab-finding-sticky-'+findingId+'"]' ).hide();
    	$('div[id^="inspec-tools-tab-finding-reply-sticky-"]').not( 'div[id="inspec-tools-tab-finding-reply-sticky-'+itemId+'"]' ).hide();

	    if($('#inspec-tools-tab-finding-'+findingId).attr('expanded')){
	    	// console.log('#inspec-tools-tab-finding-'+findingId+' expanded');

	        $('#inspec-tools-tab-finding-sticky-'+findingId).fadeIn( "fast" );
	        $('#inspec-tools-tab-finding-sticky-'+findingId).css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
		}else{
			// hide that sticky
			// console.log('hiding #inspec-tools-tab-finding-sticky-'+findingId+'');
			$('#inspec-tools-tab-finding-sticky-'+findingId).fadeOut("fast");
		}
		
		if($(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-item-'+itemId).attr('expanded')){
	    	
	        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).fadeIn( "fast" );
	        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
		}else{
			// hide that sticky
			// console.log('hiding #inspec-tools-tab-finding-sticky-'+findingId+'');
			$(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).fadeOut("fast");
		}
    } else {
    	// hide the sticky for all findings
    	// console.log('scrolltop <= 40');
	    $('div[id^="inspec-tools-tab-finding-sticky-"]').css("margin-top", 0);
    	$('div[id^="inspec-tools-tab-finding-sticky-"]').fadeOut("fast");
    }

});    

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

$('.filter-button-set-right').find('[uk-filter-control]').click(function() {
	if($(this).find('span').is(':visible')){

		// switch order
		var currentOrdering = 'sort-asc';
	  	if($(this).find('span a').hasClass('sort-desc')){
			currentOrdering = 'sort-desc';
	  	}

	  	// switch the span data and the visual
	  	if(currentOrdering == "sort-asc"){
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children(); console.log(sortableElements.length);
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
	  		$(this).find('span a').removeClass('sort-asc').addClass('sort-desc');
	  	}else{
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children();console.log(sortableElements.length);
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
	  		$(this).find('span a').removeClass('sort-desc').addClass('sort-asc');
	  	}
	}else{

		// close all spans in the group
		if($(this).hasClass('auditgroup')){
			$('.auditgroup').find('span').hide();
		}else if($(this).hasClass('findinggroup')){
			$('.findinggroup').find('span').hide();
		}

		// show selected one
		$(this).find('span').toggle();
	}
});

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
function clearTextarea(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function appendBoilerplate(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function insertTag(elem){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");
    var cursorPos = $('.findings-new-add-comment-textarea textarea').prop('selectionStart');
    var v = $('.findings-new-add-comment-textarea textarea').val();
    var textBefore = v.substring(0,  cursorPos);
    var textAfter  = v.substring(cursorPos, v.length);

    $('.findings-new-add-comment-textarea textarea').val(textBefore + '%%' + $(elem).data("tag")+ '%%' + textAfter);

}
function saveBoilerplace(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function saveBoilerplaceAndNewFinding(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}

function expandFindingItems(element) {

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
				$('#inspec-tools-tab-finding-sticky-'+findingId).slideOut("fast");
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
		var url = 'findings/'+findingId+'/items';
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

						var itemtype = item.type;
						var itemauditorname = item.auditor.name;
						var itemcontent = item.comment;
						var itemstickycontent = item.comment;
						switch(item.type) {
						    case 'followup':
						        itemauditorname = item.auditor.name+'<br />Assigned To: '+item.assigned.name;
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
						        	newimage = newimage.replace(/tplComments/g, pic.commentscount);
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
						        var categoryTemplate = "<div class='finding-file-category'><i class='tplCatIcon'></i> tplCatName</div>";
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

						        file = categories+"<div class='finding-file use-hand-cursor' onclick='openFindingFile();'><i class='a-down-arrow-circle'></i> "+item.file.name+"<br />"+item.file.size+" MB "+item.file.type+"</div>";

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
							stats = stats + '<i class="a-menu" onclick="expandFindingItems(this);"></i>';
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

function addChildItem(findingId, type) {
	console.log("adding a child item to this finding");
}

function openFindingPhoto(findingid, itemid, id) {
	dynamicModalLoad('findings/'+findingid+'/items/'+itemid+'/photos/'+id, 0, 0, 0, 2);
}

function openFindingFile() {
	console.log("open file");
}
