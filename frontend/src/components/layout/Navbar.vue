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
      <!-- Notification Bell -->
      <div class="notification-menu">
        <button class="icon-btn notification-btn" @click="toggleNotifications">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
          </svg>
          <span class="badge" v-if="notificationStore.unreadCount > 0">{{ notificationStore.unreadCount }}</span>
        </button>

        <div class="dropdown-menu notif-dropdown" v-show="isNotifOpen">
          <div class="notif-header">
            <h4>Notifications</h4>
            <div style="display: flex; gap: 10px; align-items: center;">
              <button class="text-btn" @click="markAllRead" v-if="notificationStore.unreadCount > 0">Mark all as read</button>
              <button class="close-notif-btn" @click="isNotifOpen = false" title="Close Dropdown" style="padding: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
          </div>
          <div class="notif-body" v-if="unreadNotifications.length > 0">
            <div 
              v-for="notif in unreadNotifications" 
              :key="notif.id" 
              class="notif-item unread"
              @click="goToNotifications"
              style="cursor: pointer;"
            >
              <div class="notif-icon">⚠️</div>
              <div class="notif-content">
                <p>{{ notif.data.message }}</p>
                <span class="notif-time">{{ new Date(notif.created_at).toLocaleDateString() }}</span>
              </div>
              <button class="close-notif-btn" @click.stop="markAsRead(notif.id)" title="Mark as read">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
          </div>
          <div class="notif-body empty-notif" v-else>
            No notifications yet.
          </div>
        </div>
      </div>

      <!-- User Dropdown -->
      <div class="user-menu" @click="isDropdownOpen = !isDropdownOpen" v-if="authStore.user">
        <div class="user-info">
          <div class="avatar">{{ authStore.user.name.charAt(0).toUpperCase() }}</div>
          <span class="user-name">{{ authStore.user.name }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="chevron" :class="{'rotate': isDropdownOpen}">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </div>
        
        <div class="dropdown-menu" v-show="isDropdownOpen">
          <router-link to="/admin/profile" class="dropdown-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            My Profile
          </router-link>
          <div class="divider"></div>
          <button @click="handleLogout" class="dropdown-item text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useNotificationStore } from '../../stores/notification';
import { useToastStore } from '../../stores/toast';
import { useRouter } from 'vue-router';

defineEmits(['toggleSidebar']);

const authStore = useAuthStore();
const notificationStore = useNotificationStore();
const toastStore = useToastStore();
const router = useRouter();

const isDropdownOpen = ref(false);
const isNotifOpen = ref(false);

const unreadNotifications = computed(() => {
  return notificationStore.notifications.filter(n => !n.read_at);
});

onMounted(() => {
  if (authStore.user) {
    notificationStore.fetchNotifications();
    // Poll for notifications every 30 seconds
    setInterval(() => {
      notificationStore.fetchNotifications();
    }, 30000);
  }
});

const toggleNotifications = () => {
  isNotifOpen.value = !isNotifOpen.value;
  if (isNotifOpen.value) {
    isDropdownOpen.value = false;
  }
};

const goToNotifications = () => {
  isNotifOpen.value = false;
  router.push('/admin/notifications');
};

const markAsRead = async (id) => {
  await notificationStore.markAsRead(id);
};

const markAllRead = async () => {
  await notificationStore.markAllAsRead();
};

const handleLogout = async () => {
  await authStore.logout();
  toastStore.notify('Admin logged out successfully!');
  router.push('/admin/login');
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

/* Notification Bell */
.icon-btn {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  border-radius: 50%;
  transition: background-color 0.2s, color 0.2s;
}

.icon-btn:hover {
  background-color: #f1f5f9;
  color: #0f172a;
}

.icon-btn svg {
  width: 22px;
  height: 22px;
}

.badge {
  position: absolute;
  top: 0;
  right: 0;
  background-color: #ef4444;
  color: white;
  font-size: 0.65rem;
  font-weight: bold;
  height: 16px;
  min-width: 16px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  border: 2px solid #ffffff;
}

/* User Dropdown */
.user-menu {
  position: relative;
  cursor: pointer;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.25rem;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.user-info:hover {
  background-color: #f1f5f9;
}

.avatar {
  width: 32px;
  height: 32px;
  background-color: #4f46e5;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

.user-name {
  font-weight: 500;
  color: #334155;
  font-size: 0.875rem;
}

.chevron {
  width: 16px;
  height: 16px;
  color: #94a3b8;
  transition: transform 0.2s;
}

.chevron.rotate {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  width: 200px;
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  padding: 0.5rem 0;
  z-index: 50;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: #475569;
  text-decoration: none;
  font-size: 0.875rem;
  background: transparent;
  border: none;
  width: 100%;
  cursor: pointer;
  text-align: left;
  transition: background-color 0.2s, color 0.2s;
}

.dropdown-item svg {
  width: 18px;
  height: 18px;
}

.dropdown-item:hover {
  background-color: #f8fafc;
  color: #0f172a;
}

.dropdown-item.text-danger {
  color: #ef4444;
}

.dropdown-item.text-danger:hover {
  background-color: #fef2f2;
}

.divider {
  height: 1px;
  background-color: #e2e8f0;
  margin: 0.5rem 0;
}

@media (max-width: 768px) {
  .menu-toggle {
    display: block;
  }
  .user-name {
    display: none;
  }
  .notif-dropdown {
    width: 300px;
    right: -50px;
  }
}

/* Notification Dropdown Specifics */
.notification-menu {
  position: relative;
}

.notif-dropdown {
  width: 320px;
  right: 0;
  padding: 0;
  overflow: hidden;
}

.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.notif-header h4 {
  margin: 0;
  font-size: 0.9rem;
  color: #1e293b;
}

.text-btn {
  background: none;
  border: none;
  color: #4f46e5;
  font-size: 0.8rem;
  cursor: pointer;
}

.text-btn:hover {
  text-decoration: underline;
}

.notif-body {
  max-height: 350px;
  overflow-y: auto;
}

.empty-notif {
  padding: 2rem;
  text-align: center;
  color: #64748b;
  font-size: 0.9rem;
}

.notif-item {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
  transition: background-color 0.2s;
  align-items: flex-start;
}

.notif-item:hover {
  background-color: #f8fafc;
}

.notif-item.unread {
  background-color: #eff6ff;
}

.notif-icon {
  font-size: 1.25rem;
  flex-shrink: 0;
}

.notif-content {
  flex-grow: 1;
}

.notif-content p {
  margin: 0 0 0.25rem 0;
  font-size: 0.85rem;
  color: #334155;
  line-height: 1.4;
}

.notif-time {
  font-size: 0.75rem;
  color: #94a3b8;
}

.close-notif-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  flex-shrink: 0;
  transition: all 0.2s;
}

.close-notif-btn:hover {
  background: #e2e8f0;
  color: #ef4444;
}

.close-notif-btn svg {
  width: 16px;
  height: 16px;
}
</style>
