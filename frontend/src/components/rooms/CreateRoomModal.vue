<script setup lang="ts">
import { ref, watch } from 'vue'
import { X, Globe, Shield, Lock, Clock, Users, Play } from 'lucide-vue-next'
import type { RoomVisibility } from '@/types'
import { addPrayerRoom } from '@/data/mockRooms'
import { mockCommunitiesState } from '@/data/mockCommunities'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'created', code: string): void
}>()

const name = ref('')
const description = ref('')
const visibility = ref<RoomVisibility>('public')
const selectedGroup = ref(mockCommunitiesState[0]?.name || '')
const scheduledAt = ref('Hari ini, 21:00 WIB')
const durationMinutes = ref<number | undefined>(45)
const maxParticipants = ref<number>(15)
const startNow = ref(true)

const resetForm = () => {
  name.value = ''
  description.value = ''
  visibility.value = 'public'
  selectedGroup.value = mockCommunitiesState[0]?.name || ''
  scheduledAt.value = 'Hari ini, 21:00 WIB'
  durationMinutes.value = 45
  maxParticipants.value = 15
  startNow.value = true
}

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      resetForm()
    }
  }
)

const handleSubmit = () => {
  if (!name.value.trim() || !description.value.trim()) return

  const created = addPrayerRoom({
    name: name.value.trim(),
    description: description.value.trim(),
    visibility: visibility.value,
    groupName: visibility.value === 'group' ? selectedGroup.value : undefined,
    scheduledAt: scheduledAt.value,
    durationMinutes: durationMinutes.value || 30,
    maxParticipants: maxParticipants.value || 15,
    startNow: startNow.value,
  })

  emit('created', created.code)
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"
          @click="emit('close')"
        ></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
          <Transition
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
          >
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col h-full">
              <!-- Header -->
              <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
                <div>
                  <h2 class="font-serif-custom text-2xl font-normal text-black tracking-tight">
                    Buat Ruang Doa
                  </h2>
                  <p class="text-xs text-zinc-500 mt-1">
                    Buka ruang persekutuan doa bersama saudara-saudari seiman.
                  </p>
                </div>
                <button
                  type="button"
                  @click="emit('close')"
                  class="p-2 rounded-xl text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer"
                >
                  <X :size="20" />
                </button>
              </div>

              <!-- Body / Form -->
              <form @submit.prevent="handleSubmit" class="p-6 flex-1 overflow-y-auto space-y-6">
                <!-- Toggle: Mulai Sekarang -->
                <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/90 flex items-center justify-between gap-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center shrink-0">
                      <Play :size="16" />
                    </div>
                    <div>
                      <span class="text-xs font-bold text-black block">Mulai Sekarang</span>
                      <span class="text-[11px] text-zinc-500">Ruang doa akan langsung status LIVE.</span>
                    </div>
                  </div>
                  <input
                    type="checkbox"
                    v-model="startNow"
                    class="w-5 h-5 accent-black rounded cursor-pointer"
                  />
                </div>

                <!-- Nama Ruang -->
                <div>
                  <label for="room-name" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Nama Ruang Doa <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="room-name"
                    v-model="name"
                    type="text"
                    required
                    placeholder="Contoh: Doa Syafaat Pemulihan"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white"
                  />
                </div>

                <!-- Deskripsi -->
                <div>
                  <label for="room-desc" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Deskripsi / Pokok Doa <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    id="room-desc"
                    v-model="description"
                    rows="3"
                    required
                    placeholder="Jelaskan tujuan atau fokus pokok doa dalam ruang ini..."
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white resize-none"
                  ></textarea>
                </div>

                <!-- Visibility Options -->
                <div>
                  <label class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Akses Ruang Doa
                  </label>
                  <div class="grid grid-cols-3 gap-2">
                    <button
                      type="button"
                      @click="visibility = 'public'"
                      class="p-3 rounded-xl border text-center transition-all cursor-pointer space-y-1"
                      :class="
                        visibility === 'public'
                          ? 'border-black bg-zinc-50 font-bold text-black shadow-2xs'
                          : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300'
                      "
                    >
                      <Globe :size="15" class="mx-auto" />
                      <span class="text-xs block">Publik</span>
                    </button>

                    <button
                      type="button"
                      @click="visibility = 'group'"
                      class="p-3 rounded-xl border text-center transition-all cursor-pointer space-y-1"
                      :class="
                        visibility === 'group'
                          ? 'border-black bg-zinc-50 font-bold text-black shadow-2xs'
                          : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300'
                      "
                    >
                      <Shield :size="15" class="mx-auto" />
                      <span class="text-xs block">Komunitas</span>
                    </button>

                    <button
                      type="button"
                      @click="visibility = 'private'"
                      class="p-3 rounded-xl border text-center transition-all cursor-pointer space-y-1"
                      :class="
                        visibility === 'private'
                          ? 'border-black bg-zinc-50 font-bold text-black shadow-2xs'
                          : 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300'
                      "
                    >
                      <Lock :size="15" class="mx-auto" />
                      <span class="text-xs block">Privat</span>
                    </button>
                  </div>
                </div>

                <!-- Group Selector if visibility === 'group' -->
                <div v-if="visibility === 'group'">
                  <label for="room-group" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Pilih Komunitas
                  </label>
                  <select
                    id="room-group"
                    v-model="selectedGroup"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black bg-white focus:outline-none focus:border-black"
                  >
                    <option v-for="comm in mockCommunitiesState" :key="comm.id" :value="comm.name">
                      {{ comm.name }}
                    </option>
                  </select>
                </div>

                <!-- Schedule datetime if NOT startNow -->
                <div v-if="!startNow">
                  <label for="room-sched" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Jadwal Waktu Doa
                  </label>
                  <input
                    id="room-sched"
                    v-model="scheduledAt"
                    type="text"
                    placeholder="Contoh: Besok, 20:00 WIB"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black focus:outline-none focus:border-black bg-white"
                  />
                </div>

                <!-- Duration & Capacity -->
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label for="room-duration" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                      Durasi (Menit)
                    </label>
                    <input
                      id="room-duration"
                      v-model.number="durationMinutes"
                      type="number"
                      min="10"
                      step="5"
                      placeholder="45"
                      class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black focus:outline-none focus:border-black bg-white"
                    />
                  </div>

                  <div>
                    <label for="room-max" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                      Maksimal Peserta
                    </label>
                    <input
                      id="room-max"
                      v-model.number="maxParticipants"
                      type="number"
                      min="2"
                      max="50"
                      required
                      placeholder="15"
                      class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black focus:outline-none focus:border-black bg-white"
                    />
                  </div>
                </div>

                <!-- Actions -->
                <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3 mt-auto">
                  <button
                    type="button"
                    @click="emit('close')"
                    class="px-5 py-3 rounded-xl text-xs font-semibold text-zinc-700 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    :disabled="!name.trim() || !description.trim()"
                    class="px-6 py-3 rounded-xl text-xs font-semibold bg-black text-white hover:bg-zinc-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all cursor-pointer shadow-2xs"
                  >
                    Buat Ruang Doa
                  </button>
                </div>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
