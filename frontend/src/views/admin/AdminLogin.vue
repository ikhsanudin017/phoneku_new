<template>
  <div class="flex min-h-screen font-sans">
    <!-- Left Side with Logo and Image -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-100 to-cyan-50 flex-col p-8 relative overflow-hidden">
      <div class="mb-4">
        <router-link to="/" class="transform hover:scale-105 transition-transform duration-300">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60">
        </router-link>
      </div>
      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone" 
             class="w-full h-auto object-contain absolute inset-0 mx-auto my-8 transform hover:scale-105 transition-transform duration-500 scale-x-[-1]"
             style="max-height: 90vh">
      </div>
    </div>

    <!-- Right Side with Login Form -->
    <div class="w-full md:w-[45%] ml-auto bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 flex items-center justify-center p-8 relative">
      <div class="absolute inset-0 bg-grid-white/10"></div>
      <div class="w-full max-w-md transform transition-all relative">
        <!-- Form Container -->
        <div class="backdrop-blur-lg bg-white/10 rounded-2xl p-8 shadow-2xl border border-white/10">
          <div class="text-center text-white mb-8">
            <h2 class="text-3xl font-bold mb-2 text-shadow-glow">Admin Login</h2>
            <p class="text-blue-200">Welcome back! Please login to your account.</p>
          </div>

          <!-- Success Alert -->
          <div v-if="success" id="success-alert" class="bg-green-900/50 border border-green-500/50 text-green-200 px-4 py-3 rounded-lg text-sm mb-4 flex justify-between items-center">
            <span>{{ success }}</span>
            <button @click="success = ''" class="text-green-200 hover:text-white">×</button>
          </div>

          <!-- Error Alert -->
          <div v-if="error" id="error-alert" class="bg-red-900/50 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm mb-4 flex justify-between items-center">
            <span>{{ error }}</span>
            <button @click="error = ''" class="text-red-200 hover:text-white">×</button>
          </div>

          <!-- Login Form -->
          <form @submit.prevent="login" class="space-y-6">
            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-blue-200 mb-2">Email Address</label>
              <div class="relative group">
                <input type="email" 
                       id="email" 
                       v-model="email" 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 group-hover:border-white/30"
                       placeholder="Enter your email"
                       required
                       autocomplete="email">
              </div>
            </div>

            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-medium text-blue-200 mb-2">Password</label>
              <div class="relative group">
                <input :type="showPassword ? 'text' : 'password'"
                       id="password" 
                       v-model="password" 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 group-hover:border-white/30"
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password">
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white transition-colors">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input type="checkbox" 
                       id="remember" 
                       v-model="remember"
                       class="w-4 h-4 bg-white/10 border border-white/20 rounded text-blue-600 focus:ring-blue-500">
                <label for="remember" class="ml-2 text-sm text-blue-200">Remember me</label>
              </div>
              <button type="button" 
                      @click="goToForgotPassword"
                      class="text-sm text-blue-300 hover:text-white transition-colors">
                Forgot password?
              </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="loading || isLocked" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
              <span v-if="!loading">Sign In</span>
              <span v-else class="flex items-center justify-center">
                <i class="fas fa-circle-notch fa-spin mr-2"></i>
                Signing in...
              </span>
            </button>

            <!-- Register Link -->
            <div class="text-center mt-6">
              <p class="text-sm text-blue-200">
                Don't have an account?
                <button type="button" 
                        @click="goToRegister"
                        class="text-blue-300 hover:text-white ml-1 transition-colors">
                  Register here
                </button>
              </p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { createSecurityHelper } from '@/services/security'
import adminAPI from '@/services/api/admin'
import api from '@/services/api'

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
const showPassword = ref(false)

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
    error.value = `Too many login attempts. Please try again in ${remainingLockoutTime.value} minutes.`
    return
  }

  if (!email.value || !password.value) {
    error.value = 'Email and password are required'
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    // Sanitize and validate email
    const sanitizedEmail = securityHelper.sanitizeInput(email.value)
    if (!securityHelper.validateEmail(sanitizedEmail)) {
      error.value = 'Please enter a valid email address'
      loading.value = false
      return
    }

    // Use adminAPI for login (which handles CSRF internally)
    const response = await adminAPI.login({
      email: sanitizedEmail,
      password: password.value,
      remember: remember.value
    })

    if (response.data.success) {
      success.value = 'Login successful! Redirecting...'

      // Save token and user data
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      
      // Set authorization header
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

      // Reset form
      email.value = ''
      password.value = ''
      remember.value = false

      // Reset security helper
      securityHelper.resetLoginAttempts()

      // Force refresh user state (jika pakai Pinia/Vuex, bisa dispatch fetchUser)
      if (authStore.fetchUser) {
        await authStore.fetchUser()
      }

      // Redirect langsung tanpa delay
      router.replace({ name: 'AdminDashboard' })
      return
    } else {
      throw new Error(response.data.message || 'Login failed')
    }
  } catch (err) {
    console.error('Login error:', err)
    error.value = err.response?.data?.message || 'An unexpected error occurred'
    
    // Handle rate limiting
    if (err.response?.status === 429) {
      securityHelper.incrementLoginAttempts()
      if (securityHelper.shouldLockAccount()) {
        securityHelper.lockAccount()
      }
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
