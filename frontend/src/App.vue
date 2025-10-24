<script setup lang="ts">
import { computed } from 'vue'
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'

const store = useStore()
const route = useRoute()
const router = useRouter()

const showNavbar = computed(() => route.path.startsWith('/dashboard'))

const logout = async () => {
  // await store.dispatch('auth/logout')
  // router.push('/login')
  try {
    await store.dispatch('auth/logout')
  } finally {
    console.log('after logout')
    router.replace({ path: '/login' })
  }
}
</script>

<template>
  <div class="min-h-full">
    <header v-if="showNavbar" class="sticky top-0 z-10 border-b border-gray-200 bg-white/80 backdrop-blur">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
          <div class="h-8 w-8 rounded-lg bg-blue-600"></div>
          <h1 class="text-lg font-semibold">Mileapp Tasks</h1>
        </div>
        <div class="flex items-center gap-3">
          <span class="hidden text-sm text-gray-600 sm:inline">{{ (store.state as any).auth.user?.name || 'User' }}</span>
          <button class="btn-secondary" @click="logout">Logout</button>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <router-view />
    </main>
  </div>
  
</template>

<style scoped>
</style>
