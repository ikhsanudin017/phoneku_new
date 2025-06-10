<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-sm fixed top-0 left-0 right-0 z-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">
              PhoneKu Admin
            </router-link>
          </div>

          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/admin/dashboard" class="text-blue-600 font-medium">Dashboard</router-link>
            <router-link to="/admin/products" class="text-gray-700 hover:text-blue-600 transition-colors">Products</router-link>
            <router-link to="/admin/orders" class="text-gray-700 hover:text-blue-600 transition-colors">Orders</router-link>
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600 transition-colors">Users</router-link>
            <router-link to="/admin/chat" class="text-gray-700 hover:text-blue-600 transition-colors">Messages</router-link>

            <div class="relative ml-4">
              <button @click="showProfileMenu = !showProfileMenu"
                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-sm font-medium text-blue-600">{{ authStore.user?.name?.[0]?.toUpperCase() }}</span>
                </div>
                <span class="text-sm font-medium">{{ authStore.user?.name }}</span>
                <i class="fas fa-chevron-down text-xs"></i>
              </button>

              <div v-if="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <router-link to="/welcome" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-store mr-2"></i> View Store
                </router-link>
                <router-link to="/admin/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user-cog mr-2"></i> Settings
                </router-link>
                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                  <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 pt-24 pb-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">Dashboard</h1>
            <p class="mt-1 text-blue-100">A comprehensive overview of your store's performance</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 mb-8">
      <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-400"></i>
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
    </div>

    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-center items-center min-h-[400px]">
          <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
            <p class="mt-4 text-gray-600">Loading dashboard data...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="stat-card">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <i class="fas fa-users text-white text-xl"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.total_users }}</div>
                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                      <i class="fas fa-arrow-up mr-1"></i>
                      <span>12%</span>
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Orders Card -->
        <div class="stat-card">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">{{ stats.total_orders }}</div>
                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                      <i class="fas fa-arrow-up mr-1"></i>
                      <span>8%</span>
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="stat-card">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">Rp {{ formatPrice(stats.total_revenue) }}</div>
                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                      <i class="fas fa-arrow-up mr-1"></i>
                      <span>15%</span>
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Orders -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
              <router-link to="/admin/orders" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View all
              </router-link>
            </div>
            <div class="flow-root">
              <ul class="divide-y divide-gray-200">
                <li v-for="order in recentOrders" :key="order.id" class="py-4">
                  <div class="flex items-center space-x-4">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        Order #{{ order.id }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ order.customer_name }}
                      </p>
                      <p class="text-xs text-gray-400">
                        {{ formatDate(order.created_at) }}
                      </p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-medium text-gray-900">
                        Rp {{ formatPrice(order.total) }}
                      </p>
                      <span :class="getStatusClass(order.status)"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ order.status }}
                      </span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Popular Products -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Popular Products</h3>
              <router-link to="/admin/products" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View all
              </router-link>
            </div>
            <div class="flow-root">
              <ul class="divide-y divide-gray-200">
                <li v-for="product in popularProducts" :key="product.id" class="py-4">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <img class="h-12 w-12 rounded-lg object-cover" :src="getImageUrl(product.image)" :alt="product.name">
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ product.name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ product.sales_count }} sold
                      </p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-medium text-gray-900">
                        Rp {{ formatPrice(product.price) }}
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link to="/admin/products/create"
                        class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <i class="fas fa-plus-circle mr-2"></i>
              Add New Product
            </router-link>
            <router-link to="/admin/orders"
                        class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
              <i class="fas fa-clipboard-list mr-2"></i>
              Manage Orders
            </router-link>
            <router-link to="/admin/users"
                        class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
              <i class="fas fa-users-cog mr-2"></i>
              Manage Users
            </router-link>
            <router-link to="/admin/chat"
                        class="flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
              <i class="fas fa-comments mr-2"></i>
              Customer Support
            </router-link>
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

onMounted(async () => {
  try {
    const response = await fetch('/api/admin/dashboard', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to fetch dashboard data')
    }

    const data = await response.json()
    stats.value = data.stats
    recentOrders.value = data.recent_orders
    popularProducts.value = data.popular_products
  } catch (e) {
    console.error('Error fetching dashboard data:', e)
    error.value = 'Failed to load dashboard data. Please try again.'
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
  if (!image) return '/img/placeholder.png'
  return image.startsWith('http') ? image : `/storage/${image}`
}

const refreshStats = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await fetch('/api/admin/dashboard', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error('Failed to refresh dashboard data')
    }

    const data = await response.json()
    stats.value = data.stats
    recentOrders.value = data.recent_orders
    popularProducts.value = data.popular_products
  } catch (e) {
    console.error('Error refreshing dashboard data:', e)
    error.value = 'Failed to refresh dashboard data. Please try again.'
  } finally {
    loading.value = false
  }
}

const logout = async () => {
  try {
    await authStore.logout()
    router.push('/admin/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>

<style scoped>
.rounded-lg {
  border-radius: 0.5rem;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.transition-shadow {
  transition-property: box-shadow;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Additional Navigation Styles */
.router-link-active {
  color: #2563eb;
  font-weight: 500;
}

.nav-shadow {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Improved Card Styles */
.stat-card {
  background-color: white;
  overflow: hidden;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s;
}

.stat-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.stat-card:hover {
  transform: translateY(-1px);
}

/* Animation Classes */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
