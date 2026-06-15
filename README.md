# Online Product Management Platform

This repository contains the source code for a responsive Online Product Management Platform. The architecture adheres to modern development standards, maintaining a strict separation between the backend (Laravel) and the frontend (Vue JS). 

## Architecture & Design Considerations

### 1. User Authentication
- **Approach**: The platform uses stateless API authentication via **Laravel Sanctum**. 
- **Flow**: Upon successful login, the backend issues a secure token, which the Vue JS frontend stores locally and attaches to the `Authorization: Bearer` header of all subsequent Axios requests.
- **Security**: Authentication routes are protected by a strict rate limiter (`throttle:5,1`) to prevent brute force attacks. Unauthorized requests (401) are globally intercepted by Axios to clear expired tokens and redirect users to the login screen.

### 2. Product Management
- **CRUD Operations**: Handled via `ProductController` which utilizes Eloquent ORM. 
- **Filtering & Pagination**: The API supports dynamic query parameters. `Product::where()` clauses are appended conditionally based on `search` and `category_id` parameters. The results are paginated using Laravel’s built-in `paginate()` method to ensure smooth rendering on the frontend.
- **Validation**: Strict **Form Requests** (`StoreProductRequest`, `UpdateProductRequest`) are used to validate incoming product data before processing.

### 3. Low Stock Management
- **Monitoring**: After any product is created or updated, the system checks the updated `stock` attribute.
- **Alerts**: If the stock falls below the predefined threshold (e.g., 5 items), an event triggers the `LowStockNotification`.
- **Delivery**: Notifications are dispatched to all `admin` users via Laravel's Notification facade, supporting multiple channels (database for in-app alerts, mail for email alerts) to ensure timely replenishment.

### 4. File Upload Functionality
- **Processing**: Product images are uploaded via multipart form data. The backend validates the file type (e.g., `mimes:jpeg,png,jpg`) and size.
- **Storage**: Images are stored in the `public/products` disk. A symbolic link connects `storage/app/public` to `public/storage` to make the files publicly accessible.
- **Asynchronous Optimization**: Heavy image processing tasks (resizing, optimization) are dispatched to background queues (`ProcessProductImage` Job) to ensure the HTTP request completes swiftly without making the user wait.

### 5. Scalability and Fault Tolerance
- **Caching**: Frequently accessed and rarely modified data (e.g., categories list, paginated products index) are cached using `Cache::remember`. This drastically reduces database query load.
- **Queues**: Email sending, image processing, and notification dispatching are offloaded to background queues (Redis/Database driven) to handle high-throughput gracefully.
- **Frontend Optimization**: The Vue app relies on Vite for rapid HMR during development and aggressive code-splitting and asset minification for production builds.

### 6. Security Considerations
- **Data Integrity**: Database transactions are utilized where necessary. Mass assignment protection is enforced on all Eloquent models. Role-Based Access Control (RBAC) via Laravel Gates ensures users can only perform authorized actions.
- **Performance**: N+1 query problems are prevented using eager loading (`Product::with('category')`). Database indexes are placed on highly filtered columns.
- **Error Handling**: A centralized exception handler formats all API errors into a standardized JSON response (`{ success, message, errors }`). The Vue frontend utilizes Axios interceptors to catch 403, 422, and 500 errors and displays user-friendly toast notifications via `vue-toastification` without crashing the application.

---

*Note on framework versions: The current architecture utilizes standard modern Laravel practices matching the requested patterns.*
