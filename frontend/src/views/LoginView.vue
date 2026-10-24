<template>
  <main class="login-page">
    <section class="login-showcase">
      <div class="showcase-top">
        <div class="brand-mark" aria-hidden="true">S</div>
        <span>STOK<span class="brand-light">KASIR</span></span>
      </div>

      <div class="showcase-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> SISTEM MANAJEMEN TOKO</span>
        <h1>Bisnis rapi,<br />hari lebih <em>pasti.</em></h1>
        <p>Kelola produk dan stok tokomu dengan lebih mudah. Semua yang kamu butuhkan, ada di satu tempat.</p>
      </div>

      <div class="showcase-art" aria-hidden="true">
        <div class="art-orbit orbit-one"></div>
        <div class="art-orbit orbit-two"></div>
        <div class="art-card">
          <div class="art-card-top"><span class="art-dot"></span><span class="art-dot"></span><span class="art-dot"></span></div>
          <div class="art-chart-label">Ringkasan penjualan</div>
          <div class="art-chart-value">Rp 4.280.000 <span>+12,8%</span></div>
          <div class="art-chart-bars"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
          <div class="art-chart-days"><span>Sen</span><span>Rab</span><span>Jum</span><span>Min</span></div>
        </div>
        <div class="art-note"><span class="note-icon">✓</span><span><b>Stok terpantau</b><small>Semua produk tersimpan</small></span></div>
        <div class="art-sparkle sparkle-one">✳</div>
        <div class="art-sparkle sparkle-two">✳</div>
      </div>

      <div class="showcase-footer"><span>MUDAH DIGUNAKAN</span><span class="footer-line"></span><span>DIBUAT UNTUK USAHA LOKAL</span></div>
    </section>

    <section class="login-panel">
      <div class="login-card">
        <div class="mobile-brand"><div class="brand-mark" aria-hidden="true">S</div><span>STOK<span class="brand-light">KASIR</span></span></div>
        <div class="login-heading">
          <span class="login-kicker">SELAMAT DATANG KEMBALI</span>
          <h2>Masuk ke akunmu</h2>
          <p>Masukkan detail akun untuk melanjutkan.</p>
        </div>

        <form class="login-form" @submit.prevent="handleLogin">
          <label for="email">Email</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2.5"/><path d="m4.5 7 7.5 5.5L19.5 7"/></svg>
            <input id="email" v-model="email" type="email" placeholder="nama@email.com" autocomplete="email" required />
          </div>

          <div class="password-label"><label for="password">Kata sandi</label></div>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4.5" y="10" width="15" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
            <input id="password" v-model="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required />
          </div>

          <p v-if="errorMessage" class="login-error" role="alert">{{ errorMessage }}</p>
          <button class="login-button" type="submit" :disabled="isLoading">
            <span>{{ isLoading ? 'Memproses...' : 'Masuk ke dashboard' }}</span>
            <svg v-if="!isLoading" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            <span v-else class="button-spinner" aria-hidden="true"></span>
          </button>
        </form>

        <div class="login-secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.9 8.1 7 10 4.1-1.9 7-5.4 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg> Akses aman untuk akun tokomu</div>
      </div>
      <footer class="login-copyright">© 2026 StokKasir <span>•</span> Bantu toko tumbuh setiap hari</footer>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)
const router = useRouter()

