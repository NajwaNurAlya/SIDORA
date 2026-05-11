# Model: Permintaan Darah

## Tabel: permintaan_darah

| Field          | Tipe      | Keterangan                            |
|----------------|-----------|---------------------------------------|
| id             | INT (PK)  | Primary key, auto increment           |
| rumahsakit_id  | INT (FK)  | Relasi ke tabel users (role RS)       |
| golongan_darah | ENUM      | A / B / AB / O                        |
| rhesus         | ENUM      | + / -                                 |
| jumlah         | INT       | Jumlah kantong diminta                |
| prioritas      | ENUM      | normal / urgent / sangat-urgent       |
| status         | ENUM      | pending / disetujui / ditolak / dikirim|
| keterangan     | TEXT      | Catatan tambahan                      |
| surat_path     | VARCHAR   | Path file surat permintaan resmi      |
| tanggal_ajuan  | DATETIME  | Waktu pengajuan                       |
