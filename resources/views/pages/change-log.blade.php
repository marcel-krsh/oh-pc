   @extends('layouts.simplerAllita')
@section('head')
<title>Allita PC Change Log</title>


@stop
@section('content')
<!-- <script src="/js/components/upload.js{{ asset_version() }}"></script>
<script src="/js/components/form-select.js{{ asset_version() }}"></script>
<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<style>
	body, div, p {
		font-size: 13pt;
	}
	h1 {
		font-size: 24pt;

	}
	h2 {
		font-size: 20pt;
	}
	h3 {
		font-size: 16pt
	}
	h4,h5 {
		font-size: 14pt;
	}
	.crr-sections {
		width:1142px; min-height: 1502px; margin-left:auto; margin-right:auto; padding: 72px;

	}

	#crr-part {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}
	#crr-sections {
		-webkit-transition: width 1s ease-out;
			-moz-transition: width 1s ease-out;
			-o-transition: width 1s ease-out;
			transition: width 1s ease-out;

	}

#crr-panel .uk-panel-box-white {background-color:#ffffff;}
#crr-panel .uk-panel-box .uk-panel-badge {}
#crr-panel .green {color:#82a53d;}
#crr-panel .blue {color:#005186;}
#crr-panel .uk-panel + .uk-panel-divider {
    margin-top: 50px !important;
}
#crr-panel table tfoot tr td {border: none;}
#crr-panel textarea {width:100%;}
#crr-panel .note-list-item:last-child { border: none;}
#crr-panel .note-list-item { padding: 10px 0; border-bottom: 1px solid #ddd;}
#crr-panel .property-summary {margin-top:0;}
#main-window { padding-top:0px !important; padding-bottom: 0px !important; max-width: 1142px !important; min-width: 1142px !important; }
body{ background-color: white; }
.crr-blocks { page-break-inside: avoid; }

@page {
   margin: .5in;
   size: portrait;
}

ul.leaders li:before, .leaders > div:before {
	content:"";
}
ul.leaders li {
	border-bottom: 1px dotted black;
    padding-bottom: 9px;
    padding-top: 7px;
}
</style>


