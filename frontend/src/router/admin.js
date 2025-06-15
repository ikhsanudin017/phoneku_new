// src/router/admin.js
import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'

// Import views
const AdminLogin = () => import('@/views/admin/AdminLogin.vue')
const AdminDashboard = () => import('@/views/admin/AdminDashboard.vue')
const AdminProducts = () => import('@/views/admin/AdminProducts.vue')
const AdminOrders = () => import('@/views/admin/AdminOrders.vue')
const AdminUsers = () => import('@/views/admin/AdminUsers.vue')
const AdminChat = () => import('@/views/admin/AdminChat.vue')
const AdminProfile = () => import('@/views/admin/AdminProfile.vue')
const AdminSettings = () => import('@/views/admin/AdminSettings.vue')

// Define routes
export const adminRoutes = [
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { requiresGuest: true, title: 'Login' }
  },
  {
    path: '/admin',
    redirect: '/admin/dashboard',
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Dashboard' }
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: AdminProducts,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Products' }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: AdminOrders,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Orders' }
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: AdminUsers,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Users' }
  },
  {
    path: '/admin/chat',
    name: 'admin-chat',
    component: AdminChat,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Chat' }
  },
  {
    path: '/admin/profile',
    name: 'admin-profile',
    component: AdminProfile,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Profile' }
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: AdminSettings,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Settings' }
  }
]

// Navigation guard
export async function setupAdminGuard(router) {
  router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const requiresGuest = to.matched.some(record => record.meta.requiresGuest)
    const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)

    // Cek auth
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
        return next({ name: 'home' })
      }
    }

    // Handle auth required
    if (requiresAuth && !authStore.isAuthenticated) {
      return next({ 
        name: 'AdminLogin',
        query: { redirect: to.fullPath }
      })
    }

    // Handle guest routes
    if (requiresGuest && authStore.isAuthenticated) {
      return next({ name: 'AdminDashboard' })
    }

    next()
  })
}
