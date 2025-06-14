<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 rounded-lg relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-grid-white/10"></div>
        <div class="relative py-5 px-8 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-white pb-2 text-shadow-glow">Product Management</h2>
            <h5 class="text-blue-200">Manage your product catalog</h5>
          </div>
          <router-link to="/admin/products/create" 
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-all duration-200 ease-in-out transform hover:scale-105">
            Add New Product
          </router-link>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="backdrop-blur-lg bg-white/80 rounded-2xl p-6 shadow-2xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div v-for="product in filteredProducts" :key="product.id" 
               class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <!-- Product Image -->
            <div class="relative aspect-w-4 aspect-h-3">
              <img 
                :src="product.image" 
                :alt="product.name"
                class="w-full h-48 object-cover"
              />
              <div class="absolute top-0 right-0 mt-2 mr-2">
                <button 
                  @click="toggleFeatured(product)"
                  :class="[
                    product.featured ? 'text-yellow-400' : 'text-gray-400',
                    'hover:scale-110 transition-transform duration-150'
                  ]"
                >
                  <i class="fas fa-star text-xl"></i>
                </button>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ product.name }}</h3>
                <span :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  product.stock > 10 ? 'bg-green-100 text-green-800' :
                  product.stock > 0 ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]">
                  {{ product.stock > 0 ? product.stock + ' in stock' : 'Out of stock' }}
                </span>
              </div>

              <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ product.description }}</p>

              <div class="flex justify-between items-center mt-4">
                <span class="text-lg font-bold text-blue-600">{{ formatPrice(product.price) }}</span>
                <div class="flex space-x-2">
                  <button 
                    @click="editProduct(product)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition-colors duration-150"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button 
                    @click="confirmDelete(product)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-full transition-colors duration-150"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0 && !loading" class="text-center py-12">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-600">Try adjusting your search or filter criteria</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-full">
          <i class="fas fa-circle-notch fa-spin text-3xl text-blue-600"></i>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
          <p class="text-gray-600 mb-6">
            Are you sure you want to delete "{{ selectedProduct?.name }}"? This action cannot be undone.
          </p>
          <div class="flex justify-end space-x-4">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-150"
            >
              Cancel
            </button>
            <button
              @click="deleteProduct"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-150"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AdminHeader from '@/components/admin/AdminHeader.vue'

export default {
  name: 'AdminProducts',
  components: {
    AdminHeader
  },
  setup() {
    const router = useRouter()
    const products = ref([])
    const loading = ref(false)
    const error = ref(null)
    const searchQuery = ref('')
    const categoryFilter = ref('')
    const sortBy = ref('name')
    const showDeleteModal = ref(false)
    const selectedProduct = ref(null)

    const categories = ['Smartphones', 'Accessories', 'Tablets', 'Wearables']

    // Format price with currency
    const formatPrice = (price) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(price)
    }

    // Fetch products from API
    const fetchProducts = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/admin/products')
        products.value = response.data
      } catch (err) {
        error.value = 'Failed to fetch products'
        console.error('Error fetching products:', err)
      } finally {
        loading.value = false
      }
    }

    // Computed property for filtered and sorted products
    const filteredProducts = computed(() => {
      let result = [...products.value]

      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(product => 
          product.name.toLowerCase().includes(query) ||
          product.description.toLowerCase().includes(query)
        )
      }

      if (categoryFilter.value) {
        result = result.filter(product => product.category === categoryFilter.value)
      }

      result.sort((a, b) => {
        switch (sortBy.value) {
          case 'price':
            return a.price - b.price
          case 'stock':
            return b.stock - a.stock
          case 'created':
            return new Date(b.created_at) - new Date(a.created_at)
          default:
            return a.name.localeCompare(b.name)
        }
      })

      return result
    })

    // Product actions
    const editProduct = (product) => {
      router.push('/admin/products/' + product.id + '/edit')
    }

    const confirmDelete = (product) => {
      selectedProduct.value = product
      showDeleteModal.value = true
    }

    const deleteProduct = async () => {
      if (!selectedProduct.value) return

      loading.value = true
      try {
        await axios.delete('/api/admin/products/' + selectedProduct.value.id)
        products.value = products.value.filter(p => p.id !== selectedProduct.value.id)
        showDeleteModal.value = false
        selectedProduct.value = null
      } catch (err) {
        error.value = 'Failed to delete product'
        console.error('Error deleting product:', err)
      } finally {
        loading.value = false
      }
    }

    const toggleFeatured = async (product) => {
      try {
        await axios.patch('/api/admin/products/' + product.id + '/featured', {
          featured: !product.featured
        })
        const index = products.value.findIndex(p => p.id === product.id)
        if (index !== -1) {
          products.value[index] = { ...products.value[index], featured: !product.featured }
        }
      } catch (err) {
        error.value = 'Failed to update product'
        console.error('Error updating product:', err)
      }
    }

    onMounted(() => {
      fetchProducts()
    })

    return {
      products,
      loading,
      error,
      searchQuery, 
      categoryFilter,
      sortBy,
      showDeleteModal,
      selectedProduct,
      categories,
      filteredProducts,
      formatPrice,
      editProduct,
      confirmDelete,
      deleteProduct,
      toggleFeatured
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.bg-grid-white {
  background-image: linear-gradient(white 1px, transparent 1px),
                linear-gradient(to right, white 1px, transparent 1px);
}

.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7), 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
