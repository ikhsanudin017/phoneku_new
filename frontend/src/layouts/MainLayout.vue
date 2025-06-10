<template>  <div class="flex flex-col min-h-screen bg-gradient-to-b from-blue-500 to-blue-400">
    <div class="w-full flex justify-center pt-6 pb-2">
      <div class="w-full max-w-5xl px-2">
        <NavbarComponent />
      </div>
    </div>
    <main class="flex-grow flex flex-col items-center">
      <div class="w-full max-w-5xl flex-grow">
        <slot></slot>
      </div>
    </main>

    <FooterComponent />
  </div>
</template>

<script setup>
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
</script>
