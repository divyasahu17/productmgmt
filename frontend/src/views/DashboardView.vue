<template>
  <div class="dashboard-content">
    
    <!-- Welcome Card -->
    <div class="welcome-card">
      <div class="welcome-text">
        <h1 class="page-title">Welcome back, {{ authStore.user?.name || 'Admin' }}! 👋</h1>
        <p class="subtitle">Here is what's happening with your store today.</p>
      </div>
      <button class="primary-btn">View Reports</button>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon bg-blue">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
        </div>
        <div class="stat-info">
          <h3>Total Products</h3>
          <p class="stat-value">124</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon bg-green">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /></svg>
        </div>
        <div class="stat-info">
          <h3>Categories</h3>
          <p class="stat-value">12</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-orange">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <div class="stat-info">
          <h3>Low Stock</h3>
          <p class="stat-value">5</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-purple">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-info">
          <h3>Total Value</h3>
          <p class="stat-value">$14,500</p>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Areas -->
    <div class="dashboard-grid">
      <!-- Recent Activity -->
      <div class="card recent-activity">
        <div class="card-header">
          <h2>Recent Activity</h2>
          <button class="text-btn">View All</button>
        </div>
        <div class="card-body">
          <ul class="activity-list">
            <li class="activity-item">
              <div class="activity-icon bg-green-light text-green">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
              </div>
              <div class="activity-content">
                <p><strong>Admin</strong> added a new product <em>iPhone 15 Pro</em></p>
                <span class="activity-time">2 hours ago</span>
              </div>
            </li>
            <li class="activity-item">
              <div class="activity-icon bg-blue-light text-blue">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M17 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zM17 15.75a.75.75 0 00-1.5 0v1.5a.75.75 0 001.5 0v-1.5zM3.75 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5a.75.75 0 01.75-.75zM4.5 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zM10 11a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5A.75.75 0 0110 11zM10.75 2.75a.75.75 0 00-1.5 0v1.5a.75.75 0 001.5 0v-1.5zM10 6a2 2 0 100 4 2 2 0 000-4zM3.75 10a2 2 0 100 4 2 2 0 000-4zM16.25 10a2 2 0 100 4 2 2 0 000-4z" /></svg>
              </div>
              <div class="activity-content">
                <p><strong>Divya</strong> updated category <em>Electronics</em></p>
                <span class="activity-time">5 hours ago</span>
              </div>
            </li>
            <li class="activity-item">
              <div class="activity-icon bg-orange-light text-orange">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
              </div>
              <div class="activity-content">
                <p><strong>System</strong> marked <em>MacBook Air</em> as out of stock</p>
                <span class="activity-time">Yesterday</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div class="card alerts-card">
        <div class="card-header">
          <h2 class="card-title">Low Stock Alerts</h2>
          <button class="btn-text">View All</button>
        </div>
        <div class="card-body p-0">
          <div v-if="loadingLowStock" style="padding: 1rem; text-align: center; color: #64748b;">Loading alerts...</div>
          <div v-else-if="lowStockProducts.length === 0" style="padding: 1rem; text-align: center; color: #64748b;">No low stock alerts.</div>
          <ul class="alert-list" v-else>
            <li class="alert-item" v-for="product in lowStockProducts" :key="product.id">
              <div class="alert-icon">⚠️</div>
              <div class="alert-content">
                <p class="alert-text"><strong>{{ product.name }}</strong> is running low.</p>
                <span class="alert-meta">Only {{ product.stock }} items left in stock</span>
              </div>
              <button class="btn-sm" @click="$router.push('/products')">Restock</button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const lowStockProducts = ref([]);
const loadingLowStock = ref(true);

onMounted(async () => {
  try {
    const response = await api.get('/v1/dashboard/low-stock');
    lowStockProducts.value = response.data.data;
  } catch (error) {
    console.error('Failed to load low stock products', error);
  } finally {
    loadingLowStock.value = false;
  }
});

const stats = ref([
  { title: 'Total Revenue', value: '$45,231.89', change: '+20.1%', positive: true, icon: '💵' },
  { title: 'Products Sold', value: '1,205', change: '+10.5%', positive: true, icon: '📦' },
  { title: 'Active Categories', value: '12', change: '0.0%', positive: true, icon: '📂' },
  { title: 'Total Customers', value: '842', change: '+5.2%', positive: true, icon: '👥' },
]);
</script>

<style scoped>
.dashboard-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Welcome Card */
.welcome-card {
  background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
  border-radius: 12px;
  padding: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
  box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.welcome-card .page-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  color: white;
}

.welcome-card .subtitle {
  color: #e0e7ff;
  font-size: 1rem;
}

.primary-btn {
  background-color: white;
  color: #4f46e5;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.bg-blue { background-color: #eff6ff; color: #3b82f6; }
.bg-green { background-color: #f0fdf4; color: #22c55e; }
.bg-orange { background-color: #fff7ed; color: #f97316; }
.bg-purple { background-color: #faf5ff; color: #a855f7; }

.stat-info h3 {
  font-size: 0.875rem;
  color: #64748b;
  margin-bottom: 0.25rem;
  font-weight: 500;
}

.stat-info .stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0f172a;
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  overflow: hidden;
}

.card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #fcfcfc;
}

.card-header h2 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1e293b;
}

.text-btn {
  background: transparent;
  border: none;
  color: #4f46e5;
  font-weight: 500;
  cursor: pointer;
}

.card-body {
  padding: 1.5rem;
}

/* Activity List */
.activity-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.activity-item {
  display: flex;
  gap: 1rem;
}

.activity-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.activity-icon svg { width: 18px; height: 18px; }

.bg-green-light { background-color: #dcfce7; }
.text-green { color: #16a34a; }
.bg-blue-light { background-color: #dbeafe; }
.text-blue { color: #2563eb; }
.bg-orange-light { background-color: #ffedd5; }
.text-orange { color: #ea580c; }

.activity-content p {
  color: #334155;
  font-size: 0.95rem;
}

.activity-content p strong { font-weight: 600; color: #0f172a; }
.activity-time {
  font-size: 0.8rem;
  color: #64748b;
  margin-top: 0.25rem;
  display: block;
}

/* Stock List */
.stock-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.stock-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.stock-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.product-info {
  display: flex;
  flex-direction: column;
}

.product-name {
  font-weight: 500;
  color: #1e293b;
  font-size: 0.95rem;
}

.product-sku {
  font-size: 0.8rem;
  color: #64748b;
}

.stock-count {
  font-weight: 600;
  font-size: 0.9rem;
  background: #f8fafc;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.text-danger { color: #ef4444; }
.text-orange { color: #f97316; }

.badge-orange {
  background-color: #f97316;
  color: white;
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 99px;
  font-weight: 500;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .welcome-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}
</style>
