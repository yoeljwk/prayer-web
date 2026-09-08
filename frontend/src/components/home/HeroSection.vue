<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <section class="text-center max-w-4xl mx-auto pt-4 pb-16">

    <!-- Scripture Quote Headline (2 Lines) -->
    <h1 class="font-serif-custom text-2xl sm:text-3xl md:text-4xl lg:text-[40px] italic text-black font-normal leading-snug sm:leading-normal tracking-tight mb-6 max-w-4xl mx-auto">
      <span class="block">“sebab di mana dua atau tiga orang berkumpul dalam Nama-Ku,</span>
      <span class="block">di situ Aku ada di tengah-tengah mereka.”</span>
    </h1>

    <!-- Scripture Reference -->
    <p class="font-serif-custom text-xs sm:text-sm tracking-[0.25em] text-zinc-400 uppercase mb-8 font-medium">
      — MATIUS 18:20
    </p>

    <!-- Sub-headline description -->
    <p class="text-base sm:text-lg text-zinc-600 max-w-xl mx-auto leading-relaxed mb-10 font-normal">
      Tempat yang tenang untuk berbagi beban, berdoa bersama, dan saling mengingatkan bahwa tidak ada doa yang ditanggung sendirian.
    </p>

    <!-- Dual CTA Buttons -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <RouterLink 
        to="/permohonan-doa" 
        class="w-full sm:w-auto bg-black hover:bg-zinc-800 text-white px-7 py-3.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-3 shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer"
      >
        <span>Bagikan Doa</span>
      </RouterLink>
      
      <RouterLink 
        to="/ruang-doa" 
        class="w-full sm:w-auto bg-white hover:bg-zinc-50 text-black border border-zinc-300 px-7 py-3.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-3 shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer"
      >
        <span>Masuk Ruang Doa</span>
      </RouterLink>
    </div>
  </section>
</template>
