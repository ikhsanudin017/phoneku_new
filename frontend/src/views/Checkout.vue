<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <NavbarComponent class="flex-none" />

    <!-- Main Content -->
    <div class="flex-grow py-6 md:py-8">
      <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-4" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <router-link to="/welcome" class="text-gray-700 hover:text-blue-600">Home</router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <router-link to="/cart" class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">Cart</router-link>
              </div>
            </li>
            <li aria-current="page">
              <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-1 text-gray-500 md:ml-2">Checkout</span>
              </div>
            </li>
          </ol>
        </nav>

        <div class="flex items-center mb-6 bg-white p-4 rounded-lg border border-gray-200">
          <div class="flex-1">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Checkout</h1>
          </div>
          <router-link to="/cart" class="text-blue-600 hover:text-blue-700 text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Keranjang
          </router-link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          <!-- Left Column -->
          <div class="lg:col-span-8 space-y-4">
            <!-- Alamat -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <div class="flex items-center p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex-1">
                  <h2 class="text-base font-medium text-gray-700">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Alamat Pengiriman
                  </h2>
                </div>
                <button
                  type="button"
                  class="text-blue-600 hover:text-blue-700 text-sm font-medium"
                  @click="showAddressModal = true"
                >
                  Tambah Alamat
                </button>
              </div>
              <div class="p-4">
                <div class="text-center py-6">
                  <div class="text-gray-400 mb-2">
                    <i class="fas fa-map-marker-alt text-3xl"></i>
                  </div>
                  <p class="text-gray-500 mb-3">Belum ada alamat pengiriman</p>
                  <button
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 rounded-full hover:bg-blue-50 transition"
                    @click="showAddressModal = true"
                  >
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Alamat Baru
                  </button>
                </div>
              </div>
            </div>

            <!-- Produk -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-medium text-gray-700">
                  <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
                  Produk Dipesan
                </h2>
              </div>
              <div class="divide-y divide-gray-200">
                <div v-for="item in cartStore.items" :key="item.id"
                  class="p-4 hover:bg-gray-50 transition duration-150">
                  <div class="flex items-start gap-4">
                    <img :src="getImageUrl(item.product.image)" :alt="item.product.name"
                      class="w-16 h-16 object-contain rounded-md border border-gray-200 bg-white" />
                    <div class="flex-1 min-w-0">
                      <h3 class="font-medium text-gray-900 truncate">{{ item.product.name }}</h3>
                      <p class="text-sm text-gray-500 mt-1">Quantity: {{ item.quantity }}</p>
                    </div>
                    <div class="text-right">
                      <div class="font-medium text-gray-900">
                        Rp{{ formatPrice(item.product.price * item.quantity) }}
                      </div>
                      <div class="text-sm text-gray-500 mt-1">
                        Rp{{ formatPrice(item.product.price) }}/unit
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Kurir -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-medium text-gray-700">
                  <i class="fas fa-truck text-blue-600 mr-2"></i>
                  Opsi Pengiriman
                </h2>
              </div>
              <div class="p-4">
                <div class="space-y-3">
                  <label v-for="courier in courierOptions" :key="courier.value"
                    class="flex items-center p-3 border rounded-lg cursor-pointer transition"
                    :class="{ 'border-blue-600 bg-blue-50': form.courier === courier.value,
                             'border-gray-200 hover:border-blue-200': form.courier !== courier.value }">
                    <input type="radio" v-model="form.courier" :value="courier.value"
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                      <div class="flex items-center">
                        <img :src="'/img/kurir/' + courier.image" :alt="courier.name"
                          class="w-8 h-8 object-contain">
                        <div class="ml-3">
                          <div class="font-medium text-gray-900">{{ courier.name }}</div>
                          <div class="text-sm text-gray-500">{{ courier.duration }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="font-medium text-gray-900">Rp{{ formatPrice(courier.cost) }}</div>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
              <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-medium text-gray-700">
                  <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                  Metode Pembayaran
                </h2>
              </div>
              <div class="p-4">
                <div class="space-y-3">
                  <label v-for="method in paymentMethods" :key="method.id"
                    class="flex items-center p-3 border rounded-lg cursor-pointer transition"
                    :class="{ 'border-blue-600 bg-blue-50': form.paymentMethod === method.id,
                             'border-gray-200 hover:border-blue-200': form.paymentMethod !== method.id }">
                    <input type="radio" v-model="form.paymentMethod" :value="method.id"
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <div class="ml-3 flex-1">
                      <div class="flex items-center">
                        <img :src="'/img/payment/' + method.image" :alt="method.name"
                          class="h-6 object-contain">
                        <div class="ml-3">
                          <div class="font-medium text-gray-900">{{ method.name }}</div>
                          <div class="text-sm text-gray-500">{{ method.description }}</div>
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="lg:col-span-4">
            <div class="sticky top-4 space-y-4">
              <!-- Summary Card -->
              <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                  <h2 class="text-lg font-medium text-gray-900">Ringkasan Pesanan</h2>
                </div>
                <div class="p-4">
                  <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Total Harga ({{ cartStore.items.length }} Produk)</span>
                      <span class="text-gray-900">Rp{{ formatPrice(cartStore.totalPrice) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Total Ongkos Kirim</span>
                      <span class="text-gray-900">Rp{{ formatPrice(getShippingCost) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Biaya Layanan</span>
                      <span class="text-gray-900">Rp{{ formatPrice(serviceFee) }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                      <div class="flex justify-between">
                        <span class="text-base font-medium text-gray-900">Total Pembayaran</span>
                        <span class="text-xl font-bold text-blue-600">Rp{{ formatPrice(totalPayment) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Button -->
              <button
                type="button"
                class="w-full bg-blue-600 text-white rounded-lg py-3 px-4 font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                :disabled="!isFormValid || processing"
                @click="processPayment"
              >
                <span>{{ processing ? 'Memproses...' : 'Bayar Sekarang' }}</span>
                <svg v-if="!processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </button>

              <!-- Terms -->
              <p class="text-xs text-gray-500 text-center">
                Dengan melanjutkan, kamu menyetujui
                <a href="#" class="text-blue-600 hover:underline">Syarat & Ketentuan</a>
                yang berlaku
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <AddressModal
      v-if="showAddressModal"
      :address="address"
      @close="showAddressModal = false"
      @save="saveAddress"
    />

    <PaymentModal
      v-if="showPaymentModal"
      :total-amount="totalPayment"
      :selected-payment-method="form.paymentMethod"
      @close="showPaymentModal = false"
      @select-payment="selectPayment"
      @process-payment="processPayment"
    />

    <FooterComponent class="flex-none mt-auto" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
import AddressModal from '@/components/checkout/AddressModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const showAddressModal = ref(false)
const processing = ref(false)
const serviceFee = ref(1000)

const address = reactive({
  recipientName: '',
  label: '',
  phone: '',
  fullAddress: '',
  latitude: '',
  longitude: ''
})

const form = reactive({
  courier: 'jne',
  paymentMethod: ''
})

const courierOptions = [
  { value: 'jne', name: 'JNE Reguler', duration: '2-3 hari', cost: 20000, image: 'jne.png' },
  { value: 'jnt', name: 'J&T Express', duration: '1-2 hari', cost: 22000, image: 'jnt.png' },
  { value: 'sicepat', name: 'SiCepat', duration: '1-3 hari', cost: 18000, image: 'sicepat.png' }
]

const paymentMethods = [
  {
    id: 'bca',
    name: 'Transfer Bank BCA',
    description: 'Pembayaran melalui transfer bank BCA',
    image: 'bca.png'
  },
  {
    id: 'mandiri',
    name: 'Transfer Bank Mandiri',
    description: 'Pembayaran melalui transfer bank Mandiri',
    image: 'mandiri.png'
  },
  {
    id: 'bni',
    name: 'Transfer Bank BNI',
    description: 'Pembayaran melalui transfer bank BNI',
    image: 'bni.png'
  },
  {
    id: 'ovo',
    name: 'OVO',
    description: 'Pembayaran melalui dompet digital OVO',
    image: 'ovo.png'
  },
  {
    id: 'gopay',
    name: 'GoPay',
    description: 'Pembayaran melalui dompet digital GoPay',
    image: 'gopay.png'
  },
  {
    id: 'dana',
    name: 'DANA',
    description: 'Pembayaran melalui dompet digital DANA',
    image: 'dana.png'
  }
]

const getShippingCost = computed(() => {
  const courier = courierOptions.find(c => c.value === form.courier)
  return courier ? courier.cost : 0
})

const totalPayment = computed(() => {
  return cartStore.totalPrice + getShippingCost.value + serviceFee.value
})

const isFormValid = computed(() => {
  return address.recipientName &&
         address.phone &&
         address.fullAddress &&
         form.courier
})

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/img/placeholder.jpg'
  return `http://localhost:8000/storage/${imagePath}`
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const saveAddress = (newAddress) => {
  Object.assign(address, newAddress)
  showAddressModal.value = false
}

const processPayment = async () => {
  if (!form.paymentMethod) {
    alert('Silakan pilih metode pembayaran')
    return
  }

  if (!address.fullAddress) {
    alert('Silakan lengkapi alamat pengiriman')
    return
  }

  processing.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))

    // Process payment and create order
    const order = {
      items: cartStore.items,
      shipping: {
        courier: form.courier,
        address: address,
        cost: getShippingCost.value
      },
      payment: {
        method: form.paymentMethod,
        totalAmount: totalPayment.value
      }
    }

    // Clear cart after successful order
    await cartStore.clearCart()

    // Redirect to success page
    router.push({
      path: '/profile/orders',
      query: { success: true }
    })
  } catch (error) {
    console.error('Payment error:', error)
    alert('Terjadi kesalahan saat memproses pembayaran')
  } finally {
    processing.value = false
  }
}

// Reset all data on mount
onMounted(() => {
  address.recipientName = ''
  address.phone = ''
  address.fullAddress = ''
  address.label = ''
  form.paymentMethod = ''
})
</script>

<style scoped>
@media (max-width: 768px) {
  .sticky.top-4 {
    position: sticky;
    bottom: 0;
    top: auto;
    background: white;
    border-top: 1px solid #e5e7eb;
    border-radius: 1rem 1rem 0 0;
    box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
    padding: 1rem;
    margin: 0 -1rem;
  }
}
</style>
