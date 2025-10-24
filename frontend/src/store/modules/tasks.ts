import type { Task, PaginatedMeta } from '../../types'

const BASE_URL = import.meta.env.VITE_API_BACKEND_URL || 'http://localhost:8000'

function buildQuery(params: Record<string, any>) {
  const q = new URLSearchParams()
  Object.entries(params).forEach(([k, v]) => {
    if (v === undefined || v === null || v === '') return
    if (typeof v === 'object' && !Array.isArray(v)) {
      Object.entries(v).forEach(([sk, sv]) => {
        if (sv === undefined || sv === null || sv === '') return
        q.append(`${k}[${sk}]`, String(sv))
      })
    } else {
      q.append(k, String(v))
    }
  })
  return q.toString()
}

export type TasksState = {
  tasks: Task[]
  meta: PaginatedMeta
  loading: boolean
  error: string | null
  filters: { title: string; status: '' | 'open' | 'in_progress' | 'done'; due_date: string }
  sortField: '' | 'title' | 'status' | 'due_date' | 'created_at'
  sortDir: '' | 'asc' | 'desc'
}

const tasksModule = {
  namespaced: true,
  state: (): TasksState => ({
    tasks: [],
    meta: { current_page: 1, last_page: 1, per_page: 10, total: 0 },
    loading: false,
    error: null,
    filters: { title: '', status: '', due_date: '' },
    sortField: '',
    sortDir: '',
  }),
  mutations: {
    setLoading(state: TasksState, v: boolean) {
      state.loading = v
    },
    setError(state: TasksState, msg: string | null) {
      state.error = msg
    },
    setTasks(state: TasksState, list: Task[]) {
      state.tasks = list
    },
    setMeta(state: TasksState, meta: PaginatedMeta) {
      state.meta = meta
    },
    setFilters(state: TasksState, next: Partial<{ title: string; status: 'open' | 'in_progress' | 'done' | ''; due_date: string }>) {
      state.filters = { ...state.filters, ...next }
    },
    setSort(state: TasksState, payload: { field: '' | 'title' | 'status' | 'due_date' | 'created_at'; dir: '' | 'asc' | 'desc' }) {
      state.sortField = payload.field
      state.sortDir = payload.dir
    },
  },
  actions: {
    async fetch({ state, commit }: any, page: number = 1) {
      console.log('fetch TasksModule - actions - before init')

      commit('setLoading', true)
      commit('setError', null)

      try {
        console.log(`state.filters ${state.filters}`)
        const params: any = {
          per_page: state.meta.per_page,
          page,
          filter: {
            ...(state.filters.title ? { title: state.filters.title } : {}),
            ...(state.filters.status ? { status: state.filters.status } : {}),
            ...(state.filters.due_date ? { due_date: state.filters.due_date } : {}),
          },
        }
        console.log('fetch TasksModule - actions - params', params)
        
        if (state.sortField && state.sortDir) params.sort = { field: state.sortField, dir: state.sortDir }
        
        const q = buildQuery(params)
        const token = localStorage.getItem('token')
        // console.log('fetch TasksModule - actions - token', token)

        
        const res = await fetch(`${BASE_URL}/api/tasks?${q}`, {
          headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
        })
        const respData = await res.json()
        // console.log('respData', respData)
        if (!res.ok) throw new Error(respData?.message || 'Failed to load tasks')
        const data = respData?.data
        commit('setMeta', { ...state.meta, ...(data?.meta || {}) })
        commit('setTasks', (data?.tasks || []) as Task[])
      } catch (e: any) {
        commit('setError', e?.message || 'Failed to load tasks')
      } finally {
        commit('setLoading', false)
      }
    },
    async create(_: any, payload: Partial<Task>) {
      console.log('create TasksModule - actions - before init')

      const token = localStorage.getItem('token')
      const res = await fetch(`${BASE_URL}/api/tasks`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: JSON.stringify(payload),
      })
      // console.log('create TasksModule - actions - res', res)
      console.log(res.ok)
      if (!res.ok) {
        const data = await res.json().catch(() => ({}))
        throw new Error(data?.message || 'Failed to create task')
      }
    },
    async update(_: any, { id, payload }: { id: string; payload: Partial<Task> }) {
      console.log('update TasksModule - actions - before init')
      // console.log(payload)
      
      console.log('update TasksModule - actions - id, payload', id, payload)
      const token = localStorage.getItem('token')
      const res = await fetch(`${BASE_URL}/api/tasks/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
        body: JSON.stringify(payload),
      })
      if (!res.ok) {
        const data = await res.json().catch(() => ({}))
        throw new Error(data?.message || 'Failed to update task')
      }
    },
    async remove(_: any, id: string) {
      const token = localStorage.getItem('token')
      const res = await fetch(`${BASE_URL}/api/tasks/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
      })
      if (!res.ok) {
        const data = await res.json().catch(() => ({}))
        throw new Error(data?.message || 'Failed to delete task')
      }
    },
    async detail(_: any, id: string): Promise<Task> {
      const token = localStorage.getItem('token')
      const res = await fetch(`${BASE_URL}/api/tasks/${id}`, {
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
        },
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data?.message || 'Failed to get task detail')
      return data?.data as Task
    },
    setFilters({ commit }: any, next: Partial<{ title: string; status: 'open' | 'in_progress' | 'done' | ''; due_date: string }>) {
      commit('setFilters', next)
    },
    setSort({ commit }: any, payload: { field: '' | 'title' | 'status' | 'due_date' | 'created_at'; dir: '' | 'asc' | 'desc' }) {
      commit('setSort', payload)
    },
  },
}

export default tasksModule
