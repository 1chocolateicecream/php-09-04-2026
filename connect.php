<?php
$host = '192.168.10.79';
$user = 'user456';
$pass = 'password';
$db   = 'store_2026_07_04';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "Savienojums ar PDO izveidots veiksmīgi!"  . "\n";
} catch (PDOException $e) {
    die("Savienojums neizdevās: " . $e->getMessage() . "\n");
}
?>