<template>
  <Toast />
  <router-view></router-view>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';
import Toast from './components/Toast.vue';

const authStore = useAuthStore();

onMounted(() => {
  // Listen for the global unauthorized event from Axios interceptor
  window.addEventListener('auth:unauthorized', () => {
    authStore.clearAuth();
    window.location.href = '/admin/login';
  });
  
  if (authStore.isAuthenticated && !authStore.user) {
    authStore.fetchUser();
  }
});
</script>

<style>
/* Global reset and typography */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  color: #333;
  line-height: 1.6;
}
a {
  text-decoration: none;
  color: #2196F3;
}
a:hover {
  text-decoration: underline;
}

/* Global Spinner for Buttons */
.spinner {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
  margin-right: 0.5rem;
  vertical-align: middle;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.submit-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
</style>
