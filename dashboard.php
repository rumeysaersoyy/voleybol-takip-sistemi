<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">
    <h2>Hoşgeldin, <?= htmlspecialchars($username) ?>!</h2>
    <p><a href="logout.php" class="btn btn-danger">Çıkış Yap</a></p>
    
    <h3>Yönetim Menüsü</h3>
    <ul>
        <li><a href="oyuncular.php">Oyuncuları Yönet</a></li>
        <li><a href="antrenmanlar.php">Antrenmanları Yönet</a></li>
        <li><a href="katilimlar.php">Katılımları Yönet</a></li>
    </ul>
</body>
</html>
