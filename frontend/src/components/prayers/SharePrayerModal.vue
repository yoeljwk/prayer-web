<script setup lang="ts">
import { ref, computed } from 'vue'
import { X, Globe, Users, Lock, Send, Check } from 'lucide-vue-next'
import type { PrayerVisibility, PrayerRequest } from '@/types'
import { mockGroups } from '@/data/mockPrayers'
import api from '@/services/api'

defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submit-prayer', newPrayer?: any): void
}>()

const content = ref('')
const isPublic = ref(true)
const isGroup = ref(false)
const isPrivate = ref(false)
const selectedGroup = ref(mockGroups[0])
const isAnonymous = ref(false)
const isSubmitting = ref(false)
const maxChars = 500

const charCount = computed(() => content.value.length)
const isOverLimit = computed(() => charCount.value > maxChars)
const isValid = computed(() => content.value.trim().length > 0 && !isOverLimit.value && (isPublic.value || isGroup.value || isPrivate.value))

const togglePublic = () => {
  if (isPrivate.value) isPrivate.value = false
  isPublic.value = !isPublic.value
  if (!isPublic.value && !isGroup.value && !isPrivate.value) {
    isPublic.value = true
  }
}

const toggleGroup = () => {
  if (isPrivate.value) isPrivate.value = false
  isGroup.value = !isGroup.value
  if (!isPublic.value && !isGroup.value && !isPrivate.value) {
    isPublic.value = true
  }
}

const togglePrivate = () => {
  isPrivate.value = !isPrivate.value
  if (isPrivate.value) {
    isPublic.value = false
    isGroup.value = false
  } else {
    isPublic.value = true
  }
}

const resetForm = () => {
  content.value = ''
  isPublic.value = true
  isGroup.value = false
  isPrivate.value = false
  selectedGroup.value = mockGroups[0]
  isAnonymous.value = false
}

const handleClose = () => {
  resetForm()
  emit('close')
}

const handleSubmit = async () => {
  if (!isValid.value || isSubmitting.value) return

  let computedVisibility: PrayerVisibility = 'public'
  if (isPrivate.value) {
    computedVisibility = 'private'
  } else if (isGroup.value && !isPublic.value) {
    computedVisibility = 'group'
  } else {
    computedVisibility = 'public'
  }

  isSubmitting.value = true

  try {
    const response = await api.post('/prayers', {
      content: content.value.trim(),
      visibility: computedVisibility,
      is_anonymous: isAnonymous.value,
    })

    emit('submit-prayer', response.data?.data)
    resetForm()
    emit('close')
  } catch (error) {
    console.error('Gagal mengirim permohonan doa:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex justify-end bg-black/20 backdrop-blur-xs"
      @click="handleClose"
    >
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
        appear
      >
        <div
          v-if="isOpen"
          class="w-full max-w-lg h-full bg-white border-l border-zinc-200 shadow-2xl relative flex flex-col p-6 sm:p-8 overflow-y-auto"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between pb-4 border-b border-zinc-100 mb-6 shrink-0">
            <div>
              <h2 class="font-serif-custom text-2xl font-normal text-black">
                Bagikan Permohonan Doa
              </h2>
              <p class="text-xs text-zinc-500 mt-1">
                Sampaikan pergumulan doa Anda agar dapat didoakan bersama.
              </p>
            </div>

            <button
              type="button"
              @click="handleClose"
              class="text-zinc-400 hover:text-black p-2 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer"
            >
              <X :size="20" />
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-6 flex-1 flex flex-col justify-between">
            <div class="space-y-6 overflow-y-auto pr-1">
              
              <!-- Textarea Content -->
              <div>
                <label for="prayer-content" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">
                  Isi Permohonan Doa
                </label>
                <textarea
                  id="prayer-content"
                  v-model="content"
                  rows="6"
                  required
                  placeholder="Tuliskan permohonan doa Anda di sini secara jujur dan reflektif..."
                  class="w-full p-4 rounded-xl border border-zinc-200 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white resize-none"
                ></textarea>
                
                <!-- Character Counter -->
                <div class="flex justify-end mt-1.5">
                  <span
                    class="text-xs font-medium"
                    :class="isOverLimit ? 'text-red-500 font-bold' : 'text-zinc-400'"
                  >
                    {{ charCount }} / {{ maxChars }}
                  </span>
                </div>
              </div>

              <!-- Multi-select Visibility Chips -->
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2.5">
                  Tingkat Privasi / Visibility
                </label>
                
                <div class="flex flex-wrap gap-2">
                  <!-- Publik Chip Toggle -->
                  <button
                    type="button"
                    @click="togglePublic"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-medium border transition-all cursor-pointer select-none"
                    :class="[
                      isPublic
                        ? 'bg-black text-white border-black shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    ]"
                  >
                    <Globe :size="14" />
                    <span>Publik</span>
                    <Check v-if="isPublic" :size="12" class="stroke-[2.5]" />
                  </button>

                  <!-- Komunitas Chip Toggle -->
                  <button
                    type="button"
                    @click="toggleGroup"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-medium border transition-all cursor-pointer select-none"
                    :class="[
                      isGroup
                        ? 'bg-black text-white border-black shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    ]"
                  >
                    <Users :size="14" />
                    <span>Komunitas</span>
                    <Check v-if="isGroup" :size="12" class="stroke-[2.5]" />
                  </button>

                  <!-- Pribadi Chip Toggle -->
                  <button
                    type="button"
                    @click="togglePrivate"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-medium border transition-all cursor-pointer select-none"
                    :class="[
                      isPrivate
                        ? 'bg-black text-white border-black shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    ]"
                  >
                    <Lock :size="14" />
                    <span>Pribadi</span>
                    <Check v-if="isPrivate" :size="12" class="stroke-[2.5]" />
                  </button>
                </div>

                <p class="text-[11px] text-zinc-400 mt-2 font-normal">
                  * Anda dapat memilih Publik dan Komunitas sekaligus.
                </p>
              </div>

              <!-- Community Selector Dropdown (When Komunitas is active) -->
              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
              >
                <div v-if="isGroup">
                  <label for="group-select" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">
                    Pilih Komunitas
                  </label>
                  <select
                    id="group-select"
                    v-model="selectedGroup"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-200 text-sm text-black focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white cursor-pointer"
                  >
                    <option v-for="group in mockGroups" :key="group" :value="group">
                      {{ group }}
                    </option>
                  </select>
                </div>
              </Transition>

              <!-- Anonymous Toggle Checkbox -->
              <div class="pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                  <input
                    v-model="isAnonymous"
                    type="checkbox"
                    class="w-4 h-4 rounded border-zinc-300 text-black focus:ring-black cursor-pointer"
                  />
                  <span class="text-xs text-zinc-700 font-normal">Bagikan secara anonim (Nama disembunyikan)</span>
                </label>
              </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 shrink-0">
              <button
                type="button"
                @click="handleClose"
                class="px-5 py-2.5 rounded-full border border-zinc-200 text-zinc-700 font-semibold text-xs hover:bg-zinc-50 transition-colors cursor-pointer"
              >
                Batal
              </button>

              <button
                type="submit"
                :disabled="!isValid"
                class="px-6 py-2.5 rounded-full bg-black hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-xs"
              >
                <Send :size="14" />
                <span>Bagikan Doa</span>
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>
