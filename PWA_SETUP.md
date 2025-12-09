# 🚀 PWA Setup Guide - Furawa Cafe Admin

Progressive Web App (PWA) untuk Admin Panel Furawa Cafe sudah siap!

## ✅ File yang Sudah Dibuat

1. **`public/manifest.json`** - Konfigurasi PWA
2. **`public/service-worker.js`** - Service Worker untuk offline & caching
3. **`public/pwa-init.js`** - Script inisialisasi PWA
4. **`public/offline.html`** - Halaman offline fallback
5. **`public/generate-pwa-icons.php`** - Generator icon PWA
6. **`resources/views/layouts/admin.blade.php`** - Updated dengan PWA meta tags

## 📋 Langkah-Langkah Setup

### 1. Generate Icon PWA

Jalankan command ini di terminal:

```bash
php public/generate-pwa-icons.php
```

Script ini akan membuat icon dalam berbagai ukuran (72x72 sampai 512x512) di folder `public/images/`.

### 2. Test di Localhost

1. Pastikan Laravel server berjalan:
   ```bash
   php artisan serve
   ```

2. Buka browser (Chrome/Edge):
   ```
   http://localhost:8000/admin
   ```

3. Login ke admin panel

4. Lihat icon **install** (⊕) di address bar

5. Klik untuk install PWA!

### 3. Test Fitur PWA

#### ✅ Install ke Home Screen
- Klik icon install di browser
- PWA akan ter-install seperti aplikasi native
- Bisa diakses dari Start Menu (Windows) atau Home Screen (Mobile)

#### ✅ Offline Support
- Buka PWA yang sudah ter-install
- Matikan internet/WiFi
- Refresh halaman
- Halaman masih bisa dibuka (dari cache)

#### ✅ Push Notifications
- Saat pertama buka, browser akan minta izin notifikasi
- Klik "Allow"
- Notifikasi pesanan baru akan muncul otomatis

#### ✅ Background Sync
- Data akan sync otomatis saat koneksi kembali
- Tidak perlu refresh manual

## 🔧 Konfigurasi Lanjutan

### Push Notifications (Opsional)

Untuk push notifications yang lebih advanced, install package:

```bash
composer require laravel-notification-channels/webpush
```

Generate VAPID keys:

```bash
php artisan webpush:vapid
```

Update `public/pwa-init.js` line 72 dengan VAPID public key yang di-generate.

### Custom Icon

Jika punya logo sendiri:

1. Siapkan logo PNG (minimal 512x512)
2. Gunakan tool online seperti [PWA Asset Generator](https://www.pwabuilder.com/)
3. Replace file di `public/images/`

## 📱 Test di Mobile

### Android (Chrome):
1. Buka `http://YOUR_IP:8000/admin` di Chrome mobile
2. Menu → "Add to Home screen"
3. Icon akan muncul di home screen

### iOS (Safari):
1. Buka di Safari
2. Tap Share button
3. "Add to Home Screen"

## 🌐 Deploy ke Production

### Requirements:
- ✅ HTTPS (SSL Certificate) - **WAJIB**
- ✅ Domain/subdomain
- ✅ Web server (Apache/Nginx)

### Steps:

1. **Upload semua file** ke hosting

2. **Pastikan HTTPS aktif**
   - Gunakan Let's Encrypt (gratis)
   - Atau SSL dari hosting provider

3. **Update manifest.json**
   ```json
   {
     "start_url": "https://yourdomain.com/admin/dashboard",
     "scope": "https://yourdomain.com/admin/"
   }
   ```

4. **Test PWA**
   - Buka https://yourdomain.com/admin
   - Install PWA
   - Test semua fitur

## 🎯 Fitur PWA yang Tersedia

### ✅ Sudah Aktif:
- [x] Install ke home screen
- [x] Offline support (cache halaman)
- [x] App-like experience (fullscreen)
- [x] Fast loading (cache assets)
- [x] Background sync
- [x] Push notifications (basic)
- [x] Update notification
- [x] Online/offline detection

### 🔄 Bisa Ditambahkan:
- [ ] Advanced push notifications (dengan backend)
- [ ] Periodic background sync
- [ ] Share target API
- [ ] Badge API (notification count)
- [ ] Shortcuts (quick actions)

## 🐛 Troubleshooting

### PWA tidak muncul icon install?
- Pastikan HTTPS aktif (atau localhost)
- Clear browser cache
- Check console untuk error
- Pastikan manifest.json valid

### Service Worker tidak register?
- Check console: `navigator.serviceWorker`
- Pastikan path `/service-worker.js` benar
- Clear browser cache & hard reload (Ctrl+Shift+R)

### Notifikasi tidak muncul?
- Check permission: `Notification.permission`
- Pastikan browser support notifications
- Test di Chrome/Edge (Firefox kadang bermasalah)

### Offline tidak work?
- Pastikan service worker ter-register
- Check cache di DevTools → Application → Cache Storage
- Pastikan halaman sudah pernah dibuka (untuk cache)

## 📊 Monitor PWA

### Chrome DevTools:
1. F12 → Application tab
2. Check:
   - Manifest
   - Service Workers
   - Cache Storage
   - Notifications

### Lighthouse Audit:
1. F12 → Lighthouse tab
2. Select "Progressive Web App"
3. Run audit
4. Check score & recommendations

## 🎉 Selesai!

PWA Furawa Cafe Admin sudah siap digunakan!

**Test Checklist:**
- [ ] Generate icons
- [ ] Test install di localhost
- [ ] Test offline mode
- [ ] Test notifications
- [ ] Test di mobile
- [ ] Deploy ke production
- [ ] Test di production

**Support:**
- Chrome: ✅ Full support
- Edge: ✅ Full support
- Firefox: ⚠️ Partial (no install prompt)
- Safari: ⚠️ Limited (iOS only)

---

**Happy Coding! 🚀**

Jika ada pertanyaan atau masalah, check console browser untuk error messages.
