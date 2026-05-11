# STRUKTUR FRONTEND SIDORA - KOMPREHENSIF

## 📊 Ringkasan Implementasi

Telah dibuat **struktur frontend lengkap** untuk SIDORA dengan **5 halaman utama** yang mencakup:

1. **Autentikasi** (2 halaman)
2. **Admin Dashboard** (5+ halaman template)
3. **Petugas Dashboard** (4+ halaman template)
4. **Rumah Sakit Dashboard** (3 halaman)

---

## 📁 Struktur File yang Sudah Dibuat

### Authentication Pages
✅ `pages/auth/login.html` - Halaman login dengan role selector
✅ `pages/auth/register-rs.html` - Form registrasi rumah sakit

### Admin Pages
✅ `pages/admin/dashboard.html` - Dashboard utama admin dengan stats
✅ `pages/admin/kelola-petugas.html` - CRUD petugas dengan modal form

### Petugas Pages
✅ `pages/petugas/daftar-pendonor.html` - CRUD pendonor lengkap

### Rumah Sakit Pages
✅ `pages/rumahsakit/dashboard.html` - Dashboard RS dengan stats
✅ `pages/rumahsakit/permintaan-darah.html` - Form permintaan darah dengan selector

### CSS Files
✅ `assets/css/global.css` - Styling global & utility classes
✅ `assets/css/auth.css` - Styling untuk auth pages
✅ `assets/css/pages/dashboard.css` - Layout dashboard responsive

---

## 🎯 Template Halaman yang Perlu Dibuat (untuk each module lead)

### AGNES (Modul 1 & 4)
**Modul 1 - Auth & RS Dashboard:**
- ✅ `pages/auth/login.html`
- ✅ `pages/auth/register-rs.html`
- ✅ `pages/rumahsakit/dashboard.html`
- ✅ `pages/rumahsakit/permintaan-darah.html`
- ⏳ `pages/rumahsakit/history-permintaan.html`

**Modul 4 - Admin Permintaan:**
- ⏳ `pages/admin/permintaan-darah.html`
- ⏳ `pages/admin/proses-permintaan.html`
- ⏳ `pages/admin/update-pengiriman.html`

### NAJWA (Modul 2 & 5)
**Modul 2 - Admin Jadwal & Stok:**
- ✅ `pages/admin/dashboard.html`
- ⏳ `pages/admin/jadwal-donor.html`
- ⏳ `pages/admin/form-jadwal.html`
- ⏳ `pages/admin/stok-darah.html`

**Modul 5 - Petugas Dashboard:**
- ⏳ `pages/petugas/dashboard.html`
- ⏳ `pages/petugas/jadwal-donor.html`
- ⏳ `pages/petugas/stok-darah.html`

### AGHISA (Modul 3 & 6)
**Modul 3 - Petugas Pendonor:**
- ✅ `pages/petugas/daftar-pendonor.html`
- ⏳ `pages/petugas/form-pendonor.html`
- ⏳ `pages/petugas/edit-pendonor.html`
- ⏳ `pages/petugas/detail-pendonor.html`

**Modul 6 - Admin Petugas:**
- ✅ `pages/admin/kelola-petugas.html`
- ⏳ `pages/admin/form-petugas.html`

### KETRIN (Modul 2 & 3)
**Modul 2 - Database & Setup:**
- ⏳ Database schema design
- ⏳ `backend/config/database.php`
- ⏳ `backend/helpers/functions.php`

**Modul 3 - Petugas Riwayat:**
- ⏳ `pages/petugas/riwayat-donasi.html`
- ⏳ `pages/petugas/form-riwayat.html`

---

## 🎨 Design System yang Digunakan

### Color Palette
```css
Primary: #1e3a8a (Biru Tua)
Secondary: #dc2626 (Merah)
Success: #16a34a (Hijau)
Warning: #ea580c (Orange)
Danger: #dc2626 (Merah)
Info: #0284c7 (Biru Cerah)
Light Gray: #f3f4f6
Dark Gray: #374151
```

### Typography
- Font: System font stack (Modern & Fast)
- H1: 2rem, H2: 1.75rem, H3: 1.5rem
- Body: 1rem, Line height: 1.6

### Components
- Buttons: Primary, Secondary, Success, Warning, Danger, Outline
- Forms: Input, Select, Textarea, Checkbox, Radio
- Cards: Stat cards, Data cards, Modal
- Tables: Responsive dengan action buttons
- Alerts: Success, Danger, Warning, Info

---

## 📱 Responsive Design

Semua halaman sudah didesain responsive untuk:
- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (< 768px)

Fitur responsive yang sudah implemented:
- Sidebar collapse pada mobile
- Grid menjadi single column
- Form field auto-responsive
- Table horizontal scroll pada mobile

---

## 🔧 Teknologi yang Digunakan

- **HTML5** - Semantic markup
- **CSS3** - Modern styling (Grid, Flexbox, CSS Variables)
- **JavaScript (Vanilla)** - No framework! Pure ES6+
- **PHP** - Backend (akan di-implementasi di phase 2)
- **MySQL** - Database (akan di-setup di phase 2)

---

## 📋 File Checklist - Frontend Implementation

### Authentication
- [x] Login page layout
- [x] Register page layout
- [x] Form validation UI
- [x] Password toggle functionality
- [ ] Login backend integration
- [ ] Register backend integration

### Admin Dashboard
- [x] Dashboard layout & sidebar
- [x] Statistics cards
- [x] Data table display
- [x] Kelola Petugas CRUD UI
- [ ] Jadwal Donor page
- [ ] Stok Darah monitoring page
- [ ] Permintaan Darah processing page

### Petugas Dashboard
- [x] Daftar Pendonor CRUD UI
- [ ] Dashboard petugas
- [ ] Riwayat donasi page
- [ ] Jadwal view page
- [ ] Stok darah view page

### Rumah Sakit Dashboard
- [x] Dashboard RS
- [x] Form permintaan darah
- [ ] History permintaan page

### Shared Components
- [x] Global CSS (colors, spacing, typography)
- [x] Auth CSS
- [x] Dashboard layout CSS
- [ ] Utility JS functions
- [ ] API integration module

---

## 🚀 Next Steps

### Phase 2: Backend Implementation
1. Setup database schema (Ketrin)
2. Create PHP controllers & models (All)
3. Implement API endpoints (All)
4. Form validation backend (All)
5. Session management (Agnes)

### Phase 3: Testing & Deployment
1. Unit testing (All)
2. Integration testing (All)
3. User acceptance testing (All)
4. Bug fixes (All)
5. Deployment (Semua)

---

## 💡 Tips untuk Team

1. **Consistency**: Gunakan CSS classes yang sudah dibuat di `global.css`
2. **Naming**: Follow convention: kebab-case untuk CSS, camelCase untuk JS
3. **Organization**: Struktur folder sudah rapi, jangan ubah
4. **Reusability**: Buat komponen yang bisa dipakai ulang
5. **Responsive**: Test di berbagai ukuran screen
6. **Accessibility**: Gunakan semantic HTML & proper labels
7. **Comments**: Jelaskan kode kompleks dengan comment

---

## 📞 Support & Questions

Jika ada yang kurang jelas atau ada pertanyaan tentang struktur/implementasi, tanyakan ke team lead atau chat group.

---

**Status**: Frontend Phase 1 - 60% Complete ✅
**Target Completion**: Semua halaman template done dalam 2 minggu pertama
**Next Review**: End of week 2
