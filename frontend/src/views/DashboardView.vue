<template>
  <main class="dashboard-page">
    <PosHeader />
    <div class="dashboard-content">
      <section class="dashboard-heading">
        <div><p class="eyebrow">RINGKASAN TOKO</p><h1>Dashboard</h1><p class="heading-copy">Pantau performa penjualan dan kondisi stok tokomu.</p></div>
        <div class="today-pill"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M7.5 3v4M16.5 3v4M4 9.5h16"/></svg><span><small>HARI INI</small><b>{{ todayLabel }}</b></span></div>
      </section>

      <p v-if="errorMessage" class="dashboard-error" role="alert">{{ errorMessage }}</p>

      <section class="summary-grid" aria-label="Ringkasan toko">
        <article class="summary-card">
          <span class="summary-icon products-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21"/></svg></span>
          <span class="summary-copy"><small>JENIS BARANG</small><b>{{ metrics.products_count }}</b><span>produk dalam katalog</span></span>
          <span class="summary-watermark">01</span>
        </article>
        <article class="summary-card">
          <span class="summary-icon units-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="m4.5 7.8 7.5 4.3 7.5-4.3M12 12v9M8 5.3l8 4.5"/></svg></span>
          <span class="summary-copy"><small>UNIT DALAM STOK</small><b>{{ numberFormat(metrics.units_in_stock) }}</b><span>unit tersedia di toko</span></span>
          <span class="summary-watermark">02</span>
        </article>
        <article class="summary-card">
          <span class="summary-icon transactions-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v17l-3-2-4 2-4-2-3 2V4Z"/><path d="M8 8h8M8 12h8M8 16h4"/></svg></span>
          <span class="summary-copy"><small>TRANSAKSI HARI INI</small><b>{{ numberFormat(metrics.today_transactions) }}</b><span>transaksi selesai</span></span>
          <span class="summary-watermark">03</span>
        </article>
        <article class="summary-card revenue-card">
          <span class="summary-icon revenue-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
          <span class="summary-copy"><small>PENDAPATAN HARI INI</small><b>{{ formatCurrency(metrics.today_revenue) }}</b><span>dari transaksi hari ini</span></span>
          <span class="summary-watermark">04</span>
        </article>
      </section>

      <section class="dashboard-grid">
        <section class="inventory-panel">
          <header class="panel-heading">
            <div><p class="panel-eyebrow">KONTROL INVENTARIS</p><h2>Manajemen stok</h2><p class="panel-description">Pantau jumlah barang dan sesuaikan stok kapan saja.</p></div>
            <span class="inventory-total">{{ filteredProducts.length }} barang</span>
          </header>

          <div class="inventory-toolbar">
            <label class="inventory-search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg><input v-model="searchQuery" type="search" placeholder="Cari nama barang..." /></label>
            <button class="low-stock-filter" :class="{ selected: showLowStockOnly }" type="button" @click="showLowStockOnly = !showLowStockOnly"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v4M12 16.5v.1"/></svg>Stok menipis <span>{{ dashboard.low_stock_products.length }}</span></button>
          </div>

          <div class="inventory-table-wrap">
            <table class="inventory-table">
              <thead><tr><th>BARANG</th><th>HARGA JUAL</th><th>JUMLAH STOK</th><th>STATUS</th><th></th></tr></thead>
              <tbody>
                <tr v-if="isLoading"><td colspan="5" class="table-message">Memuat data inventaris...</td></tr>
                <tr v-else-if="!filteredProducts.length"><td colspan="5" class="table-message">{{ searchQuery || showLowStockOnly ? 'Tidak ada barang yang cocok.' : 'Belum ada barang di katalog.' }}</td></tr>
                <template v-else>
                  <tr v-for="product in filteredProducts" :key="product.id">
                    <td><div class="product-cell"><span class="product-avatar">{{ product.name?.charAt(0)?.toUpperCase() || 'P' }}</span><span><b>{{ product.name }}</b><small>Kode #{{ product.id }}</small></span></div></td>
                    <td class="price-cell">{{ formatCurrency(product.price) }}</td>
                    <td><div class="stock-cell"><b>{{ numberFormat(product.stock) }}</b><span class="stock-track"><i :class="stockTone(product.stock)" :style="{ width: stockWidth(product.stock) }"></i></span></div></td>
                    <td><span class="stock-status" :class="stockTone(product.stock)"><i></i>{{ stockLabel(product.stock) }}</span></td>
                    <td><button class="stock-edit-button" type="button" @click="editStock(product)">Atur stok</button></td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
          <footer class="inventory-footer"><span><i></i> Stok akan langsung diperbarui di halaman kasir</span><button type="button" @click="loadDashboard">Muat ulang</button></footer>
        </section>

        <aside class="activity-panel">
          <header class="activity-heading"><div><p class="panel-eyebrow">AKTIVITAS TOKO</p><h2>Transaksi terbaru</h2></div><RouterLink to="/history">Lihat semua <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></RouterLink></header>
          <div v-if="dashboard.recent_sales.length" class="recent-list">
            <article v-for="sale in dashboard.recent_sales" :key="sale.id" class="recent-sale"><span class="sale-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v17l-3-2-4 2-4-2-3 2V4Z"/><path d="M8 8h8M8 12h5"/></svg></span><span class="sale-info"><b>{{ sale.reference }}</b><small>{{ sale.items.reduce((total, item) => total + Number(item.quantity), 0) }} barang · {{ formatDateTime(sale.created_at) }}</small></span><b class="sale-amount">{{ formatCurrency(sale.subtotal) }}</b></article>
          </div>
          <div v-else class="activity-empty"><span class="empty-receipt"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v17l-3-2-4 2-4-2-3 2V4Z"/><path d="M8 8h8M8 12h8M8 16h4"/></svg></span><b>Belum ada transaksi</b><p>Transaksi yang selesai akan muncul di sini.</p></div>

          <div class="low-stock-box"><div class="low-stock-heading"><span class="warning-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v4M12 16.5v.1"/></svg></span><span><b>Perlu restok</b><small>{{ dashboard.low_stock_products.length }} barang stok menipis</small></span></div>
            <div v-if="dashboard.low_stock_products.length" class="low-stock-list"><button v-for="product in dashboard.low_stock_products" :key="product.id" type="button" @click="editStock(findProduct(product.id))"><span>{{ product.name }}</span><b>{{ product.stock }} tersisa</b></button></div>
            <p v-else class="all-stock-good">Semua barang memiliki stok yang cukup.</p>
          </div>
        </aside>
      </section>

      <footer class="dashboard-footer"><span>STOK<span>KASIR</span></span><span>Ringkasan performa tokomu.</span><span>{{ todayLabel }}</span></footer>
    </div>

    <Teleport to="body">
      <div v-if="selectedProduct" class="modal-backdrop" @click.self="closeStockEditor" @keydown.esc="closeStockEditor">
        <section class="stock-modal" role="dialog" aria-modal="true" aria-labelledby="stock-modal-title">
          <header class="modal-heading"><span class="modal-stock-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.7 8.2 4.5 8.2-4.5M12 12.2V21"/></svg></span><div><p class="panel-eyebrow">PERBARUI INVENTARIS</p><h2 id="stock-modal-title">Atur stok barang</h2></div><button class="modal-close" type="button" aria-label="Tutup" @click="closeStockEditor"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button></header>
          <p class="selected-name">{{ selectedProduct.name }} <span>Kode #{{ selectedProduct.id }}</span></p>
          <form @submit.prevent="saveStock">
            <label for="stock-quantity">Jumlah stok baru</label><div class="stock-input"><input id="stock-quantity" v-model.number="stockValue" type="number" min="0" required /><span>unit</span></div>
            <p v-if="stockError" class="stock-error" role="alert">{{ stockError }}</p>
            <div class="modal-actions"><button class="cancel-button" type="button" @click="closeStockEditor">Batal</button><button class="save-button" type="submit" :disabled="isSavingStock">{{ isSavingStock ? 'Menyimpan...' : 'Simpan stok' }}</button></div>
          </form>
        </section>
      </div>
    </Teleport>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'
