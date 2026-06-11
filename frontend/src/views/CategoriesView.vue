<template>
  <div class="page-container">
    <div class="header">
      <h1 class="page-title">Categories</h1>
      <button class="primary-btn">Add Category</button>
    </div>
    <div class="card">
      <p class="empty-state">Categories list will go here.</p>
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
.primary-btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: none;
  font-weight: 500;
  cursor: pointer;
}
.primary-btn:hover {
  background-color: #4338ca;
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
