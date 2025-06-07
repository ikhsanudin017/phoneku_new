<template>
  <div class="min-h-screen bg-gray-50">
    <NavbarComponent />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

      <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kiri: Alamat, Produk, Kurir -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Alamat -->
          <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-medium text-gray-500 uppercase">Alamat Pengiriman</h2>
            </div>
            <div class="p-4">
              <div class="flex flex-col md:flex-row items-start md:items-center p-4 gap-4">
                <div class="text-blue-500 text-2xl md:text-3xl">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="flex-1">
                  <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-semibold text-gray-800 text-base md:text-lg">{{ address.recipientName }}</span>
                    <span class="text-gray-500 text-xs md:text-sm">({{ address.label || 'Alamat Utama' }})</span>
                  </div>
                  <div class="text-gray-700 text-sm md:text-base">{{ address.fullAddress }}</div>
                  <div class="text-gray-700 text-xs md:text-sm mt-1">Telp: {{ address.phone }}</div>
                </div>
                <button
                  type="button"
                  class="px-4 py-2 border border-blue-500 text-blue-500 rounded-md hover:bg-blue-50 transition font-medium mt-3 md:mt-0"
                  @click="showAddressModal = true"
                >
                  Ubah
                </button>
              </div>
            </div>
          </div>

          <!-- Produk -->
          <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-medium text-gray-500 uppercase">Produk Dipesan</h2>
            </div>
            <div class="p-4 space-y-6">
              <div v-for="item in cartStore.items" :key="item.id"
                class="flex flex-col md:flex-row items-start md:items-center gap-4 border-b last:border-b-0 pb-4 last:pb-0">
                <img :src="getImageUrl(item.product.image)" :alt="item.product.name"
                  class="w-20 h-20 object-contain rounded-lg border">
                <div class="flex-1">
                  <div class="font-semibold text-gray-800 text-base md:text-lg mb-1">{{ item.product.name }}</div>
                  <div class="text-xs text-gray-500 mb-1">Qty: {{ item.quantity }}</div>
                  <div class="text-xs text-gray-500">Harga Satuan: Rp{{ formatPrice(item.product.price) }}</div>
                </div>
                <div class="font-bold text-lg text-gray-800">
                  Rp{{ formatPrice(item.product.price * item.quantity) }}
                </div>
              </div>
              <div v-if="cartStore.items.length === 0" class="text-center text-gray-500">
                Tidak ada produk di keranjang.
              </div>
            </div>
          </div>

          <!-- Kurir -->
          <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
              <h2 class="text-lg font-medium text-gray-500 uppercase">Pilih Kurir</h2>
            </div>
            <div class="p-4 flex flex-col gap-3">
              <label v-for="courier in courierOptions" :key="courier.value"
                class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition gap-3"
                :class="{ 'border-blue-500 bg-blue-50': form.courier === courier.value }">
                <input type="radio" v-model="form.courier" :value="courier.value"
                  class="mr-2 accent-blue-500">
                <img :src="'/img/kurir/' + courier.image" :alt="courier.name"
                  class="w-8 h-8 object-contain mr-2">
                <div class="flex-1">
                  <div class="font-medium">{{ courier.name }}</div>
                  <div class="text-xs text-gray-500">{{ courier.duration }} • Rp{{ formatPrice(courier.cost) }}</div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Kanan: Ringkasan -->
        <div class="lg:col-span-1">
          <div class="sticky top-4">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
              <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Ringkasan Transaksi</h2>
                <div class="space-y-2 mb-4 text-gray-700">
                  <div class="flex justify-between">
                    <span>Total Harga</span>
                    <span>Rp{{ formatPrice(cartStore.totalPrice) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Ongkos Kirim</span>
                    <span>Rp{{ formatPrice(getShippingCost) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Biaya Jasa Aplikasi</span>
                    <span>Rp{{ formatPrice(serviceFee) }}</span>
                  </div>
                </div>
                <div class="flex justify-between font-bold text-xl pt-4 border-t border-gray-200 text-gray-800">
                  <span>Total Pembayaran</span>
                  <span>Rp{{ formatPrice(totalPayment) }}</span>
                </div>
                <button type="button"
                  class="w-full bg-blue-500 text-white rounded-lg py-3 mt-6 font-bold hover:bg-blue-600 transition text-lg"
                  :disabled="!isFormValid || processing"
                  @click="showPaymentModal = true">
                  {{ processing ? 'Memproses...' : 'Buat Pesanan' }}
                </button>
                <p class="text-gray-500 text-xs text-center mt-3">
                  Dengan melanjutkan pembayaran, kamu menyetujui S&K yang berlaku
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Alamat -->
    <AddressModal
      v-if="showAddressModal"
      :address="address"
      @close="showAddressModal = false"
      @save="saveAddress"
    />

    <!-- Modal Pembayaran -->
    <PaymentModal
      v-if="showPaymentModal"
      :total-amount="totalPayment"
      :selected-payment-method="form.paymentMethod"
      @close="showPaymentModal = false"
      @select-payment="selectPayment"
      @process-payment="processPayment"
    />

    <FooterComponent />
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
import PaymentModal from '@/components/checkout/PaymentModal.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const showAddressModal = ref(false)
const showPaymentModal = ref(false)
const processing = ref(false)
const serviceFee = ref(1000)

const address = reactive({
  recipientName: '',
  label: 'Rumah',
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

const selectPayment = (method) => {
  form.paymentMethod = method
}

const processPayment = async () => {
  processing.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))

    await cartStore.clearCart()

    router.push({
      path: '/profile/orders',
      query: { success: true }
    })
  } catch (error) {
    console.error('Payment error:', error)
  } finally {
    processing.value = false
    showPaymentModal.value = false
  }
}

onMounted(() => {
  // Pre-fill address if user is logged in
  if (authStore.user) {
    address.recipientName = authStore.user.name
    address.phone = authStore.user.phone || ''
    address.fullAddress = authStore.user.address || ''
  }
})
</script>

<style scoped>
@media (max-width: 600px) {
  .sticky.top-4 {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 40;
    border-radius: 0;
  }
}
</style>
