// @ts-nocheck
<template>
  <div class="min-h-screen flex bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
    <div class="w-full md:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-8">
        <div class="text-center">
          <router-link to="/" class="inline-block">
            <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-16 mx-auto mb-4">
          </router-link>
          <h2 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-100 to-white">
            Register Admin
          </h2>
          <p class="text-blue-200 mt-2">
            Silakan lengkapi data untuk mendaftar sebagai admin
          </p>
        </div>

        <div v-if="errorMessage" class="bg-red-500/10 border border-red-500/20 text-red-200 px-4 py-3 rounded-lg flex items-center">
          <i class="fas fa-exclamation-circle mr-3"></i>
          {{ errorMessage }}
        </div>

        <div class="backdrop-blur-lg bg-white/10 rounded-xl p-8 shadow-2xl border border-white/20 relative overflow-hidden">
          <div class="absolute inset-0 bg-grid-white/10 opacity-10"></div>

          <form @submit.prevent="register" class="space-y-6 relative">
            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-user mr-2 opacity-70"></i>
                Nama Lengkap
              </label>
              <input
                v-model="form.name"
                type="text"
                required
                :class="[
                  'w-full px-4 py-3 bg-white/10 border rounded-lg text-white placeholder-gray-400 focus:outline-none transition-all duration-200 group-hover:border-white/30',
                  {
                    'border-white/20': !form.name,
                    'border-red-500/50': nameError,
                    'border-green-500/50': form.name && !nameError,
                    'focus:ring-2 focus:ring-blue-500/50': !nameError,
                    'focus:ring-2 focus:ring-red-500/50': nameError
                  }
                ]"
                placeholder="Masukkan nama lengkap"
                @input="validateName"
              />
              <p v-if="nameError" class="mt-1 text-sm text-red-300">
                {{ nameError }}
              </p>
            </div>

            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-envelope mr-2 opacity-70"></i>
                Email
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
                    'focus:ring-2 focus:ring-blue-500/50': !emailError,
                    'focus:ring-2 focus:ring-red-500/50': emailError
                  }
                ]"
                placeholder="Masukkan email"
                @input="validateEmail"
              />
              <p v-if="emailError" class="mt-1 text-sm text-red-300">
                {{ emailError }}
              </p>
            </div>

            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-lock mr-2 opacity-70"></i>
                Password
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  :class="[
                    'w-full px-4 py-3 bg-white/10 border rounded-lg text-white placeholder-gray-400 focus:outline-none transition-all duration-200 group-hover:border-white/30',
                    {
                      'border-white/20': !form.password,
                      'border-red-500/50': !isPasswordValid && form.password,
                      'border-green-500/50': isPasswordValid,
                      'focus:ring-2 focus:ring-blue-500/50': isPasswordValid,
                      'focus:ring-2 focus:ring-red-500/50': !isPasswordValid && form.password
                    }
                  ]"
                  placeholder="Minimal 8 karakter"
                  @input="validatePassword"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                >
                  <i :class="['fas', showPassword ? 'fa-eye-slash' : 'fa-eye']"></i>
                </button>
              </div>
              <div class="mt-2 text-sm">
                <div class="grid grid-cols-2 gap-2">
                  <span :class="['inline-flex items-center', passwordCriteria.length ? 'text-green-300' : 'text-red-300']">
                    <i :class="['mr-1 fas fa-xs', passwordCriteria.length ? 'fa-check' : 'fa-times']"></i>
                    Min. 8 karakter
                  </span>
                  <span :class="['inline-flex items-center', passwordCriteria.hasNumber ? 'text-green-300' : 'text-red-300']">
                    <i :class="['mr-1 fas fa-xs', passwordCriteria.hasNumber ? 'fa-check' : 'fa-times']"></i>
                    Angka
                  </span>
                   <span :class="['inline-flex items-center', passwordCriteria.hasUpper ? 'text-green-300' : 'text-red-300']">
                    <i :class="['mr-1 fas fa-xs', passwordCriteria.hasUpper ? 'fa-check' : 'fa-times']"></i>
                    Huruf besar
                  </span>
                  <span :class="['inline-flex items-center', passwordCriteria.hasLower ? 'text-green-300' : 'text-red-300']">
                    <i :class="['mr-1 fas fa-xs', passwordCriteria.hasLower ? 'fa-check' : 'fa-times']"></i>
                    Huruf kecil
                  </span>
                  <span :class="['inline-flex items-center col-span-2', passwordCriteria.hasSpecial ? 'text-green-300' : 'text-red-300']">
                    <i :class="['mr-1 fas fa-xs', passwordCriteria.hasSpecial ? 'fa-check' : 'fa-times']"></i>
                    Karakter khusus (!@#$...)
                  </span>
                </div>
              </div>
            </div>

            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-lock mr-2 opacity-70"></i>
                Konfirmasi Password
              </label>
              <div class="relative">
                <input
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  :class="[
                    'w-full px-4 py-3 bg-white/10 border rounded-lg text-white placeholder-gray-400 focus:outline-none transition-all duration-200 group-hover:border-white/30',
                    {
                      'border-white/20': !form.password_confirmation,
                      'border-red-500/50': passwordConfirmationError,
                      'border-green-500/50': form.password_confirmation && !passwordConfirmationError,
                      'focus:ring-2 focus:ring-blue-500/50': !passwordConfirmationError,
                      'focus:ring-2 focus:ring-red-500/50': passwordConfirmationError
                    }
                  ]"
                  placeholder="Masukkan ulang password"
                  @input="validatePasswordConfirmation"
                />
              </div>
              <p v-if="passwordConfirmationError" class="mt-1 text-sm text-red-300">
                {{ passwordConfirmationError }}
              </p>
            </div>

            <div class="group">
              <label class="block text-sm font-medium text-white mb-2 flex items-center">
                <i class="fas fa-key mr-2 opacity-70"></i>
                Kode Admin
              </label>
              <div class="relative">
                <input
                  v-model="form.admin_code"
                  :type="showAdminCode ? 'text' : 'password'"
                  required
                  :class="[
                    'w-full px-4 py-3 bg-white/10 border rounded-lg text-white placeholder-gray-400 focus:outline-none transition-all duration-200 group-hover:border-white/30',
                    {
                      'border-white/20': !form.admin_code,
                      'border-red-500/50': adminCodeError,
                      'border-green-500/50': form.admin_code && !adminCodeError,
                      'focus:ring-2 focus:ring-blue-500/50': !adminCodeError,
                      'focus:ring-2 focus:ring-red-500/50': adminCodeError
                    }
                  ]"
                  placeholder="Masukkan kode admin"
                  @input="validateAdminCode"
                />
                <button
                  type="button"
                  @click="showAdminCode = !showAdminCode"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                >
                  <i :class="['fas', showAdminCode ? 'fa-eye-slash' : 'fa-eye']"></i>
                </button>
              </div>
              <p class="mt-1 text-sm text-blue-200/80">
                <i class="fas fa-info-circle mr-1"></i>
                Hubungi administrator untuk mendapatkan kode admin
              </p>
              <p v-if="adminCodeError" class="mt-1 text-sm text-red-300">
                {{ adminCodeError }}
              </p>
            </div>

            <button
              type="submit"
              :disabled="loading || !isFormValid"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></div>
                Mendaftar...
              </span>
              <span v-else class="flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i>
                Daftar
              </span>
            </button>

            <div class="text-center text-white/90">
              Sudah punya akun?
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
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { createSecurityHelper } from '@/services/security'

