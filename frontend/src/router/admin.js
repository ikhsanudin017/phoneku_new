// src/router/admin.js

import AdminLogin from '@/views/admin/AdminLogin.vue'
import AdminRegister from '@/views/admin/AdminRegister.vue'
import AdminForgotPassword from '@/views/admin/AdminForgotPassword.vue'
import AdminDashboard from '@/views/admin/AdminDashboard.vue'
import AdminProducts from '@/views/admin/AdminProducts.vue'
import AdminUsers from '@/views/admin/AdminUsers.vue'
import AdminChat from '@/views/admin/AdminChat.vue'
import AdminOrders from '@/views/admin/AdminOrders.vue'
import AdminProductCreate from '@/views/admin/AdminProductCreate.vue'

export const adminRoutes = [
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { requiresGuest: true }
  },
  {
    path: '/admin/register',
    name: 'AdminRegister',
    component: AdminRegister,
    meta: { requiresGuest: true }
  },
  {
    path: '/admin/forgot-password',
    name: 'AdminForgotPassword',
    component: AdminForgotPassword,
    meta: { requiresGuest: true }
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
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/products',
    name: 'admin-products',
    component: AdminProducts,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: AdminUsers,
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
    path: '/admin/chat',
    name: 'admin-chat',
    component: AdminChat,
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]
