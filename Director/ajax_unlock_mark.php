<?php
include('../connection/connection.php');

$mark_id = (int)($_POST['mark_id'] ?? 0);
$new_status = isset($_POST['new_status']) ? (int)$_POST['new_status'] : null;

header('Content-Type: application/json');

if (!$mark_id || $new_status === null) {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$update = mysqli_query($conn,"UPDATE marks SET mark_status=$new_status WHERE mid=$mark_id");

if ($update) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]);
}
