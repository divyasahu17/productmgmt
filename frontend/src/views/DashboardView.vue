<template>
  <div class="dashboard-content">
    <div class="header">
      <h1 class="page-title">Dashboard</h1>
      <p class="subtitle">Welcome back to your dashboard.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Products</h3>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card">
        <h3>Categories</h3>
        <p class="stat-value">0</p>
      </div>
      <div class="stat-card">
        <h3>Notifications</h3>
        <p class="stat-value">0</p>
      </div>
    </div>
  </div>
</template>

<script setup>
// Dashboard logic will go here
</script>

<style scoped>
.dashboard-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.header .page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.header .subtitle {
  color: #64748b;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-card h3 {
  font-size: 1rem;
  color: #64748b;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.stat-card .stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #0f172a;
}
</style>
