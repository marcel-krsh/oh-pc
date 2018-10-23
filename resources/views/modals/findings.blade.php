<div id="modal-findings" class="uk-margin-top" >
	<div class="modal-findings-right" uk-filter="target: .js-findings">
		<div class="modal-findings-right-top">
		    <div class="uk-width-1-1 filter-button-set-right" uk-grid>
		        <div class="uk-width-1-4 uk-active findinggroup" uk-filter-control="filter: [data-finding-filter*='my-finding']; group: findingfilter;">
	                <button class="uk-button uk-button-default button-filter button-filter-border-left" >My finding</button>
		        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 findinggroup" uk-filter-control="filter: [data-finding-filter*='all']; group: findingfilter;">    
	            	<button class="uk-button uk-button-default button-filter">All findings</button>
		        	<span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 uk-active auditgroup" uk-filter-control="filter: [data-audit-filter*='this-audit']; group: auditfilter;">
	                <button class="uk-button uk-button-default button-filter">this audit</button>
	                <span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 auditgroup" uk-filter-control="filter: [data-audit-filter*='all']; group: auditfilter; ">
	                <button class="uk-button uk-button-default button-filter">all audits</button>
	                <span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
		        </div>
		    </div>
		   </div>
	    <div class="modal-findings-right-bottom-container">
			<div class="modal-findings-right-bottom">
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-padding-remove js-findings">
			    	@foreach($data['findings'] as $finding)
			        <div id="inspec-tools-tab-finding-{{$finding['id']}}" class="inspec-tools-tab-finding" data-ordering-finding="{{$finding['id']}}" data-finding-id="{{$finding['id']}}" data-audit-filter="{{$finding['audit-filter']}} all" data-finding-filter="{{$finding['finding-filter']}} all" uk-grid>
			        	<div class="inspec-tools-tab-finding-sticky uk-width-1-1 uk-padding-remove  {{$finding['status']}}" style="display:none">
			        		<div class="uk-grid-match" uk-grid>
								<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">1</div>
								<div class="uk-width-3-4 uk-padding-remove-top uk-padding-remove-right">2</div>
			        		</div>
			        	</div>

						<div class="inspec-tools-tab-finding-info uk-width-1-1  uk-active {{$finding['status']}}" style="padding-top: 15px;">
		    				<div class="uk-grid-match" uk-grid>
				    			<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
				    				<div class="uk-display-block">
					    				<i class="{{$finding['icon']}}"></i><br>
					    				<span class="auditinfo">AUDIT {{$finding['audit']}}</span>
					    			</div>
					    			<div class="uk-display-block" style="margin: 15px 0;">
					    				<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">
									    	 &nbsp; </span>RESOLVE</button>
									</div>
									<div class="inspec-tools-tab-finding-stats" style="margin: 0 0 15px 0;">
										<i class="a-bell-plus"></i> <span id="inspec-tools-tab-finding-stat-reminders">0</span><br />
										<i class="a-comment-plus"></i> 1<br />
										<i class="a-file-plus"></i> 0<br />
										<i class="a-picture"></i> 0<br />

										<i class="a-menu" onclick="expandFindingItems(this);"></i>
									</div>
				    			</div>
				    			<div class="uk-width-3-4 uk-padding-remove-right uk-padding-remove">
				    				<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
					    				<p>{{$finding['date']}}: FN#{{$finding['ref']}}<br />
					    					By {{$finding['auditor']['name']}}<br>
					    					{{$finding['amenity']['address']}}<br />
					    					{{$finding['amenity']['city']}}, {{$finding['amenity']['state']}} {{$finding['amenity']['zip']}}
					    				</p>
					    				<p>{{$finding['building']['name']}}: {{$finding['amenity']['name']}}<br />{{$finding['description']}}</p>
					    				<div class="inspec-tools-tab-finding-actions">
										    <button class="uk-button uk-link"><i class="a-pencil-2"></i> EDIT</button>
					    					<button class="uk-button uk-link"><i class="a-trash-3"></i> DELETE</button>
					    				</div>
					    				<div class="inspec-tools-tab-finding-top-actions">
					    					<i class="a-circle-plus use-hand-cursor"></i>
										    <div uk-drop="mode: click" style="min-width: 315px;">
										        <div class="uk-card uk-card-body uk-card-default uk-card-small">
										    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
										    	 		<div class="icon-circle use-hand-cursor" onclick="addChildItem({{$finding['id']}}, 'followup')"><i class="a-bell-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'comment')"><i class="a-comment-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'document')"><i class="a-file-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'photo')"><i class="a-picture"></i></div>
										    	 	</div>
										        </div>
										    </div>
					    				</div>
					    			</div>
				    			</div>
				    		</div>
				    	</div>
					</div>
				    @endforeach
				</div>
			</div>
		</div>

	</div>
	<div class="modal-findings-left" uk-filter="target: .js-filter-findings">
		<div class="modal-findings-left-bottom-container">
			<div class="modal-findings-left-bottom">
				<div id="modal-findings-filters" class="uk-margin uk-child-width-auto" uk-grid>
			        <div class="uk-width-1-1 uk-padding-remove uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Showing all inspectable amenities for the buiding selected');"><i class="a-mobile-home"></i> Elevator #2</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Showing all buildings for the address selected');"><i class="a-buildings"></i> Building</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Change address within project');"><i class="a-buildings"></i> 1234567 Silvegwood Street, Colombus, OH 43219</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" disabled><i class="a-calendar-pencil"></i> December 22, 2018</button>
					</div>
		        </div>
		        <div class="uk-margin-remove" uk-grid>
            		<div class="uk-width-1-1 uk-padding-remove">
            			<button class="uk-button uk-button-primary button-finding-filter uk-width-1-1 @if(!$checkDoneAddingFindings) uk-modal-close @endif" @if($checkDoneAddingFindings) onclick="completionCheck();return false;" @endif>DONE ADDING FINDINGS</button>
            		</div>
            	</div>
			</div>
		</div>
		<div class="modal-findings-left-main-container">
			<div class="modal-findings-left-main">
				<div id="modal-findings-list-filters" class="uk-margin uk-child-width-auto uk-grid filter-checkbox-list js-filter-findings">
						@foreach($data['finding-types'] as $finding_type)
						<div id="filter-checkbox-list-item-{{$finding_type['id']}}" class=" uk-padding-remove filter-checkbox-list-item" data-finding="{{$finding_type['type']}}" data-title-finding="{{$finding_type['name']}}" uk-grid>
							<div class="uk-width-1-1 uk-padding-remove indented">
					            <input id="filter-findings-filter-{{$finding_type['id']}}" value="" type="checkbox" data-finding="{{$finding_type['type']}}" onclick="newFinding({{$finding_type['id']}});"/>
								<label for="filter-findings-filter-{{$finding_type['id']}}" data-finding="{{$finding_type['type']}}" ><i class="{{$finding_type['icon']}}"></i> {{$finding_type['name']}}</label>
							</div>
						</div>
						@endforeach

		        </div>
			</div>
		</div>
		<div class="modal-findings-left-top" uk-grid>
			<div class="uk-width-1-1 filter-button-set">
				<div uk-grid>
			        <div class="uk-inline uk-width-1-2">
			            <i class="a-magnify-2 uk-form-icon"></i>
			            <input name="finding-description" id="finding-description" class="uk-input button-filter" placeholder="ENTER FINDING DESCRIPTION" type="text">
			        </div>
			        <div class="uk-inline uk-width-1-2">
			        	<div uk-grid>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter uk-active" uk-filter-control><i class="uk-icon-asterisk"></i></button>
					        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='file'];"><i class="a-folder"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='nlt'];"><i class="a-booboo"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='lt'];"><i class="a-skull"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-asc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='sd'];"><i class="a-flames"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>

