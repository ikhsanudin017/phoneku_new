<template>
  <!-- Top Wave and Banner -->
  <div class="bg-blue-500 h-48 md:h-64 relative overflow-hidden">
    <!-- Wave Decoration -->
    <div class="absolute bottom-0 left-0 right-0">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full" preserveAspectRatio="none">
        <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
      </svg>
    </div>
    <!-- Banner Image -->
    <div class="absolute top-[5%] left-[calc(50%+1cm)] transform -translate-x-1/2 w-full max-w-3xl mx-auto z-0 pointer-events-none">
      <div class="relative w-full h-full" style="overflow: hidden;">
        <img src="/img/banner4.png" alt="Smartphones" class="object-contain w-full h-auto max-h-[300px] md:max-h-[350px] lg:max-h-[400px]">
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8 -mt-48 relative z-10">
    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="border-b border-gray-200 pb-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Purchase History</h2>
        <p class="text-gray-600 mt-1">View all your past orders and their status</p>
      </div>

      <!-- Filter and Search -->
      <div class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search orders by product name or order ID..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @keyup.enter="fetchOrders"
            />
          </div>
          <select
            v-model="statusFilter"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="fetchOrders"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading orders...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="orders.length === 0" class="text-center py-12">
        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mt-4">No orders found</h3>
        <p class="text-gray-600 mb-6">You haven't made any purchases yet.</p>
        <router-link to="/products" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
          Start Shopping
        </router-link>
      </div>

      <!-- Orders List -->
      <div v-else class="space-y-6">
        <div v-for="order in orders" :key="order.id" class="bg-white border border-gray-200 rounded-lg shadow-sm">
          <!-- Order Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
              <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-6">
                <div>
                  <span class="text-sm text-gray-500">Order ID</span>
                  <p class="font-semibold text-gray-900">#{{ order.order_number || order.id }}</p>
                </div>
                <div>
                  <span class="text-sm text-gray-500">Order Date</span>
                  <p class="font-medium text-gray-900">{{ formatDate(order.created_at) }}</p>
                </div>
                <div>
                  <span class="text-sm text-gray-500">Total</span>
                  <p class="font-bold text-blue-600">Rp {{ formatPrice(order.total_amount) }}</p>
                </div>
              </div>
              <div class="mt-4 md:mt-0">
                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                  {{ getStatusText(order.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Order Items -->
          <div class="px-6 py-4">
            <div class="space-y-4">
              <div v-for="item in order.items" :key="item.id" class="flex items-center space-x-4">
                <img
                  :src="getImageUrl(item.product?.image)"
                  :alt="item.product?.name"
                  class="w-16 h-16 object-cover rounded-md border border-gray-200"
                />
                <div class="flex-grow">
                  <h4 class="font-semibold text-gray-900">{{ item.product?.name || 'Product not found' }}</h4>
                  <p class="text-sm text-gray-600">Quantity: {{ item.quantity }}</p>
                  <p class="text-sm font-medium text-blue-600">Rp {{ formatPrice(item.price) }} each</p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-gray-900">Rp {{ formatPrice(item.price * item.quantity) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-2 md:space-y-0">
              <div class="text-sm text-gray-600">
                <span v-if="order.delivery_address">Delivered to: {{ order.delivery_address }}</span>
                <span v-if="order.tracking_number" class="block">Tracking: {{ order.tracking_number }}</span>
              </div>
              <div class="flex space-x-3">
                <button
                  @click="viewOrderDetails(order)"
                  class="px-4 py-2 text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50 text-sm"
                >
                  View Details
                </button>
                <button
                  v-if="order.status === 'delivered'"
                  @click="reorderItems(order)"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                >
                  Order Again
                </button>
                <button
                  v-if="order.status === 'pending'"
                  @click="cancelOrder(order)"
                  class="px-4 py-2 text-red-600 border border-red-600 rounded-md hover:bg-red-50 text-sm"
                >
                  Cancel Order
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
        <nav class="flex space-x-2">
          <button
            v-for="page in paginationPages"
            :key="page"
            @click="changePage(page)"
            :class="[
              'px-3 py-2 rounded-md',
              page === pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            ]"
            :disabled="page === '...'"
          >
            {{ page }}
          </button>
        </nav>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg">
        {{ successMessage }}
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { userAPI } from '@/services/api'

const router = useRouter()
const cartStore = useCartStore()

const orders = ref([])
const loading = ref(false)
const searchQuery = ref('')
const statusFilter = ref('')
const pagination = ref(null)
const successMessage = ref('')
const errorMessage = ref('')

const fetchOrders = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: 10,
      search: searchQuery.value,
      status: statusFilter.value
    }

    const response = await userAPI.getOrders(params)
    if (response.data.success) {
      orders.value = response.data.data
      pagination.value = response.data.pagination
    }
  } catch (error) {
    errorMessage.value = 'Failed to load orders'
    console.error('Fetch orders error:', error)
    clearMessages()
  } finally {
    loading.value = false
  }
}

const viewOrderDetails = (order) => {
  // For now, just show an alert with order details
  // In a real app, this would navigate to a detailed order page
  alert(`Order Details:\nOrder ID: #${order.order_number || order.id}\nStatus: ${getStatusText(order.status)}\nTotal: Rp ${formatPrice(order.total_amount)}\nDate: ${formatDate(order.created_at)}`)
}

const reorderItems = async (order) => {
  try {
    for (const item of order.items) {
      if (item.product) {
        await cartStore.addToCart(item.product.id, { quantity: item.quantity })
      }
    }
    successMessage.value = 'Items added to cart successfully!'
    clearMessages()
  } catch (error) {
    errorMessage.value = 'Failed to add items to cart'
    console.error('Reorder error:', error)
    clearMessages()
  }
}

const cancelOrder = async (order) => {
  if (!confirm('Are you sure you want to cancel this order?')) return

  try {
    const response = await userAPI.cancelOrder(order.id)
    if (response.data.success) {
      order.status = 'cancelled'
      successMessage.value = 'Order cancelled successfully'
    } else {
      errorMessage.value = response.data.message || 'Failed to cancel order'
    }
    clearMessages()
  } catch (error) {
    errorMessage.value = 'Failed to cancel order'
    console.error('Cancel order error:', error)
    clearMessages()
  }
}

const changePage = (page) => {
  if (page !== '...' && page !== pagination.value.current_page) {
    fetchOrders(page)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/placeholder-image.jpg'
  return `http://localhost:8000/storage/${imagePath}`
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return texts[status] || 'Unknown'
}

const paginationPages = computed(() => {
  if (!pagination.value) return []

  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []

  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')

    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }

    if (current < last - 2) pages.push('...')
    pages.push(last)
  }

  return pages
})

const clearMessages = () => {
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 3000)
}

onMounted(() => {
  fetchOrders()
})
</script>
