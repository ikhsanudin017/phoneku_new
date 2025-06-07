<template>
  <div class="bg-gradient-to-r from-blue-600 to-blue-500 shadow-lg">
    <!-- Top Navigation -->
    <div class="container mx-auto px-4">
      <div class="py-2 flex justify-end text-white text-sm">
        <div v-if="!authStore.isAuthenticated" class="space-x-4">
          <router-link to="/login" class="hover:text-blue-100 transition duration-200">Masuk</router-link>
          <span class="text-blue-200">|</span>
          <router-link to="/register" class="hover:text-blue-100 transition duration-200">Daftar</router-link>
        </div>

        <button v-else @click="logout" class="hover:text-blue-100 transition duration-200 flex items-center">
          <i class="fas fa-sign-out-alt mr-1"></i>
          <span>Keluar</span>
        </button>
      </div>

      <!-- Main Navigation -->
      <nav class="bg-white/95 backdrop-blur-sm py-4 px-6 flex items-center justify-between">
        <!-- Logo (Desktop) -->
        <div class="hidden md:block">
          <router-link to="/" class="flex items-center space-x-2">
            <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-10 hover:scale-105 transition duration-200">
          </router-link>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link to="/" :class="getNavLinkClass('welcome')">Beranda</router-link>
          <router-link to="/about" :class="getNavLinkClass('about')">Tentang</router-link>
          <router-link to="/tim" :class="getNavLinkClass('tim')">Tim</router-link>
          <router-link to="/products" :class="getNavLinkClass('products')">Belanja</router-link>
          <router-link to="/kontak" :class="getNavLinkClass('kontak')">Kontak</router-link>
        </div>

        <!-- Icons -->
        <div class="hidden md:flex items-center space-x-6">
          <router-link v-if="authStore.isAuthenticated" to="/cart" :class="getIconClass('cart')" class="relative">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span v-if="cartStore.cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartStore.cartCount }}
            </span>
          </router-link>

          <router-link v-if="authStore.isAuthenticated" to="/chat" :class="getIconClass('chat')" class="relative">
            <i class="fas fa-comments text-xl"></i>
            <span v-if="unreadCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ unreadCount }}
            </span>
          </router-link>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" class="md:hidden text-gray-600">
          <i :class="['fas', mobileMenuOpen ? 'fa-times' : 'fa-bars', 'text-xl']"></i>
        </button>
      </nav>
    </div>
  </div>

  <!-- Content spacing -->
  <div class="h-4"></div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()
const mobileMenuOpen = ref(false)
const unreadCount = ref(0) // This should be connected to your chat system

const toggleMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  document.body.style.overflow = mobileMenuOpen.value ? 'hidden' : ''
}

const getNavLinkClass = (routeName) => {
  const isActive = route.name === routeName
  return [
    'text-gray-700',
    'hover:text-blue-600',
    'font-medium',
    'transition-colors',
    { 'text-blue-600': isActive }
  ]
}

const getIconClass = (type) => {
  const activeRoutes = {
    cart: ['cart', 'checkout'],
    chat: ['chat'],
    profile: ['profile', 'settings']
  }
  const isActive = activeRoutes[type]?.includes(route.name)
  return [
    'text-gray-600',
    'hover:text-blue-600',
    'transition-colors',
    { 'text-blue-600': isActive }
  ]
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Close menu when route changes
router.afterEach(() => {
  if (mobileMenuOpen.value) {
    toggleMenu()
  }
})
</script>

<style scoped>
.router-link-active {
  @apply text-blue-600 font-medium;
}

/* Ensure body scroll is re-enabled when component is destroyed */
@media (max-width: 768px) {
  :global(body) {
    overflow: auto !important;
  }
}
</style>
