import { defineStore } from 'pinia'
import { authAPI } from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isCustomer: (state) => state.user?.role === 'customer',
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await authAPI.login(credentials)

        if (response.data.success) {
          this.token = response.data.token
          this.user = response.data.user
          this.isAuthenticated = true

          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))

          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async adminLogin(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await authAPI.adminLogin(credentials)

        if (response.data.success) {
          this.token = response.data.token
          this.user = response.data.user
          this.isAuthenticated = true

          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))

          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Admin login failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null

      try {
        const response = await authAPI.register(userData)

        if (response.data.success) {
          this.token = response.data.token
          this.user = response.data.user
          this.isAuthenticated = true

          localStorage.setItem('token', this.token)
          localStorage.setItem('user', JSON.stringify(this.user))

          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        if (this.token) {
          await authAPI.logout()
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        this.isAuthenticated = false

        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    },

    async fetchUser() {
      if (!this.token) return

      try {
        const response = await authAPI.getUser()

        if (response.data.success) {
          this.user = response.data.user
          this.isAuthenticated = true
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        this.logout()
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')

      if (token && user) {
        this.token = token
        this.user = JSON.parse(user)
        this.isAuthenticated = true
        this.fetchUser() // Verify token is still valid
      }
    }
  }
})
