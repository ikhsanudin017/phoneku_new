<template>
  <div class="min-h-screen flex bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900">
    <!-- Left Side with Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md space-y-8 relative">
        <div class="text-center">
          <router-link to="/welcome" class="inline-block mb-4">
            <h2 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">PhoneKu</h2>
          </router-link>
          <h2 class="text-3xl font-bold tracking-tight text-white">
            Admin Registration
          </h2>
          <p class="mt-2 text-sm text-gray-400">
            Create your administrator account
          </p>
        </div>

        <div class="backdrop-blur-lg bg-white/10 rounded-2xl p-8 shadow-2xl border border-white/10">
          <form class="space-y-6" @submit.prevent="register">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300">Full Name</label>
              <div class="mt-1 relative">
                <input
                  id="name"
                  v-model="form.name"
                  name="name"
                  type="text"
                  required
                  class="block w-full px-4 py-3 bg-black/30 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  placeholder="Enter your full name"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
              <div class="mt-1 relative">
                <input
                  id="email"
                  v-model="form.email"
                  name="email"
                  type="email"
                  required
                  class="block w-full px-4 py-3 bg-black/30 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  placeholder="Enter your email address"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
              <div class="mt-1 relative">
                <input
                  id="password"
                  v-model="form.password"
                  name="password"
                  type="password"
                  required
                  class="block w-full px-4 py-3 bg-black/30 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  placeholder="Create a strong password"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
              <div class="mt-1 relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  name="password_confirmation"
                  type="password"
                  required
                  class="block w-full px-4 py-3 bg-black/30 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  placeholder="Confirm your password"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>

            <div>
              <label for="admin_code" class="block text-sm font-medium text-gray-300">Admin Registration Code</label>
              <div class="mt-1 relative">
                <input
                  id="admin_code"
                  v-model="form.admin_code"
                  name="admin_code"
                  type="text"
                  required
                  class="block w-full px-4 py-3 bg-black/30 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                  placeholder="Enter admin registration code"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
              <p class="mt-1 text-xs text-gray-500">Contact your system administrator for the registration code</p>
            </div>

            <div v-if="errorMessage" class="bg-red-900/50 backdrop-blur-sm border border-red-500/50 text-red-200 px-4 py-3 rounded-lg text-sm">
              {{ errorMessage }}
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-black border-t-transparent mr-2"></div>
                Creating Account...
              </span>
              <span v-else>Create Admin Account</span>
            </button>

            <div class="text-center space-y-2">
              <p class="text-sm text-gray-300">
                Already have an account?
                <router-link to="/admin/login" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                  Sign in here
                </router-link>
              </p>
            </div>
          </form>
        </div>
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
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '@/services/api'

const router = useRouter()

const loading = ref(false)
const errorMessage = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  admin_code: ''
})

const register = async () => {
  if (form.password !== form.password_confirmation) {
    errorMessage.value = 'Passwords do not match'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const response = await authAPI.adminRegister({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
      admin_code: form.admin_code
    })

    if (response.data.success) {
      alert('Admin account created successfully! Please contact your system administrator to activate your account.')
      router.push('/admin/login')
    } else {
      errorMessage.value = response.data.message || 'Registration failed'
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage.value = errors.join(', ')
    } else {
      errorMessage.value = error.response?.data?.message || 'Registration failed. Please try again.'
    }
    console.error('Admin registration error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-grid-white\/10 {
  background-image: linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
}

/* Improve form field aesthetics */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-text-fill-color: white;
  -webkit-box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0.3) inset;
  transition: background-color 5000s ease-in-out 0s;
}
</style>
