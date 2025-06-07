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

// Admin views
import AdminLogin from '@/views/admin/AdminLogin.vue'
import AdminRegister from '@/views/admin/AdminRegister.vue'
import AdminForgotPassword from '@/views/admin/AdminForgotPassword.vue'
import AdminDashboard from '@/views/admin/AdminDashboard.vue'
import AdminProducts from '@/views/admin/AdminProducts.vue'
import AdminUsers from '@/views/admin/AdminUsers.vue'
import AdminChat from '@/views/admin/AdminChat.vue'
import AdminOrders from '@/views/admin/AdminOrders.vue'
import AdminProductCreate from '@/views/admin/AdminProductCreate.vue'

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
      meta: { requiresAuth: true }
    },
    // Admin routes
    {
      path: '/admin/login',
      name: 'admin-login',
      component: AdminLogin,
      meta: { requiresGuest: true }
    },
    {
      path: '/admin/register',
      name: 'admin-register',
      component: AdminRegister,
      meta: { requiresGuest: true }
    },
    {
      path: '/admin/forgot-password',
      name: 'admin-forgot-password',
      component: AdminForgotPassword,
      meta: { requiresGuest: true }
    },
    {
      path: '/admin',
      name: 'admin',
      redirect: '/admin/dashboard',
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: AdminDashboard,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/products',
      name: 'admin-products',
      component: AdminProducts,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/products/create',
      name: 'admin-product-create',
      component: AdminProductCreate,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/orders',
      name: 'admin-orders',
      component: AdminOrders,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/users',
      name: 'admin-users',
      component: AdminUsers,
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/chat',
      name: 'admin-chat',
      component: AdminChat,
      meta: { requiresAuth: true, requiresAdmin: true }
    }
  ]
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Initialize auth if not already done
  if (!authStore.isAuthenticated && authStore.token) {
    authStore.initializeAuth()
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
    return;
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      next('/admin/dashboard');
    } else {
      next('/welcome');
    }
    return;
  }

  // Check if route requires admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/welcome');
    return;
  }

  next();
})

export default router
