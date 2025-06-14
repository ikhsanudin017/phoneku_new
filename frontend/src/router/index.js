import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Import views
import HomeView from '@/views/HomeView.vue'
import Welcome from '@/views/Welcome.vue'
import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
import ForgotPassword from '@/views/auth/ForgotPassword.vue'
import AllProduct from '@/views/AllProduct.vue'
import ProductDetail from '@/views/ProductDetail.vue'
import Cart from '@/views/Cart.vue'
import Checkout from '@/views/Checkout.vue'
import Profile from '@/views/profile/Profile.vue'
import AboutUs from '@/views/AboutUs.vue'
import Kontak from '@/views/Kontak.vue'
import Tim from '@/views/Tim.vue'
import Chat from '@/views/Chat.vue'

// Silent tracking component
const EmptyComponent = { template: '<div></div>' }

// Import admin routes
import { adminRoutes } from '@/router/admin.js'

// Profile views
import TentangSaya from '@/views/profile/TentangSaya.vue'
import UbahEmail from '@/views/profile/UbahEmail.vue'
import UbahNoTlp from '@/views/profile/UbahNoTlp.vue'
import RiwayatPembelian from '@/views/profile/RiwayatPembelian.vue'
import KeamananPrivasi from '@/views/profile/KeamananPrivasi.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      redirect: '/welcome'
    },
    {
      path: '/welcome',
      name: 'welcome',
      component: Welcome
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { requiresGuest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: { requiresGuest: true }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPassword,
      meta: { requiresGuest: true }
    },
    {
      path: '/products',
      name: 'all-products',
      component: AllProduct
    },
    {
      path: '/product/:id',
      name: 'product-detail',
      component: ProductDetail,
      props: true
    },
    {
      path: '/cart',
      name: 'cart',
      component: Cart,
      meta: { requiresAuth: true }
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: Checkout,
      meta: { requiresAuth: true }
    },
    {
      path: '/profile',
      component: Profile,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'profile',
          redirect: '/profile/tentang-saya'
        },
        {
          path: 'tentang-saya',
          name: 'tentang-saya',
          component: TentangSaya
        },
        {
          path: 'ubah-email',
          name: 'ubah-email',
          component: UbahEmail
        },
        {
          path: 'ubah-no-tlp',
          name: 'ubah-no-tlp',
          component: UbahNoTlp
        },
        {
          path: 'riwayat-pembelian',
          name: 'riwayat-pembelian',
          component: RiwayatPembelian
        },
        {
          path: 'keamanan-privasi',
          name: 'keamanan-privasi',
          component: KeamananPrivasi
        }
      ]
    },
    {
      path: '/about',
      name: 'about',
      component: AboutUs
    },
    {
      path: '/kontak',
      name: 'kontak',
      component: Kontak
    },
    {
      path: '/tim',
      name: 'tim',
      component: Tim
    },
    {
      path: '/chat',
      name: 'chat',
      component: Chat,
      meta: { requiresAuth: true },
      alias: '/customer-support'
    },
    // Admin routes
    ...adminRoutes,
    // Silently handle tracking URLs
    {
      path: '/hybridaction/:action',
      name: 'tracking',
      component: { template: '<div></div>' }
    },
    // Catch-all route - redirect to home
    {
      path: '/:pathMatch(.*)*',
      redirect: '/'
    }
  ]
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)

  // Wait for auth initialization if needed
  if (!authStore.initialized) {
    await authStore.initializeAuth()
  }

  // Handle admin routes
  if (requiresAdmin) {
    if (!authStore.isAuthenticated) {
      return next({ 
        name: 'AdminLogin',
        query: { redirect: to.fullPath }
      })
    }
    if (!authStore.isAdmin) {
      console.warn('Non-admin user attempted to access admin route:', to.fullPath)
      return next({ name: 'welcome' })
    }
  }

  // Handle auth required routes
  if (requiresAuth && !authStore.isAuthenticated) {
    return next({ 
      name: 'login',
      query: { redirect: to.fullPath }
    })
  }

  // Handle guest routes
  if (requiresGuest && authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      return next({ name: 'AdminDashboard' })
    }
    return next({ name: 'welcome' })
  }

  // If all checks pass, proceed
  next()
})

// Handle navigation errors
router.onError((error) => {
  console.error('Router error:', error)
  if (error.message.includes('Failed to fetch dynamically imported module')) {
    window.location.reload()
  }
})

export default router
