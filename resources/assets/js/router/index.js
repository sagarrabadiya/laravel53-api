import Vue from 'vue';

/**
 * install validator
 */
import VueValidator from 'vue-validator';
Vue.use(VueValidator);

import VueRouter from 'vue-router';


Vue.use(VueRouter);

var router = new VueRouter({
	hashbang: false,
	history: true
});

router.map(require('./routes'));

router.redirect({
	'/' : '/login'
});

export default router;