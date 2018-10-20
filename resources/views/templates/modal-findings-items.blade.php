<template class="uk-hidden" id="inspec-tools-tab-finding-item-template">
	<div class="inspec-tools-tab-finding-item tplStatus inspec-tools-tab-finding-info uk-width-1-1 uk-margin-remove" data-finding-id="tplFindingId" data-parent-id="tplParentItemId">
		<div class="uk-grid-match" uk-grid>
			<div class="uk-width-1-4 uk-padding-remove-left uk-first-column">
				<div class="uk-display-block">
    				<i class="tplIcon"></i><br>
    				<small class="auditinfo">AUDIT tplAuditId</small><br />
    				<small class="findinginfo">FND#: tplFindingId</small>
    			</div>
    			<div class="uk-display-block" style="margin: 15px 0;">
    				
				</div>
				<div class="inspec-tools-tab-finding-stats" style="margin: 0 0 15px 0;">
					tplStats
				</div>
			</div>
			<div class="uk-width-3-4 uk-padding-remove-right ">
				<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
    				<p>tplDate: tplType#tplRef<br />
    					By tplName</p>
    				<p>tplContent</p>
    				<div class="inspec-tools-tab-finding-actions">
					    <button class="uk-button uk-link"><i class="a-comment-plus"></i> REPLY</button>
    				</div>
    				<div class="inspec-tools-tab-finding-top-actions">
    					<i class="a-circle-plus use-hand-cursor"></i>
					    <div uk-drop="mode: click" style="min-width: 315px;">
					        <div class="uk-card uk-card-body uk-card-default uk-card-small">
					    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
					    	 		tplTopActions
					    	 		<div class="icon-circle use-hand-cursor" onclick="addChildItem(123, 'followup')"><i class="a-bell-plus"></i></div>
					    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'comment')"><i class="a-comment-plus"></i></div>
					    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'document')"><i class="a-file-plus"></i></div>
					    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem(123, 'photo')"><i class="a-picture"></i></div>
					    	 	</div>
					        </div>
					    </div>
    				</div>
    			</div>
    		</div>
    	</div>
	</div>
</template>

<template class="uk-hidden" id="inspec-tools-tab-finding-items-template">
	<div class="inspec-tools-tab-finding-items uk-width-1-1 uk-first-column uk-margin-remove" style="display:none">
		<div class="inspec-tools-tab-finding-items-list" uk-grid>
	    	
	    </div>
	</div>
</template>