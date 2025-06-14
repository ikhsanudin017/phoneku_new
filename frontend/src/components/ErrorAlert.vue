<template>
  <div v-if="message" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <i class="fas fa-exclamation-circle text-red-400"></i>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-red-800">Error</h3>
        <div class="mt-1 text-sm text-red-700">
          {{ message }}
          <button @click="dismiss" class="ml-2 text-red-800 hover:text-red-900 underline">Dismiss</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['dismiss'])

const dismiss = () => {
  emit('dismiss')
}
</script>
