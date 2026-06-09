<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public function showLogin()
    {
        require 'views/auth/login.php';
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmailOrUsername($email);

        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            if ($user['role'] === 'rumahsakit' && $user['status'] !== 'aktif') {
                $_SESSION['error'] = 'Akun rumah sakit belum aktif. Tunggu verifikasi admin.';
                header('Location: index.php?page=login');
                exit;
            }
            if ($user['status'] === 'nonaktif' || $user['status'] === 'ditolak') {
                $_SESSION['error'] = 'Akun Anda tidak aktif. Hubungi administrator.';
                header('Location: index.php?page=login');
                exit;
            }

            $_SESSION['user'] = $user;

            if ($user['role'] === 'admin') {
                header('Location: index.php?page=admin-dashboard');
            } elseif ($user['role'] === 'petugas') {
                header('Location: index.php?page=petugas-dashboard');
            } else {
                header('Location: index.php?page=rs-dashboard');
            }
            exit;
        }

        $_SESSION['error'] = 'Email atau password salah.';
        header('Location: index.php?page=login');
    }

    public function registerRs()
    {
        $namaRs = trim($_POST['nama_rs'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $telepon = trim($_POST['telepon'] ?? '');
        $kontak = trim($_POST['kontak'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $desa = trim($_POST['desa'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $provinsi = trim($_POST['provinsi'] ?? '');
        $kode_pos = trim($_POST['kode_pos'] ?? '');
        $tipe_rs = trim($_POST['tipe_rs'] ?? '');
        $no_izin = trim($_POST['no_izin'] ?? '');

        if (!$namaRs || !$email || !$password) {
            $_SESSION['error'] = 'Data penting (Nama, Email, Password) harus diisi.';
            header('Location: index.php?page=register-rs');
            exit;
        }

        $userModel = new User();
        $existingUser = $userModel->findByEmail($email);

        if ($existingUser) {
            $_SESSION['error'] = 'Email sudah terdaftar.';
            header('Location: index.php?page=register-rs');
            exit;
        }

        if ($username !== '' && $userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Username sudah digunakan. Silakan pilih username lain.';
            header('Location: index.php?page=register-rs');
            exit;
        }

        try {
            $created = $userModel->createRumahSakitPending([
                'name' => $namaRs,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'rumahsakit',
                'status' => 'pending',
                'username' => $username,
                'telepon' => $telepon,
                'kontak' => $kontak,
                'alamat' => $alamat,
                'kecamatan' => $kecamatan,
                'desa' => $desa,
                'kode_pos' => $kode_pos,
                'provinsi' => $provinsi,
                'tipe_rs' => $tipe_rs,
                'no_izin' => $no_izin,
            ]);
        } catch (PDOException $e) {
            $_SESSION['error'] = str_contains($e->getMessage(), 'username')
                ? 'Username sudah digunakan. Silakan pilih username lain.'
                : 'Terjadi kesalahan saat registrasi.';
            header('Location: index.php?page=register-rs');
            exit;
        }

        if ($created) {
            $_SESSION['success'] = 'Registrasi berhasil. Tunggu aktivasi admin.';
            header('Location: index.php?page=login');
            exit;
        }

        $_SESSION['error'] = 'Terjadi kesalahan saat registrasi.';
        header('Location: index.php?page=register-rs');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Location: index.php?page=login');
        exit;
    }
}
