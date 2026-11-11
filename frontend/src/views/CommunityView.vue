<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, Filter, RefreshCw, Users, KeyRound } from 'lucide-vue-next'
import api from '@/services/api'
import type { Community } from '@/types'
import { useQueryState } from '@/composables/useQueryState'
import CommunityCard from '@/components/community/CommunityCard.vue'
import CommunitySkeleton from '@/components/community/CommunitySkeleton.vue'
import CreateCommunityModal from '@/components/community/CreateCommunityModal.vue'
import JoinByCodeModal from '@/components/community/JoinByCodeModal.vue'

import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// searchQuery is initialized from URL (?q=) so direct URL access and refresh work correctly.
const searchQuery = ref((route.query.q as string) || '')
// selectedFilter is synced with URL query param (?tab=public|joined).
// Persists on refresh and restores on Back/Forward navigation.
const selectedFilter = useQueryState<'public' | 'joined'>('tab', 'public')
const isLoading = ref(false)
const isCreateModalOpen = ref(false)
const isJoinCodeModalOpen = ref(false)
const apiCommunities = ref<Community[]>([])

const fetchCommunities = async () => {
  isLoading.value = true
  try {
    const params: Record<string, any> = {}
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim()
    }

    if (selectedFilter.value === 'public') {
      params.visibility = 'public'
      params.not_joined = true
    } else if (selectedFilter.value === 'joined') {
      params.joined = true
    }

    const res = await api.get('/groups', { params })
    if (res.data && res.data.success && Array.isArray(res.data.data)) {
      apiCommunities.value = res.data.data.map((item: any) => ({
        id: String(item.id),
        slug: item.slug || String(item.id),
        name: item.name,
        description: item.description || '',
        avatar: item.avatar || 'https://images.unsplash.com/photo-1544427920-c49ccfb85579?q=80&w=400&auto=format&fit=crop',
        visibility: item.visibility,
        memberCount: item.members_count || 0,
        maxMembers: item.max_members || 100,
        joinStatus: item.is_joined ? 'joined' : 'not_joined',
        createdAt: 'Baru saja',
        members: [],
        activities: [],
        prayers: [],
      }))
    }
  } catch (err) {
    console.error('Failed to fetch communities:', err)
  } finally {
    isLoading.value = false
  }
}

// Route-driven modal state: open modal when on /create, close it when navigating away.
const checkRouteState = () => {
  if (route.path.endsWith('/create')) {
    if (!authStore.isAuthenticated) {
      router.replace('/masuk')
      return
    }
    isCreateModalOpen.value = true
  } else {
    // Base route (/komunitas) — close modal (handles Back button)
    isCreateModalOpen.value = false
  }
}

onMounted(() => {
  fetchCommunities()
  checkRouteState()
})

watch(
  () => route.path,
  () => {
    checkRouteState()
  }
)

watch(selectedFilter, () => {
  fetchCommunities()
})

const handleSearchSubmit = () => {
  const query = { ...route.query }
  if (searchQuery.value.trim()) {
    query.q = searchQuery.value.trim()
  } else {
    delete query.q
  }
  router.replace({ query })
  fetchCommunities()
}

// Restore search query and re-fetch when Back/Forward navigation changes ?q=.
watch(
  () => route.query.q,
  (val) => {
    const restored = (val as string) || ''
    if (restored !== searchQuery.value) {
      searchQuery.value = restored
      fetchCommunities()
    }
  },
)

const filteredCommunities = computed(() => {
  if (selectedFilter.value === 'public') {
    return apiCommunities.value.filter((c) => c.joinStatus !== 'joined')
  }
  if (selectedFilter.value === 'joined') {
    return apiCommunities.value.filter((c) => c.joinStatus === 'joined')
  }
  return apiCommunities.value
})

const handleOpenCreateModal = () => {
  if (!authStore.isAuthenticated) {
    router.push('/masuk')
    return
  }
  isCreateModalOpen.value = true
  router.push('/komunitas/create')
}

const handleCloseCreateModal = () => {
  isCreateModalOpen.value = false
  if (route.path.endsWith('/create')) {
    router.replace('/komunitas')
  }
}

