import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import CashierView from '../views/CashierView.vue'
import DashboardView from '../views/DashboardView.vue'
import HistoryView from '../views/HistoryView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
    },
    {
      path: '/products',
      name: 'products',
      component: CashierView,
    },
    {
      path: '/history',
      name: 'history',
      component: HistoryView,
    },
  ],
})

router.beforeEach((to, from) => {
  let token = localStorage.getItem('auth_token')

  // Bersihkan token invalid yang mungkin tersimpan dari versi login sebelumnya.
  if (token === 'undefined' || token === 'null') {
    localStorage.removeItem('auth_token')
    token = null
  }

  // Jika belum login dan ingin ke halaman terproteksi -> lempar ke login
  if (to.name !== 'login' && !token) {
    return { name: 'login' }
  }

  // Jika sudah login dan ingin ke halaman login -> lempar ke products
  if (to.name === 'login' && token) {
    return { name: 'products' }
  }
})

export default router
