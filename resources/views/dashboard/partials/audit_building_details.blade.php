		<div class="building-details uk-overflow-auto" style="">
			<div class="sortable" uk-sortable="handle: .uk-sortable-details">
				@if($details)
				@foreach($details as $key=>$detail)
				@php
				//dd($detail);
				@endphp
				<div id="building-{{$context}}-detail-r-{{$key}}" class="building building-detail @if($detail->unit) building-{{$detail->unit->status}} {{$detail->unit->status}} @endif uk-grid-match uk-margin-remove" data-audit="{{$detail->audit_id}}" data-building="{{$detail->building_id}}" data-area="{{$detail->unit_id}}" uk-grid>
					<div class="uk-width-1-6 uk-padding-remove">
						<div class="uk-padding-remove uk-flex">
							<div id="building-{{$context}}-detail-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-details-disabled" style="    min-width: 16px; padding: 0 3px;">
								<div class="linespattern"></div>
								<span id="building-{{$context}}-detail-rid-{{$key}}" class="uk-position-bottom-center colored"><small>#<span class="rowindex">{{$loop->iteration}}</span></small></span>
							</div>
							<div id="building-{{$context}}-detail-{{$target}}-c-2-{{$key}}" class="building-type">
								<div class="uk-padding-remove building-type-top uk-height-1-1" uk-grid>
									<div class="uk-width-3-4 uk-padding-remove">
										<div uk-grid>
											<div class="uk-width-1-1 uk-padding-remove">
												<div uk-grid style="padding-top:10px;">
													<div id="unit-auditors-{{$detail->unit_id}}" class="building-auditors uk-width-1-2 @if($detail->auditors()) hasAuditors @endif">
														@if($detail->auditors())
														<div uk-slideshow="animation: slide; min-height:90;">
														    <div class="uk-position-relative uk-visible-toggle">
														        <ul class="uk-slideshow-items" style="min-height: 90px;">
														            <li class="uk-active uk-transition-active" style="transform: translateX(0px);">
														            	<div uk-grid>
																		@foreach($detail->auditors() as $auditor)
																		<div id="unit-auditor-{{$auditor->id}}{{$audit}}{{$building}}{{$detail->unit->unit_id}}" class="building-auditor uk-width-1-2 uk-margin-remove">
																			<div id="building-{{$context}}-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor->full_name()}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor->badge_color}} no-float use-hand-cursor" onclick="swapAuditor({{$auditor->id}}, {{$audit}}, {{$building}}, {{$detail->unit->unit_id}}, 'unit-auditor-{{$auditor->id}}{{$audit}}{{$building}}{{$detail->unit->unit_id}}')">
																				{{$auditor->initials()}}
																			</div>
																			@if($auditor->status != '')
																			<div class="auditor-status"><span></span></div>
																			@endif
																		</div>
																	@if($loop->iteration % 6 == 0 && $loop->iteration < count($detail->auditors()) )
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

														@else
														<div uk-slideshow="animation: slide; min-height:90;">
														    <div class="uk-position-relative uk-visible-toggle">
														        <ul class="uk-slideshow-items" style="min-height: 90px;">
														            <li class="uk-active uk-transition-active" style="transform: translateX(0px);">
														            	<div uk-grid>
													            		@php
																		$rand = mt_rand();
																		@endphp
																		<div class="unit-auditor uk-width-1-2 uk-margin-remove">
																		<div id="unit-auditor-{{$rand}}" class="building-auditor uk-width-1-2 uk-margin-remove">
																			<i class="a-avatar-plus_1 use-hand-cursor" uk-tooltip="pos:top-left;title:ASSIGN AUDITOR;" onclick="assignAuditor({{$audit}}, {{$building}}, {{$detail->unit_id}}, 0, 'unit-auditor-{{$audit}}{{$building}}{{$detail->unit_id}}');"></i>
																		</div>
																		</div>
																		</div>
														            </li>
														        </ul>

														    </div>

														    <ul class="uk-slideshow-nav uk-dotnav uk-flex-center"></ul>

														</div>



														@endif

													</div>
													<div class="uk-width-1-2 uk-padding-remove">
														<div class="building-type-icon ">
															@if($detail->unit->type == "pool")
															<i class="a-pool colored"></i>
															@else
															<i class="a-buildings-2 colored"></i>
															@endif
														</div>
														<div class="building-status">
															<span class="uk-badge colored" uk-tooltip="pos:top-left;title:# finding icon;" title="" aria-expanded="false">{{$detail->unit->finding_total}}</span>
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
						            		@if($detail->unit->followup_date !== null)
							            		<div class="alert-icon {{$detail->unit->status}}">
								            		<i class="a-bell-ring" uk-tooltip="pos:top-left;title:Followup: {{\Carbon\Carbon::createFromFormat('Y-m-d', $detail->unit->followup_date)->format('m/d/Y')}};"></i>
												</div>
											@endif
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
											<div class="uk-width-3-5 uk-padding-remove">
												<div uk-grid class="building-address">

									            	<div class="uk-width-1-1 uk-padding-remove">
									            		<h3 class="uk-margin-bottom-remove colored">
									            			{{$detail->unit->unit_name}}</h3>
										            	<small class="colored">@if($detail->unit->address){{$detail->unit->address}}, {{$detail->unit->city}}, {{$detail->unit->state}} {{$detail->unit->zip}}@else NO BUILDING ADDRESS IN DEVCO @endIf</small><br />
										            	<small class="colored"><span id="unit-amenity-count-{{$audit}}{{$building}}{{$detail->unit->unit_id}}" class="uk-text-middle">{{$detail->unit->amenity_totals()}} @if($detail->unit->amenity_totals() > 1) {{$detail->unit->type_text_plural}} @else {{$detail->unit->type_text}} @endif</span></small>
									            	</div>
									            </div>
								            </div>
											<div class="uk-width-2-5 uk-padding-remove uk-margin-small-top">
												<div uk-grid>
									            	<div class="uk-width-1-1 findings-icons"  style="margin-top: 0px;" uk-grid>
									            		<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$detail->unit->finding_file_status}}">
									            			<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building}}, {{$detail->unit->unit_id}}, 'file',null,'0');">
																<i class="a-folder"></i>
																<div class="findings-icon-status">
																	@if($detail->unit->finding_file_completed == 0)
																		<span class="uk-badge {{$detail->unit->finding_file_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{$detail->unit->finding_file_total}}</span>
																		@else
																		<i class="a-rotate-left {{$detail->unit->finding_file_status}}" uk-tooltip="pos:top-left;title:{{$detail->unit->finding_file_total - $detail->unit->finding_file_completed}} in progress<br />{{$detail->unit->finding_file_completed}} completed;"></i>
																	@endif
																</div>
															</div>
														</div>

														<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$detail->unit->finding_nlt_status}}">
															<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building}}, {{$detail->unit->unit_id}}, 'nlt',null,'0');">
																<i class="a-booboo"></i>
																<div class="findings-icon-status">
																	@if($detail->unit->finding_nlt_completed == 0)
																		<span class="uk-badge {{$detail->unit->finding_nlt_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{$detail->unit->finding_nlt_total}}</span>
																		@else
																		<i class="a-rotate-left {{$detail->unit->finding_nlt_status}}" uk-tooltip="pos:top-left;title:{{$detail->unit->finding_nlt_total - $detail->unit->finding_nlt_completed}} in progress<br />{{$detail->unit->finding_nlt_completed}} completed;"></i>
																	@endif
																</div>
															</div>
														</div>

														<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$detail->unit->finding_lt_status}}">
															<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building}}, {{$detail->unit->unit_id}}, 'lt',null,'0');">
																<i class="a-skull"></i>
																<div class="findings-icon-status">
																	@if($detail->unit->finding_lt_completed == 0)
																		<span class="uk-badge {{$detail->unit->finding_lt_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{$detail->unit->finding_lt_total}}</span>
																		@else
																		<i class="a-rotate-left {{$detail->unit->finding_lt_status}}" uk-tooltip="pos:top-left;title:{{$detail->unit->finding_lt_total - $detail->unit->finding_lt_completed}} in progress<br />{{$detail->unit->finding_lt_completed}} completed;"></i>
																	@endif
																</div>
															</div>
														</div>
													</div>
													<div class="uk-width-1-1 findings-action ok-actionable" style="margin-top: 0px;">
														<button class="uk-button program-status uk-link" onclick="inspectionDetails({{$detail->unit->id}},{{$building}},{{$audit}},{{$key}},{{$targetaudit}},{{$loop->iteration}},'{{$context}}');"><i class="a-home-search"></i> INSPECT UNIT</button>
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
										@if($detail->unit->amenities_and_findings())

										<div uk-grid class="area-status-list">
										    @foreach($detail->unit->amenities_and_findings() as $amenity)
												@if($loop->iteration < 9)
											    <div class="uk-width-1-3 use-hand-cursor uk-padding-remove-top uk-margin-remove-top area-status colored @if($amenity['status'] != '') area-status-{{$amenity['status']}} @endif "  onclick="openFindings(this, {{$audit}}, {{$building}}, {{$detail->unit->unit_id}}, 'all', {{$amenity['id']}});">
											    	<span class="uk-badge">@if($amenity['completed'] == 1) <i class="a-check"></i> @else {{$amenity['findings_total']}} @endif</span>
											    	{{$amenity['name']}}
											    </div>
											    @else
											    	@if($loop->iteration == 9)
												    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status colored @if($amenity['status'] != '') area-status-{{$amenity['status']}} @endif">
												    	<span class="uk-badge" uk-tooltip="pos:top-left;title: @endif {{$amenity['findings_total']}} {{$amenity['name']}}<br /> @if($loop->last) ;"><i class="a-plus"></i> </span> MORE...
												    </div>
												    @endif
											    @endif
											@endforeach
										</div>
										@endif
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
										<div class="uk-width-1-1">
											<i class="a-arrow-diagonal-both use-hand-cursor" uk-tooltip="pos:top-left;title:CLICK TO SWAP UNITS;" onclick="dynamicModalLoad('projects/{{$project_id}}/programs/0/summary/{{$audit}}',0,0,1);"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endforeach
				@endif
			</div>
		</div>
