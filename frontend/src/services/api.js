import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// Request interceptor to attach token
api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

import { useToast } from 'vue-toastification';

const toast = useToast();

// Response interceptor to handle errors
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            const status = error.response.status;
            const message = error.response.data?.message || 'An error occurred';

            if (status === 401) {
                localStorage.removeItem('token');
                window.dispatchEvent(new Event('auth:unauthorized'));
                toast.error('Session expired. Please login again.');
            } else if (status === 403) {
                toast.error('Permission Denied.');
            } else if (status === 422) {
                // For validation errors, we might want to just show a generic message or let the component handle it
                // toast.warning(message);
            } else if (status >= 500) {
                toast.error('Something went wrong on our end.');
            } else {
                toast.error(message);
            }
        } else {
            toast.error('Network Error.');
        }
        return Promise.reject(error);
    }
);

export default api;
