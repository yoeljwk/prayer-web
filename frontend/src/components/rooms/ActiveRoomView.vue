<script setup lang="ts">
import { ref } from 'vue'
import { LogOut, Hand, HeartHandshake, BookOpen, Users, Sparkles, Check, Heart } from 'lucide-vue-next'
import type { PrayerRoom } from '@/types'

const props = defineProps<{
  room: PrayerRoom
}>()

const emit = defineEmits<{
  (e: 'leave'): void
}>()

// Local interactive state for current user
const isPraying = ref(false)
const hasRaisedHand = ref(false)
const supportToast = ref<string | null>(null)

const togglePraying = () => {
  isPraying.value = !isPraying.value
}

const toggleHand = () => {
  hasRaisedHand.value = !hasRaisedHand.value
}

const sendSupport = (message: string) => {
  supportToast.value = message
  setTimeout(() => {
    supportToast.value = null
  }, 3000)
}
</script>

<template>
  <div class="min-h-screen bg-[#FBFBF9] flex flex-col justify-between selection:bg-black selection:text-white">
    <!-- Header Room Control Bar -->
    <header class="bg-white border-b border-zinc-200/90 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-6 h-18 flex items-center justify-between">
        <!-- Room Title & Live Pulse -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 bg-black text-white px-3 py-1 rounded-full text-xs font-bold shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>LIVE</span>
          </div>

          <div>
            <h1 class="font-serif-custom text-lg sm:text-xl font-normal text-black tracking-tight leading-tight">
              {{ room.name }}
            </h1>
            <p class="text-[11px] text-zinc-500 font-normal">
              Host: {{ room.hostName }}
            </p>
          </div>
        </div>

        <!-- Participants Count & Leave Button -->
        <div class="flex items-center gap-4">
          <div class="hidden sm:flex items-center gap-1.5 text-xs text-zinc-600 font-medium bg-zinc-100 px-3 py-1.5 rounded-full border border-zinc-200">
            <Users :size="14" class="text-zinc-500" />
            <span>{{ room.participants.length || room.currentParticipants }} Peserta Berdoa</span>
          </div>

          <button
            type="button"
            @click="emit('leave')"
            class="bg-zinc-100 hover:bg-zinc-200 text-zinc-800 hover:text-black font-semibold text-xs px-4 py-2.5 rounded-xl border border-zinc-200 flex items-center gap-2 transition-colors cursor-pointer"
          >
            <LogOut :size="15" />
            <span>Keluar Ruang</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Spiritual Prayer Atmosphere Content -->
    <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-8 sm:py-12 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
      
      <!-- Left & Middle: Focus Prayer & Scripture Section (2 Columns) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Focus Topic Main Card (Peaceful White Surface) -->
        <div class="bg-white rounded-3xl border border-zinc-200/90 p-8 sm:p-10 shadow-xs relative overflow-hidden">
          <!-- Background Subtle Flame/Water Drop Graphic Accent -->
          <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-zinc-100/50 pointer-events-none blur-xl"></div>

          <div class="flex items-center gap-2 text-xs font-bold text-zinc-500 uppercase tracking-widest mb-3">
            <Sparkles :size="15" class="text-black" />
            <span>Fokus Pokok Doa Saat Ini</span>
          </div>

          <h2 class="font-serif-custom text-2xl sm:text-3xl font-normal text-black leading-snug tracking-tight mb-4">
            {{ room.currentFocusTopic }}
          </h2>

          <p class="text-xs sm:text-sm text-zinc-600 leading-relaxed font-normal">
            {{ room.description }}
          </p>

          <!-- Current User Active Praying Status Indicator Banner -->
          <div
            v-if="isPraying"
            class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between text-xs font-semibold animate-fade-in"
          >
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
              <span>Anda sedang menaikkan doa bersama komunitas ini.</span>
            </div>
            <span class="text-[11px] text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full font-bold">Aktif Berdoa</span>
          </div>
        </div>

        <!-- Scripture Verse Card -->
        <div class="bg-zinc-900 text-white rounded-3xl p-8 sm:p-10 shadow-md space-y-4 relative overflow-hidden">
          <div class="flex items-center gap-2 text-xs font-semibold text-zinc-400 uppercase tracking-wider">
            <BookOpen :size="15" class="text-zinc-300" />
            <span>Ayat Alkitab Pendukung</span>
          </div>

          <blockquote class="font-serif-custom text-lg sm:text-xl font-normal text-zinc-100 italic leading-relaxed">
            "{{ room.scriptureVerse.text }}"
          </blockquote>

          <p class="text-xs font-bold text-zinc-400 tracking-wider text-right uppercase">
            — {{ room.scriptureVerse.passage }}
          </p>
        </div>

        <!-- Support Reaction Toast Alert -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 translate-y-2"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="supportToast" class="bg-black text-white p-4 rounded-2xl shadow-xl flex items-center justify-between text-xs font-medium">
            <div class="flex items-center gap-2">
              <Heart :size="16" class="text-red-400 fill-red-400" />
              <span>{{ supportToast }}</span>
            </div>
            <span class="text-zinc-400 text-[11px]">Tersampaikan</span>
          </div>
        </Transition>
      </div>

      <!-- Right: Participants Drawer / List (1 Column) -->
      <div class="bg-white rounded-3xl border border-zinc-200/90 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
          <div class="flex items-center gap-2 text-xs font-bold text-black uppercase tracking-wider">
            <Users :size="16" class="text-zinc-600" />
            <span>Daftar Peserta</span>
          </div>
          <span class="text-xs font-semibold text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-full">
            {{ room.participants.length }}
          </span>
        </div>

        <!-- Participants List -->
        <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1 scrollbar-none">
          <div
            v-for="p in room.participants"
            :key="p.id"
            class="flex items-center justify-between p-3 rounded-2xl transition-colors border"
            :class="p.name === 'Youwel Ginting' ? 'bg-zinc-50 border-zinc-300' : 'bg-white border-zinc-100'"
          >
            <div class="flex items-center gap-3 min-w-0">
              <div class="relative">
                <div class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
                  {{ p.name.charAt(0) }}
                </div>
                <!-- Praying indicator dot -->
                <span
                  v-if="p.isPraying || (p.name === 'Youwel Ginting' && isPraying)"
                  class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"
                  title="Sedang Berdoa"
                ></span>
              </div>

              <div class="truncate">
                <div class="flex items-center gap-1.5">
                  <span class="text-xs font-semibold text-black truncate">{{ p.name }}</span>
                  <span v-if="p.role === 'host'" class="text-[10px] font-bold text-white bg-black px-1.5 py-0.2 rounded-md">Host</span>
                </div>
                <span class="text-[11px] text-zinc-400 block truncate">Bergabung {{ p.joinedAt }}</span>
              </div>
            </div>

            <!-- Raised Hand Indicator -->
            <div v-if="p.hasRaisedHand || (p.name === 'Youwel Ginting' && hasRaisedHand)" class="shrink-0 bg-amber-100 text-amber-900 p-1.5 rounded-lg" title="Ingin Menyampaikan Pokok Doa">
              <Hand :size="14" class="fill-amber-600" />
            </div>
          </div>
        </div>
      </div>

    </main>

    <!-- Bottom Simple Spiritual Controls Bar -->
    <footer class="bg-white border-t border-zinc-200/90 py-4 px-6 sticky bottom-0 z-40 shadow-lg">
      <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-center gap-3">
        
        <!-- Control 1: Saya Sedang Berdoa Toggle -->
        <button
          type="button"
          @click="togglePraying"
          class="px-5 py-3 rounded-2xl font-semibold text-xs flex items-center gap-2 transition-all cursor-pointer shadow-2xs"
          :class="
            isPraying
              ? 'bg-emerald-700 text-white shadow-md scale-105'
              : 'bg-zinc-100 text-zinc-800 border border-zinc-200 hover:bg-zinc-200'
          "
        >
          <HeartHandshake :size="16" />
          <span>{{ isPraying ? 'Saya Sedang Berdoa' : 'Mulai Berdoa' }}</span>
          <Check v-if="isPraying" :size="14" class="stroke-[3]" />
        </button>

        <!-- Control 2: Angkat Tangan Toggle -->
        <button
          type="button"
          @click="toggleHand"
          class="px-5 py-3 rounded-2xl font-semibold text-xs flex items-center gap-2 transition-all cursor-pointer border"
          :class="
            hasRaisedHand
              ? 'bg-amber-50 text-amber-900 border-amber-300 shadow-2xs font-bold'
              : 'bg-white text-zinc-700 border-zinc-200 hover:bg-zinc-50'
          "
        >
          <Hand :size="16" :class="{ 'fill-amber-600': hasRaisedHand }" />
          <span>{{ hasRaisedHand ? 'Tangan Terangkat' : 'Angkat Tangan' }}</span>
        </button>

        <!-- Control 3: Kirim Dukungan Reactions -->
        <div class="flex items-center gap-1.5 pl-2 border-l border-zinc-200">
          <button
            type="button"
            @click="sendSupport('Amin! Mari aminkan doa bersama.')"
            class="px-3.5 py-3 rounded-2xl bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-semibold transition-colors cursor-pointer"
          >
            Amin 🙏
          </button>
          <button
            type="button"
            @click="sendSupport('Mendoakan dari jauh dengan kasih.')"
            class="px-3.5 py-3 rounded-2xl bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-semibold transition-colors cursor-pointer"
          >
            Ikut Mendoakan ❤️
          </button>
        </div>

      </div>
    </footer>
  </div>
</template>
