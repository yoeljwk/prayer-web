<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <footer class="w-full border-t border-zinc-200 bg-white py-10 mt-auto">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
      <div class="flex items-center gap-2">
        <span class="font-serif-custom text-base text-black font-medium">TwoOrThree</span>
        <span>&copy; {{ new Date().getFullYear() }} Ruang Hening Online.</span>
      </div>

      <div class="flex items-center gap-6 font-medium">
        <RouterLink to="/tentang" class="hover:text-black transition-colors">Tentang</RouterLink>
        <RouterLink to="/permohonan-doa" class="hover:text-black transition-colors">Permohonan Doa</RouterLink>
        <RouterLink to="/ruang-doa" class="hover:text-black transition-colors">Ruang Doa</RouterLink>
      </div>
    </div>
  </footer>
</template>
