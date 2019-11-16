			<div class="uk-overflow-auto">
				<div class="divTable divTableFixed">
					<div class="divTableBody">
						<div class="divTableRow divTableHeader">
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
							@foreach($data['auditors'] as $auditor)
							<div class="divTableCell">
								<span uk-tooltip="pos:top-left;title:{{$auditor['name']}};" title="" aria-expanded="false" class="user-badge user-badge-{{$auditor['color']}} no-float uk-link">{{$auditor['initials']}}</span>
							</div>
							@endforeach
							<div class="divTableCell">
								<i class="a-circle-plus"></i>
							</div>
							<div class="divTableCell">&nbsp;</div>
						</div>
						@foreach($data['projects'] as $project)
						<div class="divTableRow @if(Auth::user()->id == $project['lead']) isLead @endif">
							<div class="divTableCell">
								<strong>{{$project['id']}}</strong><br />
								<strong>{{$project['date']}}</strong>
							</div>
							<div class="divTableCell">
								<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;" title="" aria-expanded="false"></i> <strong>{{$project['name']}}</strong><br />
							</div>
							@foreach($project['schedules'] as $schedule)
							<div class="divTableCell {{$schedule['status']}} @if($schedule['is_lead']) isLead @endif">
								@if($schedule['is_lead']) <i class="a-star-3 corner"></i> @endif
								<i class="{{$schedule['icon']}}"></i>
							</div>
							@endforeach
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
						</div>
						@endforeach
						
					</div>
				</div>
			</div>
			<hr />
			<span class="italic">NOTE: YOU CAN ONLY APPROVE SCHEDULE CONFLICTS FOR AUDITS THAT YOU ARE THE LEAD. IF YOU ARE NOT THE LEAD, YOU CAN REQUEST APPROVAL FOR THE CONFLICT BY THE LEAD OF THAT AUDIT.</span>

			<style>

				.divTable{
					display: table;
					width: 100%;
					margin-top: 30px;
				}
				.divTableFixed {
					table-layout: auto;
				}
				.divTableRow {
					display: table-row;
					opacity:0.5;
				}
				.divTableRow.isLead, .divTableHeader {
					opacity:1;
				}
				.divTableHeading {
					background-color: #EEE;
					display: table-header-group;
				}
				.divTableCell, .divTableHead {
					border-left: 2px solid #939598;
					border-top: 2px solid #939598;
					display: table-cell;
					padding: 3px 10px;
					width:50px;
				}
				.divTableHeader i {
				    font-size: 30px;
				    display: inline-block;
				    line-height: 31px;
				    vertical-align: middle;
				    color: #939598;
				}
				.divTableHeader .divTableCell {
					text-align:center;
					padding: 13px 0;
    				height: 32px;
				}
				.divTableHeader .divTableCell span.user-badge{
					width: 30px;
				    height: 30px;
				    color: rgba(255,255,255,0.8);
				    line-height: 31px;
				    font-size: 12px;
				    font-weight: normal;
				    display: inline-block;
    				float: none;
    				vertical-align: middle;
    				padding: 0;
    				margin: 0;
				}
				.divTableRow {
					
				}
				.divTableRow:first-child .divTableCell {
				  border-top: 0;
				  border-right: 0;
				}
				.divTableRow .divTableCell:first-child {
				  border-left: 0;
				  width: 80px;
				  vertical-align: top;
    			  padding-top: 13px;
				}
				.divTableRow .divTableCell:nth-child(2) {
				  border-left: 0;
				  width: 240px;
				  vertical-align: top;
    			  padding-top: 13px;
				}
				.divTableRow .divTableCell:last-child {
					width: auto;
				}
				.divTableRow .divTableCell:last-child {
				  border-right: 0;
				}
				.divTableHeading {
					background-color: #EEE;
					display: table-header-group;
					font-weight: bold;
				}
				.divTableFoot {
					background-color: #EEE;
					display: table-footer-group;
					font-weight: bold;
				}
				.divTableBody {
					display: table-row-group;
				}
				.divTableCell:nth-child(n+3) {
					text-align: center;
					padding-top: 14px;
    				padding-bottom: 14px;
    				height: auto;
				}
				.divTableCell:nth-child(n+3) i {
					font-size: 34px;
				}
				.isLead {
				    position: relative;
				}
				.isLead i.corner {
				    position: absolute;
				    top: 4px;
				    right: 4px;
				    font-size: 16px;
				}

				#project-details-info-container .divTable .no-action,
				#project-details-info-container .divTable .action-needed,
				#project-details-info-container .divTable .action-required, 
				#project-details-info-container .divTable .critical,
				#project-details-info-container .divTable .ok-actionable,
				#project-details-info-container .divTable .in-progress {
					border-top: 2px solid #939598; border-left: 2px solid #939598;
					border-bottom: none; border-right: 0px;
					-webkit-animation: none;
					opacity:1;
				}
				#project-details-info-container .divTable .no-action { color:#939598; }
				#project-details-info-container .divTable .action-needed { color:#76338b; }
				#project-details-info-container .divTable .action-required, 
				#project-details-info-container .divTable .critical { color:#da328a; background-color: rgba(218, 50, 138, 0.1); }
				#project-details-info-container .divTable .ok-actionable { color:#56b285; background-color:rgba(86, 178, 133, 0.2); }
				#project-details-info-container .divTable .in-progress { color:#49ade9; }

				#project-details-info-container .divTableRow:last-child .divTableCell {
				  
					border-bottom: 2px solid #939598;
				}
			</style>