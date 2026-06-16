<template>
  <div class="marketplace-layout">
    <!-- Navbar -->
    <header class="marketplace-navbar" :class="{ 'scrolled': isScrolled }">
      <div class="nav-container">
        <router-link to="/" class="brand-logo">
          ProductMgmt
        </router-link>
        <nav class="main-nav">
          <router-link to="/" class="nav-link">Home</router-link>
          <a @click="scrollToSection('categories')" class="nav-link" style="cursor: pointer;">Categories</a>
          <a @click="scrollToSection('featured')" class="nav-link" style="cursor: pointer;">Featured</a>
        </nav>
        <div class="nav-actions">
          <button v-if="authStore.isAuthenticated" @click="cartStore.toggleSidebar" class="cart-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
            <span v-if="cartStore.totalItems > 0" class="cart-badge">{{ cartStore.totalItems }}</span>
          </button>
          <router-link v-if="!authStore.isAuthenticated" to="/login" class="login-btn">Login</router-link>
          <template v-else>
            <router-link to="/profile" class="user-greeting">Welcome, <span class="user-name">{{ authStore.user?.name || 'User' }}</span></router-link>
            <router-link v-if="authStore.user?.role === 'admin'" to="/admin/dashboard" class="dashboard-btn">Dashboard</router-link>
            <button @click="handleLogout" class="logout-btn">Logout</button>
          </template>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="marketplace-content">
      <router-view></router-view>
    </main>

    <!-- Footer -->
    <footer class="marketplace-footer">
      <div class="footer-container">
        <div class="footer-brand">
          <h3>ProductMgmt</h3>
          <p>Your one-stop premium marketplace for everything you need.</p>
        </div>
        <div class="footer-links">
          <h4>Quick Links</h4>
          <router-link to="/">Home</router-link>
          <router-link to="/admin/login">Admin Login</router-link>
        </div>
        <div class="footer-social">
          <h4>Follow Us</h4>
          <div class="social-icons">
            <a href="#">FB</a>
            <a href="#">TW</a>
            <a href="#">IG</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; {{ new Date().getFullYear() }} ProductMgmt. All rights reserved.
      </div>
    </footer>
    
    <CartSidebar />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useCartStore } from '../../stores/cart';
import { useToastStore } from '../../stores/toast';
import { useRouter } from 'vue-router';
import CartSidebar from '../cart/CartSidebar.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();
const toastStore = useToastStore();
const router = useRouter();
const isScrolled = ref(false);

const handleLogout = async () => {
  await authStore.logout();
  toastStore.notify('Logged out successfully!');
  router.push('/login');
};

const scrollToSection = async (sectionId) => {
  if (router.currentRoute.value.path !== '/') {
    await router.push('/');
    setTimeout(() => {
      document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
    }, 100);
  } else {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: 'smooth' });
  }
};

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.marketplace-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f8fafc;
  font-family: 'Inter', sans-serif;
}

.marketplace-navbar {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 100;
  transition: all 0.3s ease;
  padding: 1.5rem 0;
  background: transparent;
}

.marketplace-navbar.scrolled {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  padding: 1rem 0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-logo {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
  text-decoration: none;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.main-nav {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: #334155;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.nav-link:hover {
  color: #4f46e5;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.cart-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #334155;
  transition: color 0.2s;
  padding: 0.5rem;
}

.cart-btn:hover {
  color: #4f46e5;
}

.cart-btn svg {
  width: 24px;
  height: 24px;
}

.cart-badge {
  position: absolute;
  top: 0;
  right: 0;
  background: #ec4899;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 9999px;
  padding: 0.1rem 0.4rem;
  transform: translate(25%, -25%);
}

.login-btn, .dashboard-btn {
  padding: 0.5rem 1.5rem;
  border-radius: 9999px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.login-btn {
  color: #4f46e5;
  background: white;
  border: 1px solid #e0e7ff;
}

.login-btn:hover {
  background: #f8fafc;
  border-color: #c7d2fe;
}

.dashboard-btn {
  color: white;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);
}

.dashboard-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.user-greeting {
  display: flex;
  align-items: center;
  color: #64748b;
  font-size: 0.95rem;
  margin-right: 0.5rem;
  text-decoration: none;
  transition: opacity 0.2s;
}

.user-greeting:hover {
  opacity: 0.8;
}

.user-greeting .user-name {
  font-weight: 700;
  color: #0f172a;
  margin-left: 0.25rem;
}

.logout-btn {
  padding: 0.5rem 1.25rem;
  border-radius: 9999px;
  background: #fee2e2;
  color: #ef4444;
  border: none;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: #fecaca;
  transform: translateY(-2px);
}

.marketplace-content {
  flex: 1;
}

.marketplace-footer {
  background-color: #0f172a;
  color: #f8fafc;
  padding: 4rem 2rem 2rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 3rem;
  margin-bottom: 3rem;
}

.footer-brand p {
  color: #94a3b8;
  margin-top: 1rem;
  line-height: 1.6;
}

.footer-links, .footer-social {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.footer-links h4, .footer-social h4 {
  color: white;
  font-weight: 600;
}

.footer-links a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #4f46e5;
}

.social-icons {
  display: flex;
  gap: 1rem;
}

.social-icons a {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #1e293b;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: background 0.2s;
}

.social-icons a:hover {
  background: #4f46e5;
}

.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid #1e293b;
  color: #64748b;
  font-size: 0.9rem;
}
</style>
