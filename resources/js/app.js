
// good one from Helder Lucas (bottom of page):
// https://stackoverflow.com/questions/41539961/vuejs-js-for-multiple-pages-not-for-a-single-page-application

// insert JS dependencies
require('./bootstrap');

window.Vue = require('vue');
import Vue from 'vue';
//import VueChatScroll from 'vue-chat-scroll';
//Vue.use(VueChatScroll);

/// for notifications
import Toaster from 'v-toaster'
import 'v-toaster/dist/v-toaster.css'
Vue.use(Toaster, {timeout: 5000})

//chat
//Vue.component('message', require('./components/message.vue').default);


// load all components
//Vue.component('example', require('./components/Example.vue').default);
Vue.component('auditrow', require('./components/AuditRow.vue').default, {
    name: 'auditrow'
});
Vue.component('communication-row', require('./components/CommunicationRow.vue').default);
// Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
// Vue.component('chat-form', require('./components/ChatForm.vue').default);
Vue.component('address-row', require('./components/AuditorAddress.vue').default);

// https://github.com/ElemeFE/vue-infinite-scroll
var infiniteScroll =  require('vue-infinite-scroll');
Vue.use(infiniteScroll);

// each page will be its own main Vue instance

