<script setup lang="ts">
import { ref, computed } from 'vue'
import { CheckCheck, BellOff, Sparkles } from 'lucide-vue-next'
import {
  mockNotificationsState,
  unreadNotificationCount,
  markAllNotificationsAsRead,
} from '@/data/mockNotifications'
import NotificationCard from '@/components/notifications/NotificationCard.vue'
import NotificationPreferences from '@/components/notifications/NotificationPreferences.vue'

const selectedFilter = ref<'all' | 'unread' | 'prayer' | 'community' | 'room'>('all')

const filteredNotifications = computed(() => {
  let list = [...mockNotificationsState]

  if (selectedFilter.value === 'unread') {
    list = list.filter((n) => !n.isRead)
  } else if (selectedFilter.value === 'prayer') {
    list = list.filter((n) => n.category === 'prayer')
  } else if (selectedFilter.value === 'community') {
    list = list.filter((n) => n.category === 'community')
  } else if (selectedFilter.value === 'room') {
    list = list.filter((n) => n.category === 'room')
  }

  return list
})
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen py-10 sm:py-14">
    <div class="max-w-4xl mx-auto px-6">
      
      <!-- Top Title & Subtitle Header -->
      <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
          <h1 class="font-serif-custom text-4xl sm:text-5xl font-normal text-black tracking-tight mb-2">
            Notifikasi
          </h1>
          <p class="text-sm sm:text-base text-zinc-500 font-normal max-w-xl leading-relaxed">
            Aktivitas lembut dari komunitas dan permohonan doamu.
          </p>
        </div>

        <!-- Mark All as Read Action -->
        <button
          type="button"
          @click="markAllNotificationsAsRead"
          :disabled="unreadNotificationCount === 0"
          class="inline-flex items-center gap-2 bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-800 font-semibold text-xs px-4 py-2.5 rounded-xl transition-all duration-200 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed shrink-0 shadow-2xs"
        >
          <CheckCheck :size="15" />
          <span>Tandai Semua Sudah Dibaca</span>
        </button>
      </div>

      <!-- Filter Tabs Bar -->
      <div class="flex items-center gap-2 mb-8 pb-1 overflow-x-auto scrollbar-none">
        <button
          type="button"
          @click="selectedFilter = 'all'"
          class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer shrink-0"
          :class="selectedFilter === 'all' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
        >
          Semua
        </button>

        <button
          type="button"
          @click="selectedFilter = 'unread'"
          class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer shrink-0 flex items-center gap-1.5"
          :class="selectedFilter === 'unread' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
        >
          <span>Belum Dibaca</span>
          <span
            v-if="unreadNotificationCount > 0"
            class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-800 text-white font-bold"
            :class="{ 'bg-white text-black': selectedFilter === 'unread' }"
          >
            {{ unreadNotificationCount }}
          </span>
        </button>

        <button
          type="button"
          @click="selectedFilter = 'prayer'"
          class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer shrink-0"
          :class="selectedFilter === 'prayer' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
        >
          Doa
        </button>

        <button
          type="button"
          @click="selectedFilter = 'community'"
          class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer shrink-0"
          :class="selectedFilter === 'community' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
        >
          Komunitas
        </button>

        <button
          type="button"
          @click="selectedFilter = 'room'"
          class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer shrink-0"
          :class="selectedFilter === 'room' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
        >
          Ruang Doa
        </button>
      </div>

      <!-- Notifications Feed -->
      <div class="space-y-3 mb-12">
        <template v-if="filteredNotifications.length > 0">
          <NotificationCard
            v-for="notif in filteredNotifications"
            :key="notif.id"
            :notification="notif"
          />
        </template>

        <!-- Empty State -->
        <div
          v-else
          class="bg-white rounded-3xl p-12 text-center border border-zinc-200/90 shadow-xs space-y-3"
        >
          <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
            <BellOff :size="22" />
          </div>
          <h3 class="font-serif-custom text-xl font-normal text-black">
            Belum Ada Notifikasi
          </h3>
          <p class="text-xs text-zinc-500 max-w-sm mx-auto leading-relaxed">
            Semua aktivitas baru dari komunitas dan permohonan doamu akan muncul di sini secara lembut.
          </p>
          <button
            v-if="selectedFilter !== 'all'"
            type="button"
            @click="selectedFilter = 'all'"
            class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
          >
            Lihat Semua Notifikasi
          </button>
        </div>
      </div>

      <!-- Section: Notification Preferences UI -->
      <NotificationPreferences />

    </div>
  </div>
</template>
