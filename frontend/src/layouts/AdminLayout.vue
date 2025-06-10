<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and Brand -->
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="flex items-center space-x-3">
              <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-8 w-auto">
              <span class="text-xl font-bold text-white">Admin Panel</span>
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-6">
            <router-link
              v-for="item in navigationItems"
              :key="item.path"
              :to="item.path"
              class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors rounded-md"
              :class="isCurrentPath(item.path) ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-600'"
            >
              <i :class="[item.icon, 'mr-2']"></i>
              {{ item.name }}
            </router-link>

            <!-- Profile Dropdown -->
            <div class="relative ml-4" @click="toggleDropdown" @blur="closeDropdown" tabindex="0">
              <button class="flex items-center space-x-3 text-white hover:text-blue-100 focus:outline-none">
                <div class="flex items-center">
                  <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                      {{ user?.name?.[0]?.toUpperCase() || 'A' }}
                    </span>
                  </div>
                  <span class="ml-2 text-sm">{{ user?.name || 'Admin' }}</span>
                  <i class="fas fa-chevron-down text-xs ml-2"></i>
                </div>
              </button>

              <!-- Dropdown Menu -->
              <div v-if="showDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <router-link to="/welcome" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-store mr-2"></i> View Store
                </router-link>
                <router-link to="/admin/profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user-cog mr-2"></i> Settings
                </router-link>
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="handleLogout" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                  <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <div class="flex items-center md:hidden">
            <button @click="toggleMobileMenu" class="text-white hover:text-blue-100">
              <i :class="isMobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'" class="text-xl"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-show="isMobileMenuOpen" class="md:hidden bg-blue-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link
            v-for="item in navigationItems"
            :key="item.path"
            :to="item.path"
            class="block px-3 py-2 text-base font-medium rounded-md transition-colors"
            :class="isCurrentPath(item.path) ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-600'"
            @click="closeMobileMenu"
          >
            <i :class="[item.icon, 'mr-2']"></i>
            {{ item.name }}
          </router-link>
        </div>
        <div class="border-t border-blue-800 pt-4 pb-3">
          <div class="flex items-center px-5">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
              <span class="text-sm font-medium text-white">
                {{ user?.name?.[0]?.toUpperCase() || 'A' }}
              </span>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-white">{{ user?.name }}</div>
              <div class="text-sm font-medium text-blue-100">{{ user?.email }}</div>
            </div>
          </div>
          <div class="mt-3 px-2 space-y-1">
            <router-link to="/welcome" class="block px-3 py-2 text-base font-medium text-blue-100 hover:bg-blue-600 rounded-md">
              <i class="fas fa-store mr-2"></i> View Store
            </router-link>
            <router-link to="/admin/profile" class="block px-3 py-2 text-base font-medium text-blue-100 hover:bg-blue-600 rounded-md">
              <i class="fas fa-user-cog mr-2"></i> Settings
            </router-link>
            <button @click="handleLogout" class="w-full text-left px-3 py-2 text-base font-medium text-red-300 hover:bg-blue-600 rounded-md">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-500 mb-4">
          <router-link to="/admin/dashboard" class="hover:text-blue-600">
            <i class="fas fa-home"></i>
          </router-link>
          <i class="fas fa-chevron-right mx-2 text-xs text-gray-400"></i>
          <span class="font-medium text-gray-900">{{ currentPageName }}</span>
        </div>

        <!-- Route Content -->
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'AdminLayout',
  data() {
    return {
      showDropdown: false,
      isMobileMenuOpen: false,
      navigationItems: [
        {
          name: 'Dashboard',
          path: '/admin/dashboard',
          icon: 'fas fa-chart-line'
        },
        {
          name: 'Products',
          path: '/admin/products',
          icon: 'fas fa-mobile-alt'
        },
        {
          name: 'Orders',
          path: '/admin/orders',
          icon: 'fas fa-shopping-cart'
        },
        {
          name: 'Users',
          path: '/admin/users',
          icon: 'fas fa-users'
        },
        {
          name: 'Chat',
          path: '/admin/chat',
          icon: 'fas fa-comments'
        }
      ]
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    },
    user() {
      return this.authStore.user
    },
    currentPageName() {
      const currentItem = this.navigationItems.find(item => item.path === this.$route.path)
      return currentItem ? currentItem.name : this.$route.name
    }
  },
  methods: {
    isCurrentPath(path) {
      return this.$route.path === path
    },
    toggleDropdown() {
      this.showDropdown = !this.showDropdown
    },
    closeDropdown() {
      setTimeout(() => {
        this.showDropdown = false
      }, 200)
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen
    },
    closeMobileMenu() {
      this.isMobileMenuOpen = false
    },
    async handleLogout() {
      try {
        await this.authStore.logout()
        this.$router.push('/admin/login')
      } catch (error) {
        console.error('Logout error:', error)
      }
    }
  }
}
</script>

<style scoped>
.router-link-active {
  color: white;
  background-color: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
  .mobile-menu-enter-active,
  .mobile-menu-leave-active {
    transition: opacity 0.2s;
  }

  .mobile-menu-enter-from,
  .mobile-menu-leave-to {
    opacity: 0;
  }
}
</style>
