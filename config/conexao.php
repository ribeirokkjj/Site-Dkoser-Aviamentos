<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'dkoser_aviamentos';
$porta = 3306;

$conn = new mysqli($host, $user, $password, $dbname, $porta);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>