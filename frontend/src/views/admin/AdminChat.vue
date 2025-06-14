<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
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
      <!-- Page Header -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 rounded-lg relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-grid-white/10"></div>
        <div class="relative py-5 px-8">
          <h2 class="text-2xl font-bold text-white pb-2 text-shadow-glow">Customer Support Chat</h2>
          <h5 class="text-blue-200">Manage customer conversations</h5>
        </div>
      </div>

      <!-- Chat Interface -->
      <div class="backdrop-blur-lg bg-white/80 rounded-2xl shadow-2xl h-[calc(100vh-300px)] flex">
        <!-- Chat List -->
        <div class="w-1/4 border-r border-blue-100 p-4">
          <div class="mb-4">
            <input type="text" placeholder="Search conversations..." 
              class="w-full px-4 py-2 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div class="space-y-2">
            <!-- Conversations List -->
            <LoadingState :loading="loadingConversations" message="Loading conversations...">
              <div v-if="conversations.length === 0" class="p-4 text-center">
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
            </LoadingState>
          </div>
        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col">
          <div class="flex-1 p-4 overflow-y-auto">
            <!-- Messages -->
            <div class="chat-messages" ref="messagesContainer">
              <LoadingState :loading="loadingMessages" message="Loading messages...">
                <template v-if="!selectedConversation">
                  <div class="message received">
                    Select a customer to start chatting.
                    <div class="message-time">{{ formatTime(new Date()) }}</div>
                  </div>
                </template>

                <template v-else>
                  <div v-for="message in messages" :key="message.id" class="message" :class="message.sender_type === 'admin' ? 'sent' : 'received'">
                    {{ message.message }}
                    <div class="message-time">{{ formatTime(message.created_at) }}</div>
                  </div>
                </template>
              </LoadingState>
            </div>
          </div>
          
          <!-- Message Input -->
          <div class="p-4 border-t border-blue-100">
            <div class="flex space-x-2">
              <input
                v-model="newMessage"
                type="text"
                placeholder="Type your message..."
                class="flex-1 px-4 py-2 rounded-lg border border-blue-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :disabled="!selectedConversation || sending"
              />
              <button
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-all duration-200"
                :disabled="!newMessage.trim() || !selectedConversation || sending"
                @click.prevent="sendMessage"
              >
                <i :class="sending ? 'fas fa-circle-notch fa-spin' : 'fas fa-paper-plane'"></i>
              </button>
            </div>
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

.bg-grid-white\/10 {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
}

.text-shadow-glow {
  text-shadow: 0 0 10px rgba(148, 163, 184, 0.5);
}
</style>

<script setup>
import { ref, onMounted, nextTick, watch, onUnmounted } from 'vue'
import { format } from 'date-fns'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/services/api'
import ErrorAlert from '@/components/ErrorAlert.vue'
import SuccessAlert from '@/components/SuccessAlert.vue'
import LoadingState from '@/components/LoadingState.vue'
import PusherClient from 'pusher-js'

const router = useRouter()
const authStore = useAuthStore()
const showProfileMenu = ref(false)

// State management
const conversations = ref([])
const messages = ref([])
const selectedConversation = ref(null)
const newMessage = ref('')
const errorMessage = ref('')
const successMessage = ref('')
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
const pusher = ref(null)
const channel = ref(null)

// Methods
const fetchConversations = async () => {
  try {
    loadingConversations.value = true;
    errorMessage.value = '';

    const response = await api.get('/chat/conversations');
    conversations.value = response.data;
  } catch (error) {
    console.error('Error fetching conversations:', error);
    errorMessage.value = 'Failed to load conversations. Please try again.';
  } finally {
    loadingConversations.value = false;
  }
};

const fetchMessages = async (conversationId) => {
  try {
    loadingMessages.value = true;
    errorMessage.value = '';

    const response = await api.get(`/chat/conversations/${conversationId}/messages`);
    messages.value = response.data;

    // Mark messages as read
    await api.post(`/chat/conversations/${conversationId}/mark-read`);

    // Update unread count in conversations list
    const conv = conversations.value.find(c => c.id === conversationId);
    if (conv) {
      conv.unread_count = 0;
    }
  } catch (error) {
    console.error('Error fetching messages:', error);
    errorMessage.value = 'Failed to load messages. Please try again.';
  } finally {
    loadingMessages.value = false;
    await scrollToBottom();
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return;

  try {
    sending.value = true;
    errorMessage.value = '';

    const response = await api.post(`/chat/conversations/${selectedConversation.value.id}/messages`, {
      message: newMessage.value,
      sender_type: 'admin'
    });

    messages.value.push(response.data);
    newMessage.value = '';
    await scrollToBottom();
  } catch (error) {
    console.error('Error sending message:', error);
    errorMessage.value = 'Failed to send message. Please try again.';
  } finally {
    sending.value = false;
  }
};

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

const setupPusher = () => {
  try {
    // Initialize Pusher with auth endpoint
    pusher.value = new PusherClient(import.meta.env.VITE_PUSHER_APP_KEY, {
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      encrypted: true,
      authEndpoint: `${import.meta.env.VITE_API_URL}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: `Bearer ${authStore.token}`,
          Accept: 'application/json',
        },
      },
    });

    // Subscribe to the admin private channel
    channel.value = pusher.value.subscribe(`private-admin.${authStore.user.id}`);

    // Listen for new messages
    channel.value.bind('chat.message', (data) => {
      // Play notification sound for messages from customers
      if (data.sender_type === 'customer') {
        notificationSound.play();
      }

      // If this is for the currently selected conversation
      if (selectedConversation.value?.id === data.conversation_id) {
        messages.value.push(data.message);
        scrollToBottom();
      }

      // Update unread count in conversations list
      const conversation = conversations.value.find(
        c => c.id === data.conversation_id
      );
      if (conversation && data.sender_type === 'customer') {
        conversation.unread_count++;
      }
    });

    // Listen for user status changes
    channel.value.bind('chat.status', (data) => {
      const conversation = conversations.value.find(
        c => c.customer_id === data.user_id
      );
      if (conversation) {
        conversation.is_online = data.status === 'online';
      }
    });

  } catch (error) {
    console.error('Error setting up Pusher:', error);
    errorMessage.value = 'Failed to initialize real-time updates';
  }
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

// Add cleanup on component unmount
onUnmounted(() => {
  if (channel.value) {
    channel.value.unsubscribe()
  }
  if (pusher.value) {
    pusher.value.disconnect()
  }
})
</script>
