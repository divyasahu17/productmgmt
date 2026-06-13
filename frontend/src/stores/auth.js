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
        async updateProfile(profileData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put('/v1/profile', profileData);
                if (!response.data.requires_otp) {
                    this.user = response.data.user;
                }
                return response.data;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async verifyEmailOtp(otpData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/profile/email/verify', otpData);
                this.user = response.data.user;
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async updatePassword(passwordData) {
            this.loading = true;
            this.error = null;
            try {
                await api.put('/v1/profile/password', passwordData);
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async requestPasswordReset(email) {
            this.loading = true;
            this.error = null;
            try {
                await api.post('/v1/forgot-password', { email });
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async resetPassword(data) {
            this.loading = true;
            this.error = null;
            try {
                await api.post('/v1/reset-password', data);
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
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
