<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 rounded-lg relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-grid-white/10"></div>
        <div class="relative py-5 px-8 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-white pb-2 text-shadow-glow">User Management</h2>
            <h5 class="text-blue-200">Manage system users and permissions</h5>
          </div>
          <button @click="openAddModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-all duration-200 ease-in-out transform hover:scale-105">
            Add New User
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="backdrop-blur-lg bg-white/80 rounded-2xl p-6 shadow-2xl">

        <!-- Alerts -->
        <ErrorAlert
          v-if="errorMessage"
          :message="errorMessage"
          @dismiss="errorMessage = ''"
        />
        <SuccessAlert
          v-if="successMessage"
          :message="successMessage"
          @dismiss="successMessage = ''"
        />

        <!-- Users List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">All Users</h2>
            <p class="text-sm text-gray-500 mt-1">View and manage user accounts</p>
          </div>

          <LoadingState :loading="loading" message="Loading users...">
            <div v-if="users.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-2xl text-gray-400"></i>
              </div>
              <p class="text-gray-500">No users found.</p>
              <button @click="openAddModal" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
                Add your first user
              </button>
            </div>

            <div v-else>
              <table class="min-w-full divide-y divide-gray-100">
                <thead>
                  <tr class="bg-gray-50">
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                  <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
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
                          <div class="text-sm text-gray-500">
                            <div>{{ user.email }}</div>
                            <div v-if="user.profile?.phone" class="text-xs text-gray-400">
                              {{ user.profile.phone }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getRoleClass(user.role)"
                        class="px-3 py-1 text-xs font-medium rounded-full inline-flex items-center">
                        <i :class="['fas', user.role === 'admin' ? 'fa-shield-alt' : 'fa-user', 'mr-1']"></i>
                        {{ user.role }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-col gap-1">
                        <span :class="getStatusClass(user.status)"
                          class="px-3 py-1 text-xs font-medium rounded-full inline-flex items-center w-fit">
                          <i class="fas fa-circle text-[8px] mr-1.5"></i>
                          {{ user.status || 'active' }}
                        </span>
                        <span v-if="user.last_login_at" class="text-xs text-gray-400">
                          Last login: {{ formatDate(user.last_login_at) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(user.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ user.orders_count || 0 }} orders
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-3">
                        <button @click="editUser(user)"
                          class="text-gray-500 hover:text-blue-600 transition-colors">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button v-if="user.id !== authStore.user.id"
                          @click="deleteUser(user)"
                          class="text-gray-500 hover:text-red-600 transition-colors">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Pagination -->
              <div v-if="pagination && pagination.last_page > 1"
                class="flex justify-center items-center space-x-2 p-6 bg-gray-50 border-t border-gray-100">
                <button
                  v-for="page in paginationPages"
                  :key="page"
                  @click="changePage(page)"
                  :class="[
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                    page === pagination.current_page
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
                  ]"
                  :disabled="page === '...'"
                >
                  {{ page }}
                </button>
              </div>
            </div>
          </LoadingState>
        </div>

        <!-- User Form Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
              <h3 class="text-xl font-semibold text-gray-900">
                {{ isEditing ? 'Edit User' : 'Add New User' }}
              </h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
              </button>
            </div>

            <form @submit.prevent="saveUser" class="px-6 py-4 space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input
                  v-model="userForm.name"
                  type="text"
                  required
                  placeholder="Enter full name"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input
                  v-model="userForm.email"
                  type="email"
                  required
                  placeholder="Enter email address"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                />
              </div>

              <div v-if="!isEditing">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input
                  v-model="userForm.password"
                  type="password"
                  :required="!isEditing"
                  placeholder="Enter password"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                  <select
                    v-model="userForm.role"
                    required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors appearance-none"
                  >
                    <option value="">Select Role</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                  <select
                    v-model="userForm.status"
                    required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors appearance-none"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input
                  v-model="userForm.phone"
                  type="tel"
                  placeholder="Enter phone number"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors"
                />
              </div>

              <div v-if="errorMessage"
                class="bg-red-50 border border-red-200 text-red-600 px-5 py-4 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                {{ errorMessage }}
              </div>

              <div class="sticky bottom-0 bg-gray-50 px-6 py-4 -mx-6 mt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="saving"
                  class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 font-medium transition-colors inline-flex items-center"
                >
                  <i v-if="saving" class="fas fa-circle-notch fa-spin mr-2"></i>
                  <span>{{ saving ? 'Saving...' : (isEditing ? 'Update User' : 'Create User') }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { adminAPI } from '@/services/api'
import ErrorAlert from '@/components/ErrorAlert.vue'
import SuccessAlert from '@/components/SuccessAlert.vue'
import LoadingState from '@/components/LoadingState.vue'
import AdminHeader from '@/components/admin/AdminHeader.vue'

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
const successMessage = ref('')

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
  phone: '',
  status: 'active',
  profile: {
    recipient_name: '',
    phone: '',
    address: '',
    label: 'Default'
  }
})

const fetchUsers = async (page = 1) => {
  try {
    loading.value = true
    errorMessage.value = ''

    const response = await adminAPI.getUsers({
      page,
      search: filters.search,
      role: filters.role,
      status: filters.status
    })

    if (response.data.success) {
      users.value = response.data.data
      pagination.value = response.data.meta
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    errorMessage.value = 'Failed to load users. Please try again.'
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
  Object.assign(userForm, {
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    status: user.status || 'active',
    phone: user.profile?.phone || '',
    password: '', // Don't pre-fill password
    profile: {
      recipient_name: user.name,
      phone: user.profile?.phone || '',
      address: user.profile?.address || '',
      label: user.profile?.label || 'Default'
    }
  })
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
    phone: '',
    status: 'active',
    profile: {
      recipient_name: '',
      phone: '',
      address: '',
      label: 'Default'
    }
  })
}

const saveUser = async () => {
  saving.value = true
  errorMessage.value = ''

  try {
    // Prepare user data
    const userData = {
      name: userForm.name,
      email: userForm.email,
      role: userForm.role,
      status: userForm.status,
      phone: userForm.phone
    }

    if (!isEditing.value) {
      userData.password = userForm.password
    } else if (userForm.password) {
      userData.password = userForm.password
    }

    let response
    if (isEditing.value) {
      response = await adminAPI.updateUser(userForm.id, userData)
    } else {
      response = await adminAPI.createUser(userData)
    }

    if (response.data.success) {
      successMessage.value = `User ${isEditing.value ? 'updated' : 'created'} successfully.`
      showModal.value = false
      fetchUsers()
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
  if (user.id === authStore.user?.id) {
    errorMessage.value = 'You cannot delete your own account.'
    return
  }

  if (!confirm(`Are you sure you want to delete "${user.name}"?\nThis action cannot be undone and will delete all associated data.`)) return

  try {
    loading.value = true
    errorMessage.value = ''

    const response = await adminAPI.deleteUser(user.id)
    if (response.data.success) {
      successMessage.value = 'User deleted successfully.'
      fetchUsers()
    } else {
      errorMessage.value = response.data.message || 'Failed to delete user'
    }
  } catch (error) {
    console.error('Delete user error:', error)
    errorMessage.value = 'Failed to delete user'
  } finally {
    loading.value = false
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
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 0) {
    return 'Today ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } else if (diffDays === 1) {
    return 'Yesterday ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } else {
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
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

const userStats = computed(() => {
  if (!users.value.length) return {}

  return {
    total: users.value.length,
    admins: users.value.filter(u => u.role === 'admin').length,
    customers: users.value.filter(u => u.role === 'customer').length,
    active: users.value.filter(u => u.status === 'active').length,
    inactive: users.value.filter(u => u.status === 'inactive').length,
  }
})

const usersWithOrders = computed(() => {
  return users.value.filter(u => u.orders_count > 0).length
})

const averageOrdersPerUser = computed(() => {
  if (!users.value.length) return 0
  const totalOrders = users.value.reduce((sum, user) => sum + (user.orders_count || 0), 0)
  return (totalOrders / users.value.length).toFixed(1)
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.table-row-enter-active,
.table-row-leave-active {
  transition: all 0.3s ease;
}

.table-row-enter-from,
.table-row-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}

/* Custom scrollbar for modal */
.modal-content::-webkit-scrollbar {
  width: 8px;
}

.modal-content::-webkit-scrollbar-track {
  background-color: rgba(243, 244, 246, 1);
  border-radius: 6px;
}

.modal-content::-webkit-scrollbar-thumb {
  background-color: rgba(209, 213, 219, 1);
  border-radius: 6px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 1);
}

.bg-grid-white {
  background-image: linear-gradient(white 1px, transparent 1px),
    linear-gradient(to right, white 1px, transparent 1px);
}
.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7), 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
