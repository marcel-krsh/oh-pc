		<td colspan="10">
			<div class="rowinset-top">INSPECTION AREAS <span class="uk-link" style="color:#ffffff;" onclick="$('#audit-r-{{$target}}-buildings').remove();"><i class="a-circle-cross"></i></span></div>
			<div class="buildings uk-overflow-auto" style="">
				<div class="sortable" uk-sortable="handle: .uk-sortable-handle-{{$context}}">
					@foreach($buildings as $key=>$building)
					<div id="building-{{$context}}-r-{{$key}}" class="uk-margin-remove building @if($building['status']) building-{{$building['status']}} {{$building['status']}} @endif @if($building['status'] != 'critical') notcritical @endif uk-grid-match" style=" @if(session('audit-hidenoncritical') == 1 && $building['status'] != 'critical') display:none; @endif " uk-grid>
						<div class="uk-width-1-6 uk-padding-remove">
							<div class="uk-padding-remove uk-flex">
								<div id="building-{{$context}}-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-handle-{{$context}}" style="min-width: 16px; padding: 0 3px;">
									<div class="linespattern"></div>
									<span id="building-{{$context}}-rid-1" class="uk-position-bottom-center colored"><small>#<span class="rowindex">{{$loop->iteration}}</span></small></span>
								</div>
								<div id="building-{{$context}}-{{$target}}-c-2-{{$key}}" class="building-type">
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
															                @foreach($building['auditors'] as $auditor)
																			<div class="building-auditor uk-width-1-2 uk-margin-remove">
																				<div id="building-{{$context}}-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
																					{{$auditor['initials']}}
																				</div>
																				<div class="auditor-status"><i class="a-circle-checked"></i></div>
																			</div>
																		@if($loop->iteration % 6 == 0 && $loop->iteration < count($building['auditors']) )
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
																@if($building['type'] == "pool")
																<i class="a-pool colored"></i>
																@else
																<i class="a-buildings colored"></i>
																@endif
															</div>
															<div class="building-status">
																<i class="a-check colored" uk-tooltip="pos:top-left;title:# finding icon;"></i>
															</div>
														</div>
													</div>
												</div>
												<div id="inspection-{{$context}}-menus-{{$key}}-container" class="uk-width-1-1 uk-margin-remove building-type-bottom" style="display:none;">
								            		<div id="inspection-{{$context}}-menus-{{$key}}"></div>
								            	</div>
											</div>
										</div>
										<div class="uk-width-1-4 uk-padding-remove uk-text-right">
											<div class=" @if($loop->last) journey-end @elseif($loop->first) journey-start @else journey @endif">
							            		<i class="@if($loop->last) a-home-marker @elseif($loop->first) a-home-marker @else a-marker-basic @endif colored"></i>
							            		<div class="alert-icon action-required">
								            		<i class="a-bell-ring" uk-tooltip="pos:top-left;title:Followup: 12/22/2018;"></i>
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
									<div id="building-{{$context}}-{{$target}}-c-3-{{$key}}" class="uk-margin-remove" style="flex: 750px;" uk-grid>
										<div class="uk-width-1-1">
											<div uk-grid>
												<div class="uk-width-1-2 uk-padding-remove">
													<div class="building-address" uk-grid>
										            	<div class="uk-width-1-1 uk-padding-remove">
										            		<h3 class="uk-margin-bottom-remove colored">{{$building['street']}}</h3>
											            	<small class="colored">{{$building['city']}}, {{$building['state']}} {{$building['zip']}}</small><br />
											            	<small class="colored" onclick="buildingDetails(123,{{$audit}},{{$key}},{{$target}},10,'{{$context}}');" uk-tooltip="pos:top-left;title:Building details;" ><span class="uk-badge colored">3</span> <i class="a-list colored uk-text-middle"></i> <span class="uk-text-middle">TOWN HOMES</span></small>
										            	</div>
										            </div>
										        </div>
										        <div class="uk-width-1-2 uk-padding-remove uk-margin-small-top">
													<div uk-grid>
														<div class="uk-width-1-1 findings-icons" uk-grid style="margin-top: 0px;"> 
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
																		<i class="a-rotate-left in-progress" uk-tooltip="pos:top-left;title:23 in progress<br />19 completed;"></i>
																	</div>
																</div>
															</div>
															<div class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center in-progress">
																<div class="findings-icon">
																	<i class="a-skull" uk-tooltip="pos:top-left;title:Reason;"></i>
																	<div class="findings-icon-status">
																		<span class="uk-badge in-progress" uk-tooltip="pos:top-left;title:Unit # finding;">3</span>
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
														<div class="uk-width-1-1 uk-margin-remove findings-action ok-actionable" style="margin-top: 0px;">
															<button class="uk-button program-status uk-link" onclick="inspectionDetailsFromBuilding({{$building['id']}}, {{$audit}}, {{$key}},{{$target}}, {{$loop->iteration}},'{{$context}}'); "><i class="a-home-search"></i> 2 PROGRAMS</button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="inspection-{{$context}}-main-{{$key}}-container" class="uk-width-1-1 uk-margin-remove-top uk-padding-remove" style="display:none;">
											<div id="inspection-{{$context}}-main-{{$key}}" class="inspection-main-list"></div>
										</div>
									</div>
								</div>
								<div class="uk-width-1-2 uk-flex">
									<div id="building-{{$context}}-{{$target}}-c-5-{{$key}}" style="flex: 640px;" class="uk-margin-remove" uk-grid>
										<div class="uk-width-1-1" id="inspection-{{$context}}-tools-switch-{{$key}}">
											<div uk-grid class="area-status-list">
												@foreach($building['areas'] as $area)
												@if($loop->iteration < 9)
											    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}} colored">
											    	<span class="uk-badge">
											    	@if($area['qty']){{$area['qty']}} @else <i class="a-check"></i>@endif </span>
											    	{{$area['type']}}
											    </div>
											    @else
											    	@if($loop->iteration == 9)
												    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}} colored">
												    	<span class="uk-badge" uk-tooltip="pos:top-left;title: @endif @if($area['qty']) {{$area['qty']}} @endif {{$area['type']}}<br /> @if($loop->last) ;"><i class="a-plus"></i> </span> MORE...
												    </div>
												    @endif
											    @endif
											    @endforeach
											</div>
										</div>
										<div id="inspection-{{$context}}-tools-{{$key}}-container" class="uk-width-1-1 uk-margin-remove-top uk-padding-remove" style="display:none;">
											<div id="inspection-{{$context}}-tools-{{$key}}"></div>
										</div>
									</div>
									<div id="building-{{$context}}-{{$target}}-c-6-{{$key}}">
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
			<div class="rowinset-bottom">
				<span class="uk-link" onclick="addArea({{$audit}});">+ ADD INSPECTABLE AREA TO PROJECT</span>
			</div>
			<script>
				
			</script>
		</td>