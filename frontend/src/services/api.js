import axios from 'axios'

// Create axios instance
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Clear token and redirect to login
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth API
export const authAPI = {
  // User auth
  login: (credentials) => api.post('/auth/login', credentials),
  register: (userData) => api.post('/auth/register', userData),
  logout: () => api.post('/auth/logout'),
  getUser: () => api.get('/auth/user'),

  // Password reset
  forgotPassword: (data) => api.post('/auth/forgot-password', data),
  resetPassword: (data) => api.post('/auth/reset-password', data),

  // Admin auth
  adminLogin: (credentials) => api.post('/auth/admin/login', credentials),
  adminRegister: (userData) => api.post('/auth/admin/register', userData),

  // Admin-specific password reset methods
  adminForgotPassword: (data) => api.post('/auth/forgot-password', data),
  adminResetPassword: (data) => api.post('/auth/reset-password', data),
}

// Products API
export const productsAPI = {
  getAll: (params = {}) => api.get('/products', { params }),
  getById: (id) => api.get(`/products/${id}`),
  getFeatured: (params = {}) => api.get('/products/featured', { params }),
  search: (params = {}) => api.get('/products/search', { params }),

  // Admin only
  create: (productData) => api.post('/admin/products', productData),
  update: (id, productData) => api.put(`/admin/products/${id}`, productData),
  delete: (id) => api.delete(`/admin/products/${id}`),
}

// Cart API
export const cartAPI = {
  getItems: () => api.get('/cart'),
  addItem: (productId, data) => api.post(`/cart/add/${productId}`, data),
  updateQuantity: (itemId, quantity) => api.put(`/cart/${itemId}`, { quantity }),
  removeItem: (itemId) => api.delete(`/cart/${itemId}`),
  getCount: () => api.get('/cart/count'),
  clear: () => api.delete('/cart'),
}

// User API
export const userAPI = {
  getProfile: () => api.get('/user/profile'),
  updateProfile: (data) => api.put('/user/profile', data),
  changePassword: (data) => api.put('/user/change-password', data),

  // Email change with OTP
  changeEmailRequest: (data) => api.post('/user/change-email/request', data),
  changeEmailVerify: (data) => api.post('/user/change-email/verify', data),

  // Phone change with OTP
  changePhoneRequest: (data) => api.post('/user/change-phone/request', data),
  changePhoneVerify: (data) => api.post('/user/change-phone/verify', data),

  // Order management
  getOrders: (params) => api.get('/user/orders', { params }),
  getOrder: (id) => api.get(`/user/orders/${id}`),
  cancelOrder: (id) => api.post(`/user/orders/${id}/cancel`),
}

// Admin API
export const adminAPI = {
  getDashboard: () => api.get('/admin/dashboard'),
  getStats: () => api.get('/admin/stats'),

  // User management
  getUsers: (params = {}) => api.get('/admin/users', { params }),
  createUser: (userData) => api.post('/admin/users', userData),
  updateUser: (id, userData) => api.put(`/admin/users/${id}`, userData),
  deleteUser: (id) => api.delete(`/admin/users/${id}`),
}

// Chat API
export const chatAPI = {
  getMessages: (receiverId) => api.get(`/chat/messages/${receiverId}`),
  sendMessage: (messageData) => api.post('/chat/send', messageData),
  getUnreadCount: () => api.get('/chat/unread-count'),
  markAsRead: (senderId) => api.post('/chat/mark-read', { sender_id: senderId }),

  // Customer
  getCustomerChat: () => api.get('/chat/customer'),

  // Admin
  getAdminChat: () => api.get('/admin/chat'),
}

export default api
