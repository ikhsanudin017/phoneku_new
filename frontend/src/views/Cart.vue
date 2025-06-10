<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavbarComponent class="flex-none" />

    <!-- Main Content -->
    <section class="flex-grow py-6 md:py-8">
      <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Keranjang</h2>
          <router-link to="/products" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            &larr; Lanjutkan Belanja
          </router-link>
        </div>

        <!-- Alert Messages -->
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ successMessage }}
          </div>
        </div>

        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            {{ errorMessage }}
          </div>
        </div>

        <!-- Empty Cart -->
        <div v-if="cartStore.items.length === 0" class="flex justify-center">
          <div class="text-center py-12 bg-white w-full max-w-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang Anda kosong</h3>
            <p class="mt-1 text-gray-500">Mulai tambahkan beberapa produk menarik ke keranjang Anda</p>
            <div class="mt-6">
              <router-link to="/products" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Mulai Belanja
              </router-link>
            </div>
          </div>
        </div>

        <!-- Cart Items -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-6">
            <div v-for="item in cartStore.items" :key="item.id"
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between border border-gray-200 rounded-xl p-6 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">
              <div class="flex items-start gap-6 w-full sm:w-auto">
                <div class="relative">
                  <img :src="getImageUrl(item.product.image)" :alt="item.product.name"
                    class="w-24 h-24 sm:w-28 sm:h-28 object-contain rounded-lg border border-gray-200" />
                  <span v-if="item.quantity > item.product.stock"
                    class="absolute top-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-full transform translate-x-1/2 -translate-y-1/2">
                    Stok kurang
                  </span>
                </div>
                <div class="space-y-1 flex-1">
                  <h3 class="font-bold text-lg text-gray-800">{{ item.product.name }}</h3>
                  <p class="text-sm text-gray-600">{{ item.color || 'Red' }}</p>
                  <p class="text-sm text-gray-500">SKU: {{ item.product.sku || 'N/A' }}</p>
                  <p class="text-sm font-medium" :class="{ 'text-green-600': item.product.stock > 0, 'text-red-600': item.product.stock === 0 }">
                    {{ item.product.stock > 0 ? `Tersedia (${item.product.stock} unit)` : 'Stok habis!' }}
                  </p>
                  <p v-if="item.quantity > item.product.stock" class="text-xs text-red-500 mt-1">
                    Anda memesan {{ item.quantity }} unit, tetapi stok hanya {{ item.product.stock }} unit
                  </p>
                </div>
              </div>
              <div class="flex flex-col sm:items-end w-full sm:w-auto mt-4 sm:mt-0 gap-3">
                <div class="text-xl font-bold text-gray-800 text-right">
                  Rp {{ formatPrice(item.product.price * item.quantity) }}
                  <div v-if="item.quantity > 1" class="text-sm font-normal text-gray-500">
                    (Rp {{ formatPrice(item.product.price) }} per unit)
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <div class="flex items-center">
                    <button @click="updateQuantity(item.id, item.quantity - 1)"
                      :disabled="updating === item.id"
                      class="border px-3 py-1 rounded-l-full hover:bg-gray-100 font-bold text-gray-600 hover:text-gray-800 transition-colors">
                      &minus;
                    </button>
                    <input type="text" :value="item.quantity" readonly
                      class="w-12 text-center border-t border-b py-1 text-sm bg-gray-50 focus:outline-none" />
                    <button @click="updateQuantity(item.id, Math.min(item.product.stock, item.quantity + 1))"
                      :disabled="updating === item.id || item.quantity >= item.product.stock"
                      class="border px-3 py-1 rounded-r-full hover:bg-gray-100 font-bold text-gray-600 hover:text-gray-800 transition-colors"
                      :class="{ 'opacity-50 cursor-not-allowed': item.quantity >= item.product.stock }">
                      &plus;
                    </button>
                  </div>
                  <button @click="removeItem(item.id)"
                    :disabled="removing === item.id"
                    class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded-full hover:bg-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="lg:col-span-1">
            <div class="border border-gray-200 rounded-2xl shadow-sm p-6 bg-white sticky top-4">
              <h3 class="text-lg font-bold mb-4 text-gray-800">Ringkasan Pesanan</h3>
              <div class="space-y-3 mb-4">
                <div class="flex justify-between text-sm text-gray-700">
                  <span>Subtotal</span>
                  <span>Rp {{ formatPrice(cartStore.totalPrice) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700">
                  <span>Pengiriman</span>
                  <span class="text-blue-600">Gratis</span>
                </div>
              </div>
              <hr class="my-3 border-gray-200" />
              <div class="flex justify-between text-lg font-bold text-gray-800 mb-6">
                <span>Total</span>
                <span>Rp {{ formatPrice(cartStore.totalPrice) }}</span>
              </div>

              <div v-if="hasStockIssue" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                      Beberapa produk melebihi stok yang tersedia. Silahkan perbarui jumlah pesanan sebelum checkout.
                    </p>
                  </div>
                </div>
              </div>

              <router-link v-else to="/checkout"
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors text-base font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center justify-center">
                Lanjutkan Pembayaran
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </router-link>

              <div class="mt-4 text-center text-sm text-gray-500">
                atau <router-link to="/products" class="text-blue-600 hover:text-blue-800">lanjutkan belanja</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <FooterComponent class="flex-none" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const updating = ref(null)
const removing = ref(null)
const clearing = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const hasStockIssue = computed(() => {
  return cartStore.items.some(item => item.quantity > item.product.stock)
})

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1) return

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
    errorMessage.value = 'Gagal memperbarui jumlah'
    console.error('Update quantity error:', error)
  } finally {
    updating.value = null
    clearMessages()
  }
}

const removeItem = async (itemId) => {
  if (!confirm('Hapus item dari keranjang?')) return

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
    errorMessage.value = 'Gagal menghapus item'
    console.error('Remove item error:', error)
  } finally {
    removing.value = null
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

const clearMessages = () => {
  setTimeout(() => {
    successMessage.value = ''
    errorMessage.value = ''
  }, 3000)
}

// Initialize on mount
onMounted(() => {
  authStore.initializeAuth()
  cartStore.fetchCartItems()
})
</script>
