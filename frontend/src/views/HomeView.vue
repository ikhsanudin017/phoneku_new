<template>
  <div>
    <!-- This component will redirect to Welcome -->
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  router.replace('/welcome')
})
</script>
