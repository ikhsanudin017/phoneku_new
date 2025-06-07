<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Global Navbar -->
    <NavbarComponent />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <router-link to="/welcome" class="text-gray-700 hover:text-blue-600">Home</router-link>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-gray-500 md:ml-2">Shopping Cart</span>
            </div>
          </li>
        </ol>
      </nav>

      <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

      <!-- Loading -->
      <div v-if="cartStore.loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading cart...</p>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.items.length === 0" class="text-center py-12">
        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
        </svg>
        <h2 class="text-2xl font-semibold text-gray-900 mt-4 mb-2">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Start adding some products to your cart!</p>
        <router-link to="/products" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
          Continue Shopping
        </router-link>
      </div>

      <!-- Cart Items -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items List -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
              <h2 class="text-xl font-semibold mb-6">Cart Items ({{ cartStore.items.length }})</h2>

              <div class="space-y-6">
                <div v-for="item in cartStore.items" :key="item.id" class="flex items-center space-x-4 border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                  <!-- Product Image -->
                  <div class="flex-shrink-0">
                    <img :src="getImageUrl(item.product.image)" :alt="item.product.name" class="w-20 h-20 object-cover rounded-md">
                  </div>

                  <!-- Product Details -->
                  <div class="flex-grow">
                    <router-link :to="`/product/${item.product.id}`" class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                      {{ item.product.name }}
                    </router-link>
                    <p class="text-gray-600 text-sm mt-1">{{ item.product.description || 'No description' }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                      <span class="text-lg font-bold text-blue-600">Rp {{ formatPrice(item.product.price) }}</span>
                      <span v-if="item.color" class="text-sm text-gray-500">Color: {{ item.color }}</span>
                    </div>
                  </div>

                  <!-- Quantity Controls -->
                  <div class="flex items-center space-x-2">
                    <button
                      @click="updateQuantity(item.id, item.quantity - 1)"
                      :disabled="updating === item.id"
                      class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 disabled:opacity-50"
                    >
                      -
                    </button>
                    <span class="w-8 text-center">{{ item.quantity }}</span>
                    <button
                      @click="updateQuantity(item.id, item.quantity + 1)"
                      :disabled="updating === item.id || item.quantity >= item.product.stock"
                      class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 disabled:opacity-50"
                    >
                      +
                    </button>
                  </div>

                  <!-- Item Total -->
                  <div class="text-right">
                    <div class="text-lg font-semibold text-gray-900">
                      Rp {{ formatPrice(item.product.price * item.quantity) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ item.quantity }} × Rp {{ formatPrice(item.product.price) }}
                    </div>
                  </div>

                  <!-- Remove Button -->
                  <button
                    @click="removeItem(item.id)"
                    :disabled="removing === item.id"
                    class="text-red-600 hover:text-red-800 disabled:opacity-50"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Clear Cart -->
              <div class="mt-6 pt-6 border-t border-gray-200">
                <button
                  @click="clearCart"
                  :disabled="clearing"
                  class="text-red-600 hover:text-red-800 text-sm disabled:opacity-50"
                >
                  {{ clearing ? 'Clearing...' : 'Clear Cart' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md sticky top-8">
            <div class="p-6">
              <h2 class="text-xl font-semibold mb-6">Order Summary</h2>

              <div class="space-y-4">
                <div class="flex justify-between">
                  <span class="text-gray-600">Subtotal ({{ cartStore.totalItems }} items)</span>
                  <span class="font-semibold">Rp {{ formatPrice(cartStore.totalPrice) }}</span>
                </div>

                <div class="flex justify-between">
                  <span class="text-gray-600">Shipping</span>
                  <span class="font-semibold">Free</span>
                </div>

                <div class="border-t border-gray-200 pt-4">
                  <div class="flex justify-between">
                    <span class="text-lg font-semibold">Total</span>
                    <span class="text-lg font-bold text-blue-600">Rp {{ formatPrice(cartStore.totalPrice) }}</span>
                  </div>
                </div>
              </div>

              <router-link
                to="/checkout"
                class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 mt-6 block text-center font-medium"
              >
                Proceed to Checkout
              </router-link>

              <router-link
                to="/products"
                class="w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-md hover:bg-gray-50 mt-3 block text-center"
              >
                Continue Shopping
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="successMessage" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg">
        {{ successMessage }}
      </div>

      <div v-if="errorMessage" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg">
        {{ errorMessage }}
      </div>
    </div>

    <!-- Global Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const showProfileMenu = ref(false)
const updating = ref(null)
const removing = ref(null)
const clearing = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const updateQuantity = async (itemId, newQuantity) => {
  updating.value = itemId
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const result = await cartStore.updateQuantity(itemId, newQuantity)
    if (result.success) {
      successMessage.value = result.message
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Failed to update quantity'
    console.error('Update quantity error:', error)
  } finally {
    updating.value = null
    clearMessages()
  }
}

const removeItem = async (itemId) => {
  removing.value = itemId
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const result = await cartStore.removeItem(itemId)
    if (result.success) {
      successMessage.value = result.message
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Failed to remove item'
    console.error('Remove item error:', error)
  } finally {
    removing.value = null
    clearMessages()
  }
}

const clearCart = async () => {
  if (!confirm('Are you sure you want to clear your cart?')) return

  clearing.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const result = await cartStore.clearCart()
    if (result.success) {
      successMessage.value = result.message
    } else {
      errorMessage.value = result.message
    }
  } catch (error) {
    errorMessage.value = 'Failed to clear cart'
    console.error('Clear cart error:', error)
  } finally {
    clearing.value = false
    clearMessages()
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

const clearMessages = () => {
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 3000)
}

onMounted(() => {
  authStore.initializeAuth()
  cartStore.fetchCartItems()
})
</script>
