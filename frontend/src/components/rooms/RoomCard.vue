<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Users, Clock, Globe, Lock, Shield, Bell, BellRing, ArrowRight } from 'lucide-vue-next'
import type { PrayerRoom } from '@/types'
import { toggleRoomReminder } from '@/data/mockRooms'

const props = defineProps<{
  room: PrayerRoom
}>()

const handleReminderClick = (e: Event) => {
  e.preventDefault()
  e.stopPropagation()
  toggleRoomReminder(props.room.code)
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-zinc-200/90 p-6 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
    <div>
      <!-- Top Row: Live / Scheduled / Ended Badge & Visibility -->
      <div class="flex items-center justify-between gap-3 mb-4">
        <!-- Status Badge -->
        <div class="flex items-center gap-2">
          <span
            v-if="room.status === 'live'"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-black text-white shadow-2xs"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>LIVE BERLANGSUNG</span>
          </span>

          <span
            v-else-if="room.status === 'scheduled'"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200"
          >
            <Clock :size="12" class="text-amber-700" />
            <span>AKAN DATANG</span>
          </span>

          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600 border border-zinc-200"
          >
            <span>SELESAI</span>
          </span>
        </div>

        <!-- Visibility Badge -->
        <span class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500">
          <Globe v-if="room.visibility === 'public'" :size="13" class="text-zinc-400" />
          <Shield v-else-if="room.visibility === 'group'" :size="13" class="text-zinc-400" />
          <Lock v-else :size="13" class="text-zinc-400" />
          <span class="capitalize">{{ room.visibility === 'group' ? (room.groupName || 'Komunitas') : room.visibility }}</span>
        </span>
      </div>

      <!-- Room Name & Description -->
      <RouterLink :to="`/ruang-doa/${room.code}`" class="block group/link">
        <h3 class="font-serif-custom text-xl font-normal text-black group-hover/link:underline underline-offset-4 tracking-tight mb-2">
          {{ room.name }}
        </h3>
      </RouterLink>

      <p class="text-xs text-zinc-500 line-clamp-2 leading-relaxed mb-4 font-normal">
        {{ room.description }}
      </p>

      <!-- Host & Schedule Info -->
      <div class="space-y-1.5 mb-4 text-xs text-zinc-600">
        <div class="flex items-center gap-1.5">
          <span class="text-zinc-400">Host:</span>
          <span class="font-semibold text-black">{{ room.hostName }}</span>
        </div>
        <div class="flex items-center gap-1.5 text-zinc-500">
          <Clock :size="13" class="text-zinc-400" />
          <span>{{ room.scheduledAt }}</span>
          <span v-if="room.durationMinutes" class="text-zinc-400">({{ room.durationMinutes }} menit)</span>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Participant Count & Actions -->
    <div class="pt-4 border-t border-zinc-100 flex items-center justify-between gap-3 mt-auto">
      <div class="flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
        <Users :size="14" class="text-zinc-400" />
        <span>{{ room.currentParticipants }} / {{ room.maxParticipants }} Peserta</span>
      </div>

      <div class="flex items-center gap-2">
        <!-- Scheduled Action: Ingatkan Saya -->
        <button
          v-if="room.status === 'scheduled'"
          type="button"
          @click="handleReminderClick"
          class="px-3 py-2 rounded-xl text-xs font-semibold border transition-all cursor-pointer flex items-center gap-1.5"
          :class="
            room.hasReminderSet
              ? 'bg-zinc-900 text-white border-black'
              : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
          "
        >
          <BellRing v-if="room.hasReminderSet" :size="13" />
          <Bell v-else :size="13" />
          <span>{{ room.hasReminderSet ? 'Diingatkan' : 'Ingatkan' }}</span>
        </button>

        <!-- Main Action Link: Masuk / Lihat Detail -->
        <RouterLink
          :to="`/ruang-doa/${room.code}`"
          class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center gap-1.5 shrink-0"
          :class="
            room.status === 'live'
              ? 'bg-black text-white hover:bg-zinc-800 shadow-2xs'
              : 'bg-zinc-100 text-zinc-800 hover:bg-zinc-200'
          "
        >
          <span>{{ room.status === 'live' ? 'Masuk Ruang' : 'Lihat Detail' }}</span>
          <ArrowRight :size="13" />
        </RouterLink>
      </div>
    </div>
  </div>
</template>
