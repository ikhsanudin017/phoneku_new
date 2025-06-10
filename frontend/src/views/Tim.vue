<template>
  <div class="min-h-screen flex flex-col">
    <NavbarComponent class="z-10" />

    <!-- Header Section with Wave -->
    <div class="flex-1">
      <div class="bg-blue-500">
        <section class="container mx-auto px-4 pt-16 pb-16">
          <div class="text-center">
            <h1 class="text-5xl font-bold text-white mb-4">Tim Kami</h1>
            <p class="text-xl text-white opacity-90 mb-12">Orang-orang hebat di balik PhoneKu</p>
          </div>

          <div class="flex flex-wrap justify-center gap-8">
            <!-- Muhammad Yusuf Nurwahid -->
            <div class="relative bg-white rounded-[212px] w-48 pt-10 text-center py-12 px-3 shadow-lg lg:mt-40 flex flex-col justify-between min-h-[460px]">
              <h2 class="text-xl font-bold text-gray-800">Muhammad Yusuf Nurwahid</h2>
              <div class="flex flex-col items-center gap-1">
                <p class="text-sm text-gray-700 font-medium">Frontend Developer</p>
                <p class="text-sm text-gray-700 font-medium">Backend Developer</p>
              </div>
              <div class="w-full h-48 rounded-[212px] overflow-hidden mb-4">
                <img src="/img/yusuf.jpg" alt="Muhammad Yusuf Nurwahid" class="w-full h-full object-cover">
              </div>
              <p class="text-sm text-gray-600">
                <i class="fa-solid fa-quote-left"></i>
                Kode bukan sekadar instruksi untuk mesin,tapi juga catatan diam dari pikiran manusia yang ingin menciptakan solusi.Setiap kurung yang dibuka harus ditutup.Setiap bug yang muncul adalah tantangan untuk ditaklukkan, bukan musuh yang harus ditakuti.
                <i class="fa-solid fa-quote-right"></i>
              </p>
              <div class="flex justify-center gap-4 mt-4 text-gray-600 text-xl">
                <a href="https://github.com/yusufwahid" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/wahidd_y?igsh=MWhjcWR1eHY3OXo5ag==" target="_blank"><i class="fab fa-instagram"></i></a>
              </div>
            </div>

            <!-- Nandana Zada Abiproya -->
            <div class="relative bg-white rounded-[212px] w-48 pt-10 text-center py-12 px-3 shadow-lg lg:mt-0 lg:mb-40 flex flex-col justify-between min-h-[460px]">
              <h2 class="text-xl font-bold text-gray-800">Nandana Zada Abiproya</h2>
              <div class="flex flex-col items-center gap-1">
                <p class="text-sm text-gray-700 font-medium">Frontend Developer</p>
                <p class="text-sm text-gray-700 font-medium">Backend Developer</p>
              </div>
              <div class="w-full h-48 rounded-[212px] overflow-hidden mb-4">
                <img src="/img/zada.jpg" alt="Nandana Zada Abiproya" class="w-full h-full object-cover">
              </div>
              <p class="text-sm text-gray-600">
                <i class="fa-solid fa-quote-left"></i>
                Petir bukan sembarang petir.Ada yang mau rumahhnya disambar petir?
                <i class="fa-solid fa-quote-right"></i>
              </p>
              <div class="flex justify-center gap-4 mt-4 text-gray-600 text-xl">
                <a href="https://github.com/nandanazadaa" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/zada.abiproya/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
              </div>
            </div>

            <!-- Muhammad Zayga Ernesto -->
            <div class="relative bg-white rounded-[212px] w-48 pt-10 text-center py-12 px-3 shadow-lg lg:mt-40 flex flex-col justify-between min-h-[460px]">
              <h2 class="text-xl font-bold text-gray-800">Muhammad Zayga Ernesto</h2>
              <div class="flex flex-col items-center gap-1">
                <p class="text-sm text-gray-700 font-medium">Frontend Developer</p>
                <p class="text-sm text-gray-700 font-medium">Backend Developer</p>
              </div>
              <div class="w-full h-48 rounded-[212px] overflow-hidden mb-4">
                <img src="/img/zayga.jpg" alt="Muhammad Zayga Ernesto" class="w-full h-full object-cover">
              </div>
              <p class="text-sm text-gray-600">
                <i class="fa-solid fa-quote-left"></i>
                Belajarku bukan dari buku besar, tapi dari error kecil yang muncul tiap malam. Aku anak hasil eksperimen: antara niat, koneksi Wi-Fi, dan tutorial berdurasi 2x speed.
                <i class="fa-solid fa-quote-right"></i>
              </p>
              <div class="flex justify-center gap-4 mt-4 text-gray-600 text-xl">
                <a href="https://github.com/ZaygaErnesto" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/zaygaernesto/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
              </div>
            </div>

            <!-- Ikhsanudin -->
            <div class="relative bg-white rounded-[212px] w-48 pt-10 text-center py-12 px-3 shadow-lg lg:mt-0 lg:mb-40 flex flex-col justify-between min-h-[460px]">
              <h2 class="text-xl font-bold text-gray-800">Ikhsanudin</h2>
              <div class="flex flex-col items-center gap-1">
                <p class="text-sm text-gray-700 font-medium">Frontend Developer</p>
                <p class="text-sm text-gray-700 font-medium">Backend Developer</p>
              </div>
              <div class="w-full h-48 rounded-[212px] overflow-hidden mb-4">
                <img src="/img/ikhsan.jpg" alt="Ikhsanudin" class="w-full h-full object-cover">
              </div>
              <p class="text-sm text-gray-600">
                <i class="fa-solid fa-quote-left"></i>
                Jadilah Orang Pengembang sistem,Kalau tidak mau tergerus Oleh Sistem.
                <i class="fa-solid fa-quote-right"></i>
              </p>
              <div class="flex justify-center gap-4 mt-4 text-gray-600 text-xl">
                <a href="https://github.com/ikhsanudin017" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/i_can017/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
              </div>
            </div>

            <!-- Akbar Rizqy Effendi -->
            <div class="relative bg-white rounded-[212px] w-48 pt-10 text-center py-12 px-3 shadow-lg lg:mt-40 flex flex-col justify-between min-h-[460px]">
              <h2 class="text-xl font-bold text-gray-800">Akbar Rizqy Effendi</h2>
              <div class="flex flex-col items-center gap-1">
                <p class="text-sm text-gray-700 font-medium">Frontend Developer</p>
                <p class="text-sm text-gray-700 font-medium">Backend Developer</p>
              </div>
              <div class="w-full h-48 rounded-[212px] overflow-hidden mb-4">
                <img src="/img/akbar.jpg" alt="Akbar Rizqy Effendi" class="w-full h-full object-cover">
              </div>
              <p class="text-sm text-gray-600">
                <i class="fa-solid fa-quote-left"></i>
                Menjadi programmer bukan sekadar menulis baris kode. Ini tentang memecahkan masalah, gagal berkali-kali, lalu bangkit lagi dengan solusi yang lebih baik.
                <i class="fa-solid fa-quote-right"></i>
              </p>
              <div class="flex justify-center gap-4 mt-4 text-gray-600 text-xl">
                <a href="https://github.com/RizqyAkbar" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/rizqyme/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
              </div>
            </div>
          </div>
        </section>

        <div class="wave-section">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="wave-svg" preserveAspectRatio="none">
            <path fill="#ffffff" fill-opacity="1"
              d="M0,160L80,138.7C160,117,320,75,480,80C640,85,800,139,960,149.3C1120,160,1280,128,1360,112L1440,96L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z">
            </path>
          </svg>
        </div>
      </div>
    </div>

    <FooterComponent />
  </div>
</template>

<script setup>
import { defineComponent } from 'vue'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

defineComponent({
  name: 'TimView'
})
</script>

<style scoped>
.wave-section {
  position: relative;
  margin-bottom: -1px; /* Remove any gap between wave and next section */
}

.wave-svg {
  width: 100%;
  height: 80px;
  display: block; /* Remove inline element gaps */
}

/* Fix for extra spaces in quotes */
.text-sm.text-gray-600 {
  margin-bottom: 0;
}

/* Remove extra closing tags */
p:empty {
  display: none;
}
</style>
