<template>
  <div class="page-container">
    <!-- Success Toast -->
    <div v-if="successMessage" class="toast-notification">
      <div class="toast-content">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="toast-icon"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
        <span>{{ successMessage }}</span>
      </div>
      <button class="toast-close" @click="successMessage = ''">&times;</button>
    </div>

    <div class="header">
      <h1 class="page-title">Products</h1>
      <button class="btn-primary" @click="openAddModal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Product
      </button>
    </div>

    <div class="card">
      <div v-if="store.loading && !store.products.length" class="loading-state">
        Loading products...
      </div>
      
      <div v-else-if="!store.products.length" class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No products found</h3>
        <p>Get started by creating your first product.</p>
        <button class="btn-secondary" @click="openAddModal">Create Product</button>
      </div>

      <div v-else class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in store.products" :key="product.id">
              <td>
                <span class="fw-500">{{ product.name }}</span>
              </td>
              <td>
                <span class="category-badge">{{ product.category?.name || '--' }}</span>
              </td>
              <td class="fw-500">₹{{ product.price.toFixed(2) }}</td>
              <td>
                <span :class="['stock-badge', product.stock > 10 ? 'stock-good' : product.stock > 0 ? 'stock-low' : 'stock-out']">
                  {{ product.stock }} in stock
                </span>
              </td>
              <td>
                <label class="switch">
                  <input type="checkbox" :checked="product.status" @change="toggleStatus(product)" />
                  <span class="slider round"></span>
                </label>
              </td>
              <td class="actions-col">
                <button class="action-btn edit-btn" @click="openEditModal(product)" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                </button>
                <button class="action-btn delete-btn" @click="confirmDelete(product.id)" title="Delete">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ProductModal 
      :is-open="isModalOpen" 
      :product="selectedProduct"
      @close="isModalOpen = false"
      @saved="handleModalSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useProductStore } from '../stores/product';
import ProductModal from '../components/products/ProductModal.vue';

const store = useProductStore();
const isModalOpen = ref(false);
const selectedProduct = ref(null);
const successMessage = ref('');

let toastTimer = null;

const showSuccess = (msg) => {
  successMessage.value = msg;
  if (toastTimer) clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    successMessage.value = '';
  }, 3000);
};

onMounted(() => {
  store.fetchProducts();
});

const openAddModal = () => {
  selectedProduct.value = null;
  isModalOpen.value = true;
};

const openEditModal = (product) => {
  selectedProduct.value = product;
  isModalOpen.value = true;
};

const confirmDelete = async (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    const success = await store.deleteProduct(id);
    if (success) {
      showSuccess('Product deleted successfully!');
    }
  }
};

const toggleStatus = async (product) => {
  const success = await store.toggleStatus(product);
  if (success) {
    showSuccess(`Product status updated to ${product.status ? 'Active' : 'Inactive'}!`);
  }
};

const handleModalSaved = () => {
  store.fetchProducts();
  showSuccess(selectedProduct.value ? 'Product updated successfully!' : 'Product created successfully!');
};
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Toast Styles */
.toast-notification {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  background-color: #22c55e;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  z-index: 1000;
  animation: slideInRight 0.3s ease-out forwards;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.toast-icon { width: 20px; height: 20px; }

.toast-close {
  background: transparent;
  border: none;
  color: white;
  font-size: 1.25rem;
  cursor: pointer;
  opacity: 0.8;
  padding: 0;
  line-height: 1;
}

.toast-close:hover { opacity: 1; }

@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e293b;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #4f46e5;
  color: white;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}
.btn-primary:hover { background-color: #4338ca; }
.btn-primary svg { width: 18px; height: 18px; }

.btn-secondary {
  background-color: white;
  color: #4f46e5;
  border: 1px solid #4f46e5;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 1rem;
}
.btn-secondary:hover { background-color: #f5f3ff; }

.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  color: #64748b;
}
.empty-icon { font-size: 3rem; margin-bottom: 1rem; }
.empty-state h3 { color: #1e293b; margin-bottom: 0.5rem; }

.loading-state {
  padding: 3rem;
  text-align: center;
  color: #64748b;
  font-weight: 500;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th, .data-table td {
  padding: 1rem 1.5rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.data-table th {
  background-color: #f8fafc;
  font-weight: 600;
  color: #475569;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.data-table tr:hover {
  background-color: #f8fafc;
}

.fw-500 { font-weight: 500; color: #1e293b; }

.category-badge {
  background-color: #f1f5f9;
  color: #475569;
  padding: 0.25rem 0.6rem;
  border-radius: 99px;
  font-size: 0.8rem;
  font-weight: 500;
}

.stock-badge {
  padding: 0.25rem 0.6rem;
  border-radius: 99px;
  font-size: 0.8rem;
  font-weight: 500;
}

.stock-good { background-color: #dcfce7; color: #16a34a; }
.stock-low { background-color: #ffedd5; color: #ea580c; }
.stock-out { background-color: #fee2e2; color: #ef4444; }

.actions-col {
  text-align: right;
  width: 100px;
}

.action-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.4rem;
  border-radius: 6px;
  color: #64748b;
  transition: all 0.2s;
  margin-left: 0.25rem;
}
.action-btn svg { width: 18px; height: 18px; }

.edit-btn:hover { background-color: #eff6ff; color: #3b82f6; }
.delete-btn:hover { background-color: #fef2f2; color: #ef4444; }

/* Switch Styles */
.switch {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 20px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1;
  transition: .4s;
  border-radius: 20px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 14px;
  width: 14px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider { background-color: #22c55e; }
input:checked + .slider:before { transform: translateX(16px); }
</style>
