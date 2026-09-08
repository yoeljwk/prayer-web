<script setup lang="ts">
import { Bell, HeartHandshake, UserPlus, Info } from 'lucide-vue-next'
import type { Community } from '@/types'

const props = defineProps<{
  community: Community
}>()
</script>

<template>
  <div class="space-y-8">
    <!-- Community Overview Card -->
    <div class="bg-white rounded-2xl border border-zinc-200/90 p-6 sm:p-8 shadow-2xs space-y-4">
      <div class="flex items-center gap-2 text-black font-semibold text-sm">
        <Info :size="18" class="text-zinc-600" />
        <span>Tentang Komunitas</span>
      </div>
      <p class="text-sm text-zinc-600 leading-relaxed font-normal">
        {{ community.description }}
      </p>
      <div class="pt-4 border-t border-zinc-100 flex flex-wrap gap-6 text-xs text-zinc-500">
        <div>
          <span class="text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Tipe Komunitas</span>
          <span class="font-semibold text-black capitalize">{{ community.visibility === 'public' ? 'Publik' : 'Privat' }}</span>
        </div>
        <div>
          <span class="text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Total Anggota</span>
          <span class="font-semibold text-black">{{ community.memberCount }} Anggota</span>
        </div>
        <div>
          <span class="text-zinc-400 block text-[11px] uppercase tracking-wider font-semibold">Tanggal Dibuat</span>
          <span class="font-semibold text-black">{{ community.createdAt }}</span>
        </div>
      </div>
    </div>

    <!-- Recent Activities Feed -->
    <div class="space-y-4">
      <h3 class="font-serif-custom text-xl font-normal text-black tracking-tight">
        Aktivitas Terbaru
      </h3>

      <div v-if="community.activities.length > 0" class="space-y-3">
        <div
          v-for="act in community.activities"
          :key="act.id"
          class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-2xs flex items-start gap-4"
        >
          <div
            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
            :class="{
              'bg-zinc-100 text-zinc-800': act.type === 'announcement',
              'bg-emerald-50 text-emerald-800': act.type === 'member_joined',
              'bg-zinc-900 text-white': act.type === 'prayer_shared',
            }"
          >
            <Bell v-if="act.type === 'announcement'" :size="18" />
            <UserPlus v-else-if="act.type === 'member_joined'" :size="18" />
            <HeartHandshake v-else :size="18" />
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2 mb-1">
              <h4 v-if="act.title" class="text-sm font-bold text-black truncate">
                {{ act.title }}
              </h4>
              <span class="text-[11px] text-zinc-400 shrink-0 ml-auto">{{ act.createdAt }}</span>
            </div>
            <p class="text-xs text-zinc-600 leading-relaxed font-normal">
              {{ act.content }}
            </p>
            <p class="text-[11px] text-zinc-400 mt-2 font-medium">
              Oleh {{ act.authorName }}
            </p>
          </div>
        </div>
      </div>

      <!-- Empty State for Activities -->
      <div v-else class="bg-white rounded-2xl border border-zinc-200/80 p-8 text-center text-zinc-400 text-xs">
        Belum ada aktivitas terbaru di komunitas ini.
      </div>
    </div>
  </div>
</template>
