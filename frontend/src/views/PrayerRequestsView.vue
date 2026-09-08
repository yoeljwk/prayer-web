<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, Filter, RefreshCw } from 'lucide-vue-next'
import type { PrayerRequest } from '@/types'
import { mockPrayersState } from '@/data/mockPrayers'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import PrayerCard from '@/components/prayers/PrayerCard.vue'
import SharePrayerModal from '@/components/prayers/SharePrayerModal.vue'
import PrayerDetailModal from '@/components/prayers/PrayerDetailModal.vue'
import PrayerSkeleton from '@/components/prayers/PrayerSkeleton.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// UI state
const searchQuery = ref('')
const selectedFilter = ref<'today' | '3days' | 'thisweek'>('today')
const isLoading = ref(false)
const apiPrayers = ref<PrayerRequest[]>([])

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

const fetchPrayers = async () => {
  isLoading.value = true
  try {
    const res = await api.get('/prayers')
    if (res.data && res.data.success && res.data.data) {
      const items = res.data.data.data || res.data.data
      if (Array.isArray(items)) {
        apiPrayers.value = items.map((item: any) => ({
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
          hasPrayed: false,
          isOwner: item.user_id === authStore.user?.id,
          comments: item.comments || [],
        }))
      }
    }
  } catch (err) {
    console.error('Failed to load prayers from backend:', err)
  } finally {
    isLoading.value = false
  }
}

// Combined prayers list
const allPrayers = computed(() => {
  return [...apiPrayers.value, ...mockPrayersState]
})

// Modals state
const isShareModalOpen = ref(false)
const isDetailModalOpen = ref(false)
const selectedPrayer = ref<PrayerRequest | null>(null)

// Watch routes for /permohonan-doa/create and /permohonan-doa/:id
const checkRouteState = () => {
  if (route.path === '/permohonan-doa/create' || route.path === '/prayers/create') {
    isShareModalOpen.value = true
  } else if (route.params.id) {
    const prayerId = route.params.id as string
    const found = allPrayers.value.find((p) => p.id === prayerId)
    if (found) {
      selectedPrayer.value = found
      isDetailModalOpen.value = true
    }
  }
}

onMounted(() => {
  fetchPrayers()
  checkRouteState()
})

watch(
  () => route.params,
  () => {
    checkRouteState()
  }
)

// Computed filtered prayers list
const filteredPrayers = computed(() => {
  let list = [...allPrayers.value]

  // Search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    list = list.filter(
      (p) =>
        p.content.toLowerCase().includes(query) ||
        (!p.isAnonymous && p.authorName.toLowerCase().includes(query)) ||
        (p.groupName && p.groupName.toLowerCase().includes(query))
    )
  }

  // Time-based Filter Tabs
  if (selectedFilter.value === 'today') {
    list = list.filter(
      (p) =>
        p.createdAt.includes('jam') ||
        p.createdAt.includes('menit') ||
        p.createdAt.includes('Baru saja')
    )
  } else if (selectedFilter.value === '3days') {
    list = list.filter(
      (p) =>
        p.createdAt.includes('jam') ||
        p.createdAt.includes('menit') ||
        p.createdAt.includes('Baru saja') ||
        p.createdAt.includes('1 hari') ||
        p.createdAt.includes('2 hari') ||
        p.createdAt.includes('3 hari')
    )
  } else if (selectedFilter.value === 'thisweek') {
    list = list.filter(
      (p) =>
        !p.createdAt.includes('bulan') &&
        !p.createdAt.includes('tahun')
    )
  }

  return list
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

const handleAddComment = (prayerId: string, commentText: string, isAnon: boolean) => {
  const item = allPrayers.value.find((p) => p.id === prayerId)
  if (item) {
    item.comments.unshift({
      id: `c-${Date.now()}`,
      authorName: isAnon ? 'Anonim' : 'Youwel Ginting',
      isAnonymous: isAnon,
      createdAt: 'Baru saja',
      content: commentText,
    })
  }
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-6">
      
      <!-- Top Title & Subtitle Header (Matched with Reference Image) -->
      <div class="mb-8">
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

      <!-- Search Input Section (Matched with Reference Bar with Search Button) -->
      <form @submit.prevent class="mb-6 flex items-center gap-3">
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
          @click="triggerMockLoading"
          title="Refresh feed"
          class="p-2 rounded-lg text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer shrink-0"
        >
          <RefreshCw :size="16" :class="{ 'animate-spin': isLoading }" />
        </button>
      </div>

      <!-- Feed Container: 2-Column Grid Layout (Matched with Reference Image) -->
      <div>
        <!-- Skeleton Loading State -->
        <PrayerSkeleton v-if="isLoading" />

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
            Tidak ditemukan permohonan doa yang sesuai dengan pencarian atau filter Anda.
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
