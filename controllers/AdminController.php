<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Pendonor.php';
require_once __DIR__ . '/../models/JadwalDonor.php';
require_once __DIR__ . '/../models/StokDarah.php';
require_once __DIR__ . '/../models/RiwayatDonasi.php';
require_once __DIR__ . '/../models/PermintaanDarah.php';

class AdminController
{
    private $userModel;
    private $pendonorModel;
    private $jadwalModel;
    private $stokModel;
    private $permintaanModel;
    private $riwayatModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->pendonorModel = new Pendonor();
        $this->jadwalModel = new JadwalDonor();
        $this->stokModel = new StokDarah();
        $this->permintaanModel = new PermintaanDarah();
        $this->riwayatModel = new RiwayatDonasi();
    }

    private function csvDateText($value)
    {
        return $value ? "\t" . $value : '';
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    
    public function dashboard()
    {
        $this->requireAdmin();
        $stokList = $this->stokModel->getAll();
        $stokTotal = array_reduce($stokList, function($carry, $item) {
            return $carry + (int)$item['jumlah'];
        }, 0);

        $permintaanAll = $this->permintaanModel->getAll();
        $permintaanPending = count(array_filter($permintaanAll, function($p) {
            return $p['status'] === 'Pending';
        }));

        $users = $this->userModel->getAll();
        $petugasCount = count(array_filter($users, function($u) { return $u['role'] === 'petugas'; }));
        $rsCount = count(array_filter($users, function($u) { return $u['role'] === 'rumahsakit'; }));

        $statistics = [
            'pendonor'   => count($this->pendonorModel->getAll()),
            'stok_total' => $stokTotal,
            'permintaan' => $permintaanPending,
            'jadwal'     => count($this->jadwalModel->getAll()),
            'petugas'    => $petugasCount,
            'rumahsakit' => $rsCount,
        ];

        $permintaan  = $this->permintaanModel->getAllWithUser();
        $stokDarah   = $stokList;

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    
    public function stokDarah()
    {
        $this->requireAdmin();
        $stokList = $this->stokModel->getAll();

        $stokTotal = 0;
        $stokAman = 0;
        $stokRendah = 0;
        $stokKritis = 0;

        $stockMap = [];
        foreach ($stokList as $stok) {
            $g = strtoupper($stok['golongan'] ?? $stok['golongan_darah'] ?? '');
            $r = $stok['rhesus'] ?? $stok['rh'] ?? '';
            $qty = (int)($stok['quantity'] ?? $stok['jumlah'] ?? 0);
            $stockMap[$g . $r] = $qty;
            $stokTotal += $qty;
        }

        
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        foreach ($bloodTypes as $bt) {
            $qty = $stockMap[$bt] ?? 0;
            if ($qty >= 50) {
                $stokAman++;
            } elseif ($qty >= 30) {
                $stokRendah++;
            } else {
                $stokKritis++;
            }
        }

        $statistics = [
            'stok_total'  => $stokTotal,
            'stok_aman'   => $stokAman,
            'stok_rendah' => $stokRendah,
            'stok_kritis' => $stokKritis
        ];
        $stokHistory = $this->stokModel->getHistoryLast7Days();

        require __DIR__ . '/../views/admin/stok-darah.php';
    }

    public function alertStok()
    {
        $this->requireAdmin();
        $_SESSION['success'] = 'Notifikasi alert stok kritis berhasil dicatat.';
        header('Location: index.php?page=admin-stok-darah');
        exit;
    }

    public function pesanStok()
    {
        $this->requireAdmin();
        $golongan = trim($_POST['golongan'] ?? '');
        $jumlah   = intval($_POST['jumlah'] ?? 0);
        if (!$golongan || $jumlah <= 0) {
            $_SESSION['error'] = 'Golongan darah dan jumlah wajib diisi.';
            header('Location: index.php?page=admin-stok-darah');
            exit;
        }
        $_SESSION['success'] = "Permintaan tambahan stok golongan $golongan sebanyak $jumlah kantong berhasil dicatat.";
        header('Location: index.php?page=admin-stok-darah');
        exit;
    }

    
    public function permintaanDarah()
    {
        $this->requireAdmin();
        $permintaan = $this->permintaanModel->getAllWithUser();
        require __DIR__ . '/../views/admin/permintaan-darah.php';
    }

    public function terimaPermintaan()
    {
        $this->requireAdmin();
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $permintaan = $this->permintaanModel->getById($id);
            if (!$permintaan) {
                $_SESSION['error'] = 'Permintaan tidak ditemukan.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
                exit;
            }

            if (($permintaan['status'] ?? '') !== 'Pending') {
                $_SESSION['error'] = 'Hanya permintaan berstatus Pending yang bisa diterima.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
                exit;
            }

            $details = $this->permintaanModel->getDetails($id);
            if (empty($details)) {
                $_SESSION['error'] = 'Detail permintaan tidak ditemukan.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
                exit;
            }

            $stokCukup = true;

            foreach ($details as $det) {
                if (!$this->stokModel->isStockAvailable($det['golongan'], $det['rhesus'], $det['jumlah'])) {
                    $stokCukup = false;
                    break;
                }
            }

            if (!$stokCukup) {
                $_SESSION['error'] = 'Stok darah tidak mencukupi untuk menerima permintaan ini.';
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
                exit;
            }

            if ($this->permintaanModel->approve($id)) {
                foreach ($details as $det) {
                    $this->stokModel->reduceStock(
                        $det['golongan'],
                        $det['rhesus'],
                        $det['jumlah'],
                        'Stok keluar saat permintaan darah disetujui'
                    );
                }
                $_SESSION['success'] = 'Permintaan berhasil disetujui. Stok darah telah dikurangi.';
            } else {
                $_SESSION['error'] = 'Permintaan gagal disetujui.';
            }
        } else {
            $_SESSION['error'] = 'ID permintaan tidak valid.';
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
        exit;
    }

    public function tolakPermintaan()
    {
        $this->requireAdmin();
        $id    = intval($_POST['permintaan_id'] ?? 0);
        $alasan = trim($_POST['alasan'] ?? '');
        if ($id && $alasan) {
            $permintaan = $this->permintaanModel->getById($id);
            if (!$permintaan) {
                $_SESSION['error'] = 'Permintaan tidak ditemukan.';
            } elseif (($permintaan['status'] ?? '') !== 'Pending') {
                $_SESSION['error'] = 'Hanya permintaan berstatus Pending yang bisa ditolak.';
            } elseif ($this->permintaanModel->reject($id, $alasan)) {
                $_SESSION['success'] = 'Permintaan berhasil ditolak.';
            } else {
                $_SESSION['error'] = 'Permintaan gagal ditolak.';
            }
        } else {
            $_SESSION['error'] = 'Alasan penolakan wajib diisi.';
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
        exit;
    }

    public function kirimPermintaan()
    {
        $this->requireAdmin();
        $id    = intval($_POST['permintaan_id'] ?? 0);
        $kurir = trim($_POST['kurir'] ?? '');

        if ($id && $kurir) {
            $permintaan = $this->permintaanModel->getById($id);
            if (!$permintaan) {
                $_SESSION['error'] = 'Permintaan tidak ditemukan.';
            } elseif (($permintaan['status'] ?? '') !== 'Disetujui') {
                $_SESSION['error'] = 'Permintaan hanya bisa dikirim setelah berstatus Disetujui.';
            } elseif ($this->permintaanModel->markAsSent($id, $kurir)) {
                $_SESSION['success'] = 'Permintaan berhasil dikirim via kurir ' . htmlspecialchars($kurir) . '.';
            } else {
                $_SESSION['error'] = 'Permintaan gagal dikirim.';
            }
        } else {
            $_SESSION['error'] = 'Nama kurir wajib diisi.';
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=admin-permintaan-darah'));
        exit;
    }

    
    public function permintaanKirim()   { return $this->kirimPermintaan(); }
    public function prosesPermintaan()  { return $this->tolakPermintaan(); }
    public function permintaanProcess() { return $this->tolakPermintaan(); }

    
    public function kelolaPetugas()
    {
        $this->requireAdmin();
        $users = $this->userModel->getAll();
        $petugasList = array_values(array_filter($users, fn($user) => ($user['role'] ?? '') === 'petugas'));
        $rumahSakitList = array_values(array_filter(
            $users,
            fn($user) => ($user['role'] ?? '') === 'rumahsakit' && ($user['status'] ?? '') !== 'ditolak'
        ));
        require __DIR__ . '/../views/admin/kelola-petugas.php';
    }

    public function formPetugas()
    {
        $this->requireAdmin();
        require __DIR__ . '/../views/admin/form-petugas.php';
    }

    public function tambahPetugasProcess()
    {
        $this->requireAdmin();
        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telepon  = trim($_POST['telepon'] ?? '');
        $status   = $_POST['status'] ?? 'aktif';
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$nama || !$email || !$username || !$password) {
            $_SESSION['error'] = 'Nama, email, username, dan password wajib diisi.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Email sudah terdaftar.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        if ($username !== '' && $this->userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Username sudah digunakan.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        $this->userModel->createPetugas([
            'name'     => $nama,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'petugas',
            'status'   => $status,
            'telepon'  => $telepon,
            'username' => $username,
        ]);

        $_SESSION['success'] = 'Petugas berhasil ditambahkan.';
        header('Location: index.php?page=admin-kelola-petugas');
        exit;
    }

    public function editPetugasProcess()
    {
        $this->requireAdmin();
        $id      = intval($_POST['id'] ?? 0);
        $nama    = trim($_POST['nama'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $status  = $_POST['status'] ?? 'aktif';
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$id || !$nama || !$email || !$username) {
            $_SESSION['error'] = 'Data tidak lengkap.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        $existingEmail = $this->userModel->findByEmail($email);
        if ($existingEmail && (int)$existingEmail['id'] !== $id) {
            $_SESSION['error'] = 'Email sudah digunakan akun lain.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        $existingUsername = $this->userModel->findByUsername($username);
        if ($existingUsername && (int)$existingUsername['id'] !== $id) {
            $_SESSION['error'] = 'Username sudah digunakan akun lain.';
            header('Location: index.php?page=admin-kelola-petugas');
            exit;
        }

        $data = [
            'name'    => $nama,
            'email'   => $email,
            'telepon' => $telepon,
            'status'  => $status,
            'username' => $username,
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->updatePetugas($id, $data);

        $_SESSION['success'] = 'Data petugas berhasil diperbarui.';
        header('Location: index.php?page=admin-kelola-petugas');
        exit;
    }

    public function hapusPetugasProcess()
    {
        $this->requireAdmin();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->userModel->deletePetugas($id);
            $_SESSION['success'] = 'Petugas berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'ID tidak valid.';
        }
        header('Location: index.php?page=admin-kelola-petugas');
        exit;
    }

    public function hapusRsProcess()
    {
        $this->requireAdmin();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($id) {
            $this->userModel->deleteRumahSakit($id);
            $_SESSION['success'] = 'Akun rumah sakit berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'ID rumah sakit tidak valid.';
        }

        header('Location: index.php?page=admin-kelola-petugas');
        exit;
    }

    
    public function approveRs()
    {
        $this->requireAdmin();
        $id     = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? null;

        if ($id && in_array($status, ['aktif', 'ditolak', 'nonaktif', 'pending'])) {
            $user = $this->userModel->findById($id);
            if (!$user || ($user['role'] ?? '') !== 'rumahsakit') {
                $_SESSION['error'] = 'Data rumah sakit tidak ditemukan.';
            } elseif ($this->userModel->updateStatusByRole($id, $status, 'rumahsakit')) {
                $_SESSION['success'] = 'Status rumah sakit berhasil diperbarui.';
            } else {
                $_SESSION['error'] = 'Status rumah sakit gagal diperbarui.';
            }
        } else {
            $_SESSION['error'] = 'Data tidak valid.';
        }

        header('Location: index.php?page=admin-kelola-petugas');
        exit;
    }

    
    public function jadwalDonor()
    {
        $this->requireAdmin();
        $jadwalList = $this->jadwalModel->getAll();

        $currentMonth = date('Y-m');
        $jadwalBulanIni = 0;
        $totalPeserta = 0;
        
        foreach ($jadwalList as $j) {
            if (strpos($j['tanggal'] ?? '', $currentMonth) === 0) {
                $jadwalBulanIni++;
            }
            $totalPeserta += intval($j['terdaftar'] ?? 0);
        }

        $statistics = [
            'jadwal'       => count($jadwalList),
            'jadwal_bulan' => $jadwalBulanIni,
            'peserta'      => $totalPeserta
        ];

        require __DIR__ . '/../views/admin/jadwal-donor.php';
    }

    public function formJadwal()
    {
        $this->requireAdmin();
        require __DIR__ . '/../views/admin/form-jadwal.php';
    }

    public function formJadwalProcess()
    {
        $this->requireAdmin();
        $lokasi  = trim($_POST['lokasi'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $target  = intval($_POST['target'] ?? 0);
        $waktuMulai = $_POST['waktu_mulai'] ?? $_POST['jam_mulai'] ?? '08:00';
        $waktuSelesai = $_POST['waktu_selesai'] ?? $_POST['jam_selesai'] ?? '14:00';
        $status = $_POST['status'] ?? 'Akan Datang';
        $catatan = trim($_POST['catatan'] ?? '');

        if (!$lokasi || !$tanggal || $target <= 0) {
            $_SESSION['error'] = 'Semua field jadwal harus diisi dengan benar.';
            header('Location: index.php?page=admin-form-jadwal');
            exit;
        }

        $this->jadwalModel->create([
            'lokasi' => $lokasi, 
            'tanggal' => $tanggal, 
            'target' => $target,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'status' => $status,
            'catatan' => $catatan
        ]);
        $_SESSION['success'] = 'Jadwal donor berhasil ditambahkan.';
        header('Location: index.php?page=admin-jadwal-donor');
        exit;
    }

    public function editJadwalProcess()
    {
        $this->requireAdmin();
        $id      = intval($_POST['id'] ?? 0);
        $lokasi  = trim($_POST['lokasi'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $target  = intval($_POST['target'] ?? 0);
        $waktuMulai = $_POST['waktu_mulai'] ?? $_POST['jam_mulai'] ?? '08:00';
        $waktuSelesai = $_POST['waktu_selesai'] ?? $_POST['jam_selesai'] ?? '14:00';
        $status = $_POST['status'] ?? 'Akan Datang';
        $catatan = trim($_POST['catatan'] ?? '');

        if (!$id || !$lokasi || !$tanggal || $target <= 0) {
            $_SESSION['error'] = 'Semua field jadwal harus diisi.';
            header('Location: index.php?page=admin-jadwal-donor');
            exit;
        }

        $this->jadwalModel->update($id, [
            'lokasi' => $lokasi, 
            'tanggal' => $tanggal, 
            'target' => $target,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'status' => $status,
            'catatan' => $catatan
        ]);
        $_SESSION['success'] = 'Jadwal berhasil diperbarui.';
        header('Location: index.php?page=admin-jadwal-donor');
        exit;
    }

    public function hapusJadwalProcess()
    {
        $this->requireAdmin();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->jadwalModel->delete($id);
            $_SESSION['success'] = 'Jadwal berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'ID tidak valid.';
        }
        header('Location: index.php?page=admin-jadwal-donor');
        exit;
    }

    
    public function daftarPendonor()
    {
        $this->requireAdmin();
        $pendonorList = $this->pendonorModel->getAll();
        $search   = $_GET['search'] ?? '';
        $golongan = $_GET['golongan'] ?? '';
        $status   = $_GET['status'] ?? '';

        if ($search || $golongan || $status) {
            $pendonorList = array_filter($pendonorList, function($item) use ($search, $golongan, $status) {
                if ($golongan && ($item['golongan'] ?? '') != $golongan) return false;
                if ($status && ($item['status'] ?? 'aktif') != $status) return false;
                if ($search) {
                    $s = strtolower($search);
                    if (
                        strpos(strtolower($item['nama'] ?? ''), $s) === false &&
                        strpos(strtolower($item['nik'] ?? ''), $s) === false &&
                        strpos(strtolower($item['telepon'] ?? ''), $s) === false
                    ) return false;
                }
                return true;
            });
        }

        require __DIR__ . '/../views/admin/daftar-pendonor-admin.php';
    }

    
    public function exportSemuaLaporan()
    {
        $this->requireAdmin();
        $users      = $this->userModel->getAll();
        $petugas    = array_values(array_filter($users, fn($user) => ($user['role'] ?? '') === 'petugas'));
        $rumahSakit = array_values(array_filter(
            $users,
            fn($user) => ($user['role'] ?? '') === 'rumahsakit' && ($user['status'] ?? '') !== 'ditolak'
        ));
        $pendonor   = $this->pendonorModel->getAll();
        $permintaan = $this->permintaanModel->getAllWithUser();
        $stokDarah  = $this->stokModel->getAll();
        $stokHistory = $this->stokModel->getHistoryLast7Days();
        $riwayatDonasi = $this->riwayatModel->getAll();
        $jadwalDonor = $this->jadwalModel->getAll();

        $stokTotal = array_reduce($stokDarah, fn($carry, $item) => $carry + (int)($item['jumlah'] ?? 0), 0);
        $permintaanPending = count(array_filter($permintaan, fn($item) => ($item['status'] ?? '') === 'Pending'));
        $permintaanDisetujui = count(array_filter($permintaan, fn($item) => in_array(($item['status'] ?? ''), ['Disetujui', 'Dikirim'])));
        $permintaanDitolak = count(array_filter($permintaan, fn($item) => ($item['status'] ?? '') === 'Ditolak'));

        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=Laporan_SIDORA_" . date('Y-m-d') . ".csv");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); 

        fputcsv($out, ['=== RINGKASAN OPERASIONAL SIDORA ===']);
        fputcsv($out, ['Item', 'Jumlah']);
        fputcsv($out, ['Total Petugas', count($petugas)]);
        fputcsv($out, ['Total Rumah Sakit Terverifikasi/Pending', count($rumahSakit)]);
        fputcsv($out, ['Total Pendonor', count($pendonor)]);
        fputcsv($out, ['Total Stok Darah', $stokTotal]);
        fputcsv($out, ['Total Riwayat Donasi', count($riwayatDonasi)]);
        fputcsv($out, ['Total Jadwal Donor', count($jadwalDonor)]);
        fputcsv($out, ['Total Permintaan Darah', count($permintaan)]);
        fputcsv($out, ['Permintaan Pending', $permintaanPending]);
        fputcsv($out, ['Permintaan Disetujui/Dikirim', $permintaanDisetujui]);
        fputcsv($out, ['Permintaan Ditolak', $permintaanDitolak]);
        fputcsv($out, []);

        fputcsv($out, ['=== DATA PETUGAS ===']);
        fputcsv($out, ['Nama', 'Email', 'Username', 'Telepon', 'Status', 'Tanggal Bergabung']);
        foreach ($petugas as $p) {
            fputcsv($out, [
                $p['name'] ?? '',
                $p['email'] ?? '',
                $p['username'] ?? '',
                $p['telepon'] ?? '',
                $p['status'] ?? '',
                $this->csvDateText($p['created_at'] ?? ''),
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== DATA RUMAH SAKIT ===']);
        fputcsv($out, ['Nama Rumah Sakit', 'Email', 'Username', 'Tipe Rumah Sakit', 'No. Izin Operasional', 'Telepon', 'Kontak / PIC', 'Alamat', 'Kelurahan / Desa', 'Kecamatan', 'Provinsi', 'Kode Pos', 'Status', 'Tanggal Daftar']);
        foreach ($rumahSakit as $rs) {
            fputcsv($out, [
                $rs['name'] ?? '',
                $rs['email'] ?? '',
                $rs['username'] ?? '',
                $rs['tipe_rs'] ?? '',
                $rs['no_izin'] ?? '',
                $rs['telepon'] ?? '',
                $rs['kontak'] ?? '',
                $rs['alamat'] ?? '',
                $rs['desa'] ?? '',
                $rs['kecamatan'] ?? '',
                $rs['provinsi'] ?? '',
                $rs['kode_pos'] ?? '',
                $rs['status'] ?? '',
                $this->csvDateText($rs['created_at'] ?? ''),
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== DATA PENDONOR ===']);
        fputcsv($out, ['Nama', 'Golongan Darah', 'NIK', 'Telepon', 'Email', 'Alamat', 'Kota', 'Provinsi', 'Status']);
        foreach ($pendonor as $pd) {
            fputcsv($out, [
                $pd['nama'] ?? '',
                ($pd['golongan'] ?? '') . ($pd['rhesus'] ?? ''),
                $pd['nik'] ?? '',
                $pd['telepon'] ?? '',
                $pd['email'] ?? '',
                $pd['alamat'] ?? '',
                $pd['kota'] ?? '',
                $pd['provinsi'] ?? '',
                $pd['status'] ?? '',
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== RIWAYAT DONASI ===']);
        fputcsv($out, ['Tanggal Donasi', 'Nama Pendonor', 'NIK', 'Golongan Darah', 'Jumlah Kantong', 'Tekanan Darah', 'Status', 'Telepon', 'Alamat']);
        foreach ($riwayatDonasi as $riwayat) {
            fputcsv($out, [
                $this->csvDateText($riwayat['tanggal'] ?? ''),
                $riwayat['nama_pendonor'] ?? '',
                $riwayat['nik'] ?? '',
                ($riwayat['golongan'] ?? '') . ($riwayat['rhesus'] ?? ''),
                $riwayat['jumlah'] ?? '',
                $riwayat['tekanan_darah'] ?? '',
                $riwayat['status'] ?? '',
                $riwayat['telepon'] ?? '',
                $riwayat['alamat'] ?? '',
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== STOK DARAH SAAT INI ===']);
        fputcsv($out, ['Golongan Darah', 'Rhesus', 'Jumlah Kantong', 'Minimum Stok', 'Status Stok']);
        foreach ($stokDarah as $stok) {
            $jumlah = (int)($stok['jumlah'] ?? 0);
            $minStock = (int)($stok['min_stock'] ?? 50);
            $statusStok = $jumlah >= $minStock ? 'Aman' : ($jumlah >= 30 ? 'Rendah' : 'Kritis');
            fputcsv($out, [
                $stok['golongan_darah'] ?? '',
                $stok['rhesus'] ?? '',
                $jumlah,
                $minStock,
                $statusStok,
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== RIWAYAT UPDATE STOK 7 HARI TERAKHIR ===']);
        fputcsv($out, ['Tanggal', 'Golongan Darah', 'Masuk', 'Keluar', 'Stok Akhir', 'Catatan']);
        foreach ($stokHistory as $history) {
            fputcsv($out, [
                $this->csvDateText($history['tanggal'] ?? ''),
                ($history['golongan'] ?? '') . ($history['rhesus'] ?? ''),
                $history['masuk'] ?? 0,
                $history['keluar'] ?? 0,
                $history['stok_akhir'] ?? 0,
                $history['catatan'] ?? '',
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== JADWAL DONOR ===']);
        fputcsv($out, ['Tanggal', 'Waktu Mulai', 'Waktu Selesai', 'Lokasi', 'Target', 'Terdaftar', 'Status', 'Catatan']);
        foreach ($jadwalDonor as $jadwal) {
            fputcsv($out, [
                $this->csvDateText($jadwal['tanggal'] ?? ''),
                $jadwal['waktu_mulai'] ?? '',
                $jadwal['waktu_selesai'] ?? '',
                $jadwal['lokasi'] ?? '',
                $jadwal['target'] ?? '',
                $jadwal['terdaftar'] ?? '',
                $jadwal['status'] ?? '',
                $jadwal['catatan'] ?? '',
            ]);
        }
        fputcsv($out, []);
        fputcsv($out, ['=== DATA PERMINTAAN ===']);
        fputcsv($out, ['Rumah Sakit', 'Golongan', 'Jumlah', 'Prioritas', 'Status', 'Tanggal Permintaan', 'Alasan Tolak', 'Tanggal Tolak', 'Tanggal Kirim', 'Kurir', 'Keterangan']);
        foreach ($permintaan as $req) {
            fputcsv($out, [
                $req['rumah_sakit'] ?? '',
                ($req['golongan'] ?? '') . ($req['rhesus'] ?? ''),
                $req['detail_jumlah'] ?? 0,
                $req['prioritas'] ?? '',
                $req['status'] ?? '',
                $this->csvDateText($req['created_at'] ?? ''),
                $req['alasan_tolak'] ?? '',
                $this->csvDateText($req['tanggal_tolak'] ?? ''),
                $this->csvDateText($req['tanggal_kirim'] ?? ''),
                $req['kurir'] ?? '',
                $req['keterangan'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportPermintaanLaporan()
    {
        $this->requireAdmin();
        $permintaan = $this->permintaanModel->getAllWithUser();

        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=Permintaan_Darah_" . date('Y-m-d') . ".csv");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, ['No', 'Rumah Sakit', 'Golongan', 'Jumlah', 'Prioritas', 'Status', 'Tanggal', 'Kurir']);
        $no = 1;
        foreach ($permintaan as $req) {
            fputcsv($out, [
                $no++,
                $req['rumah_sakit'] ?? '',
                ($req['golongan'] ?? '') . ($req['rhesus'] ?? ''),
                $req['detail_jumlah'] ?? 0,
                $req['prioritas'] ?? '',
                $req['status'] ?? '',
                $this->csvDateText($req['created_at'] ?? ''),
                $req['kurir'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }
}
