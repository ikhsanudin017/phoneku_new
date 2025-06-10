<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavbarComponent />

    <!-- Header Section with Wave -->
    <div class="relative overflow-hidden bg-gradient-to-b from-blue-600 to-blue-400">
      <!-- Banner Container -->
      <div class="container mx-auto px-4 py-8">
        <div class="rounded-2xl overflow-hidden shadow-2xl relative bg-white" id="banner-slider">
          <div class="slide-container relative w-full h-[400px] md:h-[500px] group">
            <div class="slides w-full h-full">
              <div v-for="(banner, index) in banners"
                :key="index"
                :class="['slide w-full h-full', { 'active': currentSlideIndex === index }]">
                <img :src="banner.image" :alt="banner.alt"
                  class="w-full h-full object-cover"
                  @error="banner.image = '/img/placeholder.jpg'" />
                <div class="absolute inset-0 bg-gradient-to-r from-black/30 via-transparent to-black/30"></div>
              </div>
            </div>

            <!-- Banner Navigation Dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex justify-center space-x-4 z-10">
              <button
                v-for="(_, index) in banners"
                :key="index"
                :class="[
                  'w-2.5 h-2.5 rounded-full transition-all duration-300 transform',
                  currentSlideIndex === index
                    ? 'bg-white w-8'
                    : 'bg-white/50 hover:bg-white/70'
                ]"
                @click="currentSlideIndex = index"
                aria-label="Pilih slide">
              </button>
            </div>

            <!-- Navigation Buttons -->
            <button
              class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/20 backdrop-blur-sm hover:bg-black/40 text-white rounded-full p-3 z-20 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
              @click="prevSlide"
              aria-label="Sebelumnya">
              <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button
              class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/20 backdrop-blur-sm hover:bg-black/40 text-white rounded-full p-3 z-20 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
              @click="nextSlide"
              aria-label="Selanjutnya">
              <i class="fas fa-chevron-right text-xl"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Wave Transition -->
      <div class="wave-section">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="wave-svg" preserveAspectRatio="none">
          <path fill="#f9fafb" fill-opacity="1"
            d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,128C672,107,768,85,864,96C960,107,1056,149,1152,154.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
          </path>
        </svg>
      </div>
    </div>

    <!-- Main Content -->
    <main>
      <!-- Handphone Section -->
      <div class="container mx-auto px-4 pt-16 pb-8 border-t border-gray-200">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
          <div>
            <h2 class="text-2xl font-bold">Handphone</h2>
            <p class="text-sm text-gray-600">Produk kami yang tersedia</p>
          </div>

          <div class="w-full md:w-auto max-w-md">
            <form @submit.prevent="searchProducts" class="search-form">
              <div class="flex items-center bg-blue-500 rounded-full overflow-hidden">
                <input
                  v-model="searchQuery"
                  type="text"
                  class="w-full bg-blue-500 text-white placeholder-white/80 py-3 px-6 outline-none"
                  placeholder="Cari barang yang anda inginkan...."
                >
                <button type="submit" class="bg-blue-500 text-white p-3">
                  <i class="fas fa-search text-xl"></i>
                </button>
              </div>
              <input type="hidden" name="category" value="handphone">
            </form>
          </div>
        </div>

        <!-- Products Grid -->
        <!-- Handphone Slider with Navigation -->
        <div class="relative">
          <!-- Navigation Buttons -->
          <button
            class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white border rounded-full shadow p-2 z-10 hover:bg-gray-100"
            @click="scrollSlider('left')">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button
            class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white border rounded-full shadow p-2 z-10 hover:bg-gray-100"
            @click="scrollSlider('right')">
            <i class="fas fa-chevron-right"></i>
          </button>

          <!-- Product Slider -->
          <div ref="handphoneSlider" class="overflow-x-auto hide-scrollbar scroll-smooth py-4 px-8">
            <div class="flex space-x-4 min-w-max">
              <div v-if="loading" class="text-center py-12 w-full">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                <p class="mt-4 text-gray-600">Memuat produk...</p>
              </div>

              <div v-else-if="phones.length === 0" class="text-center w-full py-8">
                <p class="text-gray-500">Tidak ada produk handphone tersedia saat ini.</p>
              </div>

              <template v-else>
                <div v-for="product in phones" :key="product.id"
                  class="w-64 flex-shrink-0 bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-sm transition duration-300 ease-in-out hover:shadow-lg">
                  <div class="bg-gray-100 w-full h-56 flex items-center justify-center p-4 relative group">
                    <img :src="product.image" :alt="product.name"
                      class="max-h-full object-contain transition transform group-hover:scale-105">
                    <span v-if="product.discount_percentage"
                      class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                      {{ product.discount_percentage }}% OFF
                    </span>
                  </div>
                  <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-semibold text-base text-gray-800 mb-2">{{ product.name }}</h3>
                    <p class="text-blue-600 font-bold text-lg">{{ formatPrice(product.price) }}</p>
                    <p v-if="product.original_price" class="text-gray-500 line-through text-sm">
                      {{ formatPrice(product.original_price) }}
                    </p>
                    <div class="flex mt-4 space-x-2">
                      <button @click="addToCart(product)"
                        class="bg-blue-100 text-blue-600 border border-blue-300 rounded-lg py-2 px-4 text-center text-sm w-full hover:bg-blue-200">
                        <i class="fas fa-cart-plus mr-1"></i> Keranjang
                      </button>
                      <router-link :to="'/product/' + product.id"
                        class="bg-blue-500 text-white rounded-lg py-2 px-8 text-sm text-center hover:bg-blue-600">
                        <i class="fas fa-shopping-bag mr-1"></i>Beli
                      </router-link>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- View All Button -->
        <div class="flex justify-center mt-8">
          <router-link to="/products?category=handphone"
            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-12 rounded-md no-underline">
            Lihat Semua Produk
          </router-link>
        </div>
      </div>

      <!-- Accessories Section -->
      <div class="container mx-auto px-4 py-8 border-t border-gray-200">
        <div class="mb-6">
          <h2 class="text-2xl font-bold">Aksesoris</h2>
          <p class="text-sm text-gray-600">Produk kami yang tersedia</p>
        </div>

        <!-- Products Grid -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-4 text-gray-600">Memuat produk...</p>
        </div>

        <div v-else-if="accessories.length === 0"
          class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-8">
          <p class="text-gray-500">Tidak ada produk aksesoris tersedia saat ini.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="product in accessories" :key="product.id"
            class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-sm transition duration-300 ease-in-out hover:shadow-lg">
            <div class="product-image-container bg-gray-100 w-full h-56 flex items-center justify-center flex-shrink-0 p-4 relative group">
              <img :src="product.image" :alt="product.name"
                class="product-image max-h-full object-contain transition duration-500 ease-in-out transform group-hover:scale-105">
            </div>
            <div class="p-4 text-blue-600 flex flex-col flex-grow">
              <h3 class="font-semibold text-base text-gray-800 flex-grow mb-2">{{ product.name }}</h3>
              <p class="text-2xl font-bold mt-1">{{ formatPrice(product.price) }}</p>
              <p v-if="product.original_price" class="text-white/70 line-through">{{ formatPrice(product.original_price) }}</p>
              <div class="flex mt-4 space-x-2">
                <button @click="addToCart(product)"
                  class="add-to-cart-btn bg-blue-100 text-blue-600 border border-blue-300 rounded-lg py-2 px-3 text-sm w-full text-center hover:bg-blue-200 transition duration-200">
                  <i class="fas fa-cart-plus mr-1"></i> Keranjang
                </button>
                <router-link :to="'/product/' + product.id"
                  class="bg-blue-500 text-white rounded-lg py-2 px-3 text-sm flex-1 text-center no-underline hover:bg-blue-600 transition duration-200">
                  <i class="fas fa-shopping-bag mr-1"></i>Beli
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- View All Button -->
        <div class="flex justify-center mt-8">
          <router-link to="/products?category=accessory"
            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 px-12 rounded-md no-underline">
            Lihat Semua Produk
          </router-link>
        </div>
      </div>
    </main>

    <FooterComponent />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineComponent } from 'vue'
