import { defineStore } from 'pinia';
import api from '../services/api';

export const useCategoryStore = defineStore('category', {
    state: () => ({
        categories: [],
        loading: false,
        error: null,
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
    }),
    actions: {
        async fetchCategories(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/categories', { params });
                this.categories = response.data.data;
                if (response.data.meta) {
                    this.currentPage = response.data.meta.current_page;
                    this.totalPages = response.data.meta.last_page;
                    this.totalItems = response.data.meta.total;
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load categories';
            } finally {
                this.loading = false;
            }
        },
        async createCategory(categoryData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/categories', categoryData);
                this.categories.unshift(response.data.data);
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async updateCategory(id, categoryData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put(`/v1/categories/${id}`, categoryData);
                const index = this.categories.findIndex(c => c.id === id);
                if (index !== -1) {
                    this.categories[index] = response.data.data;
                }
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async deleteCategory(id) {
            this.loading = true;
            this.error = null;
            try {
                await api.delete(`/v1/categories/${id}`);
                this.categories = this.categories.filter(c => c.id !== id);
                return true;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to delete category';
                return false;
            } finally {
                this.loading = false;
            }
        },
        async toggleStatus(category) {
            // Optimistic update
            const newStatus = !category.status;
            const index = this.categories.findIndex(c => c.id === category.id);
            if (index !== -1) {
                this.categories[index].status = newStatus;
            }

            try {
                await api.put(`/v1/categories/${category.id}`, { status: newStatus });
                return true;
            } catch (err) {
                // Revert if fails
                if (index !== -1) {
                    this.categories[index].status = category.status;
                }
                this.error = 'Failed to update status';
                return false;
            }
        }
    }
});
