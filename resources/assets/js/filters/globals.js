/**
 * Created by sagar on 30/08/16.
 */


import Vue from 'vue';
import userFilter from "./user";
import dateFilter from "./date";

Vue.filter('username', userFilter);
Vue.filter('date', dateFilter);
