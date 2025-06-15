<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />
    
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-950 pt-24 pb-32 relative overflow-hidden">
      <div class="absolute inset-0 bg-grid-white/10"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <h1 class="text-4xl font-bold text-white mb-2 text-shadow-glow">Dashboard</h1>
        <p class="text-blue-200">Welcome back, {{ authStore.user?.name }}</p>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative pb-12">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6 backdrop-blur-lg bg-opacity-90 transform hover:scale-105 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Orders</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_orders }}</p>
            </div>
            <div class="p-3 bg-blue-500 bg-opacity-10 rounded-full">
              <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-500 mr-2">
                <i class="fas fa-arrow-up"></i>
                12%
              </span>
              <span class="text-gray-600">vs last month</span>
            </div>
          </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-lg p-6 backdrop-blur-lg bg-opacity-90 transform hover:scale-105 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</p>
            </div>
            <div class="p-3 bg-indigo-500 bg-opacity-10 rounded-full">
              <i class="fas fa-users text-2xl text-indigo-600"></i>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-500 mr-2">
                <i class="fas fa-arrow-up"></i>
                8%
              </span>
              <span class="text-gray-600">vs last month</span>
            </div>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-lg p-6 backdrop-blur-lg bg-opacity-90 transform hover:scale-105 transition-all duration-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Revenue</p>
              <p class="text-3xl font-bold text-gray-900">Rp {{ formatPrice(stats.total_revenue) }}</p>
            </div>
            <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
              <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
            </div>
          </div>
          <div class="mt-4">
            <div class="flex items-center text-sm">
              <span class="text-green-500 mr-2">
                <i class="fas fa-arrow-up"></i>
                15%
              </span>
              <span class="text-gray-600">vs last month</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders & Popular Products -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
          </div>
          <div class="p-6">
            <div class="flow-root">
              <ul class="-my-5 divide-y divide-gray-200">
                <li v-for="order in recentOrders" :key="order.id" class="py-5">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <img :src="getImageUrl(order.product_image)" alt="" class="h-12 w-12 rounded-lg object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        Order #{{ order.id }}
                      </p>
                      <p class="text-sm text-gray-500 truncate">
                        {{ order.customer_name }}
                      </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                      <p class="text-sm font-medium text-gray-900">
                        Rp {{ formatPrice(order.total) }}
                      </p>
                      <p :class="[
                        'text-xs font-medium px-2 py-1 rounded-full inline-block',
                        getStatusClass(order.status)
                      ]">
                        {{ order.status }}
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Popular Products -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Popular Products</h2>
          </div>
          <div class="p-6">
            <div class="flow-root">
              <ul class="-my-5 divide-y divide-gray-200">
                <li v-for="product in popularProducts" :key="product.id" class="py-5">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <img :src="getImageUrl(product.image)" alt="" class="h-12 w-12 rounded-lg object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ product.name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ product.sold }} sold
                      </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                      <p class="text-sm font-medium text-gray-900">
                        Rp {{ formatPrice(product.price) }}
                      </p>
                      <p class="text-xs text-gray-500">
                        Stock: {{ product.stock }}
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <ErrorAlert
      v-if="error"
      :message="error"
      @dismiss="error = null"
      class="fixed bottom-4 right-4"
    />

    <!-- Loading State -->
    <LoadingState 
      v-if="loading"
      message="Loading dashboard data..."
      class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import ErrorAlert from '@/components/ErrorAlert.vue'
import LoadingState from '@/components/LoadingState.vue'
import AdminHeader from '@/components/admin/AdminHeader.vue'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const error = ref(null)
const showProfileMenu = ref(false)

const stats = ref({
  total_orders: 0,
  total_users: 0,
  total_revenue: 0
})

const recentOrders = ref([])
const popularProducts = ref([])

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

const getImageUrl = (image) => {
  if (!image) return '/img/placeholder.png'
  return image.startsWith('http') ? image : `/storage/${image}`
}

const fetchDashboardData = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await api.get('/admin/dashboard')
    if (response.data.success) {
      stats.value = response.data.stats
      recentOrders.value = response.data.recent_orders
      popularProducts.value = response.data.popular_products
    } else {
      throw new Error(response.data.message || 'Failed to fetch dashboard data')
    }
  } catch (e) {
    console.error('Dashboard error:', e)
    error.value = e.response?.data?.message || 
                 (e.response?.status === 401 ? 'Please login again' : 'Failed to load dashboard data. Please check if the backend server is running.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
/* Background grid pattern */
.bg-grid-white\/10 {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Text shadow for glow effect */
.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7), 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Card hover effects */
.transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Animation for loading states */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style>
