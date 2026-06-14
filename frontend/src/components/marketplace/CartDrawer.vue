<template>
  <div class="cart-overlay" :class="{ 'is-open': cartStore.isOpen }" @click="cartStore.toggleCart">
    <div class="cart-drawer" @click.stop>
      <div class="cart-header">
        <h2>Your Cart ({{ cartStore.totalItems }})</h2>
        <button class="close-btn" @click="cartStore.toggleCart">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="cart-body">
        <div v-if="cartStore.items.length === 0" class="empty-cart">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="empty-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
          </svg>
          <p>Your cart is empty.</p>
          <button class="shop-now-btn" @click="cartStore.toggleCart">Shop Now</button>
        </div>

        <div v-else class="cart-items">
          <div v-for="item in cartStore.items" :key="item.product.id" class="cart-item">
            <div class="item-image">
              <img v-if="item.product.image_url" :src="item.product.image_url" :alt="item.product.name" />
              <div v-else class="placeholder-img">{{ item.product.name.charAt(0).toUpperCase() }}</div>
            </div>
            
            <div class="item-details">
              <h4 class="item-name">{{ item.product.name }}</h4>
              <div class="item-price">₹{{ item.product.price }}</div>
              
              <div class="item-actions">
                <div class="quantity-selector">
                  <button @click="updateQuantity(item.product.id, item.quantity - 1)" class="qty-btn">-</button>
                  <span class="qty-display">{{ item.quantity }}</span>
                  <button @click="updateQuantity(item.product.id, item.quantity + 1)" class="qty-btn">+</button>
                </div>
                <button class="remove-btn" @click="cartStore.removeFromCart(item.product.id)">Remove</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="cart-footer" v-if="cartStore.items.length > 0">
        <div class="cart-summary">
          <span>Subtotal</span>
          <span class="summary-total">₹{{ cartStore.cartTotalAmount.toFixed(2) }}</span>
        </div>
        <p class="tax-note">Shipping and taxes calculated at checkout.</p>
        <button class="checkout-btn" @click="handleCheckout">
          Proceed to Checkout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCartStore } from '../../stores/cart';
import { useToastStore } from '../../stores/toast';

const cartStore = useCartStore();
const toastStore = useToastStore();

const updateQuantity = (productId, newQuantity) => {
  cartStore.updateQuantity(productId, newQuantity);
};

const handleCheckout = () => {
  toastStore.notify('Checkout functionality coming soon!', 'info');
  cartStore.toggleCart();
};
</script>

<style scoped>
.cart-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(2px);
  z-index: 1000;
  display: flex;
  justify-content: flex-end;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.cart-overlay.is-open {
  opacity: 1;
  visibility: visible;
}

.cart-drawer {
  width: 100%;
  max-width: 450px;
  background: white;
  height: 100vh;
  display: flex;
  flex-direction: column;
  transform: translateX(100%);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: -4px 0 15px rgba(0, 0, 0, 0.1);
}

.cart-overlay.is-open .cart-drawer {
  transform: translateX(0);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
}

.cart-header h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.close-btn {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.close-btn svg {
  width: 24px;
  height: 24px;
}

.cart-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
}

.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
  color: #64748b;
}

.empty-icon {
  width: 64px;
  height: 64px;
  color: #cbd5e1;
  margin-bottom: 1rem;
}

.shop-now-btn {
  margin-top: 1.5rem;
  padding: 0.75rem 2rem;
  background: white;
  border: 1px solid #4f46e5;
  color: #4f46e5;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.shop-now-btn:hover {
  background: #f8fafc;
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
  background: linear-gradient(135deg, #e0e7ff, #fbcfe8);
  color: #4f46e5;
  font-weight: 700;
  font-size: 2rem;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-name {
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.25rem 0;
}

.item-price {
  color: #64748b;
  font-weight: 500;
  margin-bottom: 0.75rem;
}

.item-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.quantity-selector {
  display: flex;
  align-items: center;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  overflow: hidden;
}

.qty-btn {
  background: white;
  border: none;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #475569;
  transition: background 0.2s;
}

.qty-btn:hover {
  background: #f1f5f9;
}

.qty-display {
  width: 32px;
  text-align: center;
  font-size: 0.9rem;
  font-weight: 500;
  color: #0f172a;
}

.remove-btn {
  background: transparent;
  border: none;
  color: #ef4444;
  font-size: 0.85rem;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  transition: background 0.2s;
}

.remove-btn:hover {
  background: #fef2f2;
  text-decoration: underline;
}

.cart-footer {
  padding: 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.cart-summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.125rem;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.summary-total {
  font-size: 1.25rem;
}

.tax-note {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 1.25rem;
}

.checkout-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.checkout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
</style>
