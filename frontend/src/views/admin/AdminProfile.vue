<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-white">
    <AdminHeader />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-950 rounded-lg relative overflow-hidden mb-8">
        <div class="absolute inset-0 bg-grid-white/10"></div>
        <div class="relative py-5 px-8 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-white pb-2 text-shadow-glow">Admin Profile</h2>
            <h5 class="text-blue-200">View and update your profile information</h5>
          </div>
        </div>
      </div>
      <!-- Profile Card -->
      <div class="backdrop-blur-lg bg-white/80 rounded-2xl p-6 shadow-2xl">
        <p class="text-gray-700">This is a dummy profile page. Implement your profile details here.</p>
      </div>
    </div>
  </div>
</template>
<script setup>
import AdminHeader from '@/components/admin/AdminHeader.vue'
</script>
<style scoped>
.bg-grid-white {
  background-image: linear-gradient(white 1px, transparent 1px),
    linear-gradient(to right, white 1px, transparent 1px);
}
.text-shadow-glow {
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.7), 0 0 10px rgba(255, 255, 255, 0.5);
}
</style>
