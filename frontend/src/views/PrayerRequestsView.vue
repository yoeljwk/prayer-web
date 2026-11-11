<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, Filter, RefreshCw, Lock, Users } from 'lucide-vue-next'
import type { PrayerRequest } from '@/types'
import { mockPrayersState } from '@/data/mockPrayers'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useQueryState } from '@/composables/useQueryState'
import PrayerCard from '@/components/prayers/PrayerCard.vue'
import SharePrayerModal from '@/components/prayers/SharePrayerModal.vue'
import PrayerDetailModal from '@/components/prayers/PrayerDetailModal.vue'
import PrayerSkeleton from '@/components/prayers/PrayerSkeleton.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// UI state
// searchQuery is initialized from URL (?q=) so direct URL access and refresh work correctly.
// It is synced to URL manually on form submit (not reactive-on-type).
const searchQuery = ref((route.query.q as string) || '')
// selectedFilter and activeSubMenu are synced with URL query params via useQueryState.
// This persists state across refreshes and restores it on Back/Forward navigation.
const selectedFilter = useQueryState<'today' | '3days' | 'thisweek'>('filter', 'today')
const activeSubMenu = useQueryState<'public' | 'community'>('tab', 'public')
const userCommunities = ref<{ id: number | string; name: string; prayersCount: number }[]>([])
const selectedCommunityId = ref<number | string | null>((route.query.group as string) || null)
const isLoading = ref(false)
const isNextLoading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const hasMorePages = computed(() => currentPage.value < lastPage.value)
const apiPrayers = ref<PrayerRequest[]>([])

const fetchUserCommunities = async () => {
  if (!authStore.isAuthenticated) return
  try {
    const res = await api.get('/groups', { params: { joined: true } })
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      userCommunities.value = res.data.data.map((g: any) => ({
        id: g.id,
        name: g.name,
        prayersCount: g.prayers_count ?? 0,
      }))
      const groupFromUrl = route.query.group ? String(route.query.group) : null
      const foundInList = groupFromUrl ? userCommunities.value.find((c) => String(c.id) === groupFromUrl) : null
      if (foundInList) {
        selectedCommunityId.value = foundInList.id
      } else if (userCommunities.value.length > 0 && !selectedCommunityId.value) {
        selectedCommunityId.value = userCommunities.value[0]?.id ?? null
      }
    }
  } catch (err) {
    console.error('Failed to load user communities:', err)
  }
}

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

