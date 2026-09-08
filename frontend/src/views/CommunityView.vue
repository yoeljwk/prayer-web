<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, Filter, RefreshCw, Users } from 'lucide-vue-next'
import { mockCommunitiesState } from '@/data/mockCommunities'
import CommunityCard from '@/components/community/CommunityCard.vue'
import CommunitySkeleton from '@/components/community/CommunitySkeleton.vue'
import CreateCommunityModal from '@/components/community/CreateCommunityModal.vue'

const route = useRoute()
const router = useRouter()

// UI state
const searchQuery = ref('')
const selectedFilter = ref<'all' | 'public' | 'private' | 'joined'>('all')
const isLoading = ref(false)
const isCreateModalOpen = ref(false)

// Check if URL is /komunitas/create or /communities/create
const checkRouteState = () => {
  if (route.path.endsWith('/create')) {
    isCreateModalOpen.value = true
  }
}

onMounted(() => {
  checkRouteState()
})

watch(
  () => route.path,
  () => {
    checkRouteState()
  }
)

// Computed filtered communities
const filteredCommunities = computed(() => {
  let list = [...mockCommunitiesState]

  // Search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    list = list.filter(
      (c) =>
        c.name.toLowerCase().includes(query) ||
        c.description.toLowerCase().includes(query)
    )
  }

  // Filter tabs: Semua, Public, Private, Sudah Diikuti
  if (selectedFilter.value === 'public') {
    list = list.filter((c) => c.visibility === 'public')
  } else if (selectedFilter.value === 'private') {
    list = list.filter((c) => c.visibility === 'private')
  } else if (selectedFilter.value === 'joined') {
    list = list.filter((c) => c.joinStatus === 'joined')
  }

  return list
})

// Trigger mock loading
const triggerMockLoading = () => {
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
  }, 500)
}

const handleOpenCreateModal = () => {
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

          <!-- Main Button: Buat Komunitas -->
          <button
            type="button"
            @click="handleOpenCreateModal"
            class="hidden sm:inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-3 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer shrink-0"
          >
            <Plus :size="16" class="stroke-[2.5]" />
            <span>Buat Komunitas</span>
          </button>
        </div>
      </div>

      <!-- Search Input Section -->
      <form @submit.prevent class="mb-6 flex items-center gap-3">
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
          type="button"
          @click="handleOpenCreateModal"
          class="sm:hidden bg-black hover:bg-zinc-800 text-white p-3.5 rounded-xl transition-colors cursor-pointer shrink-0"
          title="Buat Komunitas"
        >
          <Plus :size="18" />
        </button>
      </form>

      <!-- Filter Chips Bar & Refresh Button -->
      <div class="flex items-center justify-between gap-3 mb-8 pb-1 overflow-x-auto scrollbar-none">
        <div class="flex items-center gap-2 shrink-0">
          <button
            type="button"
            @click="selectedFilter = 'all'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'all' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Semua Komunitas
          </button>

          <button
            type="button"
            @click="selectedFilter = 'public'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'public' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Publik
          </button>

          <button
            type="button"
            @click="selectedFilter = 'private'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'private' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Privat
          </button>

          <button
            type="button"
            @click="selectedFilter = 'joined'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer"
            :class="selectedFilter === 'joined' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            Sudah Diikuti
          </button>
        </div>

        <button
          type="button"
          @click="triggerMockLoading"
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
            @click="searchQuery = ''; selectedFilter = 'all'"
            class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
          >
            Reset Filter
          </button>
        </div>
      </div>

    </div>

    <!-- Create Community Modal -->
    <CreateCommunityModal
      :is-open="isCreateModalOpen"
      @close="handleCloseCreateModal"
      @created="handleCommunityCreated"
    />
  </div>
</template>
