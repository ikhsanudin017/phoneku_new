<template>
  <div>
    <!-- Header with Wave -->
    <div class="relative">
      <div class="bg-blue-500 min-h-[400px] sm:min-h-[400px] md:min-h-[500px] lg:min-h-[400px] xl:min-h-[350px] relative">
      </div>

      <!-- Wave SVG -->
      <div class="absolute bottom-[-24px] left-0 w-full overflow-hidden wave-container" style="line-height: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
          <path fill="#f9fafb" fill-opacity="1"
            d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,266.7C960,267,1056,245,1152,224C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
          </path>
        </svg>
      </div>

      <!-- Banner Image -->
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4/5 max-w-3xl z-0 pointer-events-none">
        <div class="relative w-full h-full overflow-hidden">
          <img src="/img/banner4.png" alt="Smartphones"
            class="object-contain w-full h-auto max-h-[180px] sm:max-h-[250px] md:max-h-[300px] lg:max-h-[350px] xl:max-h-[400px]">
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 -mt-48 relative z-10">
      <div class="bg-white rounded-xl p-6 shadow-md max-w-3xl mx-auto">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-900">Ubah Email</h2>
          <p class="text-gray-600">Update alamat email Anda dengan verifikasi</p>
        </div>

        <!-- Current Email -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
          <div class="text-center">
            <h3 class="text-sm font-medium text-gray-700 mb-1">Email Saat Ini</h3>
            <p class="text-lg text-gray-900">{{ authStore.user?.email }}</p>
          </div>
        </div>

        <!-- Step 1: Enter New Email -->
        <div v-if="step === 1">
          <form @submit.prevent="sendOTP" class="space-y-6">
            <div>
              <label for="new_email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email Baru</label>
              <input
                id="new_email"
                v-model="form.new_email"
                type="email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan alamat email baru Anda"
              />
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan kata sandi saat ini untuk verifikasi"
              />
            </div>

            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
              {{ errorMessage }}
            </div>

            <div class="flex justify-end space-x-4">
              <button
                type="button"
                @click="$router.push('/profile/tentang-saya')"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              >
                <span v-if="loading" class="mr-2">
                  <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                </span>
                {{ loading ? 'Mengirim OTP...' : 'Kirim Kode Verifikasi' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Step 2: Enter OTP -->
        <div v-if="step === 2">
          <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
              Kami telah mengirimkan kode verifikasi ke <strong>{{ form.new_email }}</strong>. Silakan periksa email Anda dan masukkan kode di bawah ini.
            </div>
          </div>

          <form @submit.prevent="verifyOTP" class="space-y-6">
            <div>
              <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">Kode Verifikasi</label>
              <input
                id="otp"
                v-model="form.otp"
                type="text"
                required
                maxlength="6"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl tracking-widest"
                placeholder="000000"
              />
              <p class="text-sm text-gray-500 mt-1">Masukkan kode 6 digit yang dikirim ke email Anda</p>
            </div>

            <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
              {{ errorMessage }}
            </div>

            <div class="flex justify-between items-center">
              <button
                type="button"
                @click="step = 1; form.otp = ''"
                class="text-blue-600 hover:text-blue-700"
              >
                ← Kembali ke Masukan Email
              </button>

              <div class="flex space-x-4">
                <button
                  type="button"
                  @click="resendOTP"
                  :disabled="resendCooldown > 0 || loading"
                  class="text-blue-600 hover:text-blue-700 disabled:text-gray-400"
                >
                  {{ resendCooldown > 0 ? `Kirim Ulang dalam ${resendCooldown}s` : 'Kirim Ulang Kode' }}
                </button>
                <button
                  type="submit"
                  :disabled="loading || form.otp.length !== 6"
                  class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                >
                  <span v-if="loading" class="mr-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                  </span>
                  {{ loading ? 'Memverifikasi...' : 'Verifikasi & Perbarui Email' }}
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded">
          {{ successMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { userAPI } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const step = ref(1)
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const resendCooldown = ref(0)

const form = reactive({
  new_email: '',
  password: '',
  otp: ''
})

let cooldownInterval = null

const sendOTP = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await userAPI.changeEmailRequest({
      new_email: form.new_email,
      password: form.password
    })

    if (response.data.success) {
      step.value = 2
      startResendCooldown()
    } else {
      errorMessage.value = response.data.message || 'Failed to send verification code'
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage.value = errors.join(', ')
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to send verification code'
    }
    console.error('Send OTP error:', error)
  } finally {
    loading.value = false
  }
}

const verifyOTP = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await userAPI.changeEmailVerify({
      new_email: form.new_email,
      otp: form.otp
    })

    if (response.data.success) {
      successMessage.value = 'Email updated successfully! Please log in again with your new email.'

      // Update user email in store
      authStore.user.email = form.new_email

      // Redirect to login after delay
      setTimeout(() => {
        authStore.logout()
        router.push('/login')
      }, 2000)
    } else {
      errorMessage.value = response.data.message || 'Invalid verification code'
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage.value = errors.join(', ')
    } else {
      errorMessage.value = error.response?.data?.message || 'Invalid verification code'
    }
    console.error('Verify OTP error:', error)
  } finally {
    loading.value = false
  }
}

const resendOTP = async () => {
  if (resendCooldown.value > 0) return

  loading.value = true
  errorMessage.value = ''

  try {
    const response = await userAPI.changeEmailRequest({
      new_email: form.new_email,
      password: form.password
    })

    if (response.data.success) {
      startResendCooldown()
      // Show temporary success message
      const originalError = errorMessage.value
      errorMessage.value = ''
      successMessage.value = 'Verification code sent!'
      setTimeout(() => {
        successMessage.value = ''
        errorMessage.value = originalError
      }, 3000)
    } else {
      errorMessage.value = response.data.message || 'Failed to resend verification code'
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Failed to resend verification code'
    console.error('Resend OTP error:', error)
  } finally {
    loading.value = false
  }
}

const startResendCooldown = () => {
  resendCooldown.value = 60
  cooldownInterval = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) {
      clearInterval(cooldownInterval)
      cooldownInterval = null
    }
  }, 1000)
}

onUnmounted(() => {
  if (cooldownInterval) {
    clearInterval(cooldownInterval)
  }
})
</script>
