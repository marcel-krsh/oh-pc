<template class="uk-hidden" id="inspection-left-template">
    <div class="inspection-menu">
    </div>
</template>

<template class="uk-hidden" id="inspection-menu-item-template">
    <button class="uk-button uk-link menuStatus" onclick="" style="menuStyle"><i class="menuIcon"></i> menuName</button>
</template>

<template class="uk-hidden" id="inspection-areas-template">
    <div class="inspection-areas">
    </div>
</template>

<template class="uk-hidden" id="inspection-area-template">
	    <div class="inspection-area uk-flex uk-flex-row areaStatus">
	    	<div class="uk-inline uk-padding-remove uk-margin-top">
    			<div class="area-avatar">
					<div uk-tooltip="pos:top-left;title:areaAuditorName;" title="" aria-expanded="false" class="user-badge auditor-badge-areaAuditorColor no-float">
						areaAuditorInitials
					</div>
				</div>
			</div>
    		<div class="uk-inline uk-padding-remove uk-margin-top" style="flex:140px;">
    			<div class="area-name">
    				<i class="a-circle-checked"></i>
					areaName
				</div>
			</div>
    		<div class="uk-inline">
    			<div class="findings-icon uk-inline areaNLTStatus">
					<i class="a-booboo"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaLTStatus">
					<i class="a-skull"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaSDStatus">
					<i class="a-flames"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline">
				<div class="findings-icon uk-inline areaPicStatus">
					<i class="a-picture"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaCommentStatus">
					<i class="a-comment-text"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline">	
				<div class="findings-icon uk-inline areaCopyStatus">
					<i class="a-file-copy-2"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaTrashStatus">
					<i class="a-trash-4"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div> 
		
	    </div>
</template>

<template class="uk-hidden" id="inspection-tools-template">
    <div class="inspection-tools">
    	<p>Tools and tabs here<br />+ AREA | EDIT</p>
    	<ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
		    <li><a href="#">FINDINGS</a></li>
		    <li><a href="#">COMMENTS</a></li>
		    <li><a href="#">PHOTOS</a></li>
		    <li><a href="#">DOCUMENTS</a></li>
		    <li><a href="#">FOLLOW UPS</a></li>
		</ul>

		<ul class="uk-switcher uk-margin">
		    <li>Hello!</li>
		    <li>Hello again!</li>
		    <li>Bazinga!</li>
		    <li>Hello again 2!</li>
		    <li>Bazinga 3!</li>
		</ul>
    </div>
