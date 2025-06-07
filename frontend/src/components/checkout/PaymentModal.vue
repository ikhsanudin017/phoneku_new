<template>
  <div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
    <div class="modal-content relative bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto p-0 z-10 animate-fadeIn">
      <button
        type="button"
        class="absolute top-3 right-3 text-gray-400 hover:text-blue-500 text-2xl font-bold z-20"
        @click="closeModal"
      >&times;</button>

      <!-- Step 1: Pilih Metode Pembayaran -->
      <div v-show="currentStep === 'method'" class="payment-step">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-800">Pilih Metode Pembayaran</h2>
          <p class="text-sm text-gray-600 mt-1">Pilih cara pembayaran yang paling mudah untuk Anda</p>
        </div>

        <div class="p-6 max-h-96 overflow-y-auto">
          <!-- Bank Transfer -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase">Transfer Bank</h3>
            <div class="space-y-2">
              <label v-for="bank in banks" :key="bank.value"
                class="payment-option block p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition"
                :class="{ 'border-blue-500 bg-blue-50': selectedMethod === bank.value }">
                <input
                  type="radio"
                  name="payment_method"
                  :value="bank.value"
                  v-model="selectedMethod"
                  class="hidden">
                <div class="flex items-center gap-3">
                  <img :src="'/img/payment/' + bank.image" :alt="bank.name" class="w-10 h-8 object-contain">
                  <span class="font-medium">{{ bank.name }}</span>
                  <span class="ml-auto text-green-600 text-sm">Gratis</span>
                </div>
              </label>
            </div>
          </div>

          <!-- E-Wallet -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase">E-Wallet</h3>
            <div class="space-y-2">
              <label v-for="wallet in ewallets" :key="wallet.value"
                class="payment-option block p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition"
                :class="{ 'border-blue-500 bg-blue-50': selectedMethod === wallet.value }">
                <input
                  type="radio"
                  name="payment_method"
                  :value="wallet.value"
                  v-model="selectedMethod"
                  class="hidden">
                <div class="flex items-center gap-3">
                  <img :src="'/img/payment/' + wallet.image" :alt="wallet.name" class="w-10 h-8 object-contain">
                  <span class="font-medium">{{ wallet.name }}</span>
                  <span class="ml-auto" :class="wallet.fee ? 'text-red-500' : 'text-green-600'">
                    {{ wallet.fee ? `Rp${formatPrice(wallet.fee)}` : 'Gratis' }}
                  </span>
                </div>
              </label>
            </div>
          </div>

          <!-- Other Methods -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase">Metode Lainnya</h3>
            <div class="space-y-2">
              <label v-for="method in otherMethods" :key="method.value"
                class="payment-option block p-3 border rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition"
                :class="{ 'border-blue-500 bg-blue-50': selectedMethod === method.value }">
                <input
                  type="radio"
                  name="payment_method"
                  :value="method.value"
                  v-model="selectedMethod"
                  class="hidden">
                <div class="flex items-center gap-3">
                  <div v-if="method.icon" class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                    <i :class="method.icon" class="text-blue-600"></i>
                  </div>
                  <img v-else :src="'/img/payment/' + method.image" :alt="method.name" class="w-10 h-8 object-contain">
                  <span class="font-medium">{{ method.name }}</span>
                  <span class="ml-auto" :class="method.fee ? 'text-red-500' : 'text-green-600'">
                    {{ method.fee ? `Rp${formatPrice(method.fee)}` : 'Gratis' }}
                  </span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 bg-gray-50">
          <div class="flex justify-between">
            <button
              type="button"
              class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition"
              @click="closeModal"
            >
              Batal
            </button>
            <button
              type="button"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
              :disabled="!selectedMethod"
              @click="showConfirm"
            >
              Lanjutkan
            </button>
          </div>
        </div>
      </div>

      <!-- Step 2: Konfirmasi Order -->
      <div v-show="currentStep === 'confirm'" class="payment-step">
        <div class="p-6 border-b border-gray-200">
          <button
            type="button"
            class="text-blue-500 hover:text-blue-600 mb-2"
            @click="currentStep = 'method'"
          >
            <i class="fas fa-arrow-left mr-2"></i>Kembali
          </button>
          <h2 class="text-xl font-bold text-gray-800">Konfirmasi Pesanan</h2>
          <p class="text-sm text-gray-600 mt-1">Pastikan semua informasi sudah benar sebelum membayar</p>
        </div>

        <div class="p-6">
          <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center mb-2">
              <i class="fas fa-credit-card text-blue-500 mr-2"></i>
              <span class="font-medium">Metode Pembayaran</span>
            </div>
            <div class="text-gray-700">{{ getSelectedMethodName }}</div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center mb-3">
              <i class="fas fa-shopping-cart text-blue-500 mr-2"></i>
              <span class="font-medium">Ringkasan Pesanan</span>
            </div>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span>Total Pembayaran</span>
                <span class="font-bold">Rp{{ formatPrice(finalAmount) }}</span>
              </div>
            </div>
          </div>

          <div class="text-xs text-gray-500 mb-4">
            Dengan melanjutkan pembayaran, Anda menyetujui
            <a href="#" class="text-blue-500 hover:underline">Syarat & Ketentuan</a>
            dan
            <a href="#" class="text-blue-500 hover:underline">Kebijakan Privasi</a>
            PhoneKu
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 bg-gray-50">
          <button
            type="button"
            class="w-full py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition"
            :disabled="processing"
            @click="processPayment"
          >
            <i class="fas fa-lock mr-2"></i>
            {{ processing ? 'Memproses...' : 'Bayar Sekarang' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  totalAmount: {
    type: Number,
    required: true
  },
  selectedPaymentMethod: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'select-payment', 'process-payment'])

