import { defineStore } from 'pinia';
import api from '../services/api';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        notifications: [],
        unreadCount: 0,
        loading: false,
    }),
    actions: {
        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await api.get('/v1/notifications');
                this.notifications = response.data.notifications;
                this.unreadCount = response.data.unread_count;
            } catch (err) {
                console.error('Failed to fetch notifications', err);
            } finally {
                this.loading = false;
            }
        },
        async markAsRead(id) {
            try {
                await api.put(`/v1/notifications/${id}/read`);
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1 && this.notifications[index].read_at === null) {
                    this.notifications[index].read_at = new Date().toISOString();
                    this.unreadCount--;
                }
            } catch (err) {
                console.error('Failed to mark notification as read', err);
            }
        },
        async markAllAsRead() {
            try {
                await api.put('/v1/notifications/read-all');
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            } catch (err) {
                console.error('Failed to mark all as read', err);
            }
        }
    }
});
