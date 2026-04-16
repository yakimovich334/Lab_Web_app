<?php
require_once '_db.php';

$name = $_POST['name'];
$capacity = $_POST['capacity'];
$status = $_POST['status'];

// Використовуємо $pdo або $db залежно від вашого _db.php
$stmt = $pdo->prepare("INSERT INTO rooms (name, capacity, status) VALUES (:name, :capacity, :status)");
$stmt->execute([
    'name' => $name,
    'capacity' => $capacity,
    'status' => $status
]);

header('Content-Type: application/json');
echo json_encode(["result" => "OK"]);