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
            <router-link to="/admin/dashboard" class="text-gray-700 hover:text-blue-600 transition-colors">Dashboard</router-link>
            <router-link to="/admin/products" class="text-blue-600 font-medium">Products</router-link>
            <router-link to="/admin/orders" class="text-gray-700 hover:text-blue-600 transition-colors">Orders</router-link>
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600 transition-colors">Users</router-link>
            <router-link to="/admin/chat" class="text-gray-700 hover:text-blue-600 transition-colors">Messages</router-link>
            <router-link to="/admin/orders" class="text-gray-700 hover:text-blue-600">Orders</router-link>

            <div class="relative ml-4">
              <button @click="showProfileMenu = !showProfileMenu"
                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-sm font-medium text-blue-600">{{ user?.name?.[0]?.toUpperCase() }}</span>
                </div>
                <span class="text-sm font-medium">{{ user?.name }}</span>
                <i class="fas fa-chevron-down text-xs"></i>
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

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 pt-24 pb-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white">Products Management</h1>
            <p class="mt-1 text-blue-100">Manage your product catalog and inventory</p>
          </div>
          <button @click="openAddModal"
            class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i>
            Add New Product
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24">

      <!-- Filters and Search -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <div class="relative">
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search products..."
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
              />
            </div>
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

      <!-- Products List -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900">All Products</h2>
          <p class="text-sm text-gray-500 mt-1">Manage and monitor your product inventory</p>
        </div>

        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Loading products...</p>
        </div>

        <div v-else-if="products.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-box-open text-2xl text-gray-400"></i>
          </div>
          <p class="text-gray-500">No products found.</p>
          <button @click="openAddModal" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
            Add your first product
          </button>
        </div>

        <ul v-else class="divide-y divide-gray-100">
          <li v-for="product in products" :key="product.id" class="p-6 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <img :src="getImageUrl(product.image)" :alt="product.name"
                  class="w-16 h-16 rounded-lg object-cover bg-gray-100" />
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ product.name }}</h3>
                  <div class="flex items-center space-x-4 mt-1">
                    <span class="text-sm text-gray-600">
                      Rp {{ formatPrice(product.price) }}
                    </span>
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      product.is_featured ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'
                    ]">
                      {{ product.is_featured ? 'Featured' : 'Regular' }}
                    </span>
                    <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-3">
                <button @click="editProduct(product)"
                  class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteProduct(product)"
                  class="p-2 text-gray-500 hover:text-red-600 transition-colors">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1"
          class="flex justify-center items-center space-x-2 p-6 bg-gray-50">
          <button
            v-for="page in paginationPages"
            :key="page"
            @click="changePage(page)"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
              page === pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
            ]"
            :disabled="page === '...'"
          >
            {{ page }}
          </button>
        </div>
      </div>    <!-- Add/Edit Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-xl font-semibold text-gray-900">
            {{ isEditing ? 'Edit Product' : 'Add New Product' }}
          </h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
            <i class="fas fa-times"></i>
          </button>
        </div>        <form @submit.prevent="saveProduct" class="px-6 py-4 space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
            <input
              v-model="productForm.name"
              type="text"
              required
              placeholder="Enter product name"
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              v-model="productForm.description"
              rows="3"
              placeholder="Enter product description"
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors resize-none"
            ></textarea>
          </div>          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                <input
                  v-model="productForm.price"
                  type="number"
                  required
                  min="0"
                  placeholder="0"
                  class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
              <input
                v-model="productForm.stock"
                type="number"
                required
                min="0"
                placeholder="Enter stock quantity"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
              />
              </div>
            </div>          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="productForm.category"
              required
              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors appearance-none"
            >
              <option value="">Select Category</option>
              <option value="handphone">Handphone</option>
              <option value="accessory">Accessory</option>
            </select>
          </div>

          <div class="flex items-center space-x-3">
            <div class="relative inline-flex items-center cursor-pointer">
              <input
                id="is_featured"
                v-model="productForm.is_featured"
                type="checkbox"
                class="sr-only peer"
              />
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
              <label for="is_featured" class="ml-3 text-sm font-medium text-gray-700">Featured Product</label>
            </div>
          </div>          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Colors</label>
            <ColorPicker v-model="productForm.selectedColors" />
          </div>

          <div v-if="errorMessage"
            class="bg-red-50 border border-red-200 text-red-600 px-5 py-4 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            {{ errorMessage }}
          </div>

          <div class="sticky bottom-0 bg-gray-50 px-6 py-4 -mx-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 font-medium transition-colors inline-flex items-center"
            >
              <i v-if="saving" class="fas fa-circle-notch fa-spin mr-2"></i>
              <span>{{ saving ? 'Saving...' : (isEditing ? 'Update Product' : 'Create Product') }}</span>
            </button>
          </div>
          </form>
        </div>
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
