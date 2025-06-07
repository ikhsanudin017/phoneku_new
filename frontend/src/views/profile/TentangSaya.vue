<template>
  <div>
    <!-- Header with Wave -->
    <div class="relative">
      <div class="bg-blue-500 min-h-[400px] sm:min-h-[400px] md:min-h-[500px] lg:min-h-[400px] xl:min-h-[350px] relative">
      </div>

      <!-- Wave SVG -->
      <div class="absolute bottom-[-24px] left-0 w-full overflow-hidden wave-container" style="line-height: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
          <path fill="#f9fafb" fill-opacity="1"
            d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,266.7C960,267,1056,245,1152,224C1248,203,1344,181,1392,170.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
          </path>
        </svg>
      </div>

      <!-- Banner Image -->
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4/5 max-w-3xl z-0 pointer-events-none">
        <div class="relative w-full h-full overflow-hidden">
          <img src="/img/banner4.png" alt="Smartphones"
            class="object-contain w-full h-auto max-h-[180px] sm:max-h-[250px] md:max-h-[300px] lg:max-h-[350px] xl:max-h-[400px]">
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 -mt-48 relative z-10">
      <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ successMessage }}
      </div>

      <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="text-center mb-8">
          <div class="inline-block relative mb-4">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg mx-auto">
              <img :src="form.profile_picture_preview || form.profile_picture || '/img/profile.png'"
                alt="Profile Picture" class="w-full h-full object-cover">
            </div>
            <button @click="$refs.fileInput.click()"
              class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full shadow-md hover:bg-blue-600">
              <i class="fas fa-camera"></i>
            </button>
            <input type="file" ref="fileInput" @change="handleProfilePictureChange"
              class="hidden" accept="image/png, image/jpeg, image/jpg">
          </div>
          <h2 class="text-2xl font-bold text-gray-900">Tentang Saya</h2>
          <p class="text-gray-600">Atur dan ubah informasi pribadi anda sesuai keinginan</p>
        </div>

        <form @submit.prevent="handleSubmit" class="max-w-3xl mx-auto">
          <!-- Username -->
          <div class="mb-6">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" id="username" v-model="form.username"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <span v-if="errors.username" class="text-red-500 text-sm">{{ errors.username }}</span>
          </div>

          <!-- Name -->
          <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" id="name" v-model="form.name"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
          </div>

          <!-- Email -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-1">
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <router-link to="/profile/ubah-email" class="text-blue-600 hover:text-blue-700 text-sm">Ubah</router-link>
            </div>
            <input type="email" id="email" :value="authStore.user?.email" readonly
              class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
          </div>

          <!-- Phone -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-1">
              <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
              <router-link to="/profile/ubah-no-tlp" class="text-blue-600 hover:text-blue-700 text-sm">
                {{ form.phone ? 'Ubah' : 'Tambah' }}
              </router-link>
            </div>
            <input type="tel" id="phone" v-model="form.phone" placeholder="Belum ditambahkan"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>

          <!-- Address -->
          <div class="mb-6">
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea id="address" v-model="form.address" rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit"
              class="bg-blue-500 text-white px-8 py-3 rounded-full hover:bg-blue-600 shadow-md transition duration-300"
              :disabled="loading">
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const fileInput = ref(null)

const form = ref({
  username: '',
  name: '',
  phone: '',
  address: '',
  recipient_name: '',
  label: 'Rumah',
  gender: '',
  birth_day: '',
  birth_month: '',
  birth_year: '',
  profile_picture: null,
  profile_picture_preview: null
})

const loading = ref(false)
const errors = ref({})
const successMessage = ref('')

const months = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: currentYear - 1900 + 1 }, (_, i) => currentYear - i)
})

const handleProfilePictureChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file size (1MB max)
  if (file.size > 1024 * 1024) {
    errors.value.profile_picture = 'File size must be less than 1MB'
    return
  }

  // Validate file type
  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']
  if (!allowedTypes.includes(file.type)) {
    errors.value.profile_picture = 'File must be PNG, JPG, or JPEG'
    return
  }

  form.value.profile_picture = file
  form.value.profile_picture_preview = URL.createObjectURL(file)
}

const handleSubmit = async () => {
  try {
    loading.value = true
    errors.value = {}

    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (key === 'profile_picture' && form.value[key] instanceof File) {
        formData.append(key, form.value[key])
      } else if (key !== 'profile_picture_preview') {
        formData.append(key, form.value[key])
      }
    })

    const response = await axios.post('/api/profile/update', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    successMessage.value = 'Profile updated successfully!'
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

    // Update auth store
    authStore.setUser(response.data.user)
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value = { general: 'An error occurred while updating your profile.' }
    }
  } finally {
    loading.value = false
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/profile')
    const userData = response.data
    form.value = {
      username: userData.username || '',
      name: userData.name || '',
      phone: userData.phone || '',
      address: userData.address || '',
      recipient_name: userData.recipient_name || userData.name || '',
      label: userData.label || 'Rumah',
      gender: userData.gender || '',
      birth_day: userData.birth_day || '',
      birth_month: userData.birth_month || '',
      birth_year: userData.birth_year || '',
      profile_picture: userData.profile_picture || null,
      profile_picture_preview: null
    }
  } catch (error) {
    console.error('Error loading profile:', error)
    errors.value = { general: 'Failed to load profile data' }
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.wave-container > svg {
  display: block;
}

@media screen and (max-width: 640px) {
  .banner-height {
    height: 400px;
  }
  .banner-image {
    max-height: 250px;
  }
}

@media screen and (min-width: 1500px) {
  .banner-height {
    height: 700px;
  }
  .banner-image {
    max-height: 500px;
  }
}
</style>
