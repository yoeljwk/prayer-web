<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { ArrowLeft, LayoutGrid, HeartHandshake, Users, Info, Lock, Check } from 'lucide-vue-next'
import api from '@/services/api'
import type { Community, PrayerRequest } from '@/types'
import CommunityDetailHeader from '@/components/community/CommunityDetailHeader.vue'
import CommunityTabBeranda from '@/components/community/CommunityTabBeranda.vue'
import CommunityTabPrayers from '@/components/community/CommunityTabPrayers.vue'
import CommunityTabMembers from '@/components/community/CommunityTabMembers.vue'
import CommunityTabAbout from '@/components/community/CommunityTabAbout.vue'
import SharePrayerModal from '@/components/prayers/SharePrayerModal.vue'
import PrayerDetailModal from '@/components/prayers/PrayerDetailModal.vue'
import { useAuthStore } from '@/stores/auth'
import { useQueryState } from '@/composables/useQueryState'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const activeTab = useQueryState<'beranda' | 'prayers' | 'members' | 'about'>('tab', 'beranda')
const isLoading = ref(true)
const communityData = ref<Community | null>(null)

// Modal states for prayers in community
const isShareModalOpen = ref(false)
const isPrayerDetailOpen = ref(false)
const selectedPrayer = ref<PrayerRequest | null>(null)

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

