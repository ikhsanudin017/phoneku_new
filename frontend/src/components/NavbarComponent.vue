<template>
  <div class="bg-gradient-to-r from-blue-600 to-blue-500">
    <div class="container mx-auto px-4">
      <!-- Top Navigation -->
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
      <nav class="bg-white/90 backdrop-blur-md shadow-lg rounded-xl px-8 py-4 mb-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <router-link to="/" class="flex items-center">
              <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-10 hover:scale-105 transition duration-200">
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-8">
            <router-link v-for="(item, idx) in navItems" :key="item.to" :to="item.to"
              :class="[getNavLinkClass(item.name), { 'active-animate': activeIndex === idx }]"
              @click="handleNavClick(idx)">
              {{ item.label }}
            </router-link>
          </div>

          <!-- Icons -->
          <div class="hidden md:flex items-center space-x-6">
            <router-link v-if="!authStore.isAuthenticated" to="/login"
              class="text-gray-600 hover:text-blue-600 transition-colors">
              <i class="fas fa-sign-in-alt text-xl"></i>
            </router-link>

            <router-link v-else to="/profile"
              class="text-gray-600 hover:text-blue-600 transition-colors">
              <i class="fas fa-user text-xl"></i>
            </router-link>

            <router-link to="/cart" class="text-gray-600 hover:text-blue-600 transition-colors relative">
              <i class="fas fa-shopping-cart text-xl"></i>
              <span v-if="cartStore.cartCount > 0"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                {{ cartStore.cartCount }}
              </span>
            </router-link>
          </div>

          <!-- Mobile Menu Button -->
          <button @click="toggleMenu" class="md:hidden text-gray-600">
            <i :class="['fas', mobileMenuOpen ? 'fa-times' : 'fa-bars', 'text-xl']"></i>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden mt-4">
          <div class="px-2 pt-2 pb-3 space-y-1">
            <router-link v-for="item in navItems" :key="item.to" :to="item.to"
              class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
              {{ item.label }}
            </router-link>
            <router-link v-if="!authStore.isAuthenticated" to="/login"
              class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
              Masuk
            </router-link>
          </div>
        </div>
      </nav>
    </div>
  </div>
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
const navItems = [
  { to: '/', label: 'Beranda', name: 'welcome' },
  { to: '/about', label: 'Tentang', name: 'about' },
  { to: '/tim', label: 'Tim', name: 'tim' },
  { to: '/products', label: 'Belanja', name: 'products' },
  { to: '/kontak', label: 'Kontak', name: 'kontak' },
]
const activeIndex = ref(null)

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

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

function handleNavClick(idx) {
  activeIndex.value = idx
  setTimeout(() => {
    activeIndex.value = null
  }, 400)
}

// Close menu when route changes
router.afterEach(() => {
  if (mobileMenuOpen.value) {
    toggleMenu()
  }
})
</script>

<style scoped>
.active-animate {
  animation: linkClick 0.4s ease-out;
}

@keyframes linkClick {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(0.95);
  }
  100% {
    transform: scale(1);
  }
}
</style>
