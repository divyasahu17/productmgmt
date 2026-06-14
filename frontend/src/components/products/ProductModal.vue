<template>
  <div v-if="isOpen" class="modal-backdrop" @click="close">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>{{ isEdit ? 'Edit Product' : 'Add New Product' }}</h2>
        <button class="close-btn" @click="close">&times;</button>
      </div>
      
      <div class="modal-body">
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="name">Product Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="name" 
              v-model="form.name" 
              placeholder="e.g. iPhone 15 Pro" 
              required
            />
            <span class="error" v-if="store.error?.name">{{ store.error.name[0] }}</span>
          </div>

          <div class="form-row">
            <div class="form-group half-width">
              <label for="category_id">Category <span class="required">*</span></label>
              <select id="category_id" v-model="form.category_id" required>
                <option value="" disabled>Select a category</option>
                <option v-for="cat in categoryStore.categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
              <span class="error" v-if="store.error?.category_id">{{ store.error.category_id[0] }}</span>
            </div>

            <div class="form-group half-width">
              <label for="price">Price (₹) <span class="required">*</span></label>
              <input 
                type="number" 
                id="price" 
                step="0.01" 
                v-model="form.price" 
                required
              />
              <span class="error" v-if="store.error?.price">{{ store.error.price[0] }}</span>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group half-width">
              <label for="stock">Stock Quantity <span class="required">*</span></label>
              <input 
                type="number" 
                id="stock" 
                v-model="form.stock" 
                required
              />
              <span class="error" v-if="store.error?.stock">{{ store.error.stock[0] }}</span>
            </div>

            <div class="form-group toggle-group half-width" style="margin-top: 1.5rem;">
              <label>Status</label>
              <label class="switch">
                <input type="checkbox" v-model="form.status" />
                <span class="slider round"></span>
              </label>
              <span class="status-text">{{ form.status ? 'Active' : 'Inactive' }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="image">Product Image <span style="font-weight: normal; color: #64748b; font-size: 0.8rem;">(Max 2MB, Optional)</span></label>
            <input 
              type="file" 
              id="image" 
              @change="handleFileChange"
              accept="image/*"
              class="file-input"
            />
            <span class="error" v-if="store.error?.image">{{ store.error.image[0] }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea 
              id="description" 
              v-model="form.description" 
              placeholder="Product details..."
              rows="3"
            ></textarea>
            <span class="error" v-if="store.error?.description">{{ store.error.description[0] }}</span>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="close" :disabled="store.loading">Cancel</button>
            <button type="submit" class="btn-save" :disabled="store.loading">
              {{ store.loading ? 'Saving...' : 'Save Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useProductStore } from '../../stores/product';
import { useCategoryStore } from '../../stores/category';

const props = defineProps({
  isOpen: Boolean,
  product: Object
});

const emit = defineEmits(['close', 'saved']);
const store = useProductStore();
const categoryStore = useCategoryStore();

const isEdit = ref(false);
const form = ref({
  name: '',
  category_id: '',
  price: 0,
  stock: 0,
  description: '',
  status: true,
  image: null
});

onMounted(() => {
  if (categoryStore.categories.length === 0) {
    categoryStore.fetchCategories();
  }
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    if (props.product) {
      isEdit.value = true;
      form.value = {
        name: props.product.name,
        category_id: props.product.category_id,
        price: props.product.price,
        stock: props.product.stock,
        description: props.product.description || '',
        status: props.product.status,
        image: null
      };
    } else {
      isEdit.value = false;
      form.value = { name: '', category_id: '', price: 0, stock: 0, description: '', status: true, image: null };
    }
    // reset file input
    const fileInput = document.getElementById('image');
    if (fileInput) fileInput.value = '';
    
    store.error = null;
  }
});

const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    form.value.image = e.target.files[0];
  } else {
    form.value.image = null;
  }
};

const submitForm = async () => {
  let success;
  if (isEdit.value) {
    success = await store.updateProduct(props.product.id, form.value);
  } else {
    success = await store.createProduct(form.value);
  }

  if (success) {
    emit('saved');
    close();
  }
};

const close = () => {
  emit('close');
};
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: #64748b;
  cursor: pointer;
}

.modal-body {
  padding: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.half-width {
  flex: 1;
}

.form-group {
  margin-bottom: 1.25rem;
}

label {
  display: block;
  font-weight: 500;
  color: #334155;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.required {
  color: #ef4444;
}

input[type="text"], input[type="number"], select, textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-family: inherit;
  transition: border-color 0.2s;
  background-color: #fff;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.file-input {
  padding: 0.5rem;
  background-color: #f8fafc;
  cursor: pointer;
}

.file-input::file-selector-button {
  background-color: #e2e8f0;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  margin-right: 1rem;
  font-weight: 500;
  color: #475569;
  transition: background-color 0.2s;
}

.file-input::file-selector-button:hover {
  background-color: #cbd5e1;
}

.error {
  color: #ef4444;
  font-size: 0.8rem;
  margin-top: 0.25rem;
  display: block;
}

.toggle-group {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.toggle-group label {
  margin-bottom: 0;
}

.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider { background-color: #22c55e; }
input:checked + .slider:before { transform: translateX(20px); }

.status-text {
  font-size: 0.9rem;
  color: #475569;
  font-weight: 500;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  background: white;
  border: 1px solid #cbd5e1;
  color: #475569;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.btn-cancel:hover { background: #f8fafc; }

.btn-save {
  background: #4f46e5;
  border: none;
  color: white;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.btn-save:hover { background: #4338ca; }
.btn-save:disabled { background: #94a3b8; cursor: not-allowed; }

@media (max-width: 600px) {
  .form-row {
    flex-direction: column;
    gap: 0;
  }
}
</style>
