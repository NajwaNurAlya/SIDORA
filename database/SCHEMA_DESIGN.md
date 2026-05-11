# 📊 DATABASE SCHEMA - SIDORA

## 🎯 Overview

Database SIDORA menggunakan relational model dengan 7 tabel utama:
- users (Admin, Petugas, Rumah Sakit)
- pendonor (Data pendonor darah)
- stok_darah (Inventory darah)
- jadwal_donor (Jadwal kegiatan donor)
- riwayat_donasi (Histori donasi)
- permintaan_darah (Permintaan dari RS)
- log_activity (Audit trail)

---

## 📋 Table Structures

### 1. USERS
Menyimpan semua pengguna sistem (Admin, Petugas, Rumah Sakit)

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- hashed password
    email VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    role ENUM('admin', 'petugas', 'rumahsakit') NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    nomor_telepon VARCHAR(20),
    alamat TEXT,
    kota VARCHAR(100),
    provinsi VARCHAR(100),
    
    -- Khusus untuk Rumah Sakit
    nama_rs VARCHAR(150),
    nomor_registrasi VARCHAR(50),
    
    -- Khusus untuk Petugas
    nip VARCHAR(20),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    
    -- Indexes
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);
```

### 2. PENDONOR
Menyimpan data semua pendonor darah

```sql
CREATE TABLE pendonor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_identitas VARCHAR(20) UNIQUE NOT NULL, -- KTP/NIK
    nama_lengkap VARCHAR(150) NOT NULL,
    golongan_darah ENUM('A', 'B', 'AB', 'O') NOT NULL,
    rhesus ENUM('+', '-') NOT NULL,
    
    -- Info Pribadi
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    umur INT GENERATED ALWAYS AS (YEAR(CURDATE()) - YEAR(tanggal_lahir)) STORED,
    
    -- Kontak
    nomor_telepon VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    alamat TEXT NOT NULL,
    kelurahan VARCHAR(100),
    kecamatan VARCHAR(100),
    kota VARCHAR(100),
    provinsi VARCHAR(100),
    kode_pos VARCHAR(10),
    
    -- Info Tambahan
    pekerjaan VARCHAR(100),
    status_sipil ENUM('Belum Menikah', 'Menikah', 'Cerai', 'Duda/Janda'),
    kondisi ENUM('aktif', 'nonaktif', 'suspended') DEFAULT 'aktif',
    catatan TEXT,
    
    -- Riwayat Donor
    total_donasi INT DEFAULT 0,
    terakhir_donor DATE NULL,
    status_kelayakan ENUM('layak', 'tidak_layak', 'perlu_cek') DEFAULT 'layak',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_nomor_identitas (nomor_identitas),
    INDEX idx_nama (nama_lengkap),
    INDEX idx_golongan_darah (golongan_darah),
    INDEX idx_rhesus (rhesus),
    INDEX idx_kondisi (kondisi),
    INDEX idx_gol_rhesus (golongan_darah, rhesus)
);
```

### 3. RIWAYAT_DONASI
Menyimpan record setiap kali pendonor melakukan donasi

```sql
CREATE TABLE riwayat_donasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pendonor_id INT NOT NULL,
    petugas_id INT NOT NULL, -- User yang mencatat
    
    -- Info Donasi
    tanggal_donasi DATE NOT NULL,
    waktu_mulai TIME,
    waktu_selesai TIME,
    volume_darah INT DEFAULT 450, -- ml
    golongan_darah ENUM('A', 'B', 'AB', 'O') NOT NULL,
    rhesus ENUM('+', '-') NOT NULL,
    
    -- Kondisi Pendonor
    tekanan_darah VARCHAR(10), -- 120/80
    hemoglobin DECIMAL(4, 1), -- g/dL
    denyut_nadi INT,
    suhu_tubuh DECIMAL(3, 1),
    
    -- Hasil Donasi
    status_donasi ENUM('berhasil', 'gagal', 'ditunda') DEFAULT 'berhasil',
    alasan_gagal VARCHAR(255),
    komplikasi VARCHAR(255),
    
    -- Jadwal Donor
    jadwal_donor_id INT,
    lokasi TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (pendonor_id) REFERENCES pendonor(id) ON DELETE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (jadwal_donor_id) REFERENCES jadwal_donor(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_pendonor_id (pendonor_id),
    INDEX idx_tanggal_donasi (tanggal_donasi),
    INDEX idx_status_donasi (status_donasi)
);
```

### 4. STOK_DARAH
Menyimpan inventory darah yang tersedia

```sql
CREATE TABLE stok_darah (
    id INT PRIMARY KEY AUTO_INCREMENT,
    golongan_darah ENUM('A', 'B', 'AB', 'O') NOT NULL,
    rhesus ENUM('+', '-') NOT NULL,
    
    -- Stok Info
    jumlah_kantong INT DEFAULT 0, -- unit kantong
    jumlah_ml DECIMAL(10, 2) DEFAULT 0, -- total ml
    minimum_stok INT DEFAULT 50, -- Alert level
    
    -- Status
    kondisi_stok ENUM('aman', 'rendah', 'kritis') DEFAULT 'aman',
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_blood_type (golongan_darah, rhesus),
    INDEX idx_kondisi_stok (kondisi_stok)
);
```

### 5. JADWAL_DONOR
Menyimpan jadwal kegiatan donor darah

```sql
CREATE TABLE jadwal_donor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL, -- User yang membuat jadwal
    
    -- Info Jadwal
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    
    -- Lokasi
    lokasi_nama VARCHAR(255) NOT NULL,
    alamat TEXT NOT NULL,
    kota VARCHAR(100),
    koordinat_gps VARCHAR(50), -- latitude,longitude
    
    -- Peserta
    target_peserta INT,
    peserta_terdaftar INT DEFAULT 0,
    
    -- Status
    status ENUM('draft', 'aktif', 'selesai', 'dibatalkan') DEFAULT 'draft',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_tanggal (tanggal_mulai),
    INDEX idx_status (status)
);
```

### 6. PERMINTAAN_DARAH
Menyimpan permintaan darah dari Rumah Sakit

```sql
CREATE TABLE permintaan_darah (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rumah_sakit_id INT NOT NULL,
    admin_id INT, -- User admin yang proses
    
    -- Identitas Pasien
    nama_pasien VARCHAR(150) NOT NULL,
    nomor_pasien VARCHAR(50),
    ruangan VARCHAR(100),
    dokter_penanggungjawab VARCHAR(150),
    
    -- Permintaan Darah
    golongan_darah ENUM('A', 'B', 'AB', 'O') NOT NULL,
    rhesus ENUM('+', '-') NOT NULL,
    jumlah_kantong INT NOT NULL,
    
    -- Status & Proses
    status ENUM('pending', 'disetujui', 'ditolak', 'dikirim', 'diterima') DEFAULT 'pending',
    prioritas ENUM('biasa', 'segera', 'darurat') DEFAULT 'biasa',
    alasan_penolakan VARCHAR(255),
    
    -- Tanggal
    tanggal_permintaan DATETIME NOT NULL,
    tanggal_disetujui DATETIME,
    tanggal_dikirim DATETIME,
    tanggal_diterima DATETIME,
    
    -- Catatan
    keterangan TEXT,
    catatan_admin TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (rumah_sakit_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_rumah_sakit_id (rumah_sakit_id),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_permintaan),
    INDEX idx_prioritas (prioritas)
);
```

### 7. LOG_ACTIVITY
Menyimpan audit trail untuk compliance

```sql
CREATE TABLE log_activity (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    aksi VARCHAR(255) NOT NULL,
    modul VARCHAR(100),
    keterangan TEXT,
    ip_address VARCHAR(50),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Index
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);
```

---

## 🔑 Key Relationships

```
users (admin, petugas, RS)
    ├─ 1:N → riwayat_donasi (petugas mencatat)
    ├─ 1:N → jadwal_donor (admin buat jadwal)
    └─ 1:N → permintaan_darah (RS ajukan, admin proses)

