<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { fetchTasks, createTask, updateTask, deleteTask, type Task } from '@/services/api'

const auth = useAuthStore()
auth.load()

const items = ref<Task[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const page = ref(1)
const perPage = 10

const showForm = ref(false)
const isEditing = ref(false)
const form = ref<Task>({ title: '', description: '', status: 'open', due_date: '' })

const canSubmit = computed(() => form.value.title.trim().length > 0 && !!form.value.status)

async function load() {
  loading.value = true
  error.value = null
  try {
    const res: any = await fetchTasks(auth.token!, page.value, perPage)
    const tasks = res?.data?.tasks || []
    items.value = tasks
  } catch (e: any) {
    error.value = e?.message || 'Failed to load tasks'
  } finally {
    loading.value = false
  }
}

function openCreate() {
  isEditing.value = false
  form.value = { title: '', description: '', status: 'open', due_date: '' }
  showForm.value = true
}

function openEdit(task: Task) {
  isEditing.value = true
  form.value = { ...task }
  showForm.value = true
}

async function submitForm() {
  if (!canSubmit.value) return
  try {
    if (isEditing.value && form.value._id) {
      await updateTask(auth.token!, form.value._id, form.value)
    } else {
      await createTask(auth.token!, form.value)
    }
    showForm.value = false
    await load()
  } catch (e: any) {
    alert(e?.message || 'Failed to save task')
  }
}

async function remove(id?: string) {
  if (!id) return
  if (!confirm('Delete this task?')) return
  try {
    await deleteTask(auth.token!, id)
    await load()
  } catch (e: any) {
    alert(e?.message || 'Failed to delete')
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Tasks</h1>
      <button @click="openCreate" class="rounded-lg bg-indigo-600 text-white px-3 py-2 text-sm font-medium hover:bg-indigo-700">New Task</button>
    </div>

    <div v-if="error" class="text-sm text-red-600">{{ error }}</div>

    <div class="overflow-hidden rounded-xl border bg-white">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="px-4 py-2 text-left">Title</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Due</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-8 text-center text-gray-500">Loading...</td>
          </tr>
          <tr v-else-if="!items.length">
            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No tasks</td>
          </tr>
          <tr v-for="t in items" :key="t._id" class="border-t">
            <td class="px-4 py-2 font-medium">{{ t.title }}</td>
            <td class="px-4 py-2">
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                    :class="{
                      'bg-gray-100 text-gray-700': t.status==='open',
                      'bg-amber-100 text-amber-800': t.status==='in_progress',
                      'bg-emerald-100 text-emerald-800': t.status==='done',
                    }"
              >{{ t.status }}</span>
            </td>
            <td class="px-4 py-2">{{ t.due_date || '-' }}</td>
            <td class="px-4 py-2 text-right space-x-2">
              <button @click="openEdit(t)" class="rounded-md border px-2 py-1 text-xs hover:bg-gray-50">Edit</button>
              <button @click="remove(t._id)" class="rounded-md border px-2 py-1 text-xs hover:bg-gray-50">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showForm" class="fixed inset-0 z-50 grid place-items-center bg-black/40 p-4">
      <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-lg">
        <h2 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit Task' : 'New Task' }}</h2>
        <form @submit.prevent="submitForm" class="space-y-3">
          <div>
            <label class="block text-sm font-medium mb-1">Title</label>
            <input v-model="form.title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium mb-1">Status</label>
              <select v-model="form.status" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="open">open</option>
                <option value="in_progress">in_progress</option>
                <option value="done">done</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Due date</label>
              <input v-model="form.due_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showForm=false" class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="!canSubmit" class="rounded-md bg-indigo-600 text-white px-3 py-2 text-sm font-medium disabled:opacity-50">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>

