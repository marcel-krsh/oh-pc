
// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue'));

// connect sockets
var socket = io('http://192.168.100.100:3000');

new Vue({
    el: '#app',

    data: {
        users: [ 'JohnDoe' ]
    },

    mounted: function() {
        socket.on('test-channel:UserSignedUp', function(data){
            console.log("socket message received");
            this.users.push(data.username);
        }.bind(this));
    }
});

new Vue({
  el: '#v-tab-com-stat',
  data: {
    stat: statsCommunicationTotal
  },

    mounted: function() {
        socket.on('communications:NewMessage', function(data){
            console.log(data.stats_communication_total);
            this.stat = data.stats_communication_total;
        }.bind(this));
    }
});