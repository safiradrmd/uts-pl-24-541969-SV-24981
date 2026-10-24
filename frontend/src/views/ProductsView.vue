<template>
  <main class="pos-page">
    <header class="pos-topbar">
      <div class="topbar-inner">
        <RouterLink class="brand" to="/products" aria-label="StokKasir">
          <span class="brand-mark">S</span><span class="brand-name">STOK<span>KASIR</span></span>
        </RouterLink>
        <nav class="main-nav" aria-label="Navigasi utama">
          <span class="nav-item active"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v7H4zM14 4h6v4h-6zM14 12h6v8h-6zM4 15h6v5H4z"/></svg>Kasir</span>
        </nav>
        <div class="topbar-right">
          <span class="workspace-label"><i></i>Toko saya</span>
          <button class="manage-button" type="button" @click="openProductManager"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg><span>Kelola produk</span></button>
          <span class="topbar-divider"></span>
          <button class="logout-button" type="button" @click="handleLogout" title="Keluar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg><span>Keluar</span></button>
        </div>
      </div>
    </header>

    <div class="pos-content">
      <section class="page-heading">
        <div><p class="heading-kicker">KASIR / TRANSAKSI BARU</p><h1>Mulai transaksi</h1><p class="heading-description">Pilih produk, masukkan ke keranjang, lalu selesaikan pembayaran.</p></div>
        <div class="shift-status"><span class="status-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></svg></span><span><small>STATUS KASIR</small><b><i></i> Siap melayani</b></span></div>
      </section>

      <section class="pos-layout">
        <div class="catalog-panel">
          <div class="catalog-heading"><div><h2>Pilih produk</h2><p>{{ filteredProducts.length }} produk tersedia</p></div><span class="catalog-count">{{ products.length }} item</span></div>
          <label class="search-box"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg><input v-model="searchQuery" type="search" placeholder="Cari nama produk..." aria-label="Cari nama produk" /></label>
          <div class="category-row"><button class="category-chip active" type="button">Semua produk <span>{{ products.length }}</span></button><span class="catalog-hint">Pilih produk untuk menambahkan</span></div>

          <div v-if="filteredProducts.length" class="product-grid">
            <button v-for="(product, index) in filteredProducts" :key="product.id" class="product-card" type="button" :disabled="Number(product.stock) < 1" @click="addToCart(product)">
              <span class="product-card-top"><span class="product-art" :class="`art-${index % 6}`"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21"/></svg></span><span class="add-product-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span></span>
              <span class="product-card-name">{{ product.name }}</span><span class="product-card-price">{{ formatCurrency(product.price) }}</span>
              <span class="product-card-stock" :class="{ 'stock-low': Number(product.stock) < 5 }"><i></i>{{ product.stock > 0 ? `${product.stock} tersedia` : 'Stok habis' }}</span>
            </button>
          </div>
          <div v-else class="catalog-empty"><span class="empty-search-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg></span><b>{{ searchQuery ? 'Produk tidak ditemukan' : 'Belum ada produk' }}</b><p>{{ searchQuery ? 'Coba kata kunci lain.' : 'Tambahkan produk dari menu Kelola produk.' }}</p><button v-if="!searchQuery" type="button" class="empty-add-button" @click="openProductManager">Tambah produk</button></div>
          <div class="catalog-footer"><span><i></i> Inventaris terhubung</span><span>Harga dalam Rupiah</span></div>
        </div>

        <aside class="cart-panel">
          <div class="cart-heading"><div class="cart-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 1.9-1.4L22 9H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg></div><div><h2>Keranjang</h2><p>{{ cartCount }} item dipilih</p></div><button v-if="cart.length" class="clear-cart" type="button" @click="clearCart">Kosongkan</button></div>

          <div v-if="cart.length" class="cart-items">
            <article v-for="item in cart" :key="item.id" class="cart-item">
              <div class="cart-item-main"><span class="cart-item-avatar">{{ item.name?.charAt(0)?.toUpperCase() || 'P' }}</span><div class="cart-item-info"><b>{{ item.name }}</b><small>{{ formatCurrency(item.price) }}</small></div><button class="remove-item" type="button" :aria-label="`Hapus ${item.name} dari keranjang`" @click="removeFromCart(item.id)"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>
              <div class="cart-item-bottom"><span class="quantity-control"><button type="button" :aria-label="`Kurangi jumlah ${item.name}`" @click="changeQuantity(item.id, -1)">−</button><b>{{ item.quantity }}</b><button type="button" :disabled="item.quantity >= item.stock" :aria-label="`Tambah jumlah ${item.name}`" @click="changeQuantity(item.id, 1)">+</button></span><b class="line-total">{{ formatCurrency(item.price * item.quantity) }}</b></div>
            </article>
          </div>
          <div v-else class="cart-empty"><div class="cart-empty-art"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 1.9-1.4L22 9H6"/><circle cx="10" cy="21" r="1"/><circle cx="18" cy="21" r="1"/></svg></div><b>Keranjang masih kosong</b><p>Pilih produk di sebelah kiri untuk memulai transaksi.</p></div>

          <div class="cart-summary"><div><span>Subtotal</span><b>{{ formatCurrency(subtotal) }}</b></div><div><span>Diskon</span><b>{{ formatCurrency(0) }}</b></div><div class="summary-total"><span>Total pembayaran</span><b>{{ formatCurrency(subtotal) }}</b></div></div>
          <button class="checkout-button" type="button" :disabled="!cart.length" @click="openPayment"><span>Lanjut pembayaran</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
          <p class="payment-note"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg> Pembayaran tunai</p>
        </aside>
      </section>
      <footer class="pos-footer"><span>STOK<span>KASIR</span></span><span>Kelola transaksi toko dengan praktis.</span><span>{{ todayLabel }}</span></footer>
    </div>

    <Teleport to="body">
      <div v-if="showProductManager" class="modal-backdrop" @click.self="closeProductManager" @keydown.esc="closeProductManager">
        <section class="manager-modal" role="dialog" aria-modal="true" aria-labelledby="manager-title">
          <header class="modal-header"><div class="modal-title-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21"/></svg></div><div><p class="modal-kicker">KATALOG TOKO</p><h2 id="manager-title">{{ productFormMode ? (isEditing ? 'Edit produk' : 'Tambah produk') : 'Kelola produk' }}</h2></div><button class="modal-close" type="button" aria-label="Tutup" @click="closeProductManager"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button></header>

          <form v-if="productFormMode" class="modal-product-form" @submit.prevent="saveProduct">
            <p class="modal-description">{{ isEditing ? 'Perbarui informasi produk di katalog tokomu.' : 'Masukkan detail produk yang akan dijual.' }}</p>
            <label for="modal-product-name">Nama produk</label><input id="modal-product-name" v-model="form.name" placeholder="Contoh: Kopi Arabika" required />
            <div class="form-fields-row"><div><label for="modal-product-price">Harga jual</label><div class="modal-money-input"><span>Rp</span><input id="modal-product-price" v-model="form.price" type="number" min="0" placeholder="0" required /></div></div><div><label for="modal-product-stock">Jumlah stok</label><div class="modal-stock-input"><input id="modal-product-stock" v-model="form.stock" type="number" min="0" placeholder="0" required /><span>unit</span></div></div></div>
            <p v-if="managerError" class="form-error" role="alert">{{ managerError }}</p>
            <div class="modal-form-actions"><button class="secondary-button" type="button" @click="cancelProductForm">Kembali</button><button class="primary-button" type="submit" :disabled="isSavingProduct">{{ isSavingProduct ? 'Menyimpan...' : (isEditing ? 'Simpan perubahan' : 'Tambah produk') }}</button></div>
          </form>

          <template v-else>
            <div class="manager-toolbar"><p>Kelola harga dan stok produk tokomu.</p><button class="primary-button add-product-button" type="button" @click="startCreateProduct"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Produk baru</button></div>
            <div class="manager-product-list">
              <div v-if="!products.length" class="manager-empty">Belum ada produk. Tambahkan produk pertama untuk mulai berjualan.</div>
              <article v-for="product in products" :key="product.id" class="manager-product-row"><span class="manager-product-avatar">{{ product.name?.charAt(0)?.toUpperCase() || 'P' }}</span><span class="manager-product-info"><b>{{ product.name }}</b><small>{{ formatCurrency(product.price) }} <i>•</i> Stok {{ product.stock }}</small></span><button class="manager-edit" type="button" @click="startEditProduct(product)"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z"/></svg><span>Edit</span></button><button class="manager-delete" type="button" :aria-label="`Hapus ${product.name}`" @click="deleteProduct(product)"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M5.5 7l1 14h11l1-14M9 7V4h6v3"/></svg></button></article>
            </div>
            <footer class="manager-footer"><span>{{ products.length }} produk terdaftar</span><button class="secondary-button" type="button" @click="closeProductManager">Selesai</button></footer>
          </template>
        </section>
      </div>

      <div v-if="showPayment" class="modal-backdrop payment-backdrop" @click.self="closePayment">
        <section class="payment-modal" role="dialog" aria-modal="true" aria-labelledby="payment-title">
          <header class="payment-header"><button class="modal-close" type="button" aria-label="Kembali" @click="closePayment"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button><div class="payment-heading-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg></div><div><p class="modal-kicker">PEMBAYARAN TUNAI</p><h2 id="payment-title">Selesaikan transaksi</h2></div></header>
          <div class="payment-total"><span>Total yang harus dibayar</span><b>{{ formatCurrency(subtotal) }}</b></div>
          <label class="cash-label" for="cash-received">Uang diterima</label><div class="cash-input-wrap"><span>Rp</span><input id="cash-received" v-model.number="cashReceived" type="number" min="0" :placeholder="String(subtotal)" /></div>
          <div class="quick-cash"><button v-for="amount in quickCashAmounts" :key="amount" type="button" @click="cashReceived = amount">{{ formatCompactCurrency(amount) }}</button><button type="button" @click="cashReceived = subtotal">Uang pas</button></div>
          <div class="change-row"><span>Kembalian</span><b :class="{ 'change-short': changeDue < 0 }">{{ formatCurrency(Math.max(changeDue, 0)) }}</b></div>
          <p v-if="paymentError" class="form-error" role="alert">{{ paymentError }}</p>
          <button class="confirm-payment" type="button" :disabled="isSubmittingSale || changeDue < 0 || !cart.length" @click="submitSale"><span>{{ isSubmittingSale ? 'Menyimpan transaksi...' : 'Konfirmasi pembayaran' }}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></button>
          <p class="payment-secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.9 8.1 7 10 4.1-1.9 7-5.4 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg> Stok akan diperbarui setelah pembayaran berhasil.</p>
        </section>
      </div>

      <div v-if="completedSale" class="modal-backdrop" @click.self="completedSale = null">
        <section class="receipt-modal" role="dialog" aria-modal="true" aria-labelledby="receipt-title">
          <div class="receipt-success"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg></div><p class="modal-kicker">TRANSAKSI BERHASIL</p><h2 id="receipt-title">Terima kasih!</h2><p class="receipt-reference">{{ completedSale.reference }}</p>
          <div class="receipt-items"><div v-for="item in completedSale.items" :key="item.id"><span>{{ item.product_name }} <small>× {{ item.quantity }}</small></span><b>{{ formatCurrency(item.subtotal) }}</b></div></div>
          <div class="receipt-line"><span>Total</span><b>{{ formatCurrency(completedSale.subtotal) }}</b></div><div class="receipt-line"><span>Tunai diterima</span><b>{{ formatCurrency(completedSale.cash_received) }}</b></div><div class="receipt-change"><span>Kembalian</span><b>{{ formatCurrency(completedSale.change_due) }}</b></div>
          <button class="primary-button receipt-done" type="button" @click="completedSale = null">Selesai</button>
        </section>
      </div>
    </Teleport>
  </main>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const products = ref([])
