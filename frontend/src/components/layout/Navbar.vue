<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { User, Menu, X, LogOut } from 'lucide-vue-next'
import type { NavItem } from '@/types'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import NavbarNotificationDropdown from '@/components/notifications/NavbarNotificationDropdown.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const isMobileMenuOpen = ref(false)
const isUserDropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const navItems: NavItem[] = [
  { name: 'Ruang Doa', path: '/ruang-doa' },
  { name: 'Permohonan Doa', path: '/permohonan-doa' },
  { name: 'Komunitas', path: '/komunitas' },
  { name: 'Tentang', path: '/tentang' },
]

const isNavItemActive = (path: string) => {
  if (path === '/permohonan-doa') {
    return route.path.startsWith('/permohonan-doa') || route.path.startsWith('/prayers')
  }
  return route.path === path || route.path.startsWith(path + '/')
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const toggleUserDropdown = () => {
  isUserDropdownOpen.value = !isUserDropdownOpen.value
}

const closeUserDropdown = () => {
  isUserDropdownOpen.value = false
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch {
    // Ignore network error on logout
  } finally {
    authStore.logout()
    closeUserDropdown()
    closeMobileMenu()
    router.push('/masuk')
  }
}

// Click outside handler for dropdown
const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeUserDropdown()
  }
}

onMounted(() => {
  if (authStore.token && !authStore.user) {
    authStore.fetchUser()
  }
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <header class="w-full border-b border-zinc-200 bg-white/90 backdrop-blur-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
      
      <!-- Logo (Link to Home) -->
      <RouterLink to="/" @click="closeMobileMenu" class="flex items-center gap-3 cursor-pointer group">
        <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center p-2 group-hover:bg-zinc-800 transition-colors shrink-0">
          <svg 
            width="24" 
            height="24" 
            class="w-6 h-6 text-white" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="1.8" 
            stroke-linecap="round" 
            stroke-linejoin="round"
          >
            <path d="M12 4c-1.5 2.5-3 5-3 7.5A3 3 0 0 0 12 14.5a3 3 0 0 0 3-3C15 9 13.5 6.5 12 4z" />
            <path d="M7.5 10.5C6 12 5 14 5 16a4 4 0 0 0 8 0" />
            <path d="M16.5 10.5C18 12 19 14 19 16a4 4 0 0 1-8 0" />
          </svg>
        </div>
        <span class="font-serif-custom text-2xl font-normal text-black tracking-tight whitespace-nowrap">TwoOrThree</span>
      </RouterLink>

      <!-- Desktop Navigation Links -->
      <nav class="hidden md:flex items-center space-x-9 text-sm font-medium text-zinc-600">
        <RouterLink 
          v-for="item in navItems" 
          :key="item.path"
          :to="item.path" 
          :class="[
            isNavItemActive(item.path) ? 'text-black font-bold' : 'hover:text-black transition-colors'
          ]"
        >
          {{ item.name }}
        </RouterLink>
      </nav>

      <!-- Right Side Actions -->
      <div class="hidden md:flex items-center space-x-3">
        <!-- Notification Dropdown -->
        <NavbarNotificationDropdown />

        <!-- Guest State: Show Masuk button only -->
        <template v-if="!authStore.isAuthenticated">
          <RouterLink 
            to="/masuk" 
            class="bg-black hover:bg-zinc-800 text-white text-sm font-medium px-5 py-2.5 rounded-full shadow-xs hover:shadow-md transition-all duration-200 cursor-pointer"
          >
            Masuk
          </RouterLink>
        </template>

        <!-- Authenticated State: Show User Name + User Icon + Dropdown -->
        <template v-else>
          <div ref="dropdownRef" class="relative flex items-center gap-3">
            <!-- Display User Name -->
            <span class="text-sm font-medium text-zinc-900 selection:bg-black selection:text-white">
              {{ authStore.user?.name || authStore.user?.username || 'Pengguna' }}
            </span>

            <!-- User Icon Toggle -->
            <button 
              @click.stop="toggleUserDropdown"
              aria-label="User Account Menu"
              class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:bg-zinc-800 transition-colors cursor-pointer shrink-0"
            >
              <User :size="20" class="w-5 h-5 text-white" />
            </button>

            <!-- User Dropdown Menu -->
            <Transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="opacity-0 scale-95 -translate-y-1"
              enter-to-class="opacity-100 scale-100 translate-y-0"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="opacity-100 scale-100 translate-y-0"
              leave-to-class="opacity-0 scale-95 -translate-y-1"
            >
              <div 
                v-if="isUserDropdownOpen" 
                class="absolute right-0 top-12 w-52 bg-white rounded-xl border border-zinc-200 shadow-xl py-2 z-50"
              >
                <div class="px-4 py-2.5 border-b border-zinc-100">
                  <p class="text-xs font-semibold text-black truncate">{{ authStore.user?.name }}</p>
                  <p class="text-[11px] text-zinc-400 truncate mt-0.5">@{{ authStore.user?.username }}</p>
                </div>
                <button 
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-zinc-50 flex items-center gap-2 transition-colors cursor-pointer mt-1"
                >
                  <LogOut :size="15" />
                  <span>Keluar (Logout)</span>
                </button>
              </div>
            </Transition>
          </div>
        </template>
      </div>

      <!-- Mobile Menu Button -->
      <button 
        @click="toggleMobileMenu"
        aria-label="Toggle navigation menu"
        class="md:hidden text-black p-2 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer"
      >
        <Menu v-if="!isMobileMenuOpen" :size="24" class="w-6 h-6" />
        <X v-else :size="24" class="w-6 h-6" />
      </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div v-if="isMobileMenuOpen" class="md:hidden bg-white border-b border-zinc-200 px-6 pt-2 pb-6 space-y-4">
      <RouterLink 
        v-for="item in navItems"
        :key="item.path"
        :to="item.path" 
        @click="closeMobileMenu" 
        :class="[
          isNavItemActive(item.path) ? 'text-black font-bold' : 'text-zinc-700 hover:text-black'
        ]"
        class="block text-base font-medium"
      >
        {{ item.name }}
      </RouterLink>

      <div class="pt-4 border-t border-zinc-200">
        <!-- Guest State (Mobile) -->
        <RouterLink 
          v-if="!authStore.isAuthenticated" 
          to="/masuk" 
          @click="closeMobileMenu" 
          class="bg-black text-white text-sm font-medium px-5 py-2.5 rounded-full block text-center"
        >
          Masuk
        </RouterLink>

        <!-- Authenticated State (Mobile) -->
        <div v-else class="space-y-3">
          <div class="flex items-center gap-3 px-1">
            <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center shrink-0">
              <User :size="20" />
            </div>
            <div class="truncate">
              <p class="text-sm font-semibold text-black truncate">{{ authStore.user?.name }}</p>
              <p class="text-xs text-zinc-500 truncate">@{{ authStore.user?.username }}</p>
            </div>
          </div>
          <button 
            @click="handleLogout"
            class="w-full bg-zinc-100 hover:bg-zinc-200 text-red-600 text-xs font-semibold py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors cursor-pointer"
          >
            <LogOut :size="15" />
            <span>Keluar (Logout)</span>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
