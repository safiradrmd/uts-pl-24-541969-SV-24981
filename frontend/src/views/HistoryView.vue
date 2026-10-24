<template>
  <main class="history-page">
    <PosHeader />
    <div class="history-content">
      <section class="page-heading">
        <div><p class="heading-kicker">LAPORAN / TRANSAKSI</p><h1>Riwayat penjualan</h1><p class="heading-description">Lihat transaksi yang sudah selesai dan rincian pembayaran.</p></div>
        <RouterLink class="back-to-cashier" to="/products"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>Kembali ke kasir</RouterLink>
      </section>

      <section class="history-summary"><span class="summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4"/></svg></span><span><small>SELURUH TRANSAKSI</small><b>{{ pagination.total }}</b><small>transaksi tercatat</small></span><span class="summary-mark">TRX</span></section>

      <section class="history-panel">
        <div class="panel-header"><div><h2>Daftar transaksi</h2><p>Transaksi terbaru ditampilkan lebih dulu.</p></div><span class="history-count">{{ pagination.total }} transaksi</span></div>
        <form class="history-filters" @submit.prevent="searchHistory">
          <label class="history-search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg><input v-model="search" type="search" placeholder="Cari nomor transaksi atau produk" /><button v-if="search" type="button" aria-label="Hapus pencarian" @click="search = ''; searchHistory()">×</button></label>
          <label class="history-date"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M7.5 3v4M16.5 3v4M4 9.5h16"/></svg><input v-model="date" type="date" aria-label="Filter tanggal transaksi" @change="searchHistory" /></label>
          <button class="filter-button" type="submit">Cari</button>
          <button v-if="search || date" class="reset-button" type="button" @click="clearFilters">Reset</button>
        </form>

        <div v-if="errorMessage" class="history-error" role="alert">{{ errorMessage }}</div>
        <div class="table-scroll">
          <table class="sales-table">
            <thead><tr><th>TRANSAKSI</th><th>WAKTU</th><th>PRODUK</th><th>PEMBAYARAN</th><th>TOTAL</th><th></th></tr></thead>
            <tbody>
              <tr v-if="isLoading"><td colspan="6" class="history-state">Memuat riwayat transaksi...</td></tr>
              <tr v-else-if="!sales.length"><td colspan="6" class="history-state"><span class="empty-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4"/></svg></span><b>Belum ada transaksi</b><small>Transaksi yang sudah dibayar akan muncul di sini.</small></td></tr>
              <template v-else>
                <tr v-for="sale in sales" :key="sale.id">
                  <td><span class="reference">{{ sale.reference }}</span><small class="sub-label">Struk penjualan</small></td>
                  <td class="sale-date">{{ formatDateTime(sale.created_at) }}</td>
                  <td><span class="item-count">{{ sale.items.reduce((count, item) => count + Number(item.quantity), 0) }} item</span><small class="item-names">{{ sale.items.map((item) => item.product_name).join(', ') }}</small></td>
                  <td><span class="cash-method"><i></i> Tunai</span></td>
                  <td class="sale-total">{{ formatCurrency(sale.subtotal) }}</td>
                  <td><button class="detail-button" type="button" @click="selectedSale = sale">Rincian</button></td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <footer class="pagination"><span>Menampilkan {{ sales.length }} dari {{ pagination.total }} transaksi</span><div><button type="button" :disabled="pagination.current_page <= 1 || isLoading" @click="loadSales(pagination.current_page - 1)">Sebelumnya</button><span>Halaman {{ pagination.current_page }} / {{ pagination.last_page }}</span><button type="button" :disabled="pagination.current_page >= pagination.last_page || isLoading" @click="loadSales(pagination.current_page + 1)">Berikutnya</button></div></footer>
      </section>

      <footer class="page-footer"><span>STOK<span>KASIR</span></span><span>Riwayat transaksi tersimpan di database.</span><span>{{ todayLabel }}</span></footer>
    </div>

    <Teleport to="body">
      <div v-if="selectedSale" class="modal-backdrop" @click.self="selectedSale = null">
        <section class="receipt-modal" role="dialog" aria-modal="true" aria-labelledby="receipt-title">
          <div class="receipt-top"><span class="receipt-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4"/></svg></span><button class="modal-close" type="button" aria-label="Tutup rincian" @click="selectedSale = null"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button></div>
          <p class="modal-kicker">RINCIAN TRANSAKSI</p><h2 id="receipt-title">{{ selectedSale.reference }}</h2><p class="receipt-date">{{ formatDateTime(selectedSale.created_at) }}</p>
          <div class="receipt-items"><div v-for="item in selectedSale.items" :key="item.id"><span>{{ item.product_name }} <small>× {{ item.quantity }} · {{ formatCurrency(item.price) }}</small></span><b>{{ formatCurrency(item.subtotal) }}</b></div></div>
          <div class="receipt-row"><span>Total belanja</span><b>{{ formatCurrency(selectedSale.subtotal) }}</b></div><div class="receipt-row"><span>Tunai diterima</span><b>{{ formatCurrency(selectedSale.cash_received) }}</b></div><div class="receipt-change"><span>Kembalian</span><b>{{ formatCurrency(selectedSale.change_due) }}</b></div>
          <button class="done-button" type="button" @click="selectedSale = null">Tutup</button>
        </section>
      </div>
    </Teleport>
  </main>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'