const router = useRouter()
const authStore = useAuthStore()
const securityHelper = createSecurityHelper()

// Form state
const loading = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)
const showAdminCode = ref(false)

// Validation states
const nameError = ref('')
const emailError = ref('')
const passwordCriteria = reactive({
  length: false,
  hasNumber: false,
  hasSpecial: false,
  hasUpper: false,
  hasLower: false
})
const passwordConfirmationError = ref('')
const adminCodeError = ref('')
const ADMIN_CODE = 'PK_ADMIN_2025'

// Form data
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  admin_code: ''
})

// Computed properties
const isPasswordValid = computed(() => {
    return passwordCriteria.length &&
           passwordCriteria.hasNumber &&
           passwordCriteria.hasSpecial &&
           passwordCriteria.hasUpper &&
           passwordCriteria.hasLower
})

const isFormValid = computed(() => {
  return !nameError.value &&
         !emailError.value &&
         !passwordConfirmationError.value &&
         !adminCodeError.value &&
         isPasswordValid.value &&
         form.name &&
         form.email &&
         form.password &&
         form.password_confirmation &&
         form.admin_code.trim() === ADMIN_CODE;
});

// Validation functions
const validateName = () => {
  const sanitizedName = securityHelper.sanitizeInput(form.name)
  if (!sanitizedName) {
    nameError.value = 'Nama wajib diisi'
  } else if (sanitizedName.length < 3) {
    nameError.value = 'Nama minimal 3 karakter'
  } else if (sanitizedName.length > 50) {
    nameError.value = 'Nama maksimal 50 karakter'
  } else if (!/^[a-zA-Z\s]*$/.test(sanitizedName)) {
    nameError.value = 'Nama hanya boleh mengandung huruf dan spasi'
  } else {
    nameError.value = ''
  }
}

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

