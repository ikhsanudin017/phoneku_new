<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Global Navbar -->
    <NavbarComponent />

    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading product...</p>
      </div>
    </div>

    <!-- Product Details -->
    <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <router-link to="/welcome" class="text-gray-700 hover:text-blue-600">Home</router-link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <router-link to="/products" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Products</router-link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-gray-500 md:ml-2">{{ product.name }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Product Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
        <!-- Product Images -->
        <div>
          <div class="mb-4">
            <img :src="getImageUrl(selectedImage)" :alt="product.name" class="w-full h-96 object-cover rounded-lg">
          </div>

          <div class="flex space-x-2">
            <button
              v-for="(image, index) in productImages"
              :key="index"
              @click="selectedImage = image"
              :class="[
                'w-20 h-20 rounded-md overflow-hidden border-2',
                selectedImage === image ? 'border-blue-600' : 'border-gray-200'
              ]"
            >
              <img :src="getImageUrl(image)" :alt="product.name" class="w-full h-full object-cover">
            </button>
          </div>
        </div>

        <!-- Product Info -->
        <div>
          <div class="mb-4">
            <span v-if="product.is_featured" class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">
              Featured
            </span>
            <span class="ml-2 text-sm text-gray-500 capitalize">{{ product.category }}</span>
          </div>

          <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>

          <div class="mb-6">
            <div class="flex items-center space-x-4">
              <span class="text-3xl font-bold text-blue-600">Rp {{ formatPrice(product.price) }}</span>
              <span v-if="product.original_price" class="text-xl text-gray-500 line-through">
                Rp {{ formatPrice(product.original_price) }}
              </span>
            </div>
            <div v-if="product.original_price" class="mt-2">
              <span class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                Save {{ Math.round(((product.original_price - product.price) / product.original_price) * 100) }}%
              </span>
            </div>
          </div>

          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Description</h3>
            <p class="text-gray-700">{{ product.description || 'No description available.' }}</p>
          </div>

          <div class="mb-6">
            <div class="flex items-center space-x-4 text-sm text-gray-600">
              <span>Stock: {{ product.stock }}</span>
              <span v-if="product.color">Color: {{ product.color }}</span>
            </div>
          </div>

          <!-- Add to Cart -->
          <div v-if="authStore.isAuthenticated" class="mb-6">
            <div class="flex items-center space-x-4 mb-4">
              <label class="text-sm font-medium">Quantity:</label>
              <div class="flex items-center border border-gray-300 rounded-md">
                <button @click="decreaseQuantity" class="px-3 py-1 hover:bg-gray-100">-</button>
                <input
                  v-model.number="quantity"
                  type="number"
                  min="1"
                  :max="product.stock"
                  class="w-16 text-center border-0 focus:ring-0"
                />
                <button @click="increaseQuantity" class="px-3 py-1 hover:bg-gray-100">+</button>
              </div>
            </div>

            <button
              @click="addToCart"
              :disabled="cartLoading || product.stock === 0"
              class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <span v-if="cartLoading" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
          </div>

          <div v-else class="mb-6">
            <router-link to="/login" class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 text-center block">
              Login to Add to Cart
            </router-link>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded mb-4">
            {{ successMessage }}
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-4">
            {{ errorMessage }}
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="relatedProducts && relatedProducts.length > 0">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <router-link :to="`/product/${relatedProduct.id}`">
              <img :src="getImageUrl(relatedProduct.image)" :alt="relatedProduct.name" class="w-full h-48 object-cover">
              <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ relatedProduct.name }}</h3>
                <div class="flex items-center justify-between">
                  <span class="text-lg font-bold text-blue-600">Rp {{ formatPrice(relatedProduct.price) }}</span>
                  <span v-if="relatedProduct.original_price" class="text-sm text-gray-500 line-through">
                    Rp {{ formatPrice(relatedProduct.original_price) }}
                  </span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Not Found -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Product Not Found</h2>
        <p class="text-gray-600 mb-6">The product you're looking for doesn't exist.</p>
        <router-link to="/products" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
          Browse Products
        </router-link>
      </div>
    </div>

    <!-- Global Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
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

const product = ref(null)
const relatedProducts = ref([])
const loading = ref(false)
const cartLoading = ref(false)
const quantity = ref(1)
const selectedImage = ref('')
const showProfileMenu = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const productImages = computed(() => {
  if (!product.value) return []
  const images = []
  if (product.value.image) images.push(product.value.image)
  if (product.value.image2) images.push(product.value.image2)
  if (product.value.image3) images.push(product.value.image3)
  return images
})

const fetchProduct = async () => {
  loading.value = true
  try {
    const response = await productsAPI.getById(route.params.id)
    if (response.data.success) {
      product.value = response.data.data
      relatedProducts.value = response.data.related_products
      selectedImage.value = product.value.image
    }
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
}

const increaseQuantity = () => {
  if (quantity.value < product.value.stock) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const addToCart = async () => {
  cartLoading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const result = await cartStore.addToCart(product.value.id, {
      quantity: quantity.value,
      color: product.value.color
    })

    if (result.success) {
      successMessage.value = result.message
      quantity.value = 1
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Failed to add product to cart'
    console.error('Add to cart error:', error)
  } finally {
    cartLoading.value = false

    // Clear messages after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
      errorMessage.value = ''
    }, 3000)
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

onMounted(() => {
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.getCartCount()
  }
  fetchProduct()
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
