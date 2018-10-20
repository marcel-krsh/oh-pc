<template class="uk-hidden" id="modal-findings-new-form-template">
	<div class="findings-new-add-comment" data-finding-id="tplFindingId">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea">Custom comment based on what I saw... %%date-in-7-days%%</textarea>
	    	<div class="textarea-status">SAVED</div>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-action" uk-grid>
	    	<button class="uk-width-1-3" onclick="useBoilerplate();"><i class="a-file-text"></i> Use a boilerplate</button>
	    	<button class="uk-width-1-3" onclick="clearTextarea();"><i class="a-file-minus"></i> Clear</button>
	    	<button class="uk-width-1-3" onclick="appendBoilerplate();"><i class="a-file-plus"></i> Append a boilerplate</button>
	    </div>
	    <div class="findings-new-add-comment-quick-entry-list">
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="property-manager-contact-name">PROPERTY MANAGER CONTACT NAME</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="address-of-this-building">ADDRESS OF THIS BUILDING</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="date-in-7-days">DATE IN 7 DAYS</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="tomorrow-date">TOMORROW'S DATE</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="head-of-household-name">HEAD OF HOUSEHOLD NAME</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="another-tag">ANOTHER QUICK ENTRY BUTTON</span>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-save" uk-grid>
	    	<div class="uk-width-1-2">
	    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	</div>
	    	<div class="uk-width-1-2">
	    		<button onclick="saveBoilerplaceAndNewFinding();"><i class="a-file-copy-2"></i> Save and add another of this same finding</button>
	    	</div>
	    </div>
	</div>
</template>