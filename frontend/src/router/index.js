import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import ProductsView from '../views/ProductsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsView,
    },
  ],
})

// Navigation Guard menggunakan Return Value (Standard Vue Router 4)
router.beforeEach((to) => {
  const token = localStorage.getItem('auth_token')

  // Jika mencoba akses selain login tanpa token -> redirect ke login
  if (to.name !== 'login' && !token) {
    return { name: 'login' }
  }

  // Jika sudah punya token tapi mencoba akses login -> redirect ke products
  if (to.name === 'login' && token) {
    return { name: 'products' }
  }
})

export default router