const cart = ref([])
const searchQuery = ref('')
const router = useRouter()
const showProductManager = ref(false)
const productFormMode = ref(false)
const showPayment = ref(false)
const completedSale = ref(null)
const isEditing = ref(false)
const isSavingProduct = ref(false)
const isSubmittingSale = ref(false)
const managerError = ref('')
const paymentError = ref('')
const editingId = ref(null)
const cashReceived = ref(0)
const form = ref({ name: '', price: '', stock: '' })

const filteredProducts = computed(() => {
  const query = searchQuery.value.trim().toLocaleLowerCase('id-ID')
  return products.value.filter((product) => !query || product.name.toLocaleLowerCase('id-ID').includes(query))
})
const cartCount = computed(() => cart.value.reduce((count, item) => count + item.quantity, 0))
const subtotal = computed(() => cart.value.reduce((total, item) => total + Number(item.price) * item.quantity, 0))
const changeDue = computed(() => Number(cashReceived.value || 0) - subtotal.value)
const quickCashAmounts = computed(() => [...new Set([roundCash(subtotal.value), roundCash(subtotal.value, 50000), roundCash(subtotal.value, 100000)])].filter((amount) => amount > subtotal.value).slice(0, 3))
const todayLabel = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date())

function roundCash(amount, step = 10000) {
  return Math.ceil(amount / step) * step
}

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
  style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
}).format(Number(value) || 0)

