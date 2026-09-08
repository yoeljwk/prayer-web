<script setup lang="ts">
interface Props {
  isOpen: boolean
  title: string
  message: string
  userName?: string
  buttonText?: string
}

withDefaults(defineProps<Props>(), {
  userName: '',
  buttonText: 'Lanjutkan ke Beranda',
})

const emit = defineEmits<{
  (e: 'confirm'): void
}>()
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
    >
      <div
        class="w-full max-w-sm bg-white rounded-xl p-6 sm:p-7 text-center border border-zinc-200 shadow-xl"
        @click.stop
      >
        <!-- Header -->
        <h2 class="font-serif-custom text-2xl font-normal text-black mb-1">
          {{ title }}
        </h2>
        
        <!-- User Subtitle -->
        <p v-if="userName" class="text-sm font-semibold text-zinc-900 mb-2">
          Selamat datang, {{ userName }}!
        </p>

        <!-- Description -->
        <p class="text-xs text-zinc-500 leading-relaxed mb-6">
          {{ message }}
        </p>

        <!-- Action Button -->
        <button
          type="button"
          @click="emit('confirm')"
          class="w-full bg-black hover:bg-zinc-800 text-white font-semibold text-xs uppercase tracking-wider py-3.5 rounded-lg transition-colors cursor-pointer"
        >
          {{ buttonText }}
        </button>
      </div>
    </div>
  </Transition>
</template>
