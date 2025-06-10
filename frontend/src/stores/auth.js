import { defineStore } from 'pinia'
import { authAPI, initializeCsrf } from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token'),
    isAuthenticated: !!localStorage.getItem('token'),
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
        // Initialize CSRF protection
        await initializeCsrf()

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
        console.error('Login error:', error)
        if (error.response?.status === 403 && error.response?.data?.message?.includes('admin')) {
          return {
            success: false,
            isAdmin: true,
            message: error.response.data.message
          }
        }
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
        // Clear any existing auth data
        this.clearAuth()

        // Initialize CSRF protection
        await initializeCsrf()

        console.log('Attempting admin login with:', credentials)

        const response = await authAPI.adminLogin(credentials)
        console.log('Admin login response:', response.data)

        if (response.data.success) {
          const { token, user, token_type } = response.data

          // Validate admin role and token
          if (user.role !== 'admin') {
            throw new Error('Invalid admin account')
          }

          this.token = token
          this.user = user
          this.isAuthenticated = true
          this.tokenType = token_type || 'Bearer'

          // Store authentication data
          localStorage.setItem('token', token)
          localStorage.setItem('user', JSON.stringify(user))
          localStorage.setItem('isAdmin', 'true')
          localStorage.setItem('tokenType', this.tokenType)

          return { success: true }
        }

        throw new Error(response.data.message || 'Admin login failed')
      } catch (error) {
        console.error('Admin login error:', error)
        this.error = error.response?.data?.message || error.message || 'Admin login failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      this.error = null

      try {
        await initializeCsrf()

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
        console.error('Registration error:', error)
        this.error = error.response?.data?.message || 'Registration failed'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async adminRegister(userData) {
      this.loading = true
      this.error = null

      try {
        await initializeCsrf()

        const response = await authAPI.adminRegister(userData)

        if (response.data.success) {
          const { token, user, token_type } = response.data

          if (user.role !== 'admin') {
            throw new Error('Invalid admin registration')
          }

          this.token = token
          this.user = user
          this.isAuthenticated = true
          this.tokenType = token_type || 'Bearer'

          localStorage.setItem('token', token)
          localStorage.setItem('user', JSON.stringify(user))
          localStorage.setItem('isAdmin', 'true')
          localStorage.setItem('tokenType', this.tokenType)

          return { success: true }
        }
      } catch (error) {
        console.error('Admin registration error:', error)
        this.error = error.response?.data?.message || 'Admin registration failed'
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
        this.clearAuth()
      }
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('isAdmin')
      localStorage.removeItem('tokenType')
    },

    async fetchUser() {
      if (!this.token) return

      try {
        const response = await authAPI.getUser()

        if (response.data.success) {
          this.user = response.data.user
          this.isAuthenticated = true
          localStorage.setItem('user', JSON.stringify(this.user))
        }
      } catch (error) {
        console.error('Fetch user error:', error)
        if (error.response?.status === 401) {
          this.clearAuth()
        }
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')

      if (token && user) {
        try {
          this.token = token
          this.user = JSON.parse(user)
          this.isAuthenticated = true
          // Verify token is still valid
          this.fetchUser()
        } catch (error) {
          console.error('Error parsing user data:', error)
          this.clearAuth()
        }
      }
    }
  }
})
