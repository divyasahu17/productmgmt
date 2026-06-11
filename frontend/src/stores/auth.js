import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async register(userData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/register', userData);
                this.setToken(response.data.token);
                this.user = response.data.user;
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/login', credentials);
                this.setToken(response.data.token);
                this.user = response.data.user;
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async fetchUser() {
            if (!this.token) return false;
            this.loading = true;
            try {
                const response = await api.get('/v1/user');
                // Handle Laravel Resource 'data' wrapper if present
                this.user = response.data.data ? response.data.data : response.data;
                return true;
            } catch (err) {
                this.clearAuth();
                return false;
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                if (this.token) {
                    await api.post('/v1/logout');
                }
            } catch (err) {
                console.error('Logout failed on backend', err);
            } finally {
                this.clearAuth();
            }
        },
        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },
        clearAuth() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('token');
        }
    }
});
