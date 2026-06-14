import { defineStore } from 'pinia';
import api from '../services/api';

export const useMarketplaceStore = defineStore('marketplace', {
    state: () => ({
        products: [],
        categories: [],
        currentProduct: null,
        loading: false,
        error: null,
        selectedCategory: null,
        categoriesPage: 1,
        categoriesLastPage: 1,
        productsPage: 1,
        productsLastPage: 1
    }),
    actions: {
        async fetchCategories(page = 1) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/marketplace/categories', {
                    params: { page }
                });
                this.categories = response.data.data;
                if (response.data.meta) {
                    this.categoriesPage = response.data.meta.current_page;
                    this.categoriesLastPage = response.data.meta.last_page;
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Failed to load categories';
                console.error('Fetch categories error:', err);
            } finally {
                this.loading = false;
            }
        },
        async fetchProducts(categoryId = null, page = 1) {
            this.loading = true;
            this.error = null;
            this.selectedCategory = categoryId;
            try {
                const params = { page };
                if (categoryId) {
                    params.category_id = categoryId;
                }
                const response = await api.get('/v1/marketplace/products', { params });
                this.products = response.data.data;
                if (response.data.meta) {
                    this.productsPage = response.data.meta.current_page;
                    this.productsLastPage = response.data.meta.last_page;
                }
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
