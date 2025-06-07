<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <router-link to="/welcome" class="text-blue-600 hover:text-blue-800">
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
            </router-link>
            <h1 class="text-xl font-semibold text-gray-900">Customer Support</h1>
          </div>
          <div class="flex items-center">
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
              Online
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Container -->
    <div class="max-w-4xl mx-auto p-4">
      <div class="bg-white rounded-lg shadow-sm border">
        <!-- Chat Header -->
        <div class="p-4 border-b bg-gray-50">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-900">Customer Support</p>
              <p class="text-xs text-gray-500">Online - responds within minutes</p>
            </div>
          </div>
        </div>

        <!-- Messages Container -->
        <div
          ref="messagesContainer"
          class="h-96 overflow-y-auto p-4 space-y-4"
        >
          <div v-if="loading" class="flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          </div>

          <div v-else-if="messages.length === 0" class="text-center text-gray-500 py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p>No messages yet. Start a conversation!</p>
          </div>

          <div
            v-for="message in messages"
            :key="message.id"
            class="flex"
            :class="message.sender_id === authStore.user?.id ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg text-sm"
              :class="message.sender_id === authStore.user?.id ?
                'bg-blue-500 text-white' :
                'bg-gray-200 text-gray-900'"
            >
              <p>{{ message.message }}</p>
              <p
                class="text-xs mt-1 opacity-75"
                :class="message.sender_id === authStore.user?.id ? 'text-blue-100' : 'text-gray-500'"
              >
                {{ formatTime(message.created_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="border-t p-4">
          <form @submit.prevent="sendMessage" class="flex space-x-2">
            <input
              v-model="newMessage"
              type="text"
              placeholder="Type your message..."
              class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :disabled="sending"
            />
            <button
              type="submit"
              :disabled="!newMessage.trim() || sending"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="sending" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { chatAPI } from '@/services/api'

const authStore = useAuthStore()

const messages = ref([])
const newMessage = ref('')
const loading = ref(false)
const sending = ref(false)
const messagesContainer = ref(null)

const fetchMessages = async () => {
  loading.value = true
  try {
    const response = await chatAPI.getCustomerChat()
    if (response.data.success) {
      messages.value = response.data.data || []
      await nextTick()
      scrollToBottom()
    }
  } catch (error) {
    console.error('Error fetching messages:', error)
  } finally {
    loading.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  sending.value = true
  try {
    const messageData = {
      message: newMessage.value.trim(),
      receiver_id: null // Admin will handle this
    }

    const response = await chatAPI.sendMessage(messageData)
    if (response.data.success) {
      newMessage.value = ''
      await fetchMessages() // Refresh messages
    }
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    sending.value = false
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTime = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diffInHours = Math.abs(now - date) / 36e5

  if (diffInHours < 24) {
    return date.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit'
    })
  } else {
    return date.toLocaleDateString('id-ID', {
      day: '2-digit',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
}

onMounted(() => {
  fetchMessages()

  // Auto-refresh messages every 3 seconds
  const interval = setInterval(() => {
    fetchMessages()
  }, 3000)

  // Cleanup interval on component unmount
  onUnmounted(() => {
    clearInterval(interval)
  })
})
</script>
