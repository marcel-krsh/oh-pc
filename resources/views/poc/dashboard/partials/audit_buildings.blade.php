	<tr id="audit-r-{{$target}}-buildings" class="rowinset">
		<td colspan="10">
			<div class="rowinset-top">INSPECTION AREAS <span class="uk-link" style="color:#ffffff;" onclick="$('#audit-r-{{$target}}-buildings').remove();"><i class="a-circle-cross"></i></span></div>
			<div class="buildings uk-overflow-auto" style="">
				

				<div class="sortable" uk-sortable="handle: .uk-sortable-handle">
					@foreach($buildings as $key=>$building)
					<div id="building-r-{{$key}}" class="uk-flex uk-flex-row building @if($building['status'] == 'critical') building-critical @endif " >
						<div id="building-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-handle" style="    min-width: 16px; padding: 0 3px;">
							<span id="building-rid-1" class="uk-position-bottom-right colored"><small>#{{$building['id']}}</small></span>
						</div>
						<div id="building-{{$target}}-c-2-{{$key}}" class="building-type">
							<div  uk-grid>
								<div class="building-auditors uk-width-2-5">
									<div uk-grid>
										@foreach($building['auditors'] as $auditor)
										<div class="building-auditor uk-width-1-2 uk-margin-remove">
											<div id="building-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
												{{$auditor['initials']}}
											</div>
											<div class="auditor-status"><i class="a-circle-checked"></i></div>
										</div>
										@endforeach
									</div>
								</div>
								<div class="building-type-icon uk-width-2-5">
									<i class="a-buildings"></i>
								</div>
								<div class="building-type-bell uk-width-1-5">
									<i class="a-bell-2"></i>
								</div>
							</div>
						</div>
						<div id="building-{{$target}}-c-3-{{$key}}">
							<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top">
			            		<i class="a-home-marker uk-text-muted"></i>
			            	</div> 
			            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
			            		<h3 class="uk-margin-bottom-remove">{{$building['street']}}</h3>
				            	<small class="uk-text-muted">{{$building['city']}}, {{$building['state']}} {{$building['zip']}}</small><br />
				            	<small class="colored"><span class="uk-badge">3</span> TOWN HOMES</small>
			            	</div>
						</div>
						<div id="building-{{$target}}-c-4-{{$key}}">
							<div uk-grid>
				            	<div class="uk-width-1-1" uk-grid> 
				            		<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span> 
								</div>
								<div class="uk-width-1-1 uk-margin-remove">
									<button class="uk-button program-status">2 PROGRAMS</button>
								</div>
							</div>
						</div>
						<div id="building-{{$target}}-c-5-{{$key}}">
							<div uk-grid>
								@foreach($building['areas'] as $area)
							    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}}">
							    	@if($area['qty'])<span class="uk-badge">{{$area['qty']}}</span> @else <i class="a-circle-checked"></i>@endif {{$area['type']}}
							    </div>
							    @endforeach
							</div>
						</div>
						<div id="building-{{$target}}-c-6-{{$key}}">
							<i class="a-avatar-clock"></i>
						</div>
					</div>
					@endforeach
				</div>


				<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="display:none">
					<thead>
						<tr>
							<th class="uk-table-shrink"></th>
							<th class="uk-table-small" style="width:190px;"></th>
							<th class="uk-table-expand"></th>
							<th style="min-width:190px;"></th>
							<th style="min-width:190px;"></th>
							<th class="uk-table-shrink"></th>
						</tr>
					</thead>
					<tbody  >
					@foreach($buildings as $key=>$building)
					<tr id="building-r-{{$key}}" class="building @if($building['status'] == 'critical') building-critical @endif " >
						<td id="building-{{$target}}-c-1-1" class="uk-text-bottom uk-sortable-handle">
							<span id="building-rid-1" class="colored"><small>#{{$building['id']}}</small></span>
						</td>
						<td id="building-{{$target}}-c-2-1">
							<div class="building-type">
								<div  uk-grid>
									<div class="building-auditors uk-width-2-5">
										<div uk-grid>
											@foreach($building['auditors'] as $auditor)
											<div class="building-auditor uk-width-1-2 uk-margin-remove">
												<div id="building-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
													{{$auditor['initials']}}
												</div>
												<div class="auditor-status"><i class="a-circle-checked"></i></div>
											</div>
											@endforeach
										</div>
									</div>
									<div class="building-type-icon uk-width-2-5">
										<i class="a-buildings"></i>
									</div>
									<div class="building-type-bell uk-width-1-5">
										<i class="a-bell-2"></i>
									</div>
								</div>
							</div>
						</td>
						<td id="building-{{$target}}-c-3-1">
							<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top">
			            		<i class="a-home-marker uk-text-muted"></i>
			            	</div> 
			            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
			            		<h3 class="uk-margin-bottom-remove">{{$building['street']}}</h3>
				            	<small class="uk-text-muted">{{$building['city']}}, {{$building['state']}} {{$building['zip']}}</small><br />
				            	<small class="colored"><span class="uk-badge">3</span> TOWN HOMES</small>
			            	</div>
						</td>
						<td id="building-{{$target}}-c-4-1">
							<div uk-grid>
				            	<div class="uk-width-1-1" uk-grid> 
				            		<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span>
									<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-center">
										<i class="a-avatar"></i>
									</span> 
								</div>
								<div class="uk-width-1-1 uk-margin-remove">
									<button class="uk-button program-status">2 PROGRAMS</button>
								</div>
							</div>
						</td>
						<td id="building-{{$target}}-c-5-1">
							<div uk-grid>
								@foreach($building['areas'] as $area)
							    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}}">
							    	@if($area['qty'])<span class="uk-badge">{{$area['qty']}}</span> @else <i class="a-circle-checked"></i>@endif {{$area['type']}}
							    </div>
							    @endforeach
							</div>
						</td>
						<td id="building-{{$target}}-c-6-1">
							<i class="a-avatar-clock"></i>
						</td>
					</tr>
					@endforeach
					</tbody>
				</table>
			</div>
			<div class="rowinset-bottom">
				<span class="uk-link" style="color:#ffffff;" onclick="addArea({{$audit}});">+ ADD INSPECTABLE AREA TO PROJECT</span>
			</div>
			<script>


				UIkit.util.on('.sortable', 'start', function (item) {console.log(item.detail);
				  // $(this).attr('data-previndex', item.index());
				  // console.log(item.detail[1].id)
				  // UIkit.notification(`Moving ${item.detail[1].id}`, 'success');
				});
				UIkit.util.on('.sortable', 'moved', function (item) {
					// var newIndex = item.index();
			  //       var oldIndex = $(this).attr('data-previndex');
			        // $(this).removeAttr('data-previndex');
				  console.log(item.detail[1].id)
				  //UIkit.notification(`Moving ${item.detail[1].id} from `+oldIndex+' to '+newIndex, 'success');
				});
			</script>
		</td>
	</tr>