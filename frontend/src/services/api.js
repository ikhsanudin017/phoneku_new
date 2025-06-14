import axios from 'axios'

const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

// Create axios instance with default config
export const api = axios.create({
    baseURL: `${BASE_URL}/api`,
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
})

// Initialize CSRF token with retries
export const initializeCsrf = async (retries = 3) => {
    for (let i = 0; i < retries; i++) {
        try {
            console.log(`Initializing CSRF token (attempt ${i + 1}/${retries})`)
            
            // First, ensure we have the CSRF cookie
            await axios.get(`${BASE_URL}/sanctum/csrf-cookie`, {
                withCredentials: true,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })

            // Wait a bit longer for cookies to be set
            await new Promise(resolve => setTimeout(resolve, 1000))

            // Get XSRF-TOKEN from cookies
            const token = document.cookie
                .split('; ')
                .find(row => row.startsWith('XSRF-TOKEN='))
                ?.split('=')[1]

            if (token) {
                const decodedToken = decodeURIComponent(token)
                // Set both the X-CSRF-TOKEN and X-XSRF-TOKEN headers
                api.defaults.headers.common['X-CSRF-TOKEN'] = decodedToken
                api.defaults.headers.common['X-XSRF-TOKEN'] = decodedToken
                console.log('CSRF token initialized successfully:', decodedToken)
                return true
            }
            
            console.warn(`No CSRF token found in cookies (attempt ${i + 1}). Cookies:`, document.cookie)
            
            // Wait longer before retrying
            if (i < retries - 1) {
                await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)))
            }
        } catch (error) {
            console.error(`Failed to initialize CSRF token (attempt ${i + 1}):`, error)
            if (i === retries - 1) throw error
            await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)))
        }
    }
    throw new Error('Failed to initialize CSRF token after ' + retries + ' attempts')
}

// Add request interceptor to ensure CSRF token is present
api.interceptors.request.use(async config => {
    // If no CSRF token, try to initialize it
    if (!api.defaults.headers.common['X-XSRF-TOKEN']) {
        try {
            await initializeCsrf()
        } catch (error) {
            console.error('Failed to initialize CSRF token in interceptor:', error)
        }
    }
    return config
})

// Add response interceptor to handle CSRF token errors
api.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch, try to refresh it
            try {
                await initializeCsrf(1)
                // Retry the original request
                return api(error.config)
            } catch (e) {
                console.error('Failed to refresh CSRF token:', e)
            }
        }
        return Promise.reject(error)
    }
)

// User API endpoints
export const userAPI = {
    // Profile Management
    getProfile: async () => {
        return api.get('/user/profile')
    },

    updateProfile: async (profileData) => {
        return api.put('/user/profile', profileData)
    },

    // Email Management
    updateEmail: async (data) => {
        return api.put('/user/email', {
            email: data.email,
            password: data.password
        })
    },

    verifyEmail: async (token) => {
        return api.post('/email/verify', { token })
    },

    resendVerificationEmail: async () => {
        return api.post('/email/verification-notification')
    },

    // Password Management
    updatePassword: async (data) => {
        return api.put('/user/password', {
            current_password: data.currentPassword,
            password: data.newPassword,
            password_confirmation: data.confirmPassword
        })
    },

    forgotPassword: async (email) => {
        return api.post('/forgot-password', { email })
    },

    resetPassword: async (data) => {
        return api.post('/reset-password', {
            token: data.token,
            email: data.email,
            password: data.password,
            password_confirmation: data.passwordConfirmation
        })
    },

    // Account Management
    deleteAccount: async (password) => {
        return api.delete('/user/account', {
            data: { password }
        })
    },

    // OTP & 2FA
    generateOtp: async () => {
        return api.post('/user/otp/generate')
    },

    verifyOtp: async (otp) => {
        return api.post('/user/otp/verify', { otp })
    }
}

// Admin API endpoints
export const adminAPI = {
    // Authentication
    login: async (credentials) => {
        return api.post('/auth/admin/login', credentials)
    },

    register: async (data) => {
        return api.post('/auth/admin/register', data)
    },

    forgotPassword: async (email) => {
        return api.post('/auth/admin/forgot-password', { email })
    },

    resetPassword: async (data) => {
        return api.post('/auth/admin/reset-password', data)
    },

    setAuthToken: (token) => {
        if (token) {
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        } else {
            delete api.defaults.headers.common['Authorization']
        }
    },

    logout: async () => {
        return api.post('/admin/logout')
    },

    // Dashboard
    getDashboardData: async () => {
        return api.get('/admin/dashboard')
    },

    // Users Management
    getUsers: async (params = {}) => {
        return api.get('/admin/users', { params })
    },

    createUser: async (userData) => {
        return api.post('/admin/users', userData)
    },

    updateUser: async (id, userData) => {
        return api.put(`/admin/users/${id}`, userData)
    },

    deleteUser: async (id) => {
        return api.delete(`/admin/users/${id}`)
    },

    // User Status Management
    activateUser: async (id) => {
        return api.put(`/admin/users/${id}/activate`)
    },

    deactivateUser: async (id) => {
        return api.put(`/admin/users/${id}/deactivate`)
    }
}

