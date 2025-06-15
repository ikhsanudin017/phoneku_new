import axios from 'axios'

const api = axios.create({
  baseURL: '/api',  // Gunakan path relatif karena menggunakan proxy
  timeout: 15000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// Request interceptor untuk token
api.interceptors.request.use(
  async (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor untuk handling error
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config

    // Jika error unauthorized dan bukan retry
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true
      
      // Clear local storage dan redirect ke login
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      window.location.href = '/admin/login'
      return Promise.reject(error)
    }
    
    return Promise.reject(error)
  }
)

export async function initializeCsrf() {
  try {
    await axios.get('/sanctum/csrf-cookie', {
      withCredentials: true,
      baseURL: ''  // Use root path for CSRF
    })
    
    const token = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))
      ?.split('=')[1]
    
    if (token) {
      api.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(token)
      return true
    }
    return false
  } catch (error) {
    console.error('CSRF initialization failed:', error)
    return false
  }
}

export default api
