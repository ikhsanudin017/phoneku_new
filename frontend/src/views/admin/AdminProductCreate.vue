<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="text-2xl font-bold text-blue-600">
              PhoneKu Admin
            </router-link>
          </div>

          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/admin/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</router-link>
            <router-link to="/admin/products" class="text-blue-600 font-medium">Products</router-link>
            <router-link to="/admin/orders" class="text-gray-700 hover:text-blue-600">Orders</router-link>
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600">Users</router-link>
            <router-link to="/admin/chat" class="text-gray-700 hover:text-blue-600">Messages</router-link>

            <div class="relative" ref="profileDropdown">
              <button @click="showProfileMenu = !showProfileMenu" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                <span>{{ authStore.user?.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>

              <div v-if="showProfileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <router-link to="/welcome" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Store</router-link>
                <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Panel Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-t-lg text-white py-5 px-8">
        <div>
          <h2 class="text-2xl font-bold pb-2">Create New Product</h2>
          <h5 class="opacity-70">Add a new product to the catalog</h5>
        </div>
      </div>

      <!-- Error/Success Alerts -->
      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 whitespace-pre-line" role="alert">
        <span class="block sm:inline">{{ errorMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="errorMessage = ''">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Close</title>
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>

      <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ successMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="successMessage = ''">
          <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Close</title>
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form @submit.prevent="createProduct" class="p-6 space-y-6">
          <!-- Basic Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input
                  type="text"
                  id="name"
                  v-model="productForm.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select
                  id="category"
                  v-model="productForm.category"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  required
                >
                  <option value="handphone">Phone</option>
                  <option value="accessory">Accessory</option>
                </select>
              </div>

              <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price (Rp)</label>
                <input
                  type="number"
                  id="price"
                  v-model="productForm.price"
                  min="0"
                  step="1000"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  required
                />
              </div>

              <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input
                  type="number"
                  id="stock"
                  v-model="productForm.stock"
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  required
                />
              </div>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              id="description"
              v-model="productForm.description"
              rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              required
            ></textarea>
          </div>

          <!-- Images -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Product Images</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
              <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-4h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                  <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                    <span>Upload images</span>
                    <input
                      type="file"
                      id="images"
                      multiple
                      accept="image/*"
                      @change="handleImageUpload"
                      class="sr-only"
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
              </div>
            </div>
            <!-- Preview selected images -->
            <div v-if="imagePreviewUrls.length > 0" class="mt-4 grid grid-cols-3 gap-4">
              <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative">
                <img :src="url" class="h-24 w-24 object-cover rounded-md" />
                <button
                  @click="removeImage(index)"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"
                  type="button"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="saving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ saving ? 'Creating...' : 'Create Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { productsAPI } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()
const showProfileMenu = ref(false)

// Handle logout
const logout = async () => {
  try {
    await authStore.logout()
    router.push('/admin/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

// Form state
const productForm = reactive({
  name: '',
  category: 'handphone',
  price: '',
  stock: '',
  description: '',
})

const saving = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const imageFiles = ref([])
const imagePreviewUrls = ref([])

// Handle image upload
const handleImageUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      imageFiles.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        imagePreviewUrls.value.push(e.target.result)
      }
      reader.readAsDataURL(file)
    }
  })
}

// Remove image from preview
const removeImage = (index) => {
  imageFiles.value.splice(index, 1)
  imagePreviewUrls.value.splice(index, 1)
}

// Create product
const createProduct = async () => {
  saving.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    // Verify we have a token
    const token = localStorage.getItem('token')
    if (!token) {
      throw new Error('You are not logged in. Please log in and try again.')
    }

    const formData = new FormData()

    // Add all form fields
    const numberFields = ['price', 'stock']
    Object.keys(productForm).forEach(key => {
      let value = productForm[key]
      // Convert number strings to actual numbers
      if (numberFields.includes(key)) {
        value = parseFloat(value) || 0
      }
      formData.append(key, value)
    })

    // Add images
    imageFiles.value.forEach((file, index) => {
      const fieldName = index === 0 ? 'image' : `image${index + 1}`
      formData.append(fieldName, file)
    })

    const response = await productsAPI.create(formData)

    if (response.data.success) {
      successMessage.value = 'Product created successfully'

      // Clear form and images
      Object.keys(productForm).forEach(key => {
        productForm[key] = key === 'category' ? 'handphone' : ''
      })
      imageFiles.value = []
      imagePreviewUrls.value = []

      // Redirect after showing success message
      setTimeout(() => {
        router.push('/admin/products')
      }, 2000)
    } else {
      throw new Error(response.data.message || 'Failed to create product')
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errorMessages = Object.values(error.response.data.errors).flat()
      errorMessage.value = errorMessages.join('\n')
    } else {
      errorMessage.value = error.response?.data?.message || error.message || 'Failed to create product. Please try again.'
    }
    console.error('Error creating product:', error)
  } finally {
    saving.value = false
  }
}
</script>
