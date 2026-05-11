# PEMBAGIAN TUGAS FRONTEND - SIDORA PROJECT

## Tim Pengembang
1. **Agnes Pusvita Sari** (NPM. 2417052022)
2. **Najwa Nur Alya** (NPM. 2417052028)
3. **Aghisa Christyananda** (NPM. 2457052004)
4. **Ketrin Pertiwi** (NPM. 2457052006)

---

## Strategi Pembagian Kerja

Setiap anggota tim akan mengerjakan MULTIPLE MODULES agar semua mendapat pengalaman di berbagai bagian (Frontend, Backend, Database).

### Sistem Penilaian Kontribusi
- **Frontend HTML/CSS**: 30%
- **Backend PHP Logic**: 40%
- **Database Design & Query**: 30%

---

## Pembagian Module

### AGNES PUSVITA SARI - Module 1 & 4
**Module 1: Authentication & Rumah Sakit Dashboard**
- Halaman Login (`pages/auth/login.html`)
- Halaman Registrasi Rumah Sakit (`pages/auth/register-rs.html`)
- Dashboard Rumah Sakit (`pages/rumahsakit/dashboard.html`)
- Form Permintaan Darah (`pages/rumahsakit/permintaan-darah.html`)
- History Permintaan Darah (`pages/rumahsakit/history-permintaan.html`)
- Styling CSS untuk auth & rumah sakit

**Module 4: Admin - Kelola Permintaan**
- Halaman Lihat Permintaan Darah (`pages/admin/permintaan-darah.html`)
- Form Proses Permintaan (Approve/Reject) (`pages/admin/proses-permintaan.html`)
- Update Status Pengiriman (`pages/admin/update-pengiriman.html`)
- Backend Logic untuk permintaan darah
- Database Queries untuk permintaan

---

### NAJWA NUR ALYA - Module 2 & 5
**Module 2: Admin Dashboard & Kelola Jadwal**
- Dashboard Admin (`pages/admin/dashboard.html`)
- Halaman Jadwal Donor (`pages/admin/jadwal-donor.html`)
- Form Buat Jadwal Donor (`pages/admin/form-jadwal.html`)
- Edit Jadwal Donor (`pages/admin/edit-jadwal.html`)
- Halaman Monitoring Stok Darah (`pages/admin/stok-darah.html`)
- Styling CSS untuk admin main

**Module 5: Petugas Dashboard**
- Dashboard Petugas (`pages/petugas/dashboard.html`)
- Lihat Jadwal Donor (`pages/petugas/jadwal-donor.html`)
- Lihat Stok Darah (`pages/petugas/stok-darah.html`)
- Backend logic petugas
- Database queries untuk jadwal & stok

---

### AGHISA CHRISTYANANDA - Module 3 & 6
**Module 3: Petugas - Kelola Data Pendonor**
- Halaman Daftar Pendonor (`pages/petugas/daftar-pendonor.html`)
- Form Tambah Pendonor (`pages/petugas/form-pendonor.html`)
- Edit Data Pendonor (`pages/petugas/edit-pendonor.html`)
- Detail Pendonor (`pages/petugas/detail-pendonor.html`)
- Styling CSS untuk petugas

**Module 6: Admin - Kelola Petugas & Layout**
- Layout Template (Navbar, Sidebar, Footer) (`backend/includes/`)
- Halaman Kelola Petugas (`pages/admin/kelola-petugas.html`)
- Form Tambah Petugas (`pages/admin/form-petugas.html`)
- Edit Petugas (`pages/admin/edit-petugas.html`)
- Global styling dan responsive design
- Backend logic untuk user management

---

### KETRIN PERTIWI - Module 2 & 3
**Module 2 (Parallel): Admin Dashboard & Database Design**
- Backup untuk halaman Admin (bantuan Najwa)
- Design Database Schema lengkap
- Setup Database Connection (`backend/config/database.php`)
- Helper Functions (`backend/helpers/functions.php`)

**Module 3 (Parallel): Petugas - Riwayat Donasi**
- Halaman Catat Riwayat Donasi (`pages/petugas/riwayat-donasi.html`)
- Form Input Riwayat Donasi (`pages/petugas/form-riwayat.html`)
- Backend logic riwayat donasi
- Database queries untuk riwayat donasi
- Backend logic untuk auto update stok darah

---

## Timeline Pengerjaan

### Phase 1: Frontend HTML/CSS (Week 1-2)
- Setiap module lead membuat template HTML/CSS halaman mereka
- Fokus pada layout, struktur semantik, responsive design
- Tidak ada backend logic di phase ini

### Phase 2: Backend Logic & Database (Week 3-4)
- Setiap module membuat backend PHP
- Setup database schema dan queries
- Integration dengan frontend

### Phase 3: Testing & Integration (Week 5)
- Test semua module
- Fix bug & integration issues
- Final deployment

