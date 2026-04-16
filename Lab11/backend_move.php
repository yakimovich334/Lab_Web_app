<?php
require_once '_db.php';

$id = $_POST['id'];
$start = $_POST['newStart'];
$end = $_POST['newEnd'];
$resource = $_POST['newResource'];

// Перевірка на накладання (Overlap)
$stmt = $pdo->prepare("SELECT count(*) FROM reservations WHERE NOT ((end <= :start) OR (start >= :end)) AND id <> :id AND room_id = :resource");
$stmt->execute(['start' => $start, 'end' => $end, 'id' => $id, 'resource' => $resource]);
$overlaps = $stmt->fetchColumn() > 0;

if ($overlaps) {
    echo json_encode(["result" => "Error", "message" => "Ця кімната вже зайнята на ці дати!"]);
    exit;
}

// Якщо вільно — оновлюємо
$stmt = $pdo->prepare("UPDATE reservations SET start = :start, end = :end, room_id = :resource WHERE id = :id");
$stmt->execute(['id' => $id, 'start' => $start, 'end' => $end, 'resource' => $resource]);

echo json_encode(["result" => "OK", "message" => "Переміщено успішно"]);