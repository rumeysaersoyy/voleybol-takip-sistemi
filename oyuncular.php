<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/db.php';

$message = '';

// Oyuncu ekleme
if (isset($_POST['add'])) {
    $ad_soyad = trim($_POST['ad_soyad']);
    $numara = $_POST['numara'];
    $pozisyon = $_POST['pozisyon'];

    if ($ad_soyad && $numara && $pozisyon) {
        $stmt = $conn->prepare("INSERT INTO oyuncular (ad_soyad, numara, pozisyon) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $ad_soyad, $numara, $pozisyon);
        $stmt->execute();
        $stmt->close();
        $message = "Oyuncu eklendi.";
    }
}

// Oyuncu silme
if (isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $conn->query("DELETE FROM oyuncular WHERE id = $id");
    header("Location: oyuncular.php");
    exit();
}

// Oyuncu güncelleme
if (isset($_POST['guncelle'])) {
    $id = $_POST['id'];
    $ad_soyad = trim($_POST['ad_soyad']);
    $numara = $_POST['numara'];
    $pozisyon = $_POST['pozisyon'];

    $stmt = $conn->prepare("UPDATE oyuncular SET ad_soyad=?, numara=?, pozisyon=? WHERE id=?");
    $stmt->bind_param("sisi", $ad_soyad, $numara, $pozisyon, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: oyuncular.php");
    exit();
}

// Güncellenecek oyuncuyu çek
$duzenle = null;
if (isset($_GET['duzenle'])) {
    $id = $_GET['duzenle'];
    $result = $conn->query("SELECT * FROM oyuncular WHERE id = $id");
    $duzenle = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Oyuncular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Oyuncular</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Geri Dön</a>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Oyuncu Ekle / Güncelle -->
    <form method="post" class="mb-4">
        <input type="hidden" name="id" value="<?= $duzenle['id'] ?? '' ?>">
        <div class="row">
            <div class="col">
                <input type="text" name="ad_soyad" class="form-control" placeholder="Ad Soyad" required value="<?= $duzenle['ad_soyad'] ?? '' ?>">
            </div>
            <div class="col">
                <input type="number" name="numara" class="form-control" placeholder="Forma No" required value="<?= $duzenle['numara'] ?? '' ?>">
            </div>
            <div class="col">
                <input type="text" name="pozisyon" class="form-control" placeholder="Pozisyon" required value="<?= $duzenle['pozisyon'] ?? '' ?>">
            </div>
            <div class="col">
                <?php if ($duzenle): ?>
                    <button type="submit" name="guncelle" class="btn btn-warning">Güncelle</button>
                    <a href="oyuncular.php" class="btn btn-secondary">İptal</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary">Ekle</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- Oyuncu Listesi -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>Numara</th>
                <th>Pozisyon</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM oyuncular ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['ad_soyad']) ?></td>
                <td><?= htmlspecialchars($row['numara']) ?></td>
                <td><?= htmlspecialchars($row['pozisyon']) ?></td>
                <td>
                    <a href="oyuncular.php?duzenle=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
                    <a href="oyuncular.php?sil=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
