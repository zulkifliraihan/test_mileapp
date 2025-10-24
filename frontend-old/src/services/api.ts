export type ApiConfig = {
  baseURL: string
}

export const apiConfig: ApiConfig = {
  baseURL: import.meta.env.VITE_APP_API_URL || '',
}

type HttpOptions = {
  method?: string
  headers?: Record<string, string>
  body?: any
  token?: string | null
}

async function http(path: string, { method = 'GET', headers = {}, body, token }: HttpOptions = {}) {
  const url = `${apiConfig.baseURL}${path}`
  const allHeaders: Record<string, string> = {
    'Content-Type': 'application/json',
    ...headers,
  }
  if (token) allHeaders['Authorization'] = `Bearer ${token}`

  const res = await fetch(url, {
    method,
    headers: allHeaders,
    body: body ? JSON.stringify(body) : undefined,
  })
  if (!res.ok) {
    let err
    try { err = await res.json() } catch { err = await res.text() }
    throw err
  }
  try { return await res.json() } catch { return null }
}

// Auth
export async function loginApi(email: string, password: string): Promise<{ token: string, user?: any }> {
  if (!apiConfig.baseURL) {
    // Mock fallback if no API URL configured
    return new Promise(resolve => setTimeout(() => resolve({ token: 'mock-token-123', user: { name: 'Admin Mileapp', email } }), 400))
  }
  const data = await http('/api/login', { method: 'POST', body: { email, password } })
  const token = data?.data?.authorization?.token
  const user = data?.data?.user
  return { token, user }
}

// Tasks endpoints based on Postman collection
export type Task = {
  _id?: string
  title: string
  description?: string
  status: 'open' | 'in_progress' | 'done'
  due_date?: string
}

export async function fetchTasks(token: string, page = 1, perPage = 10) {
  if (!apiConfig.baseURL) {
    // Mock list
    return new Promise(resolve => setTimeout(() => resolve({ data: { meta: { current_page: 1, total: 2 }, tasks: [
      { _id: '1', title: 'Sample Task', description: 'Demo task', status: 'open', due_date: '2025-12-31' },
      { _id: '2', title: 'Second Task', description: 'Another demo', status: 'in_progress', due_date: '2025-11-30' },
    ] } }), 300))
  }
  return http(`/api/tasks?per_page=${perPage}&page=${page}`, { token })
}

export async function createTask(token: string, task: Task) {
  if (!apiConfig.baseURL) {
    return new Promise(resolve => setTimeout(() => resolve({ data: { ...task, _id: String(Math.random()).slice(2) } }), 300))
  }
  return http('/api/tasks', { method: 'POST', body: task, token })
}

export async function updateTask(token: string, id: string, task: Task) {
  if (!apiConfig.baseURL) {
    return new Promise(resolve => setTimeout(() => resolve({ data: { ...task, _id: id } }), 300))
  }
  return http(`/api/tasks/${id}`, { method: 'PUT', body: task, token })
}

export async function deleteTask(token: string, id: string) {
  if (!apiConfig.baseURL) {
    return new Promise(resolve => setTimeout(() => resolve({ data: null }), 200))
  }
  return http(`/api/tasks/${id}`, { method: 'DELETE', token })
}