const formatCompactCurrency = (value) => {
  if (value >= 1000000) return `Rp ${(value / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 })} jt`
  return `Rp ${(value / 1000).toLocaleString('id-ID', { maximumFractionDigits: 0 })} rb`
}

const fetchProducts = async () => {
  try {
    const response = await api.get('/products')
    products.value = response.data.data || response.data
    cart.value = cart.value.flatMap((item) => {
      const latest = products.value.find((product) => product.id === item.id)
      if (!latest || Number(latest.stock) < 1) return []
      return [{ ...latest, quantity: Math.min(item.quantity, Number(latest.stock)) }]
    })
  } catch (error) {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/')
    }
  }
}

const addToCart = (product) => {
  if (Number(product.stock) < 1) return
  const existing = cart.value.find((item) => item.id === product.id)
  if (existing) {
    if (existing.quantity < Number(product.stock)) existing.quantity += 1
    return
  }
  cart.value.push({ ...product, quantity: 1 })
}

const changeQuantity = (productId, amount) => {
  const item = cart.value.find((entry) => entry.id === productId)
  if (!item) return
  item.quantity += amount
  if (item.quantity < 1) removeFromCart(productId)
}

const removeFromCart = (productId) => {
  cart.value = cart.value.filter((item) => item.id !== productId)
}

const clearCart = () => { cart.value = [] }

const openProductManager = () => {
  managerError.value = ''
  productFormMode.value = false
  showProductManager.value = true
}

const closeProductManager = () => {
  showProductManager.value = false
  resetProductForm()
}

const startCreateProduct = () => {
  resetProductForm()
  productFormMode.value = true
}

const startEditProduct = (product) => {
  isEditing.value = true
  editingId.value = product.id
  form.value = { name: product.name, price: product.price, stock: product.stock }
  managerError.value = ''
  productFormMode.value = true
}

const cancelProductForm = () => {
  resetProductForm()
  productFormMode.value = false
}

const resetProductForm = () => {
  form.value = { name: '', price: '', stock: '' }
  isEditing.value = false
  editingId.value = null
  managerError.value = ''
}

const saveProduct = async () => {
  if (isSavingProduct.value) return
  isSavingProduct.value = true
  managerError.value = ''
  try {
    if (isEditing.value) {
      await api.put(`/products/${editingId.value}`, form.value)
    } else {
      await api.post('/products', form.value)
    }
    await fetchProducts()
    resetProductForm()
    productFormMode.value = false
  } catch (error) {
    managerError.value = error.response?.data?.message || 'Produk gagal disimpan. Coba lagi.'
  } finally {
    isSavingProduct.value = false
  }
}

