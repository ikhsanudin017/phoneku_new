<template>
  <div class="group">
    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow relative h-full flex flex-col">
      <!-- Product Image -->
      <div class="relative pt-[100%] bg-gray-100">
        <img
          :src="product.image || '/img/placeholder.jpg'"
          :alt="product.name"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
          :class="{ 'opacity-0': imageLoading }"
          @load="imageLoading = false"
          @error="handleImageError"
        >

        <!-- Discount Badge -->
        <div v-if="discount > 0"
          class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 text-xs font-medium rounded">
          -{{ discount }}%
        </div>

        <!-- Loading State -->
        <div v-if="imageLoading"
          class="absolute inset-0 bg-gray-100 animate-pulse flex items-center justify-center">
          <i class="fas fa-image text-3xl text-gray-300"></i>
        </div>
      </div>

      <!-- Product Info -->
      <div class="p-4 flex-grow flex flex-col">
        <!-- Product Name -->
        <h3 class="text-gray-900 font-medium mb-2 line-clamp-2 min-h-[2.5rem]">
          {{ product.name }}
        </h3>

        <!-- Prices -->
        <div class="mt-auto">
          <div class="space-y-1 mb-3">
            <p class="text-lg font-bold text-blue-600">
              Rp {{ formatPrice(product.price) }}
            </p>
            <p v-if="product.original_price && product.original_price > product.price"
              class="text-sm text-gray-500 line-through">
              Rp {{ formatPrice(product.original_price) }}
            </p>
          </div>

          <!-- Add to Cart -->
          <button
            @click="$emit('add-to-cart', product)"
            class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded transition-colors flex items-center justify-center space-x-2"
          >
            <i class="fas fa-shopping-cart"></i>
            <span>Tambah ke Keranjang</span>
          </button>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const imageLoading = ref(true)

const handleImageError = (e) => {
  e.target.src = '/img/placeholder.jpg'
  imageLoading.value = false
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const discount = computed(() => {
  if (!props.product.original_price || !props.product.price) return 0
  const diff = props.product.original_price - props.product.price
  return Math.round((diff / props.product.original_price) * 100)
})

defineEmits(['add-to-cart'])
</script>
