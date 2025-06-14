import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export const productsAPI = {
  async getAll(params = {}) {
    const authStore = useAuthStore()
    return axios.get(`${BASE_URL}/api/admin/products`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      },
      params
    })
  },

  async create(formData) {
    const authStore = useAuthStore()
    return axios.post(`${BASE_URL}/api/admin/products`, formData, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    })
  },

  async update(id, formData) {
    const authStore = useAuthStore()
    formData.append('_method', 'PUT') // For Laravel to handle as PUT request
    return axios.post(`${BASE_URL}/api/admin/products/${id}`, formData, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'multipart/form-data'
      }
    })
  },

  async delete(id) {
    const authStore = useAuthStore()
    return axios.delete(`${BASE_URL}/api/admin/products/${id}`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
  }
}
