# Online Product Management Platform

A clean, highly responsive, and robust Product Management Platform built with a decoupled architecture utilizing **Laravel** (Backend API) and **Vue.js 3** (Frontend SPA). 

This repository was progressively developed with atomic commits to demonstrate a structured, step-by-step software engineering lifecycle, covering everything from foundational CRUD to advanced architectural implementations.

---

## Architecture Overview

The platform strictly enforces a **Separation of Concerns** (SoC) by maintaining independent backend and frontend architectures:
- **Backend (Laravel REST API)**: Handles all business logic, database transactions, queue processing, and data validation. It acts purely as an API provider.
- **Frontend (Vue.js + Pinia + Vite)**: A reactive Single Page Application (SPA) that consumes the API, manages complex local state (using Pinia), and provides a dynamic, user-friendly interface.

---

## Core Implementations & Task Requirements

### 1. User Authentication
- **Token-Based Auth**: Implemented using **Laravel Sanctum** to issue secure, lightweight API tokens upon login.
- **Role-Based Access Control (RBAC)**: Strict segregation between `admin` and `standard user` roles. Dedicated middleware ensures users cannot access the admin portal and vice versa.
- **State Management**: The Vue frontend uses Pinia stores to securely persist the authentication token and user state across sessions.

### 2. Product Management
- **Full CRUD Operations**: Users/Admins can add, view, edit, and delete products seamlessly via interactive UI modals.
- **Advanced Filtering & Pagination**: The backend API supports dynamic filtering (by category, status, and search keywords) and server-side pagination, ensuring fast response times even with massive datasets.
- **Eager Loading**: Eloquent's `with('category')` is utilized across all resource controllers to eliminate the N+1 query problem, drastically optimizing performance.

### 3. Low Stock Management
- **Real-Time Monitoring**: Product stock levels are monitored dynamically during updates or sales.
- **Multi-Channel Alerts**: When a product's stock drops below a predefined threshold (e.g., 5 items), the system automatically triggers a `LowStockNotification`.
- **In-App & Email Delivery**: The notification is instantly saved to the database (for the frontend notification bell) and concurrently dispatched as an Email Alert to administrators to ensure timely replenishment.

### 4. File Upload Functionality
- **Secure Handling**: Product images are uploaded via `multipart/form-data`. The backend validates file types (`mimes:jpeg,png,jpg,webp`) and enforces size constraints.
- **Storage Management**: Images are securely stored in Laravel's local `public` storage disk, with unique hashed filenames to prevent collisions. Old images are automatically deleted from the server when a product is updated or deleted, preventing storage bloat.

### 5. Scalability and Fault Tolerance
- **Asynchronous Processing (Queues)**: Resource-intensive operations, such as sending the low-stock email alerts, are offloaded to **Laravel Queues** (backed by the database driver). This prevents the API from blocking and ensures high throughput.
- **Frontend Graceful Degradation**: The Vue frontend integrates `axios-retry`. If the backend is temporarily unreachable due to network glitches, the frontend automatically retries idempotent requests with an exponential backoff strategy.
- **Database Indexing**: The `products` table features dedicated indexes on `category_id`, `status`, and `slug` to guarantee fast, O(log n) search performance at scale.
- **Caching**: Heavy dashboard metrics utilize Laravel's `Cache::remember` to serve complex aggregations quickly, minimizing database bottlenecks on high-traffic admin dashboards.

### 6. Security Considerations & Data Integrity
- **SQL Injection & XSS Prevention**: All database queries strictly utilize Eloquent ORM parameterized queries. The Vue template engine automatically escapes all output to prevent Cross-Site Scripting (XSS).
- **Centralized Error Handling**: Production API exceptions are standardized via `Handler.php` to emit consistent, sanitized JSON error structures, ensuring sensitive stack traces are never exposed.
- **API Rate Limiting**: Laravel's `ThrottleRequests` middleware protects authentication and core endpoints against brute-force abuse and DDoS attempts.
- **Data Integrity**: Enforced through strict database foreign key constraints (`cascade` on delete) and comprehensive Form Request validation classes before any data touches the models.

---

## Installation & Setup Guide

### Prerequisites
- PHP ^8.1
- Composer
- Node.js & NPM
- MySQL

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Configure your DB credentials in .env
php artisan migrate --seed
php artisan storage:link
```

### Running the Services
To fully utilize the decoupled architecture and fault-tolerant queues, you need three terminal windows:

1. **Start the Laravel API Server:**
```bash
cd backend
php artisan serve
```

2. **Start the Queue Worker (For Email Alerts):**
```bash
cd backend
php artisan queue:work
```

3. **Start the Vue Frontend:**
```bash
cd frontend
npm install
npm run dev
```

The frontend will be accessible at `http://localhost:5173`.
