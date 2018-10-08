		<div class="building-details uk-overflow-auto" style="">
			<div class="sortable" uk-sortable="handle: .uk-sortable-details">
				@foreach($details as $key=>$detail)
				<div id="building-{{$context}}-detail-r-{{$key}}" class="building building-detail @if($detail['status']) building-{{$detail['status']}} {{$detail['status']}} @endif uk-grid-match uk-margin-remove" uk-grid>
					<div class="uk-width-1-6 uk-padding-remove">
						<div class="uk-padding-remove uk-flex">
							<div id="building-{{$context}}-detail-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-details" style="    min-width: 16px; padding: 0 3px;">
								<div class="linespattern"></div>
								<span id="building-{{$context}}-detail-rid-{{$key}}" class="uk-position-bottom-center colored"><small>#<span class="rowindex">{{$loop->iteration}}</span></small></span>
							</div>
							<div id="building-{{$context}}-detail-{{$target}}-c-2-{{$key}}" class="building-type">
								<div class="uk-padding-remove building-type-top uk-height-1-1" uk-grid>
									<div class="uk-width-3-4 uk-padding-remove">
										<div uk-grid>
											<div class="uk-width-1-1 uk-padding-remove">
												<div uk-grid style="padding-top:10px;">
													<div class="building-auditors uk-width-1-2">
														<div uk-slideshow="animation: slide; min-height:90;">

														    <div class="uk-position-relative uk-visible-toggle">

														        <ul class="uk-slideshow-items">
														            <li>
														            	<div uk-grid>
														                @foreach($detail['auditors'] as $auditor)
																		<div class="building-auditor uk-width-1-2 uk-margin-remove">
																			<div id="building-{{$context}}-detail-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
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
													<div class="uk-width-1-2 uk-padding-remove">
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
											<div id="inspection-{{$context}}-detail-menus-{{$key}}-container" class="uk-width-1-1 uk-margin-remove building-type-bottom" style="display:none;">
							            		<div id="inspection-{{$context}}-detail-menus-{{$key}}"></div>
							            	</div>
										</div>
									</div>
									<div class="uk-width-1-4 uk-padding-remove uk-text-right">
										<div class="journey">
						            		<i class=" a-marker-basic colored"></i>
						            		<div class="alert-icon action-required">
							            		<i class="a-bell-ring"></i>
											</div>
										</div>
					            	</div> 	
					            </div>		
							</div>
						</div>
					</div>
					<div class="uk-width-5-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-2">
								<div id="building-{{$context}}-detail-{{$target}}-c-3-{{$key}}" class="uk-margin-remove" style="flex: 750px;" uk-grid>
									<div class="uk-width-1-1">
										<div uk-grid>
											<div class="uk-width-1-2 uk-padding-remove">
												<div uk-grid class="building-address">
													
									            	<div class="uk-width-1-1 uk-padding-remove">
									            		<h3 class="uk-margin-bottom-remove colored">{{$detail['street']}}</h3>
										            	<small class="colored">{{$detail['city']}}, {{$detail['state']}} {{$detail['zip']}}</small><br />
										            	<small class="colored"><span class="uk-badge colored">3</span> <span class="uk-text-middle">INSPECTABLE ITEMS + FILE AUDIT</span></small>
									            	</div>
									            </div>
								            </div>
											<div class="uk-width-1-2 uk-padding-remove uk-margin-small-top">
												<div uk-grid>
									            	<div class="uk-width-1-1 findings-icons"  style="margin-top: 0px;" uk-grid> 
									            		
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
													<div class="uk-width-1-1 findings-action ok-actionable" style="margin-top: 0px;">
														<button class="uk-button program-status uk-link" onclick="inspectionDetails({{$detail['id']}},{{$building}},{{$audit}},{{$key}},{{$targetaudit}},{{$loop->iteration}},'{{$context}}');"><i class="a-home-search"></i> 2 PROGRAMS</button>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="inspection-{{$context}}-detail-main-{{$key}}-container" class="uk-width-1-1 uk-margin-remove-top uk-padding-remove" style="display:none;">
										<div id="inspection-{{$context}}-detail-main-{{$key}}" class="inspection-detail-main-list"></div>
									</div>
								</div>
							</div>
							<div class="uk-width-1-2 uk-flex">
								<div id="building-{{$context}}-detail-{{$target}}-c-5-{{$key}}" style="flex: 640px;" class="uk-margin-remove" uk-grid>
									<div class="uk-width-1-1" id="inspection-{{$context}}-detail-tools-switch-{{$key}}">
										<div uk-grid class="area-status-list">
											@foreach($detail['areas'] as $area)
										    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}} colored">
										    	<span class="uk-badge">
										    	@if($area['qty']){{$area['qty']}} @else <i class="a-check"></i>@endif </span> {{$area['type']}}
										    </div>
										    @endforeach
										</div>
									</div>
									<div id="inspection-{{$context}}-detail-tools-{{$key}}-container" class="uk-width-1-1 uk-margin-remove-top uk-padding-remove" style="display:none;">
										<div id="inspection-{{$context}}-detail-tools-{{$key}}"></div>
									</div>
								</div>
								<div id="building-{{$context}}-detail-{{$target}}-c-6-{{$key}}">
									<div uk-grid class="building-history">
										<div class="uk-width-1-1">
											<i class="a-person-clock colored uk-link"></i>
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
