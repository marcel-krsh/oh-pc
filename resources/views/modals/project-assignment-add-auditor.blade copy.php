<div id="modal-project-assignment-add-auditor" class="uk-padding-remove uk-margin-bottom uk-overflow-auto">
	<h2>Please Add Auditors for the Selected Date: <small class="use-hand-cursor" style="padding-left: 30px; font-size: 0.7em;"  uk-tooltip="title:CLICK TO CHANGE SELECTED DATE;" onclick=""><i class="a-calendar-pencil" style=" font-size: 18px; vertical-align: text-top;"></i> {{$data['summary']['date']}}</small></h2>

	<div id="project-assignment-add-auditor-table">
		<div id="project-assignment-add-auditor-table-header" uk-grid>
			<div class="uk-width-3-5 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-1 uk-padding-remove">
				            </div>
						</div>
					</div>
					<div class="uk-width-5-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
									<div class="uk-width-1-1 uk-padding-remove-left">
										STATS AUDITOR NAME
									</div>
									<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-left uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" uk-tooltip="title:SORT BY NAME;" aria-expanded="false">
										<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-name',1);"></a>
									</span> 
									<div class="uk-dropdown" aria-expanded="false"></div>
								</div>
							</div>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
					            	<div class="uk-width-1-1">
										TIME AVAILABLE THIS DAY
									</div>
									<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY AVAILABILITY;" aria-expanded="false">
										<a id="" class="sort-asc" onclick="loadListTab(1,null,null,'sort-by-availability',1);"></a>
									</span> 
									<div class="uk-dropdown" aria-expanded="false"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uk-width-2-5 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								OPEN
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY OPEN TIME;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-open',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								STARTING
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY START TIME;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-starting',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
					<div class="uk-width-1-2 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								DISTANCE TO PROJECT
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY DISTANCE;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-distance',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="auditorListScroller" class="uk-overflow-auto">
			@foreach($data['auditors'] as $auditor)
			<div class="project-assignment-add-auditor-row @if($auditor['status'] == 'action-required') {{$auditor['status']}} @endif" uk-grid>
				<div class="uk-width-3-5 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-6 uk-padding-remove uk-text-center">
							<div uk-grid>
								<div class="uk-width-1-1 uk-padding-remove {{$auditor['status']}}">
									<i class="{{$auditor['icon']}} large use-hand-cursor" onclick="$('#auditorListScroller').toggleClass('noscroll');" uk-tooltip="title:{{$auditor['icon_tooltip']}};"></i>
									<div class="" uk-drop="mode: click">
									    <div class="uk-card uk-card-body uk-card-rounded">
									        <ul class="uk-list">
					                        	<li onclick=""><i class="a-folder"></i> File Audit Only</li>	
					                        	<li onclick=""><i class="a-mobile-home"></i> Site Visit Only</li>	
					                        	<li onclick=""><i class="a-mobile-home"></i><i class="a-folder"></i> Both</li>	
						                    </ul>
									    </div>
									</div>
					            </div>
							</div>
						</div>
						<div class="uk-width-5-6 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-1-2 uk-padding-remove">
									<div class="leaders uk-width-1-1">
					    				<div>
					    					<span><i class="a-person-chart-bar large use-hand-cursor" style="padding-right: 8px;" onclick="addAssignmentAuditorStats({{$data['project']['id']}}, {{$auditor['id']}});" uk-tooltip="title:CLICK TO VIEW AUDITOR'S SCHEDULE & STATS;"></i> {{$auditor['name']}}</span>
					    				</div>
					    			</div>
								</div>
								<div class="uk-width-1-2">
									{{$auditor['availability']}}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-2-5 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:{{$auditor['open_tooltip']}};">{{$auditor['open']}}</span>
						</div>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:{{$auditor['starting_tooltip']}};">{{$auditor['starting']}}</span>
						</div>
						<div class="uk-width-1-2 uk-text-center">
							{{$auditor['distance_time']}} | {{$auditor['distance']}} Miles | <i class="{{$auditor['distance_icon']}}" uk-tooltip="title:{{$auditor['distance_tooltip']}};"></i>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>

	<div class="project-details-info-assignment-summary uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-1-6 uk-margin-top " style="display:none">
			<canvas id="chartjs-assignment-auditor" class="chartjs" style="display: block;"></canvas>
		</div>
		<div class="uk-width-1-2 uk-margin-top uk-padding-remove" style="display:none">
			<h4>
				<div>It will take an <span class="underlined italic">ESTIMATED</span> <i class="a-pencil-2 use-hand-cursor" uk-tooltip="title:EDIT ESTIMATED HOURS;"></i>{{$data['summary']['estimated']}} to complete this audit.</div>
				<div id="editEstimated" class="" uk-drop="mode: click">
				    <div class="uk-card uk-card-body uk-card-rounded">
				        <ul class="uk-list no-hover uk-form-horizontal ">
	                    	<li onclick="">
						        <label class="uk-form-label">HOURS:</label>
						        <div class="uk-form-controls">
						            <input class="uk-input" type="text" value="{{$data['summary']['estimated']}}">
						        </div>
	                    	</li>	
	                    	<li onclick="">
						        <label class="uk-form-label">MINUTES:</label>
						        <div class="uk-form-controls">
						            <select class="uk-select">
						                <option>00</option>
						                <option>01</option>
						                <option>02</option>
						                <option>03</option>
						                <option>04</option>
						                <option>05</option>
						                <option>06</option>
						                <option>07</option>
						                <option>08</option>
						                <option>09</option>
						                <option>10</option>
						                <option>11</option>
						                <option>12</option>
						                <option>13</option>
						                <option>14</option>
						                <option>15</option>
						                <option>16</option>
						                <option>17</option>
						                <option>18</option>
						                <option>19</option>
						                <option>20</option>
						                <option>21</option>
						                <option>22</option>
						                <option>23</option>
						                <option>24</option>
						                <option>25</option>
						                <option>26</option>
						                <option>27</option>
						                <option>28</option>
						                <option>29</option>
						                <option>30</option>
						                <option>31</option>
						                <option>32</option>
						                <option>33</option>
						                <option>34</option>
						                <option>35</option>
						                <option>36</option>
						                <option>37</option>
						                <option>38</option>
						                <option>39</option>
						                <option>40</option>
						                <option>41</option>
						                <option>42</option>
						                <option>43</option>
						                <option>44</option>
						                <option>45</option>
						                <option>46</option>
						                <option>47</option>
						                <option>48</option>
						                <option>49</option>
						                <option>50</option>
						                <option>51</option>
						                <option>52</option>
						                <option>53</option>
						                <option>54</option>
						                <option>55</option>
						                <option>56</option>
						                <option>57</option>
						                <option>58</option>
						                <option>59</option>
						            </select>
						        </div>
	                    	</li>	
	                    </ul>
				    </div>
				</div>
				<div>{{$data['summary']['needed']}} Need Assigned</div>
				<div id="editEstimated" class="" uk-drop="mode: click">
				    <div class="uk-card uk-card-body uk-card-rounded">
				        <ul class="uk-list no-hover uk-form-horizontal ">
	                    	<li onclick="">
						        <label class="uk-form-label">HOURS:</label>
						        <div class="uk-form-controls">
						            <input class="uk-input" type="text" value="{{$data['summary']['estimated']}}">
						        </div>
	                    	</li>	
	                    	<li onclick="">
						        <label class="uk-form-label">MINUTES:</label>
						        <div class="uk-form-controls">
						            <select class="uk-select">
						                <option>00</option>
						                <option>01</option>
						                <option>02</option>
						                <option>03</option>
						                <option>04</option>
						                <option>05</option>
						                <option>06</option>
						                <option>07</option>
						                <option>08</option>
						                <option>09</option>
						                <option>10</option>
						                <option>11</option>
						                <option>12</option>
						                <option>13</option>
						                <option>14</option>
						                <option>15</option>
						                <option>16</option>
						                <option>17</option>
						                <option>18</option>
						                <option>19</option>
						                <option>20</option>
						                <option>21</option>
						                <option>22</option>
						                <option>23</option>
						                <option>24</option>
						                <option>25</option>
						                <option>26</option>
						                <option>27</option>
						                <option>28</option>
						                <option>29</option>
						                <option>30</option>
						                <option>31</option>
						                <option>32</option>
						                <option>33</option>
						                <option>34</option>
						                <option>35</option>
						                <option>36</option>
						                <option>37</option>
						                <option>38</option>
						                <option>39</option>
						                <option>40</option>
						                <option>41</option>
						                <option>42</option>
						                <option>43</option>
						                <option>44</option>
						                <option>45</option>
						                <option>46</option>
						                <option>47</option>
						                <option>48</option>
						                <option>49</option>
						                <option>50</option>
						                <option>51</option>
						                <option>52</option>
						                <option>53</option>
						                <option>54</option>
						                <option>55</option>
						                <option>56</option>
						                <option>57</option>
						                <option>58</option>
						                <option>59</option>
						            </select>
						        </div>
	                    	</li>	
	                    </ul>
				    </div>
				</div>
			</h4>
			
		</div>
		<div class="uk-width-1-3 uk-padding-remove">
			<button class="uk-button uk-button-border uk-link" onclick="" type="button"><i class="far fa-calendar-check"></i> DONE SCHEDULING</button>
		</div>
	</div>

	<div id="project-details-info-assignment-stats" class="uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-2-3" uk-padding-remove>
			<div class="uk-card uk-card-info uk-card-body">
				<div class="uk-grid-small uk-flex-top" uk-grid>
		            <div class="uk-width-1-6">
		                <i class="a-info-circle"></i>
		            </div>
		            <div class="uk-width-5-6">
			            <p>Clicking the <i class="a-circle-plus"></i> icon will add the auditor to your audit and automatically assign either all their open hours, or the number of hours needed (whichever is less) to your audit.</p> 
			            <p>Pink/Grayed out lines indicate auditors who have assignments that same day, and will require approval by the lead of the other assignments if selected.</p> 
			            <p>"Time Available This Day" is the time period the auditor stated they are available to be scheduled for audits on the selected day.</p>
			            <p>"Open" is the number of hours that the auditor has left on the selected day that can be scheduled for this audit.</p> 
			            <p>"Starting" is the approximate time they would be available to start their travel to this audit.</p>
			            <p>"Distance to Project" shows the time, miles, and an icon designating if they will be traveling from their default starting point (<i class="a-home-marker"></i>) or from another audit(<i class="a-marker-basic"></i>).</p>
		            </div>
		        </div>
	        </div>
		</div>
	</div>
