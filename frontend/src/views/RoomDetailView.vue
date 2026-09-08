<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { ArrowLeft, Radio } from 'lucide-vue-next'
import { mockRoomsState } from '@/data/mockRooms'
import RoomLobbyView from '@/components/rooms/RoomLobbyView.vue'
import ActiveRoomView from '@/components/rooms/ActiveRoomView.vue'

const route = useRoute()
const router = useRouter()

const isInsideRoom = ref(false)

const room = computed(() => {
  const code = route.params.code as string
  return mockRoomsState.find((r) => r.code === code)
})

const handleEnterRoom = () => {
  isInsideRoom.value = true
}

const handleLeaveRoom = () => {
  isInsideRoom.value = false
}
</script>

<template>
  <div class="flex-1 bg-[#FBFBF9] w-full min-h-screen">
    <!-- State 1: Room Not Found Fallback -->
    <div v-if="!room" class="max-w-4xl mx-auto px-6 py-20 text-center space-y-4">
      <div class="w-14 h-14 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto mb-2">
        <Radio :size="24" />
      </div>
      <h2 class="font-serif-custom text-3xl font-normal text-black tracking-tight">
        Ruang Doa Tidak Ditemukan
      </h2>
      <p class="text-xs text-zinc-500 max-w-sm mx-auto">
        Kode ruang doa ini tidak valid atau rute ruang doa telah ditutup.
      </p>
      <RouterLink
        to="/ruang-doa"
        class="inline-flex items-center gap-2 text-xs font-semibold text-white bg-black px-5 py-3 rounded-xl shadow-xs cursor-pointer"
      >
        <ArrowLeft :size="16" />
        <span>Kembali ke Daftar Ruang Doa</span>
      </RouterLink>
    </div>

    <!-- State 2: Inside Active Prayer Room -->
    <ActiveRoomView
      v-else-if="isInsideRoom"
      :room="room"
      @leave="handleLeaveRoom"
    />

    <!-- State 3: Room Lobby View (Pre-join) -->
    <RoomLobbyView
      v-else
      :room="room"
      @enter="handleEnterRoom"
    />
  </div>
</template>
