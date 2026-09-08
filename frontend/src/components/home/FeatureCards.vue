<script setup lang="ts">
import { RouterLink } from 'vue-router'
import type { FeatureCardItem } from '@/types'

const cardItems: FeatureCardItem[] = [
  {
    id: 'share',
    title: 'Bagikan Doamu',
    description: 'Sampaikan apa yang ada di dalam hatimu kepada komunitas. Tulis dengan jujur, sederhana, dan pilih untuk membagikannya secara publik atau anonim.',
    link: '/permohonan-doa',
  },
  {
    id: 'together',
    title: 'Berdoa Bersama',
    description: 'Masuk ke dalam ruang doa terbuka dan hadir dalam keheningan bersama sesama percaya dari berbagai belahan waktu.',
    link: '/ruang-doa',
  },
  {
    id: 'connected',
    title: 'Tetap Terhubung',
    description: 'Bangun ritme doa harian yang tenang, dan dapatkan notifikasi lembut saat seseorang menaikkan doamu ke atas.',
    link: '/komunitas',
  },
]
</script>

<template>
  <section class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 pb-8">
    <RouterLink 
      v-for="card in cardItems"
      :key="card.id"
      :to="card.link" 
      class="bg-white rounded-lg p-7 border border-zinc-200 hover:border-black transition-all duration-200 cursor-pointer block"
    >
      <h2 class="font-sans-custom font-bold text-lg text-black mb-3 tracking-tight">
        {{ card.title }}
      </h2>
      <p class="text-sm text-zinc-500 leading-relaxed font-normal">
        {{ card.description }}
      </p>
    </RouterLink>
  </section>
</template>
