import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminAPI } from '@/services/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(null)
    const loading = ref(false)
    const errorMessage = ref('')

    const adminLogin = async (email, password) => {
        try {
            console.log('Starting admin login process...')
            loading.value = true
            errorMessage.value = ''

            const response = await adminAPI.login(email, password)

            if (response.data.success) {
                user.value = response.data.user
                token.value = response.data.token
                console.log('Admin login successful')
                router.push('/admin/dashboard')
            } else {
                throw new Error(response.data.message || 'Login failed')
            }
        } catch (error) {
            console.error('Admin login error:', error)
            errorMessage.value = error.response?.data?.message || 'Login failed'
            throw error
        } finally {
            loading.value = false
        }
    }

    return {
        user,
        token,
        loading,
        errorMessage,
        adminLogin
    }
})
