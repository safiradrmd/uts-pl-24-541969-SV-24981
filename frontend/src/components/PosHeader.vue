<template>
  <header class="pos-topbar">
    <div class="topbar-inner">
      <RouterLink class="brand" to="/products" aria-label="StokKasir">
        <span class="brand-mark">S</span><span class="brand-name">STOK<span>KASIR</span></span>
      </RouterLink>
      <nav class="main-nav" aria-label="Navigasi utama">
        <RouterLink class="nav-item" active-class="active" to="/dashboard">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V4H3zM13 20h8V11h-8zM13 8h8V4h-8zM3 20h8v-3H3z"/></svg>Dashboard
        </RouterLink>
        <RouterLink class="nav-item" active-class="active" to="/products">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v7H4zM14 4h6v4h-6zM14 12h6v8h-6zM4 15h6v5H4z"/></svg>Kasir
        </RouterLink>
        <RouterLink class="nav-item" active-class="active" to="/history">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/><circle cx="19" cy="18" r="3"/></svg>Riwayat
        </RouterLink>
      </nav>
      <div class="topbar-right">
        <span class="workspace-label"><i></i>Toko saya</span>
        <button v-if="showProductManager" class="manage-button" type="button" @click="$emit('manage-products')">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg><span>Kelola produk</span>
        </button>
        <span class="topbar-divider"></span>
        <button class="logout-button" type="button" title="Keluar" @click="handleLogout">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5M15 12H3M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg><span>Keluar</span>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

defineProps({
  showProductManager: { type: Boolean, default: false },
})
defineEmits(['manage-products'])

const router = useRouter()
const isLoggingOut = ref(false)

const handleLogout = async () => {
  if (isLoggingOut.value) return
  isLoggingOut.value = true
  try {
    await api.post('/logout')
  } catch (error) {
    // Abaikan jika token sudah kedaluwarsa.
  } finally {
    localStorage.removeItem('auth_token')
    router.push('/')
  }
}
</script>

<style scoped>
.pos-topbar { position: sticky; z-index: 5; top: 0; height: 66px; border-bottom: 1px solid #e7ece7; background: #fff; }
.topbar-inner { display: flex; width: min(1360px, calc(100% - 64px)); height: 100%; align-items: center; margin: 0 auto; }
.brand { display: flex; flex: none; align-items: center; gap: 9px; color: #20382b; text-decoration: none; }
.brand-mark { display: grid; width: 32px; height: 32px; place-items: center; border-radius: 10px; color: #17422f; background: #dcefe1; font-family: Georgia, serif; font-size: 20px; font-style: italic; font-weight: 700; }
.brand-name { font-size: 12px; font-weight: 800; letter-spacing: 1.25px; }
.brand-name span { color: #718479; font-weight: 500; }
.main-nav { display: flex; height: 100%; align-items: center; margin-left: 58px; }
.nav-item { position: relative; display: flex; height: 100%; align-items: center; gap: 8px; padding: 0 15px; color: #718078; font-size: 11px; text-decoration: none; }
.nav-item svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.nav-item.active { color: #176b4d; font-weight: 650; }
.nav-item.active::after { position: absolute; right: 13px; bottom: -1px; left: 13px; height: 2px; border-radius: 2px 2px 0 0; background: #2f865c; content: ''; }
.topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }
.workspace-label { display: flex; align-items: center; gap: 7px; color: #69776e; font-size: 10px; }
.workspace-label i { width: 6px; height: 6px; border-radius: 50%; background: #61a879; box-shadow: 0 0 0 3px #61a87920; }
.manage-button { display: flex; height: 34px; align-items: center; gap: 6px; padding: 0 11px; border: 1px solid #dce9de; border-radius: 7px; color: #39734d; background: #f5faf5; font-size: 10px; font-weight: 600; transition: .18s; }
.manage-button:hover { border-color: #b8d5bd; background: #edf6ee; }
.manage-button svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-width: 1.8; }
.topbar-divider { width: 1px; height: 21px; background: #e6ebe6; }
.logout-button { display: flex; align-items: center; gap: 6px; padding: 7px; border: 0; border-radius: 6px; color: #8a958d; background: transparent; font-size: 10px; }
.logout-button:hover { color: #a64a42; background: #fff5f4; }
.logout-button svg { width: 15px; height: 15px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }
@media (max-width: 900px) { .topbar-inner { width: calc(100% - 40px); }.main-nav { margin-left: 24px; } }
@media (max-width: 680px) { .pos-topbar { height: 58px; }.topbar-inner { width: calc(100% - 28px); }.brand-mark { width: 29px; height: 29px; font-size: 18px; }.brand-name { font-size: 10px; }.main-nav { margin-left: 13px; }.nav-item { gap: 5px; padding: 0 7px; font-size: 9px; }.nav-item svg { width: 13px; height: 13px; }.topbar-right { gap: 6px; }.workspace-label, .topbar-divider, .logout-button span { display: none; }.manage-button { width: 31px; height: 31px; justify-content: center; padding: 0; }.manage-button span { display: none; }.logout-button { padding: 7px; } }
@media (max-width: 390px) { .main-nav { margin-left: 7px; }.nav-item { gap: 0; padding: 0 5px; font-size: 0; }.nav-item svg { width: 16px; height: 16px; } }
</style>
