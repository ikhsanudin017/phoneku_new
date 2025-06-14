<template>
  <div v-if="loading" class="h-full w-full flex flex-col items-center justify-center py-12">
    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
    <p class="mt-4 text-gray-600">{{ message || 'Loading...' }}</p>
  </div>
  <div v-else>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: ''
  }
})
</script>