import PosHeader from '../components/PosHeader.vue'

const router = useRouter()
const search = ref('')
const date = ref('')
const sales = ref([])
const selectedSale = ref(null)
const isLoading = ref(false)
const errorMessage = ref('')
const pagination = ref({ current_page: 1, last_page: 1, total: 0 })
const todayLabel = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date())

const formatCurrency = (value) => new Intl.NumberFormat('id-ID', {
  style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
}).format(Number(value) || 0)

const formatDateTime = (value) => new Intl.DateTimeFormat('id-ID', {
  dateStyle: 'medium', timeStyle: 'short',
}).format(new Date(value))

const searchHistory = () => loadSales(1)

const clearFilters = () => {
  search.value = ''
  date.value = ''
  loadSales(1)
}

const loadSales = async (page = 1) => {
  if (isLoading.value) return
  isLoading.value = true
  errorMessage.value = ''
  try {
    const response = await api.get('/sales', {
      params: {
        page,
        search: search.value.trim() || undefined,
        date: date.value || undefined,
      },
    })
    const result = response.data.data
    sales.value = result.data || []
    pagination.value = {
      current_page: result.current_page || 1,
      last_page: result.last_page || 1,
      total: result.total || 0,
    }
  } catch (error) {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      router.push('/')
    } else {
      errorMessage.value = error.response?.data?.message || 'Riwayat transaksi gagal dimuat. Coba lagi.'
    }
  } finally {
    isLoading.value = false
  }
}

const handleEscape = (event) => {
  if (event.key === 'Escape') selectedSale.value = null
}

onMounted(() => {
  loadSales()
  window.addEventListener('keydown', handleEscape)
})
onUnmounted(() => window.removeEventListener('keydown', handleEscape))
</script>

