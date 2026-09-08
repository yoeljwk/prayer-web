<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Plus, Search, Filter, RefreshCw, Radio } from 'lucide-vue-next'
import { mockRoomsState } from '@/data/mockRooms'
import RoomCard from '@/components/rooms/RoomCard.vue'
import RoomSkeleton from '@/components/rooms/RoomSkeleton.vue'
import CreateRoomModal from '@/components/rooms/CreateRoomModal.vue'

const route = useRoute()
const router = useRouter()

// UI state
const searchQuery = ref('')
const selectedSection = ref<'live' | 'scheduled' | 'ended'>('live')
const isLoading = ref(false)
const isCreateModalOpen = ref(false)

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

// Filtered rooms list
const filteredRooms = computed(() => {
  let list = [...mockRoomsState]

  // Search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    list = list.filter(
      (r) =>
        r.name.toLowerCase().includes(query) ||
        r.description.toLowerCase().includes(query) ||
        r.hostName.toLowerCase().includes(query)
    )
  }

  // Section tabs filter
  list = list.filter((r) => r.status === selectedSection.value)

  return list
})

// Count by section for pill badges
const liveCount = computed(() => mockRoomsState.filter((r) => r.status === 'live').length)
const scheduledCount = computed(() => mockRoomsState.filter((r) => r.status === 'scheduled').length)
const endedCount = computed(() => mockRoomsState.filter((r) => r.status === 'ended').length)

// Trigger mock loading
const triggerMockLoading = () => {
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
  }, 500)
}

const handleOpenCreateModal = () => {
  isCreateModalOpen.value = true
  router.push('/ruang-doa/create')
}

const handleCloseCreateModal = () => {
  isCreateModalOpen.value = false
  if (route.path.endsWith('/create')) {
    router.replace('/ruang-doa')
  }
}

const handleRoomCreated = (code: string) => {
  router.push(`/ruang-doa/${code}`)
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
              Ruang Doa
            </h1>
            <p class="text-sm sm:text-base text-zinc-500 font-normal max-w-2xl leading-relaxed">
              Masuki ruang persekutuan doa bersama dalam suasana yang tenang, hening, dan penuh kesungguhan iman.
            </p>
          </div>

          <!-- Main Button: Buat Ruang Doa -->
          <button
            type="button"
            @click="handleOpenCreateModal"
            class="hidden sm:inline-flex items-center gap-2 bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider px-5 py-3 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer shrink-0"
          >
            <Plus :size="16" class="stroke-[2.5]" />
            <span>Buat Ruang Doa</span>
          </button>
        </div>
      </div>

      <!-- Search Input Section -->
      <form @submit.prevent class="mb-6 flex items-center gap-3">
        <div class="relative flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari ruang doa berdasarkan nama, host, atau topik..."
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
          title="Buat Ruang Doa"
        >
          <Plus :size="18" />
        </button>
      </form>

      <!-- Section Tabs: Sedang Berlangsung, Akan Datang, Selesai -->
      <div class="flex items-center justify-between gap-3 mb-8 pb-1 overflow-x-auto scrollbar-none">
        <div class="flex items-center gap-2 shrink-0">
          <!-- Live Section Tab -->
          <button
            type="button"
            @click="selectedSection = 'live'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-2"
            :class="selectedSection === 'live' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-400" :class="{ 'animate-pulse': selectedSection === 'live' }"></span>
            <span>Sedang Berlangsung</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-800 text-white" v-if="selectedSection === 'live'">
              {{ liveCount }}
            </span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-100 text-zinc-700" v-else>
              {{ liveCount }}
            </span>
          </button>

          <!-- Scheduled Section Tab -->
          <button
            type="button"
            @click="selectedSection = 'scheduled'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-2"
            :class="selectedSection === 'scheduled' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            <span>Akan Datang</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-800 text-white" v-if="selectedSection === 'scheduled'">
              {{ scheduledCount }}
            </span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-100 text-zinc-700" v-else>
              {{ scheduledCount }}
            </span>
          </button>

          <!-- Ended Section Tab -->
          <button
            type="button"
            @click="selectedSection = 'ended'"
            class="px-4 py-2 rounded-full text-xs font-semibold transition-colors cursor-pointer flex items-center gap-2"
            :class="selectedSection === 'ended' ? 'bg-black text-white' : 'bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700'"
          >
            <span>Selesai</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-800 text-white" v-if="selectedSection === 'ended'">
              {{ endedCount }}
            </span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-zinc-100 text-zinc-700" v-else>
              {{ endedCount }}
            </span>
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

      <!-- Prayer Rooms Container -->
      <div>
        <RoomSkeleton v-if="isLoading" />

        <template v-else-if="filteredRooms.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <RoomCard
              v-for="room in filteredRooms"
              :key="room.id"
              :room="room"
            />
          </div>
        </template>

        <!-- Empty State -->
        <div
          v-else
          class="bg-white rounded-2xl p-12 text-center border border-zinc-200/90 shadow-xs my-8 space-y-3"
        >
          <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
            <Radio :size="22" />
          </div>
          <h3 class="font-serif-custom text-xl font-normal text-black">
            Tidak Ada Ruang Doa
          </h3>
          <p class="text-xs text-zinc-500 max-w-sm mx-auto">
            Tidak ditemukan ruang doa dalam kategori ini. Anda dapat membuat ruang doa baru.
          </p>
          <button
            type="button"
            @click="searchQuery = ''; selectedSection = 'live'"
            class="mt-2 text-xs font-semibold text-black underline underline-offset-4 cursor-pointer"
          >
            Reset Filter
          </button>
        </div>
      </div>

    </div>

    <!-- Create Room Modal -->
    <CreateRoomModal
      :is-open="isCreateModalOpen"
      @close="handleCloseCreateModal"
      @created="handleRoomCreated"
    />
  </div>
</template>
