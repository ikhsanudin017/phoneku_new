<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Admin Navigation -->
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
            <router-link to="/admin/products" class="text-blue-600 font-medium">Products</router-link>
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600">Users</router-link>
            <router-link to="/admin/chat" class="text-gray-700 hover:text-blue-600">Messages</router-link>
            <router-link to="/admin/orders" class="text-gray-700 hover:text-blue-600">Orders</router-link>

            <div class="relative" ref="profileDropdown">
              <button @click="showProfileMenu = !showProfileMenu" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                <span>{{ user?.name }}</span>
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
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
        <button @click="openAddModal" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
          Add New Product
        </button>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search products..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <select
              v-model="filters.category"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Categories</option>
              <option value="handphone">Handphone</option>
              <option value="accessory">Accessory</option>
            </select>
          </div>
          <div>
            <select
              v-model="filters.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div>
            <button
              @click="fetchProducts"
              class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Loading products...</p>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-12">
          <p class="text-gray-500">No products found.</p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
          <li v-for="product in products" :key="product.id" class="px-6 py-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <img :src="getImageUrl(product.image)" :alt="product.name" class="w-16 h-16 object-cover rounded-md">
                <div class="ml-4">
                  <p class="text-lg font-medium text-gray-900">{{ product.name }}</p>
                  <p class="text-sm text-gray-500">{{ product.category }}</p>
                  <p class="text-sm font-semibold text-blue-600">Rp {{ formatPrice(product.price) }}</p>
                  <div class="flex items-center mt-1 space-x-1">
                    <div v-for="color in parseColors(product.color)"
                         :key="color.hex"
                         class="w-4 h-4 rounded-full shadow-sm"
                         :style="{ backgroundColor: color.hex }"
                         :title="color.name"></div>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-4">
                <span :class="product.is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                  {{ product.is_featured ? 'Featured' : 'Regular' }}
                </span>
                <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>

                <div class="flex space-x-2">
                  <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button @click="deleteProduct(product)" class="text-red-600 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </li>
        </ul>
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

    <!-- Add/Edit Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Edit Product' : 'Add New Product' }}
          </h3>
        </div>

        <form @submit.prevent="saveProduct" class="px-6 py-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
            <input
              v-model="productForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              v-model="productForm.description"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
              <input
                v-model="productForm.price"
                type="number"
                required
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
              <input
                v-model="productForm.stock"
                type="number"
                required
                min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="productForm.category"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select Category</option>
              <option value="handphone">Handphone</option>
              <option value="accessory">Accessory</option>
            </select>
          </div>

          <div class="flex items-center">
            <input
              id="is_featured"
              v-model="productForm.is_featured"
              type="checkbox"
              class="mr-2"
            />
            <label for="is_featured" class="text-sm font-medium text-gray-700">Featured Product</label>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Colors</label>
            <ColorPicker v-model="productForm.selectedColors" />
          </div>

          <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
            {{ errorMessage }}
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { productsAPI } from '@/services/api'
import ColorPicker from '@/components/ColorPicker.vue'

