import { defineStore } from 'pinia';
import api from '../services/api';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: JSON.parse(localStorage.getItem('cartItems')) || [],
    isOpen: false,
    loading: false,
  }),
  getters: {
    totalItems: (state) => {
      return state.items.reduce((total, item) => total + item.quantity, 0);
    },
    cartTotalAmount: (state) => {
      return state.items.reduce((total, item) => total + (parseFloat(item.product.price) * item.quantity), 0);
    }
  },
  actions: {
    toggleCart() {
      this.isOpen = !this.isOpen;
    },
    async fetchCart() {
      const { useAuthStore } = await import('./auth');
      const authStore = useAuthStore();
      if (!authStore.isAuthenticated) return;
      this.loading = true;
      try {
        const response = await api.get('/v1/cart');
        this.items = response.data;
        this.saveLocalCart();
      } catch (err) {
        console.error('Failed to fetch cart', err);
      } finally {
        this.loading = false;
      }
    },
    async syncCart() {
      const { useAuthStore } = await import('./auth');
      const authStore = useAuthStore();
      if (!authStore.isAuthenticated) return;
      
      const localItems = JSON.parse(localStorage.getItem('cartItems')) || [];
      if (localItems.length > 0) {
        try {
          const payload = {
            items: localItems.map(item => ({
              product_id: item.product.id,
              quantity: item.quantity
            }))
          };
          const response = await api.post('/v1/cart/sync', payload);
          this.items = response.data;
        } catch (err) {
          console.error('Failed to sync cart', err);
        }
      } else {
        await this.fetchCart();
      }
      this.saveLocalCart();
    },
    async addToCart(product, quantity = 1) {
      const existingItem = this.items.find(item => item.product.id === product.id);
      
      if (existingItem) {
        existingItem.quantity += quantity;
      } else {
        this.items.push({
          product: product,
          quantity: quantity
        });
      }
      
      const { useAuthStore } = await import('./auth');
      const authStore = useAuthStore();
      if (authStore.isAuthenticated) {
        try {
          const currentItem = this.items.find(item => item.product.id === product.id);
          await api.post('/v1/cart', {
            product_id: product.id,
            quantity: currentItem.quantity
          });
        } catch(err) {
          console.error(err);
        }
      } else {
        this.saveLocalCart();
      }
    },
    async removeFromCart(productId) {
      this.items = this.items.filter(item => item.product.id !== productId);
      
      const { useAuthStore } = await import('./auth');
      const authStore = useAuthStore();
      if (authStore.isAuthenticated) {
        try {
          await api.delete(`/v1/cart/${productId}`);
        } catch(err) {
          console.error(err);
        }
      } else {
        this.saveLocalCart();
      }
    },
    async updateQuantity(productId, quantity) {
      const item = this.items.find(item => item.product.id === productId);
      if (item) {
        item.quantity = quantity;
        if (item.quantity <= 0) {
          await this.removeFromCart(productId);
        } else {
          const { useAuthStore } = await import('./auth');
          const authStore = useAuthStore();
          if (authStore.isAuthenticated) {
            try {
              await api.post('/v1/cart', {
                product_id: productId,
                quantity: item.quantity
              });
            } catch(err) {
              console.error(err);
            }
          } else {
            this.saveLocalCart();
          }
        }
      }
    },
    async clearCart() {
      this.items = [];
      const { useAuthStore } = await import('./auth');
      const authStore = useAuthStore();
      if (authStore.isAuthenticated) {
        try {
          await api.delete('/v1/cart/clear');
        } catch(err) {
          console.error(err);
        }
      }
      this.saveLocalCart();
    },
    saveLocalCart() {
      localStorage.setItem('cartItems', JSON.stringify(this.items));
    },
    clearLocalOnly() {
        this.items = [];
        localStorage.removeItem('cartItems');
    }
  }
});
