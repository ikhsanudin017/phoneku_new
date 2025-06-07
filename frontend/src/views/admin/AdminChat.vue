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
            <router-link to="/admin/users" class="text-gray-700 hover:text-blue-600">Users</router-link>
            <router-link to="/admin/chat" class="text-blue-600 font-medium">Messages</router-link>

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
          <h2 class="text-2xl font-bold pb-2">Chat Management</h2>
          <h5 class="opacity-70">Communicate with Customers</h5>
        </div>
      </div>

      <!-- Error Alerts -->
      <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ errorMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="errorMessage = ''">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Close</title>
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Chat Interface</h3>
        </div>

        <div class="chat-container">
          <!-- Conversations List -->
          <div class="contact-list">
            <div v-if="loadingConversations" class="p-4 text-center">
              <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
              <p class="mt-2 text-sm text-gray-600">Loading conversations...</p>
            </div>

            <div v-else-if="conversations.length === 0" class="p-4 text-center">
              <p class="text-gray-500">No customers found.</p>
            </div>

            <div v-else class="overflow-y-auto" style="max-height: calc(100vh - 250px);">
              <div
                v-for="conversation in conversations"
                :key="conversation.id"
                @click="selectConversation(conversation)"
                :class="[
                  'contact-item',
                  selectedConversation?.id === conversation.id ? 'active' : ''
                ]"
              >
                <div class="contact-avatar">
                  {{ getInitials(conversation.customer_name) }}
                </div>
                <div>
                  <div><strong>{{ conversation.customer_name }}</strong></div>
                  <div class="text-muted text-sm">
                    {{ conversation.customer_email }}
                    <span v-if="conversation.unread_count > 0" class="badge">
                      {{ conversation.unread_count }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Chat Area -->
          <div class="chat-area">
            <!-- Chat Header -->
            <div v-if="!selectedConversation" class="chat-header">
              Select a customer to start chatting
            </div>
            <div v-else class="chat-header">
              <strong>{{ selectedConversation.customer_name }}</strong>
              <br>
              <small>Online</small>
            </div>

            <!-- Messages -->
            <div class="chat-messages" ref="messagesContainer">
              <div v-if="loadingMessages" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
              </div>

              <template v-else>
                <div v-if="!selectedConversation" class="message received">
                  Select a customer to start chatting.
                  <div class="message-time">{{ formatTime(new Date()) }}</div>
                </div>

                <div v-for="message in messages" :key="message.id" class="message" :class="message.sender_type === 'admin' ? 'sent' : 'received'">
                  {{ message.message }}
                  <div class="message-time">{{ formatTime(message.created_at) }}</div>
                </div>
              </template>
            </div>

            <!-- Message Input -->
            <div class="message-input">
              <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                <input
                  v-model="newMessage"
                  type="text"
                  placeholder="Type a message..."
                  :disabled="!selectedConversation"
                  class="flex-1 p-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
                <button
                  type="submit"
                  :disabled="sending || !selectedConversation"
                  class="btn-send"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Quick Response Templates -->
        <div class="p-6 border-t border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Response Templates</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <button
              v-for="template in quickResponses"
              :key="template.id"
              @click="useQuickResponse(template.message)"
              class="p-3 text-left border border-gray-200 rounded-md hover:bg-gray-50 text-sm"
            >
              {{ template.message }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.chat-container {
  display: flex;
  height: calc(100vh - 250px);
  min-height: 500px;
  background-color: #e5ddd5;
}

.contact-list {
  width: 300px;
  background: white;
  border-right: 1px solid #e2e8f0;
  overflow-y: auto;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  cursor: pointer;
  border-bottom: 1px solid #e2e8f0;
  transition: background-color 0.2s;
}

.contact-item:hover {
  background-color: #f7fafc;
}

.contact-item.active {
  background-color: #ebf8ff;
}

.contact-avatar {
  width: 40px;
  height: 40px;
  background-color: #4a90e2;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.chat-area {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.chat-header {
  background: white;
  padding: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
}

.message {
  max-width: 70%;
  margin-bottom: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 1rem;
  position: relative;
}

.message.sent {
  background-color: #dcf8c6;
  margin-left: auto;
  border-bottom-right-radius: 0.25rem;
}

.message.received {
  background-color: white;
  margin-right: auto;
  border-bottom-left-radius: 0.25rem;
}

.message-time {
  font-size: 0.75rem;
  color: #666;
  margin-top: 0.25rem;
  text-align: right;
}

.message-input {
  background: white;
  padding: 1rem;
  border-top: 1px solid #e2e8f0;
}

.btn-send {
  padding: 0.75rem;
  background-color: #4a90e2;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.btn-send:hover:not(:disabled) {
  background-color: #357abd;
}

.btn-send:disabled {
  background-color: #cbd5e0;
  cursor: not-allowed;
}

.badge {
  background-color: #e53e3e;
  color: white;
  border-radius: 9999px;
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  font-weight: bold;
  margin-left: 0.5rem;
}

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import Pusher from 'pusher-js'
import axios from 'axios'
import { format } from 'date-fns'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { chatAPI } from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()
const showProfileMenu = ref(false)

// State management
const conversations = ref([])
const messages = ref([])
const selectedConversation = ref(null)
const newMessage = ref('')
const errorMessage = ref('')
const loadingConversations = ref(true)
const loadingMessages = ref(false)
const sending = ref(false)
const messagesContainer = ref(null)

// Quick response templates
const quickResponses = ref([
  { id: 1, message: "Hi! How can I help you today?" },
  { id: 2, message: "Thank you for contacting us. Please let me check that for you." },
  { id: 3, message: "Is there anything else I can help you with?" }
])

// Audio for notifications
const notificationSound = new Audio('/notification.mp3')

// Pusher setup
const setupPusher = () => {
  const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    encrypted: true
  })

  // Subscribe to the admin channel
  const channel = pusher.subscribe('admin-chat')

  // Listen for new messages
  channel.bind('new-message', (data) => {
    if (selectedConversation.value?.id === data.conversation_id) {
      messages.value.push(data.message)
      scrollToBottom()
    }
    // Update unread count for other conversations
    if (selectedConversation.value?.id !== data.conversation_id) {
      const conv = conversations.value.find(c => c.id === data.conversation_id)
      if (conv) {
        conv.unread_count++
        notificationSound.play()
      }
    }
  })
}