import PosHeader from '../components/PosHeader.vue'

const router = useRouter()
const products = ref([])
const dashboard = ref({ low_stock_products: [], recent_sales: [] })
const isLoading = ref(false)
const isSavingStock = ref(false)
const errorMessage = ref('')
const searchQuery = ref('')
const showLowStockOnly = ref(false)
const selectedProduct = ref(null)
const stockValue = ref(0)
const stockError = ref('')
const metrics = ref({ products_count: 0, units_in_stock: 0, today_transactions: 0, today_revenue: 0 })
const todayLabel = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date())

const filteredProducts = computed(() => {
  const query = searchQuery.value.trim().toLocaleLowerCase('id-ID')
  return products.value.filter((product) => {
    const matchesQuery = !query || product.name.toLocaleLowerCase('id-ID').includes(query)
    const matchesStock = !showLowStockOnly.value || Number(product.stock) <= 5
    return matchesQuery && matchesStock
  })
})

const numberFormat = (value) => new Intl.NumberFormat('id-ID').format(Number(value) || 0)
const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
  style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
}).format(Number(value) || 0)
const formatDateTime = (value) => new Intl.DateTimeFormat('id-ID', {
  dateStyle: 'medium', timeStyle: 'short',
}).format(new Date(value))

const stockTone = (stock) => Number(stock) < 1 ? 'out-of-stock' : Number(stock) <= 5 ? 'low' : 'healthy'
const stockLabel = (stock) => Number(stock) < 1 ? 'Habis' : Number(stock) <= 5 ? 'Menipis' : 'Aman'
const stockWidth = (stock) => `${Math.max(5, Math.min(100, Number(stock) * 4))}%`
const findProduct = (id) => products.value.find((product) => product.id === id)

