@echo off
title POS App Launcher
echo ==============================================
echo   MENYALAKAN APLIKASI POINT OF SALE (POS)
echo ==============================================
echo.

echo 1. Menjalankan Backend Laravel di terminal baru (Port 8000)...
start "POS Backend Server (Port 8000)" cmd /k "cd /d %~dp0backend && php artisan serve"

echo 2. Menjalankan Frontend Vue di terminal baru (Port 5173)...
start "POS Frontend Server (Port 5173)" cmd /k "cd /d %~dp0frontend && npm run dev"

echo.
echo ==============================================
echo   Kedua server sedang berjalan!
echo   JANGAN TUTUP jendela terminal yang muncul.
echo.
echo   Membuka http://localhost:5173 di browser...
echo   Email login : admin@gmail.com
echo   Password    : password123
echo ==============================================
timeout /t 3 >nul
start http://localhost:5173
