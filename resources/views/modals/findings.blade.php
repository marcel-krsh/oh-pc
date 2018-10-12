<div id="modal-findings" class="uk-margin-top">
	
	<div class="modal-findings-right">
		
		<div class="modal-findings-right-bottom-container">
			<div class="modal-findings-right-bottom">
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-height-large uk-height-max-large">
		    		<div class="inspec-tools-tab-finding action-needed uk-grid uk-grid-stack" uk-grid="">
		    			<div class="uk-width-1-1 uk-first-column" style="padding-top: 15px;">
		    				<div uk-grid="" class="uk-grid">
				    			<div class="uk-width-1-4 uk-first-column">
				    				<i class="a-booboo"></i> NLT<br>
				    				<span class="auditinfo">AUDIT 20120394</span><br /><br />
				    				<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">
								    	 &nbsp; </span>RESOLVE</button>
				    			</div>
				    			<div class="uk-width-3-4 bordered">
				    				<p>12/22/2018 12:51:38 PM: By Holly Swisher<br>
				    				STAIR #1 : Finding Description Goes here and continues here for when it is long</p>
				    			</div>
				    		</div>
				    	</div>
				    	<div class="uk-width-1-1 uk-margin-remove inspec-tools-tab-finding-comment uk-grid-margin uk-first-column">
				    		<div uk-grid="" class="uk-grid">
				    			<div class="uk-width-1-4 uk-first-column">
				    				<i class="a-comment-text"></i> COMMENT
				    			</div>
				    			<div class="uk-width-3-4 borderedcomment">
				    				<p>12/31/2018 12:59:38 PM: By Holly Swisher<br>
				    					<span class="finding-comment">Comment goes here and is italicised to show that it is a comment and not a finding.</span></p>
									<button class="uk-button inspec-tools-tab-finding-reply uk-link">
				    					<i class="a-comment-pencil"></i> REPLY
								    </button>
				    			</div>
				    		</div>
				    	</div>
				    	<div class="uk-width-1-1 uk-margin-remove uk-grid-margin uk-first-column">
				    		<div uk-grid="" class="uk-grid">
				    			<div class="uk-width-1-4 uk-first-column">
				    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
				    					<i class="a-calendar-pencil"></i> FOLLOW UP
								    </button>
				    			</div>
				    			<div class="uk-width-1-4">
				    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
				    					<i class="a-comment-text"></i> COMMENT
								    </button>
				    			</div>
				    			<div class="uk-width-1-4">
				    				<button class="uk-button uk-link inspec-tools-tab-finding-button colored">
				    					<i class="a-file-clock"></i> DOCUMENT
								    </button>
				    			</div>
				    			<div class="uk-width-1-4">
				    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
				    					<i class="a-picture"></i> PHOTO
								    </button>
				    			</div>
				    		</div>
				    	</div>
		    		</div>
		    	</div>
			</div>
		</div>
		<div class="modal-findings-right-top">
			<div class="uk-width-1-1 filter-button-set">
	        	<div uk-grid>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter button-filter-border-left button-filter-selected">MY FINDINGS</button>
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">ALL FINDINGS</button>
			        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
							<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
						</span>
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">THIS AUDIT</button>
			        	
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">ALL AUDITS</button>
			        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
							<a id="" class="sort-asc" onclick="loadFindingsList();"></a>
						</span>
	        		</div>
	        	</div>
	        </div>
		</div>
	</div>
	<div class="modal-findings-left" uk-filter="target: .js-filter-findings">
		<div class="modal-findings-left-bottom-container">
			<div class="modal-findings-left-bottom">
				<form id="modal-findings-filter-form" class="modal-form">
    				<fieldset class="uk-fieldset">
    					<div id="modal-findings-filters" class="uk-margin uk-child-width-auto" uk-grid>
					        <div class="uk-width-1-1 uk-padding-remove uk-inline">
					            <i class="a-mobile-home uk-form-icon"></i>
					            <input class="uk-input" type="text" value="Elevator #2" disabled>
							</div>
					        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
					            <i class="a-buildings uk-form-icon"></i>
					            <input class="uk-input" type="text" value="Building" disabled>
							</div>
					        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
					            <i class="a-buildings uk-form-icon"></i>
					            <input class="uk-input" type="text" value="1234567 Silvegwood Street, Colombus, OH 43219" disabled>
							</div>
					        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
					            <i class="a-calendar-pencil uk-form-icon"></i>
					            <input class="uk-input" type="text" value="December 22, 2018" disabled>
							</div>
				        </div>
				        <div class="uk-margin-remove" uk-grid>
                    		<div class="uk-width-1-1 uk-padding-remove">
                    			<button class="uk-button uk-button-primary uk-width-1-1 uk-modal-close">DONE ADDING FINDINGS</button>
                    		</div>
                    	</div>
    				</fieldset>
    			</form>
			</div>
		</div>
		<div class="modal-findings-left-main-container">
			<div class="modal-findings-left-main">
				<div id="modal-findings-list-filters" class="uk-margin uk-child-width-auto uk-grid filter-checkbox-list js-filter-findings">
						<div class="filter-checkbox-list-item" data-finding="file" data-title-finding="Inspection Name here 5">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file"/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> Inspection Name here 5</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="file" data-title-finding="INSPECTION GROUP SD FINDING DESCRIPTION HERE 2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file" checked/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> INSPECTION GROUP SD FINDING DESCRIPTION HERE 2</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here2</label>
				        </div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here1">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here1</label>
				        </div>
				        <div class="filter-checkbox-list-item"  data-finding="file" data-title-finding="Inspection Name here 5">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file"/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> Inspection Name here 5</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="file" data-title-finding="INSPECTION GROUP SD FINDING DESCRIPTION HERE 2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file" checked/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> INSPECTION GROUP SD FINDING DESCRIPTION HERE 2</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here2</label>
				        </div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here1">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here1</label>
				        </div>
				        <div class="filter-checkbox-list-item"  data-finding="file" data-title-finding="Inspection Name here 5">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file"/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> Inspection Name here 5</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="file" data-title-finding="INSPECTION GROUP SD FINDING DESCRIPTION HERE 2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="file" checked/>
							<label for="filter-project-summary-program-" data-finding="file" ><i class="a-folder"></i> INSPECTION GROUP SD FINDING DESCRIPTION HERE 2</label>
						</div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here2">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here2</label>
				        </div>
						<div class="filter-checkbox-list-item"  data-finding="critical" data-title-finding="Inspection Name here1">
				            <input id="filter-project-summary-program-" value="" type="checkbox" data-finding="critical" checked/>
							<label for="filter-project-summary-program-" data-finding="critical" ><i class="a-flames"></i> Inspection Name here1</label>
				        </div>
				            

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
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='file'];"><i class="a-folder"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='nlt'];"><i class="a-booboo"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='lt'];"><i class="a-skull"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-asc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='critical'];"><i class="a-flames"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>

<script>
$('.filter-button-set').find('button.button-filter').click(function() {
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
});
</script>