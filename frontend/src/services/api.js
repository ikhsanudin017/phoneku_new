import axios from 'axios'

// Create axios instance
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  withCredentials: true // Required for CSRF cookie handling
})

// Initialize CSRF protection
export const initializeCsrf = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie', {
      withCredentials: true
    })
    return true
  } catch (error) {
    console.error('Failed to initialize CSRF token:', error)
    return false
  }
}

// Request interceptor
api.interceptors.request.use(
  async (config) => {
    // For multipart/form-data, let the browser handle Content-Type
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    }

    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Unauthorized, clear auth data
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    } else if (error.response?.status === 419) {
      // CSRF token mismatch, try to refresh
      const refreshed = await initializeCsrf()
      if (refreshed) {
        // Retry the original request
        return api(error.config)
      }
    }
    return Promise.reject(error)
  }
)

// Auth API
export const authAPI = {
  login: (credentials) => api.post('/auth/login', credentials),
  adminLogin: (credentials) => api.post('/auth/admin/login', credentials),
  register: (userData) => api.post('/auth/register', userData),
  adminRegister: (userData) => api.post('/auth/admin/register', userData),
  logout: () => api.post('/auth/logout'),
  forgotPassword: (email) => api.post('/auth/forgot-password', { email }),
  resetPassword: (data) => api.post('/auth/reset-password', data),
  user: () => api.get('/auth/user')
}

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
