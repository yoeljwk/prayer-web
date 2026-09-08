import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const savedUser = localStorage.getItem('auth_user')
  const user = ref<User | null>(savedUser ? JSON.parse(savedUser) : null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  function setAuth(userData: User, userToken: string) {
    user.value = userData
    token.value = userToken
    localStorage.setItem('auth_token', userToken)
    localStorage.setItem('auth_user', JSON.stringify(userData))
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await api.get('/me')
      if (response.data.success) {
        user.value = response.data.data
        localStorage.setItem('auth_user', JSON.stringify(response.data.data))
      }
    } catch {
      logout()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    setAuth,
    logout,
    fetchUser,
  }
})
