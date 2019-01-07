
// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

// import Echo from "laravel-echo"

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//   broadcaster: 'pusher',
//   key: '6e69117f494c249535b6',
//   cluster: 'us2',
//   // wsHost: window.location.hostname,
//   // wsPort: 443,
//   disableStats: true,
// });

// load all components
Vue.component('example', require('./components/Example.vue').default);
Vue.component('auditrow', require('./components/AuditRow.vue').default, {
    name: 'auditrow'
});
Vue.component('communication-row', require('./components/CommunicationRow.vue').default);
Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

const app = new Vue({
    el: '#app',

    data: {
        messages: []
    },

    created() {

        this.fetchMessages();

        Echo.join('chat');
        Echo.channel('chat')
    		  .listen('MessageSent', (e) => {
    		    this.messages.push({
    		      message: e.message.message,
    		      user: e.user
    		    });
            console.log("receiving message");
            console.log(e.user);
    		  });
    },

    methods: {
        fetchMessages() {
            axios.get('/chat/messages').then(response => {
                this.messages = response.data;
            });
        },

        addMessage(message) {
            this.messages.push(message);

            axios.post('/chat/messages', message).then(response => {
              console.log(response.data);
            });
        }
    }
});
// connect sockets
// var socket = io('192.168.10.10:6001');
 //var socket = io('http://192.168.100.100:3000');

// https://github.com/ElemeFE/vue-infinite-scroll
var infiniteScroll =  require('vue-infinite-scroll');
Vue.use(infiniteScroll);

// each page will be its own main Vue instance

