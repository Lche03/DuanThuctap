import { createRouter, createWebHistory } from 'vue-router';
import TaskList from '../components/TaskList.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';

const routes = [
    { path: '/', component: TaskList, meta: { requiresAuth: true } },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else {
        next();
    }
});

export default router;