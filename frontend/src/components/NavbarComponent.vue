<template>
  <div class="relative bg-gradient-to-r from-blue-600 to-blue-500">
    <div class="container mx-auto px-4">
      <!-- Top Navigation -->
      <div class="py-2 flex justify-end text-white text-sm">
        <div v-if="!authStore.isAuthenticated" class="space-x-4 relative z-50">
          <a @click.prevent="$router.push('/login')" class="cursor-pointer hover:text-blue-100 transition duration-200">Masuk</a>
          <span class="text-blue-200">|</span>
          <a @click.prevent="$router.push('/register')" class="cursor-pointer hover:text-blue-100 transition duration-200">Daftar</a>
        </div>

        <button v-else @click.prevent="logout" class="cursor-pointer hover:text-blue-100 transition duration-200 flex items-center relative z-50">
          <i class="fas fa-sign-out-alt mr-1"></i>
          <span>Keluar</span>
        </button>
      </div>

      <!-- Main Navigation -->
      <nav class="relative bg-white/90 backdrop-blur-md shadow-lg rounded-xl px-8 py-4 mb-4 z-40">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="flex-shrink-0 relative z-50">
            <a @click.prevent="$router.push('/')" class="flex items-center cursor-pointer">
              <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-10 hover:scale-105 transition duration-200">
            </a>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-8 relative z-50">
            <a v-for="(item, idx) in navItems" 
               :key="item.to" 
               @click.prevent="handleNavClick(idx, item.to)"
               :class="[getNavLinkClass(item.name), { 'active-animate': activeIndex === idx }, 'cursor-pointer']">
              {{ item.label }}
            </a>
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

// Fungsi untuk menangani klik navigasi
const handleNavClick = (idx, path) => {
  activeIndex.value = idx
  router.push(path)
}

// Fungsi untuk mendapatkan kelas CSS untuk link navigasi
const getNavLinkClass = (name) => {
  const isActive = route.name === name
  return [
    'text-gray-600',
    'hover:text-blue-600',
    'transition-colors',
    'duration-200',
    'relative',
    'z-50',
    { 'text-blue-600 font-semibold': isActive }
  ]
}

const toggleMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const logout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
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
  color: #2563eb; /* blue-600 */
  font-weight: 600;
}

.active-animate {
  position: relative;
}

.active-animate::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #2563eb;
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.active-animate:hover::after {
  transform: scaleX(1);
}

/* Pastikan z-index mobile menu lebih tinggi */
nav {
  position: relative;
  z-index: 50;
}

.mobile-menu {
  z-index: 40;
}

/* Animasi untuk mobile menu */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
