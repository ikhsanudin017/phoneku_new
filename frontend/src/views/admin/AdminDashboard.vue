<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Admin Navigation -->
    <nav class="bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="text-2xl font-bold text-white">
              PhoneKu Admin
            </router-link>
            <h5 class="text-white opacity-70 ml-4">Welcome</h5>
          </div>

          <div class="hidden md:flex items-center space-x-4">
            <router-link to="/admin/dashboard" class="text-white font-medium">Dashboard</router-link>
            <router-link to="/admin/products" class="text-white hover:text-gray-200">Products</router-link>
            <router-link to="/admin/users" class="text-white hover:text-gray-200">Users</router-link>
            <router-link to="/admin/chat" class="text-white hover:text-gray-200">Messages</router-link>

            <button class="btn btn-white border border-white rounded-full px-4 py-2 text-sm mr-2">Manage</button>
            <button class="btn btn-secondary rounded-full px-4 py-2 text-sm bg-gray-800 text-white">Add Customer</button>

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
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

      <!-- Error Message -->
      <div v-if="error" class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-1 text-sm text-red-700">
              {{ error }}
              <button @click="error = null" class="ml-2 text-red-800 hover:text-red-900 underline">Dismiss</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading dashboard...</p>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Stats Circles -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
          <div class="card-title text-xl font-bold mb-2">Overall Statistics</div>
          <div class="card-category text-gray-600 mb-6">Daily information about statistics in system</div>

          <div class="flex flex-wrap justify-around pb-2 pt-4">
            <div class="px-2 pb-2 pb-md-0 text-center">
              <div ref="circles1" class="circles-wrap"></div>
              <h6 class="font-bold mt-3 mb-0">Total Pembelian</h6>
              <div class="text-2xl font-bold text-primary">{{ stats.total_orders }}</div>
            </div>

            <div class="px-2 pb-2 pb-md-0 text-center">
              <div ref="circles2" class="circles-wrap"></div>
              <h6 class="font-bold mt-3 mb-0">Total Customers</h6>
              <div class="text-2xl font-bold text-green-600">{{ stats.total_users }}</div>
            </div>

            <div class="px-2 pb-2 pb-md-0 text-center">
              <div ref="circles3" class="circles-wrap"></div>
              <h6 class="font-bold mt-3 mb-0">Total Admin</h6>
              <div class="text-2xl font-bold text-red-600">{{ stats.total_admins || 0 }}</div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Users
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.total_users }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Orders
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      {{ stats.total_orders }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                      Total Revenue
                    </dt>
                    <dd class="text-lg font-medium text-gray-900">
                      Rp {{ formatPrice(stats.total_revenue) }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Recent Orders -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Recent Orders
              </h3>
              <div class="space-y-4">
                <div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Order #{{ order.id }}</p>
                    <p class="text-sm text-gray-500">{{ order.customer_name }}</p>
                    <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">Rp {{ formatPrice(order.total) }}</p>
                    <span :class="getStatusClass(order.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ order.status }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="mt-4">
                <router-link to="/admin/orders" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  View all orders →
                </router-link>
              </div>
            </div>
          </div>

          <!-- Popular Products -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Popular Products
              </h3>
              <div class="space-y-4">
                <div v-for="product in popularProducts" :key="product.id" class="flex items-center space-x-4">
                  <img :src="getImageUrl(product.image)" :alt="product.name" class="w-12 h-12 object-cover rounded-md">
                  <div class="flex-grow">
                    <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
                    <p class="text-sm text-gray-500">{{ product.sales_count }} sold</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">Rp {{ formatPrice(product.price) }}</p>
                  </div>
                </div>
              </div>
              <div class="mt-4">
                <router-link to="/admin/products" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  Manage products →
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <router-link to="/admin/products/create" class="bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 text-center font-medium">
                Add New Product
              </router-link>
              <router-link to="/admin/users" class="bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 text-center font-medium">
                Manage Users
              </router-link>
              <router-link to="/admin/orders" class="bg-purple-600 text-white px-4 py-3 rounded-md hover:bg-purple-700 text-center font-medium">
                View Orders
              </router-link>
              <router-link to="/admin/chat" class="bg-orange-600 text-white px-4 py-3 rounded-md hover:bg-orange-700 text-center font-medium">
                Customer Messages
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Circles from 'circles.js'

export default {
  name: 'AdminDashboard',
  setup() {
    const authStore = useAuthStore()
    const loading = ref(true)
    const showProfileMenu = ref(false)
    const error = ref(null)
    const stats = ref({
      total_orders: 0,
      total_users: 0,
      total_admins: 0,
      total_revenue: 0
    })
    const recentOrders = ref([])
    const popularProducts = ref([])
    const circles1 = ref(null)
    const circles2 = ref(null)
    const circles3 = ref(null)

    // Initialize circles with error handling
    const initializeCircles = () => {
      if (!circles1.value || !circles2.value || !circles3.value) {
        error.value = 'Failed to initialize statistics display. Please refresh the page.'
        return
      }

      try {
        // Create circles with error handling
        const createCircle = (element, value, maxValue, colors) => {
          try {
            return Circles.create({
              id: element,
              radius: 45,
              value: Math.min((value / maxValue) * 100, 100),
              maxValue: 100,
              width: 7,
              text: function(value) { return value + '%' },
              colors: ['#eee', colors],
              duration: 400,
              wrpClass: 'circles-wrp',
              textClass: 'circles-text'
            })
          } catch (e) {
            console.error('Error creating circle:', e)
            error.value = 'Failed to create statistics display'
            return null
          }
        }

        createCircle(circles1.value, stats.value.total_orders, 100, '#1f3bb3')
        createCircle(circles2.value, stats.value.total_users, 100, '#2ecc71')
        createCircle(circles3.value, stats.value.total_admins || 0, 10, '#e74c3c')
      } catch (e) {
        console.error('Error in initializeCircles:', e)
        error.value = 'Failed to initialize statistics. Please try refreshing the page.'
      }
    }

    // Watch for changes in stats
    watch(stats, () => {
      error.value = null // Clear previous errors
      initializeCircles()
    })

    onMounted(async () => {
      error.value = null // Clear any previous errors
      try {
        // Fetch dashboard data with timeout
        const controller = new AbortController()
        const timeoutId = setTimeout(() => controller.abort(), 10000) // 10 second timeout

        const response = await fetch('/api/admin/dashboard', {
          signal: controller.signal,
          headers: {
            'Authorization': `Bearer ${authStore.token}`,
            'Accept': 'application/json'
          }
        })

        clearTimeout(timeoutId)

        if (!response.ok) {
          if (response.status === 401) {
            await authStore.logout()
            error.value = 'Session expired. Please log in again.'
            return
          }
          throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()
        stats.value = data.stats
        recentOrders.value = data.recent_orders
        popularProducts.value = data.popular_products

        // Initialize circles after data is loaded
        initializeCircles()
      } catch (e) {
        console.error('Error fetching dashboard data:', e)
        error.value = e.name === 'AbortError'
          ? 'Request timed out. Please check your connection and try again.'
          : 'Failed to load dashboard data. Please try again.'
      } finally {
        loading.value = false
      }
    })

    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID').format(price)
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
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
      return image.startsWith('http') ? image : `/storage/${image}`
    }

    const logout = async () => {
      await authStore.logout()
    }

    return {
      loading,
      error,
      stats,
      recentOrders,
      popularProducts,
      showProfileMenu,
      authStore,
      circles1,
      circles2,
      circles3,
      formatPrice,
      formatDate,
      getStatusClass,
      getImageUrl,
      logout
    }
  }
}
</script>

<style scoped>
.bg-primary-gradient {
  background: linear-gradient(to right, #1f3bb3, #7f3bb3);
}

.text-primary {
  color: #1f3bb3;
}

.btn-white {
  color: white;
  transition: all 0.3s ease;
}

.btn-white:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.circles-wrap {
  position: relative;
  width: 90px;
  height: 90px;
  margin: 0 auto;
}

.circles-wrp {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.circles-text {
  font-size: 20px !important;
  font-family: inherit !important;
  font-weight: 600 !important;
  fill: #555 !important;
}
</style>
