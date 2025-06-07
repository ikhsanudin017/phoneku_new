<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Global Navbar -->
    <NavbarComponent />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">All Products</h1>

        <!-- Search and Filters -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @keyup.enter="searchProducts"
            />
          </div>

          <select
            v-model="selectedCategory"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="filterProducts"
          >
            <option value="">All Categories</option>
            <option value="handphone">Smartphones</option>
            <option value="accessory">Accessories</option>
          </select>

          <select
            v-model="sortBy"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="sortProducts"
          >
            <option value="created_at">Newest</option>
            <option value="name">Name A-Z</option>
            <option value="price">Price Low-High</option>
            <option value="price_desc">Price High-Low</option>
          </select>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading products...</p>
      </div>

      <!-- Products Grid -->
      <div v-else>
        <div v-if="products.length === 0" class="text-center py-12">
          <p class="text-gray-600">No products found.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div v-for="product in products" :key="product.id" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <router-link :to="`/product/${product.id}`">
              <img :src="getImageUrl(product.image)" :alt="product.name" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ product.name }}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ product.description }}</p>

                <div class="flex items-center justify-between mb-3">
                  <div>
                    <span class="text-lg font-bold text-blue-600">Rp {{ formatPrice(product.price) }}</span>
                    <span v-if="product.original_price" class="text-sm text-gray-500 line-through ml-2">
                      Rp {{ formatPrice(product.original_price) }}
                    </span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
                  <span v-if="product.is_featured" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                    Featured
                  </span>
                </div>
              </div>
            </router-link>
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
      </div>
    </div>

    <!-- Global Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { productsAPI } from '@/services/api'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const products = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('')
const sortBy = ref('created_at')
const pagination = ref(null)
const showProfileMenu = ref(false)

const fetchProducts = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: 12,
      category: selectedCategory.value,
      search: searchQuery.value,
      sort_by: sortBy.value.includes('_desc') ? sortBy.value.replace('_desc', '') : sortBy.value,
      sort_order: sortBy.value.includes('_desc') ? 'desc' : 'asc'
    }

    const response = await productsAPI.getAll(params)
    if (response.data.success) {
      products.value = response.data.data
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
}

const searchProducts = () => {
  fetchProducts(1)
}

const filterProducts = () => {
  fetchProducts(1)
}

const sortProducts = () => {
  fetchProducts(1)
}

const changePage = (page) => {
  if (page !== '...' && page !== pagination.value.current_page) {
    fetchProducts(page)
  }
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/placeholder-image.jpg'
  return `http://localhost:8000/storage/${imagePath}`
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const logout = async () => {
  await authStore.logout()
  showProfileMenu.value = false
  router.push('/welcome')
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

// Initialize from route query parameters
onMounted(() => {
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.getCartCount()
  }

  if (route.query.category) {
    selectedCategory.value = route.query.category
  }
  if (route.query.search) {
    searchQuery.value = route.query.search
  }

  fetchProducts()
})

// Watch for route changes
watch(() => route.query, (newQuery) => {
  selectedCategory.value = newQuery.category || ''
  searchQuery.value = newQuery.search || ''
  fetchProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>
