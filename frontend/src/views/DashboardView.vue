<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  User as UserIcon,
  Plus,
  RefreshCw,
  Users,
  Globe,
  Lock,
  Copy,
  Check,
  Pencil,
  Trash2,
  LogOut,
  ExternalLink,
  Shield,
  HeartHandshake,
} from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import { useQueryState } from '@/composables/useQueryState'
import api from '@/services/api'
import type { PrayerRequest, Community } from '@/types'
import PrayerCard from '@/components/prayers/PrayerCard.vue'
import SharePrayerModal from '@/components/prayers/SharePrayerModal.vue'
import PrayerDetailModal from '@/components/prayers/PrayerDetailModal.vue'
import EditPrayerModal from '@/components/prayers/EditPrayerModal.vue'
import PrayerSkeleton from '@/components/prayers/PrayerSkeleton.vue'
import CreateCommunityModal from '@/components/community/CreateCommunityModal.vue'
import EditCommunityModal from '@/components/community/EditCommunityModal.vue'

const router = useRouter()
const authStore = useAuthStore()

// Navigation state synced with URL query params (?category=, ?tab=, ?subtab=)
const mainCategory = useQueryState<'prayers' | 'communities'>('category', 'prayers')
const activeTab = useQueryState<'my-prayers' | 'supported-prayers'>('tab', 'my-prayers')
const communitySubTab = useQueryState<'created' | 'joined'>('subtab', 'created')

const isLoading = ref(true)
const isCommunityLoading = ref(false)

// Modals state
const isShareModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const isEditModalOpen = ref(false)
const isCreateCommunityModalOpen = ref(false)
const isEditCommunityModalOpen = ref(false)

const selectedPrayer = ref<PrayerRequest | null>(null)
const editingPrayer = ref<PrayerRequest | null>(null)
const selectedGroupForEdit = ref<Community | null>(null)
const copiedCodeId = ref<string | number | null>(null)

const stats = ref({
  total_prayers: 0,
  answered_prayers: 0,
  total_supports_received: 0,
})

const myPrayers = ref<PrayerRequest[]>([])
const supportedPrayers = ref<PrayerRequest[]>([])
const userCommunities = ref<any[]>([])

const myCreatedGroups = computed(() => {
  const currentUserId = authStore.user?.id
  if (!currentUserId) return []
  return userCommunities.value.filter(
    (g) => String(g.created_by) === String(currentUserId) || String(g.creator?.id) === String(currentUserId)
  )
})

const myJoinedGroups = computed(() => {
  const currentUserId = authStore.user?.id
  if (!currentUserId) return userCommunities.value
  return userCommunities.value.filter(
    (g) => String(g.created_by) !== String(currentUserId) && String(g.creator?.id) !== String(currentUserId)
  )
})

const formatTimeAgo = (dateStr: string) => {
  if (!dateStr) return 'Baru saja'
  const date = new Date(dateStr)
  if (isNaN(date.getTime())) return dateStr
  const now = new Date()
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000)

  if (diffInSeconds < 60) return 'Baru saja'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit yang lalu`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`
  return `${Math.floor(diffInSeconds / 86400)} hari yang lalu`
}

