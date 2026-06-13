<template>
  <router-view></router-view>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

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
</style>
