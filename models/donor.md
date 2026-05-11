# Model: Donor

## Tabel: pendonors

| Field          | Tipe      | Keterangan                        |
|----------------|-----------|-----------------------------------|
| id             | INT (PK)  | Primary key, auto increment       |
| nama           | VARCHAR   | Nama lengkap pendonor             |
| nik            | VARCHAR   | NIK (16 digit)                    |
| tanggal_lahir  | DATE      | Tanggal lahir                     |
| golongan_darah | ENUM      | A / B / AB / O                    |
| rhesus         | ENUM      | + / -                             |
| no_telepon     | VARCHAR   | Nomor telepon                     |
| alamat         | TEXT      | Alamat lengkap                    |
| total_donasi   | INT       | Total berapa kali sudah donor     |
| last_donasi    | DATE      | Tanggal terakhir donor            |
| status         | ENUM      | aktif / nonaktif                  |
