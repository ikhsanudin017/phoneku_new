<script setup>
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  authStore.initializeAuth()
})
</script>

<template>
  <RouterView />
</template>

<style>
/* Reset & Base Styles */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

:root {
  --color-primary: #3b82f6;
  --color-primary-dark: #1d4ed8;
  --color-gray-50: #f8fafc;
  --color-gray-100: #f1f5f9;
  --color-gray-800: #1e293b;
  --color-gray-900: #0f172a;
}

html {
  font-size: 16px;
  scroll-behavior: smooth;
}

body {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  line-height: 1.6;
  color: var(--color-gray-800);
  background-color: var(--color-gray-50);
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
  font-weight: 600;
  line-height: 1.2;
  color: var(--color-gray-900);
}

a {
  color: var(--color-primary);
  text-decoration: none;
  transition: color 0.2s ease;
}

a:hover {
  color: var(--color-primary-dark);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--color-gray-100);
}

::-webkit-scrollbar-thumb {
  background: #3B82F6;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #2563EB;
  }

/* Remove default link styles */
a {
  text-decoration: none;
  color: inherit;
  }

/* Button reset */
button {
  border: none;
  background: none;
  cursor: pointer;
  outline: none;
  }

/* Input reset */
input {
  outline: none;
  border: none;
}

/* Remove list styling */
ul, li {
  list-style: none;
}

header, nav, .admin-header, .admin-navbar {
  pointer-events: auto !important;
  z-index: 100;
}
</style>