const fetchCommunityDetail = async () => {
  const slug = route.params.slug as string
  if (!slug) return

  isLoading.value = true
  try {
    const res = await api.get(`/groups/${slug}`)
    if (res.data && res.data.success && res.data.data) {
      const data = res.data.data
      const rawPrayers = data.prayer_requests || data.prayerRequests || []
      const mappedPrayers = rawPrayers.map((p: any) => ({
        id: String(p.id),
        authorName: p.is_anonymous ? 'Anonim' : (p.user?.name || p.user?.username || 'Jemaat'),
        authorAvatar: p.is_anonymous ? undefined : (p.user?.avatar || undefined),
        isAnonymous: Boolean(p.is_anonymous),
        createdAt: formatTimeAgo(p.created_at),
        content: p.content,
        visibility: p.visibility,
        groupName: data.name,
        status: p.status || 'active',
        prayerCount: p.supports_count || 0,
        hasPrayed: Boolean(p.has_prayed),
        comments: (p.comments || []).map((c: any) => ({
          id: String(c.id),
          authorName: c.is_anonymous ? 'Anonim' : (c.user?.name || c.user?.username || 'Jemaat'),
          authorAvatar: c.is_anonymous ? undefined : (c.user?.avatar || undefined),
          isAnonymous: Boolean(c.is_anonymous),
          createdAt: formatTimeAgo(c.created_at),
          content: c.content,
        })),
      }))

      const mappedMembers = (data.members || []).map((m: any) => ({
        id: String(m.id),
        userId: String(m.user_id || m.user?.id || m.id),
        name: m.user?.name || m.user?.username || 'Anggota',
        username: m.user?.username,
        avatar: m.user?.avatar,
        role: m.role || 'member',
        joinedAt: formatTimeAgo(m.joined_at),
      }))

      const dynamicActivities = [
        ...mappedPrayers.slice(0, 5).map((p: any) => ({
          id: `prayer-${p.id}`,
          type: 'prayer_shared' as const,
          title: 'Permohonan Doa Baru',
          content: p.content,
          authorName: p.authorName,
          authorAvatar: p.authorAvatar,
          createdAt: p.createdAt,
        })),
        ...mappedMembers.slice(0, 5).map((m: any) => ({
          id: `member-${m.id}`,
          type: 'member_joined' as const,
          title: 'Anggota Baru Bergabung',
          content: `${m.name} telah bergabung dengan komunitas.`,
          authorName: m.name,
          authorAvatar: m.avatar,
          createdAt: m.joinedAt,
        })),
      ]

      communityData.value = {
        id: String(data.id),
        slug: data.slug || String(data.id),
        name: data.name,
        description: data.description || '',
        avatar: data.avatar || 'https://images.unsplash.com/photo-1544427920-c49ccfb85579?q=80&w=400&auto=format&fit=crop',
        visibility: data.visibility,
        memberCount: data.members_count || 0,
        maxMembers: data.max_members || 100,
        joinStatus: data.is_joined ? 'joined' : 'not_joined',
        userRole: data.user_role || null,
        inviteCode: data.invite_code,
        createdAt: formatTimeAgo(data.created_at),
        members: mappedMembers,
        activities: dynamicActivities,
        prayers: mappedPrayers,
      }
    }
  } catch (err) {
    console.error('Failed to load group detail:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchCommunityDetail()
})

watch(
  () => route.params.slug,
  () => {
    fetchCommunityDetail()
  }
)

const community = computed(() => communityData.value)

const handleToggleJoin = async () => {
  if (!community.value) return

  const groupId = community.value.id
  if (community.value.joinStatus === 'joined') {
    try {
      const res = await api.post(`/groups/${groupId}/leave`)
      if (res.data && res.data.success) {
        fetchCommunityDetail()
      }
    } catch (err) {
      console.error(err)
    }
  } else {
    try {
      const res = await api.post(`/groups/${groupId}/join`)
      if (res.data && res.data.success) {
        fetchCommunityDetail()
      }
    } catch (err) {
      console.error(err)
    }
  }
}

// Navigation tabs definition
const tabs = [
  { id: 'beranda', label: 'Beranda', icon: LayoutGrid },
  { id: 'prayers', label: 'Permohonan Doa', icon: HeartHandshake },
  { id: 'members', label: 'Anggota', icon: Users },
  { id: 'about', label: 'Tentang', icon: Info },
]

// Modal handlers
const handleOpenShare = () => {
  if (!authStore.isAuthenticated) {
    router.push('/masuk')
    return
  }
  isShareModalOpen.value = true
}

const handleCloseShare = () => {
  isShareModalOpen.value = false
}

const handleSubmitPrayer = (newPrayer?: any) => {
  fetchCommunityDetail()
}

const handleOpenPrayerDetail = (id: string) => {
  if (community.value) {
    const found = community.value.prayers.find((p) => p.id === id)
    if (found) {
      selectedPrayer.value = found
      isPrayerDetailOpen.value = true
    }
  }
}

const handleClosePrayerDetail = () => {
  isPrayerDetailOpen.value = false
  selectedPrayer.value = null
}

const handleTogglePray = async (id: string) => {
  if (community.value) {
    const item = community.value.prayers.find((p) => p.id === id)
    if (item) {
      if (item.hasPrayed) {
        item.hasPrayed = false
        item.prayerCount = Math.max(0, item.prayerCount - 1)
        try {
          await api.delete(`/prayers/${id}/support`)
        } catch (e) {
          console.error(e)
        }
      } else {
        item.hasPrayed = true
        item.prayerCount += 1
        try {
          await api.post(`/prayers/${id}/support`)
        } catch (e) {
          console.error(e)
        }
      }
    }
  }
}

const handleMarkAnswered = async (id: string) => {
  try {
    await api.put(`/prayers/${id}`, { status: 'answered', answered_at: new Date().toISOString() })
    fetchCommunityDetail()
  } catch (e) {
    console.error(e)
  }
}

const handleAddComment = async (prayerId: string, commentText: string, isAnon: boolean) => {
  if (community.value) {
    const item = community.value.prayers.find((p) => p.id === prayerId)
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
          authorName: c.is_anonymous ? 'Anonim' : (c.user?.name || c.user?.username || 'Saya'),
          authorAvatar: c.is_anonymous ? undefined : (c.user?.avatar || undefined),
          isAnonymous: Boolean(c.is_anonymous),
          createdAt: 'Baru saja',
          content: c.content,
        })
      }
    } catch (e) {
      console.error(e)
    }
  }
}