---

## File Structure Reference

```
pages/
├── auth/
│   ├── login.html                 (Agnes)
│   └── register-rs.html           (Agnes)
├── admin/
│   ├── dashboard.html             (Najwa)
│   ├── jadwal-donor.html          (Najwa)
│   ├── form-jadwal.html           (Najwa)
│   ├── edit-jadwal.html           (Najwa)
│   ├── stok-darah.html            (Najwa)
│   ├── kelola-petugas.html        (Aghisa)
│   ├── form-petugas.html          (Aghisa)
│   ├── edit-petugas.html          (Aghisa)
│   ├── permintaan-darah.html      (Agnes)
│   ├── proses-permintaan.html     (Agnes)
│   └── update-pengiriman.html     (Agnes)
├── petugas/
│   ├── dashboard.html             (Najwa)
│   ├── daftar-pendonor.html       (Aghisa)
│   ├── form-pendonor.html         (Aghisa)
│   ├── edit-pendonor.html         (Aghisa)
│   ├── detail-pendonor.html       (Aghisa)
│   ├── riwayat-donasi.html        (Ketrin)
│   ├── form-riwayat.html          (Ketrin)
│   ├── jadwal-donor.html          (Najwa)
│   └── stok-darah.html            (Najwa)
└── rumahsakit/
    ├── dashboard.html             (Agnes)
    ├── permintaan-darah.html      (Agnes)
    └── history-permintaan.html    (Agnes)

assets/
├── css/
│   ├── global.css                 (Ketrin - shared)
│   ├── auth.css                   (Agnes)
│   ├── pages/
│   │   ├── admin.css              (Najwa)
│   │   ├── petugas.css            (Aghisa)
│   │   └── rumahsakit.css         (Agnes)
├── js/
│   ├── api.js
│   ├── main.js
│   ├── validation.js
│   └── pages/
│       ├── admin.js
│       ├── petugas.js
│       └── rumahsakit.js
└── images/
    └── icons/

backend/
├── config/
│   ├── database.php               (Ketrin)
│   └── session.php
├── controllers/
│   ├── auth.php                   (Agnes)
│   ├── petugas.php                (Aghisa & Ketrin)
│   ├── admin.php                  (Najwa & Aghisa)
│   └── rumahsakit.php             (Agnes)
├── models/                        (All - Database queries)
│   ├── User.php
│   ├── Pendonor.php
│   ├── StokDarah.php
│   ├── JadwalDonor.php
│   └── PermintaanDarah.php
└── helpers/
    └── functions.php              (Ketrin)
```

---

## Checklist Deliverables

### Frontend HTML/CSS
- [ ] Login page (Agnes)
- [ ] Register RS page (Agnes)
- [ ] Admin dashboard (Najwa)
- [ ] Admin - Kelola Jadwal (Najwa)
- [ ] Admin - Monitor Stok (Najwa)
- [ ] Admin - Kelola Petugas (Aghisa)
- [ ] Admin - Kelola Permintaan (Agnes)
- [ ] Petugas dashboard (Najwa)
- [ ] Petugas - Daftar Pendonor (Aghisa)
- [ ] Petugas - Riwayat Donasi (Ketrin)
- [ ] Petugas - Jadwal & Stok (Najwa)
- [ ] RS dashboard (Agnes)
- [ ] RS - Permintaan Darah (Agnes)
- [ ] RS - History Permintaan (Agnes)

### Backend & Database
- [ ] Database Schema (Ketrin)
- [ ] Database Connection (Ketrin)
- [ ] Helper Functions (Ketrin)
- [ ] Auth Controllers (Agnes)
- [ ] Admin Controllers (Najwa & Aghisa)
- [ ] Petugas Controllers (Aghisa & Ketrin)
- [ ] RS Controllers (Agnes)
- [ ] All Database Models (All)

---

## Collaboration Guidelines

1. **Git Workflow**
   - Setiap module/halaman di branch terpisah
   - Commit message: `[ModuleName] Description`
   - Pull request sebelum merge ke main

2. **Code Style**
   - Gunakan naming convention yang konsisten (camelCase untuk JS, snake_case untuk PHP)
   - Comment setiap fungsi dan logic kompleks
   - Follow semantic HTML best practices

3. **Communication**
   - Update status progress setiap hari
   - Report bugs/blockers immediately
   - Weekly sync meeting untuk integration issues

4. **Testing**
   - Test halaman sendiri di berbagai ukuran screen
   - Test navigation antar halaman
   - Test form validation

---

## Notes

- Semua halaman harus responsive (mobile-first approach)
- Gunakan hanya HTML5, CSS3, Vanilla JavaScript (no framework)
- Database akan di-setup di phase 2
- Fokus pada UX yang baik dan tampilan yang profesional
