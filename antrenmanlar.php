<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'config/db.php';

// Veritabanı bağlantı kontrolü
if (!$conn) {
    die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
}

// Tablo kontrolü
$table_check = $conn->query("SHOW TABLES LIKE 'antrenmanlar'");
if ($table_check->num_rows == 0) {
    die("'antrenmanlar' tablosu bulunamadı!");
}

// Tablo yapısını kontrol et
$columns = $conn->query("SHOW COLUMNS FROM antrenmanlar");
if (!$columns) {
    die("Tablo yapısı kontrol hatası: " . $conn->error);
}

$message = '';

// Antrenman ekleme
if (isset($_POST['add'])) {
    $tarih = $_POST['tarih'];
    $sure = $_POST['sure'];
    $tur = trim($_POST['tur']);

    if ($tarih && $sure && $tur) {
        $stmt = $conn->prepare("INSERT INTO antrenmanlar (tarih, sure_dk, tur) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $tarih, $sure, $tur);
        $stmt->execute();
        $stmt->close();
        $message = "Antrenman eklendi.";
    }
}

// Antrenman silme
if (isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $conn->query("DELETE FROM antrenmanlar WHERE id = $id");
    header("Location: antrenmanlar.php");
    exit();
}

// Antrenman güncelleme
if (isset($_POST['guncelle'])) {
    $id = $_POST['id'];
    $tarih = $_POST['tarih'];
    $sure = $_POST['sure'];
    $tur = trim($_POST['tur']);

    $stmt = $conn->prepare("UPDATE antrenmanlar SET tarih=?, sure_dk=?, tur=? WHERE id=?");
    $stmt->bind_param("sisi", $tarih, $sure, $tur, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: antrenmanlar.php");
    exit();
}

// Güncellenecek antrenmanı çek
$duzenle = null;
if (isset($_GET['duzenle'])) {
    $id = $_GET['duzenle'];
    $result = $conn->query("SELECT * FROM antrenmanlar WHERE id = $id");
    $duzenle = $result->fetch_assoc();
}

// Test verisi ekleme
if (isset($_GET['test_data'])) {
    $test_data = [
        ['2024-03-20', 90, 'Teknik Antrenman'],
        ['2024-03-21', 120, 'Kondisyon'],
        ['2024-03-22', 90, 'Taktik Antrenman']
    ];
    
    $stmt = $conn->prepare("INSERT INTO antrenmanlar (tarih, sure_dk, tur) VALUES (?, ?, ?)");
    $success = true;
    
    foreach ($test_data as $data) {
        $stmt->bind_param("sis", $data[0], $data[1], $data[2]);
        if (!$stmt->execute()) {
            $success = false;
            $message = "Test verisi eklenirken hata oluştu: " . $stmt->error;
            break;
        }
    }
    
    if ($success) {
        $message = "Test verileri başarıyla eklendi.";
    }
    
    $stmt->close();
    header("Location: antrenmanlar.php?message=success");
    exit();
}

// URL'den gelen mesaj parametresini kontrol et
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = "İşlem başarıyla tamamlandı.";
}

// Antrenmanları listele
$antrenmanlar = $conn->query("SELECT id, tarih, sure_dk, tur FROM antrenmanlar ORDER BY tarih DESC");
if (!$antrenmanlar) {
    $message = "Listeleme hatası: " . $conn->error;
    error_log("Listeleme hatası: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Antrenmanlar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Antrenmanlar</h2>
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">⟵ Geri Dön</a>
        <a href="?test_data=1" class="btn btn-info">Test Verisi Ekle</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?= $duzenle['id'] ?? '' ?>">
        <div class="row">
            <div class="col">
                <input type="date" name="tarih" class="form-control" required value="<?= $duzenle['tarih'] ?? '' ?>">
            </div>
            <div class="col">
                <input type="number" name="sure" class="form-control" placeholder="Süre (dk)" required min="1" value="<?= $duzenle['sure_dk'] ?? '' ?>">
            </div>
            <div class="col">
                <input type="text" name="tur" class="form-control" placeholder="Antrenman Türü" required value="<?= $duzenle['tur'] ?? '' ?>">
            </div>
            <div class="col">
                <?php if ($duzenle): ?>
                    <button type="submit" name="guncelle" class="btn btn-warning">Güncelle</button>
                    <a href="antrenmanlar.php" class="btn btn-secondary">İptal</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary">Ekle</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php if ($antrenmanlar && $antrenmanlar->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Tarih</th>
                    <th>Süre (dk)</th>
                    <th>Antrenman Türü</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = $antrenmanlar->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['tarih']) ?></td>
                        <td><?= htmlspecialchars($a['sure_dk']) ?></td>
                        <td><?= htmlspecialchars($a['tur']) ?></td>
                        <td>
                            <a href="antrenmanlar.php?duzenle=<?= $a['id'] ?>" class="btn btn-warning btn-sm">Düzenle</a>
                            <a href="antrenmanlar.php?sil=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Henüz antrenman kaydı bulunmamaktadır.</div>
    <?php endif; ?>
</body>
</html>