const fetchDashboardData = async () => {
  isLoading.value = true
  try {
    const [myPrayersRes, supportsRes] = await Promise.all([
      api.get('/user/prayers'),
      api.get('/user/supports'),
    ])

    if (myPrayersRes.data && myPrayersRes.data.success) {
      const data = myPrayersRes.data.data
      stats.value = data.stats || {
        total_prayers: 0,
        answered_prayers: 0,
        total_supports_received: 0,
      }

      if (Array.isArray(data.prayers)) {
        myPrayers.value = data.prayers.map((item: any) => ({
          id: String(item.id),
          authorName: item.is_anonymous ? 'Anonim' : (authStore.user?.name || authStore.user?.username || 'Saya'),
          authorAvatar: item.is_anonymous ? undefined : (authStore.user?.avatar || undefined),
          isAnonymous: Boolean(item.is_anonymous),
          createdAt: formatTimeAgo(item.created_at),
          content: item.content,
          visibility: item.visibility,
          groupName: item.group?.name,
          status: item.status || 'active',
          prayerCount: item.supports_count || 0,
          hasPrayed: Boolean(item.has_prayed),
          isOwner: true,
          comments: (item.comments || []).map((c: any) => ({
            id: String(c.id),
            authorName: c.is_anonymous ? 'Anonim' : (c.user?.name || c.user?.username || 'Jemaat'),
            authorAvatar: c.is_anonymous ? undefined : (c.user?.avatar || undefined),
            isAnonymous: Boolean(c.is_anonymous),
            createdAt: formatTimeAgo(c.created_at),
            content: c.content,
          })),
        }))
      }
    }

    if (supportsRes.data && supportsRes.data.success) {
      const items = supportsRes.data.data
      if (Array.isArray(items)) {
        supportedPrayers.value = items.map((item: any) => ({
          id: String(item.id),
          authorName: item.is_anonymous ? 'Anonim' : (item.user?.name || item.user?.username || 'Jemaat'),
          authorAvatar: item.is_anonymous ? undefined : (item.user?.avatar || undefined),
          isAnonymous: Boolean(item.is_anonymous),
          createdAt: formatTimeAgo(item.created_at),
          content: item.content,
          visibility: item.visibility,
          groupName: item.group?.name,
          status: item.status || 'active',
          prayerCount: item.supports_count || 0,
          hasPrayed: true,
          isOwner: item.user_id === authStore.user?.id,
          comments: (item.comments || []).map((c: any) => ({
            id: String(c.id),
            authorName: c.is_anonymous ? 'Anonim' : (c.user?.name || c.user?.username || 'Jemaat'),
            authorAvatar: c.is_anonymous ? undefined : (c.user?.avatar || undefined),
            isAnonymous: Boolean(c.is_anonymous),
            createdAt: formatTimeAgo(c.created_at),
            content: c.content,
          })),
        }))
      }
    }
  } catch (err) {
    console.error('Failed to load dashboard data:', err)
  } finally {
    isLoading.value = false
  }
}

const fetchUserCommunities = async () => {
  isCommunityLoading.value = true
  try {
    const res = await api.get('/groups', { params: { joined: true } })
    if (res.data && res.data.success) {
      userCommunities.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load user communities:', err)
  } finally {
    isCommunityLoading.value = false
  }
}

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  fetchDashboardData()
  fetchUserCommunities()
})

const handleTogglePray = async (id: string) => {
  const item = [...myPrayers.value, ...supportedPrayers.value].find((p) => p.id === id)
  if (!item) return

  if (item.hasPrayed) {
    item.hasPrayed = false
    item.prayerCount = Math.max(0, item.prayerCount - 1)
    try {
      await api.delete(`/prayers/${id}/support`)
      fetchDashboardData()
    } catch (e) {
      console.error(e)
    }
  } else {
    item.hasPrayed = true
    item.prayerCount += 1
    try {
      await api.post(`/prayers/${id}/support`)
      fetchDashboardData()
    } catch (e) {
      console.error(e)
    }
  }
}

const handleOpenDetail = (id: string) => {
  const item = [...myPrayers.value, ...supportedPrayers.value].find((p) => p.id === id)
  if (item) {
    selectedPrayer.value = item
    isDetailModalOpen.value = true
  }
}

const handleMarkAnswered = async (id: string) => {
  try {
    await api.put(`/prayers/${id}`, { status: 'answered', answered_at: new Date().toISOString() })
    fetchDashboardData()
  } catch (err) {
    console.error('Failed to mark prayer as answered:', err)
  }
}

const handleDeletePrayer = async (id: string) => {
  if (!confirm('Apakah Anda yakin ingin menghapus permohonan doa ini?')) return
  try {
    await api.delete(`/prayers/${id}`)
    fetchDashboardData()
  } catch (err) {
    console.error('Failed to delete prayer:', err)
  }
}

const handleOpenEdit = (prayer: PrayerRequest) => {
  editingPrayer.value = prayer
  isEditModalOpen.value = true
}

