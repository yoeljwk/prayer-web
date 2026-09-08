<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { Bell, HeartHandshake, MessageSquare, Users, Radio, CheckCheck, ArrowRight } from 'lucide-vue-next'
import {
  mockNotificationsState,
  unreadNotificationCount,
  markNotificationAsRead,
  markAllNotificationsAsRead,
} from '@/data/mockNotifications'

const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const handleItemClick = (id: string) => {
  markNotificationAsRead(id)
  closeDropdown()
}
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <!-- Bell Icon Trigger Button -->
    <button
      type="button"
      @click.stop="toggleDropdown"
      aria-label="Notifikasi"
      class="relative p-2.5 rounded-full text-zinc-600 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer"
    >
      <Bell :size="20" class="stroke-[1.8]" />

      <!-- Soft Unread Badge Dot -->
      <span
        v-if="unreadNotificationCount > 0"
        class="absolute top-2 right-2 w-2 h-2 rounded-full bg-black ring-2 ring-white"
      ></span>
    </button>

    <!-- Dropdown Preview Panel -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 scale-95 -translate-y-1"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-95 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 top-12 w-80 sm:w-96 bg-white rounded-2xl border border-zinc-200 shadow-2xl py-3 z-50 overflow-hidden"
      >
        <!-- Dropdown Header -->
        <div class="px-4 py-2 border-b border-zinc-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <h3 class="font-serif-custom text-base font-normal text-black">Notifikasi</h3>
            <span
              v-if="unreadNotificationCount > 0"
              class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-black text-white"
            >
              {{ unreadNotificationCount }} baru
            </span>
          </div>

          <button
            type="button"
            @click="markAllNotificationsAsRead"
            class="text-[11px] font-semibold text-zinc-500 hover:text-black transition-colors cursor-pointer flex items-center gap-1"
            title="Tandai semua dibaca"
          >
            <CheckCheck :size="13" />
            <span>Dibaca</span>
          </button>
        </div>

        <!-- Notification Preview List (Top 4 Items) -->
        <div class="max-h-80 overflow-y-auto divide-y divide-zinc-50">
          <RouterLink
            v-for="notif in mockNotificationsState.slice(0, 4)"
            :key="notif.id"
            :to="notif.link || '/notifications'"
            @click="handleItemClick(notif.id)"
            class="p-4 flex items-start gap-3 hover:bg-zinc-50 transition-colors block cursor-pointer"
            :class="{ 'bg-zinc-50/60': !notif.isRead }"
          >
            <!-- Category Icon -->
            <div
              class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
              :class="{
                'bg-zinc-100 text-zinc-800': notif.category === 'prayer',
                'bg-zinc-100 text-zinc-900': notif.category === 'community',
                'bg-black text-white': notif.category === 'room',
              }"
            >
              <HeartHandshake v-if="notif.category === 'prayer'" :size="14" />
              <Users v-else-if="notif.category === 'community'" :size="14" />
              <Radio v-else :size="14" />
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2 mb-0.5">
                <h4
                  class="text-xs font-semibold text-black truncate"
                  :class="{ 'font-bold': !notif.isRead }"
                >
                  {{ notif.title }}
                </h4>
                <span
                  v-if="!notif.isRead"
                  class="w-1.5 h-1.5 rounded-full bg-black shrink-0"
                ></span>
              </div>
              <p class="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed font-normal">
                {{ notif.description }}
              </p>
              <span class="text-[10px] text-zinc-400 mt-1 block">{{ notif.timestamp }}</span>
            </div>
          </RouterLink>
        </div>

        <!-- Dropdown Footer: Lihat Semua Link -->
        <div class="px-4 pt-2.5 pb-1 border-t border-zinc-100 text-center">
          <RouterLink
            to="/notifications"
            @click="closeDropdown"
            class="text-xs font-semibold text-black hover:underline underline-offset-4 inline-flex items-center gap-1 cursor-pointer py-1"
          >
            <span>Lihat Semua Notifikasi</span>
            <ArrowRight :size="13" />
          </RouterLink>
        </div>
      </div>
    </Transition>
  </div>
</template>
