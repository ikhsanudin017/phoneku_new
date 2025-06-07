<template>
  <footer class="bg-white pt-10 pb-6">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Company Info -->
        <div>
          <div class="flex items-center mb-4">
            <img src="/img/logo2.png" alt="PhoneKu Logo" class="h-15">
          </div>
          <p class="text-gray-600 mb-4">
            Kami memiliki ponsel yang sesuai dengan gaya Anda, dan Anda akan bangga memamerkannya. Dari desain yang ramping hingga performa yang tangguh.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-blue-500">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-blue-500">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-blue-500">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-blue-500">
              <i class="fab fa-tiktok"></i>
            </a>
          </div>
        </div>

        <!-- Company Links -->
        <div>
          <h3 class="text-lg font-bold mb-4 uppercase">Perusahaan</h3>
          <ul class="space-y-2">
            <li><router-link to="/about" class="text-gray-600 hover:text-blue-500">Tentang</router-link></li>
            <li><router-link to="/kontak" class="text-gray-600 hover:text-blue-500">Kontak</router-link></li>
            <li><router-link to="/tim" class="text-gray-600 hover:text-blue-500">Tim Kami</router-link></li>
          </ul>
        </div>

        <!-- Product & Services -->
        <div>
          <h3 class="text-lg font-bold mb-4 uppercase">Produk & Layanan</h3>
          <ul class="space-y-2">
            <li><router-link to="/customer-support" class="text-gray-600 hover:text-blue-500">Customer Support</router-link></li>
          </ul>
        </div>

        <!-- Payment Methods -->
        <div>
          <h3 class="text-lg font-bold mb-4 uppercase">Pembayaran</h3>
          <div class="grid grid-cols-3 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-center">
              <img src="/img/dana.png" alt="Dana" class="h-8 object-contain">
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-center">
              <img src="/img/shopeepay.png" alt="ShopeePay" class="h-8 object-contain">
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-center">
              <img src="/img/paypal.png" alt="Paypal" class="h-8 object-contain">
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex items-center justify-center">
              <img src="/img/bri.png" alt="BRI" class="h-8 object-contain">
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-200 mt-10 pt-6">
        <p class="text-gray-500 text-sm text-center">
          Phone.Ku © {{ currentYear }}-Present All Rights Reserved
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
a {
  transition: all 0.2s ease;
}

.bg-white.rounded-xl {
  transition: all 0.2s ease;
}

.bg-white.rounded-xl:hover {
  transform: scale(1.05);
}
</style>
