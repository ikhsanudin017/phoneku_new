import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api, { initializeCsrf } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
    const router = useRouter()
    
    // State
    const user = ref(null)
    const token = ref(null)
    const loading = ref(false)
    const errorMessage = ref('')
    const initialized = ref(false)
    const isAuthenticated = ref(false)

    // Getters
    const isAdmin = computed(() => user.value?.role === 'admin')

    // Actions
    const setAuth = (userData, tokenValue) => {
        console.log('[AUTH] setAuth', userData, tokenValue)
        user.value = userData
        token.value = tokenValue
        isAuthenticated.value = true
        api.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
        localStorage.setItem('token', tokenValue)
        localStorage.setItem('user', JSON.stringify(userData))
    }

    const clearAuth = () => {
        console.log('[AUTH] clearAuth')
        user.value = null
        token.value = null
        isAuthenticated.value = false
        delete api.defaults.headers.common['Authorization']
        localStorage.removeItem('token')
        localStorage.removeItem('user')
    }

    // Initialize auth state from localStorage
    const initializeAuth = async () => {
        if (initialized.value) return
        console.log('[AUTH] initializeAuth')
        try {
            const storedToken = localStorage.getItem('token')
            const storedUser = localStorage.getItem('user')
            console.log('[AUTH] localStorage', storedToken, storedUser)
            if (storedToken && storedUser) {
                const userData = JSON.parse(storedUser)
                setAuth(userData, storedToken)

                // Verify token for admin users
                if (userData.role === 'admin') {
                    await verifyAdminAuth()
                }
            }
        } catch (error) {
            console.error('Auth initialization error:', error)
            clearAuth()
        } finally {
            initialized.value = true
            console.log('[AUTH] initialized', isAuthenticated.value, user.value)
        }
    }

    const verifyAdminAuth = async () => {
        try {
            const response = await api.get('/auth/admin/check')
            console.log('[AUTH] verifyAdminAuth response', response.data)
            if (!response.data.authenticated) {
                throw new Error('Admin verification failed')
            }
            user.value = response.data.user
        } catch (error) {
            console.error('Admin verification failed:', error)
            clearAuth()
            router.push({ name: 'AdminLogin' })
        }
    }

    const handleLogout = async () => {
        try {
            if (isAuthenticated.value) {
                await api.post('/auth/admin/logout')
            }
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            clearAuth()
            router.push({ name: 'AdminLogin' })
        }
    }

    // Fetch user after login or refresh
    const fetchUser = async () => {
        try {
            const response = await api.get('/admin/user')
            user.value = response.data
            isAuthenticated.value = true
        } catch (error) {
            isAuthenticated.value = false
            user.value = null
            console.error('Failed to fetch user:', error)
        }
    }

    return {
        // State
        user,
        token,
        loading,
        errorMessage,
        initialized,
        isAuthenticated,
        isAdmin,
        
        // Actions
        initializeAuth,
        setAuth,
        clearAuth,
        handleLogout,
        verifyAdminAuth,
        fetchUser
    }
})
