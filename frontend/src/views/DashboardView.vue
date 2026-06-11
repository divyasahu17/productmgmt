<template>
  <div class="dashboard-container">
    <nav class="navbar">
      <div class="brand">ProductMgmt</div>
      <div class="nav-links">
        <span v-if="authStore.user">Welcome, {{ authStore.user.name }}!</span>
        <button @click="handleLogout" class="logout-btn">Logout</button>
      </div>
    </nav>
    <main class="content">
      <h1>Dashboard</h1>
      <p>You are successfully authenticated and viewing a protected route.</p>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

onMounted(() => {
    if (!authStore.user) {
        authStore.fetchUser();
    }
});
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background-color: #f0f2f5;
}
.navbar {
  background-color: #fff;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.brand {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.logout-btn {
  background-color: #f44336;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
.content {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}
</style>
