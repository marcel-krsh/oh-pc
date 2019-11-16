
// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

// window.Vue = require('vue');
// import Vue from 'vue'

// // for notifications
// import Toaster from 'v-toaster'
// import 'v-toaster/dist/v-toaster.css'
// Vue.use(Toaster, {timeout: 5000})

// //chat
// //Vue.component('message', require('./components/message.vue').default);


// // load all components
// //Vue.component('example', require('./components/Example.vue').default);
// Vue.component('auditrow', require('./components/AuditRow.vue').default, {
//     name: 'auditrow'
// });
// // Vue.component('communication-row', require('./components/CommunicationRow.vue').default);
// // Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
// // Vue.component('chat-form', require('./components/ChatForm.vue').default);
// Vue.component('address-row', require('./components/AuditorAddress.vue').default);

// // https://github.com/ElemeFE/vue-infinite-scroll
// var infiniteScroll =  require('vue-infinite-scroll');
// Vue.use(infiniteScroll);

// each page will be its own main Vue instance

// Reports watch
$(function(){

	function checkForReportUpdates(){
	    		// ensure the tab is active to run updates on it.
               if($('#detail-tab-3').hasClass('uk-active')){
                    //console.log("Checking for updated reports. "+$('#report-checking').val());
                    window.reportNewest =  $('#crr-newest').val();
                    $('#crr-newest').val('2200-01-01 12:12:12');
                    console.log('newest to check is now set to '+window.reportNewest);
                    $.get('/dashboard/reports', {
                                'check' : 1,
                                'page' : $('#reports-current-page').val(),
                                'newer_than' : window.reportNewest
                                }, function(data) {
                                    if(data==1){
                                        console.log('No Report Updates - newest record is '+window.reportNewest);
                                        $('#crr-newest').val(window.reportNewest);
                                        window.lastRequestCompleted = true;
										console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);

                                    } else {
                                        //UIkit.modal.alert('updated'+data1);
                                        console.log('There is a newer report than the previous time:'+window.reportNewest);
										var data = JSON.parse(data);
										data.data.forEach( function(report){
                                          	$('#crr-report-row-'+report.id).slideUp().remove();
                                          	$('#report-'+report.id+'-history').remove();
                                          });
                                        //get the views to add to the top:
                                        $.get('/dashboard/reports', {
			                                  'rows_only' : 1,
			                                  'page' : $('#reports-current-page').val(),
			                                 'newer_than' : window.reportNewest
			                                 }, function(data2) {
			                                 	console.log('Updating Reports');
			                              		$('#crr-report-list').prepend(data2);


			                            });

	                                	console.log('Updating Time Stamp to '+data.data[0].updated_at);
	                                	$('#crr-newest').val(data.data[0].updated_at);
	                                	window.lastRequestCompleted = true;
										console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);
                                    }


					});

                } else {
                	//console.log('Check not run - Current tab is Report tab:'+$('#detail-tab-3').hasClass('uk-active')+' crr-newest = "'+$('#crr-newest').val()+'"');
                	window.lastRequestCompleted = true;
					//console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);

                }


            }

	window.lastRequestCompleted = false;
 //    function runReportRequest(){

	// 			setTimeout(function(){
	// 				window.lastRequestCompleted = true;
	// 				console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);
	// 			},10000);
	// }


	setInterval(function(){
					if(window.lastRequestCompleted){
						window.lastRequestCompleted = false;
						checkForReportUpdates();
					}
					//console.log('Checked lastRequestCompleted is ' + window.lastRequestCompleted);
			},10000);

	//checkForReportUpdates();
});


// Project Reports watch
$(function(){

	function checkForProjectReportUpdates(){
	    		// ensure the tab is active to run updates on it.
               if($('#project-detail-tab-6').hasClass('uk-active')){
                    //console.log("Checking for updated reports. "+$('#report-checking').val());
                    window.projectReportNewest =  $('#project-crr-newest').val();
                    $('#project-crr-newest').val('2200-01-01 12:12:12');
                    console.log('newest to check is now set to '+window.projectReportNewest+'. Loading page '+$('#project-reports-current-page').val());
                    $.get('/projects/'+window.currentProjectOpen+'/reports', {
                                'check' : 1,
                                'page' : $('#project-reports-current-page').val(),
                                'newer_than' : window.projectReportNewest
                                }, function(data) {
                                    if(data==1){
                                        console.log('No Report Updates - newest record is '+window.projectReportNewest);
                                        $('#project-crr-newest').val(window.projectReportNewest);
                                        window.projectReportLastRequestCompleted = true;
										console.log('Set lastRequestCompleted to :'+window.projectReportLastRequestCompleted);

                                    } else {
                                        //UIkit.modal.alert('updated'+data1);
                                        console.log('There is a newer report than the previous time:'+window.projectReportNewest);
										var data = JSON.parse(data);
										data.data.forEach( function(report){
                                          	$('#crr-project-report-row-'+report.id).slideUp().remove();
                                          	$('#project-report-'+report.id+'-history').remove();
                                          });
                                        //get the views to add to the top:
                                        $.get('/projects/'+window.currentProjectOpen+'/reports', {
			                                  'rows_only' : 1,
			                                  'page' : $('#project-reports-current-page').val(),
			                                 'newer_than' : window.projectReportNewest
			                                 }, function(data2) {
			                                 	console.log('Updating Reports');
			                              		$('#crr-project-report-list').prepend(data2);


			                            });

	                                	console.log('Updating Time Stamp to '+data.data[0].updated_at);
	                                	$('#project-crr-newest').val(data.data[0].updated_at);
	                                	window.projectReportLastRequestCompleted = true;
										console.log('Set lastRequestCompleted to :'+window.projectReportLastRequestCompleted);
                                    }


					});

                } else {
                	//console.log('Check not run - Current tab is Report tab:'+$('#detail-tab-3').hasClass('uk-active')+' crr-newest = "'+$('#crr-newest').val()+'"');
                	window.projectReportLastRequestCompleted = true;
					//console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);

                }


            }

	window.projectReportLastRequestCompleted = false;
 //    function runReportRequest(){

	// 			setTimeout(function(){
	// 				window.lastRequestCompleted = true;
	// 				console.log('Set lastRequestCompleted to :'+window.lastRequestCompleted);
	// 			},10000);
	// }


	setInterval(function(){
					if(window.projectReportLastRequestCompleted){
						window.projectReportLastRequestCompleted = false;
						checkForProjectReportUpdates();
					}
					//console.log('Checked lastRequestCompleted is ' + window.lastRequestCompleted);
			},10000);

	checkForProjectReportUpdates();
});


	// Set value for script to not run
	// Run script with a timeout of 5 seconds - then set run value to run.
	// Set an interval that checks to see if variable is run.
	// If it is set to run - set it to false





