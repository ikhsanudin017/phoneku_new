<template>
  <div class="min-h-screen flex bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900">
    <!-- Left Side with Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-8 relative">
        <div class="text-center">
          <router-link to="/welcome" class="inline-block mb-4">
            <h2 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">PhoneKu</h2>
          </router-link>
        </div>

        <div class="backdrop-blur-lg bg-white/10 rounded-2xl p-8 shadow-2xl border border-white/10">
          <form class="space-y-6" @submit.prevent="handleForgotPassword">
            <div>
              <label for="email" class="block text-sm font-medium text-white">Email Anda</label>
              <div class="mt-1">
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  v-model="email"
                  class="appearance-none block w-full px-3 py-2 border border-transparent rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-gray-800 text-white sm:text-sm"
                  placeholder="name@example.com"
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="animate-spin h-5 w-5 mr-3 border-t-2 border-b-2 border-white rounded-full"></span>
              Kirim Permintaan Reset
            </button>

            <div class="text-center space-y-2">
              <p class="text-sm text-gray-300">
                <router-link to="/admin/login" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                  Kembali ke login
                </router-link>
              </p>
            </div>
          </form>
        </div>

        <div v-if="message" class="bg-green-900/50 border border-green-500/50 text-green-200 px-4 py-3 rounded-lg text-sm mt-2">{{ message }}</div>
        <div v-if="error" class="bg-red-900/50 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm mt-2">{{ error }}</div>
      </div>
    </div>

    <!-- Right Side with Image -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-indigo-100 to-purple-100 flex-col p-8 relative overflow-hidden shadow-2xl">
      <div class="mb-4 transform hover:scale-105 transition-transform duration-300">
        <router-link to="/welcome">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60">
        </router-link>
      </div>

      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone"
             class="w-full h-auto object-contain absolute inset-25 mx-auto my-8 transform hover:scale-105 transition-transform duration-500 scale-x-[-1]"
             style="max-height: 90vh">
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { createSecurityHelper } from '@/services/security'
import { authAPI } from '@/services/api'
const router = useRouter()
const authStore = useAuthStore()
const securityHelper = createSecurityHelper()

const email = ref('')
const loading = ref(false)
const message = ref('')
const error = ref('')

// Auto-hide alerts after 5 seconds
watch(error, (val) => {
  if (val) {
    setTimeout(() => { error.value = '' }, 5000)
  }
})

watch(message, (val) => {
  if (val) {
    setTimeout(() => { message.value = '' }, 5000)
  }
})

const handleForgotPassword = async () => {
  if (loading.value) return;

  // Validate email
  const sanitizedEmail = securityHelper.sanitizeInput(email.value)
  if (!securityHelper.validateEmail(sanitizedEmail)) {
    error.value = 'Masukkan alamat email yang valid'
    return
  }

  // Clear previous error messages
  error.value = ''
  message.value = ''

  loading.value = true

  try {
    // Send reset link using auth store
    const result = await authStore.sendAdminPasswordResetLink(sanitizedEmail)

    if (result.success) {
      message.value = result.message || 'Permintaan reset password berhasil dikirim. Silakan cek email Anda.'
      email.value = '' // Clear email field on success
      setTimeout(() => {
        router.push('/admin/login')
      }, 3000) // Redirect after 3 seconds
    } else {
      error.value = result.message || 'Gagal mengirim permintaan reset password.'
    }
  } catch (err) {
    console.error('Forgot password error:', err)
    if (err.response?.status === 429) {
      // Rate limit exceeded
      error.value = err.response.data.message || 'Terlalu banyak percobaan. Silakan tunggu beberapa saat.'
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Terjadi kesalahan saat mengirim permintaan reset password. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}

// Clear any timeouts when component is unmounted
let timeoutId
onUnmounted(() => {
  if (timeoutId) clearTimeout(timeoutId)
})
</script>

<style scoped>
/* Minimal styling */
</style>