pendonor
    └─ 1:N → riwayat_donasi (donor history)

jadwal_donor
    └─ 1:N → riwayat_donasi (donasi dalam jadwal)

permintaan_darah
    └─ referensi golongan_darah & rhesus ke stok_darah
```

---

## 📊 Initial Data to Insert

### Golongan Darah (8 kombinasi)
```sql
INSERT INTO stok_darah (golongan_darah, rhesus, jumlah_kantong, jumlah_ml, minimum_stok) VALUES
('O', '+', 120, 54000, 50),
('O', '-', 35, 15750, 50),
('A', '+', 85, 38250, 50),
('A', '-', 30, 13500, 50),
('B', '+', 155, 69750, 50),
('B', '-', 24, 10800, 50),
('AB', '+', 89, 40050, 30),
('AB', '-', 22, 9900, 30);
```

### Admin Default User
```sql
INSERT INTO users (username, password, email, nama_lengkap, role, status) VALUES
('admin', SHA2('admin123', 256), 'admin@sidora.id', 'Admin SIDORA', 'admin', 'aktif');
```

---

## 🔐 Security Considerations

### Best Practices:
1. **Passwords**: Hash dengan SHA2/bcrypt, NEVER plain text
2. **Timestamps**: Use CURRENT_TIMESTAMP untuk tracking
3. **Foreign Keys**: Enable cascade delete hanya di tempat yang tepat
4. **Indexes**: Pada kolom yang sering di-query (status, tanggal, id)
5. **Data Validation**: Di application layer sebelum insert
6. **Audit Log**: Catat setiap aksi penting di log_activity

### Soft Delete (Optional):
Jika perlu keep history, tambahkan `deleted_at` column ke table utama:
```sql
deleted_at TIMESTAMP NULL DEFAULT NULL
```

---

## 🚀 Database Setup Commands

### Create Database
```sql
CREATE DATABASE sidora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sidora_db;
```

### Create All Tables
Jalankan semua CREATE TABLE statements di atas.

### Setup Backup
```bash
# Backup database
mysqldump -u root -p sidora_db > sidora_backup.sql

# Restore database
mysql -u root -p sidora_db < sidora_backup.sql
```

---

## 📝 Query Examples

### Get Stok Status
```sql
SELECT * FROM stok_darah WHERE kondisi_stok != 'aman';
```

### Get Permintaan Pending
```sql
SELECT p.*, u.nama_rs FROM permintaan_darah p 
JOIN users u ON p.rumah_sakit_id = u.id 
WHERE p.status = 'pending' 
ORDER BY p.prioritas DESC, p.tanggal_permintaan ASC;
```

### Get Pendonor Stats
```sql
SELECT golongan_darah, rhesus, COUNT(*) as total, AVG(total_donasi) as rata_donasi
FROM pendonor
WHERE status = 'aktif'
GROUP BY golongan_darah, rhesus;
```

---

## 🔄 Next Phase

### Database Implementation:
1. Create all tables in MySQL
2. Setup indexes for performance
3. Create stored procedures if needed
4. Setup backups schedule
5. Test with sample data

---

**Database Schema Ready for Implementation** ✅
