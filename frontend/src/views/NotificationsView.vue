<template>
  <div class="page-container">
    <div class="header">
      <h1 class="page-title">Notifications</h1>
      <button v-if="store.unreadCount > 0" class="btn btn-secondary" @click="store.markAllAsRead">
        Mark all as read
      </button>
    </div>

    <div class="card">
      <div v-if="store.loading" class="loading-state">
        <div class="spinner"></div>
      </div>
      
      <div v-else-if="!store.notifications.length" class="empty-state">
        <p>No notifications.</p>
      </div>

      <div v-else class="notifications-list">
        <div 
          v-for="notification in store.notifications" 
          :key="notification.id"
          :class="['notification-item', { 'unread': !notification.read_at }]"
        >
          <div class="notification-content">
            <h3 class="notification-title">{{ notification.data.message }}</h3>
            <span class="notification-time">{{ new Date(notification.created_at).toLocaleString() }}</span>
          </div>
          <button 
            v-if="!notification.read_at" 
            class="btn-mark-read" 
            @click="store.markAsRead(notification.id)"
          >
            Mark as read
          </button>
        </div>
      </div>

      <!-- Pagination Controls -->
      <div v-if="store.lastPage > 1" class="pagination">
        <button 
          :disabled="store.currentPage === 1" 
          @click="changePage(store.currentPage - 1)"
          class="page-btn"
        >
          Previous
        </button>
        <span class="page-info">Page {{ store.currentPage }} of {{ store.lastPage }}</span>
        <button 
          :disabled="store.currentPage === store.lastPage" 
          @click="changePage(store.currentPage + 1)"
          class="page-btn"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useNotificationStore } from '../stores/notification';

const store = useNotificationStore();

const changePage = (page) => {
  store.fetchNotifications(page);
};

onMounted(() => {
  store.fetchNotifications();
});
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1e293b;
}
.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.notifications-list {
  display: flex;
  flex-direction: column;
}

.notification-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  transition: background-color 0.2s;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item.unread {
  background-color: #eff6ff;
  border-left: 4px solid #3b82f6;
}

.notification-title {
  font-weight: 500;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.notification-time {
  font-size: 0.875rem;
  color: #64748b;
}

.btn-mark-read {
  background: transparent;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-mark-read:hover {
  background: #f1f5f9;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
}
.btn-secondary {
  background-color: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}
.btn-secondary:hover {
  background-color: #e2e8f0;
}

.loading-state, .empty-state {
  padding: 4rem 2rem;
  text-align: center;
  color: #64748b;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #4f46e5;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1.5rem;
  gap: 1rem;
  border-top: 1px solid #e2e8f0;
}

.page-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #64748b;
  font-size: 0.875rem;
}
</style>
