<?php
require_once '_db.php';

$capacity = $_POST['capacity'] ?? '0';

$stmt = $pdo->prepare("SELECT * FROM rooms WHERE capacity = :capacity OR :capacity = '0' ORDER BY name");
$stmt->execute(['capacity' => $capacity]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = array();
foreach($rooms as $room) {
    $result[] = [
        "id"       => (string)$room['id'],
        "name"     => $room['name'],
        "capacity" => $room['capacity'],
        "status"   => $room['status']
    ];
}

header('Content-Type: application/json');
echo json_encode($result);