export default {
  name: 'AdminProducts',
  components: {
    ColorPicker
  },

  setup() {
    const router = useRouter()
    const authStore = useAuthStore()

    const loading = ref(false)
    const saving = ref(false)
    const showProfileMenu = ref(false)
    const showModal = ref(false)
    const isEditing = ref(false)
    const products = ref([])
    const pagination = ref(null)
    const errorMessage = ref('')
    const user = computed(() => authStore.user)

    const filters = reactive({
      search: '',
      category: '',
      status: ''
    })

    const productForm = reactive({
      id: null,
      name: '',
      description: '',
      price: '',
      original_price: '',
      stock: '',
      category: '',
      is_featured: false,
      selectedColors: []
    })

    const colorNameMap = {
      '#FF0000': 'Merah',
      '#00FF00': 'Hijau',
      '#0000FF': 'Biru',
      '#FFFF00': 'Kuning',
      '#FF00FF': 'Magenta',
      '#00FFFF': 'Cyan',
      '#000000': 'Hitam',
      '#FFFFFF': 'Putih',
      '#808080': 'Abu-abu',
      '#C0C0C0': 'Silver'
    }

    const resetForm = () => {
      Object.assign(productForm, {
        id: null,
        name: '',
        description: '',
        price: '',
        original_price: '',
        stock: '',
        category: '',
        is_featured: false,
        selectedColors: []
      })
    }

    const fetchProducts = async (page = 1) => {
      loading.value = true
      try {
        const params = {
          page,
          per_page: 10,
          search: filters.search,
          category: filters.category,
          status: filters.status
        }

        const response = await productsAPI.getAll(params)
        if (response.data.success) {
          products.value = response.data.data
          pagination.value = response.data.pagination
        }
      } catch (error) {
        console.error('Failed to fetch products:', error)
      } finally {
        loading.value = false
      }
    }

    const parseColors = (colorStr) => {
      if (!colorStr) return []
      try {
        return colorStr.split(',').map(c => {
          const [hex, name] = c.split('|')
          return { hex: hex.trim(), name: name?.trim() || hex.trim() }
        })
      } catch (e) {
        console.error('Error parsing colors:', e)
        return []
      }
    }

    const openAddModal = () => {
      resetForm()
      isEditing.value = false
      showModal.value = true
    }

    const editProduct = (product) => {
      const colors = parseColors(product.color)
      Object.assign(productForm, {
        ...product,
        selectedColors: colors.map(c => c.hex)
      })
      isEditing.value = true
      showModal.value = true
    }

    const saveProduct = async () => {
      saving.value = true
      errorMessage.value = ''

      try {
        const formData = new FormData()
        formData.append('name', productForm.name)
        formData.append('description', productForm.description)
        formData.append('price', productForm.price)
        formData.append('original_price', productForm.original_price)
        formData.append('stock', productForm.stock)
        formData.append('category', productForm.category)
        formData.append('is_featured', productForm.is_featured)

        // Format colors as hex|name,hex|name
        const colorString = productForm.selectedColors.map(color => {
          const name = colorNameMap[color.toUpperCase()] || color
          return `${color}|${name}`
        }).join(',')
        formData.append('color', colorString)

        let response
        if (isEditing.value) {
          response = await productsAPI.update(productForm.id, formData)
        } else {
          response = await productsAPI.create(formData)
        }

        if (response.data.success) {
          closeModal()
          fetchProducts()
          alert(`Product ${isEditing.value ? 'updated' : 'created'} successfully!`)
        } else {
          errorMessage.value = response.data.message || 'Operation failed'
        }
      } catch (error) {
        if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          errorMessage.value = errors.join(', ')
        } else {
          errorMessage.value = error.response?.data?.message || 'Operation failed'
        }
        console.error('Save product error:', error)
      } finally {
        saving.value = false
      }
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
      errorMessage.value = ''
    }

    const deleteProduct = async (product) => {
      if (!confirm(`Are you sure you want to delete "${product.name}"?`)) return

      try {
        const response = await productsAPI.delete(product.id)
        if (response.data.success) {
          fetchProducts()
          alert('Product deleted successfully!')
        } else {
          alert('Failed to delete product')
        }
      } catch (error) {
        console.error('Delete product error:', error)
        alert('Failed to delete product')
      }
    }

    const changePage = (page) => {
      if (page !== '...' && page !== pagination.value?.current_page) {
        fetchProducts(page)
      }
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

    const formatPrice = (price) => {
      return new Intl.NumberFormat('id-ID').format(price)
    }

    const getImageUrl = (imagePath) => {
      if (!imagePath) return '/placeholder-image.jpg'
      return `http://localhost:8000/storage/${imagePath}`
    }

    const logout = async () => {
      await authStore.logout()
      showProfileMenu.value = false
      router.push('/admin/login')
    }

    onMounted(() => {
      fetchProducts()
    })

    return {
      loading,
      saving,
      showProfileMenu,
      showModal,
      isEditing,
      products,
      pagination,
      errorMessage,
      filters,
      productForm,
      user,
      paginationPages,
      parseColors,
      formatPrice,
      getImageUrl,
      openAddModal,
      editProduct,
      closeModal,
      saveProduct,
      deleteProduct,
      changePage,
      logout
    }
  }
}
</script>
