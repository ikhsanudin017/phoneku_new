<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavbarComponent />

    <!-- Header Section with Wave -->
    <div class="relative bg-blue-500">
      <!-- Banner Container -->
      <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl overflow-hidden" id="banner-slider">
          <div class="slide-container">
            <div class="slides">
              <div v-for="(banner, index) in banners"
                :key="index"
                :class="['slide', currentSlideIndex === index ? 'active' : '']">
                <img :src="banner.image" :alt="banner.title" class="w-full h-auto object-cover">
              </div>
            </div>
          </div>

          <!-- Banner Navigation Dots -->
          <div class="flex justify-center space-x-2 py-4">
            <button
              v-for="(_, index) in banners"
              :key="index"
              :class="['w-4 h-4 rounded-full', currentSlideIndex === index ? 'bg-blue-500' : 'bg-gray-300']"
              @click="currentSlideIndex = index">
            </button>
          </div>
        </div>
      </div>

      <!-- Wave Transition -->
      <div class="wave-section">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="wave-svg" preserveAspectRatio="none">
          <path fill="#ffffff" fill-opacity="1"
            d="M0,160L80,138.7C160,117,320,75,480,80C640,85,800,139,960,149.3C1120,160,1280,128,1360,112L1440,96L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z">
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
    title: 'PhoneKu Banner 1'
  },
  {
    image: '/img/banner2.png',
    title: 'PhoneKu Banner 2'
  },
  {
    image: '/img/banner3.png',
    title: 'PhoneKu Banner 3'
  },
  {
    image: '/img/banner4.png',
    title: 'PhoneKu Banner 4'
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
const startSlideShow = () => {
  slideInterval.value = setInterval(() => {
    currentSlideIndex.value = (currentSlideIndex.value + 1) % banners.value.length
  }, 5000)
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
    const data = await response.json()

    phones.value = data.filter(p => p.category === 'handphone').map(product => ({
      ...product,
      discount_percentage: product.original_price ?
        Math.round(((product.original_price - product.price) / product.original_price) * 100) : null
    }))
    accessories.value = data.filter(p => p.category === 'accessory')
  } catch (error) {
    console.error('Error fetching products:', error)
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
/* Hide scrollbar */
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Wave section */
.wave-section {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.wave-svg {
  width: 100%;
  height: 80px;
  transform: translateY(1px);
}

/* Slider */
.slide-container {
  position: relative;
  overflow: hidden;
}

.slides {
  position: relative;
  height: 380px;
}

.slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 0.5s ease-in-out;
}

.slide.active {
  opacity: 1;
}

/* Product Card Transitions */
.product-image {
  transition: transform 0.3s ease-in-out;
}

.product-image-container:hover .product-image {
  transform: scale(1.05);
}
</style>
