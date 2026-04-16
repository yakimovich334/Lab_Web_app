<?php
require_once '_db.php';

// Отримуємо дані з POST
$id = $_POST['id'];
$name = $_POST['name'];
$status = $_POST['status'];
$paid = $_POST['paid'];
$room = $_POST['room'];

// Оновлюємо базу (важливо: використовуй $pdo, як ми прописали в _db.php)
$stmt = $pdo->prepare("UPDATE reservations SET name = :name, status = :status, paid = :paid, room_id = :room WHERE id = :id");
$stmt->execute([
    'name'   => $name,
    'status' => $status,
    'paid'   => $paid,
    'room'   => $room,
    'id'     => $id
]);

header('Content-Type: application/json');
echo json_encode(["result" => "OK"]);