<template>
  <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>Login POS Sistem</h2>
    <form @submit.prevent="handleLogin">
      <div style="margin-bottom: 15px;">
        <label>Email:</label>
        <input v-model="email" type="email" required style="width: 100%; padding: 8px; margin-top: 5px;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label>Password:</label>
        <input v-model="password" type="password" required style="width: 100%; padding: 8px; margin-top: 5px;" />
      </div>
      <button type="submit" style="width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
        Login
      </button>
    </form>
    <p v-if="errorMessage" style="color: red; margin-top: 15px;">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const router = useRouter()

const handleLogin = async () => {
  errorMessage.value = ''
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })
    
    // Simpan token ke localStorage (Soal 2a)
    localStorage.setItem('auth_token', response.data.token)
    router.push('/products')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login gagal, periksa email dan password.'
  }
}
</script>