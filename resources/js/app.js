import Echo from "laravel-echo"

window.Pusher = require('pusher-js');

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '1234',
  wsHost: window.location.hostname,
  wsPort: 6001,
  disableStats: true,
});

// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue'));
Vue.component('auditrow', require('./components/AuditRow.vue'), {
    name: 'auditrow'
});
Vue.component('communication-row', require('./components/CommunicationRow.vue'));

// connect sockets
var socket = io('https://pcinspectdev.ohiohome.org:6001');
 //var socket = io('http://192.168.100.100:3000');

// https://github.com/ElemeFE/vue-infinite-scroll
var infiniteScroll =  require('vue-infinite-scroll');
Vue.use(infiniteScroll);

// each page will be its own main Vue instance

