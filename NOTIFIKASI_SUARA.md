# 🔔 Notifikasi Suara Pesanan Baru

## Fitur yang Ditambahkan

Sistem notifikasi suara otomatis ketika ada pesanan baru masuk ke dashboard admin.

## Cara Kerja

1. **Auto-Check Setiap 10 Detik**
   - Sistem otomatis mengecek pesanan baru setiap 10 detik
   - Endpoint: `/admin/orders/notifications`

2. **Notifikasi Multi-Channel**
   Ketika ada pesanan baru, sistem akan:
   - ✅ **Memutar suara "DING-DONG"** (Web Audio API)
   - ✅ **Menampilkan browser notification**
   - ✅ **Menampilkan toast notification** (popup di kanan atas)
   - ✅ **Animasi card pesanan baru** (berkedip ungu)

3. **Suara Notifikasi**
   - Menggunakan Web Audio API (tidak perlu file MP3)
   - Suara "DING-DONG" dengan 2 nada (880Hz dan 660Hz)
   - Volume: 40% dan 35%
   - Durasi: ~0.6 detik

## Cara Test

### 1. Test Manual di Dashboard
1. Buka dashboard admin: `/admin/dashboard`
2. Klik tombol **"Test Suara"** di bagian bawah
3. Harus terdengar suara "DING-DONG"
4. Muncul toast notification di kanan atas
5. Card "Pesanan Baru" berkedip ungu

### 2. Test dengan Pesanan Real
1. Buka halaman customer: `/menu`
2. Pesan menu apapun
3. Kembali ke dashboard admin
4. Tunggu maksimal 10 detik
5. Suara notifikasi akan berbunyi otomatis

## Troubleshooting

### Suara Tidak Berbunyi?

**Penyebab 1: Browser Autoplay Policy**
- Browser modern memblokir audio otomatis
- **Solusi**: Klik di halaman dashboard dulu (anywhere)
- Setelah user interaction, audio akan berfungsi

**Penyebab 2: Browser Notification Permission**
- Browser notification belum diizinkan
- **Solusi**: Klik "Allow" ketika browser meminta permission

**Penyebab 3: Volume Browser/System Muted**
- Cek volume browser dan system
- **Solusi**: Unmute dan naikkan volume

### Debug Console
Buka browser console (F12) untuk melihat log:
```
🔔 Notification system ready!
Current pending orders: 0
Checking for new orders every 10 seconds...
```

Ketika ada pesanan baru:
```
🔔🔔🔔 PESANAN BARU MASUK! 🔔🔔🔔
Jumlah pesanan baru: 1
Total pending sekarang: 1
Total pending sebelumnya: 0
🔔 NOTIFICATION SOUND PLAYED!
```

## File yang Dimodifikasi

1. **resources/views/admin/dashboard.blade.php**
   - Tambah fungsi `playNotificationSound()`
   - Tambah fungsi `checkNewOrders()`
   - Tambah fungsi `showToastNotification()`
   - Tambah fungsi `animateNewOrderCard()`
   - Tambah tombol test notifikasi

2. **app/Http/Controllers/AdminOrderController.php**
   - Method `getNotifications()` sudah ada (tidak perlu diubah)

3. **routes/web.php**
   - Route `/admin/orders/notifications` sudah ada (tidak perlu diubah)

## Konfigurasi

### Ubah Interval Check (Default: 10 detik)
```javascript
// Di dashboard.blade.php, cari:
setInterval(checkNewOrders, 10000); // 10000 = 10 detik

// Ubah menjadi (contoh: 5 detik):
setInterval(checkNewOrders, 5000);
```

### Ubah Suara Notifikasi
```javascript
// Di function playDingDong(), ubah frequency:
osc1.frequency.value = 880; // Nada pertama (A5)
osc2.frequency.value = 660; // Nada kedua (E5)

// Nada lebih tinggi = angka lebih besar
// Nada lebih rendah = angka lebih kecil
```

### Ubah Volume
```javascript
// Di function playDingDong(), ubah gain:
gain1.gain.setValueAtTime(0.4, now); // 0.4 = 40% volume
gain2.gain.setValueAtTime(0.35, now2); // 0.35 = 35% volume

// Range: 0.0 (mute) - 1.0 (100%)
```

## Status

✅ **SELESAI** - Notifikasi suara sudah berfungsi!

## Next Steps (Opsional)

- [ ] Tambah pilihan suara notifikasi (bell, chime, etc)
- [ ] Tambah toggle on/off notifikasi suara
- [ ] Tambah volume control slider
- [ ] Simpan preferensi notifikasi di localStorage
- [ ] Tambah notifikasi untuk status lain (processing, completed)
