/**
 * Created by sagar on 20/08/16.
 */
import Login from './../pages/Login.vue';
import Layout from "./../pages/Layout.vue";
import Projects from "./../pages/Projects.vue";

module.exports = {
    '/login': {component: Login},
    '/projects': {
        component: Layout,
        subRoutes: {
            '/' : {
                name: 'projects.all',
                component: Projects
            },
            '/:id' : {
                name: 'project.dashboard',
                component: (resolve) => require(['./../pages/Dashboard.vue'], resolve)
            },
            '/:id/milestones': {
                name: 'project.milestones',
                component: (resolve) => require(['./../pages/milestones/Milestones.vue'], resolve)
            }
        }
    }
};