const loadDashboard = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const [summaryResponse, productsResponse] = await Promise.all([
      api.get('/dashboard'),
      api.get('/products'),
    ])
    dashboard.value = summaryResponse.data.data
    metrics.value = summaryResponse.data.data
    products.value = productsResponse.data.data || productsResponse.data
  } catch (error) {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/')
    } else {
      errorMessage.value = error.response?.data?.message || 'Ringkasan toko gagal dimuat. Coba lagi.'
    }
  } finally {
    isLoading.value = false
  }
}

const editStock = (product) => {
  if (!product) return
  selectedProduct.value = product
  stockValue.value = Number(product.stock)
  stockError.value = ''
}

const closeStockEditor = () => {
  if (isSavingStock.value) return
  selectedProduct.value = null
  stockError.value = ''
}

const saveStock = async () => {
  if (!selectedProduct.value || isSavingStock.value) return
  isSavingStock.value = true
  stockError.value = ''
  try {
    await api.put(`/products/${selectedProduct.value.id}`, { stock: stockValue.value })
    selectedProduct.value = null
    await loadDashboard()
  } catch (error) {
    stockError.value = error.response?.data?.errors?.stock?.[0]
      || error.response?.data?.message
      || 'Jumlah stok gagal diperbarui.'
  } finally {
    isSavingStock.value = false
  }
}

onMounted(loadDashboard)
</script>