// Fetch conversations
const fetchConversations = async () => {
  try {
    loadingConversations.value = true
    const response = await chatAPI.get('/admin/conversations')
    conversations.value = response.data
  } catch (error) {
    errorMessage.value = 'Failed to load conversations. Please try again.'
    console.error('Error fetching conversations:', error)
  } finally {
    loadingConversations.value = false
  }
}

// Fetch messages for selected conversation
const fetchMessages = async (conversationId) => {
  try {
    loadingMessages.value = true
    const response = await chatAPI.get(`/admin/conversations/${conversationId}/messages`)
    messages.value = response.data
    // Mark messages as read
    await chatAPI.post(`/admin/conversations/${conversationId}/mark-read`)
    // Update unread count in conversations list
    const conv = conversations.value.find(c => c.id === conversationId)
    if (conv) {
      conv.unread_count = 0
    }
  } catch (error) {
    errorMessage.value = 'Failed to load messages. Please try again.'
    console.error('Error fetching messages:', error)
  } finally {
    loadingMessages.value = false
    await scrollToBottom()
  }
}

// Send a message
const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  try {
    sending.value = true
    const response = await chatAPI.post(`/admin/conversations/${selectedConversation.value.id}/messages`, {
      message: newMessage.value,
      sender_type: 'admin'
    })
    messages.value.push(response.data)
    newMessage.value = ''
    await scrollToBottom()
  } catch (error) {
    errorMessage.value = 'Failed to send message. Please try again.'
    console.error('Error sending message:', error)
  } finally {
    sending.value = false
  }
}

// Select a conversation
const selectConversation = async (conversation) => {
  selectedConversation.value = conversation
  await fetchMessages(conversation.id)
}

// Utility functions
const getInitials = (name) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const formatTime = (date) => {
  return format(new Date(date), 'HH:mm')
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const useQuickResponse = (message) => {
  newMessage.value = message
}

// Lifecycle hooks
onMounted(() => {
  fetchConversations()
  setupPusher()
})

// Watch for conversation changes
watch(selectedConversation, () => {
  if (selectedConversation.value) {
    messages.value = []
    fetchMessages(selectedConversation.value.id)
  }
})
</script>
