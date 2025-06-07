<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <NavbarComponent />

    <!-- Main Content -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Profile Grid -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="md:col-span-1">
          <!-- User Profile Card -->
          <div class="bg-white rounded-2xl p-6 shadow-md">
            <!-- Profile Picture and Name -->
            <div class="flex flex-col items-center">
              <div class="w-24 h-24 rounded-full overflow-hidden mb-4 border-4 border-white shadow-lg">
                <img :src="authStore.user?.profile_picture || '/img/profile.png'" alt="Profile"
                  class="w-full h-full object-cover">
              </div>
              <h3 class="text-xl font-semibold text-gray-900">{{ authStore.user?.name }}</h3>
              <p class="text-sm text-gray-500">{{ authStore.user?.email }}</p>
            </div>
          </div>

          <!-- Navigation Menu -->
          <div class="mt-6 bg-white rounded-2xl shadow-md overflow-hidden">
            <!-- About Me -->
            <router-link to="/profile/tentang-saya"
              class="flex items-center px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors"
              :class="{ 'bg-gray-50': $route.path === '/profile/tentang-saya' }">
              <i class="fas fa-user text-gray-400 w-5"></i>
              <span class="ml-3 text-gray-700">Tentang Saya</span>
            </router-link>

            <!-- Purchase History -->
            <router-link to="/profile/riwayat-pembelian"
              class="flex items-center px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors"
              :class="{ 'bg-gray-50': $route.path === '/profile/riwayat-pembelian' }">
              <i class="fas fa-history text-gray-400 w-5"></i>
              <span class="ml-3 text-gray-700">Riwayat Pembelian</span>
            </router-link>

            <!-- Security & Privacy -->
            <router-link to="/profile/keamanan"
              class="flex items-center px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors"
              :class="{ 'bg-gray-50': $route.path === '/profile/keamanan' }">
              <i class="fas fa-shield-alt text-gray-400 w-5"></i>
              <span class="ml-3 text-gray-700">Keamanan & Privasi</span>
            </router-link>

            <!-- Logout -->
            <button @click="handleLogout"
              class="w-full flex items-center px-6 py-4 hover:bg-gray-50 transition-colors text-left">
              <i class="fas fa-sign-out-alt text-gray-400 w-5"></i>
              <span class="ml-3 text-gray-700">Keluar Akun</span>
            </button>
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="md:col-span-3">
          <router-view />
        </div>
      </div>
    </div>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>
