<script setup lang="ts">
import { ShieldCheck, Crown, User as UserIcon } from 'lucide-vue-next'
import type { Community } from '@/types'

const props = defineProps<{
  community: Community
}>()
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-serif-custom text-xl font-normal text-black tracking-tight">
          Daftar Anggota
        </h3>
        <p class="text-xs text-zinc-500">
          {{ community.memberCount }} orang bergabung dalam {{ community.name }}.
        </p>
      </div>
    </div>

    <!-- Members List Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div
        v-for="member in community.members"
        :key="member.id"
        class="bg-white rounded-2xl border border-zinc-200/90 p-4 shadow-2xs flex items-center justify-between gap-3"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
            {{ member.name.charAt(0) }}
          </div>

          <div class="truncate">
            <h4 class="text-sm font-semibold text-black truncate">
              {{ member.name }}
            </h4>
            <p class="text-[11px] text-zinc-400 truncate">
              @{{ member.username || 'user' }} • Bergabung {{ member.joinedAt }}
            </p>
          </div>
        </div>

        <!-- Role Badges -->
        <div class="shrink-0">
          <span
            v-if="member.role === 'owner'"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-black text-white shadow-2xs"
          >
            <Crown :size="11" class="text-amber-300 fill-amber-300" />
            <span>Pemilik</span>
          </span>

          <span
            v-else-if="member.role === 'admin'"
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-zinc-100 text-zinc-800 border border-zinc-300"
          >
            <ShieldCheck :size="12" class="text-zinc-600" />
            <span>Admin</span>
          </span>

          <span
            v-else
            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-zinc-50 text-zinc-600 border border-zinc-200"
          >
            <UserIcon :size="11" class="text-zinc-400" />
            <span>Anggota</span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