const handleGoBack = () => {
  if (window.history.length > 1 && window.history.state?.back) {
    router.back()
  } else {
    router.push('/komunitas')
  }
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen pb-16">
    <!-- Loading State -->
    <div v-if="isLoading" class="max-w-5xl mx-auto px-6 py-12 space-y-6">
      <div class="w-full h-48 bg-zinc-200/80 rounded-3xl animate-pulse"></div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6">
        <div class="h-32 bg-zinc-200/60 rounded-2xl animate-pulse"></div>
        <div class="h-32 bg-zinc-200/60 rounded-2xl animate-pulse"></div>
      </div>
    </div>

    <!-- Community Not Found Fallback -->
    <div v-else-if="!community" class="max-w-4xl mx-auto px-6 py-20 text-center space-y-4">
      <h2 class="font-serif-custom text-3xl text-black">Komunitas Tidak Ditemukan</h2>
      <p class="text-xs text-zinc-500">Komunitas dengan URL ini tidak tersedia atau telah dihapus.</p>
      <button
        type="button"
        @click="handleGoBack"
        class="inline-flex items-center gap-2 text-xs font-semibold text-white bg-black px-5 py-3 rounded-xl shadow-xs cursor-pointer"
      >
        <ArrowLeft :size="16" />
        <span>Kembali</span>
      </button>
    </div>

    <!-- Access Guard for Non-Members -->
    <div v-else-if="community.joinStatus !== 'joined'">
      <CommunityDetailHeader :community="community" @toggle-join="handleToggleJoin" />
      <div class="max-w-4xl mx-auto px-6 py-12">
        <div class="bg-white rounded-3xl p-10 sm:p-14 text-center border border-zinc-200/90 shadow-2xs space-y-4">
          <div class="w-16 h-16 rounded-2xl bg-zinc-100 text-zinc-800 flex items-center justify-center mx-auto mb-2">
            <Lock :size="28" />
          </div>
          <h3 class="font-serif-custom text-2xl sm:text-3xl text-black">Akses Khusus Anggota Komunitas</h3>
          <p class="text-xs sm:text-sm text-zinc-500 max-w-md mx-auto leading-relaxed">
            Anda perlu bergabung dengan <span class="font-bold text-black">{{ community.name }}</span> terlebih dahulu untuk dapat melihat isi permohonan doa, daftar anggota, dan aktivitas persekutuan di dalamnya.
          </p>
          <button
            type="button"
            @click="handleToggleJoin"
            class="inline-flex items-center gap-2 bg-black text-white text-xs font-semibold px-6 py-3 rounded-xl hover:bg-zinc-800 transition-all shadow-xs cursor-pointer mt-2"
          >
            <Check :size="14" />
            <span>Gabung Komunitas Sekarang</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Community Detail Content for Joined Members -->
    <template v-else>
      <!-- Header Banner & Info -->
      <CommunityDetailHeader :community="community" @toggle-join="handleToggleJoin" />

      <div class="max-w-5xl mx-auto px-6 pt-6">
        <!-- Tabs Navigation -->
        <div class="flex items-center gap-1 border-b border-zinc-200 mb-8 overflow-x-auto scrollbar-none">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            @click="activeTab = tab.id as any"
            class="flex items-center gap-2 px-5 py-3.5 text-xs font-semibold border-b-2 transition-all cursor-pointer whitespace-nowrap"
            :class="
              activeTab === tab.id
                ? 'border-black text-black'
                : 'border-transparent text-zinc-500 hover:text-black hover:border-zinc-300'
            "
          >
            <component :is="tab.icon" :size="15" />
            <span>{{ tab.label }}</span>
            <span
              v-if="tab.id === 'prayers' && community.prayers.length > 0"
              class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-zinc-100 text-zinc-800 font-bold"
            >
              {{ community.prayers.length }}
            </span>
          </button>
        </div>

        <!-- Dynamic Tab Views -->
        <div>
          <!-- Tab 1: Beranda -->
          <CommunityTabBeranda v-if="activeTab === 'beranda'" :community="community" />

          <!-- Tab 2: Permohonan Doa -->
          <CommunityTabPrayers
            v-else-if="activeTab === 'prayers'"
            :community="community"
            @open-share="handleOpenShare"
            @open-detail="handleOpenPrayerDetail"
            @toggle-pray="handleTogglePray"
          />

          <!-- Tab 3: Anggota -->
          <CommunityTabMembers
            v-else-if="activeTab === 'members'"
            :community="community"
            @refresh="fetchCommunityDetail"
          />

          <!-- Tab 4: Tentang -->
          <CommunityTabAbout v-else-if="activeTab === 'about'" :community="community" />
        </div>
      </div>
    </template>

    <!-- Modals for Community Prayers -->
    <SharePrayerModal
      :is-open="isShareModalOpen"
      :default-group-id="community?.id"
      @close="handleCloseShare"
      @submit-prayer="handleSubmitPrayer"
    />

    <PrayerDetailModal
      :is-open="isPrayerDetailOpen"
      :prayer="selectedPrayer"
      @close="handleClosePrayerDetail"
      @toggle-pray="handleTogglePray"
      @mark-answered="handleMarkAnswered"
      @add-comment="handleAddComment"
    />
  </div>
</template>