const currentStep = ref('method')
const selectedMethod = ref(props.selectedPaymentMethod)
const processing = ref(false)

// Payment methods data
const banks = [
  { value: 'bank_bca', name: 'Bank BCA', image: 'bca.png' },
  { value: 'bank_mandiri', name: 'Bank Mandiri', image: 'mandiri.png' },
  { value: 'bank_bni', name: 'Bank BNI', image: 'bni.png' },
  { value: 'bank_bri', name: 'Bank BRI', image: 'bri.png' }
]

const ewallets = [
  { value: 'gopay', name: 'GoPay', image: 'gopay.png' },
  { value: 'shopeepay', name: 'ShopeePay', image: 'shopeepay.png', fee: 2500 },
  { value: 'ovo', name: 'OVO', image: 'ovo.png', fee: 2000 },
  { value: 'dana', name: 'DANA', image: 'dana.png' }
]

const otherMethods = [
  { value: 'credit_card', name: 'Kartu Kredit/Debit', image: 'cc.png' },
  { value: 'alfamart', name: 'Alfamart', image: 'alfamart.png', fee: 2500 },
  { value: 'indomaret', name: 'Indomaret', image: 'indomaret.png', fee: 2500 },
  {
    value: 'cod',
    name: 'Bayar di Tempat (COD)',
    icon: 'fas fa-money-bill-wave'
  }
]

const getSelectedMethodName = computed(() => {
  const allMethods = [...banks, ...ewallets, ...otherMethods]
  const method = allMethods.find(m => m.value === selectedMethod.value)
  return method ? method.name : ''
})

const finalAmount = computed(() => {
  const method = [...ewallets, ...otherMethods].find(m => m.value === selectedMethod.value)
  return props.totalAmount + (method?.fee || 0)
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const showConfirm = () => {
  emit('select-payment', selectedMethod.value)
  currentStep.value = 'confirm'
}

const processPayment = async () => {
  processing.value = true
  emit('process-payment', {
    method: selectedMethod.value,
    amount: finalAmount.value
  })
}

const closeModal = () => {
  emit('close')
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}

.animate-fadeIn {
  animation: fadeIn 0.2s;
}

.payment-option input:checked + div {
  @apply border-blue-500 bg-blue-50;
}
</style>