const fetchPrayers = async (page = 1, append = false) => {
  if (append) {
    isNextLoading.value = true
  } else {
    isLoading.value = true
  }

  try {
    const params: Record<string, any> = { 
      page,
      type: activeSubMenu.value,
    }
    if (activeSubMenu.value === 'community' && selectedCommunityId.value) {
      params.group_id = selectedCommunityId.value
    }
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim()
    }
    if (selectedFilter.value) {
      params.filter = selectedFilter.value
    }

    const res = await api.get('/prayers', { params })
    if (res.data && res.data.success && res.data.data) {
      const pageData = res.data.data
      const items = pageData.data || pageData
      currentPage.value = pageData.current_page || 1
      lastPage.value = pageData.last_page || 1

      if (Array.isArray(items)) {
        const mapped = items.map((item: any) => ({
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
          hasPrayed: Boolean(item.has_prayed),
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

        if (append) {
          apiPrayers.value = [...apiPrayers.value, ...mapped]
        } else {
          apiPrayers.value = mapped
        }
      }
    }
  } catch (err) {
    console.error('Failed to load prayers from backend:', err)
  } finally {
    isLoading.value = false
    isNextLoading.value = false
  }
}

const loadMore = () => {
  if (hasMorePages.value && !isNextLoading.value) {
    fetchPrayers(currentPage.value + 1, true)
  }
}

// Combined prayers list
const allPrayers = computed(() => {
  return apiPrayers.value
})

// Modals state
const isShareModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const selectedPrayer = ref<PrayerRequest | null>(null)

// Route-driven modal state: route is the single source of truth for which modal is open.
// checkRouteState is called on mount (after data is ready) and on every route change.
const checkRouteState = () => {
  if (route.path === '/permohonan-doa/create' || route.path === '/prayers/create') {
    if (!authStore.isAuthenticated) {
      router.replace('/masuk')
      return
    }
    // Open share modal, ensure detail modal is closed
    isShareModalOpen.value = true
    isDetailModalOpen.value = false
    selectedPrayer.value = null
  } else if (route.params.id) {
    const prayerId = route.params.id as string
    const found = allPrayers.value.find((p) => p.id === prayerId)
    if (found) {
      // Open detail modal, ensure share modal is closed
      selectedPrayer.value = found
      isDetailModalOpen.value = true
      isShareModalOpen.value = false
    } else {
      // Prayer ID not found (invalid or not loaded) — fall back to list
      router.replace('/permohonan-doa')
    }
  } else {
    // Base route (/permohonan-doa) — close all modals
    isShareModalOpen.value = false
    isDetailModalOpen.value = false
    selectedPrayer.value = null
  }
}

// Await fetchPrayers so that allPrayers is populated before checkRouteState
// tries to find a prayer by :id on direct URL access.
onMounted(async () => {
  if (authStore.isAuthenticated) {
    fetchUserCommunities()
  }
  await fetchPrayers()
  checkRouteState()
})

// Watch path + params only (not query) for modal state changes.
// Using fullPath would incorrectly trigger checkRouteState when filter/tab query params change.
watch(
  [() => route.path, () => route.params.id],
  () => {
    checkRouteState()
  },
)

watch(activeSubMenu, (val) => {
  if (val === 'community') {
    if (userCommunities.value.length === 0 && authStore.isAuthenticated) {
      fetchUserCommunities()
    } else if (userCommunities.value.length > 0 && !selectedCommunityId.value) {
      selectedCommunityId.value = userCommunities.value[0]?.id ?? null
    }
  }
  fetchPrayers(1, false)
})

watch(selectedCommunityId, (val) => {
  const query = { ...route.query }
  if (val) {
    query.group = String(val)
  } else {
    delete query.group
  }
  router.replace({ query })
  if (activeSubMenu.value === 'community') {
    fetchPrayers(1, false)
  }
})

watch(
  () => route.query.group,
  (val) => {
    if (val && String(val) !== String(selectedCommunityId.value)) {
      selectedCommunityId.value = val as string
    }
  },
)

watch(selectedFilter, () => {
  fetchPrayers(1, false)
})

const handleSearchSubmit = () => {
  // Sync search query to URL on submit (not on every keystroke).
  // Use router.replace to avoid polluting browser history.
  const query = { ...route.query }
  if (searchQuery.value.trim()) {
    query.q = searchQuery.value.trim()
  } else {
    delete query.q
  }
  router.replace({ query })
  fetchPrayers(1, false)
}

// Restore search query and re-fetch when Back/Forward navigation changes ?q=.
watch(
  () => route.query.q,
  (val) => {
    const restored = (val as string) || ''
    if (restored !== searchQuery.value) {
      searchQuery.value = restored
      fetchPrayers(1, false)
    }
  },
)

// Filtered list
const filteredPrayers = computed(() => {
  return allPrayers.value.filter((p) => p.status !== 'answered')
})

// Refresh feed
const triggerMockLoading = () => {
  fetchPrayers()
}

// Handlers for Prayer actions
const handleTogglePray = async (id: string) => {
  const item = allPrayers.value.find((p) => p.id === id)
  if (!item) return

  if (item.hasPrayed) {
    item.hasPrayed = false
    item.prayerCount = Math.max(0, item.prayerCount - 1)
    if (!isNaN(Number(id))) {
      try {
        await api.delete(`/prayers/${id}/support`)
      } catch (e) {
        console.error(e)
      }
    }
  } else {
    item.hasPrayed = true
    item.prayerCount += 1
    if (!isNaN(Number(id))) {
      try {
        await api.post(`/prayers/${id}/support`)
      } catch (e) {
        console.error(e)
      }
    }
  }
}

watch(
  [isDetailModalOpen, isShareModalOpen],
  ([detailOpen, shareOpen]) => {
    if (detailOpen || shareOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
)

const handleOpenDetail = (id: string) => {
  const item = allPrayers.value.find((p) => p.id === id)
  if (item) {
    selectedPrayer.value = item
    isDetailModalOpen.value = true
    router.push(`/permohonan-doa/${id}`)
  }
}

const handleCloseDetail = () => {
  isDetailModalOpen.value = false
  selectedPrayer.value = null
  if (route.params.id) {
    router.replace('/permohonan-doa')
  }
}

const handleOpenShare = () => {
  if (!authStore.isAuthenticated) {
    router.push('/masuk')
    return
  }
  isShareModalOpen.value = true
  router.push('/permohonan-doa/create')
}

const handleCloseShare = () => {
  isShareModalOpen.value = false
  if (route.path.endsWith('/create')) {
    router.replace('/permohonan-doa')
  }
}

const handleSubmitNewPrayer = (newPrayer?: any) => {
  fetchPrayers()
}

const handleMarkAnswered = (id: string) => {
  const item = allPrayers.value.find((p) => p.id === id)
  if (item) {
    item.status = 'answered'
    item.answeredAt = 'Baru saja'
  }
}

const handleAddComment = async (prayerId: string, commentText: string, isAnon: boolean) => {
  const item = allPrayers.value.find((p) => p.id === prayerId)
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
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-6">
      
      <!-- Top Title & Subtitle Header (Matched with Reference Image) -->
      <div class="mb-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="font-serif-custom text-4xl sm:text-5xl font-normal text-black tracking-tight mb-2">
              Permohonan Doa
            </h1>
            <p class="text-sm sm:text-base text-zinc-500 font-normal max-w-2xl leading-relaxed">
              Jelajahi permohonan doa terbaru atau cari pesan doa untuk saling mendukung, menguatkan, dan mendoakan bersama komunitas.
            </p>
          </div>

          <!-- Main Button: Bagikan Doa -->
          <button
            type="button"
            @click="handleOpenShare"
            class="hidden sm:inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-3 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer shrink-0"
          >
            <Plus :size="16" class="stroke-[2.5]" />
            <span>Bagikan Doa</span>
          </button>
        </div>
      </div>

      <!-- Sub-Menu Navigation Tabs (Only visible for authenticated users) -->
      <div v-if="authStore.isAuthenticated" class="flex items-center gap-6 border-b border-zinc-200 mb-6">
        <button
          type="button"
          @click="activeSubMenu = 'public'"
          class="pb-3 text-sm sm:text-base font-semibold transition-colors relative cursor-pointer"
          :class="activeSubMenu === 'public' ? 'text-black border-b-2 border-black -mb-px' : 'text-zinc-400 hover:text-black'"
        >
          Doa Publik
        </button>
        <button
          type="button"
          @click="activeSubMenu = 'community'"
          class="pb-3 text-sm sm:text-base font-semibold transition-colors relative cursor-pointer"
          :class="activeSubMenu === 'community' ? 'text-black border-b-2 border-black -mb-px' : 'text-zinc-400 hover:text-black'"
        >
          Doa Komunitas Saya
        </button>
      </div>

      <!-- Community Filter Pills with Prayer Count Badge -->
      <div
        v-if="activeSubMenu === 'community' && authStore.isAuthenticated && userCommunities.length > 0"
        class="mb-6 flex items-center gap-2 flex-wrap"
      >
        <span class="text-xs text-zinc-400 shrink-0 flex items-center gap-1 mr-1">
          <Users :size="13" class="text-zinc-400" />
          <span>Komunitas:</span>
        </span>

        <button
          v-for="comm in userCommunities"
          :key="comm.id"
          type="button"
          @click="selectedCommunityId = comm.id"
          class="relative inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 cursor-pointer shrink-0"
          :class="selectedCommunityId === comm.id
            ? 'bg-black text-white shadow-xs'
            : 'bg-white hover:bg-zinc-100 text-zinc-700 border border-zinc-200'"
        >
          <span>{{ comm.name }}</span>
          <!-- Prayer count badge -->
          <span
            v-if="comm.prayersCount > 0"
            class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold leading-none transition-colors"
            :class="selectedCommunityId === comm.id
              ? 'bg-white/20 text-white'
              : 'bg-zinc-100 text-zinc-500'"
          >
            {{ comm.prayersCount > 99 ? '99+' : comm.prayersCount }}
          </span>
        </button>
      </div>

      <!-- Search Input Section (Matched with Reference Bar with Search Button) -->
      <form @submit.prevent="handleSearchSubmit" class="mb-6 flex items-center gap-3">
        <div class="relative flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari kata kunci permohonan doa atau nama..."
            class="w-full px-5 py-3.5 rounded-xl border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black bg-white shadow-2xs transition-colors"
          />
          <Search :size="18" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none" />
        </div>

        <button
          type="submit"
          class="bg-black hover:bg-zinc-800 text-white font-semibold text-sm px-6 sm:px-7 py-3.5 rounded-xl transition-colors cursor-pointer shadow-xs shrink-0"
        >
          Cari
        </button>

        <button
          type="button"
          @click="handleOpenShare"
          class="sm:hidden bg-black hover:bg-zinc-800 text-white p-3.5 rounded-xl transition-colors cursor-pointer shrink-0"
          title="Bagikan Doa"
        >
          <Plus :size="18" />
        </button>
      </form>

      <!-- Filter Chips Bar & Refresh -->
      <div class="flex items-center justify-between gap-3 mb-8 pb-1 overflow-x-auto scrollbar-none">
        <div class="flex items-center gap-2 shrink-0">
          <button
            type="button"
            @click="selectedFilter = 'today'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'today' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Hari Ini
          </button>

          <button
            type="button"
            @click="selectedFilter = '3days'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === '3days' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            3 Hari Terakhir
          </button>

          <button
            type="button"
            @click="selectedFilter = 'thisweek'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'thisweek' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Minggu Ini
          </button>
        </div>

        <button
          type="button"
          @click="fetchPrayers(1, false)"
          title="Refresh feed"
          class="p-2 rounded-lg text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer shrink-0"
        >
          <RefreshCw :size="16" :class="{ 'animate-spin': isLoading }" />
        </button>
      </div>

      <!-- Feed Container: 2-Column Grid Layout (Matched with Reference Image) -->
      <div>
        <!-- Unauthenticated Prompt for Doa Komunitas Saya -->
        <div
          v-if="activeSubMenu === 'community' && !authStore.isAuthenticated"
          class="bg-white rounded-2xl p-10 sm:p-12 text-center border border-zinc-200/90 shadow-xs my-8 space-y-4"
        >
          <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-2">
            <Lock :size="22" />
          </div>
          <h3 class="font-serif-custom text-xl font-normal text-black">
            Akses Terbatas
          </h3>
          <p class="text-xs sm:text-sm text-zinc-500 max-w-md mx-auto leading-relaxed">
            Silakan masuk ke akun Anda terlebih dahulu untuk melihat permohonan doa dari komunitas yang Anda ikuti.
          </p>
          <RouterLink
            to="/masuk"
            class="inline-flex items-center justify-center bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-6 py-3 rounded-xl transition-all duration-200 shadow-xs"
          >
            Masuk ke Akun
          </RouterLink>
        </div>

        <!-- Skeleton Loading State -->
        <PrayerSkeleton v-else-if="isLoading" />

        <!-- Prayer Items Grid (2 Columns) -->
        <template v-else-if="filteredPrayers.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
            <PrayerCard
              v-for="prayer in filteredPrayers"
              :key="prayer.id"
              :prayer="prayer"
              @toggle-pray="handleTogglePray"
              @open-detail="handleOpenDetail"
            />
          </div>

          <!-- Load More Button -->
          <div v-if="hasMorePages" class="text-center mt-10">
            <button
              type="button"
              @click="loadMore"
              :disabled="isNextLoading"
              class="inline-flex items-center gap-2 bg-white hover:bg-zinc-100 text-zinc-800 font-semibold text-xs border border-zinc-200 px-6 py-3 rounded-full transition-colors cursor-pointer shadow-2xs disabled:opacity-50"
            >
              <RefreshCw v-if="isNextLoading" :size="14" class="animate-spin" />
              <span>{{ isNextLoading ? 'Memuat...' : 'Muat Lebih Banyak Doa' }}</span>
            </button>
          </div>
        </template>

        <!-- Empty State -->
        <div
          v-else
          class="bg-white rounded-2xl p-12 text-center border border-zinc-200/90 shadow-xs my-8 space-y-3"
        >
          <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
            <Filter :size="22" />
          </div>
          <h3 class="font-serif-custom text-xl font-normal text-black">
            Tidak Ada Permohonan Doa
          </h3>
          <p class="text-xs text-zinc-500 max-w-sm mx-auto">
            {{ activeSubMenu === 'community' ? 'Belum ada permohonan doa dari komunitas yang Anda ikuti.' : 'Tidak ditemukan permohonan doa yang sesuai dengan pencarian atau filter Anda.' }}
          </p>
          <button
            type="button"
            @click="searchQuery = ''; selectedFilter = 'today'"
            class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
          >
            Reset Filter
          </button>
        </div>
      </div>

    </div>

    <!-- Modals -->
    <SharePrayerModal
      :is-open="isShareModalOpen"
      @close="handleCloseShare"
      @submit-prayer="handleSubmitNewPrayer"
    />

    <PrayerDetailModal
      :is-open="isDetailModalOpen"
      :prayer="selectedPrayer"
      @close="handleCloseDetail"
      @toggle-pray="handleTogglePray"
      @mark-answered="handleMarkAnswered"
      @add-comment="handleAddComment"
    />
  </div>
</template>
