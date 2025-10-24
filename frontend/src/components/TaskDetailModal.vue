<script setup lang="ts">
import type { Task } from '../types'

defineProps<{
  open: boolean
  task: Task | null
  loading?: boolean
}>()
defineEmits<{ (e: 'close'): void }>()

function formatDate(d?: string) {
  if (!d) return '-'
  try {
    return d.substring(0, 10)
  } catch {}
  return d
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="card w-full max-w-2xl p-6">
      <div class="mb-4 flex items-start justify-between gap-4">
        <h3 class="text-lg font-semibold">Task Detail</h3>
        <button class="btn-secondary" @click="$emit('close')">Close</button>
      </div>

      <div v-if="loading" class="py-8 text-center text-sm text-gray-500">Loading…</div>
      <div v-else-if="!task" class="py-8 text-center text-sm text-gray-500">No data</div>
      <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <div class="label">Title</div>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">{{ task.title }}</div>
        </div>
        <div>
          <div class="label">Status</div>
          <div class="px-3 py-2">
            <span
              class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
              :class="{
                'bg-amber-100 text-amber-800': task.status === 'open',
                'bg-blue-100 text-blue-800': task.status === 'in_progress',
                'bg-emerald-100 text-emerald-800': task.status === 'done',
              }"
              >{{ task.status.replace('_', ' ') }}</span
            >
          </div>
        </div>
        <div>
          <div class="label">Due Date</div>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">{{ formatDate(task.due_date) }}</div>
        </div>
        <div>
          <div class="label">Created / Updated</div>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
            {{ formatDate(task.created_at) }} / {{ formatDate(task.updated_at) }}
          </div>
        </div>
        <div class="sm:col-span-2">
          <div class="label">Description</div>
          <div class="whitespace-pre-wrap rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">{{ task.description || '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
