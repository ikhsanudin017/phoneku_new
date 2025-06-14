<template>
  <div class="flex flex-col min-h-screen">
    <header class="relative z-50">
      <NavbarComponent />
    </header>

    <main class="flex-grow">
      <slot></slot>
    </main>

    <footer class="relative z-40">
      <FooterComponent />
    </footer>
  </div>
</template>

<script setup>
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
</script>

<style>
/* Ensure links are clickable */
a, button {
  position: relative;
  z-index: 1;
  cursor: pointer;
}

/* Fix pointer events */
.pointer-events-auto {
  pointer-events: auto !important;
}

/* Ensure elements are interactive */
[role="button"],
[type="button"],
button,
a {
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}
</style>
