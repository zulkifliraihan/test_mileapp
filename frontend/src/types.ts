export type Task = {
  _id: string
  title: string
  description?: string
  status: 'open' | 'in_progress' | 'done'
  due_date?: string
  created_at?: string
  updated_at?: string
}

export type PaginatedMeta = {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

