<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Task } from '../types'
import TaskModal from '../components/TaskModal.vue'
import ConfirmModal from '../components/ConfirmModal.vue'
import Pagination from '../components/Pagination.vue'
import TaskDetailModal from '../components/TaskDetailModal.vue'
import { useStore } from 'vuex'

type Filters = {
  title: string
  status: '' | 'open' | 'in_progress' | 'done'
  due_date: string
}

const store = useStore()

const filters = ((store.state as any).tasks.filters) as Filters
const sortField = computed({
  get: () => ((store.state as any).tasks.sortField as '' | 'title' | 'status' | 'due_date' | 'created_at'),
  set: (v: '' | 'title' | 'status' | 'due_date' | 'created_at') => store.commit('tasks/setSort', { field: v, dir: (store.state as any).tasks.sortDir }),
})
const sortDir = computed({
  get: () => ((store.state as any).tasks.sortDir as '' | 'asc' | 'desc'),
  set: (v: '' | 'asc' | 'desc') => store.commit('tasks/setSort', { field: (store.state as any).tasks.sortField, dir: v }),
})
const taskMeta = computed(() => (store.state as any).tasks.meta)
const taskLoading = computed(() => (store.state as any).tasks.loading)
const taskError = computed(() => (store.state as any).tasks.error)

const showCreate = ref(false)
const showEdit = ref(false)
const selectedTask = ref<Task | null>(null)
const showConfirm = ref(false)
const showDetail = ref(false)
const detailLoading = ref(false)
const creating = ref(false)
const updating = ref(false)
const deleting = ref(false)
const applying = ref(false)
const selectedTaskTitle = computed(() => selectedTask.value?.title ?? '')
const confirmMessage = computed(() => `Are you sure you want to delete "${selectedTaskTitle.value}"?`)

const route = useRoute()
const router = useRouter()
const initialLoadDone = ref(false)

const filteredTasks = computed(() => {
  if (!filters.due_date) return (store.state as any).tasks.tasks as Task[]
  const date = filters.due_date
  return ((store.state as any).tasks.tasks as Task[]).filter((t) => (t.due_date ? t.due_date.startsWith(date) : false))
})

const fetchTasks = async (page = 1) => store.dispatch('tasks/fetch', page)

const resetFilters = () => {
  filters.title = ''
  filters.status = ''
  filters.due_date = ''
  sortField.value = ''
  sortDir.value = ''

  console.log('resetFilters filters sortField sortDir', filters, sortField.value, sortDir.value)
  router.push({ path: route.path })
  fetchTasks(1)
}

const onOpenCreate = () => {
  selectedTask.value = null
  showCreate.value = true
}

const onOpenEdit = (t: Task) => {
  selectedTask.value = t
  showEdit.value = true
}

const onOpenDetail = async (t: Task) => {
  selectedTask.value = t
  showDetail.value = true
  detailLoading.value = true
  try {
    const data = await store.dispatch('tasks/detail', t._id)
    selectedTask.value = data
  } catch (e) {
  } finally {
    detailLoading.value = false
  }
}

const createTask = async (form: Partial<Task>) => {
  try {
    creating.value = true
    await store.dispatch('tasks/create', form)
    showCreate.value = false
    await fetchTasks(taskMeta.value.current_page)
  } catch (e: any) {
    alert(e?.message || 'Failed to create task')
  } finally {
    creating.value = false
  }
}

const updateTask = async (form: Partial<Task>) => {
  if (!selectedTask.value) return
  try {
    updating.value = true
    await store.dispatch('tasks/update', { id: selectedTask.value._id, payload: form })
    showEdit.value = false
    await fetchTasks(taskMeta.value.current_page)
  } catch (e: any) {
    alert(e?.message || 'Failed to update task')
  } finally {
    updating.value = false
  }
}

const askDelete = (t: Task) => {
  selectedTask.value = t
  showConfirm.value = true
}

const confirmDelete = async () => {
  if (!selectedTask.value) return
  try {
    deleting.value = true
    await store.dispatch('tasks/remove', selectedTask.value._id)
    showConfirm.value = false
    await fetchTasks(taskMeta.value.current_page)
  } catch (e: any) {
    alert(e?.message || 'Failed to delete task')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  // console.log('onMounted route.query', route.query)
  if (typeof route.query.title === 'string') filters.title = route.query.title
  if (typeof route.query.status === 'string') filters.status = route.query.status as Filters['status']
  if (typeof route.query.due_date === 'string') filters.due_date = route.query.due_date
  if (typeof route.query.sort_field === 'string') sortField.value = route.query.sort_field as any
  if (typeof route.query.sort_dir === 'string') sortDir.value = route.query.sort_dir as any
  
  const initialPage = route.query.page ? Number(route.query.page) : 1
  await fetchTasks(isNaN(initialPage) ? 1 : initialPage)
  initialLoadDone.value = true
})

// watch(
//   () => route.query.page,
//   (page) => {
//     console.log('watch page', page)
//     if (!initialLoadDone.value) return
//     const p = page ? Number(page) : 1
//     fetchTasks(isNaN(p) ? 1 : p)
//   }
// )

const onPageChange = (page: number) => {
  router.push({
    path: route.path,
    query: {
      ...(filters.title ? { title: filters.title } : {}),
      ...(filters.status ? { status: filters.status } : {}),
      ...(filters.due_date ? { due_date: filters.due_date } : {}),
      ...(sortField.value && sortDir.value ? { sort_field: sortField.value, sort_dir: sortDir.value } : {}),
      page: String(page),
    },
  })
  fetchTasks(page)
}

