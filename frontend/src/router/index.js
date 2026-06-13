import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        component: () => import('../components/layout/MarketplaceLayout.vue'),
        children: [
            {
                path: '',
                name: 'Marketplace',
                component: () => import('../views/marketplace/MarketplaceView.vue')
            },
            {
                path: 'product/:id',
                name: 'ProductDetail',
                component: () => import('../views/marketplace/ProductDetailView.vue')
            },
            {
                path: 'profile',
                name: 'UserProfile',
                component: () => import('../views/marketplace/UserProfileView.vue'),
                meta: { requiresAuth: true }
            }
        ]
    },
    {
        path: '/admin',
        component: () => import('../components/layout/AdminLayout.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard'
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
        path: '/admin/login',
        name: 'AdminLogin',
        component: () => import('../views/auth/LoginView.vue'),
        meta: { guest: true }
    },

    {
        path: '/login',
        name: 'UserLogin',
        component: () => import('../views/auth/UserLoginView.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'UserRegister',
        component: () => import('../views/auth/UserRegisterView.vue'),
        meta: { guest: true }
    },
    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: () => import('../views/auth/ForgotPasswordView.vue'),
        meta: { guest: true }
    }
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    // Ensure we have user data if authenticated but user object is missing
    if (authStore.isAuthenticated && !authStore.user) {
        await authStore.fetchUser();
    }
    
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!authStore.isAuthenticated) {
            // Redirect to appropriate login page based on route
            if (to.path.startsWith('/admin')) {
                next('/admin/login');
            } else {
                next('/login');
            }
        } else if (to.matched.some(record => record.meta.requiresAdmin) && authStore.user?.role !== 'admin') {
            next('/');
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.guest) && authStore.isAuthenticated) {
        if (authStore.user?.role === 'admin') {
            next('/admin/dashboard');
        } else {
            next('/');
        }
    } else {
        next();
    }
});

export default router;
