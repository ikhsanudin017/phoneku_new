<template>
  <header class="bg-gradient-to-r from-blue-900 to-blue-950 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center py-4">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/admin/dashboard" class="flex items-center">
            <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-8 w-auto mr-2" />
            <span class="text-white text-lg font-bold">Admin Panel</span>
          </router-link>
        </div>

        <!-- Navigation -->
        <nav class="hidden md:flex space-x-8" style="pointer-events: auto; z-index: 50;">
          <router-link 
            v-for="item in navigationItems" 
            :key="item.name"
            :to="item.href"
            :class="[
              isCurrentRoute(item.href)
                ? 'text-white bg-blue-800'
                : 'text-blue-200 hover:text-white hover:bg-blue-800/50',
              'px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150',
              'focus:outline-none',
              'focus:ring-2',
              'focus:ring-blue-500',
              'cursor-pointer'
            ]"
            tabindex="0"
            style="pointer-events: auto;"
          >
            <i :class="[item.icon, 'mr-2']"></i>
            {{ item.name }}
          </router-link>
        </nav>

        <!-- Mobile Menu Button -->
        <div class="flex md:hidden">
          <button 
            @click="toggleMobileMenu"
            class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
          >
            <span class="sr-only">Open main menu</span>
            <i :class="[isOpen ? 'fa-times' : 'fa-bars', 'fas text-xl']"></i>
          </button>
        </div>

        <!-- User Menu -->
        <div class="hidden md:flex items-center">
          <div class="relative" v-click-outside="closeUserMenu">
            <button 
              @click="toggleUserMenu"
              class="flex items-center text-sm text-blue-200 hover:text-white focus:outline-none"
            >
              <span class="sr-only">Open user menu</span>
              <i class="fas fa-user-circle text-2xl mr-2"></i>
              <span class="mr-2">{{ user?.name || 'Admin' }}</span>
              <i :class="[isUserMenuOpen ? 'fa-chevron-up' : 'fa-chevron-down', 'fas text-xs']"></i>
            </button>

            <!-- User Dropdown Menu -->
            <div 
              v-show="isUserMenuOpen"
              class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
            >
              <div class="py-1">
                <router-link
                  to="/admin/profile"
                  class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50"
                >
                  <i class="fas fa-user mr-3 text-blue-500"></i>
                  Profile
                </router-link>
                <router-link
                  to="/admin/settings"
                  class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50"
                >
                  <i class="fas fa-cog mr-3 text-blue-500"></i>
                  Settings
                </router-link>
              </div>
              <div class="py-1">
                <button
                  @click="logout"
                  class="w-full flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                >
                  <i class="fas fa-sign-out-alt mr-3"></i>
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div 
      v-show="isOpen"
      class="md:hidden"
    >
      <div class="px-2 pt-2 pb-3 space-y-1">
        <router-link
          v-for="item in navigationItems"
          :key="item.name"
          :to="item.href"
          :class="[
            isCurrentRoute(item.href)
              ? 'text-white bg-blue-800'
              : 'text-blue-200 hover:text-white hover:bg-blue-800/50',
            'block px-3 py-2 rounded-md text-base font-medium'
          ]"
          @click="isOpen = false"
        >
          <i :class="[item.icon, 'mr-2']"></i>
          {{ item.name }}
        </router-link>
      </div>
      <div class="pt-4 pb-3 border-t border-blue-800">
        <div class="px-2 space-y-1">
          <router-link
            to="/admin/profile"
            class="block px-3 py-2 rounded-md text-base font-medium text-blue-200 hover:text-white hover:bg-blue-800"
            @click="isOpen = false"
          >
            <i class="fas fa-user mr-2"></i>
            Profile
          </router-link>
          <router-link
            to="/admin/settings"
            class="block px-3 py-2 rounded-md text-base font-medium text-blue-200 hover:text-white hover:bg-blue-800"
            @click="isOpen = false"
          >
            <i class="fas fa-cog mr-2"></i>
            Settings
          </router-link>
          <button
            @click="logout"
            class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-red-400 hover:text-red-300 hover:bg-blue-800"
          >
            <i class="fas fa-sign-out-alt mr-2"></i>
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

const isOpen = ref(false)
const isUserMenuOpen = ref(false)
const user = ref(null)

const navigationItems = [
  { name: 'Dashboard', href: '/admin/dashboard', icon: 'fas fa-tachometer-alt' },
  { name: 'Products', href: '/admin/products', icon: 'fas fa-box' },
  { name: 'Orders', href: '/admin/orders', icon: 'fas fa-shopping-cart' },
  { name: 'Users', href: '/admin/users', icon: 'fas fa-users' },
  { name: 'Chat', href: '/admin/chat', icon: 'fas fa-comments' }
]

const toggleMobileMenu = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) isUserMenuOpen.value = false
}

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const closeUserMenu = () => {
  isUserMenuOpen.value = false
}

const isCurrentRoute = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/admin/user')
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const logout = async () => {
  try {
    await axios.post('/api/admin/logout')
    router.push({ name: 'AdminLogin' })
  } catch (error) {
    console.error('Failed to logout:', error)
  }
}

onMounted(() => {
  fetchUser()
})

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>

<style scoped>
/* Animation for dropdowns */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Gradient background */
.nav-gradient {
  background: linear-gradient(to right, #1e3a8a, #1e40af, #1e3a8a);
}

/* Grid pattern overlay */
.bg-grid-pattern {
  background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Glow effects */
.text-glow {
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.icon-glow {
  filter: drop-shadow(0 0 2px rgba(255, 255, 255, 0.5));
}
</style>
