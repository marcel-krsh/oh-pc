// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue'));
Vue.component('communication-row', require('./components/CommunicationRow.vue'));
Vue.component('audit-row', require('./components/AuditRow.vue'), {
    name: 'audit-row'
});

// connect sockets
var socket = io('http://192.168.100.100:3000');

// https://github.com/ElemeFE/vue-infinite-scroll
var infiniteScroll =  require('vue-infinite-scroll');
Vue.use(infiniteScroll);

// each page will be its own main Vue instance