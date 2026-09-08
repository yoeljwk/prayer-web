<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { ArrowLeft, LayoutGrid, HeartHandshake, Users, Info } from 'lucide-vue-next'
import { mockCommunitiesState } from '@/data/mockCommunities'
import CommunityDetailHeader from '@/components/community/CommunityDetailHeader.vue'
import CommunityTabBeranda from '@/components/community/CommunityTabBeranda.vue'
import CommunityTabPrayers from '@/components/community/CommunityTabPrayers.vue'
import CommunityTabMembers from '@/components/community/CommunityTabMembers.vue'
import CommunityTabAbout from '@/components/community/CommunityTabAbout.vue'
import SharePrayerModal from '@/components/prayers/SharePrayerModal.vue'
import PrayerDetailModal from '@/components/prayers/PrayerDetailModal.vue'
import type { PrayerRequest } from '@/types'

const route = useRoute()
const router = useRouter()

const activeTab = ref<'beranda' | 'prayers' | 'members' | 'about'>('beranda')

// Modal states for prayers in community
const isShareModalOpen = ref(false)
const isPrayerDetailOpen = ref(false)
const selectedPrayer = ref<PrayerRequest | null>(null)

// Find community by slug from route params
const community = computed(() => {
  const slug = route.params.slug as string
  return mockCommunitiesState.find((c) => c.slug === slug)
})

// Navigation tabs definition
const tabs = [
  { id: 'beranda', label: 'Beranda', icon: LayoutGrid },
  { id: 'prayers', label: 'Permohonan Doa', icon: HeartHandshake },
  { id: 'members', label: 'Anggota', icon: Users },
  { id: 'about', label: 'Tentang', icon: Info },
]

// Modal handlers
const handleOpenShare = () => {
  isShareModalOpen.value = true
}

const handleCloseShare = () => {
  isShareModalOpen.value = false
}

const handleSubmitPrayer = (newPrayer: PrayerRequest) => {
  if (community.value) {
    newPrayer.groupName = community.value.name
    newPrayer.visibility = 'group'
    community.value.prayers.unshift(newPrayer)
  }
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

const handleTogglePray = (id: string) => {
  if (community.value) {
    const item = community.value.prayers.find((p) => p.id === id)
    if (item) {
      if (item.hasPrayed) {
        item.hasPrayed = false
        item.prayerCount = Math.max(0, item.prayerCount - 1)
      } else {
        item.hasPrayed = true
        item.prayerCount += 1
      }
    }
  }
}

const handleMarkAnswered = (id: string) => {
  if (community.value) {
    const item = community.value.prayers.find((p) => p.id === id)
    if (item) {
      item.status = 'answered'
      item.answeredAt = 'Baru saja'
    }
  }
}

const handleAddComment = (prayerId: string, commentText: string, isAnon: boolean) => {
  if (community.value) {
    const item = community.value.prayers.find((p) => p.id === prayerId)
    if (item) {
      item.comments.unshift({
        id: `cc-${Date.now()}`,
        authorName: isAnon ? 'Anonim' : 'Youwel Ginting',
        isAnonymous: isAnon,
        createdAt: 'Baru saja',
        content: commentText,
      })
    }
  }
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen pb-16">
    <!-- Community Not Found Fallback -->
    <div v-if="!community" class="max-w-4xl mx-auto px-6 py-20 text-center space-y-4">
      <h2 class="font-serif-custom text-3xl text-black">Komunitas Tidak Ditemukan</h2>
      <p class="text-xs text-zinc-500">Komunitas dengan URL ini tidak tersedia atau telah dihapus.</p>
      <RouterLink
        to="/komunitas"
        class="inline-flex items-center gap-2 text-xs font-semibold text-white bg-black px-5 py-3 rounded-xl shadow-xs"
      >
        <ArrowLeft :size="16" />
        <span>Kembali ke Daftar Komunitas</span>
      </RouterLink>
    </div>

    <!-- Community Detail Content -->
    <template v-else>
      <!-- Header Banner & Info -->
      <CommunityDetailHeader :community="community" />

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
          />

          <!-- Tab 3: Anggota -->
          <CommunityTabMembers v-else-if="activeTab === 'members'" :community="community" />

          <!-- Tab 4: Tentang -->
          <CommunityTabAbout v-else-if="activeTab === 'about'" :community="community" />
        </div>
      </div>
    </template>

    <!-- Modals for Community Prayers -->
    <SharePrayerModal
      :is-open="isShareModalOpen"
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
