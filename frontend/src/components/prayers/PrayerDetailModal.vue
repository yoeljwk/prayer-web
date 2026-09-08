<script setup lang="ts">
import { ref } from 'vue'
import { X, Heart, Check, Users, Send, CheckCircle2 } from 'lucide-vue-next'
import type { PrayerRequest } from '@/types'

const props = defineProps<{
  isOpen: boolean
  prayer: PrayerRequest | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'toggle-pray', id: string): void
  (e: 'mark-answered', id: string): void
  (e: 'add-comment', prayerId: string, commentText: string, isAnon: boolean): void
}>()

const commentContent = ref('')
const isCommentAnon = ref(false)

const handleClose = () => {
  commentContent.value = ''
  isCommentAnon.value = false
  emit('close')
}

const handleAddComment = () => {
  if (!commentContent.value.trim() || !props.prayer) return
  emit('add-comment', props.prayer.id, commentContent.value.trim(), isCommentAnon.value)
  commentContent.value = ''
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
          <!-- Side Panel Header -->
          <div class="flex items-center justify-between pb-4 border-b border-zinc-100 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
              <!-- User Avatar -->
              <img
                v-if="!prayer.isAnonymous && prayer.authorAvatar"
                :src="prayer.authorAvatar"
                :alt="prayer.authorName"
                class="w-10 h-10 rounded-full object-cover border border-zinc-200 shrink-0"
              />
              <div
                v-else
                class="w-10 h-10 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-xs font-semibold text-zinc-600 shrink-0"
              >
                {{ prayer.isAnonymous ? 'A' : prayer.authorName.charAt(0) }}
              </div>

              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <h3 class="text-sm font-semibold text-black truncate">
                    {{ prayer.isAnonymous ? 'Anonim' : prayer.authorName }}
                  </h3>

                  <span
                    v-if="prayer.visibility === 'group' && prayer.groupName"
                    class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full truncate"
                  >
                    <Users :size="11" class="shrink-0" />
                    <span class="truncate">{{ prayer.groupName }}</span>
                  </span>
                </div>

                <p class="text-xs text-zinc-400 font-normal">
                  {{ prayer.createdAt }}
                </p>
              </div>
            </div>

            <button
              type="button"
              @click="handleClose"
              class="text-zinc-400 hover:text-black p-1.5 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer shrink-0"
              title="Tutup panel"
            >
              <X :size="20" />
            </button>
          </div>

          <!-- Main Content Body (Ultra Minimalist, Frameless Design) -->
          <div class="flex-1 py-6 space-y-6 overflow-y-auto pr-1">
            
            <!-- Prayer Text (Frameless Typography) -->
            <p class="font-serif-custom text-lg text-zinc-900 leading-relaxed whitespace-pre-line font-normal">
              {{ prayer.content }}
            </p>

            <!-- Answered Banner (If Answered) -->
            <div
              v-if="prayer.status === 'answered'"
              class="py-3 px-4 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-900 text-xs flex items-center gap-2.5"
            >
              <CheckCircle2 :size="16" class="text-emerald-700 shrink-0" />
              <span>Doa Telah Terjawab! Puji Tuhan atas kasih dan pertolongan-Nya.</span>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center justify-between gap-3 pt-2 pb-4 border-b border-zinc-100">
              <button
                type="button"
                @click="emit('toggle-pray', prayer.id)"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-semibold text-xs transition-all cursor-pointer select-none"
                :class="[
                  prayer.hasPrayed
                    ? 'bg-black text-white shadow-xs'
                    : 'bg-zinc-100 hover:bg-zinc-200 text-zinc-800'
                ]"
              >
                <Heart
                  :size="14"
                  :class="prayer.hasPrayed ? 'fill-white stroke-white' : 'stroke-zinc-600'"
                />
                <span>{{ prayer.hasPrayed ? 'Sudah Didoakan' : 'Saya Mendoakan' }}</span>
                <span
                  class="ml-1 text-[11px] px-1.5 py-0.2 rounded-md"
                  :class="prayer.hasPrayed ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-700'"
                >
                  {{ prayer.prayerCount }}
                </span>
              </button>

              <button
                v-if="prayer.isOwner && prayer.status !== 'answered'"
                type="button"
                @click="emit('mark-answered', prayer.id)"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-xs border border-emerald-300 transition-colors cursor-pointer"
              >
                <Check :size="14" class="stroke-[2.5]" />
                <span>Tandai Terjawab</span>
              </button>
            </div>

            <!-- Comments Section (Frameless Minimalist List) -->
            <div>
              <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-4">
                Pesan & Dukungan Doa ({{ prayer.comments.length }})
              </h4>

              <!-- Comment Form -->
              <form @submit.prevent="handleAddComment" class="mb-6 space-y-3">
                <textarea
                  v-model="commentContent"
                  rows="2"
                  placeholder="Tuliskan pesan menguatkan atau kata-kata doa..."
                  class="w-full p-3.5 rounded-xl border border-zinc-200 text-xs text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white resize-none"
                ></textarea>

                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                      v-model="isCommentAnon"
                      type="checkbox"
                      class="w-3.5 h-3.5 rounded border-zinc-300 text-black focus:ring-black cursor-pointer"
                    />
                    <span class="text-[11px] text-zinc-500 font-medium">Kirim sebagai Anonim</span>
                  </label>

                  <button
                    type="submit"
                    :disabled="!commentContent.trim()"
                    class="px-4 py-2 rounded-full bg-black hover:bg-zinc-800 disabled:bg-zinc-300 text-white font-semibold text-xs transition-colors flex items-center gap-1.5 cursor-pointer shadow-xs"
                  >
                    <Send :size="13" />
                    <span>Kirim Pesan</span>
                  </button>
                </div>
              </form>

              <!-- Comments List (Clean Divider List) -->
              <div v-if="prayer.comments.length > 0" class="divide-y divide-zinc-100">
                <div
                  v-for="comment in prayer.comments"
                  :key="comment.id"
                  class="py-3.5 space-y-1"
                >
                  <div class="flex items-center justify-between">
                    <span class="font-semibold text-xs text-zinc-900">
                      {{ comment.isAnonymous ? 'Anonim' : comment.authorName }}
                    </span>
                    <span class="text-[11px] text-zinc-400 font-normal">
                      {{ comment.createdAt }}
                    </span>
                  </div>
                  <p class="text-xs text-zinc-700 leading-relaxed font-normal">
                    {{ comment.content }}
                  </p>
                </div>
              </div>

              <!-- Empty Comments State -->
              <div v-else class="text-center py-6 text-xs text-zinc-400 italic">
                Belum ada pesan doa. Jadilah yang pertama memberikan kata-kata penguatan.
              </div>
            </div>

          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>