const handleCommunityCreated = (slug: string) => {
  fetchCommunities()
  router.push(`/komunitas/${slug}`)
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-6">
      
      <!-- Top Title & Subtitle Header -->
      <div class="mb-8">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="font-serif-custom text-4xl sm:text-5xl font-normal text-black tracking-tight mb-2">
              Komunitas Doa
            </h1>
            <p class="text-sm sm:text-base text-zinc-500 font-normal max-w-2xl leading-relaxed">
              Temukan dan bergabunglah dalam kelompok komunitas doa untuk bertumbuh bersama, saling menguatkan, dan menopang dalam persekutuan.
            </p>
          </div>

          <!-- Main Buttons: Gabung via Kode & Buat Komunitas (Only for authenticated users) -->
          <div v-if="authStore.isAuthenticated" class="hidden sm:flex items-center gap-2 shrink-0">
            <button
              type="button"
              @click="isJoinCodeModalOpen = true"
              class="inline-flex items-center gap-2 bg-white hover:bg-zinc-100 text-zinc-800 font-semibold text-xs border border-zinc-200 uppercase tracking-wider px-5 py-3 rounded-xl shadow-2xs transition-all duration-200 cursor-pointer"
            >
              <KeyRound :size="15" />
              <span>Gabung via Kode</span>
            </button>

            <button
              type="button"
              @click="handleOpenCreateModal"
              class="inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-3 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer"
            >
              <Plus :size="16" class="stroke-[2.5]" />
              <span>Buat Komunitas</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Search Input Section -->
      <form @submit.prevent="handleSearchSubmit" class="mb-6 flex items-center gap-3">
        <div class="relative flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama komunitas atau kata kunci deskripsi..."
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
          v-if="authStore.isAuthenticated"
          type="button"
          @click="handleOpenCreateModal"
          class="sm:hidden bg-black hover:bg-zinc-800 text-white p-3.5 rounded-xl transition-colors cursor-pointer shrink-0"
          title="Buat Komunitas"
        >
          <Plus :size="18" />
        </button>
      </form>

      <!-- Filter Chips Bar (Only for authenticated users) -->
      <div
        v-if="authStore.isAuthenticated"
        class="flex items-center justify-between gap-3 mb-8 pb-1 overflow-x-auto scrollbar-none"
      >
        <div class="flex items-center gap-2 shrink-0">
          <button
            type="button"
            @click="selectedFilter = 'public'"
            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all cursor-pointer select-none"
            :class="selectedFilter === 'public' ? 'bg-black text-white shadow-xs' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Publik
          </button>

          <button
            type="button"
            @click="selectedFilter = 'joined'"
            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all cursor-pointer select-none"
            :class="selectedFilter === 'joined' ? 'bg-black text-white shadow-xs' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Komunitas Saya
          </button>
        </div>

        <button
          type="button"
          @click="fetchCommunities"
          title="Refresh list"
          class="p-2 rounded-lg text-zinc-400 hover:text-black hover:bg-zinc-100 transition-colors cursor-pointer shrink-0"
        >
          <RefreshCw :size="16" :class="{ 'animate-spin': isLoading }" />
        </button>
      </div>

      <!-- Communities List Container -->
      <div>
        <!-- Loading Skeleton -->
        <CommunitySkeleton v-if="isLoading" />

        <!-- Cards Grid (Responsive 1/2/3 Columns) -->
        <template v-else-if="filteredCommunities.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <CommunityCard
              v-for="community in filteredCommunities"
              :key="community.id"
              :community="community"
              @joined="fetchCommunities"
              @join-code="isJoinCodeModalOpen = true"
            />
          </div>
        </template>

        <!-- Empty State -->
        <div
          v-else
          class="bg-white rounded-2xl p-12 text-center border border-zinc-200/90 shadow-xs my-8 space-y-3"
        >
          <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
            <Users :size="22" />
          </div>
          <h3 class="font-serif-custom text-xl font-normal text-black">
            Komunitas Tidak Ditemukan
          </h3>
          <p class="text-xs text-zinc-500 max-w-sm mx-auto">
            Tidak ditemukan komunitas yang sesuai dengan pencarian atau filter Anda.
          </p>
          <button
            type="button"
            @click="searchQuery = ''; selectedFilter = 'public'"
            class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
          >
            Reset Filter
          </button>
        </div>
      </div>

    </div>

    <!-- Modals -->
    <CreateCommunityModal
      :is-open="isCreateModalOpen"
      @close="handleCloseCreateModal"
      @created="handleCommunityCreated"
    />

    <JoinByCodeModal
      :is-open="isJoinCodeModalOpen"
      @close="isJoinCodeModalOpen = false"
      @joined="handleCommunityCreated"
    />
  </div>
</template>