import { useRouter } from 'vue-router'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

defineComponent({
  name: 'WelcomeView'
})

// Banner slider state
const banners = ref([
  {
    image: '/img/banner1.png',
    alt: 'iPhone Collection'
  },
  {
    image: '/img/banner2.png',
    alt: 'Samsung Galaxy'
  },
  {
    image: '/img/banner3.png',
    alt: 'Google Pixel'
  },
  {
    image: '/img/banner4.png',
    alt: 'Phone Accessories'
  }
])
const currentSlideIndex = ref(0)
const slideInterval = ref(null)

// Product state
const loading = ref(true)
const phones = ref([])
const accessories = ref([])
const searchQuery = ref('')
const handphoneSlider = ref(null)

// Router
const router = useRouter()

// Methods
const nextSlide = () => {
  currentSlideIndex.value = (currentSlideIndex.value + 1) % banners.value.length
}

const prevSlide = () => {
  currentSlideIndex.value = currentSlideIndex.value === 0
    ? banners.value.length - 1
    : currentSlideIndex.value - 1
}

const startSlideShow = () => {
  slideInterval.value = setInterval(nextSlide, 5000)
}

const stopSlideShow = () => {
  if (slideInterval.value) {
    clearInterval(slideInterval.value)
  }
}

const scrollSlider = (direction) => {
  if (!handphoneSlider.value) return

  const scrollAmount = 300
  const currentScroll = handphoneSlider.value.scrollLeft

  handphoneSlider.value.scrollTo({
    left: direction === 'right' ? currentScroll + scrollAmount : currentScroll - scrollAmount,
    behavior: 'smooth'
  })
}