// Products API endpoints
export const productsAPI = {
    // Product Retrieval
    getAll: async (params = {}) => {
        return api.get('/admin/products', { params })
    },

    getById: async (id) => {
        return api.get(`/admin/products/${id}`)
    },

    // Product Management
    create: async (productData) => {
        const formData = new FormData()
        for (const key in productData) {
            if (key === 'images' && Array.isArray(productData.images)) {
                productData.images.forEach(image => {
                    formData.append('images[]', image)
                })
            } else {
                formData.append(key, productData[key])
            }
        }
        return api.post('/admin/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },

    update: async (id, productData) => {
        const formData = new FormData()
        for (const key in productData) {
            if (key === 'images' && Array.isArray(productData.images)) {
                productData.images.forEach(image => {
                    formData.append('images[]', image)
                })
            } else {
                formData.append(key, productData[key])
            }
        }
        formData.append('_method', 'PUT') // Laravel form method spoofing
        return api.post(`/admin/products/${id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },

    delete: async (id) => {
        return api.delete(`/admin/products/${id}`)
    },

    // Product Image Management
    uploadImage: async (productId, image) => {
        const formData = new FormData()
        formData.append('image', image)
        return api.post(`/admin/products/${productId}/images`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },

    deleteImage: async (productId, imageId) => {
        return api.delete(`/admin/products/${productId}/images/${imageId}`)
    }
}

// Orders API endpoints
export const ordersAPI = {
    getAll: async (params = {}) => {
        return api.get('/admin/orders', { params })
    },

    getById: async (id) => {
        return api.get(`/admin/orders/${id}`)
    },

    updateStatus: async (id, status) => {
        return api.put(`/admin/orders/${id}/status`, { status })
    },

    getStatistics: async (period = 'month') => {
        return api.get('/admin/orders/statistics', { params: { period } })
    }
}

// Cart API endpoints
export const cartAPI = {
    // Get cart items
    getItems: async () => {
        return api.get('/cart')
    },

    // Add item to cart
    addItem: async (productId, data = {}) => {
        return api.post(`/cart/add/${productId}`, data)
    },

    // Update cart item quantity
    updateQuantity: async (itemId, quantity) => {
        return api.post(`/cart/update/${itemId}`, { quantity })
    },

    // Remove item from cart
    removeItem: async (itemId) => {
        return api.delete(`/cart/remove/${itemId}`)
    },

    // Clear cart
    clear: async () => {
        return api.delete('/cart')
    },

    // Get cart count
    getCount: async () => {
        return api.get('/cart/count')
    }
}

// Chat API endpoints
export const chatAPI = {
    // Get all conversations
    getConversations: async () => {
        return api.get('/admin/chat/conversations')
    },

    // Get messages for a specific conversation
    getMessages: async (conversationId) => {
        return api.get(`/admin/chat/conversations/${conversationId}`)
    },

    // Send a message
    sendMessage: async (conversationId, message) => {
        return api.post(`/admin/chat/conversations/${conversationId}/messages`, { message })
    },

    // Mark conversation as read
    markAsRead: async (conversationId) => {
        return api.put(`/admin/chat/conversations/${conversationId}/read`)
    },

    // Get unread count
    getUnreadCount: async () => {
        return api.get('/admin/chat/unread-count')
    }
}

// Auth API endpoints
export const authAPI = {
    // Admin authentication
    adminLogin: async (credentials) => {
        return api.post('/admin/login', credentials)
    },

    adminRegister: async (data) => {
        return api.post('/admin/register', data)
    },

    // Regular user authentication
    login: async (credentials) => {
        return api.post('/login', credentials)
    },

    register: async (data) => {
        return api.post('/register', data)
    },

    logout: async () => {
        return api.post('/logout')
    },

    // Password reset
    forgotPassword: async (email) => {
        return api.post('/forgot-password', { email })
    },

    resetPassword: async (data) => {
        return api.post('/reset-password', data)
    },

    // Verify reset token
    verifyResetToken: async (token) => {
        return api.get(`/reset-password/${token}`)
    }
}

export default api


