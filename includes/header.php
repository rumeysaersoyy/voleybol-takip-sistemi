<?php
session_start();

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Voleybol Takımı Yönetimi</title>
    <!-- Bootstrap CSS CDN ekleyebilirsin ya da style.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Voleybol Yönetim</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="oyuncular.php">Oyuncular</a></li>
        <li class="nav-item"><a class="nav-link" href="antrenmanlar.php">Antrenmanlar</a></li>
        <li class="nav-item"><a class="nav-link" href="katilimlar.php">Katılımlar</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="nav-link disabled">Hoşgeldin, <?= htmlspecialchars($_SESSION['username']) ?></span></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Çıkış</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
