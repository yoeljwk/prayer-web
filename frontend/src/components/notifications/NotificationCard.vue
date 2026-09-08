<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { HeartHandshake, Users, Radio, ArrowRight } from 'lucide-vue-next'
import type { NotificationItem } from '@/types'
import { markNotificationAsRead } from '@/data/mockNotifications'

const props = defineProps<{
  notification: NotificationItem
}>()

const handleClick = () => {
  markNotificationAsRead(props.notification.id)
}
</script>

<template>
  <div
    @click="handleClick"
    class="bg-white rounded-2xl border p-5 shadow-2xs transition-all duration-200 flex items-start justify-between gap-4 cursor-pointer group hover:shadow-md"
    :class="
      notification.isRead
        ? 'border-zinc-200/90'
        : 'border-zinc-300 bg-zinc-50/40 font-semibold'
    "
  >
    <div class="flex items-start gap-4 min-w-0">
      <!-- Icon Container -->
      <div
        class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 border"
        :class="{
          'bg-zinc-100 border-zinc-200 text-zinc-800': notification.category === 'prayer',
          'bg-zinc-100 border-zinc-200 text-zinc-900': notification.category === 'community',
          'bg-black border-black text-white': notification.category === 'room',
        }"
      >
        <HeartHandshake v-if="notification.category === 'prayer'" :size="18" />
        <Users v-else-if="notification.category === 'community'" :size="18" />
        <Radio v-else :size="18" />
      </div>

      <!-- Content -->
      <div class="space-y-1 min-w-0">
        <div class="flex items-center gap-2">
          <h3
            class="text-sm font-semibold text-black tracking-tight group-hover:underline underline-offset-4"
            :class="{ 'font-bold': !notification.isRead }"
          >
            {{ notification.title }}
          </h3>
          <!-- Unread Dot Badge -->
          <span
            v-if="!notification.isRead"
            class="w-2 h-2 rounded-full bg-black shrink-0"
          ></span>
        </div>

        <p class="text-xs text-zinc-600 font-normal leading-relaxed">
          {{ notification.description }}
        </p>

        <span class="text-[11px] text-zinc-400 font-medium block pt-1">
          {{ notification.timestamp }}
        </span>
      </div>
    </div>

    <!-- Optional Navigation Link Indicator -->
    <RouterLink
      v-if="notification.link"
      :to="notification.link"
      @click.stop="handleClick"
      class="p-2 rounded-xl text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors shrink-0 cursor-pointer"
      title="Buka Halaman"
    >
      <ArrowRight :size="16" />
    </RouterLink>
  </div>
</template>
