// src/router/admin.js
import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import AdminLogin from '@/views/admin/AdminLogin.vue'
import AdminDashboard from '@/views/admin/AdminDashboard.vue'
import AdminProducts from '@/views/admin/AdminProducts.vue'
import AdminProductCreate from '@/views/admin/AdminProductCreate.vue'

// Import missing components
import AdminRegister from '@/views/admin/AdminRegister.vue'
import AdminForgotPassword from '@/views/admin/AdminForgotPassword.vue'

export const adminRoutes = [
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { requiresGuest: true, title: 'Login' }
  },
  {
    path: '/admin/register',
    name: 'AdminRegister',
    component: AdminRegister,
    meta: { requiresGuest: true, title: 'Register' }
  },
  {
    path: '/admin/forgot-password',
    name: 'AdminForgotPassword',
    component: AdminForgotPassword,
    meta: { requiresGuest: true, title: 'Forgot Password' }
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
    path: '/admin/products/create',
    name: 'admin-product-create',
    component: AdminProductCreate,
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Create Product' }
  },
  {
    path: '/admin/products/:id',
    name: 'admin-product-edit',
    component: () => import('@/views/admin/AdminProductEdit.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Edit Product' }
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: () => import('@/views/admin/AdminUsers.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Users' }
  },
  {
    path: '/admin/users/:id',
    name: 'admin-user-edit',
    component: () => import('@/views/admin/AdminUserEdit.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Edit User' }
  },
  {
    path: '/admin/orders',
    name: 'admin-orders',
    component: () => import('@/views/admin/AdminOrders.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Orders' }
  },
  {
    path: '/admin/orders/:id',
    name: 'admin-order-detail',
    component: () => import('@/views/admin/AdminOrderDetail.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Order Detail' }
  },
  {
    path: '/admin/chat',
    name: 'admin-chat',
    component: () => import('@/views/admin/AdminChat.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Chat' }
  },
  {
    path: '/admin/profile',
    name: 'admin-profile',
    component: () => import('@/views/admin/AdminProfile.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Profile' }
  },
  {
    path: '/admin/settings',
    name: 'admin-settings',
    component: () => import('@/views/admin/AdminSettings.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Settings' }
  }
]
