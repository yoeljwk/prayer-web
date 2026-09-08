<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Users, Lock, Globe, Check, Clock } from 'lucide-vue-next'
import type { Community } from '@/types'
import { toggleCommunityJoin } from '@/data/mockCommunities'

const props = defineProps<{
  community: Community
}>()

const handleJoinToggle = (e: Event) => {
  e.preventDefault()
  e.stopPropagation()
  toggleCommunityJoin(props.community.slug)
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-zinc-200/90 p-6 shadow-2xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
    <div>
      <!-- Top Row: Avatar & Public/Private Badge -->
      <div class="flex items-start justify-between gap-4 mb-4">
        <div class="relative">
          <img
            :src="community.avatar"
            :alt="community.name"
            class="w-14 h-14 rounded-2xl object-cover border border-zinc-100 shadow-2xs group-hover:scale-105 transition-transform duration-200"
          />
        </div>

        <span
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border"
          :class="
            community.visibility === 'public'
              ? 'bg-zinc-50 border-zinc-200 text-zinc-700'
              : 'bg-zinc-100 border-zinc-300 text-zinc-900 font-semibold'
          "
        >
          <Globe v-if="community.visibility === 'public'" :size="12" class="text-zinc-500" />
          <Lock v-else :size="12" class="text-zinc-700" />
          <span>{{ community.visibility === 'public' ? 'Publik' : 'Privat' }}</span>
        </span>
      </div>

      <!-- Community Name & Description -->
      <RouterLink :to="`/komunitas/${community.slug}`" class="block group/link">
        <h3 class="font-serif-custom text-xl font-normal text-black group-hover/link:underline underline-offset-4 tracking-tight mb-2">
          {{ community.name }}
        </h3>
      </RouterLink>

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

      <div class="flex items-center gap-2">
        <RouterLink
          :to="`/komunitas/${community.slug}`"
          class="text-xs font-semibold text-zinc-700 hover:text-black px-3 py-2 rounded-xl hover:bg-zinc-100 transition-colors cursor-pointer"
        >
          Lihat
        </RouterLink>

        <!-- Join Button with Local State -->
        <button
          type="button"
          @click="handleJoinToggle"
          class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer flex items-center gap-1.5 shrink-0"
          :class="{
            'bg-black text-white hover:bg-zinc-800 shadow-2xs': community.joinStatus === 'not_joined',
            'bg-zinc-100 text-zinc-800 border border-zinc-200 hover:bg-zinc-200': community.joinStatus === 'joined',
            'bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100': community.joinStatus === 'pending',
          }"
        >
          <template v-if="community.joinStatus === 'joined'">
            <Check :size="13" class="stroke-[2.5]" />
            <span>Sudah Bergabung</span>
          </template>
          <template v-else-if="community.joinStatus === 'pending'">
            <Clock :size="13" class="stroke-[2.5]" />
            <span>Permintaan Terkirim</span>
          </template>
          <template v-else>
            <span>Gabung</span>
          </template>
        </button>
      </div>
    </div>
  </div>
</template>
