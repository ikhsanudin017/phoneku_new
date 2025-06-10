<template>
  <div>
    <!-- Header with Wave -->
    <div class="relative">
      <div class="bg-blue-500 min-h-[200px] relative">
        <!-- Wave SVG -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden" style="line-height: 0;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
            <path fill="#f9fafb" fill-opacity="1"
              d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,266.7C960,267,1056,245,1152,224C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
            </path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 -mt-10 relative z-10">
      <div class="bg-white rounded-xl p-8 shadow-md border border-gray-100">
        <h3 class="text-2xl font-semibold text-gray-700 mb-2">Keamanan & Privasi</h3>
        <p class="text-gray-500 mb-6">Kontrol penuh atas informasi pribadi dan perlindungan akses akun Anda.</p>

        <form @submit.prevent="changePassword">
          <!-- Ubah Password -->
          <div class="space-y-6 max-w-3xl">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Ubah Password</label>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input
                  type="password"
                  v-model="passwordForm.current_password"
                  placeholder="Password Lama"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-700"
                  required
                >
                <input
                  type="password"
                  v-model="passwordForm.new_password"
                  placeholder="Password Baru"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-700"
                  required
                >
                <input
                  type="password"
                  v-model="passwordForm.confirm_password"
                  placeholder="Konfirmasi Password"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-700"
                  required
                >
              </div>
              <div class="mt-2">
                <router-link to="/forgot-password" class="text-sm text-blue-500">Lupa Password?</router-link>
              </div>
              <div v-if="passwordError" class="mt-2 text-red-600 text-sm">{{ passwordError }}</div>
              <div v-if="passwordSuccess" class="mt-2 text-green-600 text-sm">{{ passwordSuccess }}</div>

              <button
                type="submit"
                :disabled="passwordLoading || !isPasswordFormValid"
                class="mt-4 w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-300 disabled:opacity-50"
              >
                {{ passwordLoading ? 'Menyimpan...' : 'Perbarui Password' }}
              </button>
            </div>

            <!-- Hapus Akun -->
            <div class="pt-6 border-t border-gray-200">
              <label class="block text-sm font-medium text-gray-700 mb-1">Hapus Akun</label>
              <p class="block text-sm text-gray-500 mb-4">Akun Anda akan dihapus secara permanen dan tidak dapat dikembalikan.</p>

              <button
                type="button"
                @click="requestAccountDeletion"
                class="w-full px-4 py-2 border border-red-500 text-red-500 hover:bg-red-50 font-semibold rounded-lg transition-colors"
              >
                Ajukan Penghapusan Akun
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { userAPI } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  confirm_password: ''
})

const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const userProfile = ref({
  name: '',
  profile_picture: null
})

const isPasswordFormValid = computed(() => {
  return passwordForm.current_password &&
         passwordForm.new_password &&
         passwordForm.confirm_password &&
         passwordForm.new_password === passwordForm.confirm_password &&
         passwordForm.new_password.length >= 8
})

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.confirm_password) {
    passwordError.value = 'Password baru dan konfirmasi tidak sama'
    return
  }

  passwordLoading.value = true
  passwordError.value = ''
  passwordSuccess.value = ''

  try {
    const response = await userAPI.changePassword({
      current_password: passwordForm.current_password,
      new_password: passwordForm.new_password,
      new_password_confirmation: passwordForm.confirm_password
    })

    if (response.data.success) {
      passwordSuccess.value = 'Password berhasil diperbarui!'
      // Reset form
      passwordForm.current_password = ''
      passwordForm.new_password = ''
      passwordForm.confirm_password = ''
    } else {
      passwordError.value = response.data.message || 'Gagal memperbarui password'
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      passwordError.value = errors.join(', ')
    } else {
      passwordError.value = error.response?.data?.message || 'Gagal memperbarui password'
    }
    console.error('Change password error:', error)
  } finally {
    passwordLoading.value = false

    // Clear messages after 3 seconds
    setTimeout(() => {
      passwordError.value = ''
      passwordSuccess.value = ''
    }, 3000)
  }
}

const requestAccountDeletion = () => {
  if (confirm('Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) {
    // In a real app, this would call an API endpoint
    alert('Permintaan penghapusan akun telah dikirim. Anda akan menerima email dengan instruksi selanjutnya.')
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

onMounted(async () => {
  // Load user profile
  try {
    const response = await userAPI.getProfile()
    if (response.data.success) {
      userProfile.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading profile:', error)
  }
})
</script>
