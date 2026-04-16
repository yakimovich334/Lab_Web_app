<?php
require_once '_db.php';

$id = $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
$stmt->execute(['id' => $id]);

header('Content-Type: application/json');
echo json_encode(["result" => "OK"]);