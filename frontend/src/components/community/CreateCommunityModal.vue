<script setup lang="ts">
import { ref, watch } from 'vue'
import { X, Globe, Lock, Upload, Image as ImageIcon } from 'lucide-vue-next'
import type { CommunityVisibility } from '@/types'
import { addCommunity } from '@/data/mockCommunities'

const props = defineProps<{
  isOpen: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'created', slug: string): void
}>()

const name = ref('')
const description = ref('')
const visibility = ref<CommunityVisibility>('public')
const maxMembers = ref<number | undefined>(undefined)
const avatarUrl = ref('https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80')

// Avatar presets
const presetAvatars = [
  'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=150&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=150&auto=format&fit=crop&q=80',
]

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    avatarUrl.value = URL.createObjectURL(file)
  }
}

const resetForm = () => {
  name.value = ''
  description.value = ''
  visibility.value = 'public'
  maxMembers.value = undefined
  avatarUrl.value = presetAvatars[0] || 'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80'
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

  const created = addCommunity({
    name: name.value.trim(),
    description: description.value.trim(),
    avatar: avatarUrl.value,
    visibility: visibility.value,
    maxMembers: maxMembers.value || undefined,
  })

  emit('created', created.slug)
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
                    Buat Komunitas
                  </h2>
                  <p class="text-xs text-zinc-500 mt-1">
                    Bentuk wadah persekutuan dan doa bersama komunitas baru.
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
                <!-- Avatar Preview & Preset Selection -->
                <div>
                  <label class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Foto Avatar Komunitas
                  </label>
                  <div class="flex items-center gap-4 mb-3">
                    <img
                      :src="avatarUrl"
                      alt="Avatar Preview"
                      class="w-16 h-16 rounded-2xl object-cover border border-zinc-200 shadow-2xs"
                    />
                    <label
                      class="cursor-pointer bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs px-4 py-2.5 rounded-xl border border-zinc-200 flex items-center gap-2 transition-colors"
                    >
                      <Upload :size="14" />
                      <span>Unggah Foto</span>
                      <input type="file" accept="image/*" class="hidden" @change="handleFileUpload" />
                    </label>
                  </div>

                  <p class="text-[11px] text-zinc-400 mb-2">Atau pilih gambar preset di bawah:</p>
                  <div class="flex items-center gap-2">
                    <button
                      v-for="(img, idx) in presetAvatars"
                      :key="idx"
                      type="button"
                      @click="avatarUrl = img"
                      class="w-10 h-10 rounded-xl overflow-hidden border-2 transition-all cursor-pointer"
                      :class="avatarUrl === img ? 'border-black scale-105 shadow-xs' : 'border-transparent opacity-60 hover:opacity-100'"
                    >
                      <img :src="img" alt="Preset" class="w-full h-full object-cover" />
                    </button>
                  </div>
                </div>

                <!-- Nama Komunitas -->
                <div>
                  <label for="comm-name" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Nama Komunitas <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="comm-name"
                    v-model="name"
                    type="text"
                    required
                    placeholder="Contoh: Doa Malam Syafaat"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white"
                  />
                </div>

                <!-- Deskripsi -->
                <div>
                  <label for="comm-desc" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Deskripsi Singkat <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    id="comm-desc"
                    v-model="description"
                    rows="3"
                    required
                    placeholder="Jelaskan tujuan dan aktivitas komunitas ini..."
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white resize-none"
                  ></textarea>
                </div>

                <!-- Visibility (Public / Private) -->
                <div>
                  <label class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Privasi Komunitas
                  </label>
                  <div class="grid grid-cols-2 gap-3">
                    <button
                      type="button"
                      @click="visibility = 'public'"
                      class="p-4 rounded-xl border text-left transition-all cursor-pointer flex flex-col justify-between space-y-2"
                      :class="
                        visibility === 'public'
                          ? 'border-black bg-zinc-50 shadow-2xs'
                          : 'border-zinc-200 bg-white hover:border-zinc-300'
                      "
                    >
                      <div class="flex items-center gap-2">
                        <Globe :size="16" :class="visibility === 'public' ? 'text-black' : 'text-zinc-400'" />
                        <span class="text-xs font-bold text-black">Publik</span>
                      </div>
                      <p class="text-[11px] text-zinc-500 leading-normal">
                        Siapa saja dapat bergabung dan melihat permohonan doa.
                      </p>
                    </button>

                    <button
                      type="button"
                      @click="visibility = 'private'"
                      class="p-4 rounded-xl border text-left transition-all cursor-pointer flex flex-col justify-between space-y-2"
                      :class="
                        visibility === 'private'
                          ? 'border-black bg-zinc-50 shadow-2xs'
                          : 'border-zinc-200 bg-white hover:border-zinc-300'
                      "
                    >
                      <div class="flex items-center gap-2">
                        <Lock :size="16" :class="visibility === 'private' ? 'text-black' : 'text-zinc-400'" />
                        <span class="text-xs font-bold text-black">Privat</span>
                      </div>
                      <p class="text-[11px] text-zinc-500 leading-normal">
                        Memerlukan persetujuan admin untuk bergabung.
                      </p>
                    </button>
                  </div>
                </div>

                <!-- Max Member Optional -->
                <div>
                  <label for="comm-max" class="block text-xs font-semibold text-zinc-800 uppercase tracking-wider mb-2">
                    Maksimal Anggota <span class="text-zinc-400 font-normal font-sans">(Opsional)</span>
                  </label>
                  <input
                    id="comm-max"
                    v-model.number="maxMembers"
                    type="number"
                    min="2"
                    placeholder="Contoh: 50 (Biarkan kosong jika tidak dibatasi)"
                    class="w-full px-4 py-3 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white"
                  />
                </div>

                <!-- Submit & Cancel Actions -->
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
                    Buat Komunitas
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
