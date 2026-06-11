<template>
  <div class="page-container">
    <div class="header">
      <h1 class="page-title">Profile</h1>
    </div>
    <div class="card">
      <p class="empty-state">User profile settings will go here.</p>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.page-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1e293b;
}
.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.empty-state {
  color: #64748b;
  text-align: center;
}
</style>
