<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Users, Lock, Check, KeyRound, ExternalLink } from 'lucide-vue-next'
import type { Community } from '@/types'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const props = defineProps<{
  community: Community
}>()

const emit = defineEmits<{
  (e: 'joined'): void
  (e: 'join-code'): void
}>()

const router = useRouter()
const authStore = useAuthStore()
const isSubmitting = ref(false)

const handleJoinPublicGroup = async () => {
  if (isSubmitting.value) return

  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }

  isSubmitting.value = true
  try {
    const res = await api.post(`/groups/${props.community.id}/join`)
    if (res.data && res.data.success) {
      emit('joined')
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal bergabung dengan komunitas.')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-zinc-200/90 p-6 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
    <div>
      <!-- Top Row: Avatar & Public/Private Badge -->
      <div class="flex items-start justify-between gap-4 mb-4">
        <div class="relative">
          <img
            :src="community.avatar || 'https://images.unsplash.com/photo-1544427920-c49ccfb85579?q=80&w=400&auto=format&fit=crop'"
            :alt="community.name"
            class="w-14 h-14 rounded-2xl object-cover border border-zinc-100 shadow-2xs group-hover:scale-105 transition-transform duration-200"
          />
        </div>

        <!-- Visibility Badge: always shown, simple -->
        <span
          class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-zinc-100 text-zinc-500"
        >
          <Lock v-if="community.visibility !== 'public'" :size="11" class="shrink-0" />
          <span>{{ community.visibility === 'public' ? 'Publik' : 'Privat' }}</span>
        </span>
      </div>

      <!-- Community Name (Not a link unless joined) -->
      <h3 class="font-serif-custom text-xl font-normal text-black tracking-tight mb-2">
        {{ community.name }}
      </h3>

      <p class="text-xs text-zinc-500 line-clamp-2 leading-relaxed mb-4 font-normal">
        {{ community.description }}
      </p>
    </div>

    <!-- Bottom Row: Member Count & Action Buttons -->
    <div class="pt-4 border-t border-zinc-100 flex items-center justify-between gap-3 mt-auto">
      <div class="flex items-center gap-1.5 text-xs text-zinc-500 font-medium">
        <Users :size="14" class="text-zinc-400" />
        <span>{{ community.memberCount }} anggota</span>
      </div>

      <div>
        <!-- Option 1: Joined -> Open Community Detail -->
        <router-link
          v-if="community.joinStatus === 'joined'"
          :to="`/komunitas/${community.slug}`"
          class="px-4 py-2.5 rounded-xl bg-black hover:bg-zinc-800 text-white font-semibold text-xs transition-all duration-200 cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs"
        >
          <span>Buka Komunitas</span>
          <ExternalLink :size="13" />
        </router-link>

        <!-- Option 2: Public & Not Joined -> Join Public Button -->
        <button
          v-else-if="community.visibility === 'public'"
          type="button"
          @click="handleJoinPublicGroup"
          :disabled="isSubmitting"
          class="px-4 py-2.5 rounded-xl bg-black hover:bg-zinc-800 text-white font-semibold text-xs transition-all duration-200 cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs disabled:opacity-50"
        >
          <Check :size="13" />
          <span>{{ isSubmitting ? 'Proses...' : 'Gabung Komunitas' }}</span>
        </button>

        <!-- Option 3: Private & Not Joined -> Join via Code Modal -->
        <button
          v-else
          type="button"
          @click="emit('join-code')"
          class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs transition-all duration-200 cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs"
        >
          <KeyRound :size="13" />
          <span>Gabung via Kode</span>
        </button>
      </div>
    </div>
  </div>
</template>
