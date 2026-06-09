<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$page = $_GET['page'] ?? 'auth-index';




$adminPages = [
    'admin-dashboard', 'admin-stok-darah', 'admin-permintaan-darah',
    'admin-kelola-petugas', 'admin-jadwal-donor', 'admin-form-jadwal',
    'admin-daftar-pendonor', 'admin-form-petugas',
    
    'admin-form-jadwal-process', 'admin-edit-jadwal', 'admin-hapus-jadwal',
    'admin-approve-rs', 'admin-hapus-rs',
    'admin-tambah-petugas', 'admin-edit-petugas', 'admin-hapus-petugas',
    'admin-permintaan-process', 'admin-permintaan-kirim',
    'admin-terima-permintaan', 'admin-tolak-permintaan', 'admin-kirim-permintaan',
    'admin-alert-stok', 'admin-pesan-stok',
    'admin-export-semua', 'admin-export-permintaan',
];

if (in_array($page, $adminPages)) {
    require_once 'controllers/AdminController.php';
    $controller = new AdminController();

    switch ($page) {
        case 'admin-dashboard':             $controller->dashboard(); break;
        case 'admin-stok-darah':            $controller->stokDarah(); break;
        case 'admin-permintaan-darah':      $controller->permintaanDarah(); break;
        case 'admin-kelola-petugas':        $controller->kelolaPetugas(); break;
        case 'admin-jadwal-donor':          $controller->jadwalDonor(); break;
        case 'admin-form-jadwal':           $controller->formJadwal(); break;
        case 'admin-form-petugas':          $controller->formPetugas(); break;
        case 'admin-daftar-pendonor':       $controller->daftarPendonor(); break;
        case 'admin-form-jadwal-process':   $controller->formJadwalProcess(); break;
        case 'admin-edit-jadwal':           $controller->editJadwalProcess(); break;
        case 'admin-hapus-jadwal':          $controller->hapusJadwalProcess(); break;
        case 'admin-approve-rs':            $controller->approveRs(); break;
        case 'admin-hapus-rs':              $controller->hapusRsProcess(); break;
        case 'admin-tambah-petugas':        $controller->tambahPetugasProcess(); break;
        case 'admin-edit-petugas':          $controller->editPetugasProcess(); break;
        case 'admin-hapus-petugas':         $controller->hapusPetugasProcess(); break;
        case 'admin-terima-permintaan':     $controller->terimaPermintaan(); break;
        case 'admin-tolak-permintaan':      $controller->tolakPermintaan(); break;
        case 'admin-kirim-permintaan':      
        case 'admin-permintaan-kirim':      $controller->kirimPermintaan(); break;
        case 'admin-permintaan-process':    $controller->tolakPermintaan(); break;
        case 'admin-alert-stok':            $controller->alertStok(); break;
        case 'admin-pesan-stok':            $controller->pesanStok(); break;
        case 'admin-export-semua':          $controller->exportSemuaLaporan(); break;
        case 'admin-export-permintaan':     $controller->exportPermintaanLaporan(); break;
        default: echo "Admin: halaman tidak ditemukan.";
    }
    return;
}




$petugasPages = [
    'petugas-dashboard', 'petugas-daftar-pendonor', 'petugas-riwayat-donasi',
    'petugas-jadwal-donor', 'petugas-stok-darah', 'petugas-form-riwayat',
    'petugas-form-pendonor',
    
    'petugas-tambah-pendonor', 'petugas-edit-pendonor', 'petugas-hapus-pendonor',
    'petugas-form-riwayat-process', 'petugas-edit-riwayat', 'petugas-hapus-riwayat',
];

if (in_array($page, $petugasPages)) {
    require_once 'controllers/PetugasController.php';
    $controller = new PetugasController();

    switch ($page) {
        case 'petugas-dashboard':           $controller->dashboard(); break;
        case 'petugas-daftar-pendonor':     $controller->daftarPendonor(); break;
        case 'petugas-riwayat-donasi':      $controller->riwayatDonasi(); break;
        case 'petugas-jadwal-donor':        $controller->jadwalDonor(); break;
        case 'petugas-stok-darah':          $controller->stokDarah(); break;
        case 'petugas-form-riwayat':        $controller->formRiwayat(); break;
        case 'petugas-form-pendonor':       $controller->formPendonor(); break;
        case 'petugas-tambah-pendonor':     $controller->tambahPendonorProcess(); break;
        case 'petugas-edit-pendonor':       $controller->editPendonorProcess(); break;
        case 'petugas-hapus-pendonor':      $controller->hapusPendonorProcess(); break;
        case 'petugas-form-riwayat-process':$controller->formRiwayatProcess(); break;
        case 'petugas-edit-riwayat':        $controller->editRiwayatProcess(); break;
        case 'petugas-hapus-riwayat':       $controller->hapusRiwayatProcess(); break;
        default: echo "Petugas: halaman tidak ditemukan.";
    }
    return;
}




$rsPages = [
    'rs-dashboard', 'rs-permintaan', 'rs-history-permintaan',
    'rs-stok-darah', 'rs-profil', 'rs-profil-update',
    'rs-ubah-password', 'rs-permintaan-store',
];

if (in_array($page, $rsPages)) {
    require_once 'controllers/RumahSakitController.php';
    $controller = new RumahSakitController();

    switch ($page) {
        case 'rs-dashboard':            $controller->dashboard(); break;
        case 'rs-permintaan':           $controller->permintaan(); break;
        case 'rs-history-permintaan':   $controller->historyPermintaan(); break;
        case 'rs-stok-darah':           $controller->stokDarah(); break;
        case 'rs-profil':               $controller->profil(); break;
        case 'rs-profil-update':        $controller->profilUpdate(); break;
        case 'rs-ubah-password':        $controller->ubahPassword(); break;
        case 'rs-permintaan-store':     $controller->permintaanStore(); break;
        default: echo "RS: halaman tidak ditemukan.";
    }
    return;
}




switch ($page) {
    case 'auth-index':
        require 'views/auth/index.php';
        break;

    case 'login':
        require 'views/auth/login.php';
        break;

    case 'register-rs':
        require 'views/auth/register-rs.php';
        break;

    case 'login-process':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register-rs-process':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->registerRs();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 — Halaman tidak ditemukan</h1><a href='index.php'>Kembali ke Beranda</a>";
        break;
}
?>
