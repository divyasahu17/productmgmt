import { defineStore } from 'pinia';
import api from '../services/api';

export const useNotificationStore = defineStore('notification', {
  state: () => ({
    notifications: [],
    unreadCount: 0,
    loading: false,
    currentPage: 1,
    lastPage: 1,
    total: 0,
    perPage: 10
  }),

  actions: {
    async fetchNotifications(page = 1) {
      this.loading = true;
      try {
        const response = await api.get('/v1/notifications', {
          params: { page, per_page: this.perPage }
        });
        this.notifications = response.data.notifications;
        this.unreadCount = response.data.unread_count;
        this.currentPage = response.data.meta.current_page;
        this.lastPage = response.data.meta.last_page;
        this.total = response.data.meta.total;
      } catch (error) {
        console.error('Error fetching notifications:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchUnreadCount() {
      try {
        // Fetch page 1 just to update the unread count in background
        const response = await api.get('/v1/notifications', { params: { per_page: 1 } });
        this.unreadCount = response.data.unread_count;
      } catch (error) {
        console.error('Error fetching unread count:', error);
      }
    },

    async markAsRead(id) {
      try {
        await api.put(`/v1/notifications/${id}/read`);
        // Update local state
        const notification = this.notifications.find(n => n.id === id);
        if (notification && !notification.read_at) {
          notification.read_at = new Date().toISOString();
          this.unreadCount = Math.max(0, this.unreadCount - 1);
        }
      } catch (error) {
        console.error('Error marking notification as read:', error);
      }
    },

    async markAllAsRead() {
      try {
        await api.put('/v1/notifications/read-all');
        this.notifications.forEach(n => n.read_at = new Date().toISOString());
        this.unreadCount = 0;
      } catch (error) {
        console.error('Error marking all notifications as read:', error);
      }
    }
  }
});