<style scoped>
.dashboard-page { min-height: 100vh; color: #24342a; background: #f5f7f4; }
.dashboard-content { width: min(1240px, calc(100% - 64px)); margin: 0 auto; }
.dashboard-heading { display: flex; align-items: center; justify-content: space-between; padding: 30px 0 22px; }
.eyebrow, .panel-eyebrow { margin: 0 0 5px; color: #70917a; font-size: 8px; font-weight: 750; letter-spacing: 1.25px; }
.dashboard-heading h1 { margin: 0; color: #1e2e23; font-family: Georgia, 'Times New Roman', serif; font-size: 31px; font-weight: 400; letter-spacing: -.7px; }
.heading-copy { margin: 5px 0 0; color: #89948b; font-size: 10px; }
.today-pill { display: flex; align-items: center; gap: 9px; padding: 9px 12px; border: 1px solid #e5ebe5; border-radius: 8px; background: #fff; }
.today-pill svg { width: 17px; height: 17px; fill: none; stroke: #6a9477; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.today-pill small, .today-pill b { display: block; }
.today-pill small { color: #a0aaa2; font-size: 7px; font-weight: 700; letter-spacing: .7px; }
.today-pill b { margin-top: 2px; color: #536258; font-size: 9px; font-weight: 600; }
.dashboard-error { margin: 0 0 14px; padding: 10px 12px; border: 1px solid #f1d6d2; border-radius: 7px; color: #a34d45; background: #fff5f4; font-size: 9px; }
.summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; }
.summary-card { position: relative; display: flex; min-height: 104px; align-items: center; gap: 12px; overflow: hidden; padding: 16px; border: 1px solid #e7ece7; border-radius: 9px; background: #fff; }
.summary-icon { display: grid; width: 39px; height: 39px; flex: none; place-items: center; border-radius: 11px; }
.summary-icon svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.products-icon { color: #548267; background: #edf6ef; }.units-icon { color: #a67d4e; background: #faf3e9; }.transactions-icon { color: #667ba0; background: #eef1f8; }.revenue-icon { color: #548a7f; background: #edf6f4; }
.summary-copy { position: relative; z-index: 1; display: flex; min-width: 0; flex-direction: column; }
.summary-copy small { color: #8e9a91; font-size: 7px; font-weight: 650; letter-spacing: .55px; white-space: nowrap; }
.summary-copy b { overflow: hidden; margin: 3px 0 1px; color: #293a2f; font-size: 20px; font-weight: 650; letter-spacing: -.5px; text-overflow: ellipsis; white-space: nowrap; }
.revenue-card .summary-copy b { font-size: 17px; }
.summary-copy span { color: #a1aaa3; font-size: 8px; }
.summary-watermark { position: absolute; right: 7px; bottom: -2px; color: #f1f4f1; font-family: Georgia, serif; font-size: 39px; font-weight: 700; line-height: 1; }
.dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(300px, .82fr); align-items: start; gap: 14px; margin-top: 15px; }
.inventory-panel, .activity-panel { min-width: 0; overflow: hidden; border: 1px solid #e7ece7; border-radius: 9px; background: #fff; }
.panel-heading { display: flex; align-items: center; justify-content: space-between; padding: 17px 17px 15px; }
.panel-heading h2, .activity-heading h2 { margin: 0; color: #2d3e32; font-size: 13px; font-weight: 650; }
.panel-description { margin: 4px 0 0; color: #96a098; font-size: 8px; }
.inventory-total { padding: 5px 8px; border-radius: 5px; color: #708477; background: #f3f6f3; font-size: 8px; white-space: nowrap; }
.inventory-toolbar { display: flex; align-items: center; gap: 8px; padding: 10px 17px; border-top: 1px solid #f0f2f0; border-bottom: 1px solid #f0f2f0; background: #fbfcfb; }
.inventory-search { display: flex; width: min(290px, 55%); height: 33px; align-items: center; gap: 7px; padding: 0 9px; border: 1px solid #e7ece7; border-radius: 6px; background: #fff; }
.inventory-search:focus-within { border-color: #85ae8d; box-shadow: 0 0 0 3px #85ae8d18; }
.inventory-search svg { width: 14px; height: 14px; flex: none; fill: none; stroke: #9aa59c; stroke-linecap: round; stroke-width: 1.5; }
.inventory-search input { width: 100%; min-width: 0; border: 0; outline: 0; color: #45554a; background: transparent; font-size: 9px; }
.inventory-search input::placeholder { color: #adb6af; }
.low-stock-filter { display: flex; height: 31px; align-items: center; gap: 5px; padding: 0 8px; border: 1px solid #ece7dc; border-radius: 6px; color: #9a7a4d; background: #fffdf9; font-size: 8px; white-space: nowrap; }
.low-stock-filter.selected { border-color: #e1c99f; background: #fbf3e6; }
.low-stock-filter svg { width: 13px; height: 13px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.low-stock-filter span { display: grid; min-width: 16px; height: 16px; place-items: center; border-radius: 5px; color: #9b7844; background: #f7edda; font-size: 7px; }
.inventory-table-wrap { overflow-x: auto; }
.inventory-table { width: 100%; border-collapse: collapse; text-align: left; }
.inventory-table thead { background: #f8faf8; }
.inventory-table th { height: 34px; padding: 0 10px; color: #99a39b; font-size: 7px; font-weight: 700; letter-spacing: .6px; white-space: nowrap; }
.inventory-table th:first-child, .inventory-table td:first-child { padding-left: 17px; }
.inventory-table th:last-child, .inventory-table td:last-child { padding-right: 17px; }
.inventory-table td { height: 58px; padding: 7px 10px; border-top: 1px solid #f0f2f0; color: #58665c; font-size: 9px; }
.product-cell { display: flex; align-items: center; gap: 8px; }
.product-avatar { display: grid; width: 30px; height: 30px; flex: none; place-items: center; border: 1px solid #e5eee5; border-radius: 8px; color: #548164; background: #f1f7f2; font-family: Georgia, serif; font-size: 13px; font-weight: 700; }
.product-cell b, .product-cell small { display: block; }
.product-cell b { max-width: 150px; overflow: hidden; color: #435247; font-size: 9px; font-weight: 620; text-overflow: ellipsis; white-space: nowrap; }
.product-cell small { margin-top: 2px; color: #a3aca5; font-size: 7px; }
.price-cell { color: #45574a !important; font-weight: 550; white-space: nowrap; }
.stock-cell { display: flex; min-width: 55px; flex-direction: column; gap: 5px; }
.stock-cell b { color: #48584d; font-size: 9px; font-weight: 650; }
.stock-track { display: block; width: 48px; height: 3px; overflow: hidden; border-radius: 3px; background: #edf1ed; }
.stock-track i { display: block; height: 100%; border-radius: inherit; background: #69a878; }.stock-track i.low { background: #d3a24e; }.stock-track i.out-of-stock { background: #ce7568; }
.stock-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 6px; border-radius: 20px; font-size: 7px; white-space: nowrap; }
.stock-status i { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.stock-status.healthy { color: #568263; background: #eff7f0; }.stock-status.low { color: #a67e44; background: #fbf4e8; }.stock-status.out-of-stock { color: #ad5c53; background: #fff1ef; }
.stock-edit-button { padding: 6px 7px; border: 1px solid #e4ebe4; border-radius: 5px; color: #69816e; background: #fff; font-size: 7px; white-space: nowrap; }
.stock-edit-button:hover { border-color: #c9dfcc; color: #317047; background: #f4f9f4; }
.table-message { height: 135px !important; color: #9aa49c !important; text-align: center; }
.inventory-footer { display: flex; min-height: 39px; align-items: center; justify-content: space-between; padding: 0 17px; border-top: 1px solid #edf0ed; color: #9ba59d; font-size: 7px; }
.inventory-footer span { display: flex; align-items: center; gap: 5px; }.inventory-footer i { width: 5px; height: 5px; border-radius: 50%; background: #6eaa79; }
.inventory-footer button { border: 0; color: #62826b; background: transparent; font-size: 8px; }
.activity-heading { display: flex; min-height: 66px; align-items: center; justify-content: space-between; padding: 13px 15px; border-bottom: 1px solid #f0f2f0; }
.activity-heading h2 { font-size: 12px; }.activity-heading a { display: flex; align-items: center; gap: 4px; color: #68866e; font-size: 8px; text-decoration: none; }.activity-heading a:hover { color: #286543; }
.activity-heading a svg { width: 12px; height: 12px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.recent-list { padding: 0 15px; }.recent-sale { display: flex; min-height: 57px; align-items: center; gap: 8px; border-bottom: 1px solid #f1f3f1; }.sale-icon { display: grid; width: 29px; height: 29px; flex: none; place-items: center; border-radius: 8px; color: #62836a; background: #f0f6f1; }.sale-icon svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.sale-info { min-width: 0; flex: 1; }.sale-info b, .sale-info small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.sale-info b { color: #4b5c50; font-size: 8px; font-weight: 650; }.sale-info small { margin-top: 3px; color: #a2aba4; font-size: 7px; }.sale-amount { color: #42644a; font-size: 8px; font-weight: 650; white-space: nowrap; }
.activity-empty { display: flex; min-height: 155px; flex-direction: column; align-items: center; justify-content: center; color: #94a097; text-align: center; }.empty-receipt { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 12px; color: #789681; background: #f0f6f1; }.empty-receipt svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.activity-empty b { margin-top: 8px; color: #58685d; font-size: 9px; }.activity-empty p { margin: 3px 0 0; font-size: 8px; }
.low-stock-box { margin: 8px 12px 12px; padding: 11px; border: 1px solid #f0e6d4; border-radius: 7px; background: #fffcf6; }.low-stock-heading { display: flex; align-items: center; gap: 8px; }.warning-icon { display: grid; width: 29px; height: 29px; flex: none; place-items: center; border-radius: 8px; color: #ad854a; background: #f8efdf; }.warning-icon svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }.low-stock-heading b, .low-stock-heading small { display: block; }.low-stock-heading b { color: #65553d; font-size: 9px; font-weight: 650; }.low-stock-heading small { margin-top: 2px; color: #ad9b7f; font-size: 7px; }.low-stock-list { margin-top: 7px; }.low-stock-list button { display: flex; width: 100%; align-items: center; justify-content: space-between; padding: 6px 0; border: 0; border-top: 1px solid #f3ecdf; color: #75664f; background: transparent; text-align: left; font-size: 8px; }.low-stock-list button:hover span { text-decoration: underline; }.low-stock-list button b { color: #ae7743; font-size: 7px; font-weight: 600; white-space: nowrap; }.all-stock-good { margin: 9px 0 0; color: #7d927e; font-size: 8px; }
.dashboard-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 19px; padding: 13px 0 18px; border-top: 1px solid #e7ebe7; color: #a1aaa3; font-size: 8px; }.dashboard-footer > span:first-child { color: #688272; font-weight: 750; letter-spacing: .8px; }.dashboard-footer > span:first-child span { color: #96a69a; font-weight: 500; }
.modal-backdrop { position: fixed; z-index: 20; inset: 0; display: grid; overflow-y: auto; place-items: center; padding: 20px; background: #13251dc2; backdrop-filter: blur(4px); }
.stock-modal { width: min(100%, 390px); padding: 20px; border: 1px solid #e7ece7; border-radius: 12px; background: #fff; box-shadow: 0 24px 80px #10261a40; }
.modal-heading { display: flex; align-items: center; gap: 10px; }.modal-stock-icon { display: grid; width: 36px; height: 36px; flex: none; place-items: center; border-radius: 10px; color: #548261; background: #edf6ef; }.modal-stock-icon svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }.modal-heading .panel-eyebrow { margin-bottom: 2px; font-size: 7px; }.modal-heading h2 { margin: 0; color: #293a2f; font-family: Georgia, serif; font-size: 19px; font-weight: 400; }.modal-close { display: grid; width: 29px; height: 29px; place-items: center; margin-left: auto; border: 0; border-radius: 7px; color: #8c9990; background: transparent; }.modal-close:hover { color: #3c5744; background: #f2f6f2; }.modal-close svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.7; }
.selected-name { margin: 18px 0; padding: 11px; border: 1px solid #e9eee9; border-radius: 7px; color: #45564a; background: #fafcfa; font-size: 10px; font-weight: 650; }.selected-name span { display: block; margin-top: 3px; color: #9ba59d; font-size: 8px; font-weight: 400; }
.stock-modal form label { display: block; margin-bottom: 6px; color: #536258; font-size: 9px; font-weight: 600; }.stock-input { position: relative; }.stock-input input { width: 100%; height: 40px; padding: 0 44px 0 11px; border: 1px solid #e3e9e3; border-radius: 6px; outline: 0; color: #35443a; font-size: 11px; }.stock-input input:focus { border-color: #78a683; box-shadow: 0 0 0 3px #78a68318; }.stock-input span { position: absolute; top: 0; right: 11px; display: flex; height: 40px; align-items: center; color: #98a39a; font-size: 8px; }
.stock-error { margin: 10px 0 0; padding: 8px 9px; border: 1px solid #f1d6d2; border-radius: 6px; color: #a34d45; background: #fff5f4; font-size: 8px; }.modal-actions { display: flex; justify-content: flex-end; gap: 7px; margin-top: 19px; padding-top: 13px; border-top: 1px solid #edf0ed; }.cancel-button, .save-button { min-height: 33px; padding: 0 11px; border-radius: 6px; font-size: 8px; font-weight: 600; }.cancel-button { border: 1px solid #e2e8e2; color: #66746a; background: #fff; }.save-button { border: 0; color: #fff; background: #176b4d; }.save-button:hover:not(:disabled) { background: #10543b; }.save-button:disabled { opacity: .7; cursor: wait; }
@media (max-width: 1050px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }.dashboard-grid { grid-template-columns: minmax(0, 1.35fr) minmax(275px, .85fr); }.summary-card { min-height: 94px; } }
@media (max-width: 760px) { .dashboard-content { width: calc(100% - 32px); }.dashboard-heading { align-items: flex-start; padding: 22px 0 16px; }.dashboard-heading h1 { font-size: 27px; }.heading-copy { max-width: 260px; font-size: 9px; line-height: 1.6; }.today-pill { gap: 6px; padding: 7px 8px; }.today-pill svg { width: 15px; height: 15px; }.today-pill small { font-size: 6px; }.today-pill b { font-size: 8px; }.summary-grid { gap: 8px; }.summary-card { min-height: 82px; gap: 8px; padding: 10px; }.summary-icon { width: 32px; height: 32px; border-radius: 9px; }.summary-icon svg { width: 16px; height: 16px; }.summary-copy small { font-size: 6px; }.summary-copy b, .revenue-card .summary-copy b { font-size: 15px; }.summary-copy span { font-size: 7px; }.summary-watermark { font-size: 30px; }.dashboard-grid { grid-template-columns: 1fr; gap: 11px; }.panel-heading { padding: 14px 12px; }.inventory-toolbar { flex-wrap: wrap; padding: 9px 12px; }.inventory-search { width: 100%; }.inventory-table { min-width: 620px; }.inventory-table th:first-child, .inventory-table td:first-child { padding-left: 12px; }.inventory-table th:last-child, .inventory-table td:last-child { padding-right: 12px; }.inventory-footer { padding: 0 12px; }.activity-panel { margin-bottom: 4px; }.dashboard-footer { margin-top: 12px; }.dashboard-footer span:nth-child(2) { display: none; } }
@media (max-width: 390px) { .today-pill { max-width: 135px; }.today-pill b { max-width: 103px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }.summary-card { align-items: flex-start; flex-direction: column; }.summary-copy b { margin-top: 4px; }.summary-copy span { display: none; }.summary-watermark { font-size: 26px; }.inventory-total { font-size: 7px; } }
</style>
