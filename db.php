<?php
// Veritabanı bağlantı ayarları
$servername = "localhost"; // Genellikle hosting ortamlarında veritabanı sunucusu localhost olur
$username = "dbusr22360859070";
$password = "Ea5qyCfF20As"; // Şifreyi tekrar girdik, bir hata olmaması için
$dbname = "dbstorage22360859070";

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