const handleAddComment = async (prayerId: string, commentText: string, isAnon: boolean) => {
  const item = [...myPrayers.value, ...supportedPrayers.value].find((p) => p.id === prayerId)
  if (!item) return

  try {
    const res = await api.post(`/prayers/${prayerId}/comments`, {
      content: commentText,
      is_anonymous: isAnon,
    })

    if (res.data && res.data.success) {
      const c = res.data.data
      item.comments.unshift({
        id: String(c.id),
        authorName: c.is_anonymous ? 'Anonim' : (c.user?.name || c.user?.username || authStore.user?.name || 'Saya'),
        authorAvatar: c.is_anonymous ? undefined : (c.user?.avatar || authStore.user?.avatar || undefined),
        isAnonymous: Boolean(c.is_anonymous),
        createdAt: 'Baru saja',
        content: c.content,
      })
    }
  } catch (err) {
    console.error('Failed to post comment:', err)
  }
}

// Community Action Handlers
const handleOpenEditGroup = (group: any) => {
  selectedGroupForEdit.value = group
  isEditCommunityModalOpen.value = true
}

const handleDeleteGroup = async (groupId: number | string) => {
  if (!confirm('Apakah Anda yakin ingin menghapus komunitas ini? Seluruh anggota dan aktivitas di dalamnya akan dihapus.')) return
  try {
    await api.delete(`/groups/${groupId}`)
    fetchUserCommunities()
  } catch (err) {
    console.error('Failed to delete group:', err)
  }
}

const handleLeaveGroup = async (groupId: number | string) => {
  if (!confirm('Apakah Anda yakin ingin keluar dari komunitas ini?')) return
  try {
    await api.post(`/groups/${groupId}/leave`)
    fetchUserCommunities()
  } catch (err) {
    console.error('Failed to leave group:', err)
  }
}

const copyInviteCode = (code: string, id: number | string) => {
  if (!code) return
  navigator.clipboard.writeText(code)
  copiedCodeId.value = id
  setTimeout(() => {
    copiedCodeId.value = null
  }, 2000)
}

