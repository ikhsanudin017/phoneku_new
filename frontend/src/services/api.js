// src/services/api.js

import axios from 'axios';

// Base URL untuk API dari variabel environment atau default
const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

// Instance axios dasar untuk permintaan non-API seperti csrf-cookie
const axiosInstance = axios.create({
  baseURL: BASE_URL,
  withCredentials: true,
});

// Instance API utama
const api = axios.create({
  baseURL: `${BASE_URL}/api`,
  withCredentials: true, // WAJIB untuk mengirim cookie
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Fungsi untuk menginisialisasi perlindungan CSRF
export const initializeCsrf = async () => {
  try {
    await axiosInstance.get('/sanctum/csrf-cookie');
    console.log('CSRF cookie initialized.');
    return true;
  } catch (error) {
    console.error('Failed to initialize CSRF token:', error);
    return false;
  }
};

// Interceptor untuk menangani error, terutama 419 (CSRF Mismatch)
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 419) {
      console.log('CSRF token mismatch. Refreshing token and retrying...');
      const refreshed = await initializeCsrf();
      if (refreshed) {
        return api(error.config); // Coba lagi permintaan asli
      }
    }
    return Promise.reject(error);
  }
);

// Helper function untuk memanggil API setelah memastikan CSRF
const performApiRequest = async (request) => {
  await initializeCsrf();
  return request();
};

// Kumpulan fungsi API yang sudah aman CSRF
export const authAPI = {
  login: (credentials) => performApiRequest(() => api.post('/auth/login', credentials)),
  adminLogin: (credentials) => performApiRequest(() => api.post('/auth/admin/login', credentials)),
  register: (userData) => performApiRequest(() => api.post('/auth/register', userData)),
  adminRegister: (userData) => performApiRequest(() => api.post('/auth/admin/register', userData)),
  logout: () => performApiRequest(() => api.post('/auth/logout')),
  forgotPassword: (email) => performApiRequest(() => api.post('/auth/forgot-password', { email })),
  resetPassword: (data) => performApiRequest(() => api.post('/auth/reset-password', data)),
  getUser: () => api.get('/auth/user'), // GET request tidak butuh CSRF, tapi tidak masalah
};



// Admin API
export const adminAPI = {
  // Users
  getUsers: (params) => api.get('/admin/users', { params }),
  getUserDetails: (id) => api.get(`/admin/users/${id}`),
  createUser: (userData) => api.post('/admin/users', userData),
  updateUser: (id, userData) => api.put(`/admin/users/${id}`, userData),
  deleteUser: (id) => api.delete(`/admin/users/${id}`),
  updateUserStatus: (id, status) => api.put(`/admin/users/${id}/status`, { status }),
  resetUserPassword: (id) => api.post(`/admin/users/${id}/reset-password`),

  // Products
  getProducts: (params) => api.get('/admin/products', { params }),
  createProduct: (formData) => api.post('/admin/products', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  updateProduct: (id, formData) => api.post(`/admin/products/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  }),
  deleteProduct: (id) => api.delete(`/admin/products/${id}`),

  // Orders
  getOrders: (params) => api.get('/admin/orders', { params }),
  updateOrderStatus: (id, status) => api.put(`/admin/orders/${id}/status`, { status }),

  // Dashboard
  getDashboardStats: () => api.get('/admin/dashboard/stats'),
  getRecentOrders: () => api.get('/admin/dashboard/recent-orders'),
  getPopularProducts: () => api.get('/admin/dashboard/popular-products'),
}

// Chat API
export const chatAPI = {
  getChats: () => api.get('/admin/chats'),
  getChatMessages: (userId) => api.get(`/admin/chats/${userId}/messages`),
  sendMessage: (userId, message) => api.post(`/admin/chats/${userId}/messages`, { message })
}

// Products API
export const productsAPI = {
  getAll: (params) => api.get('/products', { params }),
  getOne: (id) => api.get(`/products/${id}`),
  create: (formData) => api.post('/products', formData),
  update: (id, formData) => api.put(`/products/${id}`, formData),
  delete: (id) => api.delete(`/products/${id}`)
}

// User Profile API
export const userAPI = {
  getProfile: () => api.get('/user/profile'),
  updateProfile: (data) => api.put('/user/profile', data),
  updateAvatar: (formData) => api.post('/user/profile/avatar', formData),
  changePassword: (data) => api.put('/user/password', data),
  deleteAccount: () => api.delete('/user/account')
}

// Cart API
export const cartAPI = {
  getItems: () => api.get('/cart'),
  addItem: (productId, quantity) => api.post('/cart/items', { product_id: productId, quantity }),
  updateItem: (itemId, quantity) => api.put(`/cart/items/${itemId}`, { quantity }),
  removeItem: (itemId) => api.delete(`/cart/items/${itemId}`),
  clear: () => api.delete('/cart')
}

// Orders API
export const ordersAPI = {
  create: (data) => api.post('/orders', data),
  getAll: () => api.get('/orders'),
  getOne: (id) => api.get(`/orders/${id}`),
  cancel: (id) => api.put(`/orders/${id}/cancel`)
}

export default api
