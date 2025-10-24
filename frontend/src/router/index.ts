import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import TasksView from '../views/TasksView.vue'
import store from '../store'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/login' },
    { path: '/login', name: 'login', component: LoginView, meta: { public: true } },
    {
      path: '/dashboard/tasks',
      name: 'tasks',
      component: TasksView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to) => {
  const isAuthenticated = (store.getters as any)['auth/isAuthenticated'] as boolean
  if (to.meta.requiresAuth && !isAuthenticated) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
  if (to.path === '/login' && isAuthenticated) {
    return { path: '/dashboard/tasks' }
  }
})

export default router
