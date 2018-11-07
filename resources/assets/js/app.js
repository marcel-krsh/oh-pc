
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