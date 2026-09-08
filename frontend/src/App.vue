<script setup lang="ts">
import Navbar from '@/components/layout/Navbar.vue'
import Footer from '@/components/layout/Footer.vue'
</script>

<template>
  <div class="min-h-screen bg-white text-black flex flex-col font-sans-custom selection:bg-black selection:text-white">
    <Navbar />
    <main class="flex-1 flex flex-col">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>