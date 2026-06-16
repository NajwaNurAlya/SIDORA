<?php
require_once __DIR__ . '/../config/database.php';

class PermintaanDarah
{
    public function create($data)
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO permintaan_darah (user_id, status, prioritas, keterangan) VALUES (:user_id, :status, :prioritas, :keterangan)');
        if (!$stmt->execute($data)) {
            return false;
        }
        return $pdo->lastInsertId();
    }

    public function getAllByUser($userId)
    {
        global $pdo;
        $stmt = $pdo->prepare('
            SELECT p.*, pd.golongan, pd.rhesus, pd.jumlah AS detail_jumlah
            FROM permintaan_darah p 
            LEFT JOIN (
                SELECT permintaan_id, MAX(golongan) as golongan, MAX(rhesus) as rhesus, SUM(jumlah) as jumlah 
                FROM permintaan_darah_detail 
                GROUP BY permintaan_id 
            ) pd ON p.id = pd.permintaan_id
            WHERE p.user_id = :user_id 
            ORDER BY p.created_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM permintaan_darah ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllWithUser()
    {
        global $pdo;
        $stmt = $pdo->query('
            SELECT p.*, u.name AS rumah_sakit, pd.golongan, pd.rhesus, pd.jumlah AS detail_jumlah,
                   DATE_FORMAT(p.created_at, "%Y-%m-%d") AS tanggal
            FROM permintaan_darah p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN (
                SELECT permintaan_id, MAX(golongan) as golongan, MAX(rhesus) as rhesus, SUM(jumlah) as jumlah 
                FROM permintaan_darah_detail 
                GROUP BY permintaan_id 
            ) pd ON p.id = pd.permintaan_id
            ORDER BY p.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        global $pdo;
        $stmt = $pdo->prepare('
            SELECT p.*, u.name AS rumah_sakit, pd.golongan, pd.rhesus, pd.jumlah AS detail_jumlah
            FROM permintaan_darah p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN (
                SELECT permintaan_id, MAX(golongan) as golongan, MAX(rhesus) as rhesus, SUM(jumlah) as jumlah 
                FROM permintaan_darah_detail 
                GROUP BY permintaan_id 
            ) pd ON p.id = pd.permintaan_id
            WHERE p.id = :id LIMIT 1
        ');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $extraData = [])
    {
        global $pdo;
        $sets = ['status = :status'];
        $params = ['status' => $status, 'id' => $id];
        foreach ($extraData as $k => $v) {
            $sets[] = "$k = :$k";
            $params[$k] = $v;
        }
        $sql = 'UPDATE permintaan_darah SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function approve($id)
    {
        return $this->updateStatus($id, 'Disetujui');
    }

    public function reject($id, $alasan)
    {
        return $this->updateStatus($id, 'Ditolak', [
            'alasan_tolak' => $alasan,
            'tanggal_tolak' => date('Y-m-d'),
        ]);
    }

    public function markAsSent($id, $kurir)
    {
        return $this->updateStatus($id, 'Dikirim', [
            'kurir' => $kurir,
            'tanggal_kirim' => date('Y-m-d'),
        ]);
    }

    public function tolakPermintaan($id, $alasan)
    {
        return $this->reject($id, $alasan);
    }

    public function kirimPermintaan($id, $kurir)
    {
        return $this->markAsSent($id, $kurir);
    }

    public function getDetails($permintaan_id)
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM permintaan_darah_detail WHERE permintaan_id = :id');
        $stmt->execute(['id' => $permintaan_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDetail($data)
    {
        global $pdo;
        $stmt = $pdo->prepare('INSERT INTO permintaan_darah_detail (permintaan_id, golongan, rhesus, jumlah) VALUES (:permintaan_id, :golongan, :rhesus, :jumlah)');
        return $stmt->execute($data);
    }
}
