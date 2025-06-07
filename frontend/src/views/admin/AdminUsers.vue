<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Admin Navigation -->
    <nav class="bg-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/admin/dashboard" class="text-2xl font-bold text-blue-600">
              PhoneKu Admin
            </router-link>
          </div>

          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/admin/dashboard" class="text-gray-700 hover:text-blue-600">Dashboard</router-link>
            <router-link to="/admin/products" class="text-gray-700 hover:text-blue-600">Products</router-link>
            <router-link to="/admin/users" class="text-blue-600 font-medium">Users</router-link>
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
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
        <button @click="openAddModal" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
          Add New User
        </button>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search users..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <select
              v-model="filters.role"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Roles</option>
              <option value="customer">Customer</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <select
              v-model="filters.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div>
            <button
              @click="fetchUsers"
              class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="mt-2 text-gray-600">Loading users...</p>
        </div>

        <div v-else-if="users.length === 0" class="text-center py-12">
          <p class="text-gray-500">No users found.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-blue-600">
                          {{ getInitials(user.name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getRoleClass(user.role)" class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(user.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ user.status || 'active' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ user.orders_count || 0 }} orders
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button @click="editUser(user)" class="text-blue-600 hover:text-blue-700">
                      Edit
                    </button>
                    <button
                      v-if="user.id !== authStore.user.id"
                      @click="deleteUser(user)"
                      class="text-red-600 hover:text-red-700"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center">
        <nav class="flex space-x-2">
          <button
            v-for="page in paginationPages"
            :key="page"
            @click="changePage(page)"
            :class="[
              'px-3 py-2 rounded-md',
              page === pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            ]"
            :disabled="page === '...'"
          >
            {{ page }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            {{ isEditing ? 'Edit User' : 'Add New User' }}
          </h3>
        </div>

        <form @submit.prevent="saveUser" class="px-6 py-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input
              v-model="userForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="userForm.email"
              type="email"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="!isEditing">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              v-model="userForm.password"
              type="password"
              :required="!isEditing"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select
              v-model="userForm.role"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Select Role</option>
              <option value="customer">Customer</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input
              v-model="userForm.phone"
              type="tel"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
            {{ errorMessage }}
          </div>

          <div class="flex justify-end space-x-4 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
            </button>
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
import { adminAPI } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const saving = ref(false)
const showProfileMenu = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const users = ref([])
const pagination = ref(null)
const errorMessage = ref('')

const filters = reactive({
  search: '',
  role: '',
  status: ''
})

const userForm = reactive({
  id: null,
  name: '',
  email: '',
  password: '',
  role: '',
  phone: ''
})

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: 10,
      search: filters.search,
      role: filters.role,
      status: filters.status
    }

    const response = await adminAPI.getUsers(params)
    if (response.data.success) {
      users.value = response.data.data
      pagination.value = response.data.pagination
    }
  } catch (error) {
    console.error('Failed to fetch users:', error)
    // Mock data for demo
    users.value = [
      { id: 1, name: 'John Doe', email: 'john@example.com', role: 'customer', status: 'active', orders_count: 5, created_at: new Date().toISOString() },
      { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'customer', status: 'active', orders_count: 12, created_at: new Date(Date.now() - 86400000).toISOString() },
      { id: 3, name: 'Admin User', email: 'admin@phoneku.com', role: 'admin', status: 'active', orders_count: 0, created_at: new Date(Date.now() - 172800000).toISOString() },
    ]
  } finally {
    loading.value = false
  }
}

const openAddModal = () => {
  resetForm()
  isEditing.value = false
  showModal.value = true
}

const editUser = (user) => {
  Object.assign(userForm, user)
  userForm.password = '' // Don't pre-fill password
  isEditing.value = true
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
  errorMessage.value = ''
}

const resetForm = () => {
  Object.assign(userForm, {
    id: null,
    name: '',
    email: '',
    password: '',
    role: '',
    phone: ''
  })
}

const saveUser = async () => {
  saving.value = true
  errorMessage.value = ''

  try {
    let response
    if (isEditing.value) {
      response = await adminAPI.updateUser(userForm.id, userForm)
    } else {
      response = await adminAPI.createUser(userForm)
    }

    if (response.data.success) {
      closeModal()
      fetchUsers()
      alert(`User ${isEditing.value ? 'updated' : 'created'} successfully!`)
    } else {
      errorMessage.value = response.data.message || 'Operation failed'
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      errorMessage.value = errors.join(', ')
    } else {
      errorMessage.value = error.response?.data?.message || 'Operation failed'
    }
    console.error('Save user error:', error)
  } finally {
    saving.value = false
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete "${user.name}"?`)) return

  try {
    const response = await adminAPI.deleteUser(user.id)
    if (response.data.success) {
      fetchUsers()
      alert('User deleted successfully!')
    } else {
      alert('Failed to delete user')
    }
  } catch (error) {
    console.error('Delete user error:', error)
    alert('Failed to delete user')
  }
}

const changePage = (page) => {
  if (page !== '...' && page !== pagination.value.current_page) {
    fetchUsers(page)
  }
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const getRoleClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    customer: 'bg-blue-100 text-blue-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-green-100 text-green-800'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const paginationPages = computed(() => {
  if (!pagination.value) return []

  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []

  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')

    const start = Math.max(2, current - 1)
    const end = Math.min(last - 1, current + 1)

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }

    if (current < last - 2) pages.push('...')
    pages.push(last)
  }

  return pages
})

const logout = async () => {
  await authStore.logout()
  showProfileMenu.value = false
  router.push('/admin/login')
}

onMounted(() => {
  fetchUsers()
})
</script>
