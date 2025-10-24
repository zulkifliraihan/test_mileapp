<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { loginApi } from '@/services/api'

const router = useRouter()
const auth = useAuthStore()
auth.load()

const email = ref('admin@mileapp.com')
const password = ref('12345678')
const loading = ref(false)
const error = ref<string | null>(null)

async function onSubmit() {
  error.value = null
  loading.value = true
  try {
    const { token, user } = await loginApi(email.value, password.value)
    if (!token) throw new Error('Token not found')
    auth.setAuth(token, user)
    router.push({ name: 'tasks' })
  } catch (e: any) {
    error.value = e?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white shadow-sm rounded-xl p-6">
      <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold tracking-tight">Sign in</h1>
        <p class="text-sm text-gray-500">Access your dashboard</p>
      </div>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="email" type="email" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="you@example.com" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <input v-model="password" type="password" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="••••••••" />
        </div>
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <button type="submit" :disabled="loading" class="w-full rounded-lg bg-indigo-600 text-white py-2.5 font-medium hover:bg-indigo-700 disabled:opacity-50">
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
      <p class="mt-4 text-xs text-gray-500">Mock auth enabled if no API URL configured.</p>
    </div>
  </div>
  
</template>

<style scoped>
</style>

