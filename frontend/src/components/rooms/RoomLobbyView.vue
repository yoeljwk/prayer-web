<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { ArrowLeft, Clock, Users, Globe, Lock, Shield, Check, Bell, BellRing, Play, ShieldAlert } from 'lucide-vue-next'
import type { PrayerRoom } from '@/types'
import { toggleRoomReminder } from '@/data/mockRooms'

const props = defineProps<{
  room: PrayerRoom
}>()

const emit = defineEmits<{
  (e: 'enter'): void
}>()

const handleReminderToggle = () => {
  toggleRoomReminder(props.room.code)
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-6 py-10 sm:py-14">
    <!-- Back Link -->
    <RouterLink
      to="/ruang-doa"
      class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 hover:text-black transition-colors mb-8 group cursor-pointer"
    >
      <ArrowLeft :size="16" class="group-hover:-translate-x-1 transition-transform" />
      <span>Kembali ke Daftar Ruang Doa</span>
    </RouterLink>

    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl border border-zinc-200/90 shadow-xs overflow-hidden">
      <!-- Header Banner -->
      <div class="p-8 sm:p-10 border-b border-zinc-100 bg-[#FDFDFD]">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
          <!-- Status Pill Badge -->
          <span
            v-if="room.status === 'live'"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-black text-white shadow-2xs"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>LIVE BERLANGSUNG</span>
          </span>

          <span
            v-else-if="room.status === 'scheduled'"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200"
          >
            <Clock :size="13" class="text-amber-700" />
            <span>TERJADWAL</span>
          </span>

          <span
            v-else
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600 border border-zinc-200"
          >
            <span>RUANG DOA TELAH SELESAI</span>
          </span>

          <!-- Visibility Badge -->
          <span class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500">
            <Globe v-if="room.visibility === 'public'" :size="14" class="text-zinc-400" />
            <Shield v-else-if="room.visibility === 'group'" :size="14" class="text-zinc-400" />
            <Lock v-else :size="14" class="text-zinc-400" />
            <span class="capitalize">{{ room.visibility === 'group' ? (room.groupName || 'Komunitas') : room.visibility }}</span>
          </span>
        </div>

        <h1 class="font-serif-custom text-3xl sm:text-4xl font-normal text-black tracking-tight mb-3">
          {{ room.name }}
        </h1>

        <p class="text-sm text-zinc-600 leading-relaxed font-normal max-w-2xl">
          {{ room.description }}
        </p>
      </div>

      <!-- Detail Stats & Rules -->
      <div class="p-8 sm:p-10 space-y-8">
        <!-- Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white border border-zinc-200 text-zinc-700 flex items-center justify-center shrink-0">
              <Clock :size="18" />
            </div>
            <div>
              <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Waktu Doa</span>
              <span class="text-xs font-bold text-black">{{ room.scheduledAt }}</span>
            </div>
          </div>

          <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white border border-zinc-200 text-zinc-700 flex items-center justify-center shrink-0">
              <Users :size="18" />
            </div>
            <div>
              <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Kapasitas</span>
              <span class="text-xs font-bold text-black">
                {{ room.currentParticipants }} / {{ room.maxParticipants }} Peserta
              </span>
            </div>
          </div>

          <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
              {{ room.hostName.charAt(0) }}
            </div>
            <div class="truncate">
              <span class="text-[11px] text-zinc-400 font-semibold uppercase tracking-wider block">Host Ruang</span>
              <span class="text-xs font-bold text-black truncate block">{{ room.hostName }}</span>
            </div>
          </div>
        </div>

        <!-- Countdown Section for Scheduled Rooms -->
        <div v-if="room.status === 'scheduled'" class="p-6 rounded-2xl bg-amber-50/60 border border-amber-200/80 text-center space-y-3">
          <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">
            Ruang Doa Belum Dimulai
          </span>
          <div class="flex items-center justify-center gap-4 text-amber-950 font-mono text-2xl font-bold">
            <div class="bg-white px-3 py-2 rounded-xl border border-amber-200 shadow-2xs">02 <span class="text-[10px] font-sans font-normal text-amber-700 block">Jam</span></div>
            <span>:</span>
            <div class="bg-white px-3 py-2 rounded-xl border border-amber-200 shadow-2xs">45 <span class="text-[10px] font-sans font-normal text-amber-700 block">Menit</span></div>
            <span>:</span>
            <div class="bg-white px-3 py-2 rounded-xl border border-amber-200 shadow-2xs">12 <span class="text-[10px] font-sans font-normal text-amber-700 block">Detik</span></div>
          </div>
          <p class="text-xs text-amber-800">
            Anda dapat mengklik tombol "Ingatkan Saya" agar mendapat pemberitahuan saat doa dimulai.
          </p>
        </div>

        <!-- Rules Section -->
        <div class="space-y-3">
          <div class="flex items-center gap-2 text-black font-semibold text-xs uppercase tracking-wider">
            <ShieldAlert :size="16" class="text-zinc-600" />
            <span>Etika & Panduan Ruang Doa</span>
          </div>
          <ul class="space-y-2 text-xs text-zinc-600 list-disc list-inside leading-relaxed font-normal">
            <li v-for="(rule, idx) in room.rules" :key="idx">{{ rule }}</li>
            <li v-if="room.rules.length === 0">Menjaga keheningan dan kebersamaan dalam menaikkan doa syafaat.</li>
          </ul>
        </div>

        <!-- Primary Action Buttons -->
        <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3">
          <!-- Scheduled Action -->
          <button
            v-if="room.status === 'scheduled'"
            type="button"
            @click="handleReminderToggle"
            class="px-5 py-3.5 rounded-xl text-xs font-semibold border transition-all cursor-pointer flex items-center gap-2"
            :class="
              room.hasReminderSet
                ? 'bg-zinc-900 text-white border-black'
                : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
            "
          >
            <BellRing v-if="room.hasReminderSet" :size="15" />
            <Bell v-else :size="15" />
            <span>{{ room.hasReminderSet ? 'Diingatkan' : 'Ingatkan Saya' }}</span>
          </button>

          <!-- Live Action: Enter Room -->
          <button
            v-if="room.status === 'live' && room.currentParticipants < room.maxParticipants"
            type="button"
            @click="emit('enter')"
            class="px-7 py-3.5 rounded-xl text-xs font-semibold bg-black hover:bg-zinc-800 text-white shadow-2xs transition-all cursor-pointer flex items-center gap-2"
          >
            <Play :size="15" class="fill-white" />
            <span>Masuk Ruang Doa</span>
          </button>

          <!-- Room Full State Button -->
          <button
            v-else-if="room.status === 'live' && room.currentParticipants >= room.maxParticipants"
            type="button"
            disabled
            class="px-6 py-3.5 rounded-xl text-xs font-semibold bg-zinc-200 text-zinc-500 cursor-not-allowed"
          >
            Ruang Penuh (Kapasitas Tercapai)
          </button>

          <!-- Ended State Button -->
          <button
            v-else-if="room.status === 'ended'"
            type="button"
            disabled
            class="px-6 py-3.5 rounded-xl text-xs font-semibold bg-zinc-100 text-zinc-400 border border-zinc-200 cursor-not-allowed"
          >
            Sesi Doa Telah Selesai
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
