import { defineStore } from 'pinia';
import api from '../services/api';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        loading: false,
        error: null,
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
    }),
    actions: {
        async fetchProducts(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/products', { params });
                this.products = response.data.data;
                if (response.data.meta) {
                    this.currentPage = response.data.meta.current_page;
                    this.totalPages = response.data.meta.last_page;
                    this.totalItems = response.data.meta.total;
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load products';
            } finally {
                this.loading = false;
            }
        },
        async createProduct(productData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/products', productData);
                this.products.unshift(response.data.data);
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async updateProduct(id, productData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put(`/v1/products/${id}`, productData);
                const index = this.products.findIndex(p => p.id === id);
                if (index !== -1) {
                    this.products[index] = response.data.data;
                }
                return true;
            } catch (err) {
                this.error = err.response?.data?.errors || err.message;
                return false;
            } finally {
                this.loading = false;
            }
        },
        async deleteProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                await api.delete(`/v1/products/${id}`);
                this.products = this.products.filter(p => p.id !== id);
                return true;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to delete product';
                return false;
            } finally {
                this.loading = false;
            }
        },
        async toggleStatus(product) {
            const newStatus = !product.status;
            const index = this.products.findIndex(p => p.id === product.id);
            if (index !== -1) {
                this.products[index].status = newStatus;
            }

            try {
                await api.put(`/v1/products/${product.id}`, { status: newStatus });
                return true;
            } catch (err) {
                if (index !== -1) {
                    this.products[index].status = product.status;
                }
                this.error = 'Failed to update status';
                return false;
            }
        }
    }
});
