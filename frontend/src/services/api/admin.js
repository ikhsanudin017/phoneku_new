import api, { initializeCsrf } from './index'

const adminAPI = {
  login: async function(credentials) {
    await initializeCsrf()
    const response = await api.post('/auth/admin/login', credentials)
    if (response.data?.token) {
      api.defaults.headers.common.Authorization = `Bearer ${response.data.token}`
    }
    return response
  },

  register: async function(data) {
    await initializeCsrf()
    return await api.post('/auth/admin/register', data)
  },

  logout: async function() {
    await initializeCsrf()
    const response = await api.post('/auth/admin/logout')
    delete api.defaults.headers.common.Authorization
    return response
  }
}

export default adminAPI