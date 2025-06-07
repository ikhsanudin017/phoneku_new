import { defineStore } from 'pinia'
import { cartAPI } from '@/services/api'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    cartCount: 0,
    subtotal: 0,
    loading: false,
    error: null
  }),

  getters: {
    totalItems: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
    totalPrice: (state) => state.items.reduce((total, item) => total + (item.product.price * item.quantity), 0),
  },

  actions: {
    async fetchCartItems() {
      this.loading = true
      this.error = null

      try {
        const response = await cartAPI.getItems()

        if (response.data.success) {
          this.items = response.data.data
          this.cartCount = response.data.cart_count
          this.subtotal = response.data.subtotal
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch cart items'
        console.error('Fetch cart error:', error)
      } finally {
        this.loading = false
      }
    },

    async addToCart(productId, data = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await cartAPI.addItem(productId, data)

        if (response.data.success) {
          this.cartCount = response.data.cart_count
          await this.fetchCartItems() // Refresh cart items
          return { success: true, message: response.data.message }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to add item to cart'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateQuantity(itemId, quantity) {
      this.loading = true
      this.error = null

      try {
        const response = await cartAPI.updateQuantity(itemId, quantity)

        if (response.data.success) {
          await this.fetchCartItems() // Refresh cart items
          return { success: true, message: response.data.message }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update quantity'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async removeItem(itemId) {
      this.loading = true
      this.error = null

      try {
        const response = await cartAPI.removeItem(itemId)

        if (response.data.success) {
          await this.fetchCartItems() // Refresh cart items
          return { success: true, message: response.data.message }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to remove item'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async clearCart() {
      this.loading = true
      this.error = null

      try {
        const response = await cartAPI.clear()

        if (response.data.success) {
          this.items = []
          this.cartCount = 0
          this.subtotal = 0
          return { success: true, message: response.data.message }
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to clear cart'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    async getCartCount() {
      try {
        const response = await cartAPI.getCount()

        if (response.data.success) {
          this.cartCount = response.data.cart_count
        }
      } catch (error) {
        console.error('Get cart count error:', error)
      }
    }
  }
})
