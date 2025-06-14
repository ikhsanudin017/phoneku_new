import api from './api';

export const chatAPI = {
    // Get all conversations for admin
    getConversations: async () => {
        return api.get('/admin/conversations');
    },

    // Get messages for a specific conversation
    getMessages: async (conversationId) => {
        return api.get(`/admin/conversations/${conversationId}/messages`);
    },

    // Send a message
    sendMessage: async (conversationId, data) => {
        return api.post(`/admin/conversations/${conversationId}/messages`, data);
    },

    // Mark messages from a conversation as read
    markRead: async (conversationId) => {
        return api.post(`/admin/conversations/${conversationId}/mark-read`);
    }
};