const applyFilters = async () => {
  applying.value = true
  await router.push({
    path: route.path,
    query: {
      ...(filters.title ? { title: filters.title } : {}),
      ...(filters.status ? { status: filters.status } : {}),
      ...(filters.due_date ? { due_date: filters.due_date } : {}),
      ...(sortField.value && sortDir.value ? { sort_field: sortField.value, sort_dir: sortDir.value } : {}),
      page: '1',
    },
  })
  await fetchTasks(1)
  applying.value = false
}

watch(
  () => [
    route.query.title, 
    route.query.status, 
    route.query.due_date, 
    route.query.sort_field, 
    route.query.sort_dir
  ],
  () => {
    if (!initialLoadDone.value) return
    filters.title = typeof route.query.title === 'string' ? route.query.title : ''
    filters.status = typeof route.query.status === 'string' ? (route.query.status as Filters['status']) : ''
    filters.due_date = typeof route.query.due_date === 'string' ? route.query.due_date : ''
    sortField.value = typeof route.query.sort_field === 'string' ? (route.query.sort_field as any) : ''
    sortDir.value = typeof route.query.sort_dir === 'string' ? (route.query.sort_dir as any) : ''
  }
)
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
        <div>
          <label class="label">Search Title</label>
          <input v-model="filters.title" class="input" placeholder="Search by title" />
        </div>
        <div>
          <label class="label">Status</label>
          <select v-model="filters.status" class="select">
            <option value="">All</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="done">Done</option>
          </select>
        </div>
        <div>
          <label class="label">Due Date</label>
          <input v-model="filters.due_date" type="date" class="input" />
        </div>
        <div>
          <label class="label">Sort Field</label>
          <select v-model="sortField" class="select">
            <option value="">None</option>
            <option value="title">Title</option>
            <option value="status">Status</option>
            <option value="due_date">Due Date</option>
            <option value="created_at">Created At</option>
          </select>
        </div>
        <div>
          <label class="label">Sort Direction</label>
          <select v-model="sortDir" class="select">
            <option value="">None</option>
            <option value="asc">Ascending (Earliest)</option>
            <option value="desc">Descending (Latest)</option>
          </select>
        </div>
      </div>
      <div class="flex gap-2">
        <button class="btn-secondary" @click="resetFilters" :disabled="taskLoading || applying">Reset</button>
        <button class="btn-primary" @click="applyFilters" :disabled="taskLoading || applying">
          {{ applying ? 'Applying…' : 'Apply' }}
        </button>
      </div>
    </div>

    <div class="card overflow-hidden">
      <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
        <h3 class="text-base font-semibold">Tasks</h3>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Total: {{ taskMeta.total }}</span>
          <button class="btn-primary" @click="onOpenCreate">New Task</button>
        </div>
      </div>
      <div class="overflow-x-auto p-4">
        <table class="min-w-full text-left text-sm">
          <thead>
            <tr class="text-gray-600">
              <th class="px-3 py-2">Title</th>
              <th class="px-3 py-2">Status</th>
              <th class="px-3 py-2">Due Date</th>
              <th class="px-3 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="taskLoading">
              <td colspan="4" class="px-3 py-6 text-center text-gray-500">Loading…</td>
            </tr>
            <tr v-else-if="taskError">
              <td colspan="4" class="px-3 py-6 text-center text-red-600">{{ taskError }}</td>
            </tr>
            <tr v-else-if="filteredTasks.length === 0">
              <td colspan="4" class="px-3 py-6 text-center text-gray-500">No tasks found</td>
            </tr>
            <tr v-for="t in filteredTasks" :key="t._id" class="border-t border-gray-100">
              <td class="px-3 py-2 font-medium">
                <button class="text-blue-600 hover:underline" @click="onOpenDetail(t)">{{ t.title }}</button>
              </td>
              <td class="px-3 py-2">
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                  :class="{
                    'bg-amber-100 text-amber-800': t.status === 'open',
                    'bg-blue-100 text-blue-800': t.status === 'in_progress',
                    'bg-emerald-100 text-emerald-800': t.status === 'done',
                  }"
                  >{{ t.status.replace('_', ' ') }}</span
                >
              </td>
              <td class="px-3 py-2">{{ t.due_date ? t.due_date.substring(0, 10) : '-' }}</td>
              <td class="px-3 py-2 text-right">
                <div class="flex justify-end gap-2">
                  <button class="btn-secondary" @click="onOpenDetail(t)" :disabled="detailLoading">{{ detailLoading ? 'Loading…' : 'View' }}</button>
                  <button class="btn-secondary" @click="onOpenEdit(t)" :disabled="updating || creating || taskLoading">Edit</button>
                  <button class="btn-secondary" @click="askDelete(t)" :disabled="deleting || taskLoading">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="border-t border-gray-200 px-4 py-3">
        <Pagination :current="taskMeta.current_page" :total-pages="taskMeta.last_page" :disabled="taskLoading" @change="onPageChange" />
      </div>
    </div>

    <TaskModal :open="showCreate" mode="create" :busy="creating" @close="showCreate = false" @submit="createTask" />
    <TaskModal :open="showEdit" mode="update" :task="selectedTask" :busy="updating" @close="showEdit = false" @submit="updateTask" />
    <TaskDetailModal :open="showDetail" :task="selectedTask" :loading="detailLoading" @close="showDetail = false" />
    <ConfirmModal
      :open="showConfirm"
      title="Delete Task"
      :message="confirmMessage"
      :busy="deleting"
      @close="showConfirm = false"
      @confirm="confirmDelete"
    />
  </div>
</template>

<style scoped></style>
