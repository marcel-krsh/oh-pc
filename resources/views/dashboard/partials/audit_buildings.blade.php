		<td colspan="10">
			<div class="rowinset-top">INSPECTION AREAS <span class="uk-link" style="color:#ffffff;" onclick="$('#audit-r-{{$target}}-buildings').remove();"><i class="a-circle-cross"></i></span></div>
			<div class="buildings uk-overflow-auto" style="">
				<div class="sortable" uk-sortable="handle: .uk-sortable-handle">
					@foreach($buildings as $key=>$building)
					<div id="building-r-{{$key}}" class="uk-flex uk-flex-row building @if($building['status']) building-{{$building['status']}} {{$building['status']}} @endif " >
						<div id="building-{{$target}}-c-1-{{$key}}" class="uk-inline uk-sortable-handle" style="    min-width: 16px; padding: 0 3px;">
							<div class="linespattern"></div>
							<span id="building-rid-1" class="uk-position-bottom-center colored"><small>#<span class="rowindex">{{$loop->iteration}}</span></small></span>
						</div>
						<div id="building-{{$target}}-c-2-{{$key}}" class="building-type">
							<div  uk-grid>
								<div class="building-auditors uk-width-1-2">
									<div uk-slideshow="animation: slide; min-height:90;">

									    <div class="uk-position-relative uk-visible-toggle">

									        <ul class="uk-slideshow-items">
									            <li>
									            	<div uk-grid>
									                @foreach($building['auditors'] as $auditor)
													<div class="building-auditor uk-width-1-2 uk-margin-remove">
														<div id="building-{{$target}}-avatar-{{$loop->iteration}}" uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{$auditor['color']}} no-float">
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
								<div class="uk-width-1-2">
									<div class="building-type-icon ">
										@if($building['type'] == "pool")
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
						<div id="building-{{$target}}-c-3-{{$key}}">
							<div class="building-address" uk-grid>
								<div class="uk-width-1-6 uk-text-center @if($loop->last) journey-end @elseif($loop->first) journey-start @else journey @endif">
				            		<i class="@if($loop->last) a-home-marker @elseif($loop->first) a-home-marker @else a-marker-basic @endif colored"></i>
				            		<div class="alert-icon action-required">
					            		<i class="a-bell-ring"></i>
									</div>
				            	</div> 
				            	<div class="uk-width-5-6 uk-padding-remove">
				            		<h3 class="uk-margin-bottom-remove colored">{{$building['street']}}</h3>
					            	<small class="colored">{{$building['city']}}, {{$building['state']}} {{$building['zip']}}</small><br />
					            	<small class="colored" onclick="buildingDetails(123,{{$audit}},{{$key}},{{$target}});" uk-tooltip="pos:top-left;title:Building details;" ><span class="uk-badge colored">3</span> <i class="a-list colored uk-text-middle"></i> <span class="uk-text-middle">TOWN HOMES</span></small>
				            	</div>
				            </div>
						</div>
						<div id="building-{{$target}}-c-4-{{$key}}">
							<div uk-grid>
				            	<div class="uk-width-1-1 findings-icons" uk-grid style="margin-top: 10px; margin-bottom: 10px;"> 
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
								<div class="uk-width-1-1 uk-margin-remove findings-action ok-actionable">
									<button class="uk-button program-status uk-link"><i class="a-home-search"></i> 2 PROGRAMS</button>
								</div>
							</div>
						</div>
						<div id="building-{{$target}}-c-5-{{$key}}">
							<div uk-grid class="area-status-list">
								@foreach($building['areas'] as $area)
							    <div class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top area-status area-status-{{$area['status']}} colored">
							    	<span class="uk-badge">
							    	@if($area['qty']){{$area['qty']}} @else <i class="a-check"></i>@endif </span>
							    	{{$area['type']}}
							    </div>
							    @endforeach
							</div>
						</div>
						<div id="building-{{$target}}-c-6-{{$key}}">
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
			<div class="rowinset-bottom">
				<span class="uk-link" onclick="addArea({{$audit}});">+ ADD INSPECTABLE AREA TO PROJECT</span>
			</div>
			<script>
				var startIndex;
				var endIndex;

				function reorder(classname, childclassname) {
					var currentIndex;
					$(classname+" > div").each(function () { 
						// console.log(this.id);
						currentIndex = $(childclassname).index(this) + 1;
						$("#"+this.id).find(".rowindex").each(function () { 
							$(this).html(currentIndex);
						});
					});
				}

				UIkit.util.on('.sortable', 'start', function (item) {
					var listItem = document.getElementById( item.detail[1].id );
					if($('#'+item.detail[1].id).hasClass('building-detail')){
						startIndex = $( ".building-detail" ).index( listItem );
					}else if($('#'+item.detail[1].id).hasClass('building')){
						startIndex = $( ".building" ).index( listItem );
					} 				
					console.log( item.detail[1].id + " started at index: " + startIndex );
				});
				UIkit.util.on('.sortable', 'moved', function (item) {
					var listItem = document.getElementById( item.detail[1].id );
					if($('#'+item.detail[1].id).hasClass('building-detail')){
						endIndex = $( ".building-detail" ).index( listItem );
						console.log( item.detail[1].id + " ended at index: " + endIndex );
						UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
						reorder(".building-details > .sortable", '.building-detail');
					}else if($('#'+item.detail[1].id).hasClass('building')){
						endIndex = $( ".building" ).index( listItem );
						console.log( item.detail[1].id + " ended at index: " + endIndex );
						UIkit.notification("You moved " + item.detail[1].id + " from " + startIndex + " to " + endIndex);
						reorder(".buildings > .sortable", '.building');
					} 
				});
			</script>
		</td>