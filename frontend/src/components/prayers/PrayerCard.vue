<script setup lang="ts">
import { computed } from 'vue'
import { Heart, MessageSquare, Check, Users, Lock } from 'lucide-vue-next'
import type { PrayerRequest } from '@/types'

const props = defineProps<{
  prayer: PrayerRequest
}>()

const emit = defineEmits<{
  (e: 'toggle-pray', id: string): void
  (e: 'open-detail', id: string): void
}>()

const isAnswered = computed(() => props.prayer.status === 'answered')

const handlePrayClick = (e: MouseEvent) => {
  e.stopPropagation()
  emit('toggle-pray', props.prayer.id)
}
</script>

<template>
  <article
    @click="emit('open-detail', prayer.id)"
    class="group bg-white rounded-2xl p-6 sm:p-7 border border-zinc-200/90 hover:border-zinc-300 shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col justify-between"
    :class="{ 'bg-emerald-50/20 border-emerald-200/80 hover:border-emerald-300': isAnswered }"
  >
    <div>
      <!-- Top Row: Pill Badge (Dari: ...) & Status Badge -->
      <div class="flex items-center justify-between gap-2 mb-4">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-zinc-100/90 text-xs font-medium text-zinc-700">
          <span class="text-zinc-400 font-normal">Dari:</span>
          <span class="font-semibold text-zinc-900">{{ prayer.isAnonymous ? 'Anonim' : prayer.authorName }}</span>
        </span>

        <span
          v-if="isAnswered"
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800"
        >
          <Check :size="13" class="stroke-[2.5]" />
          <span>Doa Terjawab</span>
        </span>

        <span
          v-else-if="prayer.visibility === 'group' && prayer.groupName"
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600"
        >
          <Users :size="12" />
          <span class="truncate max-w-[120px]">{{ prayer.groupName }}</span>
        </span>

        <span
          v-else-if="prayer.visibility === 'private'"
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600"
        >
          <Lock :size="12" />
          <span>Pribadi</span>
        </span>
      </div>

      <!-- Prayer Message Text -->
      <p class="text-sm sm:text-base text-zinc-800 leading-relaxed font-normal my-3 whitespace-pre-line line-clamp-4">
        {{ prayer.content }}
      </p>
    </div>

    <!-- Bottom Actions Row (Ultra-minimal integrated footer) -->
    <div class="flex items-center justify-between gap-3 mt-6 pt-4 border-t border-zinc-100/80">
      <div class="flex items-center gap-2.5 text-xs text-zinc-400 font-normal">
        <span>{{ prayer.createdAt }}</span>
        <span>•</span>
        <div class="flex items-center gap-1 text-zinc-500 hover:text-black transition-colors" title="Komentar">
          <MessageSquare :size="14" />
          <span class="font-medium text-xs">{{ prayer.comments.length }}</span>
        </div>
      </div>

      <!-- Mendoakan Button -->
      <button
        type="button"
        @click="handlePrayClick"
        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer select-none"
        :class="[
          prayer.hasPrayed
            ? 'bg-black text-white shadow-xs'
            : 'bg-zinc-100 hover:bg-zinc-200/80 text-zinc-800'
        ]"
      >
        <Heart
          :size="13"
          :class="prayer.hasPrayed ? 'fill-white stroke-white' : 'stroke-zinc-600'"
        />
        <span>{{ prayer.hasPrayed ? 'Didoakan' : 'Mendoakan' }}</span>
        <span
          class="text-[11px] px-1.5 py-0.2 rounded-md ml-0.5"
          :class="prayer.hasPrayed ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-700'"
        >
          {{ prayer.prayerCount }}
        </span>
      </button>
    </div>
  </article>
</template>