const handleLogin = async () => {
  if (isLoading.value) return
  errorMessage.value = ''
  isLoading.value = true

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    localStorage.setItem('auth_token', response.data.access_token)
    router.push('/products')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login gagal, periksa email dan password.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.login-page { min-height: 100vh; display: grid; grid-template-columns: minmax(420px, 1fr) minmax(480px, 0.92fr); background: #fff; }
.login-showcase { position: relative; display: flex; min-height: 100vh; flex-direction: column; overflow: hidden; padding: 38px clamp(36px, 6vw, 88px) 30px; color: #eff8f1; background: #123a2c; }
.login-showcase::before { position: absolute; top: -260px; right: -240px; width: 660px; height: 660px; border: 1px solid #ffffff12; border-radius: 50%; content: ''; }
.login-showcase::after { position: absolute; right: -125px; bottom: -260px; width: 530px; height: 530px; border: 1px solid #ffffff12; border-radius: 50%; content: ''; }
.showcase-top, .mobile-brand { position: relative; z-index: 1; display: flex; align-items: center; gap: 11px; color: #f8fff9; font-size: 15px; font-weight: 800; letter-spacing: 1.5px; }
.brand-mark { display: grid; width: 36px; height: 36px; place-items: center; border-radius: 12px; color: #123a2c; background: #b7e2c2; font-family: Georgia, serif; font-size: 23px; font-style: italic; font-weight: 700; letter-spacing: 0; }
.brand-light { color: #91c6a0; font-weight: 500; }
.showcase-copy { position: relative; z-index: 1; max-width: 520px; margin-top: clamp(68px, 10vh, 112px); }
.eyebrow, .login-kicker { display: inline-flex; align-items: center; gap: 9px; color: #a7ceb0; font-size: 10px; font-weight: 700; letter-spacing: 1.7px; }
.eyebrow-dot { width: 7px; height: 7px; border-radius: 50%; background: #a8d8b4; box-shadow: 0 0 0 4px #a8d8b421; }
.showcase-copy h1 { margin: 20px 0 17px; color: #f5fff6; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(48px, 5.8vw, 76px); font-weight: 400; letter-spacing: -2.8px; line-height: 1.04; }
.showcase-copy h1 em { color: #a8d8b4; font-weight: 400; }
.showcase-copy p { max-width: 390px; margin: 0; color: #c0d2c4; font-size: 14px; line-height: 1.8; }
.showcase-art { position: relative; z-index: 1; flex: 1; min-height: 280px; margin-top: 14px; }
.art-orbit { position: absolute; top: 24px; left: 13%; width: 320px; height: 230px; border: 1px solid #ffffff14; border-radius: 50%; transform: rotate(-16deg); }
.orbit-two { top: 53px; left: 8%; width: 390px; height: 200px; transform: rotate(18deg); }
.art-card { position: absolute; top: 48px; left: 16%; width: min(350px, 77%); padding: 18px 20px 14px; border: 1px solid #ffffff26; border-radius: 15px; background: linear-gradient(145deg, #ffffff16, #ffffff09); box-shadow: 0 22px 55px #09271d55; transform: rotate(-4deg); backdrop-filter: blur(10px); }
.art-card-top { display: flex; gap: 5px; }.art-dot { width: 5px; height: 5px; border-radius: 50%; background: #b8dec0; opacity: .6; }
.art-chart-label { margin-top: 16px; color: #bbd0c0; font-size: 10px; }.art-chart-value { margin-top: 3px; color: #f5fff6; font-size: 21px; font-weight: 700; }.art-chart-value span { margin-left: 6px; color: #a8d8b4; font-size: 10px; font-weight: 600; }
.art-chart-bars { display: flex; height: 70px; align-items: end; gap: 8px; margin-top: 12px; border-bottom: 1px solid #ffffff20; }.art-chart-bars i { flex: 1; height: 34%; border-radius: 4px 4px 0 0; background: #86bc96; opacity: .65; }.art-chart-bars i:nth-child(3n) { height: 64%; }.art-chart-bars i:nth-child(4n) { height: 49%; }.art-chart-bars i:nth-child(5n) { height: 85%; background: #b0dcba; opacity: .95; }
.art-chart-days { display: flex; justify-content: space-between; padding-top: 6px; color: #b7c9ba; font-size: 8px; }
.art-note { position: absolute; top: 168px; right: 4%; display: flex; align-items: center; gap: 10px; padding: 11px 15px 11px 11px; border: 1px solid #ffffff2b; border-radius: 12px; background: #285540; box-shadow: 0 10px 30px #09271d55; transform: rotate(4deg); }.note-icon { display: grid; width: 28px; height: 28px; place-items: center; border-radius: 9px; color: #17422f; background: #b8e2c1; font-size: 15px; font-weight: 800; }.art-note b, .art-note small { display: block; }.art-note b { color: white; font-size: 10px; }.art-note small { margin-top: 2px; color: #bed2c2; font-size: 8px; }
.art-sparkle { position: absolute; color: #a8d8b4; }.sparkle-one { top: 14px; right: 21%; font-size: 25px; }.sparkle-two { bottom: 36px; left: 9%; font-size: 14px; opacity: .6; }
.showcase-footer { position: relative; z-index: 1; display: flex; align-items: center; gap: 13px; color: #9bb5a1; font-size: 8px; font-weight: 700; letter-spacing: 1.25px; }.footer-line { width: 30px; height: 1px; background: #86ae91; }
.login-panel { display: flex; min-height: 100vh; flex-direction: column; align-items: center; justify-content: center; padding: 52px 36px 22px; background: #fff; }.login-card { width: min(100%, 390px); }.mobile-brand { display: none; color: #18392b; }
.login-heading { margin-bottom: 34px; }.login-kicker { color: #6b9579; font-size: 9px; }.login-heading h2 { margin: 12px 0 6px; color: #192820; font-family: Georgia, 'Times New Roman', serif; font-size: 37px; font-weight: 400; letter-spacing: -.8px; }.login-heading p { margin: 0; color: #828b84; font-size: 13px; }
.login-form { display: flex; flex-direction: column; }.login-form label { margin-bottom: 8px; color: #35453a; font-size: 11px; font-weight: 650; }.input-wrap { display: flex; height: 49px; align-items: center; gap: 11px; padding: 0 14px; border: 1px solid #e4e9e4; border-radius: 8px; background: #fff; transition: border-color .2s, box-shadow .2s; }.input-wrap:focus-within { border-color: #5f9b76; box-shadow: 0 0 0 3px #5f9b7618; }.input-wrap svg { width: 17px; height: 17px; flex: none; fill: none; stroke: #92a097; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.6; }.input-wrap input { width: 100%; min-width: 0; border: 0; outline: 0; color: #26342b; background: transparent; font-size: 12px; }.input-wrap input::placeholder { color: #adb5af; }.input-wrap:focus-within:has(input) { outline: none; }.password-label { margin-top: 19px; }.password-label label { display: block; }
.login-error { margin: 14px 0 0; padding: 10px 12px; border: 1px solid #f3d0cc; border-radius: 7px; color: #a23e35; background: #fff5f4; font-size: 11px; }
.login-button { display: flex; height: 49px; align-items: center; justify-content: center; gap: 10px; margin-top: 24px; border: 0; border-radius: 8px; color: #fff; background: #176b4d; box-shadow: 0 7px 14px #176b4d24; font-size: 12px; font-weight: 650; transition: background .2s, transform .2s, box-shadow .2s; }.login-button:hover:not(:disabled) { background: #10543b; box-shadow: 0 9px 18px #176b4d30; transform: translateY(-1px); }.login-button:disabled { cursor: wait; opacity: .75; }.login-button svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.7; }.button-spinner { width: 14px; height: 14px; border: 2px solid #ffffff70; border-top-color: white; border-radius: 50%; animation: spin .7s linear infinite; }
.login-secure { display: flex; align-items: center; justify-content: center; gap: 7px; margin-top: 25px; color: #929c94; font-size: 10px; }.login-secure svg { width: 15px; height: 15px; fill: none; stroke: #79a387; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }
.login-copyright { margin-top: auto; padding-top: 40px; color: #a4aaa5; font-size: 9px; }.login-copyright span { padding: 0 5px; color: #71a07d; }
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 900px) { .login-page { grid-template-columns: minmax(340px, .9fr) minmax(420px, 1.1fr); }.login-showcase { padding-right: 40px; padding-left: 40px; }.art-note { right: -2%; } }
@media (max-width: 700px) { .login-page { display: block; min-height: 100vh; background: #fff; }.login-showcase { display: none; }.login-panel { min-height: 100vh; justify-content: flex-start; padding: 30px 24px 20px; }.login-card { width: min(100%, 410px); margin: auto 0; }.mobile-brand { display: flex; margin-bottom: 52px; }.login-heading h2 { font-size: 34px; }.login-copyright { padding-top: 38px; text-align: center; } }
@media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; } }
</style>
