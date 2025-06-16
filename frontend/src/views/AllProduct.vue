<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <NavbarComponent />

    <div class="flex-grow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">All Products</h1>

          <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search products..."
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                @keyup.enter="applyFilters"
              />
            </div>

            <select
              v-model="selectedCategory"
              class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyFilters"
            >
              <option value="">All Categories</option>
              <option value="handphone">Smartphones</option>
              <option value="accessory">Accessories</option>
            </select>

            <select
              v-model="sortBy"
              class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyFilters"
            >
              <option value="created_at">Newest</option>
              <option value="name">Name A-Z</option>
              <option value="price">Price Low-High</option>
              <option value="price_desc">Price High-Low</option>
            </select>
          </div>
        </div>

        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
          <p class="mt-4 text-gray-600">Loading products...</p>
        </div>

        <div v-else>
          <div v-if="selectedCategory !== 'accessory'" class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Handphone</h2>
            <div v-if="phones.length > 0" class="relative">
              <button
                class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white border rounded-full shadow p-2 z-10 hover:bg-gray-100"
                @click="scrollSlider('left')"
              >
                <i class="fas fa-chevron-left"></i>
              </button>
              <button
                class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white border rounded-full shadow p-2 z-10 hover:bg-gray-100"
                @click="scrollSlider('right')"
              >
                <i class="fas fa-chevron-right"></i>
              </button>

              <div ref="handphoneSlider" class="overflow-x-auto hide-scrollbar scroll-smooth py-4 px-8">
                <div class="flex space-x-4 min-w-max">
                    <div v-for="product in phones" :key="product.id"
                      class="w-64 flex-shrink-0 bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-sm transition duration-300 ease-in-out hover:shadow-lg">
                      <router-link :to="`/product/${product.id}`" class="flex flex-col h-full">
                        <div class="bg-gray-100 w-full h-56 flex items-center justify-center p-4 relative group">
                          <img :src="getImageUrl(product.image)" :alt="product.name"
                            class="max-h-full object-contain transition transform group-hover:scale-105">
                        </div>
                        <div class="p-4 flex flex-col flex-grow">
                          <h3 class="font-semibold text-base text-gray-800 mb-2 line-clamp-2">{{ product.name }}</h3>
                          <p class="text-blue-600 font-bold text-lg">Rp {{ formatPrice(product.price) }}</p>
                          <p v-if="product.original_price" class="text-gray-500 line-through text-sm">
                            Rp {{ formatPrice(product.original_price) }}
                          </p>
                           <div class="flex items-center justify-between mt-auto pt-2">
                             <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
                           </div>
                        </div>
                      </router-link>
                    </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-500">No handphone products found for the current filter.</p>
            </div>
          </div>

          <div v-if="selectedCategory !== 'handphone'" class="py-8 border-t border-gray-200">
             <h2 class="text-2xl font-bold mb-6">Aksesoris</h2>
             <div v-if="accessories.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="product in accessories" :key="product.id"
                  class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col shadow-sm transition duration-300 ease-in-out hover:shadow-lg">
                  <router-link :to="`/product/${product.id}`" class="flex flex-col h-full">
                    <div class="product-image-container bg-gray-100 w-full h-56 flex items-center justify-center flex-shrink-0 p-4 relative group">
                      <img :src="getImageUrl(product.image)" :alt="product.name"
                        class="product-image max-h-full object-contain transition duration-500 ease-in-out transform group-hover:scale-105">
                    </div>
                    <div class="p-4 text-blue-600 flex flex-col flex-grow">
                      <h3 class="font-semibold text-base text-gray-800 flex-grow mb-2 line-clamp-2">{{ product.name }}</h3>
                      <p class="text-2xl font-bold mt-1">Rp {{ formatPrice(product.price) }}</p>
                       <div class="flex items-center justify-between mt-auto pt-2">
                         <span class="text-sm text-gray-500">Stock: {{ product.stock }}</span>
                       </div>
                    </div>
                  </router-link>
                </div>
            </div>
            <div v-else class="text-center py-8">
              <p class="text-gray-500">No accessory products found for the current filter.</p>
            </div>
          </div>

           <div v-if="!loading && phones.length === 0 && accessories.length === 0" class="text-center py-16">
            <p class="text-xl text-gray-600">No products found matching your criteria.</p>
          </div>

        </div>
      </div>
    </div>

    <FooterComponent />
  </div>