const deleteProduct = async (product) => {
  if (!window.confirm(`Hapus produk “${product.name}” dari katalog?`)) return
  try {
    await api.delete(`/products/${product.id}`)
    await fetchProducts()
  } catch (error) {
    managerError.value = error.response?.data?.message || 'Produk gagal dihapus. Coba lagi.'
  }
}

const openPayment = () => {
  paymentError.value = ''
  cashReceived.value = subtotal.value
  showPayment.value = true
}

const closePayment = () => {
  if (isSubmittingSale.value) return
  showPayment.value = false
  paymentError.value = ''
}

const submitSale = async () => {
  if (isSubmittingSale.value || changeDue.value < 0) return
  isSubmittingSale.value = true
  paymentError.value = ''
  try {
    const response = await api.post('/sales', {
      items: cart.value.map((item) => ({ product_id: item.id, quantity: item.quantity })),
      cash_received: Number(cashReceived.value),
    })
    completedSale.value = response.data.data
    cart.value = []
    showPayment.value = false
    await fetchProducts()
  } catch (error) {
    paymentError.value = error.response?.data?.errors?.items?.[0]
      || error.response?.data?.errors?.cash_received?.[0]
      || error.response?.data?.message
      || 'Transaksi gagal disimpan. Periksa koneksi dan stok produk.'
    if (error.response?.status === 422) await fetchProducts()
  } finally {
    isSubmittingSale.value = false
  }
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
  } catch (error) {
    // Abaikan jika token sudah kedaluwarsa.
  } finally {
    localStorage.removeItem('auth_token')
    router.push('/')
  }
}

const handleEscape = (event) => {
  if (event.key !== 'Escape') return
  if (showProductManager.value) closeProductManager()
  if (showPayment.value) closePayment()
  if (completedSale.value) completedSale.value = null
}

onMounted(() => {
  fetchProducts()
  window.addEventListener('keydown', handleEscape)
})
onUnmounted(() => window.removeEventListener('keydown', handleEscape))
</script>

