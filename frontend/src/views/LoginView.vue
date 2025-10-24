<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'

const email = ref('admin@mileapp.com')
const password = ref('12345678')
const loading = ref(false)
const error = ref<string | null>(null)
const store = useStore()
const router = useRouter()
const route = useRoute()

const onSubmit = async () => {
  console.log('onSubmit LoginView - before try')
  loading.value = true
  error.value = null
  try {
    await store.dispatch('auth/login', { email: email.value, password: password.value })
    console.log('onSubmit LoginView - after store.dispatch')
    
    const redirect = (route.query.redirect as string) || '/dashboard/tasks'
    router.push(redirect)
  } catch (e: any) {
    error.value = e?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-[70vh] items-center justify-center">
    <div class="card w-full max-w-md p-8">
      <h2 class="mb-6 text-center text-2xl font-semibold">Sign in</h2>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
          <label class="label" for="email">Email</label>
          <input id="email" v-model="email" type="email" required class="input" placeholder="you@example.com" />
        </div>
        <div>
          <label class="label" for="password">Password</label>
          <input id="password" v-model="password" type="password" required class="input" placeholder="••••••••" />
        </div>
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <button type="submit" class="btn-primary w-full" :disabled="loading">
          {{ loading ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped></style>
