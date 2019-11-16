Chart.defaults.global.legend.display = false;
Chart.defaults.global.tooltips.enabled = false;


// THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE //
		    var allocationsOptions = {
				//Boolean - Whether we should show a stroke on each segment
				segmentShowStroke : false,
				
				//String - The colour of each segment stroke
				segmentStrokeColor : "#fff",
				
				//Number - The width of each segment stroke
				segmentStrokeWidth : 0,
				
				//The percentage of the chart that we cut out of the middle.
				percentageInnerCutout : .9,
				
				//Boolean - Whether we should animate the chart	
				animation : false,
				
				//Number - Amount of animation steps
				animationSteps : 100,
				
				//String - Animation easing effect
				animationEasing : "easeOutBounce",
				
				//Boolean - Whether we animate the rotation of the Doughnut
				animateRotate : false,
			
				//Boolean - Whether we animate scaling the Doughnut from the centre
				animateScale : false,
				
				//Function - Will fire on animation completion.
				onAnimationComplete : null
			}
			
			
			// Allocations Chart Data
			/*var allocationsData = [
				{
					//MPA PENDING
					value: 12500,
					color:"rgba(122,200,162,1)"
				},
				{
					// MPA Reserved - amount remaining that has not been allocated
					value : 500,
					color : "rgba(53,182,69,1)"
				},
				{
					// MPA Paid
					value : 12000,
					color : "rgba(254,194,140,1)"
				},
				{
					// RPA Pending
					value : 3000,
					color : "rgba(236,0,140,1)"
				},
				{
					// RPA Reserved
					value : 500.50,
					color : "rgba(37,64,143,1)"
				},
				{
					// RPA PAID
					value : 3000.15,
					color : "rgba(109,207,246,1)"
				},
				{
					// Amount unused  - we do not supply a lable for this - this shows a white "gap" in the chart to signafy how much they have not used of their maximum.
					// Adding up all these numbers should total $35,000.
					value : 3499.35,
					color : "#fff"
				}
			
			]
			*/
			var data = {
				    datasets: [{
				        data: [
				            12500,
				            500,
				            12000,
				            3449.35,
				            3000,
				            500.50,
				            3000.15
				            
				        ],
				        backgroundColor: [
				            "rgba(122,200,162,1)",
				            "rgba(53,182,69,1)",
				            "rgba(254,194,140,1)",
				            "#fff",
				            "rgba(236,0,140,1)",
				            "rgba(37,64,143,1)",
				            "rgba(109,207,246,1)"
				            
				        ],
				        label: 'My dataset' // for legend
				    }],
				    labels: [
				        "MPA Pending",
				        "MPA Reserve Remaining",
				        "MPA Paid",
				         "",
				        "RPA Pending",
				        "RPA Reserve Remaining",
				        "RPA Paid"
				       
				    ]
				};
			
			//Get the context of the Doughnut Chart canvas element we want to select
			var ctx = document.getElementById("allocationsChart").getContext("2d");
			
			// Create the Doughnut Chart
			var myDoughnutChart = new Chart(ctx, {
				    type: 'doughnut',
				    data: data,
				    options: allocationsOptions
				});    
	