const handleCommunityCreated = (slug: string) => {
  fetchUserCommunities()
  router.push(`/komunitas/${slug}`)
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen py-8 sm:py-12">
    <div class="max-w-4xl mx-auto px-6">
      
      <!-- Minimal Simple Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-zinc-200">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-black text-white flex items-center justify-center font-serif-custom text-xl font-medium shrink-0 shadow-xs">
            {{ authStore.user?.name?.charAt(0) || 'U' }}
          </div>
          <div>
            <h1 class="font-serif-custom text-2xl font-normal text-black">
              {{ authStore.user?.name }}
            </h1>
            <p class="text-xs text-zinc-500 font-normal">
              @{{ authStore.user?.username || 'user' }} • {{ stats.total_prayers }} Doa dibuat • {{ myCreatedGroups.length }} Komunitas dikelola
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2.5">
          <button
            v-if="mainCategory === 'prayers'"
            type="button"
            @click="isShareModalOpen = true"
            class="inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl transition-colors cursor-pointer shadow-xs"
          >
            <Plus :size="15" />
            <span>Bagikan Doa</span>
          </button>
          <button
            v-else
            type="button"
            @click="isCreateCommunityModalOpen = true"
            class="inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-2.5 rounded-xl transition-colors cursor-pointer shadow-xs"
          >
            <Plus :size="15" />
            <span>Buat Komunitas</span>
          </button>
        </div>
      </div>

      <!-- Top Section Category Selector (Permohonan Doa vs Komunitas Saya) -->
      <div class="flex items-center gap-3 mb-8 bg-zinc-200/60 p-1.5 rounded-2xl w-fit">
        <button
          type="button"
          @click="mainCategory = 'prayers'"
          class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer select-none"
          :class="
            mainCategory === 'prayers'
              ? 'bg-white text-black shadow-xs'
              : 'text-zinc-600 hover:text-black'
          "
        >
          <HeartHandshake :size="15" />
          <span>Permohonan Doa</span>
        </button>

        <button
          type="button"
          @click="mainCategory = 'communities'"
          class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer select-none"
          :class="
            mainCategory === 'communities'
              ? 'bg-white text-black shadow-xs'
              : 'text-zinc-600 hover:text-black'
          "
        >
          <Users :size="15" />
          <span>Komunitas Saya ({{ userCommunities.length }})</span>
        </button>
      </div>

      <!-- SECTION 1: PERMOHONAN DOA -->
      <div v-if="mainCategory === 'prayers'">
        <!-- Clean Tab Switcher -->
        <div class="flex items-center justify-between gap-4 mb-6">
          <div class="flex items-center gap-6">
            <button
              type="button"
              @click="activeTab = 'my-prayers'"
              class="pb-2 text-sm font-semibold transition-all relative cursor-pointer select-none"
              :class="activeTab === 'my-prayers' ? 'text-black font-bold' : 'text-zinc-400 hover:text-zinc-700'"
            >
              <span>Doa Saya ({{ myPrayers.length }})</span>
              <span v-if="activeTab === 'my-prayers'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></span>
            </button>

            <button
              type="button"
              @click="activeTab = 'supported-prayers'"
              class="pb-2 text-sm font-semibold transition-all relative cursor-pointer select-none"
              :class="activeTab === 'supported-prayers' ? 'text-black font-bold' : 'text-zinc-400 hover:text-zinc-700'"
            >
              <span>Doa yang Didoakan ({{ supportedPrayers.length }})</span>
              <span v-if="activeTab === 'supported-prayers'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></span>
            </button>
          </div>

          <button
            type="button"
            @click="fetchDashboardData"
            title="Refresh data"
            class="p-2 text-zinc-400 hover:text-black transition-colors cursor-pointer shrink-0"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': isLoading }" />
          </button>
        </div>

        <!-- Tab Content Area -->
        <div>
          <PrayerSkeleton v-if="isLoading" />

          <!-- Tab 1: Doa Saya -->
          <div v-else-if="activeTab === 'my-prayers'">
            <div v-if="myPrayers.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div v-for="prayer in myPrayers" :key="prayer.id" class="space-y-2">
                <PrayerCard
                  :prayer="prayer"
                  @toggle-pray="handleTogglePray"
                  @open-detail="handleOpenDetail"
                />
                <div class="flex items-center justify-end gap-2 px-1">
                  <button
                    v-if="prayer.status !== 'answered'"
                    type="button"
                    @click="handleMarkAnswered(prayer.id)"
                    class="text-[11px] font-semibold text-emerald-700 hover:underline cursor-pointer"
                  >
                    ✓ Tandai Dijawab
                  </button>
                  <button
                    type="button"
                    @click="handleOpenEdit(prayer)"
                    class="text-[11px] font-semibold text-zinc-700 hover:underline cursor-pointer ml-3"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    @click="handleDeletePrayer(prayer.id)"
                    class="text-[11px] font-semibold text-red-600 hover:underline cursor-pointer ml-3"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            </div>

            <div v-else class="bg-white rounded-xl p-10 text-center border border-zinc-200 shadow-xs my-4 space-y-3">
              <h3 class="font-serif-custom text-xl text-black font-normal">Belum ada permohonan doa</h3>
              <p class="text-xs text-zinc-500">Anda belum pernah membagikan permohonan doa.</p>
              <button
                type="button"
                @click="isShareModalOpen = true"
                class="text-xs font-semibold text-black underline underline-offset-4 cursor-pointer mt-1"
              >
                Bagikan Doa Pertama
              </button>
            </div>
          </div>

          <!-- Tab 2: Doa yang Didoakan -->
          <div v-else-if="activeTab === 'supported-prayers'">
            <div v-if="supportedPrayers.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <PrayerCard
                v-for="prayer in supportedPrayers"
                :key="prayer.id"
                :prayer="prayer"
                @toggle-pray="handleTogglePray"
                @open-detail="handleOpenDetail"
              />
            </div>

            <div v-else class="bg-white rounded-xl p-10 text-center border border-zinc-200 shadow-xs my-4 space-y-3">
              <h3 class="font-serif-custom text-xl text-black font-normal">Belum mendoakan doa jemaat</h3>
              <p class="text-xs text-zinc-500">Anda belum pernah mendukung permohonan doa jemaat lain.</p>
              <button
                type="button"
                @click="router.push('/permohonan-doa')"
                class="text-xs font-semibold text-black underline underline-offset-4 cursor-pointer mt-1"
              >
                Jelajahi Permohonan Doa
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: KOMUNITAS SAYA -->
      <div v-else-if="mainCategory === 'communities'">
        <div class="flex items-center justify-between gap-4 mb-6">
          <div class="flex items-center gap-6">
            <button
              type="button"
              @click="communitySubTab = 'created'"
              class="pb-2 text-sm font-semibold transition-all relative cursor-pointer select-none"
              :class="communitySubTab === 'created' ? 'text-black font-bold' : 'text-zinc-400 hover:text-zinc-700'"
            >
              <span>Dikelola Saya ({{ myCreatedGroups.length }})</span>
              <span v-if="communitySubTab === 'created'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></span>
            </button>

            <button
              type="button"
              @click="communitySubTab = 'joined'"
              class="pb-2 text-sm font-semibold transition-all relative cursor-pointer select-none"
              :class="communitySubTab === 'joined' ? 'text-black font-bold' : 'text-zinc-400 hover:text-zinc-700'"
            >
              <span>Komunitas Diikuti ({{ myJoinedGroups.length }})</span>
              <span v-if="communitySubTab === 'joined'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></span>
            </button>
          </div>

          <button
            type="button"
            @click="fetchUserCommunities"
            title="Refresh data komunitas"
            class="p-2 text-zinc-400 hover:text-black transition-colors cursor-pointer shrink-0"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': isCommunityLoading }" />
          </button>
        </div>

        <!-- Subtab 1: Komunitas yang Dikelola -->
        <div v-if="communitySubTab === 'created'">
          <div v-if="myCreatedGroups.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div
              v-for="group in myCreatedGroups"
              :key="group.id"
              class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between"
            >
              <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div class="flex items-center gap-3.5">
                    <img
                      :src="group.avatar || 'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80'"
                      :alt="group.name"
                      class="w-12 h-12 rounded-xl object-cover border border-zinc-100 shadow-2xs"
                    />
                    <div>
                      <h3 class="font-bold text-black text-base line-clamp-1 leading-snug">
                        {{ group.name }}
                      </h3>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-500">
                          <Users :size="12" />
                          {{ group.members_count || group.memberCount || 1 }} Anggota
                        </span>
                        <span class="text-zinc-300">•</span>
                        <span
                          class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md"
                          :class="
                            group.visibility === 'public'
                              ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                              : 'bg-amber-50 text-amber-700 border border-amber-200'
                          "
                        >
                          <component :is="group.visibility === 'public' ? Globe : Lock" :size="10" />
                          {{ group.visibility === 'public' ? 'Publik' : 'Privat' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <span class="inline-flex items-center gap-1 text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-1 rounded-lg border border-zinc-200 shrink-0">
                    <Shield :size="11" />
                    Owner
                  </span>
                </div>

                <p class="text-xs text-zinc-600 line-clamp-2 leading-relaxed mb-4">
                  {{ group.description }}
                </p>

                <!-- Invite Code Box for Private/Public groups -->
                <div v-if="group.invite_code" class="mb-4 p-2.5 rounded-xl bg-zinc-50 border border-zinc-200 flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2 min-w-0">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider shrink-0">Kode Undangan:</span>
                    <span class="font-mono text-xs font-bold text-black truncate select-all">{{ group.invite_code }}</span>
                  </div>
                  <button
                    type="button"
                    @click="copyInviteCode(group.invite_code, group.id)"
                    class="p-1.5 text-zinc-500 hover:text-black rounded-lg hover:bg-zinc-200/60 transition-colors cursor-pointer shrink-0"
                    title="Salin Kode Undangan"
                  >
                    <component :is="copiedCodeId === group.id ? Check : Copy" :size="14" :class="copiedCodeId === group.id ? 'text-emerald-600' : ''" />
                  </button>
                </div>
              </div>

              <!-- Card Actions -->
              <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-2 mt-2">
                <router-link
                  :to="`/komunitas/${group.slug}`"
                  class="inline-flex items-center gap-1.5 text-xs font-bold text-black hover:text-zinc-700 transition-colors"
                >
                  <span>Buka Komunitas</span>
                  <ExternalLink :size="13" />
                </router-link>

                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    @click="handleOpenEditGroup(group)"
                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-700 hover:text-black hover:bg-zinc-100 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                  >
                    <Pencil :size="12" />
                    <span>Edit</span>
                  </button>
                  <button
                    type="button"
                    @click="handleDeleteGroup(group.id)"
                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                  >
                    <Trash2 :size="12" />
                    <span>Hapus</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="bg-white rounded-2xl p-10 text-center border border-zinc-200 shadow-xs my-4 space-y-3">
            <h3 class="font-serif-custom text-xl text-black font-normal">Belum membuat komunitas</h3>
            <p class="text-xs text-zinc-500">Anda belum pernah membuat komunitas doa sendiri.</p>
            <button
              type="button"
              @click="isCreateCommunityModalOpen = true"
              class="inline-flex items-center gap-2 bg-black text-white font-semibold text-xs px-5 py-2.5 rounded-xl hover:bg-zinc-800 transition-all cursor-pointer mt-2"
            >
              <Plus :size="14" />
              <span>Buat Komunitas Baru</span>
            </button>
          </div>
        </div>

        <!-- Subtab 2: Komunitas yang Diikuti -->
        <div v-else-if="communitySubTab === 'joined'">
          <div v-if="myJoinedGroups.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div
              v-for="group in myJoinedGroups"
              :key="group.id"
              class="bg-white rounded-2xl p-5 border border-zinc-200/80 shadow-2xs hover:border-zinc-300 transition-all flex flex-col justify-between"
            >
              <div>
                <div class="flex items-start justify-between gap-3 mb-3">
                  <div class="flex items-center gap-3.5">
                    <img
                      :src="group.avatar || 'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80'"
                      :alt="group.name"
                      class="w-12 h-12 rounded-xl object-cover border border-zinc-100 shadow-2xs"
                    />
                    <div>
                      <h3 class="font-bold text-black text-base line-clamp-1 leading-snug">
                        {{ group.name }}
                      </h3>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-500">
                          <Users :size="12" />
                          {{ group.members_count || group.memberCount || 1 }} Anggota
                        </span>
                        <span class="text-zinc-300">•</span>
                        <span
                          class="inline-flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md"
                          :class="
                            group.visibility === 'public'
                              ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                              : 'bg-amber-50 text-amber-700 border border-amber-200'
                          "
                        >
                          <component :is="group.visibility === 'public' ? Globe : Lock" :size="10" />
                          {{ group.visibility === 'public' ? 'Publik' : 'Privat' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <p class="text-xs text-zinc-600 line-clamp-2 leading-relaxed mb-4">
                  {{ group.description }}
                </p>
              </div>

              <!-- Card Actions -->
              <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-2 mt-2">
                <router-link
                  :to="`/komunitas/${group.slug}`"
                  class="inline-flex items-center gap-1.5 text-xs font-bold text-black hover:text-zinc-700 transition-colors"
                >
                  <span>Lihat Komunitas</span>
                  <ExternalLink :size="13" />
                </router-link>

                <button
                  type="button"
                  @click="handleLeaveGroup(group.id)"
                  class="inline-flex items-center gap-1 text-[11px] font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer"
                >
                  <LogOut :size="12" />
                  <span>Keluar</span>
                </button>
              </div>
            </div>
          </div>

          <div v-else class="bg-white rounded-2xl p-10 text-center border border-zinc-200 shadow-xs my-4 space-y-3">
            <h3 class="font-serif-custom text-xl text-black font-normal">Belum mengikuti komunitas</h3>
            <p class="text-xs text-zinc-500">Anda belum meggabung dengan komunitas doa mana pun.</p>
            <button
              type="button"
              @click="router.push('/komunitas')"
              class="text-xs font-semibold text-black underline underline-offset-4 cursor-pointer mt-1"
            >
              Jelajahi Komunitas Doa
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modals -->
    <SharePrayerModal
      :is-open="isShareModalOpen"
      @close="isShareModalOpen = false"
      @submit-prayer="fetchDashboardData"
    />

    <PrayerDetailModal
      :is-open="isDetailModalOpen"
      :prayer="selectedPrayer"
      @close="isDetailModalOpen = false"
      @toggle-pray="handleTogglePray"
      @add-comment="handleAddComment"
    />

    <EditPrayerModal
      :is-open="isEditModalOpen"
      :prayer="editingPrayer"
      @close="isEditModalOpen = false"
      @updated="fetchDashboardData"
    />

    <CreateCommunityModal
      :is-open="isCreateCommunityModalOpen"
      @close="isCreateCommunityModalOpen = false"
      @created="handleCommunityCreated"
    />

    <EditCommunityModal
      :is-open="isEditCommunityModalOpen"
      :group="selectedGroupForEdit"
      @close="isEditCommunityModalOpen = false"
      @updated="fetchUserCommunities"
    />
  </div>
</template>

