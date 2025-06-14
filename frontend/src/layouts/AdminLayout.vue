<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Admin Navigation -->
    <nav class="bg-gradient-to-r from-blue-900 via-purple-800 to-indigo-900 text-white shadow-lg sticky top-0 z-50 relative overflow-hidden">
      <div class="absolute inset-0 bg-grid-white/10"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex justify-between h-16">
          <!-- Logo and Brand -->
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="flex items-center space-x-3 group">
              <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-8 w-auto transition-transform duration-300 group-hover:scale-110">
              <span class="text-xl font-bold text-white text-shadow-glow">PhoneKu Admin</span>
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-6">
            <router-link to="/admin/dashboard" class="nav-link">Dashboard</router-link>
            <router-link to="/admin/products" class="nav-link">Products</router-link>

            <!-- Profile Dropdown -->
            <div class="relative ml-4">
              <button @click="showProfileMenu = !showProfileMenu"
                class="flex items-center space-x-2 text-gray-100 hover:text-white transition-colors">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                  <span class="text-sm font-medium text-white">{{ authStore.user?.name?.[0]?.toUpperCase() }}</span>
                </div>
                <span class="text-sm font-medium">{{ authStore.user?.name }}</span>
                <i class="fas fa-chevron-down text-xs"></i>
              </button>

              <div v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-48 bg-gradient-to-b from-gray-900 to-gray-800 rounded-md shadow-lg py-1 z-50 border border-purple-500/20 backdrop-blur-sm">
                <router-link to="/welcome" class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-blue-600/20 hover:text-white transition-colors">
                  <i class="fas fa-store mr-2"></i> View Store
                </router-link>
                <button @click="logout" class="w-full flex items-center px-4 py-2 text-sm text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-colors">
                  <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="min-h-screen bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <div class="flex items-center text-sm text-gray-400 mb-6">
          <router-link to="/admin/dashboard" class="hover:text-blue-400 transition-colors">
            <i class="fas fa-home text-shadow-glow"></i>
          </router-link>
          <i class="fas fa-chevron-right mx-2 text-xs text-gray-500"></i>
          <span class="font-medium text-white text-shadow-glow">{{ currentPageName }}</span>
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
      showProfileMenu: false,
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
.nav-link {
  @apply text-gray-100 hover:text-white transition-colors relative py-2;
}

.nav-link::after {
  content: '';
  @apply absolute bottom-0 left-0 w-full h-0.5 bg-blue-400 transform scale-x-0 transition-transform duration-200;
}

.nav-link:hover::after,
.nav-link.router-link-active::after {
  @apply scale-x-100;
}

.router-link-active {
  @apply text-white font-medium;
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

<style>
.text-shadow-glow {
  text-shadow: 0 0 10px rgba(148, 163, 184, 0.5);
}

.shadow-neon {
  box-shadow: 0 0 15px rgba(96, 165, 250, 0.3);
}

.bg-grid-white\/10 {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}
</style>
