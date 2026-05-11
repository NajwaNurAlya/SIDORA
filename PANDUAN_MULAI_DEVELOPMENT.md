# 🎯 SIDORA - QUICK START GUIDE

## Selamat Datang! 👋

Ini adalah panduan cepat untuk memulai development SIDORA. Semua template frontend sudah disiapkan, sekarang giliran kalian untuk melanjutkan!

---

## 📦 Apa Yang Sudah Siap?

### ✅ Frontend Foundation
```
✓ Global CSS dengan color scheme & typography
✓ 5+ template halaman utama
✓ Dashboard layout responsive
✓ Auth pages (login & register)
✓ Form components dengan styling
✓ Table components dengan CRUD
✓ Sidebar navigation
✓ Modal dialogs
```

### ✅ Dokumentasi Lengkap
```
✓ PEMBAGIAN_TUGAS_FRONTEND.md - Siapa kerja apa
✓ STRUKTUR_FRONTEND_LENGKAP.md - Daftar halaman
✓ STRUKTUR_FOLDER.md - Organisasi project
✓ API_DOCUMENTATION.md - API spec (kosong, perlu dibuat)
✓ HTML_CSS_PHP_WORKFLOW.md - Cara kerja tim
```

### ✅ Struktur Folder
```
pages/
  ├─ auth/          (Login, Register)
  ├─ admin/         (Dashboard & management pages)
  ├─ petugas/       (Petugas pages)
  └─ rumahsakit/    (RS pages)

assets/
  ├─ css/
  │  ├─ global.css
  │  ├─ auth.css
  │  └─ pages/      (Specific page CSS)
  ├─ js/
  │  ├─ api.js      (API calls)
  │  ├─ validation.js (Form validation)
  │  └─ pages/      (Page-specific JS)
  └─ images/
     └─ icons/

backend/
  ├─ config/        (Database config)
  ├─ controllers/    (Business logic)
  ├─ models/        (Database queries)
  └─ helpers/       (Utility functions)

database/
  └─ schema.sql     (Database structure)
```

---

## 🚀 Cara Memulai

### 1. Setup Project

```bash
# Navigate ke project folder
cd "c:\Users\LENOVO\Downloads\SIDORA TES"

# Buka di VS Code
code .
```

### 2. Preview Halaman

Buka file HTML di browser:
- **Login**: `pages/auth/login.html`
- **Admin Dashboard**: `pages/admin/dashboard.html`
- **Kelola Petugas**: `pages/admin/kelola-petugas.html`
- **Daftar Pendonor**: `pages/petugas/daftar-pendonor.html`
- **RS Dashboard**: `pages/rumahsakit/dashboard.html`
- **Permintaan Darah**: `pages/rumahsakit/permintaan-darah.html`

### 3. Mulai Development

Setiap orang ambil module mereka sesuai pembagian di `PEMBAGIAN_TUGAS_FRONTEND.md`

---

## 📋 Task Checklist

### Week 1: Selesaikan Semua Halaman HTML/CSS

**Agnes (Modul 1 & 4):**
- [ ] Selesaikan halaman permintaan darah dari RS (sudah ada template)
- [ ] Buat halaman history permintaan RS
- [ ] Buat halaman lihat permintaan untuk Admin
- [ ] Buat halaman proses permintaan untuk Admin
- [ ] Buat halaman update pengiriman untuk Admin

**Najwa (Modul 2 & 5):**
- [ ] Buat halaman jadwal donor untuk Admin
- [ ] Buat halaman form jadwal donor
- [ ] Buat halaman stok darah untuk Admin
- [ ] Buat dashboard untuk Petugas
- [ ] Buat halaman jadwal view untuk Petugas
- [ ] Buat halaman stok darah view untuk Petugas

**Aghisa (Modul 3 & 6):**
- [ ] Buat halaman form pendonor (add/edit)
- [ ] Buat halaman detail pendonor
- [ ] Buat halaman kelola petugas (sudah ada template)
- [ ] Buat halaman form petugas
- [ ] Setup CSS khusus untuk halaman Aghisa

**Ketrin (Database & Riwayat):**
- [ ] Design database schema
- [ ] Buat halaman riwayat donasi
- [ ] Buat halaman form riwayat donasi
- [ ] Setup `backend/config/database.php`
- [ ] Setup `backend/helpers/functions.php`

---

## 🎨 CSS Convention yang Harus Diikuti

### Menggunakan Class yang Sudah Ada

```html
<!-- ✅ BENAR - Gunakan global CSS -->
<button class="btn btn-primary">Click Me</button>
<div class="card p-3">Content</div>
<h1 class="text-center mb-3">Title</h1>

<!-- ❌ SALAH - Jangan buat inline style -->
<button style="background: blue; padding: 10px;">Click Me</button>
```

### Membuat Custom CSS

Jika perlu custom CSS:

