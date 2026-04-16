<?php
$host = '127.0.0.1';
$database_name = 'hotel_booking'; // Змінив назву з $db на $database_name
$user = 'admin';
$pass = 'admin'; 

try {
    // Створюємо PDO об'єкт
    $pdo = new PDO("mysql:host=$host;dbname=$database_name;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Створюємо аліас $db для сумісності з кодом методички
    $db = $pdo; 
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>