<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
    <div
      v-for="i in 6"
      :key="i"
      class="bg-white rounded-2xl border border-zinc-200/80 p-6 space-y-4"
    >
      <div class="flex items-center justify-between">
        <div class="w-28 h-6 bg-zinc-200 rounded-full"></div>
        <div class="w-16 h-4 bg-zinc-100 rounded-md"></div>
      </div>
      <div class="space-y-2">
        <div class="w-3/4 h-5 bg-zinc-200 rounded-md"></div>
        <div class="w-full h-3 bg-zinc-100 rounded-md"></div>
        <div class="w-2/3 h-3 bg-zinc-100 rounded-md"></div>
      </div>
      <div class="pt-4 border-t border-zinc-100 flex justify-between items-center">
        <div class="w-24 h-4 bg-zinc-200 rounded-md"></div>
        <div class="w-24 h-8 bg-zinc-200 rounded-xl"></div>
      </div>
    </div>
  </div>
</template>
