<script setup lang="ts">
import { Sliders, Heart, MessageSquare, Users, Radio } from 'lucide-vue-next'
import { mockNotificationPreferences } from '@/data/mockNotifications'
</script>

<template>
  <div class="bg-white rounded-3xl border border-zinc-200/90 p-6 sm:p-8 shadow-2xs space-y-6">
    <div class="flex items-center gap-2.5">
      <div class="w-9 h-9 rounded-xl bg-zinc-100 border border-zinc-200 text-zinc-800 flex items-center justify-center shrink-0">
        <Sliders :size="18" />
      </div>
      <div>
        <h3 class="font-serif-custom text-xl font-normal text-black tracking-tight">
          Preferensi Notifikasi
        </h3>
        <p class="text-xs text-zinc-500">
          Atur pemberitahuan lembut yang ingin kamu terima.
        </p>
      </div>
    </div>

    <!-- Toggle Options Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
      <!-- Toggle 1: Seseorang mendoakan doa saya -->
      <label class="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 transition-colors">
        <div class="flex items-center gap-3">
          <Heart :size="16" class="text-zinc-600 shrink-0" />
          <span class="text-xs font-semibold text-zinc-900">Seseorang mendoakan doa saya</span>
        </div>
        <input
          type="checkbox"
          v-model="mockNotificationPreferences.prayerPrayed"
          class="w-4 h-4 accent-black rounded cursor-pointer shrink-0"
        />
      </label>

      <!-- Toggle 2: Komentar baru -->
      <label class="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 transition-colors">
        <div class="flex items-center gap-3">
          <MessageSquare :size="16" class="text-zinc-600 shrink-0" />
          <span class="text-xs font-semibold text-zinc-900">Komentar baru</span>
        </div>
        <input
          type="checkbox"
          v-model="mockNotificationPreferences.newComments"
          class="w-4 h-4 accent-black rounded cursor-pointer shrink-0"
        />
      </label>

      <!-- Toggle 3: Aktivitas komunitas -->
      <label class="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 transition-colors">
        <div class="flex items-center gap-3">
          <Users :size="16" class="text-zinc-600 shrink-0" />
          <span class="text-xs font-semibold text-zinc-900">Aktivitas komunitas</span>
        </div>
        <input
          type="checkbox"
          v-model="mockNotificationPreferences.communityActivity"
          class="w-4 h-4 accent-black rounded cursor-pointer shrink-0"
        />
      </label>

      <!-- Toggle 4: Pengingat Ruang Doa -->
      <label class="p-4 rounded-2xl bg-zinc-50/70 border border-zinc-200/90 flex items-center justify-between gap-3 cursor-pointer hover:bg-zinc-100/60 transition-colors">
        <div class="flex items-center gap-3">
          <Radio :size="16" class="text-zinc-600 shrink-0" />
          <span class="text-xs font-semibold text-zinc-900">Pengingat Ruang Doa</span>
        </div>
        <input
          type="checkbox"
          v-model="mockNotificationPreferences.roomReminder"
          class="w-4 h-4 accent-black rounded cursor-pointer shrink-0"
        />
      </label>
    </div>
  </div>
</template>
