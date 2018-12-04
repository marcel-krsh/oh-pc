// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application


// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue'));
Vue.component('communication-row', require('./components/CommunicationRow.vue'));

// connect sockets
var socket = io('http://192.168.100.100:3000');

// each page will be its own main Vue instance
// 
// 

// new Vue({
//   el: '#v-tab-com-stat',
//   data: {
//     stat: statsCommunicationTotal
//   },

//     mounted: function() {
//         socket.on('communications.'+uid+'.'+sid+':NewRecipient', function(data){
//             console.log("user " + data.userId + " is getting a message because a new message has been sent.");
//             this.stat = data.stat;
//         }.bind(this));
//     }
// });

// new Vue({
//     el: '#pcapp',
//     components: {
//         Example,
//         CommunicationRow
//       },
//     mounted: function() {
//         console.log("initializing vue at the pcapp element");
//     }
// });

// var newSingleMessage = new Vue({
//     el: '#communication-row-updates',
//     data: {
//         messages: [
//             {
//                 DestinationNumber: '',
//                 TextDecoded: ''
//             }
//         ],
//         submitted:false
//     },
//     methods: {
//         addNewMessage: function(){
//             this.messages.push({
//                 DestinationNumber: '',
//                 TextDecoded: 'You'
//             });
//         },
//         submitForm: function(e) {
//             console.log(this.messages);
//             this.$http.post('api/outbox', {messages:this.messages})
//             .then(function(response){
//                     //handle success
//                     console.log(response);
//             }).error(function(response){
//                     //handle error
//                     console.log(response)
//             });
//             this.messages = [{ DestinationNumber: '', TextDecoded: '' }];
//             this.submitted = true;
//         }
//     },

//     mounted: function() {
//         socket.on('communications.'+uid+'.'+sid+':NewMessage', function(data){
//             console.log("user " + data.userId + " received a new message.");
//             this.messages.push({
//                 DestinationNumber: '',
//                 TextDecoded: data.summary
//             });
//         }.bind(this));
//     }
//     });