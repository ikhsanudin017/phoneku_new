import api, { initializeCsrf } from './index'

// Helper function to handle API errors
const handleApiError = async (error, retryCallback) => {
  if (error.response?.status === 419) {
    await initializeCsrf()
    return await retryCallback()
  }
  throw error
}

// Admin API service
const adminAPI = {
  // Auth methods
  login: async (credentials) => {
    try {
      await initializeCsrf()
      const response = await api.post('/auth/admin/login', credentials)
      if (response.data?.success && response.data?.token) {
        api.defaults.headers.common.Authorization = `Bearer ${response.data.token}`
      }
      return response
    } catch (error) {
      return handleApiError(error, () => adminAPI.login(credentials))
    }
  },

  register: async (data) => {
    try {
      await initializeCsrf()
      return await api.post('/auth/admin/register', data)
    } catch (error) {
      return handleApiError(error, () => adminAPI.register(data))
    }
  },

  // Dashboard methods
  getDashboard: async () => {
    try {
      await initializeCsrf()
      return await api.get('/admin/dashboard')
    } catch (error) {
      return handleApiError(error, () => adminAPI.getDashboard())
    }
  }
}

export default adminAPI
