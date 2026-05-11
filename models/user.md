# Model: User

## Tabel: users

| Field      | Tipe      | Keterangan                        |
|------------|-----------|-----------------------------------|
| id         | INT (PK)  | Primary key, auto increment       |
| username   | VARCHAR   | Username unik untuk login         |
| password   | VARCHAR   | Password (hashed)                 |
| role       | ENUM      | admin / petugas / rumahsakit      |
| nama       | VARCHAR   | Nama lengkap pengguna             |
| email      | VARCHAR   | Email pengguna                    |
| created_at | DATETIME  | Waktu dibuat                      |

## Relasi
- Admin → mengelola semua data
- Petugas → mengelola donor, stok, jadwal
- Rumah Sakit → mengajukan permintaan darah
