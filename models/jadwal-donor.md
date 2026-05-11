# Model: Jadwal Donor

## Tabel: jadwal_donor

| Field         | Tipe      | Keterangan                        |
|---------------|-----------|-----------------------------------|
| id            | INT (PK)  | Primary key, auto increment       |
| judul         | VARCHAR   | Nama/judul kegiatan donor         |
| tanggal       | DATE      | Tanggal pelaksanaan               |
| waktu_mulai   | TIME      | Jam mulai                         |
| waktu_selesai | TIME      | Jam selesai                       |
| lokasi        | VARCHAR   | Tempat pelaksanaan                |
| kuota         | INT       | Maksimal jumlah pendonor          |
| terdaftar     | INT       | Jumlah pendonor yang sudah daftar |
| status        | ENUM      | aktif / selesai / dibatalkan      |
