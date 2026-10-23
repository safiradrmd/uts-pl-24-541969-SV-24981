<template>
  <div style="max-width: 800px; margin: 30px auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <h2>Manajemen Produk POS</h2>
      <button @click="handleLogout" style="padding: 8px 15px; background-color: #f44336; color: white; border: none; cursor: pointer;">
        Logout
      </button>
    </div>

    <!-- Form Tambah / Edit Produk -->
    <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
      <h3>{{ isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
      <form @submit.prevent="saveProduct">
        <input v-model="form.name" placeholder="Nama Produk" required style="margin-right: 10px; padding: 5px;" />
        <input v-model="form.price" type="number" placeholder="Harga" required style="margin-right: 10px; padding: 5px;" />
        <input v-model="form.stock" type="number" placeholder="Stok" required style="margin-right: 10px; padding: 5px;" />
        <button type="submit" style="padding: 5px 15px; background-color: #2196F3; color: white; border: none;">
          {{ isEditing ? 'Update' : 'Simpan' }}
        </button>
        <button v-if="isEditing" @click="resetForm" type="button" style="margin-left: 5px; padding: 5px 10px;">
          Batal
        </button>
      </form>
    </div>

    <!-- Tabel Daftar Produk (Soal 2b) -->
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="background-color: #eee;">
          <th>ID</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>Rp {{ product.price }}</td>
          <td>{{ product.stock }}</td>
          <td>
            <button @click="editProduct(product)" style="margin-right: 5px;">Edit</button>
            <button @click="deleteProduct(product.id)" style="background-color: #ff4d4d; color: white;">Hapus</button>
          </td>
        </tr>
        <tr v-if="products.length === 0">
          <td colspan="5" style="text-align: center;">Belum ada data produk.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const products = ref([])
const router = useRouter()
const isEditing = ref(false)
const editingId = ref(null)

const form = ref({
  name: '',
  price: '',
  stock: ''
})

const fetchProducts = async () => {
  try {
    const response = await api.get('/products')
    products.value = response.data.data || response.data
  } catch (error) {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/')
    }
  }
}

const saveProduct = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/products/${editingId.value}`, form.value)
    } else {
      await api.post('/products', form.value)
    }
    resetForm()
    fetchProducts()
  } catch (error) {
    alert('Gagal menyimpan produk.')
  }
}

const editProduct = (product) => {
  isEditing.value = true
  editingId.value = product.id
  form.value = { name: product.name, price: product.price, stock: product.stock }
}

const deleteProduct = async (id) => {
  if (confirm('Yakin ingin menghapus produk ini?')) {
    try {
      await api.delete(`/products/${id}`)
      fetchProducts()
    } catch (error) {
      alert('Gagal menghapus produk.')
    }
  }
}

const resetForm = () => {
  form.value = { name: '', price: '', stock: '' }
  isEditing.value = false
  editingId.value = null
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch (e) {
    // Abaikan jika token sudah expired
  } finally {
    localStorage.removeItem('auth_token')
    router.push('/')
  }
}

onMounted(() => {
  fetchProducts()
})
</script>