<style type="text/css">
	.CMT-edit {
		display: inline-block;
	}
	.FLWUP-edit, .PIC-edit, .DOC-edit {
		display: none;
	}
</style>
<template class="uk-hidden" id="inspec-tools-tab-finding-item-template">
	<div id="inspec-tools-tab-finding-item-tplItemId" class="inspec-tools-tab-finding-item tplIsReply tplStatus inspec-tools-tab-finding-info uk-width-1-1 uk-margin-remove" data-finding-id="tplFindingId" data-parent-id="tplItemId">
		<div id="inspec-tools-tab-finding-reply-sticky-tplItemId" class="inspec-tools-tab-finding-reply-sticky tplStatus uk-width-1-1 uk-padding-remove" style="display:none">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
					<div>
						<i class="uk-inline tplIcon"></i> <i class="uk-inline a-menu" onclick="expandFindingItems(this);"></i>
					</div>
				</div>
				<div class="uk-width-3-4 uk-padding-remove-top uk-padding-remove-right">
					<div>
						tplDate: tplType#tplRef
						<div class="uk-float-right"><i class="a-circle-plus use-hand-cursor"></i></div>
					</div>
					<div>
						tplStickyContent
					</div>
				</div>
			</div>
		</div>
		<div class="" uk-grid>
			<div class="uk-width-1-4 uk-padding-remove-left uk-first-column">
				<div class="uk-display-block">
    				<i class="tplIcon"></i><br>
    				<small class="auditinfo">AUDIT tplAuditId</small><br />
    				<small class="findinginfo">FND#: tplFindingId</small>
    			</div>
				<div class="inspec-tools-tab-finding-stats">
					tplStats
				</div>
			</div>
			<div class="uk-width-3-4 ">
				<div class="inspec-tools-tab-finding-top-actions">
    					<i class="a-circle-plus use-hand-cursor"></i>
					    <div uk-drop="mode: click" style="min-width: 315px; background-color: #fff; z-index: 1000; ">
					        <div class="uk-card uk-card-body uk-card-default uk-card-small">
					    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
					    	 		tplTopActions
					    	 	</div>
					        </div>
					    </div>
    				</div>
				<div class="uk-width-1-1 uk-display-inline-block uk-padding-remove inspec-tools-tab-finding-description">
    				<p><small>tplDate: tplType#tplRef</small> <i class="tplType-edit a-pencil use-hand-cursor" onclick="addChildItem(tplItemId, 'comment-edit', 'comment')"></i><br />
    					By tplName</p>
    				<p>tplContent</p>

    			</div>
    		</div>
    	</div>
	</div>
</template>

<template id="photo-gallery-item-template">
	<li class="findings-item-photo-tplPhotoId use-hand-cursor" onclick="openFindingPhoto(tplFindingId,tplItemId,tplPhotoId);">
        <img src="tplUrl" alt="">
        tplComments
    </li>
</template>

<template id="photo-gallery-template">
	<div class="photo-gallery" uk-slider>
	    <div class="uk-position-relative uk-visible-toggle uk-light">
	        <ul class="uk-slider-items uk-child-width-1-1">
	            tplPhotos
	        </ul>
	    </div>
	    <ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul>
	</div>
</template>

<template id="file-template">
	<div class="finding-file-container">
	    tplFileContent
	</div>
</template>

<template class="uk-hidden" id="inspec-tools-tab-finding-items-template">
	<div class="inspec-tools-tab-finding-items uk-width-1-1 uk-margin-remove" style="    position: sticky; display:none">
		<div class="inspec-tools-tab-finding-items-list" uk-grid>

	    </div>
	</div>
</template>

<template class="uk-hidden" id="inspec-tools-tab-finding-item-replies-template">
	<div class="inspec-tools-tab-finding-item-replies uk-width-1-1 uk-margin-remove" style="display:none">
		<div class="inspec-tools-tab-finding-item-replies-list" uk-grid>

	    </div>
	</div>
</template>