import { defineStore } from 'pinia';
import api from '../services/api';

export const useMarketplaceStore = defineStore('marketplace', {
    state: () => ({
        products: [],
        categories: [],
        currentProduct: null,
        loading: false,
        error: null,
        selectedCategory: null
    }),
    actions: {
        async fetchCategories() {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/marketplace/categories');
                this.categories = response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load categories';
                console.error('Fetch categories error:', err);
            } finally {
                this.loading = false;
            }
        },
        async fetchProducts(categoryId = null) {
            this.loading = true;
            this.error = null;
            this.selectedCategory = categoryId;
            try {
                let url = '/v1/marketplace/products';
                if (categoryId) {
                    url += `?category_id=${categoryId}`;
                }
                const response = await api.get(url);
                this.products = response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load products';
                console.error('Fetch products error:', err);
            } finally {
                this.loading = false;
            }
        },
        async fetchProduct(id) {
            this.loading = true;
            this.error = null;
            this.currentProduct = null;
            try {
                const response = await api.get(`/v1/marketplace/products/${id}`);
                this.currentProduct = response.data.data;
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load product';
                console.error('Fetch product error:', err);
            } finally {
                this.loading = false;
            }
        }
    }
});