<style scoped>
.pos-page { min-height: 100vh; background: #f5f7f4; color: #24342a; }.pos-topbar { position: sticky; z-index: 5; top: 0; height: 66px; border-bottom: 1px solid #e7ece7; background: #fff; }.topbar-inner { display: flex; width: min(1360px, calc(100% - 64px)); height: 100%; align-items: center; margin: 0 auto; }.brand { display: flex; flex: none; align-items: center; gap: 9px; color: #20382b; text-decoration: none; }.brand-mark { display: grid; width: 32px; height: 32px; place-items: center; border-radius: 10px; color: #17422f; background: #dcefe1; font-family: Georgia, serif; font-size: 20px; font-style: italic; font-weight: 700; }.brand-name { font-size: 12px; font-weight: 800; letter-spacing: 1.25px; }.brand-name span { color: #718479; font-weight: 500; }.main-nav { display: flex; height: 100%; align-items: center; margin-left: 58px; }.nav-item { position: relative; display: flex; height: 100%; align-items: center; gap: 8px; padding: 0 15px; color: #718078; font-size: 11px; }.nav-item svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }.nav-item.active { color: #176b4d; font-weight: 650; }.nav-item.active::after { position: absolute; right: 13px; bottom: -1px; left: 13px; height: 2px; border-radius: 2px 2px 0 0; background: #2f865c; content: ''; }.topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }.workspace-label { display: flex; align-items: center; gap: 7px; color: #69776e; font-size: 10px; }.workspace-label i { width: 6px; height: 6px; border-radius: 50%; background: #61a879; box-shadow: 0 0 0 3px #61a87920; }.manage-button { display: flex; height: 34px; align-items: center; gap: 6px; padding: 0 11px; border: 1px solid #dce9de; border-radius: 7px; color: #39734d; background: #f5faf5; font-size: 10px; font-weight: 600; transition: .18s; }.manage-button:hover { border-color: #b8d5bd; background: #edf6ee; }.manage-button svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }.topbar-divider { width: 1px; height: 21px; background: #e6ebe6; }.logout-button { display: flex; align-items: center; gap: 6px; padding: 7px; border: 0; border-radius: 6px; color: #8a958d; background: transparent; font-size: 10px; }.logout-button:hover { color: #a64a42; background: #fff5f4; }.logout-button svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }
.pos-content { width: min(1360px, calc(100% - 64px)); margin: 0 auto; }.page-heading { display: flex; align-items: center; justify-content: space-between; padding: 27px 0 21px; }.heading-kicker { margin: 0 0 4px; color: #6c9478; font-size: 8px; font-weight: 750; letter-spacing: 1.25px; }.page-heading h1 { margin: 0; color: #1d2d22; font-family: Georgia, 'Times New Roman', serif; font-size: 30px; font-weight: 400; letter-spacing: -.7px; }.heading-description { margin: 4px 0 0; color: #89948b; font-size: 11px; }.shift-status { display: flex; align-items: center; gap: 10px; padding: 8px 13px; border: 1px solid #e7ece7; border-radius: 8px; background: #fff; }.status-icon { display: grid; width: 31px; height: 31px; place-items: center; border-radius: 8px; color: #609170; background: #eef6ef; }.status-icon svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }.shift-status small, .shift-status b { display: block; }.shift-status small { color: #a0aaa2; font-size: 7px; font-weight: 700; letter-spacing: .8px; }.shift-status b { margin-top: 2px; color: #53655a; font-size: 9px; font-weight: 600; }.shift-status b i { display: inline-block; width: 5px; height: 5px; margin-right: 4px; border-radius: 50%; background: #67aa78; vertical-align: 1px; }
.pos-layout { display: grid; grid-template-columns: minmax(0, 1.72fr) minmax(325px, .86fr); gap: 17px; align-items: stretch; }.catalog-panel, .cart-panel { min-width: 0; min-height: 575px; border: 1px solid #e7ece7; border-radius: 10px; background: #fff; }.catalog-panel { display: flex; flex-direction: column; padding: 20px 21px 0; }.catalog-heading { display: flex; align-items: center; justify-content: space-between; }.catalog-heading h2, .cart-heading h2 { margin: 0; color: #293a2f; font-size: 14px; font-weight: 650; }.catalog-heading p, .cart-heading p { margin: 3px 0 0; color: #9aa49c; font-size: 9px; }.catalog-count { padding: 5px 8px; border-radius: 5px; color: #708477; background: #f3f6f3; font-size: 8px; }.search-box { display: flex; height: 39px; align-items: center; gap: 9px; margin-top: 16px; padding: 0 11px; border: 1px solid #e8ece8; border-radius: 7px; background: #fbfcfb; transition: .2s; }.search-box:focus-within { border-color: #7eae8a; box-shadow: 0 0 0 3px #7eae8a16; }.search-box svg { width: 16px; height: 16px; flex: none; fill: none; stroke: #98a49b; stroke-linecap: round; stroke-width: 1.6; }.search-box input { width: 100%; min-width: 0; border: 0; outline: none; color: #35443a; background: transparent; font-size: 10px; }.search-box input::placeholder { color: #adb6af; }.search-box kbd { flex: none; padding: 2px 5px; border: 1px solid #e7ebe7; border-radius: 4px; color: #a5aea7; background: #fff; font-family: inherit; font-size: 8px; }.category-row { display: flex; min-height: 53px; align-items: center; justify-content: space-between; }.category-chip { padding: 7px 9px; border: 1px solid #deebe0; border-radius: 6px; color: #3d7450; background: #f2f8f3; font-size: 9px; font-weight: 600; }.category-chip span { margin-left: 4px; color: #71947a; font-weight: 500; }.catalog-hint { color: #a6afa8; font-size: 8px; }.product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(145px, 1fr)); gap: 11px; padding: 0 0 18px; }.product-card { display: flex; min-width: 0; min-height: 154px; flex-direction: column; align-items: stretch; padding: 12px; border: 1px solid #e9ede9; border-radius: 8px; color: inherit; background: #fff; text-align: left; transition: border .18s, box-shadow .18s, transform .18s; }.product-card:hover:not(:disabled) { border-color: #aed0b4; box-shadow: 0 7px 17px #244c3210; transform: translateY(-2px); }.product-card:disabled { cursor: not-allowed; opacity: .58; }.product-card-top { display: flex; align-items: center; justify-content: space-between; }.product-art { display: grid; width: 43px; height: 43px; place-items: center; border-radius: 12px; color: #528360; background: #edf6ee; }.product-art svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.art-1 { color: #a7774b; background: #faf2e9; }.art-2 { color: #687ea2; background: #eef1f8; }.art-3 { color: #a16f84; background: #f8eff3; }.art-4 { color: #58898a; background: #edf6f5; }.art-5 { color: #89834e; background: #f6f4e9; }.add-product-icon { display: grid; width: 25px; height: 25px; place-items: center; border: 1px solid #e7ede7; border-radius: 7px; color: #6f9878; background: #fff; }.add-product-icon svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }.product-card-name { overflow: hidden; margin-top: 10px; color: #35443a; font-size: 10px; font-weight: 620; text-overflow: ellipsis; white-space: nowrap; }.product-card-price { margin-top: 2px; color: #286745; font-size: 11px; font-weight: 700; }.product-card-stock { display: flex; align-items: center; gap: 5px; margin-top: auto; padding-top: 9px; color: #929e94; font-size: 8px; }.product-card-stock i { width: 5px; height: 5px; border-radius: 50%; background: #6eb07d; }.product-card-stock.stock-low { color: #b28b50; }.product-card-stock.stock-low i { background: #d0a151; }.catalog-empty { display: flex; flex: 1; min-height: 280px; flex-direction: column; align-items: center; justify-content: center; text-align: center; }.empty-search-icon { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 14px; color: #779980; background: #eff6f0; }.empty-search-icon svg { width: 21px; height: 21px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.5; }.catalog-empty b { margin-top: 12px; color: #4b5a4f; font-size: 11px; }.catalog-empty p { margin: 4px 0 0; color: #9ba49d; font-size: 9px; }.empty-add-button { margin-top: 12px; padding: 7px 10px; border: 1px solid #dce9de; border-radius: 6px; color: #39734d; background: #f5faf5; font-size: 9px; }.catalog-footer { display: flex; min-height: 38px; align-items: center; justify-content: space-between; margin-top: auto; border-top: 1px solid #f0f2f0; color: #a1aaa3; font-size: 8px; }.catalog-footer span:first-child { display: flex; align-items: center; gap: 6px; }.catalog-footer i { width: 5px; height: 5px; border-radius: 50%; background: #70ad7e; }
.cart-panel { position: sticky; top: 83px; display: flex; min-height: 575px; max-height: calc(100vh - 100px); flex-direction: column; overflow: hidden; }.cart-heading { display: flex; min-height: 73px; align-items: center; gap: 10px; padding: 13px 17px; border-bottom: 1px solid #edf0ed; }.cart-icon { display: grid; width: 34px; height: 34px; flex: none; place-items: center; border-radius: 9px; color: #43805a; background: #edf6ef; }.cart-icon svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }.cart-heading h2 { font-size: 13px; }.cart-heading p { font-size: 8px; }.clear-cart { margin-left: auto; padding: 6px 8px; border: 0; border-radius: 5px; color: #a67870; background: transparent; font-size: 8px; }.clear-cart:hover { color: #a2483f; background: #fff3f1; }.cart-items { flex: 1; overflow: auto; }.cart-item { padding: 13px 16px 11px; border-bottom: 1px solid #f0f2f0; }.cart-item-main { display: flex; align-items: center; gap: 9px; }.cart-item-avatar, .manager-product-avatar { display: grid; width: 32px; height: 32px; flex: none; place-items: center; border: 1px solid #e5eee5; border-radius: 9px; color: #548164; background: #f1f7f2; font-family: Georgia, serif; font-size: 14px; font-weight: 700; }.cart-item-info { min-width: 0; flex: 1; }.cart-item-info b, .cart-item-info small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.cart-item-info b { color: #425146; font-size: 9px; font-weight: 600; }.cart-item-info small { margin-top: 2px; color: #9aa49c; font-size: 8px; }.remove-item { display: grid; width: 24px; height: 24px; place-items: center; border: 0; border-radius: 5px; color: #b1aaa6; background: transparent; }.remove-item:hover { color: #aa4c43; background: #fff2f0; }.remove-item svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.7; }.cart-item-bottom { display: flex; align-items: center; justify-content: space-between; margin: 9px 0 0 41px; }.quantity-control { display: flex; height: 25px; align-items: center; border: 1px solid #e8ece8; border-radius: 5px; }.quantity-control button { width: 25px; height: 23px; border: 0; color: #688170; background: transparent; font-size: 14px; }.quantity-control button:hover:not(:disabled) { color: #176b4d; background: #f0f7f1; }.quantity-control button:disabled { color: #c4ccc5; cursor: not-allowed; }.quantity-control b { min-width: 20px; color: #516057; font-size: 9px; text-align: center; }.line-total { color: #405348; font-size: 9px; font-weight: 650; }.cart-empty { display: flex; flex: 1; min-height: 210px; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }.cart-empty-art { display: grid; width: 48px; height: 48px; place-items: center; border-radius: 15px; color: #82a18a; background: #f1f6f1; }.cart-empty-art svg { width: 24px; height: 24px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.cart-empty b { margin-top: 10px; color: #546258; font-size: 10px; }.cart-empty p { max-width: 190px; margin: 4px 0 0; color: #a0a9a2; font-size: 8px; line-height: 1.6; }.cart-summary { padding: 13px 17px 7px; border-top: 1px solid #edf0ed; }.cart-summary > div { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; color: #98a199; font-size: 9px; }.cart-summary > div b { color: #637067; font-size: 9px; font-weight: 550; }.cart-summary .summary-total { margin-top: 11px; padding-top: 12px; border-top: 1px dashed #e6ebe6; color: #38483d; font-size: 10px; font-weight: 650; }.cart-summary .summary-total b { color: #1d5739; font-size: 16px; font-weight: 700; }.checkout-button { display: flex; height: 42px; flex: none; align-items: center; justify-content: center; gap: 9px; margin: 5px 16px 0; border: 0; border-radius: 7px; color: #fff; background: #176b4d; box-shadow: 0 5px 12px #176b4d20; font-size: 10px; font-weight: 650; transition: .2s; }.checkout-button:hover:not(:disabled) { background: #10543b; transform: translateY(-1px); }.checkout-button:disabled { cursor: not-allowed; background: #b6c5b9; box-shadow: none; }.checkout-button svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }.payment-note { display: flex; align-items: center; justify-content: center; gap: 5px; margin: 9px 0 13px; color: #a1aaa3; font-size: 8px; }.payment-note svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.pos-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 17px; padding: 13px 0 17px; border-top: 1px solid #e7ebe7; color: #a1aaa3; font-size: 8px; }.pos-footer span:first-child { color: #688272; font-weight: 750; letter-spacing: .8px; }.pos-footer span:first-child span { color: #96a69a; font-weight: 500; }
.modal-backdrop { position: fixed; z-index: 20; inset: 0; display: grid; overflow-y: auto; place-items: center; padding: 22px; background: #13251dc2; backdrop-filter: blur(4px); }.manager-modal, .payment-modal, .receipt-modal { width: min(100%, 540px); overflow: hidden; border: 1px solid #e7ece7; border-radius: 13px; background: #fff; box-shadow: 0 24px 80px #10261a40; animation: modal-in .2s ease-out; }.modal-header { display: flex; align-items: center; gap: 12px; padding: 20px 22px; border-bottom: 1px solid #edf0ed; }.modal-title-icon, .payment-heading-icon { display: grid; width: 38px; height: 38px; flex: none; place-items: center; border-radius: 10px; color: #548261; background: #edf6ef; }.modal-title-icon svg, .payment-heading-icon svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.modal-kicker { margin: 0 0 3px; color: #769580; font-size: 8px; font-weight: 750; letter-spacing: 1px; }.modal-header h2, .payment-header h2 { margin: 0; color: #293a2f; font-family: Georgia, serif; font-size: 21px; font-weight: 400; }.modal-close { display: grid; width: 30px; height: 30px; flex: none; place-items: center; margin-left: auto; border: 0; border-radius: 7px; color: #8c9990; background: transparent; }.modal-close:hover { color: #3c5744; background: #f2f6f2; }.modal-close svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.7; }.manager-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 21px 10px; }.manager-toolbar p { margin: 0; color: #8b978e; font-size: 9px; }.primary-button, .secondary-button { display: inline-flex; min-height: 34px; align-items: center; justify-content: center; gap: 6px; padding: 0 12px; border: 0; border-radius: 6px; font-size: 9px; font-weight: 600; transition: .18s; }.primary-button { color: #fff; background: #176b4d; }.primary-button:hover:not(:disabled) { background: #10543b; }.primary-button:disabled { cursor: wait; opacity: .7; }.primary-button svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }.secondary-button { border: 1px solid #e2e8e2; color: #66746a; background: #fff; }.secondary-button:hover { background: #f6f8f6; }.manager-product-list { max-height: min(390px, 52vh); overflow-y: auto; padding: 0 21px; }.manager-empty { padding: 34px 18px; color: #98a29a; font-size: 10px; line-height: 1.6; text-align: center; }.manager-product-row { display: flex; min-height: 62px; align-items: center; gap: 10px; border-top: 1px solid #f0f2f0; }.manager-product-avatar { width: 33px; height: 33px; font-size: 13px; }.manager-product-info { min-width: 0; flex: 1; }.manager-product-info b, .manager-product-info small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.manager-product-info b { color: #46564a; font-size: 10px; font-weight: 620; }.manager-product-info small { margin-top: 3px; color: #929d94; font-size: 8px; }.manager-product-info small i { margin: 0 4px; color: #c1c9c2; font-style: normal; }.manager-edit, .manager-delete { display: flex; height: 28px; align-items: center; justify-content: center; gap: 4px; padding: 0 7px; border: 1px solid transparent; border-radius: 5px; color: #78917c; background: transparent; font-size: 8px; }.manager-edit:hover { border-color: #dceade; color: #34724d; background: #f1f7f2; }.manager-edit svg, .manager-delete svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }.manager-delete { width: 28px; color: #b28c86; }.manager-delete:hover { border-color: #f1dedb; color: #ad5048; background: #fff5f4; }.manager-footer { display: flex; min-height: 58px; align-items: center; justify-content: space-between; padding: 0 21px; border-top: 1px solid #edf0ed; color: #9ba49d; font-size: 8px; }.modal-product-form { padding: 19px 23px 22px; }.modal-description { margin: 0 0 17px; color: #8d9990; font-size: 10px; }.modal-product-form label { display: block; margin: 0 0 6px; color: #506054; font-size: 9px; font-weight: 600; }.modal-product-form > input, .modal-product-form .form-fields-row input { width: 100%; height: 39px; padding: 0 11px; border: 1px solid #e4e9e4; border-radius: 6px; outline: none; color: #34443a; background: #fff; font-size: 10px; }.modal-product-form input:focus { border-color: #72a583; box-shadow: 0 0 0 3px #72a58318; }.modal-product-form input::placeholder { color: #adb6af; }.form-fields-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 14px; }.modal-money-input, .modal-stock-input { position: relative; }.modal-money-input span, .modal-stock-input span { position: absolute; top: 0; display: flex; height: 39px; align-items: center; color: #95a097; font-size: 9px; }.modal-money-input span { left: 10px; }.modal-money-input input { padding-left: 32px !important; }.modal-stock-input span { right: 10px; }.modal-stock-input input { padding-right: 42px !important; }.modal-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 22px; padding-top: 15px; border-top: 1px solid #edf0ed; }.form-error { margin: 11px 0 0; padding: 9px 10px; border: 1px solid #f1d6d2; border-radius: 6px; color: #a34d45; background: #fff5f4; font-size: 9px; }
.payment-backdrop { z-index: 22; }.payment-modal { width: min(100%, 420px); padding: 20px 22px 17px; }.payment-header { display: flex; align-items: center; gap: 11px; }.payment-header .modal-close { margin: 0 1px 0 -7px; }.payment-heading-icon { width: 35px; height: 35px; }.payment-header h2 { font-size: 19px; }.payment-total { display: flex; flex-direction: column; align-items: center; margin: 22px 0 18px; padding: 17px; border: 1px solid #e8efe8; border-radius: 9px; background: #f7faf7; }.payment-total span { color: #87948a; font-size: 9px; }.payment-total b { margin-top: 3px; color: #205d3d; font-family: Georgia, serif; font-size: 29px; font-weight: 500; }.cash-label { display: block; margin-bottom: 6px; color: #4f5d53; font-size: 9px; font-weight: 600; }.cash-input-wrap { display: flex; height: 44px; align-items: center; gap: 8px; padding: 0 12px; border: 1px solid #dce7dd; border-radius: 7px; }.cash-input-wrap:focus-within { border-color: #72a583; box-shadow: 0 0 0 3px #72a58318; }.cash-input-wrap span { color: #809084; font-size: 10px; }.cash-input-wrap input { width: 100%; border: 0; outline: 0; color: #2e4034; font-size: 15px; font-weight: 600; }.quick-cash { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 9px; }.quick-cash button { padding: 6px 8px; border: 1px solid #e7ece7; border-radius: 5px; color: #6a7b6e; background: #fff; font-size: 8px; }.quick-cash button:hover { border-color: #c8ddca; color: #34724d; background: #f4f9f4; }.change-row { display: flex; justify-content: space-between; margin-top: 17px; padding: 12px 0; border-top: 1px dashed #e6ebe6; color: #829087; font-size: 9px; }.change-row b { color: #31734a; font-size: 12px; }.change-row b.change-short { color: #b45b4e; }.confirm-payment { display: flex; width: 100%; height: 42px; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 7px; color: #fff; background: #176b4d; font-size: 10px; font-weight: 650; }.confirm-payment:hover:not(:disabled) { background: #10543b; }.confirm-payment:disabled { cursor: not-allowed; background: #b6c5b9; }.confirm-payment svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.8; }.payment-secure { display: flex; align-items: center; justify-content: center; gap: 5px; margin: 11px 0 0; color: #9ca69e; font-size: 8px; }.payment-secure svg { width: 13px; height: 13px; fill: none; stroke: #79a387; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.receipt-modal { width: min(100%, 390px); padding: 25px 25px 21px; text-align: center; }.receipt-success { display: grid; width: 48px; height: 48px; place-items: center; margin: 0 auto 12px; border-radius: 50%; color: #fff; background: #4d9a68; box-shadow: 0 0 0 7px #edf7ef; }.receipt-success svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2; }.receipt-modal h2 { margin: 4px 0 2px; color: #293a2f; font-family: Georgia, serif; font-size: 24px; font-weight: 400; }.receipt-reference { margin: 0; color: #9aa49c; font-size: 9px; letter-spacing: .6px; }.receipt-items { margin-top: 19px; padding: 7px 0; border-top: 1px dashed #e7ece7; border-bottom: 1px dashed #e7ece7; }.receipt-items > div, .receipt-line, .receipt-change { display: flex; justify-content: space-between; padding: 7px 0; color: #829087; font-size: 9px; text-align: left; }.receipt-items span small { color: #a5ada6; }.receipt-items b, .receipt-line b { color: #56655b; font-weight: 600; }.receipt-line { padding: 5px 0; }.receipt-change { margin-top: 6px; padding-top: 11px; border-top: 1px solid #edf0ed; color: #3b6f49; font-weight: 600; }.receipt-change b { color: #286543; font-size: 13px; }.receipt-done { width: 100%; margin-top: 15px; }
@keyframes modal-in { from { opacity: 0; transform: translateY(7px) scale(.99); } to { opacity: 1; transform: translateY(0) scale(1); } }
@media (max-width: 900px) { .topbar-inner, .pos-content { width: calc(100% - 40px); }.main-nav { margin-left: 24px; }.pos-layout { grid-template-columns: minmax(0, 1.3fr) minmax(300px, .9fr); gap: 12px; }.catalog-panel { padding-right: 15px; padding-left: 15px; }.product-grid { grid-template-columns: repeat(auto-fill, minmax(125px, 1fr)); gap: 8px; }.product-card { min-height: 145px; padding: 10px; } }
@media (max-width: 680px) { .pos-topbar { height: 58px; }.topbar-inner, .pos-content { width: calc(100% - 28px); }.brand-mark { width: 29px; height: 29px; font-size: 18px; }.brand-name { font-size: 10px; }.main-nav { margin-left: 13px; }.nav-item { gap: 5px; padding: 0 8px; font-size: 9px; }.nav-item svg { width: 13px; height: 13px; }.topbar-right { gap: 6px; }.workspace-label, .topbar-divider, .logout-button span { display: none; }.manage-button { width: 31px; height: 31px; justify-content: center; padding: 0; }.manage-button span { display: none; }.logout-button { padding: 7px; }.page-heading { padding: 22px 0 16px; }.page-heading h1 { font-size: 27px; }.heading-description { max-width: 285px; font-size: 9px; line-height: 1.6; }.shift-status { gap: 7px; padding: 6px 8px; }.status-icon { width: 27px; height: 27px; }.shift-status small { font-size: 6px; }.shift-status b { font-size: 8px; }.pos-layout { grid-template-columns: 1fr; }.catalog-panel { min-height: 360px; padding: 15px 13px 0; }.product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }.product-card { min-height: 143px; }.catalog-hint { display: none; }.category-row { min-height: 47px; }.cart-panel { position: static; min-height: 410px; max-height: none; }.cart-items { max-height: 280px; }.cart-empty { min-height: 165px; }.pos-footer { margin-top: 13px; }.pos-footer span:nth-child(2) { display: none; }.modal-backdrop { align-items: end; padding: 10px; }.manager-modal, .payment-modal, .receipt-modal { max-height: calc(100dvh - 20px); overflow-y: auto; border-radius: 13px 13px 9px 9px; }.manager-product-list { max-height: 43vh; }.receipt-modal { margin: auto 0; }.payment-total b { font-size: 26px; } }
@media (max-width: 380px) { .shift-status { display: none; }.product-card { padding: 9px; }.product-card-name { font-size: 9px; }.product-card-price { font-size: 10px; }.form-fields-row { grid-template-columns: 1fr; gap: 12px; } }
@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; } }
</style>
