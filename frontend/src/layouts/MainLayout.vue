<template>  <div class="flex flex-col min-h-screen bg-gray-50">
    <header class="sticky top-0 z-50">
      <NavbarComponent />
    </header>

    <main class="flex-grow">
      <slot></slot>
    </main>

    <FooterComponent />
  </div>
</template>

<script setup>
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
</script>