<div uk-grid >




			<?php $row = 0; ?>
            <div id="main-report-view" class="" style=" min-width: auto; padding:0px; background-color: currentColor;">
            	
            	<div class="uk-shadow uk-card uk-card-default uk-card-body uk-align-center crr-sections" style="">
            		<h1>Allita PC Change Log</h1>
            		<hr class="uk-width-1-1">
            		<p>Please visit this page often to see all changes happening to the Allita PC system from August 12th, 2019 onward.</p>
            		<hr class="uk-width-1-1">
            		
            		
            		<table class="uk-table uk-striped">
            			<thead>
            				<tr>
            				<th style="width: 150px">
            					DATE
            				</th>
            				
            				<th>
            					DETAILS
            				</th>
            				
            				</tr>
            			</thead>
            			<tbody>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 20th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONFIRMED BUG : ALL SITE ITEM INSPECTIONS LISTED FOR ALL AUDITS ON EXPANSION VIEW
		            					</h3>
		            				
		            					<p>When working a project that has more than one audit in Allita PC, the site expansion currently displays the items to be inspected for all audits. The items for different audits do not display their completion circle, nor do they display their copy icon. The delete button will not work on these items.</p>

		            					<p>Workaround: You can ignore these items. They do not show up in the findings modal list of amenities for the location, nor will they affect the audit's report.</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 20th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : FIXED NULL CHECKS ON USERS WITH INCOMPLETE INFORMATION
		            					</h3>
		            				
		            					<p>Fixed bug where you would receive a 500 error when clicking on the contacts tab, or performing an action that would refresh the contacts tab when the selected or added user did not have complete information associated with them.</p>

		            					<p>Credit: Athena Lunsford</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 20th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					BUG FIX : ADD REMOVE AMENITIES WHEN TWO OR MORE AUDITS EXIST
		            					</h3>
		            				
		            					<p>Fixed bug that caused an inspection item to be added to the wrong audit when adding an amenity onto a Site, Building or Unit. The perceived behavior was that the amenity was not added because its inspection did not show up on the selected audit's list of items. This resulted in auditors trying to add the amenity multiple times, thus resulting in multiple copies of the amenity on the Site, Building, or Unit. These would then reveal themselves when a new audit was created or an existing one was rerun. Likewise, deleting an item did not appear to work as it would delete the item, but not the inspection of that item, causing it to re-appear on the list of inspection items if it was closed and opened.</p>

		            					<p>Credit: Kimberly Smith</p>

		            					
		            				
		            				</td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 20th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					BUG FIX : ADDING AMENITY TO BUILDING SEEMED TO MAKE OTHER ITEMS DISAPPEAR
		            					</h3>
		            				
		            					<p>Related to the other bug above, this bug would clear the list of items and then reload them, but because the audit was not correctly passed, the list would not load completely and it would show the inspection item that was actually added to a different audit. This has now been corrected alongside the above fix.</p>

		            					<p>Credit: Jessica George</p>

		            					
		            				
		            				</td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 19th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : UPDATED CONTACTS STATUS ICONS AND HOVERS
		            					</h3>
		            				
		            					<p>Fixed issue where it was not clear what the icons meant, or what their click action would result in.</p>

		            					<p>Credit: Holly Swisher</p>

		            					
		            				
		            				</td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					HOWTO : CREATING AND MANAGING THE CAR/EHS RESOLUTION PROCESS
		            					</h3>
		            				
		            					<p>Creating and managing the CAR while easy, does require some upfront knowledge and know how. This process was outlined based on OHFA's previous methodology and while it has automated some actions, others have to be manually processed to ensure accurate and compliant responses.</p>
		            					

		            					<p>Creating Your CAR or EHS: 
		            						<ol>
		            							<li>Ensure your project's step has been updated by the lead to be "In progress" or higher, but not "Archived".</li>
		            							<li>Open your audit's project details by clicking on the project name on the audit list, or using the quick lookup.</li>
		            							<li>Click on the "Reports" tab for the project.</li>
		            							<li>Click on the "NEW REPORT" button.</li>
		            							<li>Select the report "Type" from the drop list.</li>
		            							<li>Select the audit for the report to use as its data source from the revealed "For Audit" drop list.</li>
		            							<li>Click "GO" to create your report, or "Cancel" to cancel the process.</li>
		            							<li>The window will close, and in a moment (no more than 10 seconds), your report will appear at the top of the report list.</li>
		            						</ol></p>
		            						<hr />
		            					<p>Updating Your CAR or EHS: Adding/Removing Inspected Units: 
		            						<ol>
		            							<li>Scroll to the "Units Audited" list</li>
		            							<li>Click on the text "<span ><i class="a-arrow-diagonal-both use-hand-cursor" uk-tooltip="pos:top-left;title:CLICK TO SWAP UNITS;" title="" aria-expanded="false"></i> SWAP UNITS </span>" in the sub text preceding the summary statement.</li>
		            							<li>Select additional units or deselect units to remove them from the list (units with findings will still show, but they will be grayed out and will not count toward the required number for inspection).</li>
		            							<li>Click the X in the upper right hand corner, or outside the window to close the swap unit modal.</li>
		            							<li>If you have no other changes to make, or require the units to show on the report to continue - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.</li>
		            							<li>Confirm you want to refresh the report's content.</li>
		            						</ol></p>
		            						<hr />
		            					<p>Updating Your CAR or EHS: Editing Finding Level, Type, or Date of Finding: 
		            						<ol>
		            							<li>Click on the "F|N XXXX" link on the finding.</li>
		            							<li>Make your desired changes to the finding.<br /> NOTE: When changing the finding type, the level descriptions will not update until you save and reopen the edit modal.</li>
		            							<li>Click on the "Save Finding" button.</li>
		            							<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.</li>
		            							<li>Confirm you want to refresh the report's content.</li>
		            						</ol></p>
		            						<hr />
		            					<p>Updating Your CAR or EHS: Editing / Hiding Comments: 
		            						<ol>
		            							<li>Click on the edit icon "<i class="a-pencil"></i>" next to the comment.</li>
		            							<li>Make your desired changes to the comment text.</li>
		            							<li>If you want to prevent the comment from showing on the report, check the box next to "Do Not Display On Reports"</li>
		            							<li>Click on the "Save Comment" button.</li>
		            							<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar. <br />NOTE: The report content must be refreshed for comments to show on the report. Comments will show on the printed report unless the hide option has been selected. Findings without at least 1 reportable comment will show the violation description on the printed report.</li>
		            							<li>Confirm you want to refresh the report's content.</li>
		            						</ol></p>
		            						<hr />
		            					<p>Updating Your CAR or EHS: Adding Response (Auditor) 
		            						<ol>
		            							<li>Click on the "<i class="a-circle-plus"></i> ADD RESPONSE" button at the top right of the finding's header.</li>
		            							<li>Choose the desired response type:</li>
		            							<ul>
		            								<li>Comment</li>
		            								<ol>
		            									<li>Enter your text in the "Comment" field.</li>
		            									<li>If you want to prevent the comment from showing on the report, check the box next to "Do Not Display On Reports".</li>
		            									<li>Click the "Save Comment" button to save your comment.<br />NOTE: The report content must be refreshed for comments to show on the report. Comments will show on the printed report unless the hide option has been selected. Findings without at least 1 reportable comment will show the violation description on the printed report.</li>
		            								</ol>
		            								<li>Document</li>
		            								<ol>
		            									
		            									<li>Choose the category for your document from the left column.</li>
		            									<li>Enter a brief description about the document you are uploading.</li>
		            									<li>Either click on the link inside the upload box to browse to select your file, or drag and drop the file onto the upload box.</li>
		            									<li>Repeat 1-3 for each document you want to add to the selected finding(s)</li>
		            									<li>Click the check box next to any other findings you would like to attach the document. <br />NOTE: Hovering over the " <i class="a-info-circle"></i> " icon will give you more information about the finding. Documents do not show on printed reports. </li>
		            									<li>Click on "Save Documents"</li>
		            								</ol>
		            								<li>Communication</li>
		            								<ol>
		            									<li>Click on "Add Recipient"</li>
		            									<ol>
		            										<li>Check the box next to the recipients you want to have notified of the communication.<br />NOTE: You can find recipients names faster by typing their first or last name into the filter box.</li>
		            										<li>Click on "Done Adding Recipients" after selecting your last recipient.</li>
		            									</ol>

		            									<li>Click on "Add Finding" if you would like to attach the communication to multiple findings.
		            										<ol>
		            											<li>Check the box next to the findings you would like to attach the communication to.<br />NOTE: Hovering over the " <i class="a-info-circle"></i> " icon will give you more information about the finding.</li>
		            											<li>Click on "Done Adding Findings" button when finished selecting findings.</li>
		            											</li>
		            										</ol>
		            									<li>If you want to attach a document to the communication - Click "Add Document"</li>
		            										<ol>
				            									<li>Check the box of any existing documents you would like to be attached to the communication</li>
				            									<li>Choose the category for your document from the right column.</li>
				            									<li>Enter a brief description about the document you are uploading.</li>
				            									<li>Either click on the link inside the upload box to browse to select your file, or drag and drop the file onto the upload box.</li>
				            									<li>Repeat 2-4 for each document you want to add to the selected finding(s)</li>
				            									<li>Click on "DONE ADDING DOCUMENTS" when you have finished.</li>
				            								</ol>
				            							<li>Edit the "SUBJECT" line if needed.<br />
				            							WARNING: This will be visible in the email notification that is sent to the recipients. DO NOT STATE ANY CONFIDENTIAL OR PERSONAL INFORMATION IN THIS FIELD.</li>
				            							<li>Edit the "MESSAGE" body content if needed.<br/>NOTE: This information requires the recipient to login to the system to view it.</li>
		            									<li>Click the "SEND" button.<br />NOTE: You do NOT need to refresh the report's content for communications to show on the report since they are not a part of printed report. Communication notifications are sent out based on each recipient's notification preference. It could be up to 24 hours before they receive their notification depending on the time you send the communication. The default notification time for users is every hour. Recipients can immediately see the communication if they login, regardless of when their notification is sent.</li>
		            								</ol>
		            							</ul>
		            							<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.</li>
		            							<li>Confirm you want to refresh the report's content.</li>
		            						</ol></p>
		            						<hr />
		            						<p>Updating Your CAR or EHS: Reviewing, Approving and/or Declining Documents: 
		            						<ol>
		            							<li>Click on the document's name to download a copy of the document.</li>
		            							<ul><li>To Approve/Decline, or Clear the Approval status of the Document:</li>
		            								<ol>
		            									<li>Click on the approval status icon next to the document.</li>
		            									<li>Select the desired approval status to assign to the document.<br />NOTE: You do NOT need to refresh the report's content for the approval status to be shown on the report since documents, and their approval status are not on the printed report.</li>
		            								</ol>
		            							</ul>
		            						</ol></p>
		            						<hr />
		            						<p>Updating Your CAR or EHS: Resolving Findings: 
		            						<ul>
		            							<li>Findings resolved in the findings modal will show the date the resolve button was clicked.</li>
		            							<li>To add or change the date of a finding's resolution on the report:</li>
		            							<ol>
		            								<li>Click on the date field (or the current date recorded).</li>
		            								<li>Click on the month's name to select a different month.</li>
		            								<li>Click on the year to enter a different year.</li>
		            								<li>Click on the day to select the day.</li>
		            							</ol>
		            							<li>To clear a finding's resolution date:</li>
		            								<ol>
			            								<li>Click on the "<i class="a-circle-cross"></i> DATE" button beside the date.
			            								</li>
		            								</ol>
		            							<li>After you have finished all updates:</li>
		            							<ol>
		            								<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.<br />NOTE: The resolved date is saved immediately to the database, but the report will need to have its content refreshed for the date to appear on the report permanently. We recommend refreshing the report's content after you have finished updating all the findings on that report for your current login session. If you forgot to refresh the report's content in a session, you do not need to re-enter the dates. Just reopen the report and click on the "Refresh Report Content" link or icon. Refreshing the content will also update the dates shown for the latest resolution on the BINs.</li>
		            								<li>Confirm you want to refresh the report's content.</li>
		            							</ol>
		            						</ul></p>
		            						<hr />
		            						<p>Updating Your CAR or EHS: Cancelling a Finding: 
		            						<ol>
		            							<li>Click on the "CANCEL" button on the finding.</li>
		            							<li>Confirm you wish to cancel the finding.</li>
		            							<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.</li>
		            							<li>Confirm you want to refresh the report's content.<br />WARNING: Failing to refresh the content of the report will allow the finding to remain visible to all users with report access. If you have cancelled a finding prior but forgot to refresh the content, DO NOT click to cancel the finding again, but click to refresh the content of the report first. If after refreshing the content the finding still shows, then try repeating the cancel process again.</li>
		            							
		            						</ol></p>
		            						<hr />
		            						<p>Updating Your CAR or EHS: Adding a Finding: 
		            						<ol>
		            							<li>Click on the site or file icon from either the Buildings Audited or Units Audited list.</li>
		            							<li>In the findings modal select the date, location, amenity, and violation.</li>
		            							<li>Enter your comment and select the finding's level.</li>
		            							<li>Repeat 2-3 until you are done adding findings.</li>
		            							<li>Click on "Done Adding Findings"</li>
		            							<li>If you have no other changes to make - click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.</li>
		            							<li>Confirm you want to refresh the report's content.</li>
		            							
		            						</ol></p>
		            						<hr />

		            						<p>Sending Your CAR For Approval: 
		            						<ol>
		            							<li>If you haven't already- return the project's "Reports" tab.</li>
		            							<li>From the "ACTION" drop down list, select "SEND TO MANAGER REVIEW".</li>
		            							<li>Click on "ADD RECIPIENT"</li>
		            							<li>Check the box next to the managers and or admins you would like to notify.<br />NOTE: Only managers and admins can approve CARs.</li>
		            							<li>Click on "Done Adding Recipients"</li>
		            							<li>You can make changes to the "SUBJECT" and "MESSAGE" if you like.</li>
		            							<li>Click the "SEND" button to send the notification.<br />NOTE: You do NOT need to refresh the report's content for communications to show on the report since they are not a part of printed report. Communication notifications are sent out based on each recipient's notification preference. It could be up to 24 hours before they receive their notification depending on the time you send the communication. The default notification time for users is every hour. Recipients can immediately see the communication if they login, regardless of when their notification is sent.</li>
		            							
		            						</ol></p>
		            						<hr />
		            						<p>Signing the EHS: 
		            						<ol>
		            							<li>Scroll to the bottom of your EHS.</li>
		            							<li>Have the property representative use their finger or a stylus to sign the report inside the signature box.</li>
		            							<li>Select the name of the signator from the "Who is Signing" drop down list.<br />NOTE: If the person signing is not listed, you can either add them to the list of contacts on the project's contacts tab (they must already be a DEVCO user) or you can select "Other" and then type in the name of the person signing.</li>
		            							<li>Click the "Confirm Signature" button to save the signature.<br />NOTE:Confirming the signature on the EHS will make the report available for viewing by the project's contacts with report access.</li>
		            							
		            						</ol></p>
		            						<hr />
		            						<p>Clearing an EHS Signature: 
		            						<ol>
		            							<li>Click on the "Refresh Report Content" link or on the refresh report icon "<i class="a-rotate-left-3" style="font-weight: bolder;"></i>" in the sidebar.<br />NOTE: Do not use the EHS report to manage resolutions or finding changes. This will clear the signature on the report. Justifiably, the signature represents acknowlegement of the report's content at that specific time. Changing the report's content would change the content the person signed acknowelegment of, hence why the signature is removed any time the report content is refreshed.</li>
		            							
		            							
		            						</ol></p>
		            						<hr />
		            						<p>Sending Your CAR or EHS To Property Managers and Owners: 
		            						<ol>
		            							<li>CAR Only: Recieve approval from your manager or admin on the CAR.</li>
		            							<li>From the "ACTION" drop down list, select "SEND TO PROPERTY CONTACT".</li>
		            							<li>Click on "ADD RECIPIENT"</li>
		            							<li>Check the box next to the contacts you would like to notify.<br />NOTE: If you do not see a contact in the list, go to the project's contacts tab and add them as a project user and then try again.</li>
		            							<li>Click on "Done Adding Recipients"</li>
		            							<li>You can make changes to the "SUBJECT" and "MESSAGE" if you like.</li>
		            							<li>Click the "SEND" button to send the notification.<br />NOTE: You do NOT need to refresh the report's content for communications to show on the report since they are not a part of printed report. Communication notifications are sent out based on each recipient's notification preference. It could be up to 24 hours before they receive their notification depending on the time you send the communication. The default notification time for users is every hour. Recipients can immediately see the communication if they login, regardless of when their notification is sent.</li>
		            							
		            						</ol></p>
		            						<hr />

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
	            			
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					EXPANSION : SITE AMENITY BUG
		            					</h3>
		            				
		            					<p>There is a confirmed bug with the site amenity items listed when expanding from the audit. Clicking on the icons to open the finding modal from any on this list causes a 500 Error. A fix is coming with the upcoming removal of the individual ordering of items in the expansion view.</p>

		            					<p>Workaround: Click on the finding icon on the audit list to open the finding modal instead, click on the location option, and select the site from the list.</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>

	            			<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					DETAILS TAB : REFRESH DATA SNAPSHOT
		            					</h3>
		            				
		            					<p>The details tab shows a snap shot of the project's information at the time the audit was generated. Sometimes this information is out of date and needs to be refreshed. If this scenario arises - you can refresh the content by clicking on the refresh icon in the upper right of the details tab. </p>

		            					<p>It is important to note, this only refreshes the project details of the selected audit, not all audits. The details on this view are what will be used in the audit reports.</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					DETAILS TAB : COMPLIANCE : UPDATE REQUIRED AMOUNTS
		            					</h3>
		            				
		            					<p>When viewing the compliance tab, you can now double click on the required amount on each program's break-out section to change that required number.</p>

		            					<p>It is important to note, this only works while on your desktop, and any changes you make are recorded into the selection method history.</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>

            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : ADD USER TO PROJECT
		            					</h3>
		            				
		            					<p>The contacts tab now has a button that will allow you to add a user to the project specifically inside Allita to ensure they can access this project. </p>

		            					<p>It is important to note, to add a user, they must have a devco user that has a property manager role permission. Also, these additions do not get pushed back to DEVCO.</p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : MODIFY USER DETAILS
		            					</h3>
		            				
		            					<p>You can now edit and/or add contact details for a user. Simply click the pencil icon next to the information to edit it, or click the plus icon to add a new option.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Information directly tied to DEVCO cannot be modified - instead add a new option.</li>
		            							<li>Information will be available for use on all future audits.</li>
		            							<li>Information does not get pushed back to DEVCO</li>
		            						</ul></p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : CHANGE DEFAULT CONTACT DETAILS
		            					</h3>
		            				
		            					<p>As noted above you can modify contact details, but you can also change the default contacts/information to be used in the audit's Project Details snapshot. Clicking on the corresponding radio button beside contact information for name, organization, address, phone number, and email will then mark that as the default to be used by the snapshot.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Project Details need to be refreshed on the project details tab to reflect changes.</li>
		            							<li>Options for each detail can be selected from any contact listed.</li>
		            							<li>If a contact is removed that contained a default selection, the selection will move to the default DEVCO user's detail item.</li>
		            							<li>Selections will be used on all future audits.</li>
		            							<li>Selections do not get pushed back to DEVCO.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					CONTACTS TAB : REPORT ACCESS OVERRIDE
		            					</h3>
		            				
		            					<p>The far right icons on the contact tab give you access status. The first icon shows if the user has been given access to the project in DEVCO. Hovering over the icon will display which contact role they have been given. The second icon shows if the user has been given access to the project inside Allita. This access will override contact role restrictions from DEVCO in regards to report access. The last icon will show grayed out if the user's access from DEVCO does not allow report access. If this icon is not grayed out, this user can access the reports for this project and will be listed in the contact list on report communications.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Users who lose override access or later receive a restricted DEVCO contact role without an Allita override will lose access to reports, even if they were a contact on them prior.</li>
		            							<li>Selections will be used on all reports.</li>
		            							<li>Additions and subtractions of access do not get pushed back to DEVCO.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					EXPANSIONS : SPEED INCREASE
		            					</h3>
		            				
		            					<p>This release includes some under-the-hood optimizations that make expansions load in 30% of the time prior. Another optimization is in the works to further increase the speed and reliability of expansions loading.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Users who lose override access or later receive a restricted DEVCO contact role without an Allita override will lose access to reports, even if they were a contact on them prior.</li>
		            							<li>Selections will be used on all reports.</li>
		            							<li>Additions and subtractions of access do not get pushed back to DEVCO.</li>
		            						</ul></p>

		            					

		            					
		            				
		            				</td>
		            				
            				</tr>

            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					AUDIT : REPORTS TAB
		            					</h3>
		            				
		            					<p>A much simpler access point has been made to see reports for the project you are working on. Click on the name of your project from the audit list, or search for your project using the quick lookup. In the corresponding tab that opens, you can click on that project's reports tab to see only the reports for that project.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Filters applied on this tab do not affect other reports tabs.</li>
		            							<li>New reports from this tab will only allow a new report to be created for audits on that project.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>

            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : ACTION &amp; OPTION ICONS ADDED TO SIDE BAR
		            					</h3>
		            				
		            					<p>Now you can click on the icons in the left hand side bar to print, refresh, or change your finding columnn view option. The icons are repeated at the top and bottom of the report's thumbnails.</p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : FINDING LIST - NOW WITH 1 COLUMN VIEW OPTION
		            					</h3>
		            				
		            					<p>You can now toggle the finding list colum view between the 3-columnn grid view (default) and the 1-columnn view by clicking on the view option in the left hand side bar.</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>View selection will be remembered across all reports until your login session expires.</li>
		            							<li>This has no effect on the print view of the report.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : VIOLATION CODE REFERENCE
		            					</h3>
		            				
		            					<p>We have created an official Ohio UPCS Violation Code reference page that is publicly viewable at <a href="{{url('/codes')}}" target="codes">{{url('/codes')}}</p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Codes are broken down to four elements: STATE.TYPE.ID LEVEL ie: OH.NLT.266 LEVEL 1.</li>
		            							<li>Clicking on a violation code reference in a report will filter the codes reference page to just that code.</li>
		            							<li>Working view of the report still outputs the entire description of the violation.</li>
		            							<li>Print view of the report will only output the violation description if no comments were made on the finding.</li>
		            							<li>All codes are output on the code reference page, even old codes. Old codes that are deactivated will include a date they were deprecated.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : RESOLUTION DATE
		            					</h3>
		            				
		            					<p>You can now select the date the finding was resolved. If there is no date entered, or you see a date, click on the box and select the new date.</p>

		            					<p>VERY IMPORTANT NOTE: 
		            						<ul>
		            							<li>YOU MUST REFRESH THE REPORT CONTENT TO REFLECT DATE CHANGES.</li>
		            						</ul></p>

		            					<p>
		            					<p>Other Notes: 
		            						<ul>
		            							<li>Date changes are saved immediately as you make them, so you can update multiple before refreshing your report's content.</li>
		            							<li>You can clear a date input by clicking the <div class="uk-button"><i class="a-circle-cross"></i> DATE</div> button beside the date.</li>
		            							<li>Property managers are NOT notified of resolutions. We recommend sending a communication attached to resolved findings to notify them of resolved findings.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : LAST RESOLUTION ROLL UP DATE BY BIN
		            					</h3>
		            				
		            					<p>When all findings within a BIN have been resolved (including that building's units), the BIN listed will show the date of the most recent resolution date. Otherwise it will show "<i class="a-fail"></i> UNCORRECTED" in flashing red text. </p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>When adding in your resolution dates to findings, this info will not update on the report until you refresh the report's content.</li>
		            							<li>This is available on all views of the report.</li>
		            							<li>Clicking on the BIN from the inspection list on the working view will filter down to that BIN's findings, including the findings for units within that BIN.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>

            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : TOTAL PHYSICAL AND FILE FINDINGS BY BIN
		            					</h3>
		            				
		            					<p>The list of inspected BINs now shows a total count of findings for physical and file findings against it and any of its units. </p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>When adding or cancelling findings, this info will not update on the report until you refresh the report's content.</li>
		            							<li>This is available on all views of the report.</li>
		            							<li>Clicking on the BIN from the inspection list on the working view will filter down to that BIN's findings, including the findings for units within that BIN.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>

            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : FINDING HEADERS
		            					</h3>
		            				
		            					<p>The list of findings is now organized in this method:
		            							<ul>
		            								<li>Site Findings</li>
		            								<li>Building Findings (sorted by building name alphabetically)</li>
		            								<ul><li>That Building's Unit Findings (sorted by unit name alphabetically)</li></ul>
		            								<li>Unit Findings (where their building has no findings against it; sorted by unit name alphabetically)</li>
		            							</ul>
		            							Each finding group is broken up by a header outlining the group's type of findings, and the address for the findings.
		            					 </p>

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Addresses are only mentioned in the headers.</li>
		            							<li>When printing the report, the actual print output will not print the dark background of the header, but will rather print a horizontal rule. This is to save ink.</li>
		            							<li>When adding or cancelling findings, this info will not update on the report until you refresh the report's content.</li>
		            							<li>This is available on all views of the report.</li>
		            							<li>Clicking on the BIN from the inspection list on the working view will filter down to that BIN's findings, including the findings for units within that BIN.</li>
		            						</ul></p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : MORE OPTIMIZED PRINT VIEW
		            					</h3>
		            				
		            					<p>As mentioned in other areas - the print view has had several optimizations made to make it more succinct and easier to read when printed.
		            						<ul>
	            								<li>List of programs are not output for property managers.</li>
	            								<li>Violation descriptions are only output is there are no comments on the finding.</li>
	            								<li>Use of headers for each finding group (SITE, BUILDING/BUILDING UNIT, UNIT) easier to browse.</li>
	            								<li>Headers use smaller overall type size, and address is only output once in the header for each group of findings.</li>
	            								<li>Use of one columnn for findings makes pagination easier to follow.</li>
	            								<li>Margins are automatically set for most browsers allow for page information and page X/X to be displayed.</li>
	            								<li>Overall page count required for printing has been reduced by 70%</li>
	            							</ul>
		            					</p>

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS/COMMUNICATIONS : ALL COMMUNICATIONS DISPLAY ON PROJECT'S COMMUNICATIONS TAB
		            					</h3>
		            				
		            					<p>Now communications sent by both the Auditing team and the Property Management and Owner will display on the project's communications tab. Additionally a bug preventing auditors from seeing the contacts for the project as recipients on the add response -> communication has been fixed.</p>
		            					

		            					

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
            				<tr class="rows" <?php $row++; ?> >
		            				<td  >
		            					<small>August 12th, 2019</small>
		            				</td>
		            				<td>
		            					<h3>
			            					REPORTS : PHOTOS
		            					</h3>
		            				
		            					<p>Photos added either during the audit or after will now show on the report in both the working and printed views.</p>
		            					

		            					<p>Some important notes: 
		            						<ul>
		            							<li>Project Details need to be refreshed on the project details tab to reflect changes.</li>
		            							<li>Photos do not get pushed back to Docuware individually - they will be included in the final report export during the archive process. (TBD)</li>
		            						</ul></p>

		            					<!-- <p><a class="uk-button uk-success"  href="#modal-media-youtube{{$row}}" uk-toggle><i uk-icon="icon:play-circle " class="uk-margin-small-right"></i> WATCH</a></p> -->

		            						    <!-- <div id="modal-media-youtube{{$row}}" class="uk-flex-top" uk-modal>
		            						    	<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical uk-margin-top uk-margin-large-bottom">
												        <button class="uk-modal-close" type="button" uk-close></button>
												       <p align="center"> 
												       	<iframe width="730" height="455" src="https://www.youtube.com/embed/mYhEgUnqKsM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen  uk-video style="margin-left: auto; margin-right: auto;" align="center"></iframe>
												       </p>
												    </div>
												</div> -->
		            				
		            				<br/></td>
		            				
            				</tr>
	            				
	            			
	            			<!-- <tr class="rows">
		            				<td  >
		            					August 12, 2019
		            				</td>
		            				<td>
		            					<strong>
			            					DETAILS TAB : REFRESH
		            					</strong>
		            				</td>
		            				<td>
		            					<p>Now auditors and above can refresh the details associated with the </p>
		            				
		            				</td>
		            				
            				</tr> -->
	            	 	</tbody>
            		</table>
            	</div>
            	
            </div>




</div>


@stop