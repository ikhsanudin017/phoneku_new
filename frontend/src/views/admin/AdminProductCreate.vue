<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Product</h1>
            <p class="mt-1 text-sm text-gray-600">Create a new product in your store</p>
          </div>
          <button
            @click="router.back()"
            class="inline-flex items-center px-4 py-2 text-blue-600 hover:text-blue-700"
          >
            <i class="fas fa-arrow-left mr-2"></i>
            Back
          </button>
        </div>
      </div>

      <!-- Error Alert -->
      <div v-if="error" class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-3"></i>
        {{ error }}
      </div>

      <!-- Product Form -->
      <form @submit.prevent="submitForm" class="bg-white shadow-lg rounded-lg p-6">
        <!-- Basic Information -->
        <div class="space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b">Basic Information</h2>
          
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name*</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              :class="['w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
                       errors.name ? 'border-red-300 focus:border-red-400 focus:ring-red-400' :
                       'border-gray-300 focus:border-blue-400 focus:ring-blue-400']"
              required
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category*</label>
            <select
              id="category"
              v-model="form.category"
              :class="['w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
                       errors.category ? 'border-red-300 focus:border-red-400 focus:ring-red-400' :
                       'border-gray-300 focus:border-blue-400 focus:ring-blue-400']"
              required
            >
              <option value="">Select a category</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
            <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</p>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description*</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              :class="['w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
                       errors.description ? 'border-red-300 focus:border-red-400 focus:ring-red-400' :
                       'border-gray-300 focus:border-blue-400 focus:ring-blue-400']"
              required
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
          </div>
        </div>

        <!-- Pricing and Inventory -->
        <div class="mt-8 space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b">Pricing & Inventory</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Price -->
            <div>
              <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price*</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                <input
                  type="number"
                  id="price"
                  v-model="form.price"
                  step="0.01"
                  min="0"
                  :class="['w-full pl-8 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
                           errors.price ? 'border-red-300 focus:border-red-400 focus:ring-red-400' :
                           'border-gray-300 focus:border-blue-400 focus:ring-blue-400']"
                  required
                />
              </div>
              <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
            </div>

            <!-- Stock -->
            <div>
              <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock*</label>
              <input
                type="number"
                id="stock"
                v-model="form.stock"
                min="0"
                :class="['w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors duration-150',
                         errors.stock ? 'border-red-300 focus:border-red-400 focus:ring-red-400' :
                         'border-gray-300 focus:border-blue-400 focus:ring-blue-400']"
                required
              />
              <p v-if="errors.stock" class="mt-1 text-sm text-red-600">{{ errors.stock }}</p>
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="mt-8 space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b">Product Images</h2>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Main Image*</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg" 
                 :class="errors.image ? 'border-red-300' : 'border-gray-300'">
              <div class="space-y-2 text-center">
                <div v-if="imagePreview" class="mb-4">
                  <img :src="imagePreview" alt="Preview" class="mx-auto h-32 w-auto" />
                </div>
                <div class="flex text-sm text-gray-600">
                  <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                    <span>Upload a file</span>
                    <input 
                      id="image" 
                      type="file" 
                      class="sr-only" 
                      accept="image/*"
                      @change="handleImageUpload" 
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">
                  PNG, JPG, GIF up to 5MB
                </p>
              </div>
            </div>
            <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
          </div>
        </div>

        <!-- Additional Options -->
        <div class="mt-8 space-y-6">
          <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b">Additional Options</h2>
          
          <div class="flex items-center">
            <input
              type="checkbox"
              id="featured"
              v-model="form.featured"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="featured" class="ml-2 text-sm text-gray-700">
              Mark as featured product
            </label>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
          <button
            type="button"
            @click="router.back()"
            class="px-4 py-2 text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm transition-colors duration-150"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!loading">Create Product</span>
            <span v-else class="flex items-center">
              <i class="fas fa-circle-notch fa-spin mr-2"></i>
              Creating...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AdminHeader from '@/components/admin/AdminHeader.vue'

export default {
  name: 'AdminProductCreate',
  components: {
    AdminHeader
  },
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const error = ref(null)
    const imagePreview = ref(null)
    const errors = reactive({})

    const categories = ['Smartphones', 'Accessories', 'Tablets', 'Wearables']

    const form = reactive({
      name: '',
      category: '',
      description: '',
      price: '',
      stock: '',
      image: null,
      featured: false
    })

    const validateForm = () => {
      errors.name = !form.name ? 'Product name is required' : null
      errors.category = !form.category ? 'Category is required' : null
      errors.description = !form.description ? 'Description is required' : null
      errors.price = !form.price ? 'Price is required' : null
      errors.stock = !form.stock ? 'Stock is required' : null
      errors.image = !form.image ? 'Product image is required' : null

      return !Object.values(errors).some(error => error !== null)
    }

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (!file) return

      if (!file.type.includes('image/')) {
        errors.image = 'Please upload an image file'
        return
      }

      if (file.size > 5 * 1024 * 1024) {
        errors.image = 'Image size should not exceed 5MB'
        return
      }

      form.image = file
      const reader = new FileReader()
      reader.onload = e => {
        imagePreview.value = e.target.result
      }
      reader.readAsDataURL(file)
      errors.image = null
    }

    const submitForm = async () => {
      if (!validateForm()) return

      loading.value = true
      error.value = null

      try {
        const formData = new FormData()
        formData.append('name', form.name)
        formData.append('category', form.category)
        formData.append('description', form.description)
        formData.append('price', form.price)
        formData.append('stock', form.stock)
        formData.append('featured', form.featured)
        if (form.image) {
          formData.append('image', form.image)
        }

        await axios.post('/api/admin/products', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        router.push('/admin/products')
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to create product'
        if (err.response?.data?.errors) {
          Object.assign(errors, err.response.data.errors)
        }
      } finally {
        loading.value = false
      }
    }

    return {
      router,
      form,
      loading,
      error,
      errors,
      categories,
      imagePreview,
      handleImageUpload,
      submitForm
    }
  }
}
</script>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type="number"] {
  -moz-appearance: textfield;
}

.aspect-w-4 {
  position: relative;
  padding-bottom: 75%;
}

.aspect-h-3 {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
}
</style>
