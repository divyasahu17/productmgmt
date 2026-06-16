<template>
  <div>
    <!-- Backdrop -->
    <div 
      v-if="cartStore.isOpen" 
      class="cart-backdrop" 
      @click="cartStore.toggleSidebar"
    ></div>

    <!-- Sidebar -->
    <div :class="['cart-sidebar', { 'is-open': cartStore.isOpen }]">
      <div class="cart-header">
        <h2>Your Cart ({{ cartStore.totalItems }})</h2>
        <button class="close-btn" @click="cartStore.toggleSidebar">&times;</button>
      </div>

      <div class="cart-body">
        <div v-if="!authStore.isAuthenticated" class="empty-cart">
          <p>Please login to view your cart.</p>
          <router-link to="/login" class="btn-primary" @click="cartStore.toggleSidebar">Login</router-link>
        </div>
        <div v-else-if="cartStore.loading && cartStore.items.length === 0" class="empty-cart">
          <p>Loading cart...</p>
        </div>
        <div v-else-if="cartStore.items.length === 0" class="empty-cart">
          <p>Your cart is empty.</p>
          <button class="btn-primary" @click="cartStore.toggleSidebar">Continue Shopping</button>
        </div>
        
        <div v-else class="cart-items">
          <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
            <div class="item-image">
              <img v-if="item.product.image_url" :src="item.product.image_url" :alt="item.product.name" />
              <div v-else class="placeholder-img">{{ item.product.name.charAt(0) }}</div>
            </div>
            
            <div class="item-details">
              <h4>{{ item.product.name }}</h4>
              <p class="item-price">₹{{ parseFloat(item.product.price).toFixed(2) }}</p>
              
              <div class="item-controls">
                <div class="quantity-control">
                  <button @click="updateQuantity(item, item.quantity - 1)" :disabled="cartStore.loading">-</button>
                  <span>{{ item.quantity }}</span>
                  <button @click="updateQuantity(item, item.quantity + 1)" :disabled="cartStore.loading || item.quantity >= item.product.stock">+</button>
                </div>
                <button class="remove-btn" @click="cartStore.removeFromCart(item.id)" :disabled="cartStore.loading">Remove</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="authStore.isAuthenticated && cartStore.items.length > 0" class="cart-footer">
        <div class="cart-total">
          <span>Total:</span>
          <span class="total-price">₹{{ cartStore.totalPrice.toFixed(2) }}</span>
        </div>
        <button class="checkout-btn" @click="checkout">Proceed to Checkout</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useCartStore } from '../../stores/cart';
import { useAuthStore } from '../../stores/auth';
import { useToastStore } from '../../stores/toast';
import { useRouter } from 'vue-router';

const cartStore = useCartStore();
const authStore = useAuthStore();
const toastStore = useToastStore();
const router = useRouter();

// Fetch cart on mount if authenticated
onMounted(() => {
  if (authStore.isAuthenticated) {
    cartStore.fetchCart();
  }
});

// Re-fetch cart when authentication status changes
watch(() => authStore.isAuthenticated, (isAuthenticated) => {
  if (isAuthenticated) {
    cartStore.fetchCart();
  } else {
    cartStore.resetState();
  }
});

const updateQuantity = async (item, newQuantity) => {
  if (newQuantity > item.product.stock) {
    toastStore.notify(`Only ${item.product.stock} items available in stock.`, 'error');
    return;
  }
  await cartStore.updateQuantity(item.id, newQuantity);
};

const checkout = () => {
  toastStore.notify('Checkout functionality coming soon!', 'info');
  cartStore.toggleSidebar();
};
</script>

<style scoped>
.cart-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  backdrop-filter: blur(2px);
}

.cart-sidebar {
  position: fixed;
  top: 0;
  right: -400px;
  width: 100%;
  max-width: 400px;
  height: 100vh;
  background: white;
  z-index: 1050;
  box-shadow: -5px 0 25px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  transition: right 0.3s ease-in-out;
}

.cart-sidebar.is-open {
  right: 0;
}

.cart-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
}

.cart-header h2 {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
}

.close-btn {
  background: none;
  border: none;
  font-size: 2rem;
  line-height: 1;
  color: #64748b;
  cursor: pointer;
  padding: 0;
}

.cart-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.empty-cart {
  text-align: center;
  padding: 3rem 1rem;
  color: #64748b;
}

.btn-primary {
  display: inline-block;
  background: #4f46e5;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  margin-top: 1rem;
  border: none;
  cursor: pointer;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.cart-item {
  display: flex;
  gap: 1rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  background: #f8fafc;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.placeholder-img {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  color: #cbd5e1;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-details h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  color: #1e293b;
}

.item-price {
  color: #4f46e5;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
}

.item-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #f1f5f9;
  border-radius: 6px;
  padding: 0.25rem;
}

.quantity-control button {
  background: white;
  border: 1px solid #e2e8f0;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-weight: 600;
}

.quantity-control button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.remove-btn {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.875rem;
  cursor: pointer;
}

.remove-btn:hover {
  text-decoration: underline;
}

.cart-footer {
  padding: 1.5rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
}

.cart-total {
  display: flex;
  justify-content: space-between;
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 1rem;
}

.checkout-btn {
  width: 100%;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 8px;
  font-size: 1.125rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.checkout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
</style>
