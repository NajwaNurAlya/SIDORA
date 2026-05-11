# Model: Stok Darah

## Tabel: stok_darah

| Field          | Tipe      | Keterangan                        |
|----------------|-----------|-----------------------------------|
| id             | INT (PK)  | Primary key, auto increment       |
| golongan_darah | ENUM      | A / B / AB / O                    |
| rhesus         | ENUM      | + / -                             |
| jumlah_kantong | INT       | Jumlah kantong tersedia           |
| tanggal_masuk  | DATE      | Tanggal darah masuk               |
| kadaluarsa     | DATE      | Tanggal kadaluarsa                |
| status         | ENUM      | aman / rendah / kritis            |
