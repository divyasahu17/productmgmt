<template>
  <header class="navbar">
    <div class="nav-left">
      <button class="menu-toggle" @click="$emit('toggleSidebar')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </button>
    </div>
    <div class="nav-right">
      <span class="user-greeting" v-if="authStore.user">
        Hi, {{ authStore.user.name }}
      </span>
      <button @click="handleLogout" class="logout-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
        Logout
      </button>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

defineEmits(['toggleSidebar']);

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<style scoped>
.navbar {
  height: 64px;
  background-color: #ffffff;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1.5rem;
  position: sticky;
  top: 0;
  z-index: 30;
}

.menu-toggle {
  display: none;
  background: transparent;
  border: none;
  color: #475569;
  cursor: pointer;
  padding: 0.5rem;
}

.menu-toggle svg {
  width: 24px;
  height: 24px;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-left: auto;
}

.user-greeting {
  font-weight: 500;
  color: #334155;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: transparent;
  color: #ef4444;
  border: 1px solid #fca5a5;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}

.logout-btn:hover {
  background-color: #fef2f2;
}

.logout-btn .icon {
  width: 18px;
  height: 18px;
}

@media (max-width: 768px) {
  .menu-toggle {
    display: block;
  }
}
</style>
