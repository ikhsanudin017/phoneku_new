<template>
  <div class="min-h-screen flex bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
    <!-- Left Side with Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
          <router-link to="/" class="inline-block">
            <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-16 mx-auto mb-4">
          </router-link>
          <h2 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-100 to-white">
            Reset Password Admin
          </h2>
          <p class="text-blue-200 mt-2">
            {{ step === 'email' ? 'Masukkan email untuk mendapatkan link reset password' : 'Masukkan kode reset dan password baru' }}
          </p>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-lg flex items-center">
          <i class="fas fa-exclamation-circle mr-3"></i>
          {{ errorMessage }}
        </div>

        <!-- Success Alert -->
        <div v-if="successMessage" class="bg-green-500/10 border border-green-500/20 text-green-200 px-4 py-3 rounded-lg flex items-center">
          <i class="fas fa-check-circle mr-3"></i>
          {{ successMessage }}
        </div>

        <!-- Form Container -->
        <div class="backdrop-blur-lg bg-white/10 rounded-xl p-8 shadow-2xl border border-white/20 relative overflow-hidden">
          <!-- Background Pattern -->
          <div class="absolute inset-0 bg-grid-white/10 opacity-10"></div>

          <!-- Email Request Form -->
          <form v-if="step === 'email'" @submit.prevent="handleForgotPassword" class="space-y-6 relative">
            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-envelope mr-2 opacity-70"></i>
                Email Admin
              </label>
              <input
                v-model="form.email"
                type="email"
                required
                :class="[
                  'w-full px-4 py-3 bg-white/10 border rounded-lg text-white placeholder-gray-400 focus:outline-none transition-all duration-200 group-hover:border-white/30',
                  {
                    'border-white/20': !form.email,
                    'border-red-500/50': emailError,
                    'border-green-500/50': form.email && !emailError,
                  }
                ]"
                placeholder="Masukkan email admin"
                @input="validateEmail"
              />
              <p v-if="emailError" class="mt-1 text-sm text-red-300">
                {{ emailError }}
              </p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading || !form.email || emailError"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                Mengirim...
              </span>
              <span v-else class="flex items-center justify-center">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Link Reset
              </span>
            </button>

            <!-- Login Link -->
            <div class="text-center text-white/90">
              Ingat password?
              <router-link
                to="/admin/login"
                class="text-blue-300 hover:text-blue-200 font-medium inline-flex items-center hover:underline ml-1"
              >
                <i class="fas fa-sign-in-alt mr-1"></i>
                Login disini
              </router-link>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Side with Image -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-indigo-100 to-purple-100 flex-col p-8 relative overflow-hidden">
      <div class="mb-4">
        <router-link to="/">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60">
        </router-link>
      </div>
      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone"
             class="w-full h-auto object-contain absolute inset-25 mx-auto my-8 transform hover:scale-105 transition-transform duration-500"
             style="max-height: 90vh">
      </div>
    </div>
  </div>
</template>

<script setup>
import { authAPI } from '@/services/api'
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { createSecurityHelper } from '@/services/security'

const router = useRouter()
const authStore = useAuthStore()
const securityHelper = createSecurityHelper()

// Form state
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const emailError = ref('')
const step = ref('email')

// Form data
const form = reactive({
  email: '',
  token: '',
  password: '',
  password_confirmation: ''
})

// Validation functions
const validateEmail = () => {
  const sanitizedEmail = securityHelper.sanitizeInput(form.email)
  if (!sanitizedEmail) {
    emailError.value = 'Email wajib diisi'
  } else if (!securityHelper.validateEmail(sanitizedEmail)) {
    emailError.value = 'Format email tidak valid'
  } else {
    emailError.value = ''
  }
}

const handleForgotPassword = async () => {
  if (loading.value || !form.email || emailError.value) return

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    // Sanitize email input
    const sanitizedEmail = securityHelper.sanitizeInput(form.email)

    // Send password reset request
    const result = await authStore.sendAdminPasswordResetLink(sanitizedEmail)

    if (result.success) {
      successMessage.value = result.message || 'Link reset password telah dikirim ke email Anda'
      // Clear form
      form.email = ''
      // Redirect to login after 3 seconds
      setTimeout(() => {
        router.push('/admin/login')
      }, 3000)
    } else {
      errorMessage.value = result.message || 'Gagal mengirim link reset password'
    }
  } catch (error) {
    console.error('Forgot password error:', error)
    if (error.response?.status === 429) {
      errorMessage.value = error.response.data.message || 'Terlalu banyak percobaan. Silakan tunggu beberapa saat.'
    } else {
      errorMessage.value = 'Terjadi kesalahan saat mengirim permintaan reset password'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Background grid pattern */
.bg-grid-white\/10 {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Form field aesthetics */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-text-fill-color: white;
  -webkit-box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0.3) inset;
  transition: background-color 5000s ease-in-out 0s;
}

/* Hover effects */
.group:hover .group-hover\:border-white\/30 {
  border-color: rgba(255, 255, 255, 0.3);
}

/* Focus styles */
input:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Button animations */
button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.2);
}

button:not(:disabled):active {
  transform: translateY(0);
}

/* Transition effects */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Loading indicator */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
