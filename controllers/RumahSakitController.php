<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/StokDarah.php';
require_once __DIR__ . '/../models/PermintaanDarah.php';

class RumahSakitController
{
    private $userModel;
    private $stokModel;
    private $permintaanModel;

    public function __construct()
    {
        $this->userModel       = new User();
        $this->stokModel       = new StokDarah();
        $this->permintaanModel = new PermintaanDarah();
    }

    private function requireRumahSakit()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'rumahsakit') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    
    public function dashboard()
    {
        $this->requireRumahSakit();
        $stokList      = $this->stokModel->getAll();
        $userId        = $_SESSION['user']['id'];
        $permintaanList = $this->permintaanModel->getAllByUser($userId);

        $statistics = [
            'total_permintaan'  => count($permintaanList),
            'permintaan_ditinjau' => count(array_filter($permintaanList, fn($p) => $p['status'] === 'Pending')),
            'disetujui'         => count(array_filter($permintaanList, fn($p) => in_array($p['status'], ['Disetujui', 'Dikirim']))),
            'permintaan_ditolak'=> count(array_filter($permintaanList, fn($p) => $p['status'] === 'Ditolak')),
        ];

        require __DIR__ . '/../views/rumahsakit/dashboard.php';
    }

    
    public function permintaan()
    {
        $this->requireRumahSakit();
        $stokList = $this->stokModel->getAll();
        require __DIR__ . '/../views/rumahsakit/permintaan-darah.php';
    }

    public function permintaanStore()
    {
        $this->requireRumahSakit();
        $userId        = $_SESSION['user']['id'];
        $tujuan        = trim($_POST['tujuan_permintaan'] ?? 'pasien');
        $golongan      = trim($_POST['golongan'] ?? '');
        $rhesus        = trim($_POST['rhesus'] ?? '');
        $jumlah        = intval($_POST['jumlah'] ?? 1);
        $prioritas     = trim($_POST['prioritas'] ?? 'Biasa');
        $keteranganInput = trim($_POST['keterangan'] ?? '');
        
        $namaPasien    = '';
        $noPasien      = '';
        $ruangan       = '';
        $dokter        = '';

        if ($tujuan === 'stok') {
            $namaPasien = 'Buffer Stok RS';
            $noPasien   = '-';
        } else {
            $namaPasien = trim($_POST['nama_pasien'] ?? '');
            $noPasien   = trim($_POST['no_pasien'] ?? '');
            $ruangan    = trim($_POST['ruangan'] ?? '');
            $dokter     = trim($_POST['dokter'] ?? '');
        }

        if (
            !$golongan ||
            !$rhesus ||
            $jumlah <= 0 ||
            ($tujuan === 'pasien' && (!$namaPasien || !$noPasien || !$ruangan || !$dokter))
        ) {
            $_SESSION['error'] = 'Lengkapi semua data permintaan darah.';
            header('Location: index.php?page=rs-permintaan');
            exit;
        }

        if ($tujuan === 'stok') {
            $keterangan = sprintf(
                'Tujuan: stok. Keperluan: Penambahan Stok BDRS (Dropping). Catatan: %s',
                $keteranganInput ?: '-'
            );
        } else {
            $keterangan = sprintf(
                'Tujuan: pasien. Pasien: %s (No RM: %s). Ruangan: %s. Dokter: %s. Catatan: %s',
                $namaPasien,
                $noPasien,
                $ruangan,
                $dokter,
                $keteranganInput ?: '-'
            );
        }

        $insertId = $this->permintaanModel->create([
            'user_id'   => $userId,
            'status'    => 'Pending',
            'prioritas' => $prioritas,
            'keterangan'=> $keterangan,
        ]);

        if ($insertId) {
            $this->permintaanModel->addDetail([
                'permintaan_id' => $insertId,
                'golongan'      => $golongan,
                'rhesus'        => $rhesus,
                'jumlah'        => $jumlah,
            ]);
            $_SESSION['success'] = 'Permintaan darah telah diajukan dan sedang menunggu konfirmasi admin.';
            header('Location: index.php?page=rs-history-permintaan');
            exit;
        }

        $_SESSION['error'] = 'Terjadi kesalahan dalam mengajukan permintaan.';
        header('Location: index.php?page=rs-permintaan');
        exit;
    }

    public function historyPermintaan()
    {
        $this->requireRumahSakit();
        $userId         = $_SESSION['user']['id'];
        $permintaanList = $this->permintaanModel->getAllByUser($userId);
        require __DIR__ . '/../views/rumahsakit/history-permintaan.php';
    }

    
    public function stokDarah()
    {
        $this->requireRumahSakit();
        $stokList = $this->stokModel->getAll();
        require __DIR__ . '/../views/rumahsakit/stok-darah.php';
    }

    
    public function profil()
    {
        $this->requireRumahSakit();
        
        $user = $this->userModel->findById($_SESSION['user']['id']) ?: $_SESSION['user'];
        require __DIR__ . '/../views/rumahsakit/profil.php';
    }

    public function profilUpdate()
    {
        $this->requireRumahSakit();
        $userId   = $_SESSION['user']['id'];
        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $alamat   = trim($_POST['alamat'] ?? '');
        $telepon  = trim($_POST['telepon'] ?? '');
        $kontak   = trim($_POST['kontak'] ?? '');
        
        $desa      = trim($_POST['desa'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $provinsi  = trim($_POST['provinsi'] ?? '');
        $kode_pos  = trim($_POST['kode_pos'] ?? '');
        $currentUser = $this->userModel->findById($userId) ?: [];
        $tipeRs   = trim($_POST['tipe_rs'] ?? ($currentUser['tipe_rs'] ?? ''));
        $noIzin   = trim($_POST['no_izin'] ?? ($currentUser['no_izin'] ?? ''));

        if (!$nama) {
            $_SESSION['error'] = 'Nama Rumah Sakit wajib diisi.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        $this->userModel->updateProfil($userId, [
            'name'      => $nama,
            'username'  => $username,
            'alamat'    => $alamat,
            'telepon'   => $telepon,
            'kontak'    => $kontak,
            'desa'      => $desa,
            'kecamatan' => $kecamatan,
            'provinsi'  => $provinsi,
            'kode_pos'  => $kode_pos,
            'tipe_rs'   => $tipeRs,
            'no_izin'   => $noIzin,
        ]);

        
        $_SESSION['user']['name']      = $nama;
        $_SESSION['user']['username']  = $username;
        $_SESSION['user']['alamat']    = $alamat;
        $_SESSION['user']['telepon']   = $telepon;
        $_SESSION['user']['kontak']    = $kontak;
        $_SESSION['user']['desa']      = $desa;
        $_SESSION['user']['kecamatan'] = $kecamatan;
        $_SESSION['user']['provinsi']  = $provinsi;
        $_SESSION['user']['kode_pos']  = $kode_pos;
        $_SESSION['user']['tipe_rs']   = $tipeRs;
        $_SESSION['user']['no_izin']   = $noIzin;

        $_SESSION['success'] = 'Profil berhasil diperbarui.';
        header('Location: index.php?page=rs-profil');
        exit;
    }

    public function ubahPassword()
    {
        $this->requireRumahSakit();
        $userId      = $_SESSION['user']['id'];
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPwd  = $_POST['confirm_password'] ?? '';

        if (!$oldPassword || !$newPassword || !$confirmPwd) {
            $_SESSION['error'] = 'Lengkapi semua field password.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Password baru minimal 6 karakter.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        if ($newPassword !== $confirmPwd) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        $userDb = $this->userModel->findById($userId);
        if (!$userDb) {
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        
        $valid = password_verify($oldPassword, $userDb['password'])
              || $oldPassword === $userDb['password'];

        if (!$valid) {
            $_SESSION['error'] = 'Password lama salah.';
            header('Location: index.php?page=rs-profil');
            exit;
        }

        $this->userModel->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
        $_SESSION['success'] = 'Password berhasil diubah. Silakan login kembali.';
        header('Location: index.php?page=rs-profil');
        exit;
    }
}
