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

    <!-- Right Side with Login Form -->
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
            <h2 class="text-4xl font-extrabold mb-3 drop-shadow-lg tracking-tight bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent">Masuk</h2>
            <p class="text-sm text-blue-100 font-medium">Silahkan masuk jika sudah memiliki akun!</p>
          </div>

          <!-- Error Messages -->
          <div v-if="error" class="bg-red-100/90 backdrop-blur-sm border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-lg">
            <ul class="list-disc list-inside">
              <li>{{ error }}</li>
            </ul>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <!-- Redirect field -->
            <input v-if="$route.query.redirect" type="hidden" name="redirect" :value="$route.query.redirect">

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

            <div class="group">
              <label for="password" class="block text-blue-50 mb-2 text-sm font-medium tracking-wide">Password</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all duration-300"
                  placeholder="Masukkan password anda"
                >
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200/70" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="flex justify-between items-center">
              <div class="flex items-center">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="h-4 w-4 rounded border-white/20 bg-white/10 text-violet-400 focus:ring-2 focus:ring-violet-400/50 transition-colors duration-200"
                >
                <label for="remember" class="ml-2 text-blue-50 text-sm">Ingat saya</label>
              </div>
              <div>
                <router-link to="/forgot-password" class="text-blue-50 hover:text-white text-sm hover:underline transition-colors duration-200">
                  Lupa Password?
                </router-link>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-white via-blue-50 to-white text-violet-600 py-3 rounded-lg font-semibold transition-all duration-300 hover:bg-gradient-to-l focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-violet-600 border-t-transparent mr-2"></div>
                Masuk...
              </span>
              <span v-else>Masuk</span>
            </button>

            <div class="border-t border-white/20 pt-5 mt-6">
              <p class="text-center text-blue-50 text-sm">
                Belum memiliki akun?
                <router-link to="/register" class="text-white font-medium hover:text-blue-200 transition-colors duration-200">
                  Daftar sekarang!
                </router-link>
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
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const handleLogin = async () => {
  loading.value = true
  error.value = ''

  try {
    const result = await authStore.login(form)

    if (result.success) {
      // Redirect to /welcome for regular users
      const redirectUrl = route.query.redirect || '/welcome'
      router.push(redirectUrl)
    } else if (result.isAdmin) {
      // If this is an admin account, show message and provide link to admin login
      error.value = 'This is an admin account. Please use the admin login page.'
      router.push('/admin/login')
    } else {
      error.value = result.message || 'Login failed. Please check your credentials.'
    }
  } catch (err) {
    error.value = 'An error occurred during login. Please try again.'
    console.error('Login error:', err)
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