</template>

<script setup>
// Diasumsikan `productsAPI.getAll` bisa menerima parameter dan pada akhirnya menggunakan `fetch`.
// Jika tidak, ganti `productsAPI.getAll(params)` dengan `fetch(...)` secara langsung.
import { productsAPI } from '@/services/api'; 
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import NavbarComponent from '@/components/NavbarComponent.vue';
import FooterComponent from '@/components/FooterComponent.vue';

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const phones = ref([]);
const accessories = ref([]);
const searchQuery = ref('');
const selectedCategory = ref('');
const sortBy = ref('created_at');
const handphoneSlider = ref(null);

const fetchProducts = async () => {
  loading.value = true;
  phones.value = [];
  accessories.value = [];

  try {
    // Bangun URL dengan query parameters untuk filtering
    // Ini adalah cara yang lebih baik daripada memfilter di client-side dengan data besar
    const params = new URLSearchParams();
    params.append('search', searchQuery.value);
    params.append('category', selectedCategory.value);
    
    const sortVal = sortBy.value;
    const sortOrder = sortVal.includes('_desc') ? 'desc' : 'asc';
    const sortByField = sortVal.replace('_desc', '');
    params.append('sort_by', sortByField);
    params.append('sort_order', sortOrder);

    // Ganti '/api/products' dengan endpoint Anda yang sebenarnya
    const response = await fetch(`/api/products/`);
    const result = await response.json();
    
    const data = Array.isArray(result) ? result : result.data || [];

    // Fungsi format data yang sama dari WelcomeView
    const formatProductData = (product) => ({
      ...product,
      image: getImageUrl(product.image),
      discount_percentage: product.original_price
        ? Math.round(((product.original_price - product.price) / product.original_price) * 100)
        : null,
    });

    // Pisahkan data berdasarkan kategori setelah difilter dari server
    phones.value = data.filter(p => p.category === 'handphone').map(formatProductData);
    accessories.value = data.filter(p => p.category === 'accessory').map(formatProductData);

  } catch (error) {
    console.error('Error fetching products:', error);
    phones.value = [];
    accessories.value = [];
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  const query = {};
  if (searchQuery.value) query.search = searchQuery.value;
  if (selectedCategory.value) query.category = selectedCategory.value;
  if (sortBy.value !== 'created_at') query.sort = sortBy.value;
  
  // Mengganti query di URL tanpa reload halaman, lalu `watch` akan menangkapnya
  router.push({ query });
};

const scrollSlider = (direction) => {
  if (!handphoneSlider.value) return;
  const scrollAmount = 300;
  const currentScroll = handphoneSlider.value.scrollLeft;
  handphoneSlider.value.scrollTo({
    left: direction === 'right' ? currentScroll + scrollAmount : currentScroll - scrollAmount,
    behavior: 'smooth',
  });
};

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/img/placeholder.jpg';
  if (imagePath.startsWith('http')) return imagePath;
  // Sesuaikan dengan URL base backend Anda jika diperlukan
  return `http://localhost:8000/storage/${imagePath}`;
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price);
};

onMounted(() => {
  // Ambil state filter dari URL saat pertama kali load
  selectedCategory.value = route.query.category || '';
  searchQuery.value = route.query.search || '';
  sortBy.value = route.query.sort || 'created_at';
  fetchProducts();
});

// Awasi perubahan pada query URL. Jika berubah, fetch ulang data.
watch(() => route.query, (newQuery, oldQuery) => {
  if (JSON.stringify(newQuery) !== JSON.stringify(oldQuery)) {
    selectedCategory.value = newQuery.category || '';
    searchQuery.value = newQuery.search || '';
    sortBy.value = newQuery.sort || 'created_at';
    fetchProducts();
  }
}, { deep: true });
</script>

<style scoped>
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
</style>