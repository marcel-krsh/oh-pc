
// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue'));

// connect sockets
var socket = io('http://192.168.100.100:3000');

new Vue({
  el: '#v-tab-com-stat',
  data: {
    stat: statsCommunicationTotal
  },

    mounted: function() {
        socket.on('communications.'+uid+'.'+sid+':NewRecipient', function(data){
            console.log("user " + data.userId + " is getting a message because a new message has been sent.");
            this.stat = data.stat;
        }.bind(this));
    }
});