<script setup lang="ts">
import { ref, computed } from 'vue'
import { ShieldCheck, Crown, User as UserIcon, UserMinus, ShieldAlert, MoreVertical } from 'lucide-vue-next'
import type { Community, CommunityMember } from '@/types'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const props = defineProps<{
  community: Community
}>()

const emit = defineEmits<{
  (e: 'refresh'): void
}>()

const authStore = useAuthStore()
const isProcessing = ref<string | null>(null)
const actionMessage = ref('')

const isOwner = computed(() => {
  return props.community.userRole === 'owner'
})

const isOwnerOrAdmin = computed(() => {
  return props.community.userRole === 'owner' || props.community.userRole === 'admin'
})

const handleRemoveMember = async (member: CommunityMember) => {
  const targetId = member.userId || member.id
  if (!confirm(`Apakah Anda yakin ingin mengeluarkan ${member.name} dari komunitas?`)) return

  isProcessing.value = member.id
  actionMessage.value = ''

  try {
    const res = await api.delete(`/groups/${props.community.id}/members/${targetId}`)
    if (res.data && res.data.success) {
      actionMessage.value = res.data.message || `${member.name} berhasil dikeluarkan.`
      emit('refresh')
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal mengeluarkan anggota.')
  } finally {
    isProcessing.value = null
  }
}

const handleToggleRole = async (member: CommunityMember) => {
  const targetId = member.userId || member.id
  const newRole = member.role === 'admin' ? 'member' : 'admin'
  const roleLabel = newRole === 'admin' ? 'Admin' : 'Anggota'

  if (!confirm(`Ubah peran ${member.name} menjadi ${roleLabel}?`)) return

  isProcessing.value = member.id
  actionMessage.value = ''

  try {
    const res = await api.put(`/groups/${props.community.id}/members/${targetId}/role`, {
      role: newRole,
    })
    if (res.data && res.data.success) {
      actionMessage.value = `Peran ${member.name} berhasil diubah.`
      emit('refresh')
    }
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal mengubah peran anggota.')
  } finally {
    isProcessing.value = null
  }
}
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

    <!-- Alert message -->
    <div v-if="actionMessage" class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
      {{ actionMessage }}
    </div>

    <!-- Members List Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div
        v-for="member in community.members"
        :key="member.id"
        class="bg-white rounded-2xl border border-zinc-200/90 p-4 shadow-2xs flex items-center justify-between gap-3"
      >
        <div class="flex items-center gap-3 min-w-0">
          <img
            v-if="member.avatar"
            :src="member.avatar"
            :alt="member.name"
            class="w-10 h-10 rounded-full object-cover border border-zinc-200 shrink-0"
          />
          <div v-else class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs shrink-0">
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

        <!-- Role Badges & Actions -->
        <div class="flex items-center gap-2 shrink-0">
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

          <!-- Owner Actions for Member -->
          <div v-if="member.role !== 'owner' && (String(member.userId || member.id) !== String(authStore.user?.id))" class="flex items-center gap-1 ml-1">
            <button
              v-if="isOwner"
              type="button"
              @click="handleToggleRole(member)"
              :disabled="isProcessing === member.id"
              class="p-1.5 text-zinc-400 hover:text-black hover:bg-zinc-100 rounded-lg transition-colors cursor-pointer"
              :title="member.role === 'admin' ? 'Jadikan Anggota Biasa' : 'Jadikan Admin'"
            >
              <ShieldCheck :size="14" :class="member.role === 'admin' ? 'text-emerald-600' : ''" />
            </button>

            <button
              v-if="isOwnerOrAdmin"
              type="button"
              @click="handleRemoveMember(member)"
              :disabled="isProcessing === member.id"
              class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
              title="Keluarkan dari Komunitas"
            >
              <UserMinus :size="14" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
