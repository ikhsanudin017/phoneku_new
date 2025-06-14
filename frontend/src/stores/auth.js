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
        user.value = userData
        token.value = tokenValue
        isAuthenticated.value = true
        api.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
        localStorage.setItem('token', tokenValue)
        localStorage.setItem('user', JSON.stringify(userData))
    }

    const clearAuth = () => {
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

        try {
            const storedToken = localStorage.getItem('token')
            const storedUser = localStorage.getItem('user')

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
        }
    }

    const verifyAdminAuth = async () => {
        try {
            const response = await api.get('/auth/admin/check')
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
        verifyAdminAuth
    }
})
