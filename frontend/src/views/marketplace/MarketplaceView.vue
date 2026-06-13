<template>
  <div class="marketplace-view">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Discover Premium Products</h1>
        <p>Curated collections for your everyday needs. Shop the latest trends and exclusive deals.</p>
        <button class="cta-button" @click="scrollToFeatured">Shop Now</button>
      </div>
      <div class="hero-background"></div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="categories-section">
      <div class="section-container">
        <h2>Shop by Category</h2>
        <div v-if="marketplaceStore.loading && !marketplaceStore.categories.length" class="loading">Loading categories...</div>
        <div class="categories-grid" v-else>
          <div 
            v-for="category in marketplaceStore.categories" 
            :key="category.id" 
            class="category-card"
            @click="filterByCategory(category.id)"
          >
            <div class="category-icon">📂</div>
            <h3>{{ category.name }}</h3>
          </div>
          <div 
            class="category-card all-categories"
            @click="filterByCategory(null)"
          >
            <div class="category-icon">✨</div>
            <h3>All Products</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="featured-section">
      <div class="section-container">
        <h2>{{ currentCategoryName ? currentCategoryName : 'Featured Products' }}</h2>
        <div v-if="marketplaceStore.loading" class="loading">Loading products...</div>
        <div v-else-if="marketplaceStore.products.length === 0" class="empty-state">
          No products found in this category.
        </div>
        <div class="products-grid" v-else>
          <div 
            v-for="product in marketplaceStore.products" 
            :key="product.id" 
            class="product-card"
            @click="$router.push(`/product/${product.id}`)"
          >
            <div class="product-image-placeholder">
              {{ product.name.charAt(0).toUpperCase() }}
            </div>
            <div class="product-info">
              <span class="category-tag" v-if="product.category">{{ product.category.name }}</span>
              <h3 class="product-name">{{ product.name }}</h3>
              <div class="product-footer">
                <span class="product-price">${{ parseFloat(product.price).toFixed(2) }}</span>
                <button class="add-to-cart-btn" @click.stop="addToCart(product)">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useMarketplaceStore } from '../../stores/marketplace';
import { useRouter } from 'vue-router';

const marketplaceStore = useMarketplaceStore();
const router = useRouter();

onMounted(async () => {
  await Promise.all([
    marketplaceStore.fetchCategories(),
    marketplaceStore.fetchProducts()
  ]);
});

const currentCategoryName = computed(() => {
  if (!marketplaceStore.selectedCategory) return null;
  const cat = marketplaceStore.categories.find(c => c.id === marketplaceStore.selectedCategory);
  return cat ? cat.name : null;
});

const filterByCategory = async (categoryId) => {
  await marketplaceStore.fetchProducts(categoryId);
  document.getElementById('featured').scrollIntoView({ behavior: 'smooth' });
};

const scrollToFeatured = () => {
  document.getElementById('featured').scrollIntoView({ behavior: 'smooth' });
};

const addToCart = (product) => {
  // Simple alert for now
  alert(`Added ${product.name} to cart!`);
};
</script>

<style scoped>
.marketplace-view {
  width: 100%;
}

/* Hero Section */
.hero {
  position: relative;
  height: 80vh;
  min-height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow: hidden;
  padding: 0 2rem;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at top right, #e0e7ff, transparent 40%),
              radial-gradient(circle at bottom left, #fbcfe8, transparent 40%);
  background-color: #f8fafc;
  z-index: -1;
}

.hero-content {
  max-width: 800px;
  animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

.hero h1 {
  font-size: 4rem;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  letter-spacing: -0.02em;
}

.hero p {
  font-size: 1.25rem;
  color: #475569;
  margin-bottom: 2.5rem;
  line-height: 1.6;
}

.cta-button {
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  padding: 1rem 3rem;
  font-size: 1.125rem;
  font-weight: 600;
  border-radius: 9999px;
  cursor: pointer;
  box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
  transition: all 0.3s ease;
}

.cta-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px -5px rgba(79, 70, 229, 0.5);
}

/* Sections Common */
.section-container {
  max-width: 1200px;
  margin: 0 auto;
}

.categories-section, .featured-section {
  padding: 5rem 2rem;
}

h2 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 3rem;
  text-align: center;
}

/* Categories */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
}

.category-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  text-align: center;
  cursor: pointer;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
  border-color: #e0e7ff;
}

.category-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.category-card h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #334155;
}

.all-categories {
  background: linear-gradient(135deg, #f8fafc, #e0e7ff);
}

/* Products */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2.5rem;
}

.product-card {
  background: white;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.product-image-placeholder {
  height: 250px;
  background: linear-gradient(135deg, #e0e7ff, #f3e8ff);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 5rem;
  font-weight: 800;
  color: rgba(79, 70, 229, 0.2);
  transition: transform 0.5s ease;
}

.product-card:hover .product-image-placeholder {
  transform: scale(1.05);
}

.product-info {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  flex: 1;
  background: white;
  z-index: 1;
}

.category-tag {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  color: #ec4899;
  margin-bottom: 0.5rem;
}

.product-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 1rem;
  flex: 1;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
}

.add-to-cart-btn {
  background: #f1f5f9;
  color: #4f46e5;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.add-to-cart-btn svg {
  width: 20px;
  height: 20px;
}

.add-to-cart-btn:hover {
  background: #4f46e5;
  color: white;
  transform: scale(1.1);
}

.loading, .empty-state {
  text-align: center;
  padding: 3rem;
  color: #64748b;
  font-size: 1.125rem;
}

@media (max-width: 768px) {
  .hero h1 {
    font-size: 2.5rem;
  }
  .hero p {
    font-size: 1.125rem;
  }
}
</style>
