import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        loading: false,
        error: null,
        isOpen: false, // For sidebar
    }),

    getters: {
        totalItems: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
        totalPrice: (state) => state.items.reduce((total, item) => total + (item.quantity * item.product.price), 0),
    },

    actions: {
        toggleSidebar() {
            this.isOpen = !this.isOpen;
        },
        
        async fetchCart() {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get('/v1/cart');
                this.items = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch cart';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async addToCart(productId, quantity = 1) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/v1/cart', {
                    product_id: productId,
                    quantity: quantity
                });
                // If item exists, update it. Else push new.
                const index = this.items.findIndex(i => i.product_id === productId);
                if (index !== -1) {
                    this.items[index] = response.data;
                } else {
                    this.items.push(response.data);
                }
                this.isOpen = true; // Auto open sidebar
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to add to cart';
                import('./toast').then(({ useToastStore }) => {
                    useToastStore().notify(this.error, 'error');
                });
                return false;
            } finally {
                this.loading = false;
            }
        },

        async updateQuantity(cartId, quantity) {
            if (quantity < 1) return this.removeFromCart(cartId);
            
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put(`/v1/cart/${cartId}`, { quantity });
                const index = this.items.findIndex(i => i.id === cartId);
                if (index !== -1) {
                    this.items[index] = response.data;
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update quantity';
                import('./toast').then(({ useToastStore }) => {
                    useToastStore().notify(this.error, 'error');
                });
            } finally {
                this.loading = false;
            }
        },

        async removeFromCart(cartId) {
            this.loading = true;
            this.error = null;
            try {
                await api.delete(`/v1/cart/${cartId}`);
                this.items = this.items.filter(i => i.id !== cartId);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to remove item';
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async clearCart() {
            this.loading = true;
            try {
                await api.delete('/v1/cart');
                this.items = [];
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        // Called on logout
        resetState() {
            this.items = [];
            this.isOpen = false;
            this.error = null;
        }
    }
});