@include('templates.modal-findings-new-form')
@include('templates.modal-findings-new')
@include('templates.modal-findings-items')

<div id="modal-findings-completion-check" uk-modal>
  <div class="uk-modal-dialog uk-modal-body uk-modal-content" uk-overflow-auto> 
  	<a class="uk-modal-close-default" uk-close></a>
  	<div uk-grid>
  		<div class="uk-width-1-2  uk-margin-medium-top">
  			<p>Have you finished inspecting all items for that building/unit/common area?</p> 
  			<div class="uk-padding-remove" uk-grid>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-primary uk-margin-left uk-margin-right uk-padding-remove uk-margin-remove uk-width-1-1">Yes, Mark as Complete and Submit to Lead.</button>
	  			</div>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-primary uk-padding-remove uk-margin-remove uk-width-1-1">Just the Items I have Findings For.</button>
	  			</div>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-default uk-padding-remove uk-margin-remove uk-width-1-1 uk-modal-close">No, I am still working on it.</button>
	  			</div>
	  		</div>
  		</div>
  		<div class="uk-width-1-2  uk-margin-medium-top">
  			<div>bulleted list of items that have not had any findings here<br />
  			<ul class="uk-list">
  				<li>item</li>
  				<li>item</li>
  			</ul>
  		</div>
  	</div>
  </div>
 </div>

