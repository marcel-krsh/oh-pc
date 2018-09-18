<div id="how-to-modal" class="uk-modal-full" uk-modal>
	<div id="how-to-modal-size" class="uk-modal-dialog"> <a class="uk-modal-close-full" uk-close></a>
			<div id="how-to-modal-content">
			
			</div>
				
		</div>
	</div>
<script>
function showReimbursementHowTo() {
			$('#how-to-modal-content').load('/modals/reimbursement_how_to');	
			console.log('/modals/parcels_how_to into #dynamic-modal-content');
			// remove the class in case it is still there.
			$('#how-to-modal-size').addClass('uk-modal-dialog-blank');
			//$('#how-to-modal-content').addClass('uk-height-viewport');
			// UIkit.offcanvas.hide();
			$('#list-tab').trigger('click');
			UIkit.modal('#how-to-modal', {center: true}).show();
		}
@if(isset($showHowTo))
	
		@if($showHowTo == 1)

		showReimbursementHowTo();
		@endIf
		@if($showHowTo == 2)
			{{--UIkit.modal.alert('I\'ve taken you to your requested action page.');--}}
		@endIf
		@if($showHowTo == 3)
			UIkit.modal.alert('<h2>So you\'re ready to get reimbursed?</h2><p>That\'s great!</p><p>I\'ve loaded the "Ready for Submission" queue up for you. If everything looks good - just click the <a class="uk-button uk-button-default uk-button-small">REQUEST REIMBURSEMENT</a> button on the bottom of your screen.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 4)
			UIkit.modal.alert('<h2>Time to review your team\'s hard work.</h2><p>Please look over the parcel list carefully. I\'ve tried to detect any issues with parcels and highlight them for you. If everything looks good, just click the <a class="uk-button uk-button-default uk-button-small">APPROVE FOR SUBMISSION</a> button on the bottom of your screen.</p><p>If you see a parcel you don\'t think is ready yet, click on the <span class="a-circle-cross"></span> icon on to move it to the "Internal - Corrections Needed" queue for your team to review and remedy.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 5)
			UIkit.modal.alert('<h2>Looks like the HFA needs some things corrected.</h2><p>Please see the correction requests within each parcel\'s detail view. When you\'re satisfied with your changes, you can click the button <a class="uk-button uk-button-default uk-button-small">REQUEST RESUBMISSION APPROVAL</a> to have your changes approved by your internal approver or the <a class="uk-button uk-button-default uk-button-small">APPROVE AND RESUBMIT</a> if you are an approver.</p><p>If the corrections for a parcel is holding up your reimbursement request for all the other parcels, you can click on the <span uk-icon="times-circle"></span> icon on to move it to the "Internal - Corrections Needed" queue and remove it reimbursement request.</p><p>After you\'ve completed the requested corrections, you can submit that parcel for reimbursement in your next request.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 6)
			UIkit.modal.alert('<h2>Looks like the your approver needs some things corrected.</h2><p>Please see the correction requests within each parcel\'s detail view. When you\'re satisfied with your changes, you can click the button <a class="uk-button uk-button-default uk-button-small">REQUEST SUBMISSION APPROVAL</a> to move your parcel back into the "Ready for Submission" queue.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 7)
			UIkit.modal.alert('<h2>Ready to get paid?</h2><p>Below is a list of all your parcels that have been approved for reimbursement by the HFA. If you\'re happy with the approved amounts, click on the PO# on a parcel to view the complete Purchase Order details and click the "Invoice this PO".</p><p>If you have a question about the approved amount for the parcel, you can view the approval details by clicking on the parcel\'s id and review the approved amounts against the requested amounts as well as any notes the HFA left. If you still have questions, you can click on the <i class="uk-envelope-o"></i> icon on this list page, or click on the communications tab within the parcels detail view to send a message to the HFA.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf

		
		@if($showHowTo == 8)
			UIkit.modal.alert('<h2>These parcels are a part of a declined request for reimbursement.</h2><p>I strongly recommend you review the communications on the reimbursement request to understand why the entire request was declined.</p><p>If you have questions about a specific parcel, you can click on the <i class="uk-envelope-o"></i> icon for that parcel on this list page, or click on the communications tab within the parcels detail view to send a message to the HFA.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf

		@if($showHowTo == 9)
			UIkit.modal.alert('<h2>Let\'s get paid!</h2><p>I have listed all of your Purchase Orders.</p><p>I have included the Requested amount next to the Approved amount to help in your review.</p><hr class="uk-margin-top-remove uk-margin-bottom-remove"/><h2>Everything look good?</h2> Great! Just click on the <span uk-icon="file-text-o"></span> icon to open the PO and click to create an invoice for that purchase order.</p><hr class="uk-margin-top-remove uk-margin-bottom-remove"/><h2>Got questions?</h2><p>If you have questions about the approved amount, you can click on any colum to see a break down of approved amounts per parcel (for a full detailed breakdown, click on the parcel\'s id to open that parcel\'s detail view.).</p><p>If you have questions on the approved amount, you can click on the communications tab within the parcel\'s detail view to send a message to your HFA.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf

		@if($showHowTo == 10)
			UIkit.modal.alert('<h2>This is a list of any requests that have been declined for reimbursement.</h2><p>I strongly recommend you review the communications on the reimbursement request to understand why the entire request was declined, as this is a <strong>very rare</strong> situation.</p><p>If you have questions about a specific parcel, you can click on the <span uk-icon="list-ul"></span> icon in the "# PARCELS" column to view the parcel list for that request. There you can click on the <span uk-icon="envelop-o"></span> icon or go to the communications tab within the parcels detail view to send a message to the HFA.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 11)
			UIkit.modal.alert('<h2>Let\'s upload some parcels!</h2><p>To make things easier I have made a custom template for you to use. Any information I know about you, I will automatically put in for you, I only need the parcels location information, its target area, how many units, and how you acquired it.</p><p>To make things even easier, the template gives you a drop list of available options that I will recognize for parcel type, how acquired, and target areas.</p><p>Once you\'ve finished loading up the template with your parcel information, click on the button "SELECT YOUR CSV OR XLS PARCEL FILLED FILE" to select and upload your file from your computer.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 12)
			UIkit.modal.alert('<h2>Let\'s check your parcels</h2><p>Below is a list of parcels that have not had validation run against them yet. This includes those you just added and any others that were not run previously.</p><p>If there are parcels you don\'t want to run validation against right now just click on the <span class="a-circle-cross"></span> icon to exclude them from this validation run.</p><p>Once you\'re ready, click on the <a calss="uk-button uk-button-small">RUN VALIDATION</a> button and I\'ll check them against your HFA\'s information.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 13)
			UIkit.modal.alert('<h2>Compliant documentation made easier</h2><p>As you know, SIGTARP is very particular about everything being documented a very specific and detailed way. To help you be compliant with their rules, I will categorize and store your documents according to their standards for you.</p><p>Below are all the parcels that have been marked as requiring documentation. Click on one and then just drag and drop your documentation onto their respective drop folders (or click on a folder to browse your computer and upload it that way).</p><p>Once you are done uploading all of that parcel\'s documents, click the <a class="uk-button uk-button-small"><span uk-icon="check-circle-o"></span> DOCUMENTS COMPLETE</a> button, or if you need to come back to it, just click on the next parcel to start working on that one.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 14)
			UIkit.modal.alert('<h2>Let\'s put in our expenses!</h2><p>To make things easier I have made a custom template for you to use. Any information I know about you, I will automatically put in for you, I only need the parcel id, the vendor, the category of the expense, the amount it cost you, and then the amount you want reimbursed. You can include a brief description and/or a longer note describing the expense or clarifying it further and then select which invoice you uploaded belongs to that expense.</p><p>To make things even easier, the template gives you a drop list of available options that I will recognize for everything except the description and note.</p><p>I have provided two templates for you. One that has all your parcels in it that have not been submitted for reimbursement yet, and one that only includes those that have zero expenses recorded.</p><p><strong>Before filling out the template</strong> - please ensure all of your vendors have been entered/selected for your entity, and that all your invoices have been uploaded in the Documents section.</p><p>Once you\'ve finished loading up the template with your expense information, click on the button <a class="uk-button uk-button-small><span uk-icon="file-excel-o"></span> SELECT YOUR CSV OR XLS EXPENSE FILLED FILE</a> to select and upload your file from your computer.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 15)
			UIkit.modal.alert('<h2>Slow and steady wins the race.</h2><p>While this is slower than a bulk import, it is less error prone. So, kudos!</p><p>I have pulled up all the parcels that have been marked for to have expenses entered.</p><p>Click on a parcel and then scroll to the bottom of the detail page to enter your expenses. Clicking on the on the parcels tab title will refresh the information on the parcel\'s expense charts to help you stay aware of your budgeted allowances.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf
		@if($showHowTo == 17)
			UIkit.modal.alert('<h2>Consider your approver notified</h2><p>I sent an email to {{$sentEmailTo}} with a link to review the parcels.</p><p>As always, you can see your "8 Steps to Reimbursement" by clicking on the menu button in the upper left.<br /> <br />(By the way - the menu icon looks like this: &nbsp;<span class="a-menu"></span> &nbsp;)</p>');
		@endIf

	@endIf
	</script>