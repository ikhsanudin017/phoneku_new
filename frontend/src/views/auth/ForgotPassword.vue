<template>
  <div class="flex min-h-screen font-sans bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Left Side with Logo and Image -->
    <div class="hidden md:flex md:w-1/2 bg-white flex-col p-8 relative overflow-hidden shadow-2xl">
      <div class="mb-4 transform hover:scale-105 transition-transform duration-300">
        <router-link to="/welcome">
          <img src="/img/logo2.png" alt="PhoneKu Logo" class="w-60">
        </router-link>
      </div>

      <div class="flex-1 flex items-center justify-center">
        <img src="/img/model.png" alt="Person with phone"
             class="w-full h-auto object-contain absolute inset-25 mx-auto my-8 transform hover:scale-105 transition-transform duration-500"
             style="max-height: 90vh">
      </div>
    </div>

    <!-- Right Side with Form -->
    <div class="w-full md:w-[45%] ml-auto bg-gradient-to-br from-violet-600 via-blue-500 to-cyan-400 flex items-center justify-center p-8 relative overflow-hidden">
      <!-- Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -inset-[10px] opacity-50">
          <div class="w-40 h-40 rounded-full bg-purple-500/30 blur-3xl absolute -top-20 -left-20 animate-blob animation-delay-0"></div>
          <div class="w-48 h-48 rounded-full bg-blue-500/30 blur-3xl absolute top-60 -right-20 animate-blob animation-delay-2000"></div>
          <div class="w-44 h-44 rounded-full bg-cyan-500/30 blur-3xl absolute -bottom-20 left-40 animate-blob animation-delay-4000"></div>
        </div>
      </div>

      <!-- Form Container -->
      <div class="w-full max-w-md transform transition-all relative">
        <!-- Glass Effect Container -->
        <div class="backdrop-blur-sm bg-white/10 rounded-2xl p-8 shadow-2xl border border-white/20">
          <div class="text-center mb-8">
            <h2 class="text-4xl font-extrabold mb-3 drop-shadow-lg tracking-tight bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent">
              {{ step === 'email' ? 'Lupa Password?' : 'Reset Password' }}
            </h2>
            <p class="text-sm text-blue-100 font-medium">
              {{ step === 'email' ?
                'Masukkan email Anda dan kami akan mengirimkan kode reset password.' :
                'Masukkan kode reset dan password baru Anda.'
              }}
            </p>
          </div>

          <!-- Error/Success Messages -->
          <div v-if="error" class="bg-red-100/90 backdrop-blur-sm border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-lg">
            {{ error }}
          </div>

          <div v-if="success" class="bg-green-100/90 backdrop-blur-sm border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-lg">
            {{ success }}
          </div>

          <!-- Email Form -->
          <form v-if="step === 'email'" @submit.prevent="handleForgotPassword" class="space-y-5">
            <div class="group">
              <label for="email" class="block text-blue-50 mb-2 text-sm font-medium tracking-wide">Email</label>
              <div class="relative">
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                  placeholder="Masukkan email anda"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200/70" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-white via-blue-50 to-white text-violet-600 py-3 rounded-lg font-semibold transition-all duration-300 hover:bg-gradient-to-l focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-violet-600 border-t-transparent mr-2"></div>
                Mengirim...
              </span>
              <span v-else>Kirim Kode Reset</span>
            </button>

            <div class="border-t border-white/20 pt-5 mt-6">
              <p class="text-center">
                <router-link to="/login" class="text-white font-medium hover:text-blue-200 transition-colors duration-200">
                  Kembali ke Login
                </router-link>
              </p>
            </div>
          </form>

          <!-- Reset Password Form -->
          <form v-if="step === 'reset'" @submit.prevent="handleResetPassword" class="space-y-5">
            <div class="group">
              <label for="token" class="block text-blue-50 mb-2 text-sm font-medium tracking-wide">Kode Reset</label>
              <div class="relative">
                <input
                  id="token"
                  v-model="form.token"
                  type="text"
                  required
                  maxlength="6"
                  class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                  placeholder="Masukkan kode 6 digit"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200/70" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="group">
              <label for="password" class="block text-blue-50 mb-2 text-sm font-medium tracking-wide">Password Baru</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                  placeholder="Masukkan password baru"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200/70" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="group">
              <label for="password_confirmation" class="block text-blue-50 mb-2 text-sm font-medium tracking-wide">Konfirmasi Password</label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  required
                  class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                  placeholder="Konfirmasi password baru"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200/70" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-white via-blue-50 to-white text-violet-600 py-3 rounded-lg font-semibold transition-all duration-300 hover:bg-gradient-to-l focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-violet-600 border-t-transparent mr-2"></div>
                Mereset...
              </span>
              <span v-else>Reset Password</span>
            </button>

            <div class="border-t border-white/20 pt-5 mt-6">
              <p class="text-center">
                <button
                  type="button"
                  @click="step = 'email'"
                  class="text-white font-medium hover:text-blue-200 transition-colors duration-200"
                >
                  Kembali ke Email
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
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '@/services/api'

const router = useRouter()

const form = reactive({
  email: '',
  token: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const error = ref('')
const success = ref('')
const step = ref('email') // 'email' or 'reset'

const handleForgotPassword = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const response = await authAPI.forgotPassword({
      email: form.email
    })

    if (response.data.success) {
      success.value = response.data.message
      step.value = 'reset'
    } else {
      error.value = response.data.message || 'Terjadi kesalahan'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
    console.error('Forgot password error:', err)
  } finally {
    loading.value = false
  }
}

const handleResetPassword = async () => {
  if (form.password !== form.password_confirmation) {
    error.value = 'Password dan konfirmasi password tidak sama'
    return
  }

  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const response = await authAPI.resetPassword({
      email: form.email,
      token: form.token,
      password: form.password,
      password_confirmation: form.password_confirmation
    })

    if (response.data.success) {
      success.value = response.data.message
      // Redirect to login after 2 seconds
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else {
      error.value = response.data.message || 'Terjadi kesalahan'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
    console.error('Reset password error:', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 10s ease-in-out infinite;
}

.animation-delay-0 {
  animation-delay: 0s;
}

.animation-delay-2000 {
  animation-delay: -2s;
}

.animation-delay-4000 {
  animation-delay: -4s;
}

/* Ensure proper styling for the split layout */
body {
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

/* Improve form field aesthetics */
.group:focus-within label {
  color: rgb(255, 255, 255);
  transform: translateY(-2px);
  transition: all 0.2s ease;
}

/* Input autofill styling */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-text-fill-color: white;
  -webkit-box-shadow: 0 0 0px 1000px rgba(255, 255, 255, 0.1) inset;
  transition: background-color 5000s ease-in-out 0s;
}

/* Smooth transitions for all interactive elements */
input, button, a {
  transition: all 0.2s ease-in-out;
}

/* Button hover effect */
button:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -10px rgba(0, 0, 0, 0.2);
}

/* Link hover effect */
a:hover {
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
