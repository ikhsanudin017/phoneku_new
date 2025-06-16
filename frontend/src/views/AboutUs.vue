<template>
  <div class="font-sans flex flex-col min-h-screen">
    <!-- Include Navbar (same as Welcome) -->
    <NavbarComponent class="z-10" />

    <!-- Header Section with Banner -->
    <div class="flex-1">
      <div class="bg-blue-600 pb-24 md:pb-36 flex flex-col items-center justify-center">
        <h1 class="text-4xl md:text-5xl text-white font-bold text-center mt-12 md:mt-24 mb-4 drop-shadow-lg">Tentang Kami</h1>
      </div>

      <!-- About us Content -->
      <div class="bg-white">
      <div class="container mx-auto px-4 md:px-8 lg:px-16 xl:px-32 py-12 md:py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div class="px-4 md:px-8 lg:px-12 xl:px-24 text-justify space-y-4">
            <h6 class="text-lg md:text-xl text-blue-600 font-semibold mb-1">Pusat Belanja</h6>
            <h2 class="font-bold text-3xl md:text-4xl mb-3">Handphone</h2>
            <p class="text-base md:text-lg leading-relaxed">Selamat datang di Phoneku! Kami adalah sebuah toko yang berfokus pada penjualan handphone berkualitas tinggi.</p>
            <p class="text-base md:text-lg leading-relaxed">Berdiri sejak 2025, kami telah melayani ribuan pelanggan yang mencari produk berkualitas dan pelayanan terbaik.</p>
          </div>
          <div class="flex justify-center md:justify-start">
            <img src="/img/dokumentasi.png" alt="Team Working" class="w-full max-w-md rounded-xl shadow-lg border border-gray-200 object-cover">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-16">
          <div class="px-4 md:px-8 lg:px-12 xl:px-24 text-justify">
            <h5 class="font-bold text-2xl mb-3 text-blue-600">Visi Kami</h5>
            <p class="text-base md:text-lg leading-relaxed">Menjadi penyedia gadget terbaik yang dapat memenuhi kebutuhan digital Anda dengan memberikan produk berkualitas, harga kompetitif, dan layanan pelanggan yang memuaskan.</p>
          </div>
          <div class="px-4 md:px-8 lg:pr-12 xl:pr-24">
            <h5 class="font-bold text-2xl mb-3 text-blue-600">Misi Kami</h5>
            <ul class="list-disc list-inside text-base md:text-lg space-y-2 pl-4">
              <li>Menyediakan berbagai pilihan HP terbaru dari berbagai merek terkenal dengan kualitas terjamin.</li>
              <li>Memberikan layanan purna jual yang responsif dan membantu pelanggan dalam setiap kebutuhan mereka.</li>
              <li>Memberikan harga yang terjangkau dengan kualitas terbaik.</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Sejarah -->
      <div class="bg-gray-100 pt-12 pb-16 w-full mt-0 md:mt-8">
        <div class="container mx-auto px-4 md:px-8 lg:px-16 xl:px-32 2xl:px-64">
          <h4 class="font-bold text-2xl md:text-3xl text-blue-700 mb-2">Sejarah</h4>
          <p class="text-base md:text-lg text-gray-600 mb-4">2025 - Sekarang</p>
          <div class="space-y-4 mt-2">
            <p class="text-lg md:text-xl leading-relaxed">Phoneku didirikan pada awal tahun 2025 oleh sekelompok profesional muda yang memiliki semangat tinggi dalam dunia teknologi dan komunikasi. Berawal dari sebuah toko kecil di pusat kota, Phoneku mulai dikenal karena komitmennya dalam menyediakan handphone berkualitas dengan harga yang bersaing.</p>
            <p class="text-lg md:text-xl leading-relaxed">Hingga kini, Phoneku terus berkembang dan berinovasi dalam memberikan pelayanan terbaik, dengan fokus pada kepuasan pelanggan dan kepercayaan yang telah dibangun sejak awal berdiri.</p>
          </div>
        </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <FooterComponent />
  </div>
</template>

<script setup>
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
</script>