</div>
<script>
	var chartColors = {
		  estimated: '#0099d5',
		  needed: '#d31373'
		};
	Chart.defaults.global.legend.display = false;
    Chart.defaults.global.tooltips.enabled = true;

    // THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE  - to make this simple - this tab is reloaded on form submission of new payment/ payment edits //
    var assignmentOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke : false,
        legendPosition : 'bottom',

        rotation: (1.5 * Math.PI),

        "cutoutPercentage":70,
			"legend" : {
				"display" : false
			},
			"responsive" : true,
			"maintainAspectRatio" : false,

        //String - The colour of each segment stroke
        segmentStrokeColor : "#fff",

        //Number - The width of each segment stroke
        segmentStrokeWidth : 0,

        //The percentage of the chart that we cut out of the middle.
        // cutoutPercentage : 67,

        easing: "linear",

        duration: 100000,

        tooltips: {
            enabled: true,
            mode: 'single',
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index];
                    var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    return label + ': ' + addCommas(datasetLabel) + ':00' ;
                }
            }
        }


    }
    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    var mainAssignmentChart = new Chart(document.getElementById("chartjs-assignment-auditor"),{
		"type":"doughnut",
		"options": assignmentOptions,
		
		"data":{
			"labels": ["Needed","Estimated"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[27,107],
					"backgroundColor":[
						chartColors.needed,
						chartColors.estimated
					],
					"borderWidth": 1
				}
			]
		}
	});

	function addAssignmentAuditorStats(projectid, auditorid){
		$('#project-details-info-assignment-stats').html("updating...");

		var tempdiv = '<div style="height:100px;text-align:center;width:100%;"><div uk-spinner style="margin: 20px 0;"></div></div>';
		$('#project-details-info-assignment-stats').html(tempdiv);

		var url = 'projects/'+projectid+'/assignments/addauditor/'+auditorid+'/stats';
	    $.get(url, {
	        }, function(data) {
	            if(data=='0'){ 
	                UIkit.modal.alert("There was a problem getting the assignment information.");
	            } else {
	            	
					$('#project-details-info-assignment-stats').html(data);
	        	}
	    });
	}
</script>