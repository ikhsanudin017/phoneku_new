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
import { ref } from 'vue'
import { authAPI } from '@/services/api'

const email = ref('')
const loading = ref(false)
const message = ref('')
const error = ref('')

const handleForgotPassword = async () => {
  loading.value = true
  message.value = ''
  error.value = ''
  try {
    const response = await authAPI.forgotPassword(email.value)
    if (response.data.success) {
      message.value = response.data.message || 'Permintaan reset password berhasil dikirim. Silakan cek email Anda.'
    } else {
      error.value = response.data.message || 'Gagal mengirim permintaan reset password.'
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Terjadi kesalahan. Coba lagi.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Minimal styling */
</style>
