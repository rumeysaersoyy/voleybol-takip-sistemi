<?php
// Veritabanı bağlantı ayarları
$servername = "localhost";
$username = "root";        // XAMPP için genelde root olur
$password = "";            // XAMPP default olarak şifre yoktur
$dbname = "voleybol";      // Senin MySQL’de oluşturduğun veritabanı adı

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
