import { createRouter, createWebHistory, type NavigationGuardNext, type RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const Login = () => import('@/pages/Login.vue')
const Tasks = () => import('@/pages/tasks/TasksPage.vue')
const DashboardLayout = () => import('@/layouts/DashboardLayout.vue')

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/login' },
    { path: '/login', name: 'login', component: Login, meta: { guestOnly: true } },
    {
      path: '/dashboard',
      component: DashboardLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: '/dashboard/tasks' },
        { path: 'tasks', name: 'tasks', component: Tasks }
      ]
    }
  ]
})

router.beforeEach((to: RouteLocationNormalized, _from: RouteLocationNormalized, next: NavigationGuardNext) => {
  const auth = useAuthStore()
  // Ensure persisted auth is loaded before checks
  if (!auth.token) auth.load()
  const isAuthed = !!auth.token

  if (to.meta.requiresAuth && !isAuthed) return next({ name: 'login' })
  if (to.meta.guestOnly && isAuthed) return next({ name: 'tasks' })
  next()
})

export default router
