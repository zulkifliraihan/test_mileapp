type User = { id: string; name: string; email: string }
export type AuthState = { token: string | null; user: User | null }

const BASE_URL = import.meta.env.VITE_API_BACKEND_URL || 'http://localhost:8000'

const authModule = {
  namespaced: true,
  state: (): AuthState => ({
    token: localStorage.getItem('token'),
    user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user') as string) : null,
  }),
  getters: {
    isAuthenticated: (state: AuthState) => !!state.token,
  },
  mutations: {
    setAuth(state: AuthState, payload: { token: string; user: User }) {
      state.token = payload.token
      state.user = payload.user
      localStorage.setItem('token', payload.token)
      localStorage.setItem('user', JSON.stringify(payload.user))
    },
    clearAuth(state: AuthState) {
      state.token = null
      state.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    },
  },
  actions: {
    async login({ commit }: any, { email, password }: { email: string; password: string }) {
      const res = await fetch(`${import.meta.env.VITE_API_BACKEND_URL || 'http://localhost:8000'}/api/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data?.message || 'Login failed')
      const token = data?.data?.authorization?.token as string
      const user = data?.data?.user as User
      if (!token) throw new Error('Invalid login response')
      commit('setAuth', { token, user })
    },
    async logout({ commit }: any) {
      try {
        const token = localStorage.getItem('token')
        await fetch(`${BASE_URL}/api/logout`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
        })
      } catch (e) {
        // ignore errors to ensure client logout proceeds
      } finally {
        commit('clearAuth')
      }
    },
  },
}

export default authModule
