		<td colspan="10">
			<div class="rowinset-top">PROJECT LEVEL INSPECTION AREAS AND BUILDINGS <span class="uk-link" style="color:#ffffff;" onclick="$('#audit-r-{{$target}}-buildings').remove();"><i class="a-circle-cross"></i></span></div>
			<div class="buildings uk-overflow-auto" style="">
				<div class="sortablebuildings sortable" uk-sortable="handle: .uk-sortable-handle-{{$context}}">
					@foreach($buildings as $key=>$building)
					<div id="building-{{$context}}-r-{{$key}}" class="uk-margin-remove building @if($building->building->status) building-{{$building->building->status}} {{$building->building->status}} @endif @if($building->building->status != 'critical') notcritical @endif uk-grid-match" style=" @if(session('audit-hidenoncritical') == 1 && $building->building->status != 'critical') display:none; @endif " data-audit="{{$building->building->audit_id}}" data-building="{{$building->building->id}}" uk-grid>
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
														<div id="building-auditors-{{$building->building->building_id}}" class="building-auditors uk-width-1-2 @if(count($building->auditors())) hasAuditors @endif">
															@if(count($building->auditors()))
															<div uk-slideshow="animation: slide; min-height:90;">
															    <div class="uk-position-relative uk-visible-toggle">
															        <ul class="uk-slideshow-items" style="min-height: 90px;">
															            <li class="uk-active uk-transition-active" style="transform: translateX(0px);">
															            	<div uk-grid>
																			@foreach($building->auditors() as $auditor)
																			<div class="building-auditor uk-width-1-2 uk-margin-remove">
																				@if($building->building->building_id === NULL)
																				<div id="building-{{$context}}-{{$auditor->id}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor->full_name()}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor->badge_color}} use-hand-cursor no-float" onclick="swapAuditor({{$auditor->id}}, {{$audit}}, 0, 0, 'building-audits-{{$auditor->id}}-avatar-1', {{$building->building->amenity_id}})">
																					{{$auditor->initials()}}
																				</div>
																				@else
																				<div id="building-{{$context}}-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor->full_name()}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor->badge_color}} use-hand-cursor no-float" onclick="swapAuditor({{$auditor->id}}, {{$audit}}, {{$building->building->building_id}}, 0, 'building-auditors-{{$building->building->building_id}}')">
																					{{$auditor->initials()}}
																				</div>
																				@endif
																				@if($auditor->status != '')
																				<div class="auditor-status"><span></span></div>
																				@endif
																			</div>
																		@if($loop->iteration % 6 == 0 && $loop->iteration < count($building->auditors()) )
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
																			<div class="building-auditor uk-width-1-2 uk-margin-remove">
																			<div id="building-auditor-{{$rand}}" class="building-auditor uk-width-1-2 uk-margin-remove">
																				@if($building->building->building_id === NULL)
																				<i class="a-avatar-plus_1 use-hand-cursor" uk-tooltip="pos:top-left;title:ASSIGN AUDITOR;" onclick="assignAuditor({{$audit}}, 0, 0, {{$building->building->amenity_id}}, 'building-auditor-{{$rand}}');"></i>
																				@else
																				<i class="a-avatar-plus_1 use-hand-cursor" uk-tooltip="pos:top-left;title:ASSIGN AUDITOR;" onclick="assignAuditor({{$audit}}, {{$building->building->building_id}}, 0, 0, 'building-auditor-{{$rand}}');"></i>
																				@endif
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
															<div class="building-type-icon " uk-tooltip="pos:top-left;title:Building ID {{$building->building->id}};">
																@if($building->building->type == "pool")
																<i class="a-pool colored"></i>
																@else
																<i class="a-buildings colored"></i>
																@endif
															</div>
															<div class="building-status">
																
																<span class="uk-badge colored" uk-tooltip="pos:top-left;" title="@if(!is_null($building->building->findings_json))
																	<?php 
																	foreach($building->building->findings_json as $finding){
																		echo strtoupper($finding->finding_type).' : '.$finding->finding_description.'<br/>';
																		//echo 'hello';
																	}
																	?>@else NO FINDINGS @endIf">{{$building->building->finding_total}}</span>
																
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
							            		@if($building->building->followup_date !== null)
							            		<div class="alert-icon {{$building->building->status}}">
								            		<?php $today = date('m/d/Y',time()); 
								            			  $dueDate = \Carbon\Carbon::createFromFormat('Y-m-d',$building->building->followup_date)->format('m/d/Y');
								            			?>
								            		@if($dueDate < $today)
								            		<i class="a-bell-ring" uk-tooltip="pos:top-left;title:PAST DUE<br />FOLLOWUP: {{\Carbon\Carbon::createFromFormat('Y-m-d', $building->building->followup_date)->format('m/d/Y')}}: {{$building->building->followup_description}};"></i>
								            		@elseIf($dueDate === $today)
								            		<i class="a-bell-ring" uk-tooltip="pos:top-left;title:DUE TODAY<br />FOLLOWUP: {{\Carbon\Carbon::createFromFormat('Y-m-d', $building->building->followup_date)->format('m/d/Y')}}: {{$building->building->followup_description}};"></i>
								            		@else
								            		<i class="a-bell-2" uk-tooltip="pos:top-left;title:FOLLOWUP: {{\Carbon\Carbon::createFromFormat('Y-m-d', $building->building->followup_date)->format('m/d/Y')}}: {{$building->building->followup_description}};"></i>
								            		@endIf
												</div>
												@else
												<div style="margin-top: 12px;">
								            		<i class="a-bell" uk-tooltip="pos:top-left;title:NO INCOMPLETE FOLLOWUPS;"></i>
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
								<div class="uk-width-3-5">
									<div id="building-{{$context}}-{{$target}}-c-3-{{$key}}" class="uk-margin-remove" style="flex: 750px;" uk-grid>
										<div class="uk-width-1-1">
											<div uk-grid>
												<div class="uk-width-3-5 uk-padding-remove">
													<div class="building-address" uk-grid>
										            	<div class="uk-width-1-1 uk-padding-remove">
										            		@if($building->building->building_name)
										            		<h3 class="uk-margin-bottom-remove colored">{{$building->building->building_name}}</h3>
											            	<small class="colored">
											            		{{$building->building->address}}, {{$building->building->city}}, {{$building->building->state}} {{$building->building->zip}}</small>
										            		@else
										            		<h3 class="uk-margin-bottom-remove colored">{{$building->building->address}}</h3>
											            	<small class="colored">{{$building->building->city}}, {{$building->building->state}} {{$building->building->zip}}</small>
										            		@endif
										            		
											            	@if($building->building->type != "pool")
											            	<br />
											            	@if($building->building->building_id != '')
											            	<small class="colored use-hand-cursor" onclick="buildingDetails({{$building->building->building_id}},{{$audit}},{{$key}},{{$target}},10,'{{$context}}');" uk-tooltip="pos:top-left;title:Building details;" ><i class="a-menu colored uk-text-middle"></i> 
											            		@if($building->building->type_total > 0)
											            		<span class="uk-text-middle uk-text-uppercase">{{$building->building->type_total}} @if($building->building->type_total > 1) {{$building->building->type_text_plural}} @else {{$building->building->type_text}} @endif</span>
											            		@endif
											            	</small>
											            	@endif
											            	@endif
										            	</div>
										            </div>
										        </div>
										        <div class="uk-width-2-5 uk-padding-remove uk-margin-small-top">
													<div uk-grid>
														<div class="uk-width-1-1 findings-icons" uk-grid style="margin-top: 0px;"> 
										            		<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$building->building->finding_file_status}} action-needed">
										            			<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building->building->id}}, null, 'file');">
																	<i class="a-folder"></i>
																	<div class="findings-icon-status">
																		@if($building->building->finding_file_completed == 0)
																		<span class="uk-badge {{$building->building->finding_file_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{($building->building->finding_file_total) ? $building->building->finding_file_total : 0}}</span>
																		@else
																		<i class="a-rotate-left {{$building->building->finding_file_status}}" uk-tooltip="pos:top-left;title:{{$building->building->finding_file_total - $building->building->finding_file_completed}} in progress<br />{{$building->building->finding_file_completed}} completed;"></i>
																		@endif
																	</div>
																</div>
																
															</div>
															<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$building->building->finding_nlt_status}}">
																<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building->building->id}}, null, 'nlt');">
																	<i class="a-booboo"></i>
																	<div class="findings-icon-status">
																		@if($building->building->finding_nlt_completed == 0)
																		<span class="uk-badge {{$building->building->finding_nlt_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{$building->building->finding_nlt_total}}</span>
																		@else
																		<i class="a-rotate-left {{$building->building->finding_nlt_status}}" uk-tooltip="pos:top-left;title:{{$building->building->finding_nlt_total - $building->building->finding_nlt_completed}} in progress<br />{{$building->building->finding_nlt_completed}} completed;"></i>
																		@endif
																	</div>
																</div>
															</div>
															<div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-center {{$building->building->finding_lt_status}}">
																<div class="findings-icon" onclick="openFindings(this, {{$audit}}, {{$building->building->id}}, null, 'lt');">
																	<i class="a-skull" uk-tooltip="pos:top-left;title:Reason;"></i>
																	<div class="findings-icon-status">
																		@if($building->building->finding_lt_completed == 0)
																		<span class="uk-badge {{$building->building->finding_lt_status}}" uk-tooltip="pos:top-left;title:Unit # finding;">{{$building->building->finding_lt_total}}</span>
																		@else
																		<i class="a-rotate-left {{$building->building->finding_lt_status}}" uk-tooltip="pos:top-left;title:{{$building->building->finding_lt_total - $building->building->finding_lt_completed}} in progress<br />{{$building->building->finding_lt_completed}} completed;"></i>
																		@endif
																	</div>
																</div>
															</div>
														</div>
														<div class="uk-width-1-1 uk-margin-remove findings-action ok-actionable" style="margin-top: 0px;">
															@if($building->building->building_id !== NULL)
															<button class="uk-button program-status uk-link" onclick="@if($building->building->building_id) inspectionDetailsFromBuilding({{$building->building->building_id}}, {{$audit}}, {{$key}},{{$target}}, {{$loop->iteration}},'{{$context}}'); @else inspectionDetailsFromBuilding(0, {{$audit}}, {{$key}},{{$target}}, {{$loop->iteration}},'{{$context}}'); @endif"><i class="a-home-search"></i> INSPECT BUILDING</button>
															@endif
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
								<div class="uk-width-2-5 uk-flex">
									<div id="building-{{$context}}-{{$target}}-c-5-{{$key}}" style="flex: 640px;" class="uk-margin-remove" uk-grid>
										<div class="uk-width-1-1" id="inspection-{{$context}}-tools-switch-{{$key}}">
											<div uk-grid class="area-status-list">
												@if($building->building->amenities_json)
												@foreach($building->building->amenities_json as $amenity)
												@if($loop->iteration < 9)
											    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status @if($amenity->status != '') area-status-{{$amenity->status}} @endif colored">
											    	<span onclick="openFindings(this, {{$audit}}, {{$building->building->id}}, null, 'all','{{$amenity->id}}');"><span class="uk-badge"  >
											    	@if($amenity->qty){{$amenity->qty}} @else 1 @endif </span>
											    	{{$amenity->type}}</span>
											    </div>
											    @else
											    	@if($loop->iteration == 9)
												    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status @if($amenity->status != '') area-status-{{$amenity->status}} @endif colored">
												    	<span onclick="openFindings(this, {{$audit}}, {{$building->building->id}}, null, 'all','{{$amenity->id}}');"><span class="uk-badge" uk-tooltip="pos:top-left;title: @endif @if($amenity->qty) {{$amenity->qty}} @endif {{$amenity->type}}<br /> @if($loop->last) ;" ><i class="a-plus"></i> </span> MORE...</span>
												    </div>
												    @endif
											    @endif
											    @endforeach
											    @endif
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