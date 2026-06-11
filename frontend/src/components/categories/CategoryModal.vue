<template>
  <div v-if="isOpen" class="modal-backdrop" @click="close">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h2>{{ isEdit ? 'Edit Category' : 'Add New Category' }}</h2>
        <button class="close-btn" @click="close">&times;</button>
      </div>
      
      <div class="modal-body">
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="name">Name <span class="required">*</span></label>
            <input 
              type="text" 
              id="name" 
              v-model="form.name" 
              placeholder="e.g. Electronics" 
              required
            />
            <span class="error" v-if="store.error?.name">{{ store.error.name[0] }}</span>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea 
              id="description" 
              v-model="form.description" 
              placeholder="Optional description..."
              rows="3"
            ></textarea>
            <span class="error" v-if="store.error?.description">{{ store.error.description[0] }}</span>
          </div>

          <div class="form-group toggle-group">
            <label>Status</label>
            <label class="switch">
              <input type="checkbox" v-model="form.status" />
              <span class="slider round"></span>
            </label>
            <span class="status-text">{{ form.status ? 'Active' : 'Inactive' }}</span>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn-cancel" @click="close" :disabled="store.loading">Cancel</button>
            <button type="submit" class="btn-save" :disabled="store.loading">
              {{ store.loading ? 'Saving...' : 'Save Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useCategoryStore } from '../../stores/category';

const props = defineProps({
  isOpen: Boolean,
  category: Object
});

const emit = defineEmits(['close', 'saved']);
const store = useCategoryStore();

const isEdit = ref(false);
const form = ref({
  name: '',
  description: '',
  status: true
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    if (props.category) {
      isEdit.value = true;
      form.value = {
        name: props.category.name,
        description: props.category.description || '',
        status: props.category.status
      };
    } else {
      isEdit.value = false;
      form.value = { name: '', description: '', status: true };
    }
    store.error = null;
  }
});

const submitForm = async () => {
  let success;
  if (isEdit.value) {
    success = await store.updateCategory(props.category.id, form.value);
  } else {
    success = await store.createCategory(form.value);
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
  max-width: 500px;
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

input[type="text"], textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-family: inherit;
  transition: border-color 0.2s;
}

input[type="text"]:focus, textarea:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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

/* Switch Styles */
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
</style>
