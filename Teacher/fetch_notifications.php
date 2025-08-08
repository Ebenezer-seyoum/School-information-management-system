<?php
session_start();
include '../connection/connection.php';
// Logged-in user id
$user_id = $_SESSION['uid'];
$sql = "SELECT * FROM notifications WHERE user_id = '$user_id' AND is_read = 0 ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}
echo json_encode($notifications);
?>
