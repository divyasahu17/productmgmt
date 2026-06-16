<template>
  <div class="product-detail-view">
    <div class="detail-container">
      <button class="back-btn" @click="$router.back()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
        Back to Marketplace
      </button>

      <div v-if="marketplaceStore.loading" class="loading">Loading product details...</div>
      <div v-else-if="!marketplaceStore.currentProduct" class="error">Product not found.</div>
      
      <div v-else class="product-content">
        <div class="product-image-side">
          <div v-if="marketplaceStore.currentProduct.image_url" class="product-image-container">
            <img :src="marketplaceStore.currentProduct.image_url" alt="Product Image" class="product-image-large-img" />
          </div>
          <div v-else class="product-image-large">
            {{ marketplaceStore.currentProduct.name.charAt(0).toUpperCase() }}
          </div>
        </div>
        
        <div class="product-info-side">
          <div class="category-badge" v-if="marketplaceStore.currentProduct.category">
            {{ marketplaceStore.currentProduct.category.name }}
          </div>
          <h1 class="product-title">{{ marketplaceStore.currentProduct.name }}</h1>
          <p class="product-sku">SKU: {{ marketplaceStore.currentProduct.slug }}</p>
          
          <div class="price-section">
            <span class="price">₹{{ parseFloat(marketplaceStore.currentProduct.price).toFixed(2) }}</span>
            <span :class="['stock-badge', marketplaceStore.currentProduct.stock > 10 ? 'in-stock' : marketplaceStore.currentProduct.stock > 0 ? 'low-stock' : 'out-of-stock']">
              {{ marketplaceStore.currentProduct.stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
          </div>
          
          <div class="description-section">
            <h3>Description</h3>
            <p>{{ marketplaceStore.currentProduct.description || 'No description available for this product.' }}</p>
          </div>
          
          <div class="actions-section">
            <button 
              class="add-to-cart-btn" 
              :disabled="marketplaceStore.currentProduct.stock <= 0"
              @click="addToCart"
            >
              {{ marketplaceStore.currentProduct.stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { useCartStore } from '../../stores/cart';
import { useMarketplaceStore } from '../../stores/marketplace';

const route = useRoute();
const router = useRouter();
const marketplaceStore = useMarketplaceStore();
const authStore = useAuthStore();
const cartStore = useCartStore();

onMounted(async () => {
  const productId = route.params.id;
  await marketplaceStore.fetchProduct(productId);
});

const addToCart = async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  if (marketplaceStore.currentProduct) {
    await cartStore.addToCart(marketplaceStore.currentProduct.id, 1);
  }
};
</script>

<style scoped>
.product-detail-view {
  padding: 8rem 2rem 4rem;
  min-height: 100vh;
}

.detail-container {
  max-width: 1200px;
  margin: 0 auto;
}

.back-btn {
  background: transparent;
  border: none;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  margin-bottom: 2rem;
  padding: 0;
  font-size: 1rem;
  transition: color 0.2s;
}

.back-btn svg {
  width: 20px;
  height: 20px;
}

.back-btn:hover {
  color: #4f46e5;
}

.product-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  background: white;
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
}

.product-image-side {
  display: flex;
  justify-content: center;
  align-items: center;
}

.product-image-container {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.product-image-large-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-image-large {
  width: 100%;
  aspect-ratio: 1;
  background: linear-gradient(135deg, #e0e7ff, #fbcfe8);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 8rem;
  font-weight: 800;
  color: rgba(79, 70, 229, 0.3);
  box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
}

.product-info-side {
  display: flex;
  flex-direction: column;
}

.category-badge {
  display: inline-block;
  background: #fdf2f8;
  color: #ec4899;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  align-self: flex-start;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.product-title {
  font-size: 3rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
  margin-bottom: 0.5rem;
}

.product-sku {
  color: #94a3b8;
  font-size: 0.875rem;
  margin-bottom: 2rem;
}

.price-section {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #f1f5f9;
}

.price {
  font-size: 2.5rem;
  font-weight: 700;
  color: #4f46e5;
}

.stock-status {
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
}

.in-stock {
  background: #dcfce7;
  color: #16a34a;
}

.out-of-stock {
  background: #fee2e2;
  color: #ef4444;
}

.description-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 1rem;
}

.description-section p {
  color: #475569;
  line-height: 1.8;
  font-size: 1.05rem;
  margin-bottom: 3rem;
}

.actions-section {
  margin-top: auto;
}

.add-to-cart-btn {
  width: 100%;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  padding: 1.25rem;
  font-size: 1.125rem;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
}

.add-to-cart-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.4);
}

.add-to-cart-btn:disabled {
  background: #cbd5e1;
  box-shadow: none;
  cursor: not-allowed;
  transform: none;
}

.loading, .error {
  text-align: center;
  padding: 5rem;
  font-size: 1.25rem;
  color: #64748b;
}

@media (max-width: 992px) {
  .product-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 2rem;
  }
  
  .product-image-side {
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
  }
}

@media (max-width: 640px) {
  .product-title {
    font-size: 2rem;
  }
  .price {
    font-size: 2rem;
  }
}
</style>
