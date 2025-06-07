<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <nav class="bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="text-2xl font-bold text-blue-600">
              PhoneKu Admin
            </router-link>
          </div>

          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/admin/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</router-link>
            <router-link to="/admin/products" class="text-gray-700 hover:text-blue-600">Products</router-link>
            <router-link to="/admin/orders" class="text-blue-600 font-medium">Orders</router-link>
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600">Users</router-link>
            <router-link to="/admin/chat" class="text-gray-700 hover:text-blue-600">Messages</router-link>

            <div class="relative" ref="profileDropdown">
              <button @click="showProfileMenu = !showProfileMenu" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                <span>{{ authStore.user?.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>

              <div v-if="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <router-link to="/welcome" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Store</router-link>
                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Panel Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-t-lg text-white py-5 px-8">
        <div>
          <h2 class="text-2xl font-bold pb-2">Order Management</h2>
          <h5 class="opacity-70">View and manage customer orders</h5>
        </div>
      </div>

      <!-- Error Alerts -->
      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ errorMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="errorMessage = ''">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Close</title>
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Orders List</h3>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="loading">
                <td colspan="6" class="px-6 py-4 text-center">
                  <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                  <p class="mt-2 text-sm text-gray-600">Loading orders...</p>
                </td>
              </tr>
              <tr v-else-if="orders.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  No orders found
                </td>
              </tr>
              <template v-else>
                <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.customer_name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ formatPrice(order.total) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ order.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button @click="viewOrderDetails(order)" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                    <button @click="updateOrderStatus(order)" class="text-green-600 hover:text-green-900">Update Status</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.total > pagination.per_page" class="px-4 py-3 flex items-center justify-between border-t border-gray-200">
          <div class="flex-1 flex justify-between items-center">
            <p class="text-sm text-gray-700">
              Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
            </p>
            <div>
              <button
                @click="fetchOrders(pagination.current_page - 1)"
                :disabled="!pagination.prev_page_url"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-3',
                  { 'opacity-50 cursor-not-allowed': !pagination.prev_page_url }
                ]"
              >
                Previous
              </button>
              <button
                @click="fetchOrders(pagination.current_page + 1)"
                :disabled="!pagination.next_page_url"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50',
                  { 'opacity-50 cursor-not-allowed': !pagination.next_page_url }
                ]"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { format } from 'date-fns'

const router = useRouter()
const authStore = useAuthStore()
const showProfileMenu = ref(false)

// State management
const orders = ref([])
const loading = ref(true)
const errorMessage = ref('')
const pagination = ref(null)

// Fetch orders
const fetchOrders = async (page = 1) => {
  try {
    loading.value = true
    const response = await axios.get('/api/admin/orders', {
      params: { page }
    })
    orders.value = response.data.data
    pagination.value = response.data.meta
  } catch (error) {
    errorMessage.value = 'Failed to load orders. Please try again.'
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

// Format helpers
const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatDate = (date) => {
  return format(new Date(date), 'dd MMM yyyy HH:mm')
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

// Actions
const viewOrderDetails = (order) => {
  // Implement order details view
  console.log('View order:', order.id)
}

const updateOrderStatus = (order) => {
  // Implement status update
  console.log('Update status for order:', order.id)
}

// Lifecycle hooks
onMounted(() => {
  fetchOrders()
})
</script>
