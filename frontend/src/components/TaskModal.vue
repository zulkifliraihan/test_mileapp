<script setup lang="ts">
import { reactive, watch, computed } from 'vue'
import type { Task } from '../types'

type FormTask = Pick<Task, 'title' | 'description' | 'status' | 'due_date'>

const props = withDefaults(
  defineProps<{ open: boolean; task?: Task | null; mode?: 'create' | 'update'; busy?: boolean }>(),
  { mode: 'create', busy: false }
)
const emit = defineEmits<{ (e: 'close'): void; (e: 'submit', data: FormTask): void }>()

const form = reactive<FormTask>({
  title: '',
  description: '',
  status: 'open',
  due_date: '',
})

const title = computed(() => (props.mode === 'create' ? 'Create Task' : 'Update Task'))

watch(
  () => props.task,
  (t) => {
    if (!t) return
    form.title = t.title
    form.description = t.description || ''
    form.status = t.status
    form.due_date = t.due_date ? t.due_date.substring(0, 10) : ''
  },
  { immediate: true }
)

function onSubmit() {
  emit('submit', { ...form })
}
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="card w-full max-w-lg p-6">
      <h3 class="mb-4 text-lg font-semibold">{{ title }}</h3>
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
          <label class="label" for="title">Title</label>
          <input id="title" v-model="form.title" required class="input" />
        </div>
        <div>
          <label class="label" for="status">Status</label>
          <select id="status" v-model="form.status" class="select">
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
        <div>
          <label class="label" for="due">Due Date</label>
          <input id="due" v-model="form.due_date" type="date" class="input" />
        </div>
        <div>
          <label class="label" for="desc">Description</label>
          <textarea id="desc" v-model="form.description" rows="4" class="input"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="btn-secondary" @click="$emit('close')" :disabled="busy">Cancel</button>
          <button type="submit" class="btn-primary" :disabled="busy">
            {{ busy ? (mode === 'create' ? 'Creating…' : 'Updating…') : (mode === 'create' ? 'Create' : 'Update') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped></style>
