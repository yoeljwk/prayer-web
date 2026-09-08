<template>
  <div class="space-y-4">
    <div
      v-for="i in 3"
      :key="i"
      class="bg-white rounded-xl p-6 border border-zinc-200/80 animate-pulse space-y-4"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-zinc-200"></div>
          <div class="space-y-2">
            <div class="w-28 h-3.5 bg-zinc-200 rounded-md"></div>
            <div class="w-16 h-2.5 bg-zinc-100 rounded-md"></div>
          </div>
        </div>
        <div class="w-20 h-5 bg-zinc-100 rounded-full"></div>
      </div>

      <div class="space-y-2">
        <div class="w-full h-3.5 bg-zinc-200 rounded-md"></div>
        <div class="w-4/5 h-3.5 bg-zinc-200 rounded-md"></div>
        <div class="w-2/3 h-3.5 bg-zinc-100 rounded-md"></div>
      </div>

      <div class="pt-4 border-t border-zinc-100 flex justify-between">
        <div class="w-32 h-8 bg-zinc-100 rounded-lg"></div>
        <div class="w-16 h-4 bg-zinc-100 rounded-md"></div>
      </div>
    </div>
  </div>
</template>