<script>

$(".inspec-tools-tab-findings-container").on( 'scroll', function(){

	// what is the current finding
	// 
	// is that finding expanded
	// 
	// if expanded, check for scrolling position and display sticky header

    var offset = $(".inspec-tools-tab-findings-container").scrollTop(); 

    if ($(".inspec-tools-tab-findings-container").scrollTop() > 40) {
        $(".inspec-tools-tab-finding-sticky").fadeIn( "fast" );
        $(".inspec-tools-tab-finding-sticky").css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
    } else {
        $(".inspec-tools-tab-finding-sticky").css("margin-top", 0);
        $(".inspec-tools-tab-finding-sticky").fadeOut("fast");
    };

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
   var valThis = $(this).val();
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

	var parentFindingContainer = $(element).closest("[data-finding-id], .inspec-tools-tab-finding");
	var findingId = parentFindingContainer.data('finding-id');
	console.log(findingId);

	if (parentFindingContainer.attr('expanded')){
		parentFindingContainer.removeAttr('expanded');
		$('#inspec-tools-tab-finding-items-'+findingId).slideUp("slow", function() {

			$(this).remove();
		 });
	}else{

		if($('#inspec-tools-tab-finding-items-'+findingId).length == 0){
			var tempdiv = '<div id="inspec-tools-tab-finding-items-'+findingId+'" class="uk-width-1-1 uk-margin-remove uk-padding-remove ">';
			tempdiv = tempdiv + '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
			tempdiv = tempdiv + '</div>';
			parentFindingContainer.append(tempdiv);
		}

		// fetch and display new details
		var url = 'findings/'+findingId+'/items';
	    $.get(url, {
            }, function(data) {
                if(data=='0'){ 
                    UIkit.modal.alert("There was a problem getting the finding's replies.");
                } else {
					var findingsItemsTemplate = $('#inspec-tools-tab-finding-items-template').html();
					var findingsItemTemplate = $('#inspec-tools-tab-finding-item-template').html();
					var findingsItemStatTemplate = '<i class="tplStatIcon"></i> <span id="inspec-tools-tab-finding-stat-tplStatType">tplStatCount</span><br />';
					var findingsPhotoGalleryTemplate = $('#photo-gallery-template').html();
					var findingsPhotoGalleryItemTemplate = $('#photo-gallery-item-template').html();
					var findingsFileTemplate = $('#file-template').html();

					var items = '';
					var newitem = '';
					data.items.forEach(function(item) {
						newitem = findingsItemTemplate;
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

						items = items + newitem.replace(/tplParentItemId/g, item.parentitemid);
					});

					$('#inspec-tools-tab-finding-items-'+findingId).html(findingsItemsTemplate);
					$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items-list').html(items);

					$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items').slideDown("slow");
					parentFindingContainer.attr( "expanded", true );
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


</script>