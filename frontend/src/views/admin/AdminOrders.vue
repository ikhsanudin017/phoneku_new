<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 rounded-lg relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-grid-white/10"></div>
        <div class="relative py-5 px-8">
          <h2 class="text-2xl font-bold text-white pb-2 text-shadow-glow">Order Management</h2>
          <h5 class="text-blue-200">Track and manage customer orders</h5>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="backdrop-blur-lg bg-white/80 rounded-2xl p-6 shadow-2xl">
        <table class="min-w-full divide-y divide-blue-200">
          <thead>
            <tr class="bg-blue-50">
              <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Order ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-blue-100">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                #{{ order.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <img 
                      :src="order.customer.avatar || '/img/default-avatar.png'" 
                      :alt="order.customer.name"
                      class="h-8 w-8 rounded-full"
                    />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ order.customer.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ order.customer.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  order.status === 'Completed' ? 'bg-green-100 text-green-800' :
                  order.status === 'Processing' ? 'bg-blue-100 text-blue-800' :
                  order.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatPrice(order.total) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewOrder(order)"
                  class="text-blue-600 hover:text-blue-900 mr-4"
                >
                  View
                </button>
                <button
                  @click="updateStatus(order)"
                  class="text-blue-600 hover:text-blue-900"
                >
                  Update
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredOrders.length === 0 && !loading" class="text-center py-12">
        <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
        <p class="text-gray-600">Try adjusting your search or filter criteria</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <i class="fas fa-circle-notch fa-spin text-3xl text-blue-600"></i>
      </div>

      <!-- Update Status Modal -->
      <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4 w-full">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Order Status</h3>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="selectedStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option v-for="status in orderStatuses" :key="status" :value="status">
                {{ status }}
              </option>
            </select>
          </div>
          <div class="flex justify-end space-x-4">
            <button
              @click="showStatusModal = false"
              class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-150"
            >
              Cancel
            </button>
            <button
              @click="saveStatus"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-150"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AdminHeader from '@/components/admin/AdminHeader.vue'

