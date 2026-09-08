<script setup lang="ts">
import { Globe, Lock, ShieldAlert, Calendar, Users } from 'lucide-vue-next'
import type { Community } from '@/types'

const props = defineProps<{
  community: Community
}>()
</script>

<template>
  <div class="space-y-6">
    <!-- Community Details -->
    <div class="bg-white rounded-2xl border border-zinc-200/90 p-6 sm:p-8 shadow-2xs space-y-6">
      <div>
        <h3 class="font-serif-custom text-2xl font-normal text-black mb-3">
          Mengenai {{ community.name }}
        </h3>
        <p class="text-sm text-zinc-600 leading-relaxed font-normal">
          {{ community.description }}
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-zinc-100">
        <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
          <div class="p-2.5 rounded-lg bg-white border border-zinc-200 text-zinc-700">
            <Globe v-if="community.visibility === 'public'" :size="18" />
            <Lock v-else :size="18" />
          </div>
          <div>
            <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Akses</span>
            <span class="text-xs font-bold text-black capitalize">{{ community.visibility === 'public' ? 'Publik' : 'Privat' }}</span>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
          <div class="p-2.5 rounded-lg bg-white border border-zinc-200 text-zinc-700">
            <Users :size="18" />
          </div>
          <div>
            <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Kapasitas</span>
            <span class="text-xs font-bold text-black">
              {{ community.maxMembers ? `${community.memberCount} / ${community.maxMembers}` : `${community.memberCount} (Tanpa Batas)` }}
            </span>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
          <div class="p-2.5 rounded-lg bg-white border border-zinc-200 text-zinc-700">
            <Calendar :size="18" />
          </div>
          <div>
            <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Dibuat</span>
            <span class="text-xs font-bold text-black">{{ community.createdAt }}</span>
          </div>
        </div>
      </div>

      <!-- Guidelines -->
      <div class="pt-6 border-t border-zinc-100 space-y-3">
        <div class="flex items-center gap-2 text-black font-semibold text-xs uppercase tracking-wider">
          <ShieldAlert :size="16" class="text-zinc-600" />
          <span>Panduan & Aturan Komunitas</span>
        </div>
        <ul class="space-y-2 text-xs text-zinc-600 list-disc list-inside leading-relaxed font-normal">
          <li>Saling menghormati dan mendukung dalam kasih sesama anggota.</li>
          <li>Menjaga kerahasiaan permohonan doa yang dibagikan secara khusus.</li>
          <li>Menggunakan bahasa yang santun dan membangun dalam setiap diskusi.</li>
        </ul>
      </div>
    </div>
  </div>
</template>
