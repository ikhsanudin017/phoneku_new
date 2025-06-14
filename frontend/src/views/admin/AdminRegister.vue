// @ts-nocheck
<template>
  <div class="flex min-h-screen font-sans">
    <!-- Left Side with Logo and Image -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-100 to-cyan-50 flex-col p-8 relative overflow-hidden">
      <div class="mb-4">
        <router-link to="/" class="transform hover:scale-105 transition-transform duration-300">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60" />
        </router-link>
      </div>
      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone" 
             class="w-full h-auto object-contain absolute inset-0 mx-auto my-8 transform hover:scale-105 transition-transform duration-500 scale-x-[-1]"
             style="max-height: 90vh" />
      </div>
    </div>

    <!-- Right Side with Registration Form -->
    <div class="w-full md:w-[45%] ml-auto bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 flex items-center justify-center p-8 relative">
      <div class="absolute inset-0 bg-grid-white/10"></div>
      <div class="w-full max-w-md transform transition-all relative">
        <!-- Form Container -->
        <div class="backdrop-blur-lg bg-white/10 rounded-2xl p-8 shadow-2xl border border-white/10">
          <div class="text-center text-white mb-8">
            <h2 class="text-3xl font-bold mb-2 text-shadow-glow">Admin Registration</h2>
            <p class="text-blue-200">Create your admin account</p>
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

          <!-- Registration Form -->
          <form @submit.prevent="register" class="space-y-6">
            <!-- Name Field -->
            <div>
              <label for="name" class="block text-sm font-medium text-blue-200 mb-2">Full Name</label>
              <div class="relative group">
                <input type="text" 
                       id="name" 
                       v-model="name" 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 group-hover:border-white/30"
                       placeholder="Enter your full name"
                       required
                       autocomplete="name" />
              </div>
            </div>

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
                       autocomplete="email" />
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
                       autocomplete="new-password" />
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white transition-colors">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Password Confirmation Field -->
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-blue-200 mb-2">Confirm Password</label>
              <div class="relative group">
                <input :type="showConfirmPassword ? 'text' : 'password'"
                       id="password_confirmation" 
                       v-model="password_confirmation" 
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 group-hover:border-white/30"
                       placeholder="Confirm your password"
                       required
                       autocomplete="new-password" />
                <button type="button" 
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white transition-colors">
                  <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="loading" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
              <span v-if="!loading">Register</span>
              <span v-else class="flex items-center justify-center">
                <i class="fas fa-circle-notch fa-spin mr-2"></i>
                Registering...
              </span>
            </button>

            <!-- Login Link -->
            <div class="text-center mt-6">
              <p class="text-sm text-blue-200">
                Already have an account?
                <button type="button" 
                        @click="goToLogin"
                        class="text-blue-300 hover:text-white ml-1 transition-colors">
                  Login here
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
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import adminAPI from '@/services/api/admin'
import { createSecurityHelper } from '@/services/security'

const router = useRouter()
const authStore = useAuthStore()
const securityHelper = createSecurityHelper()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')
const success = ref('')
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const validateForm = () => {
  if (!name.value.trim()) {
    error.value = 'Name is required'
    return false
  }
  if (!email.value.trim()) {
    error.value = 'Email is required'
    return false
  }
  if (!securityHelper.validateEmail(email.value)) {
    error.value = 'Please enter a valid email address'
    return false
  }
  if (!password.value || password.value.length < 8) {
    error.value = 'Password must be at least 8 characters'
    return false
  }
  if (password.value !== password_confirmation.value) {
    error.value = 'Passwords do not match'
    return false
  }
  error.value = ''
  return true
}

const register = async () => {
  if (!validateForm()) return

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    // Get admin code from environment
    const adminCode = import.meta.env.VITE_ADMIN_REGISTRATION_CODE

    if (!adminCode) {
      throw new Error('Admin registration code not configured')
    }

    // Log for debugging (remove in production)
    console.log('Attempting registration with code:', adminCode)

    // Prepare registration data
    const registrationData = {
      name: securityHelper.sanitizeInput(name.value),
      email: securityHelper.sanitizeInput(email.value),
      password: password.value,
      password_confirmation: password_confirmation.value,
      admin_code: adminCode.trim() // Ensure no whitespace
    }

    const response = await adminAPI.register(registrationData)

    if (response.data.success) {
      success.value = 'Registration successful! Redirecting to login...'
      
      // Clear form
      name.value = ''
      email.value = ''
      password.value = ''
      password_confirmation.value = ''
      
      // Redirect after delay
      setTimeout(() => {
        router.push({ 
          name: 'AdminLogin',
          query: { registered: 'true' }
        })
      }, 2000)
    } else {
      throw new Error(response.data.message || 'Registration failed')
    }
  } catch (err) {
    console.error('Registration error:', err)
    error.value = err.response?.data?.message || 
                 err.message || 
                 'An error occurred during registration'
                 
    if (err.response?.data?.errors) {
      // Display specific validation errors
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    }
  } finally {
    loading.value = false
  }
}

const goToLogin = () => {
  router.push({ name: 'AdminLogin' })
}

// Auto-hide alerts after 5 seconds
watch([error, success], (newVal) => {
  if (newVal) {
    setTimeout(() => {
      error.value = ''
      success.value = ''
    }, 5000)
  }
})
</script>

<style scoped>
/* Background grid pattern */
.bg-grid-white\/10 {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Text shadow for glow effect */
.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7), 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
