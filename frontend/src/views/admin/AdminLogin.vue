<template>
  <div class="flex min-h-screen font-sans">
    <!-- Left Side with Logo and Image -->
    <div class="hidden md:flex md:w-1/2 bg-white flex-col p-8 relative overflow-hidden">
      <div class="mb-4">
        <router-link to="/">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60" />
        </router-link>
      </div>
      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone"
             class="w-full h-auto object-contain absolute inset-25 mx-auto my-8"
             style="max-height: 90vh" />
      </div>
    </div>

    <!-- Right Side with Login Form -->
    <div class="w-full md:w-[45%] ml-auto bg-gradient-to-b from-blue-500 to-cyan-400 flex items-center justify-center p-8">
      <div class="w-full max-w-md transform transition-all">
        <div class="text-center text-white mb-8">
          <h2 class="text-3xl font-bold mb-2">Masuk Sebagai Admin</h2>
          <p class="text-sm opacity-90">Silahkan masuk jika sudah memiliki akun!</p>
        </div>

        <!-- Success Alert -->
        <div v-if="success" id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
          <span>{{ success }}</span>
          <button @click="success = ''" class="text-green-700">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Error Alert -->
        <div v-if="error || isLocked" id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
          <div>
            <ul class="list-disc list-inside">
              <li v-if="error">{{ error }}</li>
              <li v-if="isLocked">Akun terkunci. Silakan coba lagi dalam {{ remainingLockoutTime }} menit.</li>
            </ul>
          </div>
          <button @click="error = ''" class="text-red-700">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Login Form -->
        <form class="space-y-4" @submit.prevent="login">
          <div>
            <label for="email" class="block text-white mb-2 text-sm font-semibold drop-shadow">Email</label>
            <input
              id="email"
              v-model="email"
              name="email"
              type="email"
              autocomplete="email"
              required
              class="w-full px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white/90 text-gray-800 placeholder-gray-400 shadow-sm border border-gray-200"
              placeholder="Masukkan email admin"
            />
          </div>

          <div>
            <label for="password" class="block text-white mb-2 text-sm font-semibold drop-shadow">Password</label>
            <input
              id="password"
              v-model="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              class="w-full px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white/90 text-gray-800 placeholder-gray-400 shadow-sm border border-gray-200"
              placeholder="Masukkan password anda"
            />
          </div>

          <div class="flex justify-between items-center">
            <div class="flex items-center">
              <input type="checkbox" id="remember" v-model="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-400 transition-colors duration-200" />
              <label for="remember" class="ml-2 text-white text-sm">Ingat saya</label>
            </div>
            <div>
              <router-link
                to="/admin/forgot-password"
                class="text-blue-50 hover:text-white text-sm hover:underline transition-colors duration-200"
              >Lupa Password?</router-link>
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading || isLocked"
            class="w-full bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-400 text-white py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-gradient-to-l focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
              Signing in...
            </span>
            <span v-else>Masuk</span>
          </button>

          <div class="border-t border-white/20 pt-4 mt-6">
            <p class="text-center text-white text-sm">
              Belum memiliki akun?
              <router-link
                to="/admin/register"
                class="text-blue-200 hover:underline bg-transparent ml-1 focus:outline-none"
              >Daftar sekarang!</router-link>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { createSecurityHelper } from '@/services/security'

const router = useRouter()
const authStore = useAuthStore()
const securityHelper = createSecurityHelper()

// Reactive states
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const success = ref('')
const remember = ref(false)

// Computed states
const isLocked = computed(() => securityHelper.isAccountLocked())
const remainingLockoutTime = computed(() => {
  const remaining = Math.ceil(securityHelper.getRemainingLockoutTime() / 1000 / 60)
  return remaining > 0 ? remaining : 0
})

// Navigation handlers
const goToForgotPassword = () => {
  router.push({ name: 'AdminForgotPassword' })
}

const goToRegister = () => {
  router.push({ name: 'AdminRegister' })
}

// Auto-hide alerts after 5 seconds
watch(error, (val) => {
  if (val) {
    setTimeout(() => { error.value = '' }, 5000)
  }
})
watch(success, (val) => {
  if (val) {
    setTimeout(() => { success.value = '' }, 5000)
  }
})

// Login handler
const login = async () => {
  if (isLocked.value) {
    error.value = `Terlalu banyak percobaan gagal. Silakan coba lagi dalam ${remainingLockoutTime.value} menit.`
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    await authStore.ensureCsrf()

    // Sanitize inputs
    const sanitizedEmail = securityHelper.sanitizeInput(email.value)
    if (!securityHelper.validateEmail(sanitizedEmail)) {
      error.value = 'Masukkan alamat email yang valid'
      loading.value = false
      return
    }

    const result = await authStore.adminLogin({
      email: sanitizedEmail,
      password: password.value,
      remember: remember.value
    })

    if (result.success) {
      password.value = ''
      securityHelper.resetLockout()
      success.value = 'Login berhasil!'
      setTimeout(() => {
        router.push({ name: 'AdminDashboard' })
      }, 1000)
    } else if (result.requiresVerification) {
      error.value = result.message
      // Show verification required dialog or message
      success.value = 'Link verifikasi telah dikirim ke email Anda.'
    } else {
      error.value = result.message || 'Login gagal'
      securityHelper.incrementLoginAttempts()
    }
  } catch (err) {
    console.error('Login error:', err)
    if (err.response?.status === 429) {
      error.value = 'Terlalu banyak percobaan. Silakan tunggu beberapa saat.'
    } else {
      error.value = err.response?.data?.message || 'Terjadi kesalahan saat login'
      securityHelper.incrementLoginAttempts()
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-text-fill-color: #222;
  -webkit-box-shadow: 0 0 0px 1000px #fff inset;
  transition: background-color 5000s ease-in-out 0s;
}

input, button, a {
  transition: all 0.2s ease-in-out;
}

button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.2);
}

/* Link hover effect */
a:hover {
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