</template>

		<div class="building-details uk-overflow-auto" style="">
			<div class="sortable" uk-sortable="handle: .uk-sortable-details">
				@foreach($details as $key=>$detail)
				<div id="building-detail-r-{{$key}}" class="building uk-flex uk-flex-row building-detail @if($detail['status']) building-{{$detail['status']}} {{$detail['status']}} @endif " >
					<div id="building-detail-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-details" style="    min-width: 16px; padding: 0 3px;">
						<div class="linespattern"></div>
						<span id="building-detail-rid-{{$key}}" class="uk-position-bottom-center colored"><small>#<span class="rowindex">{{$loop->iteration}}</span></small></span>
					</div>
					<div id="building-detail-{{$target}}-c-2-{{$key}}" class="building-type">
						<div class="uk-padding-remove building-type-top" uk-grid>
							<div class="uk-width-2-3 uk-padding-remove">
								<div uk-grid>
									<div class="uk-width-1-1 uk-padding-remove">
										<div uk-grid>
											<div class="building-auditors uk-width-1-2">
												<div uk-slideshow="animation: slide; min-height:90;">

												    <div class="uk-position-relative uk-visible-toggle">

												        <ul class="uk-slideshow-items">
												            <li>
												            	<div uk-grid>
												                @foreach($detail['auditors'] as $auditor)
																<div class="building-auditor uk-width-1-2 uk-margin-remove">
																	<div id="building-detail-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
																		{{$auditor['initials']}}
																	</div>
																	<div class="auditor-status"><i class="a-circle-checked"></i></div>
																</div>
															@if($loop->iteration % 6 == 0 && $loop->iteration < count($detail['auditors']) )
												            	</div>
												            </li>
												            <li>
												            	<div uk-grid>
												            @endif
																@endforeach
																</div>
												            </li>
												        </ul>

												    </div>

												    <ul class="uk-slideshow-nav uk-dotnav uk-flex-center"></ul>

												</div>

											</div>
											<div class="uk-width-1-2">
												<div class="building-type-icon ">
													@if($detail['type'] == "pool")
													<i class="a-pool colored"></i>
													@else
													<i class="a-buildings colored"></i>
													@endif
												</div>
												<div class="building-status">
													<i class="a-check colored"></i>
												</div>
											</div>
										</div>
									</div>
									<div class="uk-width-1-1 uk-margin-remove building-type-bottom">
					            		<div id="inspection-menus-{{$key}}" style="display:none;"></div>
					            	</div>
								</div>
							</div>
							<div class="uk-width-1-3 uk-text-center">
								<div class="journey">
				            		<i class=" a-marker-basic colored"></i>
				            		<div class="alert-icon action-required">
					            		<i class="a-bell-ring"></i>
									</div>
								</div>
			            	</div> 	
			            </div>		
					</div>
					<div id="building-detail-{{$target}}-c-3-{{$key}}" class="uk-margin-remove" style="flex: 750px;" uk-grid>
						<div class="uk-width-1-1">
							<div uk-grid>
								<div class="uk-width-2-5 ">
									<div uk-grid class="building-address">
										
						            	<div class="uk-width-1-1 uk-padding-remove">
						            		<h3 class="uk-margin-bottom-remove colored">{{$detail['street']}}</h3>
							            	<small class="colored">{{$detail['city']}}, {{$detail['state']}} {{$detail['zip']}}</small><br />
							            	<small class="colored"><span class="uk-badge colored">3</span> <span class="uk-text-middle">INSPECTABLE ITEMS + FILE AUDIT</span></small>
						            	</div>
						            </div>
					            </div>
								<div class="uk-width-3-5 uk-margin-small-top">
									<div uk-grid>
						            	<div class="uk-width-1-1 findings-icons"  style="margin-top: 10px;" uk-grid> 
						            		
						            		<div class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center action-needed">
						            			<div class="findings-icon">
													<i class="a-folder"></i>
													<div class="findings-icon-status">
														<span class="uk-badge action-needed">3</span>
													</div>
												</div>
												
											</div>
											
											<div class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center in-progress">
												<div class="findings-icon">
													<i class="a-booboo"></i>
													<div class="findings-icon-status">
														<i class="a-rotate-left in-progress"></i>
													</div>
												</div>
											</div>
											<div class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center in-progress">
												<div class="findings-icon">
													<i class="a-skull"></i>
													<div class="findings-icon-status">
														<span class="uk-badge in-progress">3</span>
													</div>
												</div>
											</div>
											<div class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center action-required">	
												<div class="findings-icon">
													<i class="a-flames"></i>
													<div class="findings-icon-status">
														<span class="uk-badge action-required">3</span>
													</div>
												</div>
											</div> 
										</div>
										<div class="uk-width-1-1 findings-action ok-actionable" style="margin-top: 10px;">
											<button class="uk-button program-status uk-link" onclick="inspectionDetails({{$detail['id']}},{{$building}},{{$audit}},{{$key}},{{$targetaudit}},{{$loop->iteration}});"><i class="a-home-search"></i> 2 PROGRAMS</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="uk-width-1-1 uk-margin-remove-top uk-padding-remove">
							<div id="inspection-main-{{$key}}" style="display:none;"></div>
						</div>
					</div>
					<div id="building-detail-{{$target}}-c-5-{{$key}}" style="flex: 590px;" class="uk-margin-remove" uk-grid>
						<div class="uk-width-1-1" id="inspection-tools-switch-{{$key}}">
							<div uk-grid class="area-status-list">
								@foreach($detail['areas'] as $area)
							    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}} colored">
							    	@if($area['qty'])<span class="uk-badge">{{$area['qty']}}</span> @else <i class="a-circle-checked"></i>@endif {{$area['type']}}
							    </div>
							    @endforeach
							</div>
						</div>
						<div class="uk-width-1-1 uk-margin-remove-top uk-padding-remove">
							<div id="inspection-tools-{{$key}}" style="display:none;"></div>
						</div>
					</div>
					<div id="building-detail-{{$target}}-c-6-{{$key}}">
						<div uk-grid class="building-history">
							<div class="uk-width-1-1">
								<i class="a-person-clock colored uk-link"></i>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
