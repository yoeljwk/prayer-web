<script setup lang="ts">
import { ref, watch } from 'vue'
import { X, KeyRound, ArrowRight } from 'lucide-vue-next'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'joined', slug: string): void
}>()

const authStore = useAuthStore()

const inviteCode = ref('')
const isSubmitting = ref(false)
const errorMessage = ref('')

const resetForm = () => {
  inviteCode.value = ''
  errorMessage.value = ''
  isSubmitting.value = false
}

watch(
  () => props.isOpen,
  (open) => {
    if (!open) {
      resetForm()
    }
  }
)

const handleSubmit = async () => {
  if (!inviteCode.value.trim() || isSubmitting.value) return

  if (!authStore.isAuthenticated) {
    errorMessage.value = 'Silakan masuk (login) terlebih dahulu untuk bergabung dengan komunitas.'
    return
  }

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    const res = await api.post('/groups/join-by-code', {
      invite_code: inviteCode.value.trim(),
    })

    if (res.data && res.data.success && res.data.data) {
      const slug = res.data.data.slug
      emit('joined', slug)
      emit('close')
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Kode undangan tidak valid atau komunitas tidak ditemukan.'
  } finally {
    isSubmitting.value = false
  }
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

        <div class="fixed inset-0 flex items-center justify-center p-4">
          <Transition
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transform transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
            appear
          >
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 relative">
              <!-- Close Button -->
              <button
                type="button"
                @click="emit('close')"
                class="absolute right-5 top-5 p-1.5 rounded-lg text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer"
              >
                <X :size="20" />
              </button>

              <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center text-zinc-800">
                  <KeyRound :size="20" />
                </div>
                <div>
                  <h3 class="font-serif-custom text-xl font-normal text-black">
                    Gabung Komunitas Privat
                  </h3>
                  <p class="text-xs text-zinc-500">
                    Masukkan kode undangan yang diberikan oleh pengelola komunitas.
                  </p>
                </div>
              </div>

              <form @submit.prevent="handleSubmit" class="space-y-4 pt-2">
                <div v-if="errorMessage" class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs font-medium text-red-700">
                  {{ errorMessage }}
                </div>

                <div>
                  <label for="invite-code-input" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">
                    Kode Undangan Komunitas
                  </label>
                  <input
                    id="invite-code-input"
                    v-model="inviteCode"
                    type="text"
                    required
                    placeholder="Contoh: ABC12345"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black font-mono uppercase tracking-wider bg-white"
                  />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                  <button
                    type="button"
                    @click="emit('close')"
                    class="px-4 py-2.5 rounded-full border border-zinc-200 text-zinc-700 text-xs font-semibold hover:bg-zinc-50 transition-colors cursor-pointer"
                  >
                    Batal
                  </button>

                  <button
                    type="submit"
                    :disabled="!inviteCode.trim() || isSubmitting"
                    class="px-5 py-2.5 rounded-full bg-black hover:bg-zinc-800 disabled:opacity-40 text-white font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer shadow-xs"
                  >
                    <span>Masuk Komunitas</span>
                    <ArrowRight :size="14" />
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
