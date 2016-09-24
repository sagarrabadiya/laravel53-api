
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

import Vue from 'vue';
import { sync } from 'vuex-router-sync';
import router from './router';
import store from './vuex/store';
sync(store, router);

require("./components/globals");
require("./directives/globals");
require("./filters/globals");

const App = Vue.extend({
	store
});

router.start(App, 'body');

export default Vue;