<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { X, Globe, Lock, Save } from 'lucide-vue-next'
import type { PrayerVisibility, PrayerRequest } from '@/types'
import api from '@/services/api'

const props = defineProps<{
  isOpen: boolean
  prayer: PrayerRequest | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'updated', updatedPrayer: any): void
}>()

const content = ref('')
const visibility = ref<PrayerVisibility>('public')
const isAnonymous = ref(false)
const isSubmitting = ref(false)
const errorMessage = ref('')
const maxChars = 500

const charCount = computed(() => content.value.length)
const isOverLimit = computed(() => charCount.value > maxChars)
const isValid = computed(() => content.value.trim().length > 0 && !isOverLimit.value)

watch(
  () => props.prayer,
  (newVal) => {
    if (newVal) {
      content.value = newVal.content || ''
      visibility.value = newVal.visibility || 'public'
      isAnonymous.value = Boolean(newVal.isAnonymous)
    }
  },
  { immediate: true }
)

const handleClose = () => {
  errorMessage.value = ''
  emit('close')
}

const handleSubmit = async () => {
  if (!isValid.value || isSubmitting.value || !props.prayer) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    const response = await api.put(`/prayers/${props.prayer.id}`, {
      content: content.value.trim(),
      visibility: visibility.value,
      is_anonymous: isAnonymous.value,
    })

    emit('updated', response.data?.data)
    handleClose()
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Gagal memperbarui permohonan doa.'
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
      v-if="isOpen && prayer"
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
          v-if="isOpen && prayer"
          class="w-full max-w-lg h-full bg-white border-l border-zinc-200 shadow-2xl relative flex flex-col p-6 sm:p-8 overflow-y-auto"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between pb-4 border-b border-zinc-100 mb-6 shrink-0">
            <div>
              <h2 class="font-serif-custom text-2xl font-normal text-black">
                Edit Permohonan Doa
              </h2>
              <p class="text-xs text-zinc-500 mt-1">
                Perbarui pergumulan doa Anda.
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

              <!-- Error Alert -->
              <div v-if="errorMessage" class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-medium text-red-700">
                {{ errorMessage }}
              </div>
              
              <!-- Textarea Content -->
              <div>
                <label for="edit-prayer-content" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">
                  Isi Permohonan Doa
                </label>
                <textarea
                  id="edit-prayer-content"
                  v-model="content"
                  rows="6"
                  required
                  placeholder="Tuliskan permohonan doa Anda..."
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

              <!-- Visibility Selection Chips -->
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2.5">
                  Tingkat Privasi / Visibility
                </label>
                
                <div class="flex flex-wrap gap-2">
                  <button
                    type="button"
                    @click="visibility = 'public'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-medium border transition-all cursor-pointer select-none"
                    :class="[
                      visibility === 'public'
                        ? 'bg-black text-white border-black shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    ]"
                  >
                    <Globe :size="14" />
                    <span>Publik</span>
                  </button>

                  <button
                    type="button"
                    @click="visibility = 'private'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-medium border transition-all cursor-pointer select-none"
                    :class="[
                      visibility === 'private'
                        ? 'bg-black text-white border-black shadow-xs'
                        : 'bg-white text-zinc-700 border-zinc-200 hover:border-zinc-300'
                    ]"
                  >
                    <Lock :size="14" />
                    <span>Pribadi</span>
                  </button>
                </div>
              </div>

              <!-- Anonymous Toggle -->
              <div class="pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                  <input
                    v-model="isAnonymous"
                    type="checkbox"
                    class="w-4 h-4 rounded border-zinc-300 text-black focus:ring-black cursor-pointer"
                  />
                  <span class="text-xs text-zinc-700 font-normal">Tampilkan secara anonim</span>
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
                :disabled="!isValid || isSubmitting"
                class="px-6 py-2.5 rounded-full bg-black hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-xs"
              >
                <Save :size="14" />
                <span>Simpan Perubahan</span>
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>
