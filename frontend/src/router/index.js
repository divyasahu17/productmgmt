import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        component: () => import('../components/layout/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/dashboard'
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: () => import('../views/DashboardView.vue'),
            },
            {
                path: 'products',
                name: 'Products',
                component: () => import('../views/ProductsView.vue'),
            },
            {
                path: 'categories',
                name: 'Categories',
                component: () => import('../views/CategoriesView.vue'),
            },
            {
                path: 'notifications',
                name: 'Notifications',
                component: () => import('../views/NotificationsView.vue'),
            },
            {
                path: 'profile',
                name: 'Profile',
                component: () => import('../views/ProfileView.vue'),
            }
        ]
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/auth/LoginView.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/auth/RegisterView.vue'),
        meta: { guest: true }
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    if (to.matched.some(record => record.meta.requiresAuth) && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.matched.some(record => record.meta.guest) && authStore.isAuthenticated) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;
