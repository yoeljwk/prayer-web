<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowLeft, Users, Globe, Lock, Check, Clock, Share2, Copy } from 'lucide-vue-next'
import type { Community } from '@/types'
import { toggleCommunityJoin } from '@/data/mockCommunities'

const props = defineProps<{
  community: Community
}>()

const isCopied = ref(false)

const handleShareInvite = () => {
  navigator.clipboard.writeText(window.location.href)
  isCopied.value = true
  setTimeout(() => {
    isCopied.value = false
  }, 2500)
}

const handleJoinToggle = () => {
  toggleCommunityJoin(props.community.slug)
}
</script>

<template>
  <div class="bg-white border-b border-zinc-200/80 pt-8 pb-10">
    <div class="max-w-5xl mx-auto px-6">
      
      <!-- Back Link -->
      <RouterLink
        to="/komunitas"
        class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 hover:text-black transition-colors mb-6 group cursor-pointer"
      >
        <ArrowLeft :size="16" class="group-hover:-translate-x-1 transition-transform" />
        <span>Kembali ke Daftar Komunitas</span>
      </RouterLink>

      <!-- Main Header Card Content -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-start gap-5">
          <img
            :src="community.avatar"
            :alt="community.name"
            class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border border-zinc-200/90 shadow-2xs shrink-0"
          />

          <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2.5">
              <h1 class="font-serif-custom text-3xl sm:text-4xl font-normal text-black tracking-tight">
                {{ community.name }}
              </h1>

              <span
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border"
                :class="
                  community.visibility === 'public'
                    ? 'bg-zinc-50 border-zinc-200 text-zinc-700'
                    : 'bg-zinc-100 border-zinc-300 text-zinc-900'
                "
              >
                <Globe v-if="community.visibility === 'public'" :size="12" class="text-zinc-500" />
                <Lock v-else :size="12" class="text-zinc-700" />
                <span>{{ community.visibility === 'public' ? 'Publik' : 'Privat' }}</span>
              </span>
            </div>

            <p class="text-xs sm:text-sm text-zinc-600 font-normal leading-relaxed max-w-xl">
              {{ community.description }}
            </p>

            <div class="flex items-center gap-4 pt-1 text-xs text-zinc-500 font-medium">
              <div class="flex items-center gap-1.5">
                <Users :size="14" class="text-zinc-400" />
                <span>{{ community.memberCount }} anggota</span>
              </div>
              <span class="text-zinc-300">•</span>
              <span>Dibuat {{ community.createdAt }}</span>
            </div>
          </div>
        </div>

        <!-- Action Buttons: Share Invite & Join Button -->
        <div class="flex items-center gap-3 w-full sm:w-auto shrink-0 pt-2 sm:pt-0">
          <button
            type="button"
            @click="handleShareInvite"
            class="flex-1 sm:flex-initial px-4 py-3 rounded-xl border border-zinc-200 hover:border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold flex items-center justify-center gap-2 transition-all cursor-pointer shadow-2xs"
          >
            <Check v-if="isCopied" :size="15" class="text-emerald-600" />
            <Share2 v-else :size="15" />
            <span>{{ isCopied ? 'Tersalin!' : 'Bagikan Undangan' }}</span>
          </button>

          <button
            type="button"
            @click="handleJoinToggle"
            class="flex-1 sm:flex-initial px-6 py-3 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 shrink-0 shadow-2xs"
            :class="{
              'bg-black text-white hover:bg-zinc-800': community.joinStatus === 'not_joined',
              'bg-zinc-100 text-zinc-800 border border-zinc-200 hover:bg-zinc-200': community.joinStatus === 'joined',
              'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100': community.joinStatus === 'pending',
            }"
          >
            <template v-if="community.joinStatus === 'joined'">
              <Check :size="14" class="stroke-[2.5]" />
              <span>Sudah Bergabung</span>
            </template>
            <template v-else-if="community.joinStatus === 'pending'">
              <Clock :size="14" class="stroke-[2.5]" />
              <span>Permintaan Terkirim</span>
            </template>
            <template v-else>
              <span>Gabung Komunitas</span>
            </template>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