<style scoped>
.history-page { min-height: 100vh; color: #24342a; background: #f5f7f4; }
.history-content { width: min(1200px, calc(100% - 64px)); margin: 0 auto; }
.page-heading { display: flex; align-items: center; justify-content: space-between; padding: 30px 0 20px; }
.heading-kicker, .modal-kicker { margin: 0 0 4px; color: #6c9478; font-size: 8px; font-weight: 750; letter-spacing: 1.2px; }
.page-heading h1 { margin: 0; color: #1d2d22; font-family: Georgia, 'Times New Roman', serif; font-size: 30px; font-weight: 400; letter-spacing: -.7px; }
.heading-description { margin: 4px 0 0; color: #89948b; font-size: 11px; }
.back-to-cashier { display: inline-flex; align-items: center; gap: 7px; padding: 9px 12px; border: 1px solid #dce9de; border-radius: 7px; color: #39734d; background: #fff; font-size: 9px; font-weight: 600; text-decoration: none; }
.back-to-cashier:hover { background: #f3f8f3; }
.back-to-cashier svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; transform: rotate(180deg); }
.history-summary { position: relative; display: flex; width: 250px; min-height: 85px; align-items: center; gap: 12px; overflow: hidden; padding: 14px 16px; border: 1px solid #e7ece7; border-radius: 9px; background: #fff; }
.summary-icon { display: grid; width: 38px; height: 38px; flex: none; place-items: center; border-radius: 11px; color: #4d845d; background: #edf6ef; }
.summary-icon svg { width: 19px; height: 19px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.history-summary > span:nth-child(2) { display: flex; flex-direction: column; }
.history-summary small { color: #97a198; font-size: 7px; letter-spacing: .55px; }
.history-summary b { margin: 1px 0; color: #33483a; font-size: 19px; line-height: 1.1; }
.summary-mark { position: absolute; right: 8px; bottom: 1px; color: #f2f5f2; font-size: 38px; font-weight: 750; letter-spacing: -2px; }
.history-panel { margin-top: 15px; overflow: hidden; border: 1px solid #e7ece7; border-radius: 10px; background: #fff; }
.panel-header { display: flex; min-height: 68px; align-items: center; justify-content: space-between; padding: 13px 18px; }
.panel-header h2 { margin: 0; color: #293a2f; font-size: 13px; font-weight: 650; }
.panel-header p { margin: 3px 0 0; color: #9aa49c; font-size: 9px; }
.history-count { padding: 5px 8px; border-radius: 5px; color: #718478; background: #f3f6f3; font-size: 8px; }
.history-filters { display: flex; align-items: center; gap: 8px; padding: 11px 18px; border-top: 1px solid #f0f2f0; border-bottom: 1px solid #f0f2f0; background: #fbfcfb; }
.history-search, .history-date { display: flex; height: 34px; align-items: center; gap: 7px; padding: 0 9px; border: 1px solid #e7ece7; border-radius: 6px; background: #fff; }
.history-search { width: min(340px, 48%); }
.history-search svg, .history-date svg { width: 14px; height: 14px; flex: none; fill: none; stroke: #9aa59c; stroke-linecap: round; stroke-width: 1.5; }
.history-search input, .history-date input { width: 100%; min-width: 0; border: 0; outline: 0; color: #45554a; background: transparent; font-family: inherit; font-size: 9px; }
.history-search input::placeholder { color: #adb6af; }
.history-search button { border: 0; color: #95a097; background: transparent; font-size: 16px; }
.history-date { width: 155px; }
.history-date input { font-size: 8px; }
.filter-button { height: 34px; padding: 0 13px; border: 0; border-radius: 6px; color: white; background: #176b4d; font-size: 9px; font-weight: 600; }
.filter-button:hover { background: #10543b; }
.reset-button { height: 32px; padding: 0 7px; border: 0; color: #849087; background: transparent; font-size: 8px; }
.history-error { margin: 12px 18px 0; padding: 9px 11px; border: 1px solid #f1d6d2; border-radius: 6px; color: #a34d45; background: #fff5f4; font-size: 9px; }
.table-scroll { overflow-x: auto; }
.sales-table { width: 100%; border-collapse: collapse; text-align: left; }
.sales-table thead { background: #f8faf8; }
.sales-table th { height: 34px; padding: 0 12px; color: #99a39b; font-size: 7px; font-weight: 700; letter-spacing: .65px; white-space: nowrap; }
.sales-table th:first-child, .sales-table td:first-child { padding-left: 18px; }
.sales-table th:last-child, .sales-table td:last-child { padding-right: 18px; }
.sales-table td { height: 65px; padding: 9px 12px; border-top: 1px solid #f0f2f0; color: #59675d; font-size: 9px; }
.reference, .sub-label, .item-count, .item-names { display: block; }
.reference { color: #3c6548; font-size: 9px; font-weight: 650; white-space: nowrap; }
.sub-label, .item-names { max-width: 190px; overflow: hidden; margin-top: 3px; color: #a0a9a2; font-size: 8px; text-overflow: ellipsis; white-space: nowrap; }
.sale-date { min-width: 130px; color: #77847a !important; white-space: nowrap; }
.item-count { color: #536257; font-weight: 600; }
.cash-method { display: inline-flex; align-items: center; gap: 5px; padding: 5px 7px; border-radius: 20px; color: #66816b; background: #f1f7f1; font-size: 8px; white-space: nowrap; }
.cash-method i { width: 5px; height: 5px; border-radius: 50%; background: #70a77a; }
.sale-total { color: #304b38 !important; font-weight: 650; white-space: nowrap; }
.detail-button { padding: 6px 8px; border: 1px solid #e3ebe3; border-radius: 5px; color: #65816a; background: #fff; font-size: 8px; white-space: nowrap; }
.detail-button:hover { border-color: #c9dfcc; color: #317047; background: #f4f9f4; }
.history-state { height: 245px !important; color: #96a198 !important; text-align: center; }
.history-state b, .history-state small { display: block; }
.history-state b { margin-top: 9px; color: #55645a; font-size: 10px; }
.history-state small { margin-top: 4px; color: #9ba59d; font-size: 8px; }
.empty-icon { display: grid; width: 42px; height: 42px; place-items: center; margin: 0 auto; border-radius: 13px; color: #7c9a82; background: #f0f6f1; }
.empty-icon svg { width: 21px; height: 21px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.4; }
.pagination { display: flex; min-height: 46px; align-items: center; justify-content: space-between; padding: 0 18px; border-top: 1px solid #edf0ed; color: #9ca69e; font-size: 8px; }
.pagination > div { display: flex; align-items: center; gap: 10px; }
.pagination button { padding: 6px 8px; border: 1px solid #e5eae5; border-radius: 5px; color: #637269; background: #fff; font-size: 8px; }
.pagination button:disabled { color: #bdc5be; background: #fafbfa; cursor: not-allowed; }
.page-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 17px; padding: 13px 0 17px; border-top: 1px solid #e7ebe7; color: #a1aaa3; font-size: 8px; }
.page-footer > span:first-child { color: #688272; font-weight: 750; letter-spacing: .8px; }
.page-footer > span:first-child span { color: #96a69a; font-weight: 500; }
.modal-backdrop { position: fixed; z-index: 20; inset: 0; display: grid; overflow-y: auto; place-items: center; padding: 22px; background: #13251dc2; backdrop-filter: blur(4px); }
.receipt-modal { width: min(100%, 390px); overflow: hidden; padding: 25px; border: 1px solid #e7ece7; border-radius: 13px; background: #fff; box-shadow: 0 24px 80px #10261a40; animation: modal-in .2s ease-out; }
.receipt-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.receipt-icon { display: grid; width: 39px; height: 39px; place-items: center; border-radius: 11px; color: #4d845d; background: #edf6ef; }
.receipt-icon svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.modal-close { display: grid; width: 30px; height: 30px; place-items: center; border: 0; border-radius: 7px; color: #8c9990; background: transparent; }
.modal-close:hover { color: #3c5744; background: #f2f6f2; }
.modal-close svg { width: 17px; height: 17px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.7; }
.receipt-modal h2 { margin: 4px 0 2px; color: #293a2f; font-family: inherit; font-size: 16px; font-weight: 650; }
.receipt-date { margin: 0; color: #9aa49c; font-size: 9px; }
.receipt-items { margin-top: 19px; padding: 7px 0; border-top: 1px dashed #e7ece7; border-bottom: 1px dashed #e7ece7; }
.receipt-items > div, .receipt-row, .receipt-change { display: flex; justify-content: space-between; padding: 7px 0; color: #829087; font-size: 9px; }
.receipt-items span small { color: #a5ada6; }
.receipt-items b, .receipt-row b { color: #56655b; font-weight: 600; }
.receipt-row { padding: 5px 0; }
.receipt-change { margin-top: 6px; padding-top: 11px; border-top: 1px solid #edf0ed; color: #3b6f49; font-weight: 600; }
.receipt-change b { color: #286543; font-size: 13px; }
.done-button { width: 100%; height: 36px; margin-top: 15px; border: 0; border-radius: 6px; color: #fff; background: #176b4d; font-size: 9px; font-weight: 600; }
.done-button:hover { background: #10543b; }
@keyframes modal-in { from { opacity: 0; transform: translateY(7px) scale(.99); } to { opacity: 1; transform: translateY(0) scale(1); } }
@media (max-width: 680px) { .history-content { width: calc(100% - 28px); }.page-heading { align-items: flex-start; padding: 22px 0 16px; }.page-heading h1 { font-size: 27px; }.heading-description { max-width: 270px; font-size: 9px; line-height: 1.6; }.back-to-cashier { padding: 7px 8px; font-size: 8px; white-space: nowrap; }.history-summary { width: 100%; min-height: 76px; }.panel-header { padding: 12px 13px; }.history-filters { flex-wrap: wrap; padding: 10px 12px; }.history-search { width: 100%; }.history-date { flex: 1; }.sales-table { min-width: 720px; }.sales-table th:first-child, .sales-table td:first-child { padding-left: 13px; }.sales-table th:last-child, .sales-table td:last-child { padding-right: 13px; }.pagination { align-items: flex-start; flex-direction: column; gap: 8px; padding: 10px 13px; }.pagination > div { width: 100%; justify-content: space-between; }.page-footer span:nth-child(2) { display: none; }.modal-backdrop { align-items: end; padding: 10px; }.receipt-modal { max-height: calc(100dvh - 20px); overflow-y: auto; margin: auto 0; border-radius: 13px 13px 9px 9px; } }
@media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; } }
</style>
