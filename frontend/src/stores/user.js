import { defineStore } from 'pinia';
import api from '../services/api';

export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        loading: false,
        error: null,
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
    }),
    actions: {
        async fetchUsers(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/users', { params });
                this.users = response.data.data;
                if (response.data.meta) {
                    this.currentPage = response.data.meta.current_page;
                    this.totalPages = response.data.meta.last_page;
                    this.totalItems = response.data.meta.total;
                } else if (response.data.current_page) {
                    // Fallback if resource collection is not used but direct paginate is used
                    this.currentPage = response.data.current_page;
                    this.totalPages = response.data.last_page;
                    this.totalItems = response.data.total;
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load users';
            } finally {
                this.loading = false;
            }
        }
    }
});
