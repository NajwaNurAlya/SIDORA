<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pendonor.php';
require_once __DIR__ . '/../models/JadwalDonor.php';
require_once __DIR__ . '/../models/StokDarah.php';
require_once __DIR__ . '/../models/RiwayatDonasi.php';

class PetugasController
{
    private $pendonorModel;
    private $jadwalModel;
    private $stokModel;
    private $riwayatModel;

    public function __construct()
    {
        $this->pendonorModel = new Pendonor();
        $this->jadwalModel   = new JadwalDonor();
        $this->stokModel     = new StokDarah();
        $this->riwayatModel  = new RiwayatDonasi();
    }

    private function requirePetugas()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
            header('Location: index.php?page=login');
            exit;
        }
    }

    
    public function dashboard()
    {
        $this->requirePetugas();
        $stokList   = $this->stokModel->getAll();
        $jadwalList = $this->jadwalModel->getAll();
        $riwayatList = $this->riwayatModel->getAll();

        $stokTotal = array_reduce($stokList, fn($c, $i) => $c + (int)$i['jumlah'], 0);
        $today = date('Y-m-d');
        $donasiHariIni = count(array_filter($riwayatList, fn($r) => ($r['tanggal'] ?? '') === $today));

        $statistics = [
            'pendonor'       => count($this->pendonorModel->getAll()),
            'donasi_hari_ini' => $donasiHariIni,
            'stok_total'     => $stokTotal,
            'jadwal'         => count($jadwalList),
        ];

        require __DIR__ . '/../views/petugas/dashboard.php';
    }

    
    public function daftarPendonor()
    {
        $this->requirePetugas();
        $pendonorList = $this->pendonorModel->getAll();
        require __DIR__ . '/../views/petugas/daftar-pendonor.php';
    }

    public function formPendonor()
    {
        $this->requirePetugas();
        require __DIR__ . '/../views/petugas/form-pendonor.php';
    }

    public function tambahPendonorProcess()
    {
        $this->requirePetugas();

        $nama          = trim($_POST['nama'] ?? '');
        $no_identitas  = trim($_POST['no_identitas'] ?? '');
        $golongan      = trim($_POST['golongan_darah'] ?? '');
        $rhesus        = trim($_POST['rhesus'] ?? '');
        $telepon       = trim($_POST['telepon'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '') ?: null;
        $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
        $alamat        = trim($_POST['alamat'] ?? '');
        $kota          = trim($_POST['kota'] ?? '');
        $provinsi      = trim($_POST['provinsi'] ?? '');
        $pekerjaan     = trim($_POST['pekerjaan'] ?? '');
        $status        = ($_POST['status'] ?? '') === 'aktif' ? 'aktif' : 'nonaktif';

        if (!$nama || !$no_identitas || !$golongan || !$rhesus || !$telepon) {
            $_SESSION['error'] = 'Nama, NIK, Golongan Darah, Rhesus, dan Telepon wajib diisi.';
            header('Location: index.php?page=petugas-daftar-pendonor');
            exit;
        }

        $pendonorId = $this->pendonorModel->create([
            'nama'          => $nama, 'nik'           => $no_identitas,
            'golongan'      => $golongan, 'rhesus'    => $rhesus,
            'telepon'       => $telepon, 'email'       => $email,
            'tanggal_lahir' => $tanggal_lahir,
            'jenis_kelamin' => $jenis_kelamin,
            'alamat'        => $alamat, 'kota'         => $kota,
            'provinsi'      => $provinsi, 'pekerjaan'  => $pekerjaan,
            'status'        => $status,
        ]);

        $tekanan_darah = trim($_POST['tekanan_darah'] ?? '120/80');
        $status_donasi = trim($_POST['status_donasi'] ?? 'Berhasil');

        $this->riwayatModel->create([
            'pendonor_id'  => $pendonorId,
            'golongan'     => $golongan,
            'rhesus'       => $rhesus,
            'jumlah'       => 1,
            'tanggal'      => date('Y-m-d'),
            'tekanan_darah'=> $tekanan_darah,
            'status'       => $status_donasi,
        ]);

        if ($status_donasi === 'Berhasil') {
            $this->stokModel->addStock($golongan, $rhesus, 1);
        }

        $_SESSION['success'] = 'Pendonor berhasil ditambahkan' . ($status_donasi === 'Berhasil' ? ' dan stok darah diperbarui.' : '.');
        header('Location: index.php?page=petugas-daftar-pendonor');
        exit;
    }

    public function editPendonorProcess()
    {
        $this->requirePetugas();
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID pendonor tidak valid.';
            header('Location: index.php?page=petugas-daftar-pendonor');
            exit;
        }

        $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '') ?: null;

        $this->pendonorModel->update($id, [
            'nama'          => trim($_POST['nama'] ?? ''),
            'nik'           => trim($_POST['nik'] ?? ''),
            'golongan'      => trim($_POST['golongan_darah'] ?? ''),
            'rhesus'        => trim($_POST['rhesus'] ?? ''),
            'telepon'       => trim($_POST['telepon'] ?? ''),
            'email'         => trim($_POST['email'] ?? ''),
            'tanggal_lahir' => $tanggal_lahir,
            'jenis_kelamin' => trim($_POST['jenis_kelamin'] ?? ''),
            'alamat'        => trim($_POST['alamat'] ?? ''),
            'kota'          => trim($_POST['kota'] ?? ''),
            'provinsi'      => trim($_POST['provinsi'] ?? ''),
            'pekerjaan'     => trim($_POST['pekerjaan'] ?? ''),
            'status'        => ($_POST['status'] ?? '') === 'aktif' ? 'aktif' : 'nonaktif',
        ]);

        $_SESSION['success'] = 'Data pendonor berhasil diperbarui.';
        header('Location: index.php?page=petugas-daftar-pendonor');
        exit;
    }

    public function hapusPendonorProcess()
    {
        $this->requirePetugas();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->pendonorModel->delete($id);
            $_SESSION['success'] = 'Pendonor berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'ID tidak valid.';
        }
        header('Location: index.php?page=petugas-daftar-pendonor');
        exit;
    }

    
    public function riwayatDonasi()
    {
        $this->requirePetugas();
        $riwayatList = $this->riwayatModel->getAll();
        $status  = $_GET['status'] ?? '';
        $tanggal = $_GET['tanggal'] ?? '';

        if ($status || $tanggal) {
            $riwayatList = array_filter($riwayatList, function($item) use ($status, $tanggal) {
                if ($tanggal && ($item['tanggal'] ?? '') != $tanggal) return false;
                if ($status && ($item['status'] ?? '') != $status) return false;
                return true;
            });
        }

        require __DIR__ . '/../views/petugas/riwayat-donasi.php';
    }

    public function formRiwayat()
    {
        $this->requirePetugas();
        $pendonorList = $this->pendonorModel->getAll();
        require __DIR__ . '/../views/petugas/form-riwayat.php';
    }

    public function formRiwayatProcess()
    {
        $this->requirePetugas();
        $pendonorId    = intval($_POST['pendonor_id'] ?? 0);
        $golongan      = trim($_POST['golongan'] ?? '');
        $rhesus        = trim($_POST['rhesus'] ?? '');
        $jumlah        = intval($_POST['jumlah'] ?? 1);
        $tanggal       = $_POST['tanggal'] ?? date('Y-m-d');
        $tekanan_darah = trim($_POST['tekanan_darah'] ?? '120/80');
        $status        = trim($_POST['status'] ?? 'Berhasil');

        if (!$pendonorId || !$golongan || !$rhesus || $jumlah <= 0) {
            $_SESSION['error'] = 'Isi semua field riwayat donasi dengan benar.';
            header('Location: index.php?page=petugas-form-riwayat');
            exit;
        }

        $this->riwayatModel->create([
            'pendonor_id'  => $pendonorId,
            'golongan'     => $golongan,
            'rhesus'       => $rhesus,
            'jumlah'       => $jumlah,
            'tanggal'      => $tanggal,
            'tekanan_darah'=> $tekanan_darah,
            'status'       => $status,
        ]);

        if ($status === 'Berhasil') {
            $this->stokModel->addStock($golongan, $rhesus, $jumlah);
        }

        $_SESSION['success'] = 'Riwayat donasi berhasil disimpan' . ($status === 'Berhasil' ? ' dan stok darah diperbarui.' : '.');
        header('Location: index.php?page=petugas-riwayat-donasi');
        exit;
    }

    public function editRiwayatProcess()
    {
        $this->requirePetugas();
        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['error'] = 'ID riwayat tidak valid.';
            header('Location: index.php?page=petugas-riwayat-donasi');
            exit;
        }

        
        $old = $this->riwayatModel->getById($id);

        $golongan = trim($_POST['golongan'] ?? '');
        $rhesus   = trim($_POST['rhesus'] ?? '');
        $jumlah   = intval($_POST['jumlah'] ?? 1);
        $status   = trim($_POST['status'] ?? 'Berhasil');

        
        if ($old && $old['status'] === 'Berhasil') {
            $this->stokModel->reduceStock($old['golongan'], $old['rhesus'], $old['jumlah']);
        }

        $this->riwayatModel->update($id, [
            'pendonor_id'  => intval($_POST['pendonor_id'] ?? $old['pendonor_id']),
            'golongan'     => $golongan,
            'rhesus'       => $rhesus,
            'jumlah'       => $jumlah,
            'tanggal'      => $_POST['tanggal'] ?? $old['tanggal'],
            'tekanan_darah'=> trim($_POST['tekanan_darah'] ?? '120/80'),
            'status'       => $status,
        ]);

        
        if ($status === 'Berhasil') {
            $this->stokModel->addStock($golongan, $rhesus, $jumlah);
        }

        $_SESSION['success'] = 'Riwayat donasi berhasil diperbarui.';
        header('Location: index.php?page=petugas-riwayat-donasi');
        exit;
    }

    public function hapusRiwayatProcess()
    {
        $this->requirePetugas();
        $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $old = $this->riwayatModel->getById($id);
            if ($old && $old['status'] === 'Berhasil') {
                $this->stokModel->reduceStock($old['golongan'], $old['rhesus'], $old['jumlah']);
            }
            $this->riwayatModel->delete($id);
            $_SESSION['success'] = 'Riwayat donasi berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'ID tidak valid.';
        }
        header('Location: index.php?page=petugas-riwayat-donasi');
        exit;
    }

    
    public function jadwalDonor()
    {
        $this->requirePetugas();
        $jadwalList = $this->jadwalModel->getAll();
        require __DIR__ . '/../views/petugas/jadwal-donor.php';
    }

    public function stokDarah()
    {
        $this->requirePetugas();
        $stokList = $this->stokModel->getAll();
        require __DIR__ . '/../views/petugas/stok-darah.php';
    }
}
