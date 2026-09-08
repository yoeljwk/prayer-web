<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Eye, EyeOff, ArrowRight } from 'lucide-vue-next'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import SuccessModal from '@/components/common/SuccessModal.vue'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const agreeTerms = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
const showSuccessModal = ref(false)
const registeredUserName = ref('')

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPasswordVisibility = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

const navigateHome = () => {
  router.push('/')
}

const handleRegister = async () => {
  if (!name.value || !username.value || !email.value || !password.value || !confirmPassword.value) {
    errorMessage.value = 'Silakan lengkapi seluruh kolom pendaftaran.'
    return
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Konfirmasi kata sandi tidak cocok dengan kata sandi Anda.'
    return
  }

  if (!agreeTerms.value) {
    errorMessage.value = 'Anda perlu menyetujui Syarat & Ketentuan untuk melanjutkan.'
    return
  }

  errorMessage.value = ''
  isLoading.value = true

  try {
    const response = await api.post('/register', {
      name: name.value,
      username: username.value,
      email: email.value,
      password: password.value,
    })

    if (response.data.success) {
      const { user, token } = response.data.data
      authStore.setAuth(user, token)
      registeredUserName.value = user.name
      showSuccessModal.value = true
    }
  } catch (error: any) {
    if (error.response?.data?.errors) {
      const errorKeys = Object.keys(error.response.data.errors)
      if (errorKeys.length > 0 && errorKeys[0]) {
        const firstKey = errorKeys[0]
        const messages = error.response.data.errors[firstKey]
        if (Array.isArray(messages) && messages.length > 0) {
          errorMessage.value = messages[0]
        } else {
          errorMessage.value = 'Validasi pendaftaran gagal.'
        }
      } else {
        errorMessage.value = 'Validasi pendaftaran gagal.'
      }
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Gagal terhubung ke server backend. Pastikan server Laravel sedang berjalan.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex-1 flex items-center justify-center px-6 py-12 md:py-16 bg-[#FBFBF9] w-full">
    <!-- Centered Card Box -->
    <div class="w-full max-w-md bg-white rounded-xl p-8 sm:p-9 border border-zinc-200 shadow-sm">
      
      <!-- Card Box Header -->
      <div class="text-center mb-8">
        <h1 class="font-serif-custom text-3xl font-normal text-black mb-2">
          Gabung Komunitas Doa
        </h1>
        <p class="text-sm text-zinc-500 font-normal">
          Buat akun untuk mulai berdoa bersama dan saling menguatkan.
        </p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="mb-6 p-3.5 rounded-lg bg-zinc-100 border border-zinc-300 text-xs font-medium text-black">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        
        <!-- Full Name Input (rounded-lg) -->
        <div>
          <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-1.5">
            Nama Lengkap
          </label>
          <input 
            id="name" 
            v-model="name" 
            type="text" 
            required
            placeholder="Nama Anda" 
            class="w-full px-4 py-3 rounded-lg border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
          />
        </div>

        <!-- Username Input (rounded-lg) -->
        <div>
          <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-1.5">
            Username
          </label>
          <input 
            id="username" 
            v-model="username" 
            type="text" 
            required
            placeholder="username_anda" 
            class="w-full px-4 py-3 rounded-lg border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
          />
        </div>

        <!-- Email Input (rounded-lg) -->
        <div>
          <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-1.5">
            Alamat Email
          </label>
          <input 
            id="email" 
            v-model="email" 
            type="email" 
            required
            placeholder="nama@email.com" 
            class="w-full px-4 py-3 rounded-lg border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
          />
        </div>

        <!-- Password Input (rounded-lg) -->
        <div>
          <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-1.5">
            Kata Sandi
          </label>
          
          <div class="relative">
            <input 
              id="password" 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              required
              placeholder="Minimal 8 karakter" 
              class="w-full pl-4 pr-11 py-3 rounded-lg border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
            />
            <button 
              type="button" 
              @click="togglePasswordVisibility"
              aria-label="Toggle password visibility"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-black transition-colors"
            >
              <Eye v-if="!showPassword" :size="18" />
              <EyeOff v-else :size="18" />
            </button>
          </div>
        </div>

        <!-- Confirm Password Input (rounded-lg) -->
        <div>
          <label for="confirmPassword" class="block text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-1.5">
            Ulangi Kata Sandi
          </label>
          
          <div class="relative">
            <input 
              id="confirmPassword" 
              v-model="confirmPassword" 
              :type="showConfirmPassword ? 'text' : 'password'" 
              required
              placeholder="Konfirmasi kata sandi" 
              class="w-full pl-4 pr-11 py-3 rounded-lg border border-zinc-300 text-sm text-black placeholder-zinc-400 focus:outline-none focus:border-black focus:ring-1 focus:ring-black transition-colors bg-white"
            />
            <button 
              type="button" 
              @click="toggleConfirmPasswordVisibility"
              aria-label="Toggle confirm password visibility"
              class="absolute right-3.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-black transition-colors"
            >
              <Eye v-if="!showConfirmPassword" :size="18" />
              <EyeOff v-else :size="18" />
            </button>
          </div>
        </div>

        <!-- Terms Checkbox -->
        <div class="pt-1">
          <label class="flex items-start gap-2.5 cursor-pointer select-none">
            <input 
              v-model="agreeTerms" 
              type="checkbox" 
              class="w-4 h-4 mt-0.5 rounded border-zinc-300 text-black focus:ring-black cursor-pointer"
            />
            <span class="text-xs text-zinc-600 font-normal leading-tight">
              Saya menyetujui <a href="#syarat" class="text-black font-semibold underline">Syarat & Ketentuan</a> serta <a href="#privasi" class="text-black font-semibold underline">Kebijakan Privasi</a>.
            </span>
          </label>
        </div>

        <!-- Submit Button (rounded-lg) -->
        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full bg-black hover:bg-zinc-800 disabled:bg-zinc-400 text-white font-semibold text-sm py-3.5 rounded-lg shadow-xs hover:shadow-md transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer mt-3"
        >
          <span v-if="!isLoading">Daftar & Gabung Doa</span>
          <span v-else class="flex items-center gap-2">
            <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>Memproses...</span>
          </span>
          <ArrowRight v-if="!isLoading" :size="16" />
        </button>
      </form>

      <!-- Divider -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-zinc-200"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase">
          <span class="bg-white px-3 text-zinc-400 font-medium tracking-wider">
            atau daftar dengan
          </span>
        </div>
      </div>

      <!-- Google OAuth Button (rounded-lg) -->
      <button 
        type="button" 
        class="w-full bg-white hover:bg-zinc-50 border border-zinc-300 text-black font-semibold text-sm py-3 rounded-lg transition-colors flex items-center justify-center gap-3 cursor-pointer"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Google</span>
      </button>

      <!-- Footer Login Prompt -->
      <p class="text-center text-xs text-zinc-500 mt-6 border-t border-zinc-100 pt-3">
        Sudah memiliki akun?
        <RouterLink to="/masuk" class="font-bold text-black hover:underline underline-offset-4 ml-1">
          Masuk di sini
        </RouterLink>
      </p>

    </div>

    <!-- Register Success Modal -->
    <SuccessModal
      :is-open="showSuccessModal"
      title="Pendaftaran Berhasil!"
      message="Akun Anda berhasil dibuat dan siap digunakan. Klik tombol di bawah untuk melanjutkan ke halaman utama."
      :user-name="registeredUserName"
      button-text="Lanjutkan ke Beranda"
      @confirm="navigateHome"
    />
  </div>
</template>
