import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 30000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// Interceptor untuk selalu mengirim token dari localStorage
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

export async function initializeCsrf() {
  try {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true
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