export default {
  name: 'AdminOrders',
  components: {
    AdminHeader
  },
  setup() {
    const router = useRouter()
    const orders = ref([])
    const loading = ref(false)
    const error = ref(null)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const timeFilter = ref('all')
    const sortBy = ref('date')
    const currentPage = ref(1)
    const itemsPerPage = 10
    const showStatusModal = ref(false)
    const selectedOrder = ref(null)
    const selectedStatus = ref('')

    const orderStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled']

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(price)
    }

    // Fetch orders from API
    const fetchOrders = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/admin/orders', {
          params: {
            page: currentPage.value,
            status: statusFilter.value,
            timeFilter: timeFilter.value,
            search: searchQuery.value,
            sortBy: sortBy.value
          }
        })
        orders.value = response.data.orders
      } catch (err) {
        error.value = 'Failed to fetch orders'
        console.error('Error fetching orders:', err)
      } finally {
        loading.value = false
      }
    }

    // Computed properties for filtering and pagination
    const filteredOrders = computed(() => {
      let result = [...orders.value]

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(order => 
          order.id.toString().includes(query) ||
          order.customer.name.toLowerCase().includes(query) ||
          order.customer.email.toLowerCase().includes(query)
        )
      }

      if (statusFilter.value) {
        result = result.filter(order => order.status === statusFilter.value)
      }

      if (timeFilter.value !== 'all') {
        const now = new Date()
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
        result = result.filter(order => {
          const orderDate = new Date(order.created_at)
          switch (timeFilter.value) {
            case 'today':
              return orderDate >= today
            case 'week':
              const weekAgo = new Date(today)
              weekAgo.setDate(weekAgo.getDate() - 7)
              return orderDate >= weekAgo
            case 'month':
              const monthAgo = new Date(today)
              monthAgo.setMonth(monthAgo.getMonth() - 1)
              return orderDate >= monthAgo
            default:
              return true
          }
        })
      }

      result.sort((a, b) => {
        switch (sortBy.value) {
          case 'total':
            return b.total - a.total
          case 'status':
            return a.status.localeCompare(b.status)
          default: // date
            return new Date(b.created_at) - new Date(a.created_at)
        }
      })

      return result
    })

    const totalOrders = computed(() => filteredOrders.value.length)
    const totalPages = computed(() => Math.ceil(totalOrders.value / itemsPerPage))
    const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
    const endIndex = computed(() => startIndex.value + itemsPerPage)

    const paginatedOrders = computed(() => {
      return filteredOrders.value.slice(startIndex.value, endIndex.value)
    })

    // Pagination methods
    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
      }
    }

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++
      }
    }

    const goToPage = (page) => {
      currentPage.value = page
    }

    // Calculate visible page numbers
    const visiblePages = computed(() => {
      const delta = 2
      const range = []
      const rangeWithDots = []
      let l

      range.push(1)

      for (let i = currentPage.value - delta; i <= currentPage.value + delta; i++) {
        if (i > 1 && i < totalPages.value) {
          range.push(i)
        }
      }

      if (totalPages.value > 1) {
        range.push(totalPages.value)
      }

      for (let i of range) {
        if (l) {
          if (i - l === 2) {
            rangeWithDots.push(l + 1)
          } else if (i - l !== 1) {
            rangeWithDots.push('...')
          }
        }
        rangeWithDots.push(i)
        l = i
      }

      return rangeWithDots
    })

    // Order actions
    const viewOrder = (order) => {
      router.push('/admin/orders/' + order.id)
    }

    const updateStatus = (order) => {
      selectedOrder.value = order
      selectedStatus.value = order.status
      showStatusModal.value = true
    }

    const saveStatus = async () => {
      if (!selectedOrder.value || !selectedStatus.value) return

      try {
        await axios.patch('/api/admin/orders/' + selectedOrder.value.id + '/status', {
          status: selectedStatus.value
        })
        
        const index = orders.value.findIndex(o => o.id === selectedOrder.value.id)
        if (index !== -1) {
          orders.value[index] = { 
            ...orders.value[index], 
            status: selectedStatus.value 
          }
        }
        
        showStatusModal.value = false
        selectedOrder.value = null
        selectedStatus.value = ''
      } catch (err) {
        error.value = 'Failed to update order status'
        console.error('Error updating order status:', err)
      }
    }

    const exportOrders = async () => {
      try {
        const response = await axios.get('/api/admin/orders/export', {
          responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        const date = new Date().toISOString().split('T')[0]
        link.setAttribute('download', 'orders-' + date + '.csv')
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (err) {
        error.value = 'Failed to export orders'
        console.error('Error exporting orders:', err)
      }
    }

    // Watch for changes that should trigger a refresh
    watch([currentPage, statusFilter, timeFilter, sortBy], () => {
      fetchOrders()
    })

    // Initial load
    onMounted(() => {
      fetchOrders()
    })

    return {
      orders,
      loading,
      error,
      searchQuery,
      statusFilter,
      timeFilter,
      sortBy,
      currentPage,
      showStatusModal,
      selectedOrder,
      selectedStatus,
      orderStatuses,
      filteredOrders: paginatedOrders,
      totalOrders,
      totalPages,
      startIndex,
      endIndex,
      visiblePages,
      formatDate,
      formatPrice,
      prevPage,
      nextPage,
      goToPage,
      viewOrder,
      updateStatus,
      saveStatus,
      exportOrders,
      router
    }
  }
}
</script>

<style scoped>
.aspect-w-4 {
  position: relative;
  padding-bottom: 75%;
}

.aspect-h-3 {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
}

.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7);
}

.bg-grid-white {
  background-image: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 1px,
    transparent 1px,
    transparent 100%
  );
  background-size: 50px 50px;
}
</style>
