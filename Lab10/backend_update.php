<?php
require_once '_db.php';
header('Content-Type: application/json');

$id = $_POST['id'];
$action = $_POST['action'];

try {
    if ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE reservations SET 
            name = :name, 
            start = :start, 
            end = :end, 
            room_id = :room_id, 
            status = :status, 
            paid = :paid 
            WHERE id = :id");
            
        $stmt->execute([
            ':id'      => $id,
            ':name'    => $_POST['name'],
            ':start'   => str_replace("T", " ", $_POST['start']),
            ':end'     => str_replace("T", " ", $_POST['end']),
            ':room_id' => (int)$_POST['room_id'],
            ':status'  => $_POST['status'],
            ':paid'    => (int)$_POST['paid']
        ]);
    } 
    elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    echo json_encode(["result" => "OK"]);
} catch (PDOException $e) {
    echo json_encode(["result" => "Error", "message" => $e->getMessage()]);
}