<script setup lang="ts">
import { ref } from 'vue'
import { Plus, HeartHandshake } from 'lucide-vue-next'
import type { Community } from '@/types'
import PrayerCard from '@/components/prayers/PrayerCard.vue'

const props = defineProps<{
  community: Community
}>()

const emit = defineEmits<{
  (e: 'open-share'): void
  (e: 'open-detail', id: string): void
}>()

const handleTogglePray = (id: string) => {
  const item = props.community.prayers.find((p) => p.id === id)
  if (item) {
    if (item.hasPrayed) {
      item.hasPrayed = false
      item.prayerCount = Math.max(0, item.prayerCount - 1)
    } else {
      item.hasPrayed = true
      item.prayerCount += 1
    }
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header Action Bar -->
    <div class="flex items-center justify-between gap-4">
      <div>
        <h3 class="font-serif-custom text-xl font-normal text-black tracking-tight">
          Permohonan Doa Komunitas
        </h3>
        <p class="text-xs text-zinc-500">
          Permohonan doa yang khusus dibagikan dalam {{ community.name }}.
        </p>
      </div>

      <button
        type="button"
        @click="emit('open-share')"
        class="inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-2xs transition-colors cursor-pointer shrink-0"
      >
        <Plus :size="15" />
        <span>Bagikan Doa</span>
      </button>
    </div>

    <!-- Prayers Feed -->
    <div v-if="community.prayers.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <PrayerCard
        v-for="prayer in community.prayers"
        :key="prayer.id"
        :prayer="prayer"
        @toggle-pray="handleTogglePray"
        @open-detail="(id) => emit('open-detail', id)"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-2xl border border-zinc-200/90 p-12 text-center space-y-3">
      <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
        <HeartHandshake :size="22" />
      </div>
      <h4 class="font-serif-custom text-lg font-normal text-black">
        Belum Ada Permohonan Doa
      </h4>
      <p class="text-xs text-zinc-500 max-w-sm mx-auto">
        Jadilah yang pertama membagikan pesan doa untuk didoakan bersama anggota {{ community.name }}.
      </p>
      <button
        type="button"
        @click="emit('open-share')"
        class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
      >
        Tulis Permohonan Doa
      </button>
    </div>
  </div>
</template>
