<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config/db.php';

// Katılım ekle
if (isset($_POST['add'])) {
    $oyuncu_id = $_POST['oyuncu_id'];
    $antrenman_id = $_POST['antrenman_id'];
    $katildi_mi = isset($_POST['katildi_mi']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO katilimlar (oyuncu_id, antrenman_id, katildi_mi) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $oyuncu_id, $antrenman_id, $katildi_mi);
    $stmt->execute();
    $stmt->close();
    header("Location: katilimlar.php");
    exit();
}

// Katılım sil
if (isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $conn->query("DELETE FROM katilimlar WHERE id = $id");
    header("Location: katilimlar.php");
    exit();
}

// Oyuncuları ve antrenmanları çek
$oyuncular = $conn->query("SELECT * FROM oyuncular ORDER BY ad_soyad ASC");
$antrenmanlar = $conn->query("SELECT * FROM antrenmanlar ORDER BY tarih DESC");

// Katılımları listele (join ile)
$katilimlar = $conn->query("
    SELECT k.id, o.ad_soyad, a.tarih, k.katildi_mi
    FROM katilimlar k
    JOIN oyuncular o ON k.oyuncu_id = o.id
    JOIN antrenmanlar a ON k.antrenman_id = a.id
    ORDER BY a.tarih DESC
");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Katılımlar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Katılımlar</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">← Geri Dön</a>

    <!-- Katılım Ekle -->
    <form method="post" class="row g-3 mb-4">
        <div class="col-md-4">
            <select name="oyuncu_id" class="form-select" required>
                <option value="">Oyuncu Seç</option>
                <?php while ($o = $oyuncular->fetch_assoc()): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['ad_soyad']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="antrenman_id" class="form-select" required>
                <option value="">Antrenman Seç</option>
                <?php while ($a = $antrenmanlar->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['tarih']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="katildi_mi" id="katildi_mi" value="1">
                <label class="form-check-label" for="katildi_mi">Katıldı mı?</label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add" class="btn btn-primary">Ekle</button>
        </div>
    </form>

    <!-- Katılım Listesi -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Oyuncu</th>
                <th>Antrenman Tarihi</th>
                <th>Katılım Durumu</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($k = $katilimlar->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($k['ad_soyad']) ?></td>
                <td><?= htmlspecialchars($k['tarih']) ?></td>
                <td>
                    <?php if ($k['katildi_mi']): ?>
                        <span class="badge bg-success">Katıldı</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Katılmadı</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="katilimlar.php?sil=<?= $k['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
