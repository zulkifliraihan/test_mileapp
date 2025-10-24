<script setup lang="ts">
import { useRouter, RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
auth.load()

function logout() {
  auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen">
    <header class="border-b bg-white">
      <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="text-lg font-semibold">Mileapp</div>
          <nav class="text-sm text-gray-600 flex items-center gap-3">
            <RouterLink class="hover:text-indigo-600" to="/dashboard/tasks">Tasks</RouterLink>
          </nav>
        </div>
        <div class="flex items-center gap-3 text-sm">
          <span class="text-gray-600">{{ auth.user?.name || 'User' }}</span>
          <button @click="logout" class="rounded-md border border-gray-300 px-3 py-1.5 hover:bg-gray-50">Logout</button>
        </div>
      </div>
    </header>
    <main class="mx-auto max-w-6xl p-4">
      <RouterView />
    </main>
  </div>
  
</template>

<style scoped>
</style>