const searchProducts = () => {
  if (!searchQuery.value.trim()) return
  router.push({
    path: '/products',
    query: {
      search: searchQuery.value,
      category: 'handphone'
    }
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}

const fetchProducts = async () => {
  try {
    loading.value = true
    const response = await fetch('/api/products')
    const result = await response.json()

    // Pastikan data adalah array
    const data = Array.isArray(result) ? result : result.data || []

    const formatProductData = (product) => ({
      ...product,
      image: product.image ?
        (product.image.startsWith('http') ? product.image : `/storage/${product.image}`) :
        '/img/placeholder.jpg',
      discount_percentage: product.original_price ?
        Math.round(((product.original_price - product.price) / product.original_price) * 100) : null
    })

    phones.value = data
      .filter(p => p?.category === 'handphone')
      .map(formatProductData)

    accessories.value = data
      .filter(p => p?.category === 'accessory')
      .map(formatProductData)
  } catch (error) {
    console.error('Error fetching products:', error)
    phones.value = []
    accessories.value = []
  } finally {
    loading.value = false
  }
}

const addToCart = async (product) => {
  try {
    const response = await fetch('/api/cart/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ productId: product.id })
    })

    if (!response.ok) {
      throw new Error('Failed to add to cart')
    }

    // You might want to show a success message or update the cart count here
  } catch (error) {
    console.error('Error adding to cart:', error)
    // You might want to show an error message here
  }
}

// Lifecycle hooks
onMounted(() => {
  fetchProducts()
  startSlideShow()
})

onUnmounted(() => {
  stopSlideShow()
})
</script>

<style scoped>
/* Slider styles */
.slide-container {
  position: relative;
  overflow: hidden;
  border-radius: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.slides {
  position: relative;
  height: 100%;
  width: 100%;
}

.slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.5s ease-in-out;
}

.slide.active {
  opacity: 1;
  visibility: visible;
  z-index: 1;
}

.slide img {
  transition: transform 0.5s ease-in-out;
}

/* Wave transition */
.wave-section {
  position: relative;
  width: 100%;
  height: 150px;
  margin-top: -100px;
}

.wave-svg {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Hide scrollbar */
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Text animations */
.animate-fade-in-up {
  animation: fade-in-up 0.8s cubic-bezier(0.4, 0, 0.2, 1) both;
}

.delay-200 {
  animation-delay: 200ms;
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
