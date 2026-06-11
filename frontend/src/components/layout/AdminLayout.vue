<template>
  <div class="admin-layout">
    <!-- Backdrop for mobile sidebar -->
    <div 
      v-if="isSidebarOpen" 
      class="sidebar-backdrop" 
      @click="isSidebarOpen = false"
    ></div>

    <Sidebar 
      :is-open="isSidebarOpen" 
      @close="isSidebarOpen = false" 
    />

    <div class="main-wrapper">
      <Navbar @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />
      
      <main class="content-area">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import { useAuthStore } from '../../stores/auth';

const isSidebarOpen = ref(false);
const authStore = useAuthStore();

onMounted(() => {
  if (!authStore.user) {
    authStore.fetchUser();
  }
});
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f8fafc;
}

.main-wrapper {
  flex: 1;
  margin-left: 260px; /* Width of the sidebar */
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: margin-left 0.3s ease-in-out;
}

.content-area {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

.sidebar-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 35;
}

@media (max-width: 768px) {
  .main-wrapper {
    margin-left: 0;
  }
  .sidebar-backdrop {
    display: block;
  }
  .content-area {
    padding: 1.5rem;
  }
}
</style>