const validatePassword = () => {
  passwordCriteria.length = form.password.length >= 8
  passwordCriteria.hasNumber = /\d/.test(form.password)
  passwordCriteria.hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(form.password)
  passwordCriteria.hasUpper = /[A-Z]/.test(form.password)
  passwordCriteria.hasLower = /[a-z]/.test(form.password)
  validatePasswordConfirmation()
}

const validatePasswordConfirmation = () => {
  if (form.password_confirmation && form.password !== form.password_confirmation) {
    passwordConfirmationError.value = 'Password tidak cocok'
  } else {
    passwordConfirmationError.value = ''
  }
}

const validateAdminCode = () => {
  if (!form.admin_code.trim()) {
    adminCodeError.value = 'Kode admin wajib diisi'
  } else if (form.admin_code.trim() !== ADMIN_CODE) {
    adminCodeError.value = 'Kode admin tidak valid'
  } else {
    adminCodeError.value = ''
  }
}

const register = async () => {
  // Lakukan validasi sekali lagi untuk semua field sebelum submit
  validateName();
  validateEmail();
  validatePassword();
  validatePasswordConfirmation();
  validateAdminCode();

  if (!isFormValid.value) {
    errorMessage.value = 'Mohon lengkapi semua field dengan benar sesuai persyaratan.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const sanitizedName = securityHelper.sanitizeInput(form.name)
    const sanitizedEmail = securityHelper.sanitizeInput(form.email)

    const result = await authStore.adminRegister({
      name: sanitizedName,
      email: sanitizedEmail,
      password: form.password,
      password_confirmation: form.password_confirmation,
      admin_code: form.admin_code.trim()
    })

    if (result.success) {
      router.push({
        name: 'AdminLogin',
        query: { message: 'Registrasi berhasil! Silakan login untuk melanjutkan.' }
      })
    } else {
      errorMessage.value = result.message || 'Gagal melakukan registrasi'
    }
  } catch (error) {
    console.error('Admin registration error:', error)
    if (error.response?.data?.errors) {
      const validationErrors = Object.values(error.response.data.errors).flat()
      errorMessage.value = validationErrors.join(', ')
    } else if (error.message === 'Network Error' || error.code === 'ERR_NETWORK') {
      errorMessage.value = 'Koneksi gagal. Periksa koneksi internet Anda.'
    } else {
      errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat registrasi'
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

/* Input field transitions */
input {
  transition: all 0.3s ease;
}

input:focus {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Error state styles */
input.error {
  border-color: rgba(239, 68, 68, 0.5);
}

input.error:focus {
  box-shadow: 0 2px 4px rgba(239, 68, 68, 0.1), 0 0 0 2px rgba(239, 68, 68, 0.5);
}

/* Success state styles */
input.success {
  border-color: rgba(16, 185, 129, 0.5);
}

input.success:focus {
  box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1), 0 0 0 2px rgba(16, 185, 129, 0.5);
}

/* Button loading state */
button:disabled {
  cursor: not-allowed;
  opacity: 0.7;
  transform: none !important;
}
</style>
