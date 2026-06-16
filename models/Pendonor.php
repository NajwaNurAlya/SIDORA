<?php
require_once __DIR__ . '/../config/database.php';

class Pendonor
{
    public function getAll()
    {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM pendonor ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM pendonor WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO pendonor (nama, nik, golongan, rhesus, telepon, email, tanggal_lahir, jenis_kelamin, alamat, kota, provinsi, pekerjaan, status) VALUES (:nama, :nik, :golongan, :rhesus, :telepon, :email, :tanggal_lahir, :jenis_kelamin, :alamat, :kota, :provinsi, :pekerjaan, :status)");
        $stmt->execute($data);
        return $pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE pendonor SET nama = :nama, nik = :nik, golongan = :golongan, rhesus = :rhesus, telepon = :telepon, email = :email, tanggal_lahir = :tanggal_lahir, jenis_kelamin = :jenis_kelamin, alamat = :alamat, kota = :kota, provinsi = :provinsi, pekerjaan = :pekerjaan, status = :status WHERE id = :id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        global $pdo;
        $stmt = $pdo->prepare('DELETE FROM pendonor WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
