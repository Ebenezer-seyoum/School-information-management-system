<?php
include '../connection/connection.php';

if (isset($_POST['zone_id'])) {
    $zone_id = intval($_POST['zone_id']);
    $result = $conn->query("SELECT * FROM woredas WHERE zone_id = $zone_id");

    echo '<option value="">Select Woreda</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
}
?>