```css
/* pages/admin/jadwal-donor.css */
.jadwal-card {
  border-left: 4px solid var(--primary-color);
  padding: var(--spacing-md);
}

.jadwal-time {
  color: var(--primary-color);
  font-weight: 600;
}
```

Kemudian include di HTML:
```html
<link rel="stylesheet" href="../../assets/css/pages/jadwal-donor.css">
```

---

## 💻 HTML Structure Template

Untuk halaman baru, gunakan structure ini:

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title - SIDORA</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/pages/dashboard.css">
    <!-- Add custom CSS jika perlu -->
    <link rel="stylesheet" href="../../assets/css/pages/custom.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Navbar, Sidebar, Main Content -->
    </div>

    <!-- Scripts -->
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/pages/custom.js"></script>
</body>
</html>
```

---

## 🔌 JavaScript Tips

### Event Handling

```javascript
// ✅ BENAR - Gunakan addEventListener
document.getElementById('button').addEventListener('click', function() {
    console.log('Clicked!');
});

// ❌ SALAH - Jangan gunakan onclick di HTML
// <button onclick="doSomething()">Click</button>
```

### Form Validation

```javascript
// Use built-in HTML5 validation
<input type="email" required>
<input type="number" min="1" max="100">

// Atau gunakan custom validation
document.getElementById('form').addEventListener('submit', function(e) {
    e.preventDefault();
    // Validation logic
});
```

---

## 📱 Testing Halaman

Setiap halaman harus ditest di:

- [ ] Desktop (1920x1080, 1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667, 414x896)

Gunakan DevTools di Chrome:
```
F12 → Toggle Device Toolbar (Ctrl+Shift+M)
```

---

## 📚 Reference Files

### CSS Classes Available

```css
/* Colors */
.text-primary, .text-secondary, .text-success, .text-danger, .text-warning

/* Spacing */
.mt-1, .mt-2, .mt-3, .mt-4
.mb-1, .mb-2, .mb-3, .mb-4
.p-1, .p-2, .p-3, .p-4

/* Typography */
.text-center, .text-right, .text-left
.font-bold, .font-semibold, .text-sm, .text-lg

/* Layout */
.d-flex, .d-grid, .d-none, .d-block
.justify-between, .justify-center
.align-center, .align-start

/* Buttons */
.btn, .btn-primary, .btn-secondary, .btn-outline, .btn-small

/* Cards & Containers */
.card, .card-header, .card-body, .card-footer
.container, .table-container, .form-section
```

Lihat lengkap di `assets/css/global.css`

---

## 🐛 Common Issues & Solutions

### Issue: Styling tidak berubah
**Solusi**: Clear browser cache (Ctrl+F5) atau hard refresh

### Issue: Layout berantakan di mobile
**Solusi**: Gunakan DevTools untuk test responsive, pastikan flex/grid proper

### Issue: Form input tidak bisa dikirim
**Solusi**: Pastikan ada `type="submit"` pada button dan form punya `id`

### Issue: JavaScript error di console
**Solusi**: Buka DevTools (F12), baca error message, cek file path dan syntax

---

## 📞 Contact & Support

### Jika Ada Pertanyaan:
1. **Lihat dokumentasi** di folder `/docs`
2. **Check CSS** di `/assets/css/global.css`
3. **Tanya ke team** di chat group
4. **Lihat contoh** di halaman yang sudah ada

### Review Code:
- Commit ke branch sendiri
- Buat Pull Request
- Minta review dari team

---

## ⏱️ Timeline

### Week 1 (Apr 25 - May 1)
✅ **Frontend HTML/CSS 100%** - Semua halaman template selesai

### Week 2 (May 2 - May 8)
🔄 **Frontend + Backend Integration** - Connect semua halaman dengan API

### Week 3-4 (May 9 - May 22)
🔧 **Testing & Refinement** - Bug fix, optimize, polish

### Week 5 (May 23-29)
🚀 **Final Deployment** - Go live!

---

## ✨ Best Practices

1. **Commit Often** - Jangan tunggu sampai selesai semua
2. **Write Clean Code** - Gunakan consistent naming
3. **Comment Complex Code** - Jelaskan logic yang kompleks
4. **Test Responsiveness** - Setiap halaman harus responsive
5. **Use Semantic HTML** - Use proper tags (button, form, etc)
6. **Follow Conventions** - Ikuti style yang sudah ada
7. **Ask for Help** - Jangan ragu bertanya

---

## 🎉 You're All Set!

Semua yang kalian butuhkan sudah ada. Sekarang tinggal:

1. ✅ Ambil module masing-masing
2. ✅ Buat halaman yang perlu
3. ✅ Test responsiveness
4. ✅ Commit & push
5. ✅ Minta review dari team

**Selamat mengoding! 💪**

---

*Last Updated: April 25, 2024*
*Version: 1.0 - Frontend Template Complete*
