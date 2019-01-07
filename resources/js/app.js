
// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

// load all components
Vue.component('example', require('./components/Example.vue').default);
Vue.component('auditrow', require('./components/AuditRow.vue').default, {
    name: 'auditrow'
});
Vue.component('communication-row', require('./components/CommunicationRow.vue').default);
Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

// https://github.com/ElemeFE/vue-infinite-scroll
var infiniteScroll =  require('vue-infinite-scroll');
Vue.use(infiniteScroll);

// each page will be its own main Vue instance

