<template>
  <div class="page-container">
    <div class="header">
      <h1 class="page-title">Users</h1>
    </div>

    <div class="card">
      <div class="toolbar">
        <div class="search-box">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
          <input type="text" v-model="searchQuery" @input="debouncedFetch" placeholder="Search by name or email..." />
        </div>
      </div>

      <div v-if="store.loading && !store.users.length" class="loading-state">
        Loading users...
      </div>
      
      <div v-else-if="!store.users.length" class="empty-state">
        <div class="empty-icon">👥</div>
        <h3>No users found</h3>
        <p>No users match your criteria.</p>
      </div>

      <div v-else class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Join Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in store.users" :key="user.id">
              <td>
                <span class="fw-500">{{ user.name }}</span>
              </td>
              <td class="text-muted">{{ user.email }}</td>
              <td>
                <span :class="['role-badge', user.role === 'admin' ? 'role-admin' : 'role-user']">
                  {{ user.role }}
                </span>
              </td>
              <td class="text-muted">{{ new Date(user.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div class="pagination" v-if="store.totalPages > 1">
        <button :disabled="store.currentPage === 1" @click="changePage(store.currentPage - 1)">Previous</button>
        <span class="page-info">Page {{ store.currentPage }} of {{ store.totalPages }}</span>
        <button :disabled="store.currentPage === store.totalPages" @click="changePage(store.currentPage + 1)">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useUserStore } from '../stores/user';

const store = useUserStore();

const searchQuery = ref('');
let searchTimeout = null;

const fetchUsers = () => {
  store.fetchUsers({
    page: store.currentPage,
    search: searchQuery.value,
    per_page: 10
  });
};

const debouncedFetch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    store.currentPage = 1;
    fetchUsers();
  }, 300);
};

const changePage = (page) => {
  store.currentPage = page;
  fetchUsers();
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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

.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

.toolbar {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  gap: 1rem;
}

.search-box {
  position: relative;
  flex: 1;
  max-width: 300px;
}

.search-box svg {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  color: #94a3b8;
}

.search-box input {
  width: 100%;
  padding: 0.5rem 0.5rem 0.5rem 2.25rem;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s;
}

.search-box input:focus {
  border-color: #4f46e5;
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
.text-muted { color: #64748b; }

.role-badge {
  padding: 0.25rem 0.6rem;
  border-radius: 99px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.role-admin { background-color: #fef08a; color: #854d0e; }
.role-user { background-color: #e0e7ff; color: #4338ca; }

.pagination {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pagination button {
  padding: 0.4rem 0.8rem;
  border: 1px solid #cbd5e1;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  color: #475569;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 0.875rem;
  color: #64748b;
}
</style>
