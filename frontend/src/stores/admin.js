import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import adminAPI from '@/services/api/admin'
import { useAuthStore } from './auth'

export const useAdminStore = defineStore('admin', () => {
  // State
  const loading = ref(false)
  const error = ref(null)
  const dashboardStats = ref(null)
  const recentOrders = ref([])
  const recentUsers = ref([])
  const salesStats = ref(null)

  // Get auth store
  const authStore = useAuthStore()

  // Getters
  const isLoading = computed(() => loading.value)
  const hasError = computed(() => error.value !== null)

  // Actions
  const clearError = () => {
    error.value = null
  }

  const handleError = (e) => {
    console.error('Admin store error:', e)
    error.value = e.response?.data?.message || 'An error occurred'
    throw e
  }

  // Dashboard actions
  const loadDashboardData = async () => {
    if (!authStore.isAdmin) return

    loading.value = true
    try {
      const [stats, orders, users, sales] = await Promise.all([
        adminAPI.getDashboardStats(),
        adminAPI.getRecentOrders(),
        adminAPI.getRecentUsers(),
        adminAPI.getSalesStats()
      ])

      dashboardStats.value = stats.data
      recentOrders.value = orders.data
      recentUsers.value = users.data
      salesStats.value = sales.data

      return {
        stats: stats.data,
        orders: orders.data,
        users: users.data,
        sales: sales.data
      }
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  // Product management
  const createProduct = async (productData) => {
    loading.value = true
    try {
      const response = await adminAPI.createProduct(productData)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id, productData) => {
    loading.value = true
    try {
      const response = await adminAPI.updateProduct(id, productData)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (id) => {
    loading.value = true
    try {
      const response = await adminAPI.deleteProduct(id)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  // User management
  const getUsers = async (params = {}) => {
    loading.value = true
    try {
      const response = await adminAPI.getUsers(params)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const updateUser = async (id, userData) => {
    loading.value = true
    try {
      const response = await adminAPI.updateUser(id, userData)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  // Order management
  const getOrders = async (params = {}) => {
    loading.value = true
    try {
      const response = await adminAPI.getOrders(params)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const updateOrder = async (id, orderData) => {
    loading.value = true
    try {
      const response = await adminAPI.updateOrder(id, orderData)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  // Chat management
  const getConversations = async (params = {}) => {
    loading.value = true
    try {
      const response = await adminAPI.getConversations(params)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const getConversationMessages = async (conversationId, params = {}) => {
    loading.value = true
    try {
      const response = await adminAPI.getConversationMessages(conversationId, params)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  const sendMessage = async (conversationId, message) => {
    loading.value = true
    try {
      const response = await adminAPI.sendMessage(conversationId, message)
      return response.data
    } catch (e) {
      handleError(e)
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    loading,
    error,
    dashboardStats,
    recentOrders,
    recentUsers,
    salesStats,

    // Getters
    isLoading,
    hasError,

    // Actions
    clearError,
    loadDashboardData,
    createProduct,
    updateProduct,
    deleteProduct,
    getUsers,
    updateUser,
    getOrders,
    updateOrder,
    getConversations,
    getConversationMessages,
    sendMessage
  }
})
